<?php

namespace app\controllers;

use app\models\JenisDonat;
use app\models\JenisDonatHasPenjualan;
use app\models\JenisDonatHasPrediksiPenjualan;
use app\models\Penjualan;
use app\models\search\PenjualanSearch;
use Yii;
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

        $jenisDonat = JenisDonat::find()->all();


        return $this->render('index', [
            'jenisDonat' => $jenisDonat,
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
            $modelJenisDonatHasPenjualan = new JenisDonatHasPenjualan();


            if ($this->request->isPost) {
                
                if ($model->load($this->request->post()) && $modelJenisDonatHasPenjualan->load($this->request->post())) {
                    $postJenisDonatHasPenjualan =  $this->request->post($modelJenisDonatHasPenjualan->formName());
                    $dataJumlahPenjualan = $postJenisDonatHasPenjualan['jumlah_penjualan'];
                    $jenis_donat_id = $postJenisDonatHasPenjualan['jenis_donat_id'];
                    
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
                            $jenisDonatHasPenjualan = new JenisDonatHasPenjualan();
                            $jenisDonatHasPenjualan->penjualan_id = $model->id;
                            $jenisDonatHasPenjualan->jenis_donat_id = $jenis_donat_id;
                            $jenisDonatHasPenjualan->jumlah_penjualan = $jumlah_penjualan;

                            if(!$jenisDonatHasPenjualan->save()){
                                print_r($jenisDonatHasPenjualan->getErrors());die;
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
                $modelJenisDonatHasPenjualan->loadDefaultValues();
            }

            return $this->render('create', [
                'model' => $model,
                'modelJenisDonatHasPenjualan' => $modelJenisDonatHasPenjualan,
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
            if(JenisDonatHasPenjualan::deleteAll(['penjualan_id' => $id])){
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
            $modelJenisDonatHasPenjualan = JenisDonatHasPenjualan::findOne(['id' => $id]);
            $penjualan_id = $modelJenisDonatHasPenjualan->penjualan_id;

            // Hitung jumlah data
            $jumlah_data = JenisDonatHasPenjualan::find()->where(['penjualan_id' => $penjualan_id])->count();
            
            if($modelJenisDonatHasPenjualan->delete()){
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
