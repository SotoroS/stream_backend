<?php
// Строгая типизация
declare(strict_types=1);

namespace micro\controllers;

use Yii;

use yii\base\Exception;

use yii\rest\Controller;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use micro\models\User;
use micro\models\City;
use micro\models\CityArea;
use micro\models\RentType;
use micro\models\PropertyType;

use Facebook;
use Google_Client;
use Google_Service_Oauth2;

/**
 * Class UserController
 * @package micro\controllers
 */
class UserController extends Controller
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
            'except' => ['login', 'signup-mob', 'signup-web', 'get-areas', 'verify', 'login-facebook', 'login-google'],
            'class' => HttpBearerAuth::className()
        ];

        return $behaviors;
    }

    public function actionSendMsg()
    {
        $request = Yii::$app->request;

        $text = $request->post('text');
        $file = 
    }
}
