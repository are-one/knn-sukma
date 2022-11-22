<?php

namespace app\controllers;

use app\models\JenisDonat;
use app\models\JenisDonatHasPrediksiPenjualan;
use app\models\Penjualan;
use app\models\PrediksiPenjualan;
use app\models\search\PrediksiPenjualanSearch;
use Phpml\Classification\KNearestNeighbors;
use Phpml\Math\Distance\Euclidean;
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

        $jenisDonat = JenisDonat::find()->all();

        return $this->render('index', [
            'jenisDonat' => $jenisDonat,
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
        $modelDataPrediksi = new JenisDonatHasPrediksiPenjualan();
        $model = new PrediksiPenjualan();

        if ($this->request->isPost) {

            if ($model->load($this->request->post()) && $modelDataPrediksi->load($this->request->post())) {
                $jumlah_penjualan = $this->request->post($modelDataPrediksi->formName())['jumlah_penjualan'];

                $this->prediksi($jumlah_penjualan, $modelDataPrediksi->jenis_donat_id,3);

                // $model->save();
                // return $this->redirect(['view', 'id' => $model->id]);
            }

        } else {
            $model->loadDefaultValues();
            $modelDataPrediksi->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'modelDataPrediksi' => $modelDataPrediksi,
        ]);
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
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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

    public function prediksi($data, $id_jenis_donat, $k)
    {
        try {
            $knn = new KNearestNeighbors($k, new Euclidean());
            
            $sample = [];
            
            $penjualan_donat = Penjualan::findAll(['jenis_donat_id' => $id_jenis_donat]);
            
            print_r($penjualan_donat);die;
        // $sample = [
        //     [1000,2908,4550,1278,100,50,124,50],
        //     [300,2908,4550,1278,100,50,124,50],
        //     [500,2908,5000,1278,100,50,124,50],
        //     [611,2819,2732,1198,45,15,32,12],
        //     [500,4311,3870,1450,55,30,45,20],
        //     [300,2908,4550,1278,100,124,0,0],
        //     [300,2908,4550,1278,100,45,100,37],
        //     [300,2908,4550,1278,100,45,100,37],
        //     [300,2908,4550,1278,100,45,100,37],
        //     [300,2908,4550,1278,100,50,124,50],
        //     [600,2908,4550,1278,100,50,124,50]
        // ];

        // $lable = [10060,9360,10010,7464,10281,9260,9318,9318,9318,9360,9660,918,3867,4949];
        // $lables = ['Terbanyak','Sedikit','Terbanyak','Sedikit','Terbanyak','Sedikit','Sedikit','Sedikit','Sedikit','Sedikit','Sedikit'];
        // $knn->train($sample, $lables);

        // echo $knn->predict([3002,5210,5444,3892,2389,602,1008,430]);die;
        } catch (\Throwable $th) {
            throw new ServerErrorHttpException('Terjadi masalah: '.$th->getLine().' - '. $th->getMessage());
        }

    }
}
