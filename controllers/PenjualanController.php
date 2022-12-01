<?php

namespace app\controllers;

use app\models\JenisBarang;
use app\models\JenisBarangHasPenjualan;
use app\models\JenisBarangHasPrediksiPenjualan;
use app\models\Penjualan;
use app\models\search\PenjualanSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ServerErrorHttpException;

/**
 * PenjualanController implements the CRUD actions for Penjualan model.
 */
class PenjualanController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                        'delete-jumlah-penjualan' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Penjualan models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PenjualanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        $jenisBarang = JenisBarang::find()->all();


        return $this->render('index', [
            'jenisBarang' => $jenisBarang,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Penjualan model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Penjualan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        // try {
            $transaction = Yii::$app->db->beginTransaction();
            
            $model = new Penjualan();
            $modelJenisBarangHasPenjualan = new JenisBarangHasPenjualan();


            if ($this->request->isPost) {
                
                if ($model->load($this->request->post()) && $modelJenisBarangHasPenjualan->load($this->request->post())) {
                    $postJenisBarangHasPenjualan =  $this->request->post($modelJenisBarangHasPenjualan->formName());
                    $dataJumlahPenjualan = $postJenisBarangHasPenjualan['jumlah_penjualan'];
                    $jenis_barang_id = $postJenisBarangHasPenjualan['jenis_barang_id'];
                    
                    $modelLama = Penjualan::findOne(['tahun_bulan_id' => $model->tahun_bulan_id]);
                    if($modelLama != null){
                        $modelLama->label = $model->label;
                        $model = $modelLama;
                    }

                    if($model->save()){

                        $hasSaved = true;
                        $banyak_data = 0;

                        foreach ($dataJumlahPenjualan as $_ => $jumlah_penjualan) {
                            if($jumlah_penjualan == 0) continue;

                            $banyak_data++;
                            $jenisBarangHasPenjualan = new JenisBarangHasPenjualan();
                            $jenisBarangHasPenjualan->penjualan_id = $model->id;
                            $jenisBarangHasPenjualan->jenis_barang_id = $jenis_barang_id;
                            $jenisBarangHasPenjualan->jumlah_penjualan = $jumlah_penjualan;

                            if(!$jenisBarangHasPenjualan->save()){
                                print_r($jenisBarangHasPenjualan->getErrors());die;
                                $hasSaved = false;
                                break;
                            }
                        }

                        if($banyak_data < 1){
                            $hasSaved = false;
                        }

                        if(!$hasSaved){
                            $transaction->rollBack();
                            if($banyak_data < 1){
                                Yii::$app->session->setFlash('error', 'Tidak ada data yang ditambahkan');
                            }else{
                                Yii::$app->session->setFlash('error', 'Data gagal ditambahkan');
                            }
                        }else{
                            $transaction->commit();
                            Yii::$app->session->setFlash('success', 'Data berhasil ditambahkan');
                            return $this->redirect(['index']);
                        }

                    }else{
                        $transaction->rollBack();
                    }
                    
                    return $this->redirect(['index']);
                }
            } else {
                $model->loadDefaultValues();
                $modelJenisBarangHasPenjualan->loadDefaultValues();
            }

            return $this->render('create', [
                'model' => $model,
                'modelJenisBarangHasPenjualan' => $modelJenisBarangHasPenjualan,
            ]);  

        // } catch (\Throwable $th) {
        //     $transaction->rollBack();
        //     throw new ServerErrorHttpException('Terjadi masalah : '.$th->getLine(). ' - '. $th->getMessage());
        // }
        
    }

    /**
     * Updates an existing Penjualan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Penjualan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        try {
            if(JenisBarangHasPenjualan::deleteAll(['penjualan_id' => $id])){
                $this->findModel($id)->delete();
                Yii::$app->session->setFlash('success', 'Data penjualan berhasil dihapus');
            }else{
                Yii::$app->session->setFlash('error', 'Data penjualan gagal dihapus');
            }
            
            return $this->redirect(['index']);
        } catch (\Throwable $th) {
            throw new ServerErrorHttpException('Terjadi masalah: '.$th->getLine().' - '. $th->getMessage());
        }
    }

    /**
     * Finds the Penjualan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Penjualan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Penjualan::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionDeleteJumlahPenjualan($id)
    {
        try {
            $modelJenisBarangHasPenjualan = JenisBarangHasPenjualan::findOne(['id' => $id]);
            $penjualan_id = $modelJenisBarangHasPenjualan->penjualan_id;

            // Hitung jumlah data
            $jumlah_data = JenisBarangHasPenjualan::find()->where(['penjualan_id' => $penjualan_id])->count();
            
            if($modelJenisBarangHasPenjualan->delete()){
                Yii::$app->session->setFlash('success', 'Data penjualan berhasil dihapus');
            }else{
                Yii::$app->session->setFlash('error', 'Data penjualan gagal dihapus');
            }

            // Jika tersisa 1 maka hapus data Penjualan
            if($jumlah_data == 1){
                $modelPenjualan = Penjualan::findOne(['id' => $penjualan_id]);
                $modelPenjualan->delete();
            }

            return $this->redirect(['index']);
            
        } catch (\Throwable $th) {
            throw new ServerErrorHttpException('Terjadi masalah: '. $th->getMessage());
        }

        
    }
}
