<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "jenis_donat".
 *
 * @property int $id
 * @property string $jenis_donat
 *
 * @property JenisDonatHasPrediksiPenjualan[] $jenisDonatHasPrediksiPenjualans
 * @property Penjualan[] $penjualans
 */
class JenisDonat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jenis_donat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenis_donat'], 'required'],
            [['jenis_donat'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'jenis_donat' => 'Jenis Donat',
        ];
    }

    /**
     * Gets query for [[JenisDonatHasPrediksiPenjualans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJenisDonatHasPrediksiPenjualans()
    {
        return $this->hasMany(JenisDonatHasPrediksiPenjualan::class, ['jenis_donat_id' => 'id']);
    }

    /**
     * Gets query for [[Penjualans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPenjualans()
    {
        return $this->hasMany(Penjualan::class, ['jenis_donat_id' => 'id']);
    }
}
