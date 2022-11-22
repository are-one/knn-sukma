<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "penjualan_has_jenis_donat".
 *
 * @property int $penjualan_id
 * @property int $jenis_donat_id
 * @property int $jumlah_penjualan
 *
 * @property JenisDonat $jenisDonat
 * @property Penjualan $penjualan
 */
class PenjualanHasJenisDonat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'penjualan_has_jenis_donat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['penjualan_id', 'jenis_donat_id'], 'required'],
            [['penjualan_id', 'jenis_donat_id', 'jumlah_penjualan'], 'integer'],
            [['penjualan_id', 'jenis_donat_id'], 'unique', 'targetAttribute' => ['penjualan_id', 'jenis_donat_id']],
            [['jenis_donat_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenisDonat::class, 'targetAttribute' => ['jenis_donat_id' => 'id']],
            [['penjualan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Penjualan::class, 'targetAttribute' => ['penjualan_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'penjualan_id' => 'Penjualan ID',
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
     * Gets query for [[Penjualan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPenjualan()
    {
        return $this->hasOne(Penjualan::class, ['id' => 'penjualan_id']);
    }
}
