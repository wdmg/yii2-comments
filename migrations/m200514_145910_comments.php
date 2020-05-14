<?php

use yii\db\Migration;

/**
 * Class m200514_145910_comments
 */
class m200514_145910_comments extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%comments}}', [
            'id' => $this->bigPrimaryKey(),
            'parent_id' => $this->bigInteger()->null(),
            'context' => $this->string(32)->notNull(),
            'target' => $this->string(128)->notNull(),
            'name' => $this->string(32)->notNull(),
            'email' => $this->string(32)->notNull(),
            'comment' => $this->text(),
            'user_id' => $this->integer()->null(),
            'status' => $this->tinyInteger(1)->null()->defaultValue(0),
            'session' => $this->string(32)->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),

        ], $tableOptions);

        $this->createIndex('idx_comments_parent','{{%comments}}', ['parent_id'],false);
        $this->createIndex('idx_comments_condition','{{%comments}}', ['context', 'target'],false);
        $this->createIndex('idx_comments_name','{{%comments}}', ['name'],false);
        $this->createIndex('idx_comments_email','{{%comments}}', ['email'],false);
        $this->createIndex('idx_comments_session','{{%comments}}', ['session'],false);
        $this->createIndex('idx_comments_user','{{%comments}}', ['user_id'],false);
        $this->createIndex('idx_comments_status','{{%comments}}', ['status'],false);

        // Setup foreign key parent_id to id
        $this->addForeignKey(
            'fk_comments_to_parent',
            '{{%comments}}',
            'parent_id',
            '{{%comments}}',
            'id',
            'NO ACTION',
            'CASCADE'
        );

        // If exist module `Users` set foreign key `user_id` to `users.id`
        if (class_exists('\wdmg\users\models\Users')) {
            $userTable = \wdmg\users\models\Users::tableName();
            $this->addForeignKey(
                'fk_comments_to_users',
                '{{%comments}}',
                'user_id',
                $userTable,
                'id',
                'NO ACTION',
                'CASCADE'
            );
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_comments_parent', '{{%comments}}');
        $this->dropIndex('idx_comments_condition', '{{%comments}}');
        $this->dropIndex('idx_comments_name', '{{%comments}}');
        $this->dropIndex('idx_comments_email', '{{%comments}}');
        $this->dropIndex('idx_comments_session', '{{%comments}}');
        $this->dropIndex('idx_comments_user', '{{%comments}}');
        $this->dropIndex('idx_comments_status', '{{%comments}}');

        $this->dropForeignKey(
            'fk_comments_to_parent',
            '{{%comments}}'
        );

        if (class_exists('\wdmg\users\models\Users')) {
            $userTable = \wdmg\users\models\Users::tableName();
            if (!(Yii::$app->db->getTableSchema($userTable, true) === null)) {
                $this->dropForeignKey(
                    'fk_comments_to_users',
                    '{{%comments}}'
                );
            }
        }

        $this->truncateTable('{{%comments}}');
        $this->dropTable('{{%comments}}');
    }

}
