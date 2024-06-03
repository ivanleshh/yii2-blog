<?php

use vova07\imperavi\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\file\FileInput;
use ivanleshh\blog\models\Blog;
use ivanleshh\blog\models\Tag;

/** @var yii\web\View $this */
/** @var common\models\Blog $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="blog-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'text')->widget(Widget::className(), [
            'settings' => [
                'lang' => 'ru',
                'minHeight' => 200,
                'formatting' => ['p', 'blockquote', 'h2', 'h1'],
                'imageUpload' => \yii\helpers\Url::to(['site/save-redactor-img', 'sub' => 'blog']),
                'plugins' => [
                    'clips',
                    'fullscreen',
                ],
            ],
        ]);
    ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status_id')->dropDownList(Blog::STATUS_LIST) ?>

    <?= $form->field($model, 'sort')->textInput() ?>

    <?= $form->field($model, 'file')->widget(FileInput::classname(), [
    'options' => ['accept' => 'image/*'],
    ]); ?>

    <?= $form->field($model, 'tags_array')->widget(Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(Tag::find()->all(), 'id', 'name'),
        'language' => 'en',
        'options' => ['placeholder' => 'Выбрать tag ...', 'multiple' => true],
        'pluginOptions' => [
            'allowClear' => true,
            'tags' => true,
            'maximumInputLength' => 10
        ],
        ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?= FileInput::widget([
            'name' => 'ImageManager[attachment]',
        'options' => ['multiple' => true],
        'pluginOptions' => [
                'deleteUrl' => \yii\helpers\Url::to(['/blog/delete-image']),
                'initialPreview' => $model->imagesLinks,
                'initialPreviewAsData' => true,
                'overwriteInitial' => false,
                'initialPreviewConfig' => $model->imagesLinksData,
                'uploadUrl' => \yii\helpers\Url::to(['/site/save-img']),
                'uploadExtraData' => [
                        'ImageManager[class]' => $model->formName(),
                    'ImageManager[item_id]' => $model->id,
                ],
            'maxFileCount' => 10,
        ],
        'pluginEvents' => [
                'filesorted' => new JsExpression('function(event, params) {
                    $.post("' . \yii\helpers\Url::toRoute(["/blog/sort-image", "id" => $model->id]) . '", {sort: params});
                }')
        ],
    ]); ?>
</div>
