<?php

namespace app\controllers;


use app\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\rest\ActiveController;
use yii\rest\Controller;

class UserController extends RestController
{
//    public $modelClass = 'app\models\User';
    public function behaviors()
    {
        $behaviors = parent::behaviors();
//        $behaviors['corsFilter'] = [
//            'class' => Cors::className(),
//            'cors' => [
//                'Origin' => ['*'],
//                'Access-Control-Request-Method' => ['GET', 'OPTIONS', 'PATCH', 'POST', 'PUT'],
//                'Access-Control-Request-Headers' => ['Authorization', 'Content-Type'],
//                'Access-Control-Max-Age' => 3600
//            ]
//        ];

        return $behaviors;
    }

    public function actionIndex(){
        return ['aaa'=>'sss'];
    }
    public function actionView($id) {
        return User::findOne($id);
    }
    public function actionAuth(){

        $login = Yii::$app->request->post('login');
        $password = Yii::$app->request->post('password');
        if($login && $password){
            $user = new User();
            $user->login = $login;
            $user->password = $password;
            $token = $user->login();

            if($token) {
                $user->token = $token;
                $user->save();
                Yii::$app->response->statusText = 'Successful authorization';
                return [
                    'status' => true,
                    'token' => $token,
                ];
            }
        }
        Yii::$app->response->statusCode = 401;
        Yii::$app->response->statusText = 'Invalid authorization data';
        return [
            'status' => false,
            'message' => 'Invalid authorization data',
        ];

    }

}