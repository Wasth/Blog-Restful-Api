<?php

namespace app\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string $title
 * @property string $anons
 * @property string $text
 * @property string $tags
 * @property string $image
 * @property string $datetime
 */
class Post extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['title', 'anons', 'text', 'tags', 'image', 'datetime', 'user_id'], 'required'],
            [['title', 'anons', 'text', 'image'], 'required'],
            [['anons', 'text','tags'], 'string'],
            [['datetime'], 'safe'],
            [['title', 'tags'], 'string', 'max' => 100],
            [['image'],'file','extensions' => 'png, jpg, jpeg', 'maxSize' => 2097152]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'anons' => 'Anons',
            'text' => 'Text',
            'tags' => 'Tags',
            'image' => 'Image',
            'datetime' => 'Datetime',
            'user_id' => 'User ID',
        ];
    }
    public function fields()
    {
        return ['title', 'datetime', 'anons', 'text', 'tags', 'image'=>'imageUrl'];
    }


    public function getImageUrl(){
        return Url::toRoute('post_images/'.$this->image,true);
    }
    public function getComments(){
        return $this->hasMany(Comment::className(), ['post_id' => 'id']);
    }
}
