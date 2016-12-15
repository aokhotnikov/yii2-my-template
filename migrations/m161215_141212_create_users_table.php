<?php

use yii\db\Migration;

/**
 * Handles the creation of table `users`.
 */
class m161215_141212_create_users_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'password' => $this->string()->notNull(),
            'email' => $this->string()->notNull()->unique(),
            'status' => "enum('new','activated','banned') NOT NULL DEFAULT 'new'",
            'auth_key' => $this->string(32)->notNull(),
            'token' => $this->string()->null()->unique(),
            'isAdmin' => $this->boolean()->defaultValue(0),
            'activation_token' => $this->string()->null()->unique(),
            'activation_at' => $this->dateTime()->null(),
            'password_reset_token' => $this->string()->null()->unique(),
            'password_reset_at' => $this->dateTime()->null(),
            'created_at' => $this->dateTime()->null(),
            'updated_at' => $this->dateTime()->null(),
        ]);

        $this->insert('users', array(
            'username' => Yii::$app->params['adminName'],
            'password' => Yii::$app->security->generatePasswordHash(Yii::$app->params['adminPass']),
            'email' => Yii::$app->params['adminEmail'],
            'status' => 'activated',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'isAdmin' => true,
        ));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('users');
    }
}
