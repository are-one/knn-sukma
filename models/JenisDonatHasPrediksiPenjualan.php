<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "jenis_donat_has_prediksi_penjualan".
 *
 * @property int $id
 * @property int $jenis_donat_id
 * @property int $prediksi_penjualan_id
 * @property int $jumlah_penjualan
 *
 * @property JenisDonat $jenisDonat
 * @property PrediksiPenjualan $prediksiPenjualan
 */
class JenisDonatHasPrediksiPenjualan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jenis_donat_has_prediksi_penjualan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenis_donat_id', 'prediksi_penjualan_id'], 'required'],
            [['jenis_donat_id', 'prediksi_penjualan_id', 'jumlah_penjualan'], 'integer'],
            [['jenis_donat_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenisDonat::class, 'targetAttribute' => ['jenis_donat_id' => 'id']],
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
            'jenis_donat_id' => 'Jenis Donat ID',
            'prediksi_penjualan_id' => 'Prediksi Penjualan ID',
            'jumlah_penjualan' => 'Jumlah Penjualan',
        ];
    }

    /**
     * Gets query for [[JenisDonat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJenisDonat()
    {
        return $this->hasOne(JenisDonat::class, ['id' => 'jenis_donat_id']);
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
