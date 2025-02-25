<?php

namespace app\classes;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller as WebController;

/**
 * Description of Controller
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Controller extends WebController
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {

        $behaviors = [];
        $verbs = $this->verbs();
        if (count($verbs)) {
            $behaviors['verbFilter'] = [
                'class' => VerbFilter::class,
                'actions' => $verbs,
            ];
        }

        $accessControls = $this->accessControls();
        if (count($accessControls)) {
            $only = array_keys($accessControls);
            $rules = [];
            foreach ($accessControls as $action => $roles) {
                $rules[] = [
                    'allow' => true,
                    'actions' => [$action],
                    'roles' => (array) $roles,
                ];
            }
            $behaviors['accessControl'] = [
                'class' => AccessControl::class,
                'only' => $only,
                'rules' => $rules,
            ];
        }
        return $behaviors;
    }

    public function beforeAction($action)
    {
        $request = $this->request;
        if (($request->getIsGet() || $request->getIsHead()) && ($bodyParams = $request->getBodyParams())) {
            $request->queryParams = $request->queryParams + $bodyParams;
        }
        return parent::beforeAction($action);
    }

    /**
     * Declares the allowed HTTP verbs.
     * Please refer to [[VerbFilter::actions]] on how to declare the allowed verbs.
     * @return array the allowed HTTP verbs.
     */
    protected function verbs()
    {
        return [];
    }

    /**
     *
     * @return array
     */
    protected function accessControls()
    {
        return [];
    }
}
