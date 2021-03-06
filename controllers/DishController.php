<?php

    namespace app\controllers;

    use Yii;
    use yii\filters\AccessControl;
    use yii\web\Controller;
    use yii\filters\VerbFilter;
    use app\models\Restaurant; 
    use app\models\Dish; 
    use app\models\Customer;  
    use app\models\Treatment;   
    use yii\base\Exception;
    use app\models\ActionLog;
    use yii\db\Query;

    class DishController extends Controller
    {
        public $enableCsrfValidation = false;
        public function behaviors()
        {
            return [ 
                'access' => [
                    'class' => AccessControl::className(),
                    'only' => ['list'],
                    'rules' => [
                        [
                            'actions' => ['list'],
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
                if(!isset($posts["title"])) 
                    throw new Exception("input value invalid");  

                if(!isset($_SESSION["email"]))
                    throw new Exception("Session is not configured");

                if(isset($posts["place_id"])) { 
                    $place_id = $posts["place_id"];
                    $restaurant = Restaurant::findOne(['place_id'=>$place_id]);
                    if(!$restaurant) { // if restaurant doesn't exist get detail info of restaurant and then add restaurant to snackr database
                        $url = "https://maps.googleapis.com/maps/api/place/details/json?placeid=$place_id&key=AIzaSyCwnjJk0eQCSik-GA7y042Rd9FtIoWAHzo"; 

                        $result = file_get_contents($url);  

                        $result = json_decode($result);
                        $result = $result->result;                           
                        $restaurant = new Restaurant;
                        $restaurant->address = $result->formatted_address; 
                        $restaurant->tel = isset($result->formatted_phone_number) ? $result->formatted_phone_number : "";
                        $restaurant->title = $result->name;
                        $restaurant->place_id = $result->place_id;
                        $restaurant->location = $result->geometry->location->lat.",".$result->geometry->location->lng ;
                        $restaurant->is_published = 1;                                           
                        foreach($result->address_components as $comp) {
                            if(in_array("postal_code", $comp->types)) {
                                $restaurant->zip_code = $comp->long_name;
                                break;
                            }
                        }
                        if(!$restaurant->save())
                            throw new Exception("Restaurant Save Failure");
                    }
                } else if(isset($posts["restaurant_id"])) {
                    $restaurant = Restaurant::findOne(['id'=>$posts["restaurant_id"]]);
                } else {
                    throw new Exception("Input invalid");
                }

                $id = isset($posts["id"])?$posts["id"]:null;
                $price = isset($posts["price"])?$posts["price"]:null;

                if($id) {
                    $dish = Dish::findOne($id);
                } else {
                    $dish = new Dish;
                }

                if($_FILES['photo']['name'])
                {

                    if(!$_FILES['photo']['error'])
                    {
                        $new_file_name = strtolower($_FILES['photo']['tmp_name']); //rename file
                        if($_FILES['photo']['size'] > (1024000)) //can't be larger than 1 MB
                        {
                            $valid_file = false;
                            throw new Exception('Oops!  Your file\'s size is to large.');
                        }


                        $timestamp = time();
                        $new_file_name = "{$timestamp}.jpg";

                        if(!move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/'.$new_file_name))
                            throw new Exception("Upload error occured!");

                        $dish->photo = $new_file_name;     

                    }                    
                    else
                    {                        
                        throw new Exception('Ooops!  Your upload triggered the following error:  '.$_FILES['photo']['error']);
                    }
                }

                $dish->title = $posts["title"];
                $dish->restaurant_id = $restaurant->id;
                $dish->email = $_SESSION["email"];
                $dish->price = $price;


                if(!$dish->save())
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
        public function actionLike()
        {      
            try{
                $posts = Yii::$app->request->post();
                if(!isset($posts["dish_id"])) 
                    throw new Exception("input value invalid");  


                if(!isset($_SESSION["email"]))
                    throw new Exception("Session is not configured");

                $dish_id = $posts["dish_id"];
                $email = $_SESSION["email"];

                $dish = Dish::findOne($dish_id);
                if($dish->email != $email) {
                    $treatment = Treatment::findOne(['dish_id'=>$dish_id,'email'=>$email]);
                    if(!$treatment){
                        $treatment = new Treatment;
                        $treatment->dish_id = $dish_id;
                        $treatment->email = $email;
                    }                                                  
                    if($treatment->isNewRecord) {

                        $customer = Customer::findOne(['email'=>$dish->email]);
                        if(!$customer->point)
                            $customer->point = 1;
                        else
                            $customer->point++;
                        $customer->save();
                    }    
                    $treatment->action = 'L';
                    if(!$treatment->save())
                    throw new Exception("save failure");


                }

                $jsonResult = array("success"=>true);
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            }
            return json_encode($jsonResult);
        }
        
        
        public function actionDislike()
        {      
            try{
                $posts = Yii::$app->request->post();
                if(!isset($posts["dish_id"])) 
                    throw new Exception("input value invalid");  

                if(!isset($_SESSION["email"]))
                    throw new Exception("Session is not configured");

                $dish_id = $posts["dish_id"];
                $email = $_SESSION["email"];
                $treatment = Treatment::findOne(['dish_id'=>$dish_id,'email'=>$email]);
                if(!$treatment){
                    $treatment = new Treatment;
                    $treatment->dish_id = $dish_id;
                    $treatment->email = $email;
                }                                                  

                $treatment->action = 'D';
                if(!$treatment->save())
                throw new Exception("save failure");

                $jsonResult = array("success"=>true);
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            }
            return json_encode($jsonResult);
        }
        public function actionNeversee()
        {      
            try{
                $posts = Yii::$app->request->post();
                if(!isset($posts["dish_id"])) 
                    throw new Exception("input value invalid");  

                if(!isset($_SESSION["email"]))
                    throw new Exception("Session is not configured");

                $dish_id = $posts["dish_id"];
                $email = $_SESSION["email"];
                $treatment = Treatment::findOne(['dish_id'=>$dish_id,'email'=>$email]);
                if(!$treatment){
                    $treatment = new Treatment;
                    $treatment->dish_id = $dish_id;
                    $treatment->email = $email;
                }                                                  

                $treatment->action = 'N';
                if(!$treatment->save())
                throw new Exception("save failure");

                $jsonResult = array("success"=>true);
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            }
            return json_encode($jsonResult);
        }
        
        public function actionFlag()
        {      
            try{
                $posts = Yii::$app->request->post();
                if(!isset($posts["dish_id"])) 
                    throw new Exception("input value invalid");  


                if(!isset($_SESSION["email"]))
                    throw new Exception("Session is not configured");

                $dish_id = $posts["dish_id"];
                $email = $_SESSION["email"];

                $dish = Dish::findOne($dish_id); 
                if($dish) {  
                    $dish->flag = 1;
                    if(!$dish->save())
                        throw new Exception("Database failure");
                }

                $jsonResult = array("success"=>true);
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            }
            return json_encode($jsonResult);
        }
        
        function actionBlock(){
            try{
                $posts = Yii::$app->request->post();
                if(!isset($posts["ids"])) 
                    throw new Exception("input value invalid");   


                $ids = $posts["ids"];
                $dishes = Dish::find()->where("id IN ($ids)")->all();
                
                foreach($dishes as $dish) {  
                    $dish->is_blocked = 1;                  
                    if(!$dish->save())
                        throw new Exception("delete failure");
                }  

                $jsonResult = array("success"=>true);
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            }
            return json_encode($jsonResult);
        }
        
        
        function actionFeed1() {
            try{
                //$_SESSION["email"] = "emelyan@gmail.com";
                if(!isset($_SESSION["email"])) 
                    throw new Exception("Session is not configured");
                $email = $_SESSION["email"]; 

                $gets = Yii::$app->request->get();
                
                
                $timestamp = time();
                if(!isset($_SESSION["feed_page"]) || (isset($_SESSION["timestamp"]) && ($timestamp - $_SESSION["timestamp"])>48*3600)){
                    $_SESSION["feed_page"] = 1;
                    $_SESSION["timestamp"] = $timestamp;
                    $offset = 0;
                } else { 
                    $feed_page = $_SESSION["feed_page"];
                    $feed_page ++;
                    $offset = ($feed_page-1)*25;
                    $_SESSION["feed_page"] = $feed_page;
                }  

                $data = array();
                $msg = "";

                if(isset($gets["zip_code"])) {  // In the case search on based ZipCode
                    // find the food of restaurant with equal zip code inputted ,sorting by created time  
                    // and then limit 25 
                    $query = new Query;
                    $zip_code = $gets["zip_code"];    

                    $query
                    ->select([
                        'dish_id'=>'d.id', 
                        'dish_title'=>'d.title', 
                        'price'=>'d.price', 
                        'photo'=>'d.photo', 
                        'restaurant_id'=>'r.id', 
                        'restaurant_title'=>'r.title', 
                        'tel'=>'r.tel', 
                        'address'=>'r.address', 
                        'location'=>'r.location', 
                        'zip_code'=>'r.zip_code'
                    ])
                    ->from(['d'=>'tbl_dish'])
                    ->leftJoin(['r'=>'tbl_restaurant'], 'r.id=d.restaurant_id')
                    ->leftJoin(['t'=>'tbl_treatment'], 't.dish_id=d.id AND t.email=:email', ['email'=>$email])
                    ->where("(r.zip_code='$zip_code')  AND (t.id IS NULL OR t.action<>'N')")
                    ->orderBy(['d.created_time'=>SORT_DESC])
                    ->offset($offset)
                    ->limit(25); 

                    $rows = $query->all(); 

                    foreach($rows as $row) {
                        $data[] = array_merge($row, array("distance"=>"within your zip code"));
                    }
                } else if(isset($gets["location"])) {   

                    $location = $gets["location"];    
                    $date = date("Y-m-d");

                    $query = new Query; 

                    $query->select([
                        'dish_id'=>'d.id', 
                        'dish_title'=>'d.title', 
                        'price'=>'d.price', 
                        'photo'=>'d.photo', 
                        'restaurant_id'=>'r.id', 
                        'restaurant_title'=>'r.title', 
                        'tel'=>'r.tel', 
                        'address'=>'r.address', 
                        'location'=>'r.location', 
                        'zip_code'=>'r.zip_code',
                        'distance'=>"CONCAT(Distance('$location', r.location), ' mi away')"
                    ])
                    ->from(['d'=>'tbl_dish'])
                    ->leftJoin(['r'=>'tbl_restaurant'], 'r.id=d.restaurant_id')
                    ->leftJoin(['t'=>'tbl_treatment'], 't.dish_id=d.id AND t.email=:email', ['email'=>$email])
                    ->where("(r.location IS NOT NULL AND r.location<>'') AND (t.id IS NULL OR t.action<>'N')")
                    ->orderBy(['distance'=>SORT_ASC/*, 'd.created_time'=>SORT_DESC*/])
                    ->offset($offset)
                    ->limit(25);  

                    $rows = $query->all();   
                    $msg = "We’re Sorry! No one has added any photos near you yet. We will now show you delicious photos that are farther away from your location. Please don’t let this happen to someone else, add some pictures of your own the next time you’re out!";


                    foreach($rows as $row) {
                        $data[] = $row;
                    }  
                }

                if(count($data)==0)
                {
                    $_SESSION["feed_page"] = null;
                    
                    $this->redirect(['feed1','location'=>'50.452663,30.477559'])  ;
                }
                $jsonResult = array("success"=>true, "data"=>$data/*, "msg"=>$msg*/);
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            }

            echo json_encode($jsonResult);
        } 

        function actionFeed() {
            try{
                //$_SESSION["email"] = "emelyan@gmail.com";
                if(!isset($_SESSION["email"])) 
                    throw new Exception("Session is not configured");
                $email = $_SESSION["email"]; 

                $gets = Yii::$app->request->get();
                if(isset($gets["start"]))
                    $offset = $gets["start"];
                else
                    $offset = 0;    
 

                $data = array();
                $msg = "";

                if(isset($gets["zip_code"])) {  // In the case search on based ZipCode
                    // find the food of restaurant with equal zip code inputted ,sorting by created time  
                    // and then limit 25 
                    $query = new Query;
                    $zip_code = $gets["zip_code"];    

                    $query
                    ->select([
                        'dish_id'=>'d.id', 
                        'dish_title'=>'d.title', 
                        'price'=>'d.price', 
                        'photo'=>'d.photo', 
                        'restaurant_id'=>'r.id', 
                        'restaurant_title'=>'r.title', 
                        'tel'=>'r.tel', 
                        'address'=>'r.address', 
                        'location'=>'r.location', 
                        'zip_code'=>'r.zip_code'
                    ])
                    ->from(['d'=>'tbl_dish'])
                    ->leftJoin(['r'=>'tbl_restaurant'], 'r.id=d.restaurant_id')
                    ->leftJoin(['t'=>'tbl_treatment'], 't.dish_id=d.id AND t.email=:email', ['email'=>$email])
                    ->where("(r.zip_code='$zip_code')  AND (t.id IS NULL OR t.action<>'N')")
                    ->orderBy(['d.created_time'=>SORT_DESC])
                    //->offset($offset)
                    ->limit(25); 

                    $rows = $query->all(); 

                    foreach($rows as $row) {
                        $data[] = array_merge($row, array("distance"=>"within your zip code"));
                    }
                } else if(isset($gets["location"])) {   

                    $location = $gets["location"];    
                    $date = date("Y-m-d");

                    $query = new Query; 

                    // Create pool A
                    $query
                    ->select([
                        'dish_id'=>'d.id', 
                        'dish_title'=>'d.title', 
                        'price'=>'d.price', 
                        'photo'=>'d.photo', 
                        'restaurant_id'=>'r.id', 
                        'restaurant_title'=>'r.title', 
                        'tel'=>'r.tel', 
                        'address'=>'r.address', 
                        'location'=>'r.location', 
                        'zip_code'=>'r.zip_code',
                        'distance'=>"CONCAT(Distance('$location', r.location), ' mi away')"
                    ])
                    ->from(['d'=>'tbl_dish'])
                    ->leftJoin(['r'=>'tbl_restaurant'], 'r.id=d.restaurant_id')
                    ->leftJoin(['t'=>'tbl_treatment'], 't.dish_id=d.id AND t.email=:email', ['email'=>$email])
                    ->where("(r.location IS NOT NULL AND r.location<>'')")
                    ->andWhere("(t.id IS NULL OR t.action<>'N')")
                    ->andWhere("Distance('$location',r.location)<50")   // Look for photos within 50 miles
                    ->andWhere("ViewCount(d.id)>=500")                  // Eliminate any photos with less than 500 views
                    ->andWhere("DATEDIFF('$date', d.created_time)<15")  // Find most recent photos up to 15 days   
                    ->orderBy(['distance'=>SORT_ASC, 'd.created_time'=>SORT_DESC])
                    //->offset($offset)
                    ->limit(25);  

                    $rows = $query->all(); 

                    foreach($rows as $row) {
                        $data[] = $row;
                    }

                    // Create pool B
                    $query = new Query;    

                    $query
                    ->select([
                        'dish_id'=>'d.id', 
                        'dish_title'=>'d.title', 
                        'price'=>'d.price', 
                        'photo'=>'d.photo', 
                        'restaurant_id'=>'r.id', 
                        'restaurant_title'=>'r.title', 
                        'tel'=>'r.tel', 
                        'address'=>'r.address', 
                        'location'=>'r.location', 
                        'zip_code'=>'r.zip_code',
                        'distance'=>"CONCAT(Distance('$location', r.location), ' mi away')"
                    ])
                    ->from(['d'=>'tbl_dish'])
                    ->leftJoin(['r'=>'tbl_restaurant'], 'r.id=d.restaurant_id')
                    ->leftJoin(['t'=>'tbl_treatment'], 't.dish_id=d.id AND t.email=:email', ['email'=>$email])
                    ->where("(r.location IS NOT NULL AND r.location<>'')")
                    ->andWhere("(t.id IS NULL OR t.action<>'N')")
                    ->andWhere("Distance('$location',r.location)<50")   // Look for photos within 50 miles
                    ->andWhere("ViewCount(d.id)<=300")                  // Elemintate all photos with more than 300 views
                    ->andWhere("DATEDIFF('$date', d.created_time)<10")  // Prioritize by recency up to 10 days
                    ->orderBy(['distance'=>SORT_ASC, 'd.created_time'=>SORT_DESC])
                    //->offset($offset)
                    ->limit(15);      

                    $rows = $query->all(); 

                    // Pick 8 random photos from pool B
                    $cnt = count($rows);
                    if($cnt>0) {                    
                        $cnt = $cnt > 8 ? 8 : $cnt;
                        $rand_keys = array_rand($rows, $cnt);

                        for($i=0; $i<count($rand_keys); $i++){
                            $data[] = $rows[$rand_keys[$i]];
                        }    
                    }
                    /*foreach($rows as $row) {
                    $data[] = $row;
                    }*/

                    // If all else fails SHOW pictures based on proximity regardless of activity or recency
                    if(count($data) == 0) {                                                     
                        $query = new Query; 

                        $query
                        ->select([
                            'dish_id'=>'d.id', 
                            'dish_title'=>'d.title', 
                            'price'=>'d.price', 
                            'photo'=>'d.photo', 
                            'restaurant_id'=>'r.id', 
                            'restaurant_title'=>'r.title', 
                            'tel'=>'r.tel', 
                            'address'=>'r.address', 
                            'location'=>'r.location', 
                            'zip_code'=>'r.zip_code',
                            'distance'=>"CONCAT(Distance('$location', r.location), ' mi away')"
                        ])
                        ->from(['d'=>'tbl_dish'])
                        ->leftJoin(['r'=>'tbl_restaurant'], 'r.id=d.restaurant_id')
                        ->leftJoin(['t'=>'tbl_treatment'], 't.dish_id=d.id AND t.email=:email', ['email'=>$email])
                        ->where("(r.location IS NOT NULL AND r.location<>'') AND (t.id IS NULL OR t.action<>'N')")
                        ->orderBy(['distance'=>SORT_ASC/*, 'd.created_time'=>SORT_DESC*/])
                        ->offset($offset)
                        ->limit(25);  

                        $rows = $query->all();
                        $msg = "We’re Sorry! No one has added any photos near you yet. We will now show you delicious photos that are farther away from your location. Please don’t let this happen to someone else, add some pictures of your own the next time you’re out!";


                        foreach($rows as $row) {
                            $data[] = $row;
                        }
                    }
                }

                $jsonResult = array("success"=>true, "data"=>$data, "msg"=>$msg);
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            }

            echo json_encode($jsonResult);
        } 
        function actionList() {

            try{
                $gets = Yii::$app->request->get();

                $rest_id = isset($gets["rest_id"]) ? $gets["rest_id"] : null;

                $offset = isset($gets["start"]) ? $gets["start"] : 0;
                $limit = isset($gets["limit"]) ? $gets["limit"] : 25;

                $data = array();

                if($rest_id){
                    $query = Dish::find()
                        ->where(['restaurant_id'=>$rest_id]);
                    if(isset($gets["flag"]))
                        $query->andWhere(['flag'=>$gets["flag"]]);
                    
                    $total =  $query->count();    
                    $query->orderBy(['created_time' => SORT_DESC])
                        ->offset($offset)
                        ->limit($limit);
                    $dishes = $query->all(); 
                } else {   
                    $query =  Dish::find();
                    
                    if(isset($gets["flag"])){
                        $query->where(['flag'=>1]);
                    }
                        
                        
                    $total =  $query->count();
                    $query->orderBy(['created_time' => SORT_DESC])
                        ->offset($offset)
                        ->limit($limit);
                    $dishes =  $query->all(); 
                }
                
                foreach($dishes as $dish) {  
                    if($dish->restaurant)
                        $data[] = array_merge($dish->attributes, array("location"=>$dish->restaurant->title, "address"=>$dish->restaurant->address));                       

                }
                $jsonResult = array("success"=>true, "data"=>$data, "total"=>$total);
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            }

            return json_encode($jsonResult);
        }

        function actionSubmittedlist() {

            try{ 
                $gets = Yii::$app->request->get();  

                $offset = isset($gets["start"]) ? $gets["start"] : 0;
                $limit = isset($gets["count"]) ? $gets["count"] : 25;

                //$_SESSION["email"] = "matko@asdf.com";
                if(!isset($_SESSION["email"]))
                    throw new Exception("Session is not configured");

                $email = $_SESSION["email"];  
                $data = array();
                $dishes = Dish::find()->where(['email'=>$email])->orderBy(['created_time' => SORT_DESC])->offset($offset)->limit($limit)->all();
                foreach($dishes as $dish) {
                    //$views = Dish::find()->joinWith('treatments')->where('tbl_dish.id = :dish_id AND tbl_treatment.action!=:action', ['dish_id'=>$dish->id,'action'=>'N'])->count();
                    $views = Dish::find()->joinWith('treatments')->where('tbl_dish.id = :dish_id', ['dish_id'=>$dish->id])->count();
                    $likes = Dish::find()->joinWith('treatments')->where('tbl_dish.id = :dish_id AND tbl_treatment.action=:action', ['dish_id'=>$dish->id,'action'=>'L'])->count();
                    $data[] = array_merge($dish->attributes, array("restaurant"=>$dish->restaurant->attributes, "views"=>$views, "likes"=>$likes));                       

                }
                $jsonResult = array("success"=>true, "data"=>$data);
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            }

            echo json_encode($jsonResult);
        }

        function actionLikedlist() {

            try{
                $gets = Yii::$app->request->get();  

                $offset = isset($gets["start"]) ? $gets["start"] : 0;
                $limit = isset($gets["count"]) ? $gets["count"] : 25;

                //$_SESSION["email"] = "matko@asdf.com";
                if(!isset($_SESSION["email"]))
                    throw new Exception("Session is not configured");

                $email = $_SESSION["email"];  
                $data = array();
                $dishes = Dish::find()
                ->select(['tbl_dish.*', 'tbl_treatment.created_time'])
                ->leftJoin('tbl_treatment', "tbl_treatment.dish_id = tbl_dish.id")
                ->where(['tbl_treatment.email'=>$email, 'tbl_treatment.action'=>'L'])
                ->orderBy(['tbl_treatment.created_time' => SORT_DESC])
                ->offset($offset)
                ->limit($limit)
                ->all();


                foreach($dishes as $dish) {
                    $data[] = array_merge($dish->attributes, array("restaurant"=>$dish->restaurant->attributes));                       
                }
                $jsonResult = array("success"=>true, "data"=>$data);
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
                $dishes = Dish::find()->where("id IN ($ids)")->all();
                
                foreach($dishes as $dish) {                    
                    if(!$dish->delete())
                        throw new Exception("delete failure");
                }
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

        function actionCalc() {
            return $this->distance('42.921076,129.528810', '42.921365,129.526616');
        }

    }
