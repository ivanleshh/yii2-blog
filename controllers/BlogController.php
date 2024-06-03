<?php

namespace ivanleshh\blog\controllers;

use ivanleshh\blog\models\Blog;
use ivanleshh\blog\models\BlogSearch;
use ivanleshh\blog\models\ImageManager;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * BlogController.php implements the CRUD actions for Blog model.
 */
class BlogController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                        'delete-image' => ['POST'],
                        'sort-image' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Blog models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BlogSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

//    public function actionFff()
//    {
//        for ($i = 0; $i < 30; $i++) {
//            $model = new Blog();
//            $model->title = 'Заголовок № ' . $i;
//            $model->sort = 50;
//            $model->status_id = 1;
//            $model->url = 'url_' . $i;
//            $model->save();
//        }
//        return 'success';
//    }

    /**
     * Displays a single Blog model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Blog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Blog();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Blog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

//        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Blog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionDeleteImage($id)
    {
        if ($model = ImageManager::findOne(Yii::$app->request->post('key')) && $model->delete()) {
            return true;
        } else {
            throw new NotFoundHttpException('Erroror');
        }
    }

    public function actionSortImage($id)
    {
        if (Yii::$app->request->getIsAjax()) {
            $post = Yii::$app->request->post();
            if ($post['oldIndex'] > $post['newIndex']) {
                $param = ['and', ['>=', 'sort', $post['newIndex']], ['<', 'sort', $post['oldIndex']]];
                $counter = 1;
            } else {
                $param = ['and', ['<=', 'sort', $post['newIndex']], ['>', 'sort', $post['oldIndex']]];
                $counter = -1;
            }
            ImageManager::updateAllCounters(['sort' => $counter], ['and', ['class' => 'blog', 'item_id' => $id], $param]);
            ImageManager::updateAll(['sort' => $post['newIndex']], ['id' => $post['stack'][$post['newIndex']]['key']]);
            return true;
        }
        throw new MethodNotAllowedHttpException();
    }

    /**
     * Finds the Blog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Blog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Blog::find()->with('tags')->andWhere(['id' => $id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
