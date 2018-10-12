<?php
/**
 * Created by PhpStorm.
 * User: WSR-666
 * Date: 05.09.2018
 * Time: 13:57
 */

namespace app\controllers;


use yii\filters\Cors;
use yii\rest\Controller;

class RestController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['CorsFilter'] = [
            'class' => Cors::className(),
        ];
        return $behaviors;
    }
}