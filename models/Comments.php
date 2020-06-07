<?php

namespace wdmg\comments\models;

use Yii;
use yii\helpers\ArrayHelper;

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
    const COMMENT_STATUS_REJECTED = -2; // Comment has been rejected
    const COMMENT_STATUS_DELETED = -1; // Comment has been deleted
    const COMMENT_STATUS_AWAITING = 0; // Comment has awaiting moderation
    const COMMENT_STATUS_PUBLISHED = 1; // Comment has been published

    const COMMENT_SCENARIO_CREATE = 'create';
    const COMMENT_SCENARIO_UPDATE = 'update';
    const COMMENT_SCENARIO_LISTING = 'listing';

    protected $module;
    protected $count;

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
            [['id'], 'integer', 'on' => self::COMMENT_SCENARIO_UPDATE],
            [['parent_id', 'user_id', 'status'], 'integer'],
            [['context', 'target', 'name', 'email', 'comment'], 'required'],
            [['context', 'name'], 'string', 'min' => 3, 'max' => 32],
            ['email', 'email'],
            ['comment', 'string'],
            ['target', 'string', 'max' => 128],
            ['status', 'integer'],
            [['created_at', 'updated_at', 'session'], 'safe'],
        ];

        if (class_exists('\wdmg\users\models\Users'))
            $rules[] = [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \wdmg\users\models\Users::class, 'targetAttribute' => ['user_id' => 'id']];
            
        return $rules;
    }


    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::COMMENT_SCENARIO_CREATE] = ['parent_id', 'user_id', 'status', 'context', 'target', 'name', 'email', 'comment'];
        $scenarios[self::COMMENT_SCENARIO_UPDATE] = ['id', 'context', 'target', 'comment'];
        $scenarios[self::COMMENT_SCENARIO_LISTING] = ['parent_id', 'user_id', 'status', 'context', 'target', 'name', 'email', 'comment'];
        return $scenarios;
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
     * {@inheritdoc}
     */
    public function init()
    {
        if (!($this->module = Yii::$app->getModule('admin/comments')))
            $this->module = Yii::$app->getModule('comments');

        if (!$this->module->isBackend() && !$this->module->isRestAPI())
            $this->setScenario(self::COMMENT_SCENARIO_LISTING);

        if (in_array($this->scenario, [self::COMMENT_SCENARIO_CREATE, self::COMMENT_SCENARIO_UPDATE, self::COMMENT_SCENARIO_LISTING]))
            $this->prepareAttributes();

        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function beforeValidate()
    {
        if ($this->scenario == self::COMMENT_SCENARIO_CREATE || $this->scenario == self::COMMENT_SCENARIO_UPDATE) {
            $this->prepareAttributes();

            if ($this->onModerateStatus())
                $this->status = self::COMMENT_STATUS_AWAITING;
            else
                $this->status = self::COMMENT_STATUS_PUBLISHED;

        }

        return parent::beforeValidate();
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if ($this->scenario == self::COMMENT_SCENARIO_CREATE || $this->scenario == self::COMMENT_SCENARIO_UPDATE)
            $this->prepareAttributes();

        return parent::beforeSave($insert);
    }

    private function prepareAttributes() {
        if ($user = Yii::$app->getUser()) {
            if (!$user->isGuest && ($identity = $user->getIdentity())) {
                $this->name = $identity->username;
                $this->email = $identity->email;
                $this->user_id = $identity->getId();
            } else {
                $this->name = null;
                $this->email = null;
                $this->user_id = null;
            }
        }

        if ($session = Yii::$app->getSession() && !$this->scenario == self::COMMENT_SCENARIO_UPDATE)
            $this->session = $session->getId();

    }

    /**
     * @return |null
     */
    public function getCount()
    {
        if ($this->count)
            return $this->count;

        return null;
    }

    /**
     * @return |null
     */
    public function onModerateStatus()
    {
        if ($this->module->newCommentsModeration) {
            if ($this->module->approveFromRegistered && !(Yii::$app->getUser()->getIsGuest())) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getCounts()
    {
        return ArrayHelper::map(self::find()
            ->select(['status', 'COUNT(*) AS count'])
            ->where(['context' => $this->context, 'target' => $this->target])
            ->groupBy('status')
            ->asArray()
            ->all(), 'status', 'count');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\wdmg\users\models\Users::class, ['id' => 'user_id']);
    }


    /**
     * @param bool $allContexts
     * @return array
     */
    public function getContextsList($allContexts = false)
    {
        $list = [];
        if ($allContexts) {
            $list = [
                '*' => Yii::t('app/modules/comments', 'All contexts')
            ];
        }

        $contexts = self::find()->select('context', 'DISTINCT')->groupBy('context')->asArray()->all();
        return ArrayHelper::merge($list, ArrayHelper::map($contexts, 'context', 'context'));
    }

    /**
     * @param bool $allTargets
     * @return array
     */
    public function getTargetsList($allTargets = false)
    {
        $list = [];
        if ($allTargets) {
            $list = [
                '*' => Yii::t('app/modules/comments', 'All targets')
            ];
        }

        $targets = self::find()->select('target', 'DISTINCT')->groupBy('target')->asArray()->all();
        return ArrayHelper::merge($list, ArrayHelper::map($targets, 'target', 'target'));
    }

    /**
     * @param bool $allRanges
     * @return array
     */
    public function getCommentsRangeList($allRanges = false)
    {
        $list = [];
        if ($allRanges) {
            $list = [
                '*' => Yii::t('app/modules/comments', 'All ranges')
            ];
        }

        $list = ArrayHelper::merge($list, [
            '< 1000' => Yii::t('app/modules/comments', 'Less than 1K comments'),
            '>= 1000' => Yii::t('app/modules/comments', 'Over 1K comments'),
            '>= 10000' => Yii::t('app/modules/comments', 'Over 10K comments'),
            '> 100000' => Yii::t('app/modules/comments', 'Over 100K comments'),
            '> 1000000' => Yii::t('app/modules/comments', 'More than 1M comments'),
            '> 10000000' => Yii::t('app/modules/comments', 'More than 10M comments'),
        ]);

        return $list;
    }


    public function getStatusesList($allStatuses = false)
    {
        $list = [];
        if ($allStatuses) {
            $list = [
                '*' => Yii::t('app/modules/comments', 'All statuses')
            ];
        }

        return ArrayHelper::merge($list, [
            self::COMMENT_STATUS_REJECTED => Yii::t('app/modules/comments', 'Rejected'),
            self::COMMENT_STATUS_DELETED => Yii::t('app/modules/comments', 'Deleted'),
            self::COMMENT_STATUS_AWAITING => Yii::t('app/modules/comments', 'Awaiting moderation'),
            self::COMMENT_STATUS_PUBLISHED => Yii::t('app/modules/comments', 'Published'),
        ]);
    }
}
