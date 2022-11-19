<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tahun_bulan".
 *
 * @property int $id
 * @property int $tahun
 * @property string $bulan
 *
 * @property Penjualan[] $penjualans
 * @property PrediksiPenjualan[] $prediksiPenjualans
 */
class TahunBulan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tahun_bulan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tahun', 'bulan'], 'required'],
            [['tahun'], 'integer'],
            [['bulan'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tahun' => 'Tahun',
            'bulan' => 'Bulan',
        ];
    }

    /**
     * Gets query for [[Penjualans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPenjualans()
    {
        return $this->hasMany(Penjualan::class, ['tahun_bulan_id' => 'id']);
    }

    /**
     * Gets query for [[PrediksiPenjualans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrediksiPenjualans()
    {
        return $this->hasMany(PrediksiPenjualan::class, ['tahun_bulan_id' => 'id']);
    }
}
