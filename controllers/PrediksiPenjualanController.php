<?php

namespace app\controllers;

use app\models\JenisBarang;
use app\models\JenisBarangHasPenjualan;
use app\models\JenisBarangHasPrediksiPenjualan;
use app\models\Penjualan;
use app\models\PrediksiPenjualan;
use app\models\search\PrediksiPenjualanSearch;
use Phpml\Classification\KNearestNeighbors;
use Phpml\Math\Distance\Euclidean;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ServerErrorHttpException;

/**
 * PrediksiPenjualanController implements the CRUD actions for PrediksiPenjualan model.
 */
class PrediksiPenjualanController extends Controller
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
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all PrediksiPenjualan models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PrediksiPenjualanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        $jenisBarang = JenisBarang::find()->all();

        return $this->render('index', [
            'jenisBarang' => $jenisBarang,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PrediksiPenjualan model.
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
     * Creates a new PrediksiPenjualan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        try {
            $transaction = Yii::$app->db->beginTransaction();
    
            $modelDataPrediksi = new JenisBarangHasPrediksiPenjualan();
            $model = new PrediksiPenjualan();
    
            if ($this->request->isPost) {
    
                if ($model->load($this->request->post()) && $modelDataPrediksi->load($this->request->post())) {
                    $jumlah_penjualan = $this->request->post($modelDataPrediksi->formName())['jumlah_penjualan'];
                    $dataPrediksi =  $this->request->post($modelDataPrediksi->formName());
    
                    $data_jumlah_penjualan = $dataPrediksi['jumlah_penjualan'];
                    $jenis_barang_id = $dataPrediksi['jenis_barang_id'];

                    if(array_sum($data_jumlah_penjualan) < 1){
                        Yii::$app->session->setFlash('error', 'Tidak ada data yang ditambahkan');
                        return $this->redirect(['index']);
                    }

                    $prediksi = $this->prediksi($data_jumlah_penjualan, $jenis_barang_id,3);
                    if(count($prediksi['data_training']) < 1){
                        Yii::$app->session->setFlash('error','Data training tidak ditemukan');
                        return $this->redirect(['index']);
                    }else{
                        $model->hasil_prediksi = $prediksi['result'];
                    }
    
                    if($model->save()){
    
                        $hasSaved = true;
                        $banyak_data = 0;
    
                        foreach ($data_jumlah_penjualan as $_ => $jumlah_penjualan) {
                            if($jumlah_penjualan == 0) continue;
    
                            $banyak_data++;
                            $jenisBarangHasPrediksiPenjualan = new JenisBarangHasPrediksiPenjualan();
                            $jenisBarangHasPrediksiPenjualan->prediksi_penjualan_id = $model->id;
                            $jenisBarangHasPrediksiPenjualan->jenis_barang_id = $jenis_barang_id;
                            $jenisBarangHasPrediksiPenjualan->jumlah_penjualan = $jumlah_penjualan;
    
                            if(!$jenisBarangHasPrediksiPenjualan->save()){
                                print_r($jenisBarangHasPrediksiPenjualan->getErrors());die;
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
                        print_r($model->getErrors());die;
                        $transaction->rollBack();
                    }
    
                    return $this->redirect(['index']);
                }
    
            } else {
                $model->loadDefaultValues();
                $modelDataPrediksi->loadDefaultValues();
            }
    
            return $this->render('create', [
                'model' => $model,
                'modelDataPrediksi' => $modelDataPrediksi,
            ]);

        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('Terjadi masalah : '.$th->getLine(). ' - '. $th->getMessage());
        }
    }

    /**
     * Updates an existing PrediksiPenjualan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PrediksiPenjualan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        try {
            if(JenisBarangHasPrediksiPenjualan::deleteAll(['prediksi_penjualan_id' => $id])){
                $this->findModel($id)->delete();
                Yii::$app->session->setFlash('success', 'Data prediksi penjualan berhasil dihapus');
            }else{
                Yii::$app->session->setFlash('error', 'Data prediksi penjualan gagal dihapus');
            }

            return $this->redirect(['index']);
        } catch (\Throwable $th) {
            throw new ServerErrorHttpException('Terjadi masalah: '.$th->getLine().' - '. $th->getMessage());
        }

    }

    /**
     * Finds the PrediksiPenjualan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return PrediksiPenjualan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PrediksiPenjualan::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function prediksi($data, $id_jenis_barang, $k)
    {
        try {
            $knn = new KNearestNeighbors($k, new Euclidean());
            
            $sample = [];
            
            //mencari data training berdasarkan jenis barang
            // $penjualan_barang = Penjualan::find()->joinWith(['jenisBarangHasPenjualans jp'])->where(['jp.jenis_barang_id' => $id_jenis_barang])->all();
            //mangambil data training tanpa menperhatikan jenis barang
            $penjualan_barang = Penjualan::find()->joinWith(['jenisBarangHasPenjualans jp'])->where(['jp.jenis_barang_id' => $id_jenis_barang])->all();

            $sample = [];
            $lables = [];

            foreach ($penjualan_barang as $no_sample => $dp) {
                $data_jumlah_penjualan = $dp->jenisBarangHasPenjualans;
                for ($i=0; $i < 10; $i++) { 
                    $sample[$no_sample][] = isset($data_jumlah_penjualan[$i])? $data_jumlah_penjualan[$i]->jumlah_penjualan : 0;
                }
                $lables[$no_sample] = $dp->label;
            }

        $knn->train($sample, $lables);
        
        return ['data_training' => $sample, 'result' => $knn->predict($data)];

        } catch (\Throwable $th) {
            throw new ServerErrorHttpException('Terjadi masalah: '.$th->getLine().' - '. $th->getMessage());
        }

    }

    public function actionDeleteJumlahPenjualan($id)
    {
        try {
            $jenisBarangHasPrediksiPenjualan = JenisBarangHasPrediksiPenjualan::findOne(['id' => $id]);
            $prediksi_penjualan_id = $jenisBarangHasPrediksiPenjualan->prediksi_penjualan_id;

            // Hitung jumlah data
            $jumlah_data = JenisBarangHasPrediksiPenjualan::find()->where(['prediksi_penjualan_id' => $prediksi_penjualan_id])->count();
            
            if($jenisBarangHasPrediksiPenjualan->delete()){
                Yii::$app->session->setFlash('success', 'Data penjualan berhasil dihapus');
            }else{
                Yii::$app->session->setFlash('error', 'Data penjualan gagal dihapus');
            }

            // Jika tersisa 1 maka hapus data Penjualan
            if($jumlah_data == 1){
                $modelPrediksiPenjualan = PrediksiPenjualan::findOne(['id' => $prediksi_penjualan_id]);
                $modelPrediksiPenjualan->delete();
            }

            return $this->redirect(['index']);
            
        } catch (\Throwable $th) {
            throw new ServerErrorHttpException('Terjadi masalah: '. $th->getMessage());
        }
        
    }

    public function actionPindahKeTraining($id_prediksi)
    {
        try {
            $transaction = Yii::$app->db->beginTransaction();
            $modelPrediksi = $this->findModel($id_prediksi);
            $dataJumlahPenjualan = JenisBarangHasPrediksiPenjualan::findAll(['prediksi_penjualan_id' => $modelPrediksi->id]);

            $modelPenjualan = new Penjualan();
            $modelPenjualan->tahun_bulan_id = $modelPrediksi->tahun_bulan_id;
            $modelPenjualan->label = $modelPrediksi->hasil_prediksi;

            if($modelPenjualan->save()){
                $hasSaved = true;
                foreach ($dataJumlahPenjualan as $_ => $jp) {
                    $modelJumlahPenjualan = new JenisBarangHasPenjualan();
                    $modelJumlahPenjualan->penjualan_id = $modelPenjualan->id;
                    $modelJumlahPenjualan->jumlah_penjualan = $jp->jumlah_penjualan;
                    $modelJumlahPenjualan->jenis_barang_id = $jp->jenis_barang_id;

                    if(!$modelJumlahPenjualan->save()){
                        break;
                    }                    
                }

                if($hasSaved){
                    Yii::$app->session->setFlash('success', 'Data berhasil dipindahkan');
                    $transaction->rollBack();
                }else{
                    Yii::$app->session->setFlash('error', 'Data gagal dipindahkan');
                    $transaction->rollBack();
                }
                
            }else{
                Yii::$app->session->setFlash('error', 'Data gagal dipindahkan');
                $transaction->rollBack();
            }
            
            return $this->redirect(['index']);
        } catch (\Throwable $th) {
            throw new ServerErrorHttpException('Terjadi masalah: '. $th->getMessage());
        }

    }
}
