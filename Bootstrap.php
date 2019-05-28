<?php

namespace wdmg\comments;

/**
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @copyright       Copyright (c) 2019 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 */

use yii\base\BootstrapInterface;
use Yii;


class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        // Get the module instance
        $module = Yii::$app->getModule('comments');

        // Get URL path prefix if exist
        if (isset($module->routePrefix)) {
            $app->getUrlManager()->enableStrictParsing = true;
            $prefix = $module->routePrefix . '/';
        } else {
            $prefix = '';
        }

        // Add module URL rules
        $app->getUrlManager()->addRules(
            [
                $prefix . '<module:comments>/' => '<module>/comments/index',
                $prefix . '<module:comments>/<controller:\w+>/' => '<module>/<controller>',
                $prefix . '<module:comments>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
                [
                    'pattern' => $prefix . '<module:comments>/',
                    'route' => '<module>/comments/index',
                    'suffix' => '',
                ], [
                'pattern' => $prefix . '<module:comments>/<controller:\w+>/',
                'route' => '<module>/<controller>',
                'suffix' => '',
            ], [
                'pattern' => $prefix . '<module:comments>/<controller:\w+>/<action:\w+>',
                'route' => '<module>/<controller>/<action>',
                'suffix' => '',
            ],
            ],
            true
        );
    }
}