<?php
$this->title = Yii::$app->params['adminTitle'];

use common\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use common\helpers\Url;
?>

<body class="hold-transition login-page">
<ul class="layui-nav" lay-filter="" style="height: 60px;background-color:initial">

    <li class="layui-nav-item" style="float: right">
        <a href="javascript:;"><?=t("切换语言")?></a>
        <dl class="layui-nav-child"> <!-- 二级菜单 -->
            <dd><a href="<?= Url::to(['/site/language?language=zh-CN']); ?>" ><?=t("中文")?></a></dd>
            <dd><a href="<?= Url::to(['/site/language?language=vi']); ?>" onclick="$('body').click();"><?=t("越语")?></a></dd>
        </dl>
    </li>
</ul>

<script>
    //注意：导航 依赖 element 模块，否则无法进行功能性操作
    layui.use('element', function(){
        var element = layui.element;

        //…
    });
</script>
<div class="login-box">
    <div class="login-logo">
        <?= Html::encode(t(Yii::$app->params['adminTitle'])); ?>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg"><?=t('欢迎登录')?></p>
        <?php $form = ActiveForm::begin([
                'id' => 'login-form'
        ]); ?>
        <?= $form->field($model, 'username', [
            'template' => '<div class="form-group has-feedback">{input}<span class="glyphicon glyphicon-user form-control-feedback"></span></div>{hint}{error}'
        ])->textInput(['placeholder' => t('用户名')])->label(false); ?>
        <?= $form->field($model, 'password', [
            'template' => '<div class="form-group has-feedback">{input}<span class="glyphicon glyphicon-lock form-control-feedback"></span></div>{hint}{error}'
        ])->passwordInput(['placeholder' => t('密码')])->label(false); ?>
        <?php if ($model->scenario == 'captchaRequired') { ?>
            <?= $form->field($model,'verifyCode')->widget(Captcha::class,[
                'template' => '<div class="row"><div class="col-sm-7">{input}</div><div class="col-sm-5">{image}</div></div>',
                'imageOptions' => [
                    'alt' => t('点击换图'),
                    'title' => t('点击换图'),
                    'style' => 'cursor:pointer'
                ],
                'options' => [
                    'class' => 'form-control',
                    'placeholder' =>t( '验证码'),
                ],
            ])->label(false); ?>
        <?php } ?>
        <?= $form->field($model, 'rememberMe')->checkbox() ?>
        <div class="form-group">
            <?= Html::submitButton(t('立即登录'), ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>
        </div>
        <?php ActiveForm::end(); ?>
        <div class="social-auth-links text-center">
            <p><?= Html::encode(t(Yii::$app->debris->config('web_copyright'))); ?></p>
        </div>
    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
</body>