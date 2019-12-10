<?php

namespace wdmg\comments\models;

use Yii;

/**
 * This is the model class for table "{{%comments}}".
 *
 * @property int $id
 * @property int $parent_id
 * @property int $user_id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $photo
 * @property string $condition
 * @property string $comment
 * @property string $created_at
 * @property string $updated_at
 * @property string $session
 * @property int $is_published
 *
 * @property Users $user
 */
class Comments extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%comments}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['parent_id', 'user_id', 'is_published'], 'integer'],
            [['name', 'email', 'condition', 'session'], 'required'],
            [['comment'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'email', 'phone', 'session'], 'string', 'max' => 32],
            [['photo', 'condition'], 'string', 'max' => 64],
        ];

        if(class_exists('\wdmg\users\models\Users') && isset(Yii::$app->modules['users']))
            $rules[] = [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \wdmg\users\models\Users::class, 'targetAttribute' => ['user_id' => 'id']];
            
        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/modules/comments', 'ID'),
            'parent_id' => Yii::t('app/modules/comments', 'Parent ID'),
            'user_id' => Yii::t('app/modules/comments', 'User ID'),
            'name' => Yii::t('app/modules/comments', 'Name'),
            'email' => Yii::t('app/modules/comments', 'Email'),
            'phone' => Yii::t('app/modules/comments', 'Phone'),
            'photo' => Yii::t('app/modules/comments', 'Photo'),
            'condition' => Yii::t('app/modules/comments', 'Condition'),
            'comment' => Yii::t('app/modules/comments', 'Comment'),
            'created_at' => Yii::t('app/modules/comments', 'Created At'),
            'updated_at' => Yii::t('app/modules/comments', 'Updated At'),
            'session' => Yii::t('app/modules/comments', 'Session'),
            'is_published' => Yii::t('app/modules/comments', 'Is Published'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }
}
