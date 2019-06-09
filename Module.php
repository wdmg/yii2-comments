<?php

namespace wdmg\comments;

/**
 * Yii2 Comments
 *
 * @category        Module
 * @version         0.0.8
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-comments
 * @copyright       Copyright (c) 2019 W.D.M.Group, Ukraine
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
     * @var string the module version
     */
    private $version = "0.0.8";

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
    public function bootstrap($app)
    {
        parent::bootstrap($app);
    }
}