<?php

use ivanleshh\blog\models\Blog;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var common\models\BlogSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Blogs';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .grid-view td > img {
        max-width: 50px;
    }
</style>
<div class="blog-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Blog', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'title',
            ['attribute' => 'url', 'format' => 'raw'],
            ['attribute' => 'status_id', 'filter' => Blog::STATUS_LIST,'value' => 'statusName'],
            'sort',
            'smallimage:image',
            'date_update:datetime',
            'date_create:datetime',
            ['attribute' => 'tags', 'value' => 'tagsAsString'],
//            [
//                'class' => ActionColumn::className(),
//                'urlCreator' => function ($action, Blog $model, $key, $index, $column) {
//                    return Url::toRoute([$action, 'id' => $model->id]);
//                 }
//            ],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete} {check}',
                'buttons' => [
                    'check' => function ($url, $model, $key) {
                        return Html::a('<i class="fa fa-check" aria-hidden="true"></i>', $url);
                    },
                ],
                'visibleButtons' => [
                    'check' => function ($model, $key, $index) {
                        return $model->status_id === 1;
                    }
                ]
            ],
        ],

    ]); ?>

    <?php Pjax::end(); ?>

</div>
