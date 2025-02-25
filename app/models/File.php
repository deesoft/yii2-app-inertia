<?php

namespace app\models;

use app\helpers\Tools;
use Yii;
use yii\base\DynamicModel;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\imagine\Image;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property string|null $type mime type
 * @property string|null $name original name
 * @property resource|null $content
 * @property string $url
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 */
class File extends ActiveRecord
{
    const ADDED_ID = 128 * 1024 * 1024;

    public static $types;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'default', 'value' => function () {
                    return Tools::generateUuid(16);
                }],
            [['content'], 'string'],
            [['type'], 'string', 'max' => 64],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     *
     * @param int $width
     * @param int $height
     * @return string
     */
    public function thumbnail($width = null, $height = null)
    {
        $id = $this->id;
        if ($width != null && $height != null) {
            $basename = "{$width}x{$height}.jpg";
        } elseif ($width != null) {
            $basename = "w{$width}.jpg";
        } elseif ($height != null) {
            $basename = "h{$height}.jpg";
        } else {
            $width = 120;
            $basename = "w{$width}.jpg";
        }

        $subpath = substr($id, -2);
        $filename = Yii::getAlias("@runtime/thumbnails/$subpath/{$id}-{$basename}");
        if (!file_exists($filename)) {
            FileHelper::createDirectory(dirname($filename));
            $image = Image::getImagine()->read($this->content);
            Image::thumbnail($image, $width, $height)->save($filename);
            @chmod($filename, 0777);
        }
        return $filename;
    }

    /**
     *
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getUrl($width = null, $height = null)
    {
        $url = Url::base(true) . "/file/{$this->id}";
        if ($width && $height) {
            return "$url/{$width}x{$height}";
        } elseif ($width) {
            return "$url/w{$width}";
        } elseif ($height) {
            return "$url/h{$height}";
        }
        return $url;
    }

    public static function getDb()
    {
        return Yii::$app->dbFile;
    }

    /**
     *
     * @param UploadedFile $file
     * @param string $type
     * @return static|DynamicModel
     * @throws ServerErrorHttpException
     */
    public static function store(UploadedFile $file, $type = null)
    {
        $rule = ['file', 'file', 'skipOnEmpty' => false];
        if (!empty(static::$types[$type])) {
            foreach (static::$types[$type] as $key => $value) {
                if (!array_key_exists($key, $rule)) {
                    $rule[$key] = $value;
                }
            }
        }
        $fModel = DynamicModel::validateData(['file' => $file], [$rule]);
        if ($fModel->hasErrors()) {
            return $fModel;
        }
        return static::saveAs($file->tempName, $file->name);
    }

    /**
     *
     * @param string $filename
     * @param string $name
     */
    public static function saveAs($filename, $name='')
    {
        $model = new static();
        $model->name = $name ? : StringHelper::basename($filename);
        $model->type = FileHelper::getMimeType($filename);
        $model->content = file_get_contents($filename);
        if (!$model->save() && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }

    public static function resolveLink($value)
    {
        if(is_array($value)){
            return array_map([static::class, 'resolveLink'], $value);
        }
        if (preg_match('/^[a-z0-9]{1,16}$/', $value)) {
            return Url::base(true) . "/file/{$value}";
        }
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'name' => 'Name',
            'content' => 'Content',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['url'] = 'url';
        unset($fields['content']);
        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class
        ];
    }
}

File::$types = [
    'image' => [
        'extensions' => ['jpg', 'jpeg', 'png'],
        'maxSize' => 8 * 1024 * 1024,
    ],
];
