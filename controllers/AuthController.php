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
use micro\models\Restore;

/**
 * Class UserController
 * @package micro\controllers
 */
class AuthController extends Controller
{
    public function behaviors()
    {
        // удаляем rateLimiter, требуется для аутентификации пользователя
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['login-user', 'login-university', 'signup-user', 'signup-university'],
            'rules' => [
                [
                    'actions' => ['login-user', 'login-university', 'signup-user', 'signup-university'],
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
                'signup-user' => ['post', 'get'],
                'signup-university' => ['post'],
                'login-user' => ['post'],
                'login-university' => ['post'],
            ],
        ];

        // Возвращает результаты экшенов в формате JSON  
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        // Включение аутентификации по OAuth 2.0
        $behaviors['authenticator'] = [
            'except' => ['login-user', 'login-university', 'signup-user', 'signup-university'],
            'class' => HttpBearerAuth::className()
        ];

        return $behaviors;
    }

    /**
     * Signup
     * 
     * Post
     * @param string $password - password
     * 
     * @param string $first_name - name
     * @param string $last_name - name
     * @param string $patronymic - name
     * 
     * @param int $role - role 0-student, 1-lecturer
     * 
     * Get
     * @param string $email
     * 
     * @return array
     */
    public function actionSignupUser()
    {
        try {
            $request = Yii::$app->request->post();

            if (!isset($request['password']) || !isset($request['patronymic']) || !isset($request['first_name']) || !isset($request['last_name']) || !isset($request['role'])) {
                return [
                    'error'=>"Request is not complite"
                ];
            }

            // Find user by email
            $model = User::findOne(['email' => Yii::$app->request->get('email')]);
       
            // If not exist user by email - create new user
            if (is_null($model) || !$model->status == 0) {
                return [
                    'error'=>"User Not Found"
                ];
            }

            if (!$model->load($request, '')) {
                return [
                    'error'=>"Request is Not Complite"
                ];
            }

            $password = password_hash($request['password'], PASSWORD_DEFAULT);

            $model->password = $password;

            $model->access_token = uniqid();

            $model->status = 1;

            if (!$model->save()) {
                throw new Exception('Cann\'t save user model');
            }

            return [
                'result'=>true
            ];
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
     * @param string $email - email
     * @param string $password - password
     * 
     * @return array
     */
    public function actionSignupUniversity()
    {
        try {
            $request = Yii::$app->request->post();

            if (is_null($request['email']) || is_null($request['name']) || is_null($request['password'])) {
                return [
                    'error'=>"Request is Not Complite"
                ];
            }

            // Find user by email
            $university = University::findOne(['email'=>$request['email']]);

            // If not exist user by email - create new user
            if (is_null($university)) {
                $model = new University();

                $model->access_token = uniqid();
                if (!$model->load($request,'')) {
                    return [
                        'error'=>"load University Failed"
                    ];
                }

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

            $user = User::findOne(['email'=>$email]);

            if (is_null($email) || is_null($newPassword) || is_null($user)) {
                return [
                    'error'=>"User not found"
                ];
            }

            $restore = new Restore();

            $restore->user_id = $user->id;
            $restore->password = password_hash($newPassword, PASSWORD_DEFAULT);

            if (!$restore->save()) {
                return [
                    'error'=>"Restore cannot save"
                ];
            }

            $message = Yii::$app->mailer->compose();

            $message
                ->setFrom('paperchas@mail.ru')
                ->setTo($email)
                ->setSubject('Восстановление пароля')
                ->setHtmlBody('Для того, чтобы применить новый пароль перейдите по ссылке: <a href="' . $_SERVER['HTTP_HOST'] . "/auth/reset?id=" . $user->id . '">ссылка</a>');

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
     * @param string $id - user_id to restore
     * 
     * @return array
     */
    public function actionReset()
    {
        try {

            $user = User::findOne(['id'=>Yii::$app->request->get('id')]);

            if (is_null($user)) {
                return [
                    'error'=>"User Not Found"
                ];
            }

            $restore = Restore::findOne(['user_id'=>$user->id]);

            if (is_null($restore)) {
                return [
                    'error'=>"Restore Not Found"
                ];
            }

            $user->password = $restore->password;

            if (!$user->update() || !$restore->delete()) {
                return [
                    'error'=>'Cannot Update User'
                ];
            }

            return [
                'result'=>true
            ];

        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);

            return [
                'error' => $e->getMessage()
            ];
        }
    }

     /**
     * LOGIN USER
     * 
     * @param string $email
     * @param string $password
     * 
     * @return array
     */
    public function actionLoginUser()
    {
        try {

            if (is_null($email) && is_null($password)) {
                throw new Exception("Request is empty Email or Password not found");
            }

            $user = User::findOne(['email' => $email]);

            if (is_null($user)) {
                throw new Exception("User not found by email");
            }
        
            if (!password_verify($password, $user->password)) {
                throw new Exception("Wrong Password");
            }

            if ($user->status == 1) {
                $user->access_token = uniqid();

                if ($user->update()) {
                    return [
                        "access_token" => $user->access_token
                    ];
                } else {
                    throw new Exception("Cann't generate new access token");
                }
            } else {
                throw new Exception("Confirm your account by clicking on the link in the mail");
            }

            return [
                'result'=>true
            ];

        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);

            return [
                'error' => $e->getMessage()
            ];
        }
    }

     /**
     * LOGIN UNIVERSITY
     * 
     * @param string $email
     * @param string $password
     * 
     * @return array
     */
    public function actionLoginUniversity()
    {
        try {

            if (is_null($email) && is_null($password)) {
                throw new Exception("Request is empty Email or Password not found");
            }

            $university = University::findOne(['email' => $email]);

            if (is_null($university)) {
                throw new Exception("User not found by email");
            }
        
            if (!password_verify($password, $university->password)) {
                throw new Exception("Wrong Password");
            }

            if ($university->verify == 1) {
                $university->access_token = uniqid();

                if ($university->update()) {
                    return [
                        "access_token" => $university->access_token
                    ];
                } else {
                    throw new Exception("Cann't generate new access token");
                }
            } else {
                throw new Exception("Confirm your account by clicking on the link in the mail");
            }

            return [
                'result'=>true
            ];

        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);

            return [
                'error' => $e->getMessage()
            ];
        }
    }

}


