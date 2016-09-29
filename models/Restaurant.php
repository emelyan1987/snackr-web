<?php

    namespace app\models;

    use Yii;
    use yii\db\Expression;

    /**
    * This is the model class for table "tbl_restaurant".
    *
    * @property integer $id
    * @property string $place_id
    * @property string $title
    * @property string $address
    * @property string $location
    * @property string $description
    * @property string $created_time
    * @property integer $is_published
    * @property string $modified_time
    */
    class Restaurant extends \yii\db\ActiveRecord
    {
        private $idCache;
        /**
        * @inheritdoc
        */
        public static function tableName()
        {
            return 'tbl_restaurant';
        }

        /**
        * @inheritdoc
        */
        public function rules()
        {
            return [
                [['title', 'address', 'location'], 'required'],
                [['description'], 'string'],
                [['is_published'], 'integer'],
                [['created_time', 'modified_time'], 'safe'],
                [['place_id'], 'string', 'max' => 256],
                [['title', 'location'], 'string', 'max' => 255],
                [['tel'], 'string', 'max' => 20],
                [['address'], 'string', 'max' => 512],
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
                'place_id' => 'Place ID',
                'title' => 'Title',
                'tel' => 'TelPhone',
                'address' => 'Address',
                'location' => 'Location',
                'description' => 'Description',
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
        

        public function beforeDelete()
        {
            $this->idCache = $this->id; 

            return parent::beforeDelete();
        }
        public function afterDelete()    
        {                                                                                   
            $dishes_associated_with_restaurant = Dish::findAll(['restaurant_id'=>$this->idCache]);

            foreach ($dishes_associated_with_restaurant as $dish)
            {
                $dish->delete(); 
            } 

            parent::afterDelete();
        }
        public function getDishes()
        {
            return $this->hasMany(Dish::className(), ['restaurant_id' => 'id']);
        }
    }
