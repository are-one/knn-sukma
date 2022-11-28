<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "penjualan".
 *
 * @property int $id
 * @property int $tahun_bulan_id
 * @property string $label
 *
 * @property JenisDonatHasPenjualan[] $jenisDonatHasPenjualans
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
            [['tahun_bulan_id', 'label'], 'required'],
            [['tahun_bulan_id'], 'integer'],
            [['label'], 'string', 'max' => 20],
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
            'label' => 'Label',
        ];
    }

    /**
     * Gets query for [[JenisDonatHasPenjualans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJenisDonatHasPenjualans()
    {
        return $this->hasMany(JenisDonatHasPenjualan::class, ['penjualan_id' => 'id']);
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
