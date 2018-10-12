<?php
/**
 * Created by PhpStorm.
 * User: WSR-666
 * Date: 05.09.2018
 * Time: 13:57
 */

namespace app\controllers;


use app\models\Comment;
use app\models\Post;
use app\models\User;
use Yii;
use yii\filters\auth\HttpBearerAuth;

class CommentController extends RestController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'optional' => ['create','delete'],
        ];
        return $behaviors;
    }

    public function actionCreate($id){
        $model = new Comment();
        $data = Yii::$app->request->post();
        if($data['author']){
            $model->author = $data['author'];
        }else {
            $model->author = Yii::$app->user->identity->login;
        }

        $model->text = $data['comment'];
        if($model->validate()){
            $model->datetime = date('Y-m-d H:i:s');
            $model->post_id = $id;
            $model->save();
            Yii::$app->response->statusCode = 201;
            Yii::$app->response->statusText = 'Successful creation';
            return [
                'status' => true,
            ];
        }
        Yii::$app->response->statusCode = 400;
        Yii::$app->response->statusText = 'Creating error';
        $messages = [];
        foreach ($model->errors as $key => $error){
            $messages[$key] = $error[0];
        }
        return [
            'status' => false,
            'message' => $messages,
        ];

    }
    public function actionDelete($id, $post_id) {
        if(Yii::$app->user->identity->isAdmin == 1) {
            $error = '';
            $post = Post::findOne($post_id);
            if($post) {
                $comment = Comment::findOne($id);
                if($comment) {
                    $comment->delete();
                    Yii::$app->response->statusCode = 201;
                    Yii::$app->response->statusText = 'Successful delete';
                    return [
                        'status' => true,
                    ];
                }
                $error = 'Comment not found';
            }else {
                $error = 'Post not found';
            }
            Yii::$app->response->statusCode = 404;
            Yii::$app->response->statusText = $error;
            return [
                'message' => $error,
            ];
        }
        Yii::$app->response->statusCode = 401;
        Yii::$app->response->statusText = 'Unauthorized';
        return [
            'message' => 'Unauthorized',
        ];





    }

}

/*
if(Yii::$app->user->identity->isAdmin == 1) {

}
Yii::$app->response->statusCode = 401;
Yii::$app->response->statusText = 'Unauthorized';
return [
    'message' => 'Unauthorized',
];
*/