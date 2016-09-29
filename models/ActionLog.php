<?php

    namespace app\models;

    use Yii;
    use yii\db\Expression;
    /**
    * This is the model class for table "tbl_log".
    *
    * @property string $id
    * @property string $email
    * @property string $remote_addr
    * @property string $action_time
    * @property string $action_type
    */
    class ActionLog extends \yii\db\ActiveRecord
    {
        /**
        * @inheritdoc
        */
        public static function tableName()
        {
            return 'tbl_log';
        }

        /**
        * @inheritdoc
        */
        public function rules()
        {
            return [
                [['email', 'remote_addr', 'action_type'], 'required'],
                [['action_time'], 'safe'],
                [['email', 'remote_addr', 'action_type'], 'string', 'max' => 255],
                ['action_time','default','value'=>new Expression('NOW()'),'on'=>'insert']
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
                'remote_addr' => 'Remote Addr',
                'action_time' => 'Action Time',
                'action_type' => 'Action Type',
            ];
        } 

        public function beforeSave($insert) {   
            
            $this->action_time = new Expression('NOW()');

            return parent::beforeSave($insert);
        }
        
        public function log($action) {
            if(isset($_SESSION["email"])) {                 
                $this->email = $_SESSION["email"];
                $this->remote_addr = $_SERVER['REMOTE_ADDR'];
            
                $this->action_type = $action;
                $this->save(); 
            } 
        }
    }
