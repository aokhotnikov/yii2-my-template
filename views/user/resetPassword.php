<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model app\models\ResetPasswordForm */
/* @var $form ActiveForm */
?>
<div class="main-resetPassword col-md-6 col-md-offset-3">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Изменить', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>