<?php

/* @var $this yii\web\View */

use common\helpers\Html;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>This is the About page. You may modify the following file to customize its content:<?=Yii::t('app', '注册')?></p>

    <code><?= __FILE__ ?></code>
</div>
