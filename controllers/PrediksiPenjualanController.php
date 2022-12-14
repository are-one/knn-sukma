<?php

namespace app\controllers;

use app\models\JenisBarang;
use app\models\JenisBarangHasPenjualan;
use app\models\JenisBarangHasPrediksiPenjualan;
use app\models\Penjualan;
use app\models\PrediksiPenjualan;
use app\models\search\PrediksiPenjualanSearch;
use app\models\TahunBulan;
use Phpml\Classification\KNearestNeighbors;
use Phpml\Math\Distance\Euclidean;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

/**
 * PrediksiPenjualanController implements the CRUD actions for PrediksiPenjualan model.
 */
class PrediksiPenjualanController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all PrediksiPenjualan models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PrediksiPenjualanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        $jenisBarang = JenisBarang::find()->all();
        $modelJenisBarangHasPenjualan = new JenisBarangHasPenjualan();

        return $this->render('index', [
            'jenisBarang' => $jenisBarang,
            'modelJenisBarangHasPenjualan' => $modelJenisBarangHasPenjualan,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PrediksiPenjualan model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $jenisBarang = JenisBarang::find()->all();

        return $this->render('view', [
            'model' => $this->findModel($id),
            'jenisBarang' => $jenisBarang,
        ]);
    }

    /**
     * Creates a new PrediksiPenjualan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        try {
            $transaction = Yii::$app->db->beginTransaction();
    
            $modelDataPrediksi = new JenisBarangHasPrediksiPenjualan();
            $model = new PrediksiPenjualan();
    
            if ($this->request->isPost) {
    
                if ($model->load($this->request->post()) && $modelDataPrediksi->load($this->request->post())) {
                    $jumlah_penjualan = $this->request->post($modelDataPrediksi->formName())['jumlah_penjualan'];
                    $dataPrediksi =  $this->request->post($modelDataPrediksi->formName());
    
                    $data_jumlah_penjualan = $dataPrediksi['jumlah_penjualan'];
                    $jenis_barang_id = $dataPrediksi['jenis_barang_id'];

                    if(array_sum($data_jumlah_penjualan) < 1){
                        Yii::$app->session->setFlash('error', 'Tidak ada data yang ditambahkan');
                        return $this->redirect(['index']);
                    }

                    $prediksi = PrediksiPenjualan::prediksi($data_jumlah_penjualan, $jenis_barang_id, 3);
                    if(count($prediksi['data_training']) < 1){
                        Yii::$app->session->setFlash('error','Data training tidak ditemukan');
                        return $this->redirect(['index']);
                    }else{
                        $model->hasil_prediksi = $prediksi['result'];
                    }
    
                    if($model->save()){
    
                        $hasSaved = true;
                        $banyak_data = 0;
    
                        foreach ($data_jumlah_penjualan as $_ => $jumlah_penjualan) {

                            $banyak_data++;
                            $jenisBarangHasPrediksiPenjualan = new JenisBarangHasPrediksiPenjualan();
                            $jenisBarangHasPrediksiPenjualan->prediksi_penjualan_id = $model->id;
                            $jenisBarangHasPrediksiPenjualan->jenis_barang_id = $jenis_barang_id;
                            $jenisBarangHasPrediksiPenjualan->jumlah_penjualan = $jumlah_penjualan;
    
                            if(!$jenisBarangHasPrediksiPenjualan->save()){
                                print_r($jenisBarangHasPrediksiPenjualan->getErrors());die;
                                $hasSaved = false;
                                break;
                            }
                        }
    
                        if($banyak_data < 1){
                            $hasSaved = false;
                        }
    
                        if(!$hasSaved){
                            $transaction->rollBack();
                            if($banyak_data < 1){
                                Yii::$app->session->setFlash('error', 'Tidak ada data yang ditambahkan');
                            }else{
                                Yii::$app->session->setFlash('error', 'Data gagal ditambahkan');
                            }
                        }else{
                            $transaction->commit();
                            Yii::$app->session->setFlash('success', 'Data berhasil ditambahkan');
                            return $this->redirect(['index']);
                        }
    
                    }else{
                        print_r($model->getErrors());die;
                        $transaction->rollBack();
                    }
    
                    return $this->redirect(['index']);
                }
    
            } else {
                $model->loadDefaultValues();
                $modelDataPrediksi->loadDefaultValues();
            }
    
            return $this->render('create', [
                'model' => $model,
                'modelDataPrediksi' => $modelDataPrediksi,
            ]);

        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('Terjadi masalah : '.$th->getLine(). ' - '. $th->getMessage());
        }
    }

    /**
     * Updates an existing PrediksiPenjualan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PrediksiPenjualan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        try {
            if(JenisBarangHasPrediksiPenjualan::deleteAll(['prediksi_penjualan_id' => $id])){
                $this->findModel($id)->delete();
                Yii::$app->session->setFlash('success', 'Data prediksi penjualan berhasil dihapus');
            }else{
                Yii::$app->session->setFlash('error', 'Data prediksi penjualan gagal dihapus');
            }

            return $this->redirect(['index']);
        } catch (\Throwable $th) {
            throw new ServerErrorHttpException('Terjadi masalah: '.$th->getLine().' - '. $th->getMessage());
        }

    }

    public function actionDeleteAll()
    {
        try {
            if(JenisBarangHasPrediksiPenjualan::deleteAll()){
                PrediksiPenjualan::deleteAll();
                Yii::$app->session->setFlash('success', 'Semua Data prediksi penjualan berhasil dihapus');
            }else{
                Yii::$app->session->setFlash('error', 'Semua Data prediksi penjualan gagal dihapus');
            }

            return $this->redirect(['index']);
        } catch (\Throwable $th) {
            throw new ServerErrorHttpException('Terjadi masalah: '.$th->getLine().' - '. $th->getMessage());
        }

    }

    /**
     * Finds the PrediksiPenjualan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return PrediksiPenjualan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PrediksiPenjualan::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionDeleteJumlahPenjualan($id)
    {
        try {
            $jenisBarangHasPrediksiPenjualan = JenisBarangHasPrediksiPenjualan::findOne(['id' => $id]);
            $prediksi_penjualan_id = $jenisBarangHasPrediksiPenjualan->prediksi_penjualan_id;

            // Hitung jumlah data
            $jumlah_data = JenisBarangHasPrediksiPenjualan::find()->where(['prediksi_penjualan_id' => $prediksi_penjualan_id])->count();
            
            if($jenisBarangHasPrediksiPenjualan->delete()){
                Yii::$app->session->setFlash('success', 'Data penjualan berhasil dihapus');
            }else{
                Yii::$app->session->setFlash('error', 'Data penjualan gagal dihapus');
            }

            // Jika tersisa 1 maka hapus data Penjualan
            if($jumlah_data == 1){
                $modelPrediksiPenjualan = PrediksiPenjualan::findOne(['id' => $prediksi_penjualan_id]);
                $modelPrediksiPenjualan->delete();
            }

            return $this->redirect(['index']);
            
        } catch (\Throwable $th) {
            throw new ServerErrorHttpException('Terjadi masalah: '. $th->getMessage());
        }
        
    }

    public function actionPindahKeTraining($id_prediksi)
    {
        try {
            $transaction = Yii::$app->db->beginTransaction();
            $modelPrediksi = $this->findModel($id_prediksi);
            $dataJumlahPenjualan = JenisBarangHasPrediksiPenjualan::findAll(['prediksi_penjualan_id' => $modelPrediksi->id]);

            $modelPenjualan = new Penjualan();
            $modelPenjualan->tahun_bulan_id = $modelPrediksi->tahun_bulan_id;
            $modelPenjualan->label = $modelPrediksi->hasil_prediksi;

            if($modelPenjualan->save()){
                $hasSaved = true;
                foreach ($dataJumlahPenjualan as $_ => $jp) {
                    $modelJumlahPenjualan = new JenisBarangHasPenjualan();
                    $modelJumlahPenjualan->penjualan_id = $modelPenjualan->id;
                    $modelJumlahPenjualan->jumlah_penjualan = $jp->jumlah_penjualan;
                    $modelJumlahPenjualan->jenis_barang_id = $jp->jenis_barang_id;

                    if(!$modelJumlahPenjualan->save()){
                        break;
                    }                    
                }

                if($hasSaved){
                    Yii::$app->session->setFlash('success', 'Data berhasil dipindahkan');
                    $transaction->rollBack();
                }else{
                    Yii::$app->session->setFlash('error', 'Data gagal dipindahkan');
                    $transaction->rollBack();
                }
                
            }else{
                Yii::$app->session->setFlash('error', 'Data gagal dipindahkan');
                $transaction->rollBack();
            }
            
            return $this->redirect(['index']);
        } catch (\Throwable $th) {
            throw new ServerErrorHttpException('Terjadi masalah: '. $th->getMessage());
        }

    }

    // ====================================================================================================
    public function actionTemplate()
    {
        try {
            $filePath = Yii::getAlias('@app/assets/template/template-testing.xlsx');
            $attachmentName = 'Template-upload-data-testing.xlsx';

            return $this->response->sendFile($filePath,$attachmentName);
        } catch (\Throwable $th) {
            Yii::$app->session->setFlash("error", "Terjadi masalah, silahkan hubungi operator");
        }

        return $this->redirect(['index']);
        
    }

    public function actionUpload()
    {
        try {
            $fileExcel = UploadedFile::getInstanceByName('file_data');

            if($fileExcel){
                $jenis_barang_id = $this->request->post('jenis_barang_id');

                $this->uploadData($jenis_barang_id,$fileExcel->tempName);
                
                \Yii::$app->session->setFlash('success','Data berhasil diupload.');
            }else{
                \Yii::$app->session->setFlash('error', 'Data gagal diupload.');
            }
        
        } catch (\Exception $e) {
            Yii::$app->session->setFlash("error", "Terjadi masalah, silahkan hubungi operator".$e->getMessage());
        }
        
        return $this->redirect(['index']);
    }

    protected function uploadData($jenis_barang_id,$temp)
    {
        if($this->request->isPost){
            // Mengambil data jenis barang
            
            // LOAD DATA FROM TEMP FILE
            $reader = IOFactory::load($temp);
            
            $colBulan = "A";
            $colTahun = "B";
            $colData1 = "C";
            $colData2 = "D";
            $colData3 = "E";
            $colData4 = "F";
            $colData5 = "G";
            $colData6 = "H";
            $colData7 = "I";
            $colData8 = "J";
            $colLabel = "K";
            $startRow = 3;
            $endRow = $reader->getActiveSheet()->getHighestRow($colBulan);
            
            $gagal = 0;
            $berhasil = 0;
            for ($i=$startRow; $i <= $endRow ; $i++) { 
                $transaction = Yii::$app->db->beginTransaction();

                $bulan = trim($reader->getActiveSheet()->getCell($colBulan.$i)->getValue());
                $tahun = $reader->getActiveSheet()->getCell($colTahun.$i)->getValue();
                $dt1 = $reader->getActiveSheet()->getCell($colData1.$i)->getValue();
                $dt2 = $reader->getActiveSheet()->getCell($colData2.$i)->getValue();
                $dt3 = $reader->getActiveSheet()->getCell($colData3.$i)->getValue();
                $dt4 = $reader->getActiveSheet()->getCell($colData4.$i)->getValue();
                $dt5 = $reader->getActiveSheet()->getCell($colData5.$i)->getValue();
                $dt6 = $reader->getActiveSheet()->getCell($colData6.$i)->getValue();
                $dt7 = $reader->getActiveSheet()->getCell($colData7.$i)->getValue();
                $dt8 = $reader->getActiveSheet()->getCell($colData8.$i)->getValue();
                $label = $reader->getActiveSheet()->getCell($colLabel.$i)->getValue();

                $data_jumlah_penjualan = [$dt1, $dt2, $dt3, $dt4, $dt5, $dt6, $dt7, $dt8];
                $prediksi = PrediksiPenjualan::prediksi($data_jumlah_penjualan, $jenis_barang_id, 3);

                if(count($prediksi['data_training']) < 1){
                    Yii::$app->session->setFlash('error','Data training tidak ditemukan');
                    return $this->redirect(['index']);
                }else{
                    $hasil_prediksi = $prediksi['result'];
                }
                // Cek data tahun bulan jika ada
                // jika tidak ada maka buat data baru

                if(in_array($tahun, [null, ' ', '']) || in_array($bulan, [null, ' ', ''])){
                    Yii::$app->session->setFlash('error', 'Ada data yang tidak memilik tahun atau bulan');
                    return false;
                }


                $tahun_bulan = TahunBulan::findOne(['tahun' => $tahun, 'bulan' => $bulan]);
                
                if($tahun_bulan == null){
                    $modelTahunBulan = new TahunBulan();

                    $modelTahunBulan->tahun = $tahun;
                    $modelTahunBulan->bulan = $bulan;

                    if($modelTahunBulan->save()){
                        $prediksiPenjualanBaru = new PrediksiPenjualan();
                        $prediksiPenjualanBaru->tahun_bulan_id = $modelTahunBulan->id;
                        $prediksiPenjualanBaru->hasil_prediksi = $hasil_prediksi;

                        if($prediksiPenjualanBaru->save()){
                            $query = new \yii\db\Query();
                            
                            // Simpan jumlah penjualan
                            if($query->createCommand()->batchInsert(JenisBarangHasPrediksiPenjualan::tableName(),['prediksi_penjualan_id', 'jenis_barang_id', 'jumlah_penjualan'],[
                                [$prediksiPenjualanBaru->id, $jenis_barang_id, $dt1],
                                [$prediksiPenjualanBaru->id, $jenis_barang_id, $dt2],
                                [$prediksiPenjualanBaru->id, $jenis_barang_id, $dt3],
                                [$prediksiPenjualanBaru->id, $jenis_barang_id, $dt4],
                                [$prediksiPenjualanBaru->id, $jenis_barang_id, $dt5],
                                [$prediksiPenjualanBaru->id, $jenis_barang_id, $dt6],
                                [$prediksiPenjualanBaru->id, $jenis_barang_id, $dt7],
                                [$prediksiPenjualanBaru->id, $jenis_barang_id, $dt8],
                            ])->execute()){

                                $berhasil++;
                                $transaction->commit();
                            }else{
                                $gagal++;
                                $transaction->rollBack();
                            }

                        }else{
                            $gagal++;
                            $transaction->rollBack();
                        }
                        
                    }else{
                        $gagal++;
                        $transaction->rollBack();
                    }
                }else{
                    $prediksiPenjualanBaru = new PrediksiPenjualan();
                    $prediksiPenjualanBaru->tahun_bulan_id = $tahun_bulan->id;
                    $prediksiPenjualanBaru->hasil_prediksi = $hasil_prediksi;

                    if($prediksiPenjualanBaru->save()){
                        $query = new \yii\db\Query();
                        
                        // Simpan jumlah penjualan
                        if($query->createCommand()->batchInsert(JenisBarangHasPrediksiPenjualan::tableName(),['prediksi_penjualan_id', 'jenis_barang_id', 'jumlah_penjualan'],[
                            [$prediksiPenjualanBaru->id, $jenis_barang_id, $dt1],
                            [$prediksiPenjualanBaru->id, $jenis_barang_id, $dt2],
                            [$prediksiPenjualanBaru->id, $jenis_barang_id, $dt3],
                            [$prediksiPenjualanBaru->id, $jenis_barang_id, $dt4],
                            [$prediksiPenjualanBaru->id, $jenis_barang_id, $dt5],
                            [$prediksiPenjualanBaru->id, $jenis_barang_id, $dt6],
                            [$prediksiPenjualanBaru->id, $jenis_barang_id, $dt7],
                            [$prediksiPenjualanBaru->id, $jenis_barang_id, $dt8],
                        ])->execute()){
                            $berhasil++;
                            $transaction->commit();
                        }else{
                            $gagal++;
                            $transaction->rollBack();
                        }
                        
                    }else{
                        $gagal++;
                        $transaction->rollBack();
                    }

                }                


            }

            Yii::$app->session->setFlash('success',$berhasil.' data berhasil disimpan, '. $gagal . ' disimpan');
            return;
        }
    }

    public function actionRefresh($id)
    {
        $prediksiPenjualan = PrediksiPenjualan::findOne(['id' => $id]);

        if(!empty($prediksiPenjualan->jenisBarangHasPrediksiPenjualans)){
                       
            $jumlahPenjualan = $prediksiPenjualan->jenisBarangHasPrediksiPenjualans;
            $data_prediksi = [];
            $jenis_barang_id = null;

            foreach ($jumlahPenjualan as $_ => $jp) {    
                $data_prediksi[] = $jp->jumlah_penjualan;
                $jenis_barang_id = $jp->jenis_barang_id;
            }

            $prediksi = PrediksiPenjualan::prediksi($data_prediksi, $jenis_barang_id, 3);

            if(count($prediksi['data_training']) < 1){
                Yii::$app->session->setFlash('error','Data training tidak ditemukan');
                return $this->redirect(['index']);
            }else{
                $prediksiPenjualan->hasil_prediksi = $prediksi['result'];
            }

            if($prediksiPenjualan->save()){
                Yii::$app->session->setFlash('success', 'Data berhasil refresh');
            }else{
                Yii::$app->session->setFlash('success', 'Data berhasil refresh');
            }

            return $this->redirect(['view', 'id' => $prediksiPenjualan->id]);
        }
    }
}
