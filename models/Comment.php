<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comment".
 *
 * @property int $id
 * @property int $user_id
 * @property string $text
 * @property int $post_id
 * @property string $datetime
 * @property string $author
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * @inheritdoc
     */
    public function fields(){
        return ['comment_id'=>'id', 'datetime', 'author', 'comment' => 'text'];
    }
    public function rules()
    {
        return [
            [['text', 'author'], 'required'],
            [['user_id', 'post_id'], 'integer'],
            [['text', 'author'], 'string'],
            [['datetime'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'text' => 'Text',
            'post_id' => 'Post ID',
            'datetime' => 'Datetime',
            'author' => 'Author',
        ];
    }
    public function getPost(){
        return $this->hasOne(Post::className(), ['id' => 'post_id']);
    }
}
