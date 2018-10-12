<?php
/**
 * Created by PhpStorm.
 * User: WSR-666
 * Date: 05.09.2018
 * Time: 13:57
 */

namespace app\controllers;


use app\models\Post;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class PostController extends RestController
{
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => ['index','view', 'search'],
            'optional' => ['create', 'update','delete'],
        ];
        return $behaviors;
    }
    public function beforeAction($action)
    {
        if(parent::beforeAction($action)){
            if(Yii::$app->user->identity->isAdmin == 1) {
                return true;
            }
            return [
                'status' => false,
                'message' => [
                    'auth' => 'User is not admin'
                ]
            ];
        }
        return false;
    }

    public function actionCurnick(){
        return Yii::$app->user->identity->isAdmin == 1;
    }
    public function actionCreate(){
        if(Yii::$app->user->identity->isAdmin == 1) {
            $model = new Post();
            if($model->load(Yii::$app->request->post(),'')){
                $model->image = UploadedFile::getInstanceByName('image');
                if($model->validate()) {
                    $filename = uniqid().'.'.$model->image->extension;
                    $model->image->saveAs('api/post_images/'.$filename);
                    $model->image = $filename;
                    $model->datetime = date('Y-m-d H:i:s');
                    $model->save();
                    Yii::$app->response->statusCode = 201;
                    Yii::$app->response->statusText = 'Successful creation';
                    return [
                        'status' => true,
                        'post_id' => $model->id
                    ];
                }
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
        Yii::$app->response->statusCode = 401;
        Yii::$app->response->statusText = 'Unauthorized';
        return [
            'message' => 'Unauthorized',
        ];

    }
    public function actionUpdate($id){
        if(Yii::$app->user->identity->isAdmin == 1) {
            $model = Post::findOne($id);
            $messages = [];
            $statusCode = 400;
            $statucText = 'Editing error';
            if($model) {
                if($model->load(Yii::$app->request->post(),'')){
                    $new_image = UploadedFile::getInstanceByName('image');
                    if($new_image){
                        $oldfile = $model->image;
                        $model->image = $new_image;
                    }
                    if($model->validate()){
                        if($new_image) {
                            $filename = uniqid().'.'.$model->image->extension;
                            $model->image->saveAs('api/post_images/'.$filename);
//                    unlink($oldfile);
                            $model->image = $filename;
                        }
                        $model->save();
                        Yii::$app->response->statusCode = 201;
                        Yii::$app->response->statusText = 'Successful creation';
                        return [
                            'status' => true,
                            'post' => [
                                'title' => $model->title,
                                'datetime' => $model->datetime,
                                'anons' => $model->anons,
                                'text' => $model->text,
                                'tags' => $model->tags,
                                'image' => $model->getImageUrl(),
                            ]
                        ];
                    }
                }
            }else {
                $statusCode = 404;
                $statusText = 'Post not found';
            }




            Yii::$app->response->statusCode = $statusCode;
            Yii::$app->response->statusText = $statusText;
            foreach ($model->errors as $key => $error){
                $messages[$key] = $error[0];
            }
            return [
                'status' => false,
                'message' => $messages,
            ];
        }
        Yii::$app->response->statusCode = 401;
        Yii::$app->response->statusText = 'Unauthorized';
        return [
            'message' => 'Unauthorized',
        ];

    }
    public function actionDelete($id){
        if(Yii::$app->user->identity->isAdmin == 1) {
            $model = Post::findOne($id);
            if($model) {
                $model->delete();
                Yii::$app->response->statusCode = 201;
                Yii::$app->response->statusText = 'Successful delete';
                return [
                    'status' => true,
                ];
            }
            Yii::$app->response->statusCode = 404;
            Yii::$app->response->statusText = 'Post not found';
            return [
                'message' => 'Post not found'
            ];
        }
        Yii::$app->response->statusCode = 401;
        Yii::$app->response->statusText = 'Unauthorized';
        return [
            'message' => 'Unauthorized',
        ];

    }
    public function actionIndex(){
        $posts = new Post();
        return $posts->find()->all();
    }
    public function actionView($id){
        $post = Post::findOne($id);
        if($post){
            return [
                'title' => $post->title,
                'datetime' => $post->datetime,
                'anons' => $post->anons,
                'text' => $post->text,
                'tags' => $post->tags,
                'image' => $post->getImageUrl(),
                'comments' => $post->comments,
            ];
        }
        Yii::$app->response->statusText = 'Post not found';
        Yii::$app->response->statusCode = 404;
        return [
            'message' => 'Post not found',
        ];


    }
    public function actionSearch($tag){
        $posts = Post::find()->where(['LIKE', 'tags', $tag])->all();
        if($posts) {
            Yii::$app->response->statusText = 'Found posts';
            Yii::$app->response->statusCode = 200;
            return $posts;
        }
    }
}