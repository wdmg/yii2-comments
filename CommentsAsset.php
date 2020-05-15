<?php

namespace wdmg\comments;
use yii\web\AssetBundle;

class CommentsAsset extends AssetBundle
{
    public $sourcePath = '@vendor/wdmg/yii2-comments/assets';

    public $publishOptions = [
        'forceCopy' => true
    ];

    public $css = [
        'css/comments.css',
    ];

    public $js = [
        'js/comments.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public function init()
    {
        parent::init();
    }
}

?>