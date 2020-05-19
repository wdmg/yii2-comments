<?php

namespace wdmg\comments;

/**
 * Yii2 Comments
 *
 * @category        Module
 * @version         0.0.11
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-comments
 * @copyright       Copyright (c) 2019 - 2020 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use wdmg\base\BaseModule;

/**
 * comments module definition class
 */
class Module extends BaseModule
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'wdmg\comments\controllers';

    /**
     * {@inheritdoc}
     */
    public $defaultRoute = 'comments/index';

    /**
     * @var string, the name of module
     */
    public $name = "Comments";

    /**
     * @var string, the description of module
     */
    public $description = "Tree comments system";

    public $defaultController = "comments/default";

    /**
     * @var string or array, the default routes to rendered page (use "/" - for root)
     */
    public $baseRoute = "/comments";

    public $defaultListView = '@vendor/wdmg/yii2-comments/widgets/views/_list';
    //public $defaultListView = '_list';
    public $defaultFormView = '@vendor/wdmg/yii2-comments/widgets/views/_form';
    //public $defaultFormView = '_form';

    public $editCommentTimeout = 300; // (5 min.)

    public $deleteCommentTimeout = 3600; // (1 hour)

    /**
     * @var string the module version
     */
    private $version = "0.0.11";

    /**
     * @var integer, priority of initialization
     */
    private $priority = 8;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Set version of current module
        $this->setVersion($this->version);

        // Set priority of current module
        $this->setPriority($this->priority);

    }

    /**
     * {@inheritdoc}
     */
    public function dashboardNavItems($createLink = false)
    {
        $items = [
            'label' => $this->name,
            'url' => [$this->routePrefix . '/'. $this->id],
            'icon' => 'fa fa-fw fa-comments',
            'active' => in_array(\Yii::$app->controller->module->id, [$this->id])
        ];
        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        parent::bootstrap($app);

        // Configure comments component
        $app->setComponents([
            'comments' => [
                'class' => \wdmg\comments\components\Comments::class
            ]
        ]);


        /*if (!$this->isBackend() && !is_null($this->defaultController)) {

            // Get language scheme if available
            $custom = false;
            $hide = false;
            $scheme = null;
            if (isset(Yii::$app->translations)) {
                $custom = true;
                $hide = Yii::$app->translations->module->hideDefaultLang;
                $scheme = Yii::$app->translations->module->languageScheme;
            }

            // Add routes for frontend
            switch ($scheme) {
                case "after":

                    $app->getUrlManager()->addRules([
                        $this->baseRoute . '/<action:[\w-]+>/<lang:\w+>' => $this->defaultController . '/<action>',
                        $this->baseRoute . '/<lang:\w+>' => $this->defaultController . '/index',
                    ], true);

                    if ($hide) {
                        $app->getUrlManager()->addRules([
                            $this->baseRoute . '/<action:[\w-]+>' => $this->defaultController . '/<action>',
                            $this->baseRoute => $this->defaultController . '/index',
                        ], true);
                    }

                    break;

                case "query":

                    $app->getUrlManager()->addRules([
                        $this->baseRoute . '/<action:[\w-]+>' => $this->defaultController . '/<action>',
                        $this->baseRoute => $this->defaultController . '/index',
                    ], true);

                    break;

                case "subdomain":

                    if ($host = $app->getRequest()->getHostName()) {
                        $app->getUrlManager()->addRules([
                            'http(s)?://' . $host. '/' . $this->baseRoute . '/<action:[\w-]+>' => $this->defaultController . '/<action>',
                            'http(s)?://' . $host. '/' . $this->baseRoute => $this->defaultController . '/index',
                        ], true);
                    }

                    break;

                default:

                    $app->getUrlManager()->addRules([
                        '/<lang:\w+>' . $this->baseRoute . '/<alias:[\w-]+>' => $this->defaultController . '/view',
                        '/<lang:\w+>' . $this->baseRoute => $this->defaultController . '/index',
                    ], true);

                    if ($hide || !$custom) {
                        $app->getUrlManager()->addRules([
                            $this->baseRoute . '/<alias:[\w-]+>' => $this->defaultController . '/view',
                            $this->baseRoute => $this->defaultController . '/index',
                        ], true);
                    }

                    break;
            }
        }*/
    }
}