<?php

use yii\db\Migration;

/**
 * Class m240319_125132_comments
 */
class m240319_125132_comments extends Migration
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
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer()->null(),
            'user_id' => $this->integer()->null(),

            'name' => $this->string(32)->notNull(),
            'email' => $this->string(32)->notNull(),
            'phone' => $this->string(32)->null(),
            'photo' => $this->string(64)->null(),

            'condition' => $this->string(64)->notNull(),
            'comment' => $this->text(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
            'session' => $this->string(32)->notNull(),
            'is_published' => $this->boolean(),
        ], $tableOptions);

        $this->createIndex('idx_comments_parent','{{%comments}}', ['parent_id'],false);
        $this->createIndex('idx_comments_user','{{%comments}}', ['user_id'],false);
        
        $this->createIndex('idx_comments_name','{{%comments}}', ['name'],false);
        $this->createIndex('idx_comments_email','{{%comments}}', ['email'],false);
        
        $this->createIndex('idx_comments_condition','{{%comments}}', ['condition'],false);
        $this->createIndex('idx_comments_session','{{%comments}}', ['session'],false);
        $this->createIndex('idx_comments_published','{{%comments}}', ['is_published'],false);

        // If exist module `Users` set foreign key `user_id` to `users.id`
        if(class_exists('\wdmg\users\models\Users') && isset(Yii::$app->modules['users'])) {
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
        $this->dropIndex('idx_comments_user', '{{%comments}}');

        $this->dropIndex('idx_comments_name', '{{%comments}}');
        $this->dropIndex('idx_comments_email', '{{%comments}}');
        
        $this->dropIndex('idx_comments_condition', '{{%comments}}');
        $this->dropIndex('idx_comments_session', '{{%comments}}');
        $this->dropIndex('idx_comments_published', '{{%comments}}');

        if(class_exists('\wdmg\users\models\Users') && isset(Yii::$app->modules['users'])) {
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
