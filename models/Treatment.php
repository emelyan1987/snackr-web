<?php

    namespace app\models;

    use Yii;
    use yii\db\Expression;

    /**
    * This is the model class for table "tbl_treatment".
    *
    * @property string $id
    * @property string $email
    * @property string $dish_id
    * @property string $action
    * @property string $created_time
    * @property string $modified_time
    */
    class Treatment extends \yii\db\ActiveRecord
    {
        /**
        * @inheritdoc
        */
        public static function tableName()
        {
            return 'tbl_treatment';
        }

        /**
        * @inheritdoc
        */
        public function rules()
        {
            return [
                [['email', 'dish_id', 'action'], 'required'],
                [['dish_id'], 'integer'],
                [['created_time', 'modified_time'], 'safe'],
                [['action'], 'string', 'max' => 1],
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
                'email' => 'Customer Email',
                'dish_id' => 'Dish ID',
                'action' => 'Action',
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
        
        public function getCustomer()
        {
            return $this->hasOne(Customer::className(), ['email' => 'email']);
        }
        public function getDish()
        {
            return $this->hasOne(Dish::className(), ['id' => 'dish_id']);
        }
    }
