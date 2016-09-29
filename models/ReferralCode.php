<?php

namespace app\models;

use Yii;
    use yii\db\Expression;

/**
 * This is the model class for table "tbl_referral_code".
 *
 * @property string $id
 * @property string $email
 * @property string $code
 * @property string $created_time
 * @property string $modified_time
 */
class ReferralCode extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_referral_code';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'code'], 'required'],  
            [['email'], 'string', 'max' => 256],
            [['code'], 'string', 'max' => 10],
            ['modified_time','default','value'=>new Expression('NOW()'),'on'=>'update'],
            [['created_time','modified_time'],'default','value'=>new Expression('NOW()'),'on'=>'insert'],
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
            'code' => 'Code',
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
}
