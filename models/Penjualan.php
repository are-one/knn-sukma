<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "penjualan".
 *
 * @property int $id
 * @property int $tahun_bulan_id
 * @property int $jenis_donat_id
 * @property int $jumlah_penjualan
 *
 * @property JenisDonat $jenisDonat
 * @property TahunBulan $tahunBulan
 */
class Penjualan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'penjualan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tahun_bulan_id', 'jenis_donat_id'], 'required'],
            [['tahun_bulan_id', 'jenis_donat_id', 'jumlah_penjualan'], 'integer'],
            [['jenis_donat_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenisDonat::class, 'targetAttribute' => ['jenis_donat_id' => 'id']],
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
            'tahun_bulan_id' => 'Tahun Bulan ID',
            'jenis_donat_id' => 'Jenis Donat ID',
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
     * Gets query for [[TahunBulan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTahunBulan()
    {
        return $this->hasOne(TahunBulan::class, ['id' => 'tahun_bulan_id']);
    }
}
