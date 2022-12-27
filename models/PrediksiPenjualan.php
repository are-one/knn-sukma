<?php

namespace app\models;

use Phpml\Classification\KNearestNeighbors;
use Phpml\Math\Distance\Euclidean;
use Yii;
use yii\web\ServerErrorHttpException;

/**
 * This is the model class for table "prediksi_penjualan".
 *
 * @property int $id
 * @property string $hasil_prediksi
 * @property int $tahun_bulan_id
 *
 * @property JenisBarangHasPrediksiPenjualan[] $jenisBarangHasPrediksiPenjualans
 * @property TahunBulan $tahunBulan
 */
class PrediksiPenjualan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prediksi_penjualan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hasil_prediksi', 'tahun_bulan_id'], 'required'],
            [['tahun_bulan_id'], 'integer'],
            [['hasil_prediksi'], 'string', 'max' => 45],
            [['tahun_bulan_id'], 'exist', 'skipOnError' => true, 'targetClass' => TahunBulan::class, 'targetAttribute' => ['tahun_bulan_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hasil_prediksi' => 'Hasil Prediksi',
            'tahun_bulan_id' => 'Tahun Bulan ID',
        ];
    }

    /**
     * Gets query for [[JenisBarangHasPrediksiPenjualans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJenisBarangHasPrediksiPenjualans()
    {
        return $this->hasMany(JenisBarangHasPrediksiPenjualan::class, ['prediksi_penjualan_id' => 'id']);
    }

    /**
     * Gets query for [[TahunBulan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTahunBulan()
    {
        return $this->hasOne(TahunBulan::class, ['id' => 'tahun_bulan_id']);
    }

    public static function getSampelLabel($id_jenis_barang, $jumlah_data)
    {
        //mencari data training berdasarkan jenis barang
        // $penjualan_barang = Penjualan::find()->joinWith(['jenisBarangHasPenjualans jp'])->where(['jp.jenis_barang_id' => $id_jenis_barang])->all();
        //mangambil data training tanpa menperhatikan jenis barang
        $penjualan_barang = Penjualan::find()->joinWith(['jenisBarangHasPenjualans jp'])->where(['jp.jenis_barang_id' => $id_jenis_barang])->all();

        $sample = [];
        $labels = [];
        $id_sample = [];

        foreach ($penjualan_barang as $no_sample => $dp) {
            $data_jumlah_penjualan = $dp->jenisBarangHasPenjualans;
            for ($i=0; $i < $jumlah_data; $i++) { 
                $sample[$no_sample][] = isset($data_jumlah_penjualan[$i])? $data_jumlah_penjualan[$i]->jumlah_penjualan : 0;
            }
            $labels[$no_sample] = $dp->label;
            $id_sample[$no_sample] = $dp->id;
        }

        return ['sample' => $sample, 'label' => $labels, 'id_sample' => $id_sample];
    }

    public static function prediksi($data, $id_jenis_barang, $k, $jumlah_data=8)
    {
        try {
            $knn = new KNearestNeighbors($k, new Euclidean());

            $dataTraining = self::getSampelLabel($id_jenis_barang, $jumlah_data);
            $sample = $dataTraining['sample'];
            $lables = $dataTraining['label'];

            // ambil min max data 
            $summary_data = self::getMinMaxData($sample);
            // normalisasi data
            $sample_normalisasi = self::normalisasiData($sample, $summary_data);
            
            $knn->train($sample_normalisasi, $lables);

            $hasil_prediksi = $knn->predict($data);
            
            return ['data_training' => $sample, 'result' => $hasil_prediksi];

        } catch (\Throwable $th) {
            throw new ServerErrorHttpException('Terjadi masalah: '.$th->getLine().' - '. $th->getMessage());
        }

    }

    public static function hitungDistanceTraining($id_jenis_barang, Array $samples, Array $sample_predict, $k = null)
    {
        $dataTraining = PrediksiPenjualan::getSampelLabel($id_jenis_barang, 8);
        
        if(!is_array($samples)){
            $samples = $dataTraining['sample'];
        }
        // $label = $dataTraining['label'];

        $distances = [];
        $distaceMatrict = new Euclidean();

        foreach ($samples as $index => $neighbor) {
            $distances[$index] = $distaceMatrict->distance($sample_predict, $neighbor);
        }
        asort($distances);
        
        if($k != null){
            asort($distances);
            return array_slice($distances, 0, $k, true);
        }else{
            return $distances;
        }
    }

    public static function getMinMaxData(Array $dataSet)
    {
        $jumlah_kolom = null;
        $summary = [];

        // Mencari nilai maximum dan minimum dari data
        if(is_array($dataSet)){

            $col = [];
            foreach ($dataSet as $i => $data) {
                if(is_array($data)){
                    if($jumlah_kolom == null) $jumlah_kolom = count($data);

                    if(count($data) == $jumlah_kolom){

                        foreach ($data as $i_col => $nilai) {
                            if(is_array($nilai)) throw new \Exception("Terjadi kesalahan struktur data", 1);
                            
                            $col[$i_col][] = $nilai;

                        }


                    }else{
                        throw new \Exception("Jumlah kolom baris ke-".($i+1)." tidak sama dengan baris yang lain");
                    }

                }else{
                    throw new \Exception("Bukan tipe data array", 1);
                }
            }


            foreach ($col as $i => $data_col) {
                $summary[$i]['max'] = max($data_col); 
                $summary[$i]['min'] = min($data_col); 
            }


            return $summary;

        }else{
            throw new \Exception("Bukan tipe data array", 1);
        }
    }


    public static function normalisasiData($sampel, $summary)
    {
        $hasil_normalisasi = [];

        if(is_array($sampel)){

            foreach ($sampel as $i_row => $data) {
                
                if(is_array($data)){

                    foreach ($data as $i_col => $nilai) {
                        // normalisasi min max
                        $min = $summary[$i_col]['min'];// nilai min kolom
                        $max = $summary[$i_col]['max'];// nilai max kolom

                        $hasil_normalisasi[$i_row][$i_col] =round((float) ($nilai - $min) / ($max - $min), 2);
                    }


                }else{
                    throw new \Exception("Bukan tipe data array", 1);
                }
            }

            return $hasil_normalisasi;
        }else{
            throw new \Exception("Bukan tipe data array", 1);
        }
    }
}
