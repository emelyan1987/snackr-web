<?php

/* @var $this \yii\web\View */
/* @var $content string */    

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar; 
use app\assets\AppAsset; 
?>


<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode("Snackr Admin Page") ?></title>
    <?php $this->head() ?>   
</head>
<body>
<?php $this->beginBody() ?>

<div id="header-div">
    <?php
    NavBar::begin([
        'brandLabel' => 'Snackr Administration',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => 'Home', 'url' => ['/site/index']],
            ['label' => 'Restaurant', 'url' => ['/rest/index']],
            ['label' => 'User', 'url' => ['/user/index']],
            ['label' => 'Contact', 'url' => ['/site/contact']],
            Yii::$app->user->isGuest ?
                ['label' => 'Login', 'url' => ['/site/login']] :
                [
                    'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post']
                ],
        ],
    ]);
    NavBar::end();
    ?>
</div>


<div id="footer-div">
<div class="container">
        <p class="pull-left">&copy; Snackr app <?= date('Y') ?></p>

        <p class="pull-right">Powered by MichelTeam</p>
    </div>
</div>
<?= $content ?> 
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
