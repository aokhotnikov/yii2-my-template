<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $status
 * @property string $auth_key
 * @property string $token
 * @property integer $isAdmin
 * @property string $activation_token
 * @property string $activation_at
 * @property string $password_reset_token
 * @property string $password_reset_at
 * @property string $created_at
 * @property string $updated_at
 */
class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    const STATUS_NEW = 'new';
    const STATUS_ACTIVATED = 'activated';
    const STATUS_BANNED = 'banned';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'email', 'auth_key'], 'required'],
            [['status'], 'string'],
            [['isAdmin'], 'integer'],
            [['activation_at', 'password_reset_at', 'created_at', 'updated_at'], 'safe'],
            [['username', 'password', 'email', 'token', 'activation_token', 'password_reset_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['token'], 'unique'],
            [['activation_token'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'email' => 'Email',
            'status' => 'Status',
            'auth_key' => 'Auth Key',
            'token' => 'Token',
            'isAdmin' => 'Is Admin',
            'activation_token' => 'Activation Token',
            'activation_at' => 'Activation At',
            'password_reset_token' => 'Password Reset Token',
            'password_reset_at' => 'Password Reset At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

//  ---------------------------------------------  Поведения -------------------------------------------
    public function behaviors()
    {
        return [
            [
                // Генерит поля created_at и updated_at
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),   // for mysql
            ],
        ];
    }

//  ----------------------------------------------  Хелперы ---------------------------------------------
    public function generateActivationToken()
    {
        $this->activation_token = Yii::$app->security->generateRandomString().'_'.time();
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString();
        $this->touch('password_reset_at'); // touch - TimestampBehavior assign the current timestamp to the specified attribute and save them to the database
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
        $this->password_reset_at = null;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Генерирует случайную строку из 32 шестнадцатеричных символов и присваивает (при записи) полученное значение полю auth_key
     * таблицы user для нового пользователя.
     * Вызывается из модели SignupForm.
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Сравнивает полученный пароль с паролем в поле password_hash, для текущего пользователя, в таблице user.
     * Вызывается из модели LoginForm.
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVATED]);
    }

    /**
     * Finds user by password_reset_token
     *
     * @param string $key
     * @return static|null
     */
    public static function findByPasswordResetToken($key)
    {
        $user = static::findOne(['password_reset_token' => $key]);
        //VarDumper::dump($user, 10, true);die;       // for debug
        return static::isSecretKeyExpire($key, $user->password_reset_at) ? $user : null;
    }

    public static function isSecretKeyExpire($key, $password_reset_at)
    {
        if (empty($key)) {
            return false;
        }
        $expire = Yii::$app->params['secretKeyExpire'];
        $timestamp = Yii::$app->formatter->asTimestamp($password_reset_at);
        return ($timestamp + $expire) >= time();
    }

//  -----------------------------------------  IdentityInterface  -----------------------------------------
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVATED]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }
}
