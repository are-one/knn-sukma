<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "prediksi_penjualan".
 *
 * @property int $id
 * @property string $hasil_prediksi
 * @property int $tahun_bulan_id
 *
 * @property JenisDonatHasPrediksiPenjualan[] $jenisDonatHasPrediksiPenjualans
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
     * Gets query for [[JenisDonatHasPrediksiPenjualans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJenisDonatHasPrediksiPenjualans()
    {
        return $this->hasMany(JenisDonatHasPrediksiPenjualan::class, ['prediksi_penjualan_id' => 'id']);
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
}
