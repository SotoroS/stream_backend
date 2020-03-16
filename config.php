<?php

/**
 * Created by PhpStorm.
 * User: sotoros
 * Date: 14.11.2019
 * Time: 1:11
 */

return [
	'id' => 'donate',
	'basePath' => __DIR__,
	'runtimePath' => __DIR__ . '/runtime',
	'controllerNamespace' => 'micro\controllers',
	'aliases' => [
		'@micro' => __DIR__,
	],
	'params' => [
		// If you change username, you should change mail value in Mailer component
		'email' => 'test.fokin.team@gmail.com',
		'google_client_id' => '156874812665-unh00vf96tmf4msn0j43fhie0b69k6ke.apps.googleusercontent.com',
		'google_client_secret' => '0qepssGons1TcyctkXfW-IPO',
		'google_redirect_uri' => 'https://rest.fokin-team.ru/user/login-google',
		'facebook_client_id' => 559755891418423,
		'facebook_client_secret' => 'f5a86f378bca716435d1db271695dedd',
		'facebook_client_uri' => 'https://rest.fokin-team.ru/user/login-facebook',
	],
	'defaultRoute' => 'site/index',
	'bootstrap' => [
		'log'
	],
	'components' => [
		'urlManager' => [
			'class' => 'yii\web\UrlManager',
			'showScriptName' => false,
			'enablePrettyUrl' => true,
			'rules' => [
				'<controller:\w+>/<id:\d+>' => '<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
				'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
			],
		],
		'db' => [
			'class' => 'yii\db\Connection',
			'dsn' => 'mysql:host=localhost;dbname=backend_stream',
			'username' => 'GodOfDB',
			'password' => 'hard_pass!',
			'charset' => 'utf8',
		],
		'request' => [
			'enableCookieValidation' => false,
			'enableCsrfValidation' => false,
			'parsers' => [
				'application/json' => 'yii\web\JsonParser',
			]
		],
		'response' => [
			'format' =>  \yii\web\Response::FORMAT_JSON,
		],
		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			'transport' => [
				'class' => 'Swift_SmtpTransport',
				'host' => 'smtp.gmail.com',
				// If you change username, you should change mail value in param
				'username' => 'test.fokin.team@gmail.com',
				'password' => 'Qwerty34',
				'port' => '465',
				'encryption' => 'ssl',
			],
		],
		'hereMaps' => [
			'class' => 'micro\components\HereMapsComponent',
			'apiKey' => 'GIGsSEJb9m1LlcOOpL6jQSP-Mz51UEaV-kGj4orep1k'
		],
		'user' => [
			'identityClass' => 'micro\models\User',
			'enableSession' => false,
		],
		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['info', 'error', 'warning'],
				],
			],
		],
	],

];
