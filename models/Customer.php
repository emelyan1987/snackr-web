<?php
    
    namespace app\models;

    use Yii;
    use yii\db\Expression;
    use app\models\ActionLog;
    
    /**
    * This is the model class for table "tbl_customer".
    *
    * @property string $id
    * @property string $email
    * @property string $username
    * @property string $pwd
    * @property string $class
    * @property string $referral_code
    * @property string $zip_code
    * @property string $created_time
    * @property string $modified_time
    */
    class Customer extends \yii\db\ActiveRecord
    {
        /**
        * @inheritdoc
        */
        public static function tableName()
        {
            return 'tbl_customer';
        }

        /**
        * @inheritdoc
        */
        public function rules()
        {
            return [
                [['email', 'pwd'], 'required'],
                [['email', 'username', 'pwd', 'referral_code', 'zip_code'], 'string', 'max' => 255],
                [['class'], 'string', 'max' => 2],
                ['class','default','value'=>'N'],
                [['point', 'reward', 'is_closed'], 'integer'],
                ['modified_time','default','value'=>new Expression('NOW()'),'on'=>'update'],
                [['created_time','modified_time'],'default','value'=>new Expression('NOW()'),'on'=>'insert']
            ];
        }

        /**
        * @inheritdoc
        */
        public function attributeLabels()
        {
            return [
                'id' => 'ID',
                'email' => 'Email',
                'username' => 'Username',
                'pwd' => 'Pwd',
                'class' => 'Class',
                'referral_code' => 'Referral Code',
                'zip_code' => 'Zip Code',
                'point' => 'Point',
                'is_closed' => 'Deleted Customer',
                'created_time' => 'Created Time',
                'modified_time' => 'Modified Time',
            ];
        }

        public function beforeSave($insert) {   
            if ($this->isNewRecord) {
                $this->created_time = new Expression('NOW()');
                $this->modified_time = new Expression('NOW()');
            }                                                                  
            else
                $this->modified_time = new Expression('NOW()');

            return parent::beforeSave($insert);
        }
        
        public function afterSave ( $insert, $changedAttributes ){
                 
            /*$log = new ActionLog;
            $log->email = $this->email;
            $log->remote_addr = $_SERVER['REMOTE_ADDR'];
        
            $log->action_type = $insert ? "created" : "modified";
            $log->save();*/
               
            parent::afterSave($insert, $changedAttributes);
        }
        
        public function exists() {
            $count = Customer::find()->where(['email' => $this->email])->count();
            if($count>0) return true;
            
            if(isset($this->username)) {                                                            
                $count = Customer::find()->where(['username' => $this->username])->count();   
                if($count>0) return true;
            }
            
            return false;
        }
        
        public function login() {
            $customer = Customer::find()->where(['email' => $this->email])->one();
            
            if(!$customer) return false;
            
            return $customer->pwd == $this->pwd;    
        }
        
        public function getDishes()
        {
            return $this->hasMany(Dish::className(), ['email' => 'email']);
        }
        
        public function getTreatments()
        {
            return $this->hasMany(Treatment::className(), ['email' => 'email']);
        }
    }
