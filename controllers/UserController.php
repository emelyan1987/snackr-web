<?php

    namespace app\controllers;

    use Yii;
    use yii\filters\AccessControl;
    use yii\web\Controller;
    use yii\filters\VerbFilter;
    use app\models\Customer;   
    use app\models\Restaurant;   
    use app\models\Treatment;   
    use app\models\Dish;
    use yii\base\Exception;
    use app\models\ActionLog;

    class UserController extends Controller
    {
        public $enableCsrfValidation = false;
        public function behaviors()
        {
            return [ 
                'access' => [
                    'class' => AccessControl::className(),
                    'only' => ['save','delete'],
                    'rules' => [
                        [
                            'actions' => ['save','delete'],
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
                return $this->render('index');
            }
            return $this->redirect(['site/login']);
        }
        public function actionSave()
        {      
            try{
                $posts = Yii::$app->request->post();
                if(!isset($posts["title"]) || !isset($posts["address"]) || !isset($posts["location"]) || !isset($posts["zip_code"])) 
                    throw new Exception("input value invalid");   


                $id = isset($posts["id"])?$posts["id"]:null;
                if($id) {
                    $restaurant = Restaurant::findOne($id);
                } else {
                    $restaurant = new Restaurant;
                }
                $restaurant->title = $posts["title"];
                $restaurant->tel = $posts["tel"];
                $restaurant->address = $posts["address"];
                $restaurant->location = $posts["location"];
                $restaurant->zip_code = $posts["zip_code"];
                $restaurant->description = isset($posts["description"])?$posts["description"]:"";
                $restaurant->is_published = 1;

                if(!$restaurant->save())
                throw new Exception("save failure");
                // logging customer action
                //$log = new ActionLog;
                //$log->log("Logged in");

                $jsonResult = array("success"=>true);
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            }
            return json_encode($jsonResult);
        }
        public function actionAdd()
        {      
            try{
                $posts = Yii::$app->request->post();
                if(!isset($posts["title"])) 
                    throw new Exception("input value invalid");  
                //$_SESSION["email"] = "matko@asdf.com" ;
                if(!isset($_SESSION["email"]))
                    throw new Exception("Session is not configured");


                $restaurant = new Restaurant; 

                $restaurant->title = $posts["title"];
                $restaurant->tel = "";//$posts["tel"];
                $restaurant->address = "none";//$posts["address"];
                $restaurant->location = "0,0";//$posts["location"];
                $restaurant->zip_code = "";//$posts["zip_code"];
                $restaurant->description = "";//isset($posts["description"])?$posts["description"]:"";


                if(!$restaurant->save())
                throw new Exception("save failure");
                // logging customer action
                //$log = new ActionLog;
                //$log->log("Logged in");

                $jsonResult = array("success"=>true, "restaurant"=>$restaurant->attributes);
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            }
            return json_encode($jsonResult);
        }
        function actionList() {
            try{
                $gets = Yii::$app->request->get();     

                $offset = isset($gets["start"]) ? $gets["start"] : 0;
                $limit = isset($gets["limit"]) ? $gets["limit"] : 25;
                $sort   = isset($_REQUEST['sort'])   ? json_decode($_REQUEST['sort'])   : null;

                $sortProperty = $sort[0]->property;   
                if($sort[0]->direction=="ASC")
                    $sortDirection = SORT_ASC;
                else
                    $sortDirection = SORT_DESC; 


                if($sortProperty == 'posts') {
                    $customers = Customer::find()
                        ->leftJoin('tbl_dish', "tbl_dish.email = tbl_customer.email")
                        ->where('tbl_customer.is_closed IS NULL OR tbl_customer.is_closed=0')
                        ->groupBy('tbl_customer.email')
                        ->orderBy(['COUNT(tbl_dish.id)' => $sortDirection])                    
                        ->offset($offset)
                        ->limit($limit)
                        ->all(); 
                        
                    $data = array(); 
                    
                    foreach($customers as $customer) {
                        //echo $restaurant->title;
                        $posts = Dish::find()->where(['email'=>$customer->email])->count();
                        $likes = Treatment::find()->where(['email'=>$customer->email,'action'=>'L'])->count();
                        $dislikes = Treatment::find()->where(['email'=>$customer->email,'action'=>'D'])->count();
                        $discards = Treatment::find()->where(['email'=>$customer->email,'action'=>'N'])->count();
                        $data[] = array_merge($customer->attributes, array("posts"=>$posts, "likes"=>$likes, "dislikes"=>$dislikes, "discards"=>$discards));
                    } 
                }
                else if($sortProperty == 'likes') {
                    $customers = Customer::find()
                        ->leftJoin('tbl_treatment', "tbl_treatment.email = tbl_customer.email AND tbl_treatment.action='L'")
                        ->where('tbl_customer.is_closed IS NULL OR tbl_customer.is_closed=0')
                        ->groupBy('tbl_customer.email')
                        ->orderBy(['COUNT(tbl_treatment.id)' => $sortDirection])                    
                        ->offset($offset)
                        ->limit($limit)
                        ->all(); 
                        
                    $data = array(); 
                    
                    foreach($customers as $customer) {
                        //echo $restaurant->title;
                        $posts = Dish::find()->where(['email'=>$customer->email])->count();
                        $likes = Treatment::find()->where(['email'=>$customer->email,'action'=>'L'])->count();
                        $dislikes = Treatment::find()->where(['email'=>$customer->email,'action'=>'D'])->count();
                        $discards = Treatment::find()->where(['email'=>$customer->email,'action'=>'N'])->count();
                        $data[] = array_merge($customer->attributes, array("posts"=>$posts, "likes"=>$likes, "dislikes"=>$dislikes, "discards"=>$discards));
                    } 
                } else if($sortProperty == 'dislikes') {
                    $customers = Customer::find()
                        ->leftJoin('tbl_treatment', "tbl_treatment.email = tbl_customer.email AND tbl_treatment.action='D'")
                        ->where('tbl_customer.is_closed IS NULL OR tbl_customer.is_closed=0')
                        ->groupBy('tbl_customer.email')
                        ->orderBy(['COUNT(tbl_treatment.id)' => $sortDirection])                    
                        ->offset($offset)
                        ->limit($limit)
                        ->all(); 
                        
                    $data = array(); 
                    
                    foreach($customers as $customer) {
                        //echo $restaurant->title;
                        $posts = Dish::find()->where(['email'=>$customer->email])->count();
                        $likes = Treatment::find()->where(['email'=>$customer->email,'action'=>'L'])->count();
                        $dislikes = Treatment::find()->where(['email'=>$customer->email,'action'=>'D'])->count();
                        $discards = Treatment::find()->where(['email'=>$customer->email,'action'=>'N'])->count();
                        $data[] = array_merge($customer->attributes, array("likes"=>$likes, "dislikes"=>$dislikes, "discards"=>$discards));
                    } 
                } else if($sortProperty == 'discards') {
                    $customers = Customer::find()
                        ->leftJoin('tbl_treatment', "tbl_treatment.email = tbl_customer.email AND tbl_treatment.action='N'")
                        ->where('tbl_customer.is_closed IS NULL OR tbl_customer.is_closed=0')
                        ->groupBy('tbl_customer.email')
                        ->orderBy(['COUNT(tbl_treatment.id)' => $sortDirection])                    
                        ->offset($offset)
                        ->limit($limit)
                        ->all(); 
                        
                    $data = array(); 
                    
                    foreach($customers as $customer) {
                        //echo $restaurant->title;
                        $posts = Dish::find()->where(['email'=>$customer->email])->count();
                        $likes = Treatment::find()->where(['email'=>$customer->email,'action'=>'L'])->count();
                        $dislikes = Treatment::find()->where(['email'=>$customer->email,'action'=>'D'])->count();
                        $discards = Treatment::find()->where(['email'=>$customer->email,'action'=>'N'])->count();
                        $data[] = array_merge($customer->attributes, array("posts"=>$posts, "likes"=>$likes, "dislikes"=>$dislikes, "discards"=>$discards));
                    } 
                } else {
                    $customers = Customer::find()
                    ->where('tbl_customer.is_closed IS NULL OR tbl_customer.is_closed=0')
                    ->orderBy([$sortProperty => $sortDirection])
                    ->offset($offset)
                    ->limit($limit)
                    ->all(); 
                    $data = array();

                    foreach($customers as $customer) {
                        //echo $restaurant->title;
                        $posts = Dish::find()->where(['email'=>$customer->email])->count();
                        $likes = Treatment::find()->where(['email'=>$customer->email,'action'=>'L'])->count();
                        $dislikes = Treatment::find()->where(['email'=>$customer->email,'action'=>'D'])->count();
                        $discards = Treatment::find()->where(['email'=>$customer->email,'action'=>'N'])->count();
                        $data[] = array_merge($customer->attributes, array("posts"=>$posts, "likes"=>$likes, "dislikes"=>$dislikes, "discards"=>$discards));
                    }
                }

                $total =  Customer::find()->where('tbl_customer.is_closed IS NULL OR tbl_customer.is_closed=0')->count();


                $jsonResult = array("success"=>true, "data"=>$data, "total"=>$total);
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            }

            echo json_encode($jsonResult);
        }
        function actionDelete(){
            try{
                $posts = Yii::$app->request->post();
                if(!isset($posts["ids"])) 
                    throw new Exception("input value invalid");   


                $ids = $posts["ids"]; 
                $users = Customer::find()->where("id IN ($ids)")->all();

                foreach($users as $user) {
                    $user->is_closed = true;
                    if(!$user->save())
                        throw new Exception("save failure");                     
                } 

                $jsonResult = array("success"=>true);
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            }
            return json_encode($jsonResult);
        }
        function actionLocation() {
            try{                 
                $posts = Yii::$app->request->post();
                if(!isset($posts["address"]))
                throw new Exception("Input value invalid"); 

                $address = $posts["address"];
                $result = $this->getCoordinates($address);

            } catch(Exception $e) {
                $result = $e->getMessage();
            }
            echo $result;
        }
        function getCoordinates($address){

            $address = str_replace(" ", "+", $address); // replace all the white space with "+" sign to match with google search pattern

            $url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=$address";

            $response = file_get_contents($url);


            $json = json_decode($response,TRUE); //generate array object from the response from the web

            return ($json['results'][0]['geometry']['location']['lat'].",".$json['results'][0]['geometry']['location']['lng']);

        }
    }
