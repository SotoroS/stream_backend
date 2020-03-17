<?php
// Строгая типизация

namespace micro\controllers;

use Yii;

use yii\base\Exception;

use yii\rest\Controller;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use micro\models\User;
use micro\models\University;
use micro\models\City;
use micro\models\CityArea;
use micro\models\RentType;
use micro\models\PropertyType;

use Facebook;
use Google_Client;
use Google_Service_Oauth2;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;

/**
 * Class UserController
 * @package micro\controllers
 */
class AdminController extends Controller
{
    public function behaviors()
    {
        // удаляем rateLimiter, требуется для аутентификации пользователя
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['login', 'signup-web', 'signup-mob', 'get-areas', 'verify', 'update', 'login-facebook', 'login-google'],
            'rules' => [
                [
                    'actions' => ['login', 'signup-web', 'signup-mob', 'get-areas', 'verify', 'login-facebook', 'login-google'],
                    'allow' => true,
                    'roles' => ['?'],
                ],
                [
                    'actions' => ['update', 'get-areas'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'signup-web' => ['post'],
                'signup-mob' => ['post'],
                'get-areas' => ['get'],
                'verify' => ['get'],
                'update' => ['post'],
                'login-facebook' => ['get'],
                'login-google' => ['get'],
                'login' => ['post'],
            ],
        ];

        // Возвращает результаты экшенов в формате JSON  
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        // Включение аутентификации по OAuth 2.0
        $behaviors['authenticator'] = [
            'except' => ['send-invite', 'signup-mob', 'signup-web', 'user-ban', 'verify', 'login-facebook', 'login-google'],
            'class' => HttpBearerAuth::className()
        ];

        return $behaviors;
    }

    /**
     * sending a university registration invitation for teachers
     * 
     * @return array
     */
    public function actionSendInvite()
    {
        $email = Yii::$app->request->post('email');

        if (!$email) {
            throw new Exception('Mail is not specified');
        }

        // $univer = University::findOne(Yii::$app->university->identity->id);

        // if ($univer->verified != 1) {
        //     throw new Exception('Not enough rights');
        // }

        $model = User::findOne(['email' => $email]);

        if ($model) {
            throw new Exception('User with such mail exists');
        }

        $user = new User();

        $user->email = $email;
        $user->access_token = uniqid();
        $user->status = 0;
        $user->role = 1; // 0 - student; 1 - teacher
        $user->university_id = 2; //default
        // $user->university_id = $univer->id;

        if (!$user->save()) {
            throw new Exception('Cannot save user');
        }

        $message = Yii::$app->mailer->compose();

        $message
            ->setFrom(Yii::$app->params['email'])
            ->setTo($email)
            ->setSubject('Подтверждение аккаунта')
            ->setHtmlBody('Для продолжения регистрации перейдите <a href="https://iskakov.stream.fokin-team.ru/auth/signup-user?email=' . $email . '">по ссылке</a>');

        if ($message->send()) {
            return [
                "status" => true
            ];
        } else {
            throw new Exception("Cann't send email");
        }

    }

    public function actionUserBan()
    {
        $user_id = Yii::$app->request->post('user_id');

        if (!$user_id) {
            throw new Exception('User_id is not specified');
        }

        // $univer = University::findOne(Yii::$app->university->identity->id);

        // if ($univer->verified != 1) {
        //     throw new Exception('Not enough rights');
        // }

        $user = User::findOne($user_id);
        
        if (!$user->delete()) {
            throw new Exception('Cannot delete user');
        }

        return [
            'result' => true
        ];
    }
}
