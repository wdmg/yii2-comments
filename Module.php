<?php

namespace wdmg\comments;

/**
 * Yii2 Comments
 *
 * @category        Module
 * @version         1.0.3
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-comments
 * @copyright       Copyright (c) 2019 - 2021 W.D.M.Group, Ukraine
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

    /**
     * The default frontend controller
     *
     * @var string
     */
    public $defaultController = "admin/comments/default";

    /**
     * The default routes to default controller in frontend.
     *
     * @var string or array, use "/" - for root
     */
    public $baseRoute = "/comments";

    /**
     * Default layout for listing comments in frontend.
     *
     * @var string
     */
    public $defaultListView = '@vendor/wdmg/yii2-comments/widgets/views/_list';

    /**
     * Default layout for outputting the form for adding / editing comments to the frontend.
     *
     * @var string
     */
    public $defaultFormView = '@vendor/wdmg/yii2-comments/widgets/views/_form';

    /**
     * The time during which the comment is available for editing by its author.
     *
     * @var int, in seconds. Use `0` for disable option.
     */
    public $editCommentTimeout = 300; // (5 min.)

    /**
     * The time during which the comment is available for removal by the author.
     *
     * @var int, in seconds. Use `0` for disable option.
     */
    public $deleteCommentTimeout = 3600; // (1 hour)

    /**
     * Moderate all new comments.
     *
     * @var bool, if `true` - all new comments be marked as awaiting moderation status.
     */
    public $newCommentsModeration = true;

    /**
     * Auto approve all new comments from registered users.
     *
     * @var bool, if `true` - all new comments from registered users be marked as published
     */
    public $approveFromRegistered = true;

    /**
     * @var string the module version
     */
    private $version = "1.0.3";

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
    public function dashboardNavItems($options = false)
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

        // UrlManager rules for frontend
        if (!$this->isBackend()) {
            $app->getUrlManager()->addRules([
                [
                    'pattern' => (($this->baseRoute) ?  $this->baseRoute : '/comments') . '/<action>',
                    'route' => (($this->defaultController) ?  $this->defaultController : 'comments/default') . '/<action>'
                ]
            ], true);
        }

    }
}