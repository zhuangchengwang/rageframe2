<?php

use common\helpers\Url;
use common\helpers\Html;
use yii\grid\GridView;

$this->title = t('配置管理');
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="<?= Url::to(['config/index']) ?>"><?=t('配置管理')?></a></li>
                <li><a href="<?= Url::to(['config-cate/index']) ?>"> <?=t('配置分类')?></a></li>
                <li class="pull-right">
                    <?= Html::create(['ajax-edit'],t('创建'), [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                    ]) ?>
                </li>
            </ul>
            <div class="tab-content">
                <div class="active tab-pane">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        //重新定义分页样式
                        'tableOptions' => ['class' => 'table table-hover'],
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                            ],
                            'title',
                            'name',
                            [
                                'attribute' => 'sort',
                                'value' => function ($model) {
                                    return Html::sort($model->sort);
                                },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => t('类别'),
                                'attribute' => 'cate.title',
                                'filter' => Html::activeDropDownList($searchModel, 'cate_id', $cateDropDownList, [
                                        'prompt' =>t( '全部'),
                                        'class' => 'form-control'
                                    ]
                                ),
                            ],
                            [
                                'label' => t('属性'),
                                'attribute' => 'type',
                                'value' => function ($model, $key, $index, $column) {
                                    return Yii::$app->params['configTypeList'][$model->type];
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'type',
                                    Yii::$app->params['configTypeList'], [
                                        'prompt' =>t( '全部'),
                                        'class' => 'form-control'
                                    ]
                                ),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'header' => t("操作"),
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{edit} {status} {destroy}',
                                'buttons' => [
                                    'edit' => function ($url, $model, $key) {
                                        return Html::edit(['ajax-edit', 'id' => $model->id], t('编辑'), [
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModal',
                                        ]);
                                    },
                                    'status' => function ($url, $model, $key) {
                                        return Html::status($model->status);
                                    },
                                    'destroy' => function ($url, $model, $key) {
                                        return Html::delete(['delete', 'id' => $model->id]);
                                    },
                                ],
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>