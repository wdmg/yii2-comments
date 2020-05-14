<?php

namespace wdmg\comments\models;

use Yii;

/**
 * This is the model class for table "{{%comments}}".
 *
 * @property int $id
 * @property int $parent_id
 * @property string $context
 * @property string $target
 * @property string $name
 * @property string $email
 * @property string $comment
 * @property int $user_id
 * @property int $status
 * @property string $session
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Users $user
 */

class Comments extends \yii\db\ActiveRecord
{
    const COMMENT_STATUS_REJECTED = -1; // Comment has been rejected
    const COMMENT_STATUS_AWAITING = 0; // Comment has awaiting moderation
    const COMMENT_STATUS_PUBLISHED = 1; // Comment has been published

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
            [['parent_id', 'user_id', 'status'], 'integer'],
            [['context', 'target', 'name', 'email', 'comment', 'session'], 'required'],
            [['context', 'name'], 'string', 'min' => 3, 'max' => 32],
            ['email', 'email'],
            ['comment', 'string'],
            ['target', 'string', 'max' => 128],
            ['status', 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];

        if (class_exists('\wdmg\users\models\Users') && isset(Yii::$app->modules['users']))
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
            'context' => Yii::t('app/modules/comments', 'Context'),
            'target' => Yii::t('app/modules/comments', 'Target'),
            'name' => Yii::t('app/modules/comments', 'Name'),
            'email' => Yii::t('app/modules/comments', 'E-mail'),
            'comment' => Yii::t('app/modules/comments', 'Comment'),
            'user_id' => Yii::t('app/modules/comments', 'User ID'),
            'status' => Yii::t('app/modules/comments', 'Status'),
            'session' => Yii::t('app/modules/comments', 'Session'),
            'created_at' => Yii::t('app/modules/comments', 'Created'),
            'updated_at' => Yii::t('app/modules/comments', 'Updated'),
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
