<?php
// Строгая типизация
declare(strict_types=1);

namespace micro\controllers;

use Yii;
use \DateTime;

use yii\base\Exception;

use yii\rest\Controller;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use micro\models\User;
use micro\models\Message;
use micro\models\CityArea;
use micro\models\RentType;
use micro\models\PropertyType;

use Facebook;
use Google_Client;
use Google_Service_Oauth2;

//use RingCentral\Psr7\Stream;
 
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

    // public function actionSendMsg()
    // {
    //     $request = Yii::$app->request;

    //     $text = $request->post('text');
    //     $file = 
    // }

    public function actionCreateStream()
    {
        $request = Yii::$app->request;
        $user_id = Yii::$app->user->identity->id;
        $user = User::findOne($user_id);

        if ($user->role == 1) {
            $stream = new Stream();
            $current = new DateTime(date("d.m.Y H:i:s"));
            $current = $current->format('Y-m-d H:i:s');

            $stream->name = $request->post('name');
            $stream->date = $current;
            $stream->user_id = $user_id;

            if ($stream->save()) {
                return 'fokin-team.ru/link-to-stream'.uniqid();
            } else {
                return ["error" => $stream->error];
            }
        }
    }

    public function actionSendMsg()
    {
        $request = Yii::$app->request->post();

        $text = $request['text'];

        // TO DO adding files
        $file = '';
        $stream_id = Yii::$app->request->get('stream_id');

        if (!$text && !$file) {
            throw new Exception('Not text or message file specified');
        }

        $user = User::findOne(Yii::$app->user->identity->id);

        if ($user->status == 0) {
            throw new Exception('Not enough rights');
        }

        $message = new Message();

        if ($text) {
            $message->text = $text;
        }

        if ($file && $user->role == 1) {
            $message->file = $file;
        }
        $message->user_id = $user->id;
        $message->date = new DateTime('now');
        $message->stream_id = $stream_id;

        if (!$message->save()) {
            throw new Exception('Cannot save message');
        }

    }
}
