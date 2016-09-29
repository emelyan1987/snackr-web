<?php

    namespace app\controllers;

    use Yii;
    use yii\filters\AccessControl;
    use yii\web\Controller;
    use yii\filters\VerbFilter;
    use app\models\Customer;   
    use yii\base\Exception;
    use app\models\ActionLog;
    use yii\swiftmailer\Mailer;
    use app\models\ReferralCode;
    use app\models\InputCode;

    class CustomerController extends Controller
    {
        public $enableCsrfValidation = false;
        public function behaviors()
        {
            return [ 
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'logout' => ['post'],
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

        public function actionSignin()
        {      
            try{
                $posts = Yii::$app->request->post();
                if(!isset($posts["email"]) || !isset($posts["password"])) 
                    throw new Exception("Email or password input invalid");   

                $email = $posts["email"];
                $password = $posts["password"];

                $customer = Customer::find()->where(['email' => $email])->one();
                if(!$customer)
                    throw new Exception("Email or password is incorrect");
                if($customer->is_closed==true)
                    throw new Exception("Your account was closed. please contact with Snackr Administration Team");
                    
                if($password!='facebook' && $customer->pwd!=$password)
                    throw new Exception("Email or password is incorrect");

                if(!$customer->referral_code)  {
                    while(1) {
                        $code = $this->get_random_string("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789", 6);    
                        $cnt = Customer::find()->where(['referral_code'=>$code])->count();
                        if($cnt>0)continue;
                        else break;
                    }
                    $customer->referral_code = $code;
                    $customer->save();
                }
                
                // register session variables
                $_SESSION["isSigned"] = true;
                $_SESSION["email"] = $customer->email;

                // logging customer action
                $log = new ActionLog;
                $log->log("Logged in");

                $jsonResult = array("success"=>true, "user_info"=>array("referral_code"=>$customer->referral_code?$customer->referral_code:"","zip_code"=>$customer->zip_code?$customer->zip_code:""));
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            }
            return json_encode($jsonResult);
        }
        
        public function actionSigninwithusername()
        {      
            try{
                $posts = Yii::$app->request->post();
                if(!isset($posts["username"]) || !isset($posts["password"])) 
                    throw new Exception("Username or password input invalid");   

                $username = $posts["username"];
                $password = $posts["password"];

                $customer = Customer::find()->where(['username' => $username])->one();
                if(!$customer || $customer->pwd!=$password)
                throw new Exception("Username or password is incorrect");


                // register session variables
                $_SESSION["isSigned"] = true;
                $_SESSION["email"] = $customer->email;

                // logging customer action
                $log = new ActionLog;
                $log->log("Logged in");

                $jsonResult = array("success"=>true, "user_info"=>array("email"=>$customer->email, "referral_code"=>$customer->referral_code?$customer->referral_code:"","zip_code"=>$customer->zip_code?$customer->zip_code:""));
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            }
            return json_encode($jsonResult);
        }
        
        public function actionResetpassword()
        {      
            try{ 
                $posts = Yii::$app->request->post();
                if(!isset($posts["email"]))
                    throw new Exception("Email input invalied");


                $email = $posts["email"]; 
                $customer = Customer::findOne(['email' => $email]);
                if(!$customer) throw new Exception("You are not registered user.");
                $password = $customer->pwd;
                $password = substr(md5($password), 0, 6);
                $customer->pwd = $password;
                if(!$customer->save())
                    throw new Exception("Save failure");
                    
                if(!Yii::$app->mailer->compose()
                    ->setFrom('snackradmin@gmail.com')
                    ->setTo($email)
                    ->setSubject("Snackr Web Service Team")
                    ->setHtmlBody("<html><body>Your password is reset into <strong>$password</strong></body></html>")
                    ->send())
                throw new Exception("Main send failure");  
                $jsonResult = array("success"=>true);
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            }
            return json_encode($jsonResult);
        }

        public function actionSignup()
        {    

            try{            
                $posts = Yii::$app->request->post();

                if(!isset($posts["email"]) || !isset($posts["password"])) 
                    throw new Exception("Email or password input invalid");   


                $customer = new Customer;
                $customer->email = $posts["email"];
                $customer->pwd = $posts["password"];
                if(isset($posts["username"]))
                    $customer->username = $posts["username"];
                if(isset($posts["class"]))
                    $customer->class = $posts["class"];

                if($customer->exists())
                    throw new Exception("Email is duplicated, please try other email");
                
                while(1) {
                        $code = $this->get_random_string("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789", 6);    
                        $cnt = Customer::find()->where(['referral_code'=>$code])->count();
                        if($cnt>0)continue;
                        else break;
                    }
                    $customer->referral_code = $code;
                    
                if(!$customer->save())
                throw new Exception("Signup failure!");

                // register session variables
                $_SESSION["isSigned"] = true;
                $_SESSION["email"] = $customer->email;


                $log = new ActionLog;
                $log->log("Signned up");
                $jsonResult = array("success"=>true, "user_info"=>array("referral_code"=>$customer->referral_code?$customer->referral_code:"","zip_code"=>$customer->zip_code?$customer->zip_code:""));
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            } 

            return json_encode($jsonResult);
        }

        public function actionExist()
        {    
             $exist = false;
                       
                $params = Yii::$app->request->get();   
                
                if(isset($params["email"])) {
                    $customer = Customer::find()->where(['email'=>$params["email"]])->one();
                    if($customer)    $exist = true;  
                }
                $customer = null;
                if(isset($params["username"])) {
                    $customer = Customer::find()->where(['username'=>$params["username"]])->one();
                    if($customer)    $exist = true;  
                }
                 

            return json_encode(array("exist"=>$exist));
        }
        public function actionLogout()
        {
            //echo json_encode(array("success"=>false, "msg"=>$_SESSION["email"]));  return;
            try {

                if(isset($_SESSION["email"])) { 
                    $email = $_SESSION["email"];
                    unset($_SESSION["email"]);   
                }
                if(isset($_SESSION["isSigned"]))
                    unset($_SESSION["isSigned"]);

                // logging customer action
                if(isset($email)) {

                    $log = new ActionLog;
                    $log->email = $email;
                    $log->remote_addr = $_SERVER['REMOTE_ADDR'];

                    $log->action_type = "Logged out";
                    $log->save();    
                }

                $jsonResult = array("success"=>true);
            } catch(ErrorException $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            } 

            echo json_encode($jsonResult); 
        } 

        public function actionChangepwd() {
            try{            
                $posts = Yii::$app->request->post();

                if(!isset($posts["currentPassword"]) || !isset($posts["newPassword"])) 
                    throw new Exception("CurrentPassword or NewPassword input invalid");   
                if(!isset($_SESSION["email"]))
                    throw new Exception("Session is not configured");

                $email = $_SESSION["email"];
                $currentPassword = $posts["currentPassword"];
                $newPassword = $posts["newPassword"];

                $customer = Customer::find()->where(['email' => $email])->one();

                if($customer->pwd != $currentPassword)
                    throw new Exception("Current password is incorrect. please input correct password");
                $customer->pwd = $newPassword;

                if(!$customer->save())
                throw new Exception("Password set failure!");

                // loggin customer action
                $log = new ActionLog;
                $log->log("changed password");

                $jsonResult = array("success"=>true, "msg"=>"Signup success!");
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            } 

            return json_encode($jsonResult);
        }

        public function actionInputreferralcode() {
            try{            
                $posts = Yii::$app->request->post();

                if(!isset($posts["code"])) 
                    throw new Exception("code input invalid");  

                //$_SESSION["email"] = "matko@asdf.com";
                if(!isset($_SESSION["email"]))
                    throw new Exception("Session is not configured");

                $email = $_SESSION["email"];
                $code = $posts["code"]; 

                $customer = Customer::find()->where(['email' => $email])->one();  

                $cnt = InputCode::find()->where(['email'=>$email, 'code'=>$code])->count();
                if($cnt > 0) 
                    throw new Exception("You have already inputted this code");

                //$cnt = ReferralCode::find()->where(['email'=>$email, 'code'=>$code])->count();
                //if($cnt > 0) 
                if($code == $customer->referral_code)
                    throw new Exception("You can not input code made by yourself");

                $sender = Customer::find()->where(['referral_code'=>$code])->one();
                if(!$sender)
                    throw new Exception("Invalid code!");
                    
                $sender->point += 100;
                if(!$sender->save())
                    throw new Exception("Save failure");
                
                // Insert input record to database "tbl_input_code" table    
                $input = new InputCode;
                $input->email = $email;
                $input->code = $code;
                if(!$input->save())
                throw new Exception("Referral code save failure!");

                // loggin customer action
                $log = new ActionLog;
                $log->log("inputted referral code");
                    
                /*$referrals = ReferralCode::find()->with('customer')->where(['code'=>$code])->all();                 

                if(count($referrals) > 0) {

                    foreach($referrals as $referral) {    
                        $sender = $referral->customer;
                        if($sender->email == $email)
                            throw new Exception("You can not input code made by yourself.");
                        $sender->point += 100;
                        $sender->save();
                    }
                    // Insert input record to database "tbl_input_code" table    
                    $input = new InputCode;
                    $input->email = $email;
                    $input->code = $code;
                    if(!$input->save())
                    throw new Exception("Referral code save failure!");

                    // loggin customer action
                    $log = new ActionLog;
                    $log->log("inputted referral code");
                } else {
                    throw new Exception("Invalid code");
                }*/

                $jsonResult = array("success"=>true, "msg"=>"Signup success!");
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            } 

            return json_encode($jsonResult);
        }

        public function actionInputzipcode() {
            try{            
                $posts = Yii::$app->request->post();

                if(!isset($posts["code"])) 
                    throw new Exception("code input invalid");   
                if(!isset($_SESSION["email"]))
                    throw new Exception("Session is not configured");

                $email = $_SESSION["email"];
                $code = $posts["code"]; 

                $customer = Customer::find()->where(['email' => $email])->one();

                $customer->zip_code = $code;

                if(!$customer->save())
                throw new Exception("Zip code save failure!");

                // loggin customer action
                $log = new ActionLog;
                $log->log("inputted zip code");

                $jsonResult = array("success"=>true, "msg"=>"Signup success!");
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            } 

            return json_encode($jsonResult);
        }

        public function actionPoint() {
            try{
                //$_SESSION["email"] = "matko@asdf.com";
                if(!isset($_SESSION["email"]))
                throw new Exception("Session is not configured!");

                $email = $_SESSION["email"];

                $customer = Customer::find()->where(['email'=>$email])->one();

                $data = array("point"=>$customer->point, "reward"=>$customer->reward);

                $jsonResult = array("success"=>true, "data"=>$data);
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            }

            return json_encode($jsonResult);
        }  

        public function actionSharecode() {
            try{            
                $posts = Yii::$app->request->post();

                if(!isset($posts["code"])) 
                    throw new Exception("Input invalid");   
                if(!isset($_SESSION["email"]))
                    throw new Exception("Session is not configured");

                $email = $_SESSION["email"];
                $code = $posts["code"];    

                $referral = new ReferralCode;
                $referral->email = $email;
                $referral->code = $code;

                if(!$referral->save())
                throw new Exception("Code save failure!");

                // loggin customer action
                $log = new ActionLog;
                $log->log("shared referral code");

                $jsonResult = array("success"=>true, "msg"=>"Sharing referral code success!");
            } catch(Exception $e) {
                $jsonResult = array("success"=>false, "msg"=>$e->getMessage());
            } 

            return json_encode($jsonResult);
        }  
        
        function get_random_string($valid_chars, $length)
        {
            // start with an empty random string
            $random_string = "";

            // count the number of chars in the valid chars string so we know how many choices we have
            $num_valid_chars = strlen($valid_chars);

            // repeat the steps until we've created a string of the right length
            for ($i = 0; $i < $length; $i++)
            {
                // pick a random number from 1 up to the number of valid chars
                $random_pick = mt_rand(1, $num_valid_chars);

                // take the random character out of the string of valid chars
                // subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
                $random_char = $valid_chars[$random_pick-1];

                // add the randomly-chosen char onto the end of our string so far
                $random_string .= $random_char;
            }

            // return our finished random string
            return $random_string;
        }
    }
