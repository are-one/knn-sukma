<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use Phpml\Classification\KNearestNeighbors;
use Phpml\Math\Distance\Euclidean;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        // $knn = new KNearestNeighbors(3, new Euclidean());
        
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
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
