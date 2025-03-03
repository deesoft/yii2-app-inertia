<?php

namespace app\controllers\admin;

use app\classes\Controller;
use app\models\AuthItem;
use dee\inertia\Inertia;
use Yii;
use yii\data\ActiveDataProvider;
use yii\rbac\Item;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 *
 * RoleController implements the CRUD actions for AuthItem model.
 */
class RoleController extends Controller
{

    /**
     * @inheritDoc
     */
    protected function verbs()
    {
        return [
            'delete' => ['POST'],
            'create' => ['POST'],
            'update' => ['POST'],
            'assign' => ['POST'],
            'revoke' => ['POST'],
        ];
    }

    /**
     * List of AuthItem 
     * @return string|Response
     */
    public function actionIndex()
    {
        $query = AuthItem::find();
        $request = Yii::$app->getRequest();
        $query->andFilterWhere([
            'type' => $request->get('type'),
        ])->andWhere("[[name]] NOT LIKE '/%'");

        $query->andFilterWhere(['ilike', 'name', $request->get('name')])
            ->andFilterWhere(['ilike', 'description', $request->get('description')]);

        if ($q = $request->get('q')) {
            $query->andWhere([
                'OR',
                ['ilike', 'name', $q],
                ['ilike', 'description', $q],
            ]);
        }

        $sortAttrs = [
            'name',
            'type',
            'description',
        ];
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => $sortAttrs,
            ]
        ]);
        return Inertia::render('admin/role/index', [
                'data' => $dataProvider
        ]);
    }

    protected function getData($name)
    {
        $items = AuthItem::find()->alias('i')
            ->select(['i.name', 'i.type', 'c.parent'])
            ->leftJoin('auth_item_child c', ['AND', 'c.child=i.name', ['c.parent' => $name]])
            ->orderBy(['i.name' => SORT_ASC])
            ->andWhere(['<>', 'i.name', $name])
            ->asArray()
            ->all();
        $template = [
            ['label' => 'Role', 'items' => []],
            ['label' => 'Permission', 'items' => []],
            ['label' => 'Route', 'items' => []],
        ];
        $result = [
            'available' => $template,
            'assigned' => $template,
        ];
        foreach ($items as $row) {
            $level = $row['type'] == 1 ? 0 : (strncmp($row['name'], '/', 1) === 0 ? 2 : 1);
            if ($row['parent']) {
                $result['assigned'][$level]['items'][] = ['id' => $row['name'], 'label' => $row['name']];
            } else {
                $result['available'][$level]['items'][] = ['id' => $row['name'], 'label' => $row['name']];
            }
        }
        return $result;
    }

    /**
     * Displays a single AuthItem model.
     * @param string $name Name
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionData($name)
    {
        return $this->asJson($this->getData($name));
    }

    /**
     * Creates a new AuthItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new AuthItem();
        $this->response->format = 'json';
        $model->load($this->request->post(), '');
        if ($model->save()) {
            return true;
        }
        $this->response->setStatusCode(422, 'Data Validation Failed.');
        return $model->firstErrors;
    }

    /**
     * Updates an existing AuthItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $name Name
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($name)
    {
        $model = $this->findModel($name);
        $this->response->format = 'json';
        $model->load($this->request->post(), '');
        if ($model->save()) {
            return true;
        }
        $this->response->setStatusCode(422, 'Data Validation Failed.');
        return $model->firstErrors;
    }

    public function actionAssign($name)
    {
        $items = $this->request->post('items', []);
        if ($items) {
            $manager = Yii::$app->authManager;
            /** @var Item $parent */
            $parent = $manager->getRole($name) ?: $manager->getPermission($name);
            $isRole = $parent->type == Item::TYPE_ROLE;
            foreach ($items as $item) {
                try {
                    $child = $manager->getPermission($item);
                    if (!$child && $isRole) {
                        $child = $manager->getRole($item);
                    }
                    if ($child) {
                        $manager->addChild($parent, $child);
                    }
                } catch (\Exception $exc) {

                }
            }
        }
        return $this->asJson($this->getData($name));
    }

    public function actionRevoke($name)
    {
        $items = $this->request->post('items', []);
        if ($items) {
            $manager = Yii::$app->authManager;
            /** @var Item $parent */
            $parent = $manager->getRole($name) ?: $manager->getPermission($name);
            $isRole = $parent->type == Item::TYPE_ROLE;
            foreach ($items as $item) {
                try {
                    $child = $manager->getPermission($item);
                    if (!$child && $isRole) {
                        $child = $manager->getRole($item);
                    }
                    if ($child) {
                        $manager->removeChild($parent, $child);
                    }
                } catch (\Exception $exc) {

                }
            }
        }
        return $this->asJson($this->getData($name));
    }

    /**
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $name Name
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($name)
    {
        $this->findModel($name)->delete();

        return $this->asJson(true);
    }

    /**
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $name Name
     * @return AuthItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($name)
    {
        if (($model = AuthItem::findOne(['name' => $name])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
