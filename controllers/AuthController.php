<?php

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

/**
 * Class UserController
 * @package micro\controllers
 */
class AuthController extends Controller
{
    /*
    public function behaviors()
    {
        // удаляем rateLimiter, требуется для аутентификации пользователя
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['login', 'signup-user', 'signup-university'],
            'rules' => [
                [
                    'actions' => ['login', 'signup-user', 'signup-university'],
                    'allow' => true,
                    'roles' => ['?'],
                ],
                [
                    'actions' => [],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'signup-user' => ['post'],
                'signup-university' => ['post'],
                'login' => ['post'],
            ],
        ];

        // Возвращает результаты экшенов в формате JSON  
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        // Включение аутентификации по OAuth 2.0
        $behaviors['authenticator'] = [
            'except' => ['login', 'signup-user', 'signup-university'],
            'class' => HttpBearerAuth::className()
        ];

        return $behaviors;
    }
*/
    /**
     * Signup from web
     * 
     * @param string $email - email address
     * @param string $password - password
     * 
     * @param string $first_name - name
     * @param string $last_name - name
     * @param string $patronymic - name
     * 
     * @param int $role - role 0-student, 1-lecturer
     * 
     * 
     * @return array
     */
    public function actionSignupUser()
    {
        try {
            $request = Yii::$app->request->post();

            if (!isset($request['email']) || !isset($request['password']) || !isset($request['patronymic']) || !isset($request['first_name']) || !isset($request['last_name']) || !isset($request['role'])) {
                return [
                    'error'=>"Request is not complite"
                ];
            }

            // Find user by email
            $user = User::findOne(['email' => $request['email']]);

            // If not exist user by email - create new user
            if (is_null($user)) {
                $model = new User();

                if (!$model->load($request, '')) {
                    return [
                        'error'=>"Request is Not Complite"
                    ];
                }

                $password = password_hash($request['password'], PASSWORD_DEFAULT);

                $model->password = $password;

                $model->access_token = uniqid();

                if (!$model->save()) {
                    throw new Exception('Cann\'t save user model');
                }

                return [
                    'result'=>true
                ];
            } else {
                return [
                    'error' => "User already exist"
                ];
            }
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);

            return [
                'error' => $e->getMessage()
            ];
        }
    }

     /**
     * Signup from web
     * 
     * @param string $name - name
     * 
     * @return array
     */
    public function actionSignupUniversity()
    {
        try {
            $request = Yii::$app->request;

            $name = $request->post('name');

            if (is_null($name)) {
                return [
                    'error'=>"Request is Not Complite"
                ];
            }

            // Find user by email
            $university = University::findOne(['name'=>$name]);

            // If not exist user by email - create new user
            if (is_null($university)) {
                $model = new University();

                $model->name = $name;

                if (!$model->save()) {
                    throw new Exception('Cann\'t save university model');
                }

                return [
                    'result'=>true
                ];
            } else {
                return [
                    'error' => "University already exist"
                ];
            }
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);

            return [
                'error' => $e->getMessage()
            ];
        }
    }

     /**
     * Signup from web
     * 
     * @param string $email - email
     * 
     * @param string $password - new password
     * 
     * @return array
     */
    public function actionRestorePassword()
    {
        try {

            $email = Yii::$app->request->post('email');
            $newPassword = Yii::$app->request->post('password');

            if (is_null($email) || is_null($newPassword) || is_null(User::findOne(['email'=>$email]))) {
                return [
                    'error'=>"User not found"
                ];
            }

            

            $message = Yii::$app->mailer->compose();

            $message
                ->setFrom('paperchas@mail.ru')
                ->setTo($email)
                ->setSubject('Восстановление пароля')
                ->setHtmlBody('Ваш пароль для входа: ' . $newPassword . '. Для того, чтобы применить этот пароль перейдите по ссылке: <a href="' . $_SERVER['HTTP_HOST'] . "/auth/reset?p=" . password_hash($newPassword, PASSWORD_DEFAULT) . '">ссылка</a>');

            if ($message->send()) {
                return [
                    "status" => true
                ];
            } else {
                throw new Exception("Cann't send email");
            }

        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);

            return [
                'error' => $e->getMessage()
            ];
        }
    }

         /**
     * Signup from web
     * 
     * @param string $email - email
     * 
     * @return array
     */
    public function actionReset()
    {
        try {

            //$request = 
            return true;

        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);

            return [
                'error' => $e->getMessage()
            ];
        }
    }

}


