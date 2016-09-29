<?php

    namespace app\controllers;

    use Yii;
    use yii\filters\AccessControl;
    use yii\web\Controller;
    use yii\filters\VerbFilter;
    use app\models\LoginForm;
    use app\models\ContactForm;

    class SiteController extends Controller
    {
        public $enableCsrfValidation = false;
        public function behaviors()
        {
            return [
                'access' => [
                    'class' => AccessControl::className(),
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
                    'class' => VerbFilter::className(),
                    'actions' => [
                        //'logout' => ['post'],
                    ],
                ],
            ];
        }

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

        public function actionIndex()
        {   
            if (!\Yii::$app->user->isGuest) {
                //return $this->render('index');
                return $this->redirect(['rest/index']);
            }
            return $this->redirect(['login']);
        }

        public function actionLogin()
        {
            if (!\Yii::$app->user->isGuest) {
                return $this->render('index');
            }

            $model = new LoginForm();
            $posts = Yii::$app->request->post();

            if (!empty($posts)) { 
                $model->attributes = $posts;
                if($model->login()) { 
                    $jsonResult = array("success"=>true);
                    echo json_encode($jsonResult);
                    return;
                } else {                          
                    $jsonResult = array("success"=>false, "msg"=>serialize(Yii::$app->request->post()));
                    echo json_encode($jsonResult);
                    return;
                }   
            }
            return $this->renderPartial('login', [
                'model' => $model,
            ]);
        }

        public function actionSignup()
        {           
            return $this->render('signup');
        }

        public function actionLogout()
        {
            Yii::$app->user->logout();

            return $this->goHome();
        }

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

        public function actionAbout()
        {
            return $this->render('about');
        }
    }
