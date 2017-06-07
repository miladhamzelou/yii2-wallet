<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

/*
 * @var $this  yii\web\View
 * @var $form  yii\widgets\ActiveForm
 * @var $model yuncms\user\models\SettingsForm
 */
$this->title = Yii::t('wallet', 'Wallet Manage');
$this->params['breadcrumbs'][] = $this->title;
?>

    <div class="row">
        <div class="col-md-2">
            <?= $this->render('@yuncms/user/views/_profile_menu') ?>
        </div>
        <div class="col-md-10">
            <h2 class="h3 profile-title">
                <?= Yii::t('wallet', 'Wallets') ?>
                <div class="pull-right">
                    <a class="btn btn-primary"
                       href="<?= Url::to(['/wallet/wallet/index']); ?>"><?= Yii::t('wallet', 'Wallet'); ?></a>
                </div>
            </h2>
            <div class="row">
                <div class="col-md-12">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'layout' => "{items}\n{pager}",
                        'columns' => [
                            [
                                'header' => Yii::t('wallet', 'Currency'),
                                'value' => function ($model) {
                                    return Html::a($model->currency, ['/wallet/withdrawals/index', 'currency' => $model->currency]);
                                },
                                'format' => 'raw',
                            ],
                            'money',
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => Yii::t('wallet', 'Operation'),
                                'template' => '{recharge}',
                                'buttons' => [
                                    'recharge' =>
                                        function ($url, $model, $key) {
                                            return Html::a(Yii::t('wallet', 'Recharge'), ['/wallet/wallet/recharge', 'id' => $model->id]) .

                                                '<a href="#" onclick="jQuery(\'#payment-currency\').val(\'' . $model->currency . '\');" data-toggle="modal"
                                                 data-target="#recharge_modal">' . Yii::t('wallet', 'Recharge') . '</a>   ' .
                                                Html::a(Yii::t('wallet', 'Withdrawals'), Url::to(['/wallet/withdrawals/create', 'currency' => $model->currency]));
                                        }]],
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal
    ================================================== -->
<?php
if (Yii::$app->hasModule('payment')):
    $payment = new \yuncms\payment\models\Payment();
    $form = ActiveForm::begin([
        'action' => Url::toRoute(['/wallet/wallet/recharge']),
    ]); ?>
    <?= Html::activeInput('hidden', $payment, 'currency', ['value' => '']) ?>
    <?= Html::activeInput('hidden', $payment, 'pay_type', ['value' => \yuncms\payment\models\Payment::TYPE_MWEB]) ?>
    <?php Modal::begin([
    'options' => ['id' => 'recharge_modal'],
    'header' => Yii::t('wallet', 'Recharge'),
    'footer' => Html::button(Yii::t('wallet', 'Clean'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) . Html::submitButton(Yii::t('wallet', 'Submit'), ['class' => 'btn btn-primary']),
]); ?>
    <?= $form->field($payment, 'money'); ?>
    <?= $form->field($payment, 'gateway')->inline(true)->radioList(ArrayHelper::map(Yii::$app->getModule('payment')->gateways, 'id', 'title')); ?>
    <?php
    Modal::end();
    ActiveForm::end();
endif;
?>