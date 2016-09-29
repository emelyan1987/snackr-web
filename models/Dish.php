<?php

    namespace app\models;

    use Yii;
    use yii\db\Expression;

    /**
    * This is the model class for table "tbl_dish".
    *
    * @property string $id
    * @property string $title
    * @property double $price
    * @property double $flag
    * @property double $is_blocked
    * @property integer $restaurant_id
    * @property string $email
    * @property string $created_time
    * @property string $modified_time
    */
    class Dish extends \yii\db\ActiveRecord
    {
        private $idCache;
        private $photoCache;
        
        /**
        * @inheritdoc
        */
        public static function tableName()
        {
            return 'tbl_dish';
        }

        /**
        * @inheritdoc
        */
        public function rules()
        {
            return [
                [['title', 'restaurant_id', 'email'], 'required'],
                [['price'], 'number'],
                [['restaurant_id', 'flag', 'is_blocked'], 'integer'],
                [['created_time', 'modified_time'], 'safe'],
                [['title', 'email', 'photo'], 'string', 'max' => 256],
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
                'title' => 'Title',
                'price' => 'Price',
                'restaurant_id' => 'Restaurant ID',
                'flag' => 'Flag',
                'is_blocked' => 'Is Blocked',
                'email' => 'Customer Email',
                'created_time' => 'Created Time',
                'modified_time' => 'Modified Time',
                'photo' => 'Photo',
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

        public function beforeDelete()
        {
            $this->idCache = $this->id;
            $this->photoCache = $this->photo;

            return parent::beforeDelete();
        }
        public function afterDelete()    
        {                                                                                   
            $treatments_associated_with_dish = Treatment::findAll(['dish_id'=>$this->idCache]);

            foreach ($treatments_associated_with_dish as $treatment)
            {
                $treatment->delete(); 
            }

            $filename = 'uploads/'.$this->photoCache;
            if(file_exists($filename))
            {
                unlink($filename);
            }

            parent::afterDelete();
        }


        public function getRestaurant()
        {
            return $this->hasOne(Restaurant::className(), ['id' => 'restaurant_id']);
        }

        public function getCustomer()
        {
            return $this->hasOne(Customer::className(), ['email' => 'email']);
        }

        public function getTreatments()
        {
            return $this->hasMany(Treatment::className(), ['dish_id' => 'id']);
        }
    }
