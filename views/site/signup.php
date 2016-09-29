<?php 
use app\assets\AppAsset; 
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/signup.js',['depends' => [AppAsset::className()]]); 
?>

   <style type="text/css">
        /* Styling of global error indicator */
        .form-error-state {
            font-size: 11px;
            padding-left: 20px;
            height: 16px;
            line-height: 18px;
            background-repeat: no-repeat;
            background-position: 0 0;
            cursor: default;
        }

        /* Error details tooltip */
        .errors-tip .error {
            font-style: italic;
        }
    </style> 
    <div width="100%"  id="form-div"></div>