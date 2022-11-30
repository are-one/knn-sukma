<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "jenis_barang_has_penjualan".
 *
 * @property int $id
 * @property int $penjualan_id
 * @property int $jenis_barang_id
 * @property int $jumlah_penjualan
 *
 * @property JenisBarang $jenisBarang
 * @property Penjualan $penjualan
 */
class JenisBarangHasPenjualan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jenis_barang_has_penjualan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['penjualan_id', 'jenis_barang_id'], 'required'],
            [['penjualan_id', 'jenis_barang_id', 'jumlah_penjualan'], 'integer'],
            [['penjualan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Penjualan::class, 'targetAttribute' => ['penjualan_id' => 'id']],
            [['jenis_barang_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenisBarang::class, 'targetAttribute' => ['jenis_barang_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'penjualan_id' => 'Penjualan ID',
            'jenis_barang_id' => 'Jenis Barang ID',
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
     * Gets query for [[Penjualan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPenjualan()
    {
        return $this->hasOne(Penjualan::class, ['id' => 'penjualan_id']);
    }
}
