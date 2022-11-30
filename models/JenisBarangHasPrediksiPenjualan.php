<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "jenis_barang_has_prediksi_penjualan".
 *
 * @property int $id
 * @property int $jenis_barang_id
 * @property int $prediksi_penjualan_id
 * @property int $jumlah_penjualan
 *
 * @property JenisBarang $jenisBarang
 * @property PrediksiPenjualan $prediksiPenjualan
 */
class JenisBarangHasPrediksiPenjualan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jenis_barang_has_prediksi_penjualan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenis_barang_id', 'prediksi_penjualan_id'], 'required'],
            [['jenis_barang_id', 'prediksi_penjualan_id', 'jumlah_penjualan'], 'integer'],
            [['jenis_barang_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenisBarang::class, 'targetAttribute' => ['jenis_barang_id' => 'id']],
            [['prediksi_penjualan_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrediksiPenjualan::class, 'targetAttribute' => ['prediksi_penjualan_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'jenis_barang_id' => 'Jenis Barang ID',
            'prediksi_penjualan_id' => 'Prediksi Penjualan ID',
            'jumlah_penjualan' => 'Jumlah Penjualan',
        ];
    }

    /**
     * Gets query for [[JenisBarang]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJenisBarang()
    {
        return $this->hasOne(JenisBarang::class, ['id' => 'jenis_barang_id']);
    }

    /**
     * Gets query for [[PrediksiPenjualan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrediksiPenjualan()
    {
        return $this->hasOne(PrediksiPenjualan::class, ['id' => 'prediksi_penjualan_id']);
    }
}
