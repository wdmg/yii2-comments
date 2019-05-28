[![Progress](https://img.shields.io/badge/required-Yii2_v2.0.13-blue.svg)](https://packagist.org/packages/yiisoft/yii2)
[![Github all releases](https://img.shields.io/github/downloads/wdmg/yii2-comments/total.svg)](https://GitHub.com/wdmg/yii2-comments/releases/)
[![GitHub version](https://badge.fury.io/gh/wdmg/yii2-comments.svg)](https://github.com/wdmg/yii2-comments)
![Progress](https://img.shields.io/badge/progress-in_development-red.svg)
[![GitHub license](https://img.shields.io/github/license/wdmg/yii2-comments.svg)](https://github.com/wdmg/yii2-comments/blob/master/LICENSE)

# Yii2 Comments Module
Tree comments system for Yii2

# Requirements 
* PHP 5.6 or higher
* Yii2 v.2.0.13 and newest
* [Yii2 Users](https://github.com/wdmg/yii2-users) module (optionaly)

# Installation
To install the module, run the following command in the console:

`$ composer require "wdmg/yii2-comments"`

After configure db connection, run the following command in the console:

`$ php yii comments/init`

And select the operation you want to perform:
  1) Apply all module migrations
  2) Revert all module migrations

# Migrations
In any case, you can execute the migration and create the initial data, run the following command in the console:

`$ php yii migrate --migrationPath=@vendor/wdmg/yii2-comments/migrations`

# Configure
To add a module to the project, add the following data in your configuration file:

    'modules' => [
        ...
        'comments' => [
            'class' => 'wdmg\comments\Module',
            'routePrefix' => 'admin'
        ],
        ...
    ],

If you have connected the module not via a composer add Bootstrap section:

`
$config['bootstrap'][] = 'wdmg\comments\Bootstrap';
`

# Routing
Use the `Module::dashboardNavItems()` method of the module to generate a navigation items list for NavBar, like this:

    <?php
        echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
            'label' => 'Modules',
            'items' => [
                Yii::$app->getModule('comments')->dashboardNavItems(),
                ...
            ]
        ]);
    ?>

# Status and version [in progress development]
* v.0.0.3 - Added module name and description, translations. Routing fix.
* v.0.0.2 - Added base model, CRUD
* v.0.0.1 - Added base migrations