<?php

    namespace app\controllers;

    use Yii;
    use yii\filters\AccessControl;
    use yii\web\Controller;
    use yii\filters\VerbFilter;
    use app\models\Restaurant;   
    use yii\base\Exception;
    use app\models\ActionLog;

    class RestController extends Controller
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
                if(!isset($posts["title"]) || !isset($posts["location"])) 
                    throw new Exception("input value invalid");  
                    //$_SESSION["email"] = "matko@asdf.com" ;
                if(!isset($_SESSION["email"]))
                    throw new Exception("Session is not configured");

                
                    $restaurant = new Restaurant; 
                    
                $restaurant->title = $posts["title"];
                $restaurant->tel = "";//$posts["tel"];
                $restaurant->address = "none";//$posts["address"];
                $restaurant->location = $posts["location"];
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
                $params = Yii::$app->request->get();
                
                $offset = isset($params["start"]) ? $params["start"] : 0;
                $limit = isset($params["limit"]) ? $params["limit"] : 25;
                $sort   = isset($_REQUEST['sort'])   ? json_decode($_REQUEST['sort'])   : null;

                $sortProperty = $sort[0]->property;   
                if($sort[0]->direction=="ASC")
                    $sortDirection = SORT_ASC;
                else
                    $sortDirection = SORT_DESC; 

                /*$sortDirection = SORT_DESC;
                $sortProperty = 'title';*/
                $where = array();
                
                $query = Restaurant::find();
                
                $where = "1=1"; 
                if(isset($params["is_published"])) $where .= " AND is_published='".$params["is_published"]."'";
                if(isset($params["keyword"])) $where .= " AND (title like '%".$params["keyword"]."%' OR address like '%".$params["keyword"]."%')";
                if(isset($params["zip_code"])) $where .= " AND zip_code = '".$params["zip_code"]."'";
                
                $query = $query->where($where);//$where = array("is_published"=>$params["is_published"]);
                $query->orderBy([$sortProperty => $sortDirection]);                    
                $query->offset($offset);
                $query->limit($limit);
                $restaurants = $query->all();
                
                $total =  Restaurant::find()->count();   
                
                $data = array();
                
                foreach($restaurants as $restaurant) {
                    //echo $restaurant->title;
                    if(isset($params["location"])) { 
                        if($this->distance($params["location"], $restaurant->location) <= 25)
                            $data[] = $restaurant->attributes;
                    } else {
                        $data[] = $restaurant->attributes;
                    }
                }
                $jsonResult = array("success"=>true, "data"=>$data, "total"=>$total);
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            }
            
            echo json_encode($jsonResult);
        }
        function actionDelete(){
            try{
                $posts = Yii::$app->request->post();
                if(!isset($posts["id"])) 
                    throw new Exception("input value invalid");   

                
                $id = $posts["id"]; 
                $restaurant = Restaurant::findOne($id);
                
                
                if(!$restaurant->delete())
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

        function distance($coordinate1, $coordinate2) {
            list($lat1, $lon1) = explode(',', $coordinate1);
            list($lat2, $lon2) = explode(',', $coordinate2);


            $R = 6371 * 0.62137;
            $f1 = deg2rad($lat1);
            $f2 = deg2rad($lat2);
            $df = deg2rad($lat2 - $lat1);
            $dg = deg2rad($lon2 - $lon1);

            $a = sin($df/2) * sin($df/2) + cos($f1) * cos($f2) * sin($dg/2) * sin($dg/2);
            $c = 2 * atan2(sqrt($a), sqrt(1-$a));

            $d = $R * $c;  
            return round($d, 2);
        }
    }
