<?php

namespace wdmg\comments;

use yii\base\BootstrapInterface;
use Yii;


class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        // Get the module instance
        $module = Yii::$app->getModule('comments');

        // Get URL path prefix if exist
        $prefix = (isset($module->routePrefix) ? $module->routePrefix . '/' : '');

        // Add module URL rules
        $app->getUrlManager()->addRules(
            [
                $prefix . '<module:comments>/' => '<module>/default/index',
                $prefix . '<module:comments>/<controller>/' => '<module>/<controller>',
                $prefix . '<module:comments>/<controller>/<action>' => '<module>/<controller>/<action>',
                $prefix . '<module:comments>/<controller>/<action>' => '<module>/<controller>/<action>',
            ],
            true
        );
    }
}