<?php

namespace app\controllers;

use app\classes\Controller;
use app\models\File;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;
use function env;

/**
 * Description of FileController
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class FileController extends Controller
{

    /**
     * @SWG\Get(path="/file/{id}",
     *     tags={"file"},
     *     summary="Retrieves the file.",
     *     @SWG\Parameter(in="path", name="id",type="string"),
     *     @SWG\Response(
     *         response = 200,
     *         description = "File"
     *     ),
     * ),
     * @SWG\Get(path="/file/{id}/w{width}",
     *     tags={"file"},
     *     summary="Retrieves the file.",
     *     @SWG\Parameter(in="path", name="id",type="string"),
     *     @SWG\Parameter(in="path", name="width",type="integer"),
     *     @SWG\Response(
     *         response = 200,
     *         description = "File"
     *     ),
     * ),
     * @SWG\Get(path="/file/{id}/h{height}",
     *     tags={"file"},
     *     summary="Retrieves the file.",
     *     @SWG\Parameter(in="path", name="id",type="string"),
     *     @SWG\Parameter(in="path", name="height",type="integer"),
     *     @SWG\Response(
     *         response = 200,
     *         description = "File"
     *     ),
     * ),
     * @SWG\Get(path="/file/{id}/{width}x{height}",
     *     tags={"file"},
     *     summary="Retrieves the file.",
     *     @SWG\Parameter(in="path", name="id",type="string"),
     *     @SWG\Parameter(in="path", name="width",type="integer"),
     *     @SWG\Parameter(in="path", name="height",type="integer"),
     *     @SWG\Response(
     *         response = 200,
     *         description = "File"
     *     ),
     * ),
     */
    public function actionView($id, $width = null, $height = null)
    {
        $model = $this->findModel($id);
        if ($width || $height) {
            $filename = $model->thumbnail($width, $height);
            return Yii::$app->response->sendFile($filename, $model->name, [
                    'inline' => true,
            ]);
        }

        $content = is_resource($model->content) ? stream_get_contents($model->content) : $model->content;
        return Yii::$app->response->sendContentAsFile($content, $model->name, [
                'inline' => true,
                'mimeType' => $model->type,
        ]);
    }

    /**
     * @SWG\Post(path="/file",
     *     tags={"file"},
     *     consumes={"multipart/form-data"},
     *     description="",
     *     operationId="uploadFile",
     *     @SWG\Parameter(in="query",name="type",type="string",description="Type file(image)"),
     *     @SWG\Parameter(in="formData",name="file",type="file",required=true,description="file to upload"),
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response="200",
     *         description="successful operation",
     *         @SWG\Schema(type="object")
     *     ),
     *     summary="uploads a file"
     * )
     *
     * @param string $type
     * @return File
     */
    public function actionUpload($type = null)
    {
        $this->response->format = 'json';
        Yii::$app->getRequest()->getBodyParams();
        $file = UploadedFile::getInstanceByName('file');
        $model = File::store($file, $type);
        if ($model->hasErrors()) {
            $this->response->setStatusCode(422, 'Data Validation Failed.');
            return $model->firstErrors;
        }
        return $model->toArray();
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->created_by != Yii::$app->user->id) {
            throw new ForbiddenHttpException();
        }

        if ($model->delete() === false) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }
        Yii::$app->getResponse()->setStatusCode(204);
    }

    /**
     *
     * @param string $id
     * @return File
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if ($model = File::findOne($id)) {
            return $model;
        }
        throw new NotFoundHttpException();
    }

    protected function verbs()
    {
        return [
            'view' => ['GET', 'HEAD'],
            'upload' => ['POST'],
            'update' => ['POST'],
            'delete' => ['DELETE'],
        ];
    }

    protected function accessControls()
    {
        $access = [
//            'upload' => ['@'],
//            'delete' => ['@'],
        ];
        if (!env('GUEST_ACCESS_FILE')) {
            $access['view'] = ['@'];
        }
        return $access;
    }
}
