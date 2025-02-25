<?php

namespace app\controllers;

use app\helpers\Access;
use app\helpers\Route;
use stdClass;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * 
 *
 */
class AuthController extends Controller
{

    public function actionUserInfo()
    {
        Route::refreshRoute();
        $identity = Yii::$app->user->identity;

        if ($identity) {
            $branchs = (new Query())
                ->select(['b.id', 'b.code', 'b.name', 'title' => "concat(b.code,' - ', b.name)"])
                ->from('branch b')
                ->innerJoin('user_branch ub', 'ub.branch_id=b.id')
                ->where(['ub.user_id' => $identity->id])
                ->all();
        } else {
            $branchs = [];
        }
        $bIds = ArrayHelper::getColumn($branchs, 'id');
        $warehouses = [];
        $whs = (new Query())
            ->select(['w.id', 'w.code', 'w.name', 'title' => "concat(w.code,' - ', w.name)", 'w.branch_id'])
            ->from('warehouse w')
            ->where(['w.branch_id' => $bIds])
            ->all();
        foreach ($whs as $row) {
            $warehouses[$row['branch_id']][] = $row;
        }

        return $this->asJson([
                'isLoged' => !!$identity,
                'auth' => $identity ?: new stdClass(),
                'menus' => Access::getAssignedMenu(),
                'branches' => $branchs,
                'warehouses' => $warehouses ?: new stdClass(),
        ]);
    }
}
