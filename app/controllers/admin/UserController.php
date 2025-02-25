<?php

namespace app\controllers\admin;

use app\classes\Controller;
use app\models\AuthItem;
use app\models\form\UserSignup;
use app\models\User;
use dee\inertia\Inertia;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 *
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{

    /**
     * @inheritDoc
     */
    protected function verbs()
    {
        return [
            'create' => ['POST'],
            'update' => ['POST'],
            'assign' => ['POST'],
            'revoke' => ['POST'],
            'delete' => ['POST'],
        ];
    }

    /**
     * List of User 
     * @return string|Response
     */
    public function actionIndex()
    {
        $query = User::find();
        $request = Yii::$app->getRequest();
        $query->andFilterWhere([
            'id' => $request->get('id'),
            'active' => $request->get('active'),
        ]);

        $query->andFilterWhere(['ilike', 'username', $request->get('username')])
            ->andFilterWhere(['ilike', 'email', $request->get('email')])
            ->andFilterWhere(['ilike', 'phone', $request->get('phone')])
            ->andFilterWhere(['ilike', 'fullname', $request->get('fullname')]);

        if ($q = $request->get('q')) {
            $query->andWhere([
                'OR',
                ['ilike', 'username', $q],
                ['ilike', 'email', $q],
                ['ilike', 'phone', $q],
                ['ilike', 'fullname', $q],
            ]);
        }

        $sortAttrs = [
            'id',
            'username',
            'email',
            'phone',
            'fullname',
            'active'
        ];
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => $sortAttrs,
            ]
        ]);
        return Inertia::render('admin/user/index', [
                'data' => $dataProvider
        ]);
    }

    protected function getData($id)
    {
        $items = AuthItem::find()->alias('i')
            ->select(['i.name', 'i.type', 'a.user_id'])
            ->leftJoin('auth_assignment a', ['AND', 'a.item_name=i.name', ['a.user_id' => $id]])
            ->where("[[i.name]] NOT LIKE '/%'")
            ->orderBy(['i.name' => SORT_ASC])
            ->asArray()
            ->all();
        $template = [
            ['label' => 'Role', 'items' => []],
            ['label' => 'Permission', 'items' => []],
        ];
        $result = [
            'available' => $template,
            'assigned' => $template,
        ];
        foreach ($items as $row) {
            $level = $row['type'] - 1;
            if ($row['user_id']) {
                $result['assigned'][$level]['items'][] = ['id' => $row['name'], 'label' => $row['name']];
            } else {
                $result['available'][$level]['items'][] = ['id' => $row['name'], 'label' => $row['name']];
            }
        }
        return $result;
    }

    public function actionData($id)
    {
        return $this->asJson($this->getData($id));
    }

    public function actionAssign($id)
    {
        $items = $this->request->post('items', []);
        if ($items) {
            $manager = Yii::$app->authManager;
            foreach ($items as $item) {
                try {
                    $role = $manager->getRole($item) ?: $manager->getPermission($item);
                    if ($role) {
                        $manager->assign($role, $id);
                    }
                } catch (\Exception $exc) {

                }
            }
        }
        return $this->asJson($this->getData($id));
    }

    public function actionRevoke($id)
    {
        $items = $this->request->post('items', []);
        if ($items) {
            $manager = Yii::$app->authManager;
            foreach ($items as $item) {
                try {
                    $role = $manager->getRole($item) ?: $manager->getPermission($item);
                    if ($role) {
                        $manager->revoke($role, $id);
                    }
                } catch (\Exception $exc) {

                }
            }
        }
        return $this->asJson($this->getData($id));
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new UserSignup();
        $this->response->format = 'json';
        $model->load($this->request->post(), '');
        if ($model->signup()) {
            return true;
        }
        $this->response->setStatusCode(422, 'Data Validation Failed.');
        return $model->firstErrors;
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $this->response->format = 'json';
        $model->load($this->request->post(), '');
        if ($model->save()) {
            return true;
        }
        $this->response->setStatusCode(422, 'Data Validation Failed.');
        return $model->firstErrors;
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->asJson(true);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
