<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "jenis_barang".
 *
 * @property int $id
 * @property string $jenis_barang
 *
 * @property JenisBarangHasPenjualan[] $jenisBarangHasPenjualans
 * @property JenisBarangHasPrediksiPenjualan[] $jenisBarangHasPrediksiPenjualans
 */
class JenisBarang extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jenis_barang';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenis_barang'], 'required'],
            [['jenis_barang'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'jenis_barang' => 'Jenis Barang',
        ];
    }

    /**
     * Gets query for [[JenisBarangHasPenjualans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJenisBarangHasPenjualans()
    {
        return $this->hasMany(JenisBarangHasPenjualan::class, ['jenis_barang_id' => 'id']);
    }

    /**
     * Gets query for [[JenisBarangHasPrediksiPenjualans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJenisBarangHasPrediksiPenjualans()
    {
        return $this->hasMany(JenisBarangHasPrediksiPenjualan::class, ['jenis_barang_id' => 'id']);
    }
}
