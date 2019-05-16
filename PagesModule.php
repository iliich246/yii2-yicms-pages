<?php

namespace Iliich246\YicmsPages;

use yii\base\BootstrapInterface;
use Iliich246\YicmsCommon\Annotations\AnnotateInterface;
use Iliich246\YicmsCommon\Base\Generator;
use Iliich246\YicmsCommon\Base\AbstractConfigurableModule;

/**
 * Class PagesModule
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class PagesModule extends AbstractConfigurableModule implements
    BootstrapInterface,
    AnnotateInterface
{
    /** @inheritdoc */
    public $controllerMap = [
        'dev' => 'Iliich246\YicmsPages\Controllers\DeveloperController'
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        //TODO: makes correct build of controller map via common->$yicmsLocation
        $this->controllerMap['admin'] = 'app\yicms\Pages\Controllers\AdminController';
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $generator = new Generator($this);
        $generator->generate();
    }

    /**
     * @inherited
     */
    public function getNameSpace()
    {
        return __NAMESPACE__;
    }

    /**
     * @inherited
     */
    public function getModuleDir()
    {
        return __DIR__;
    }

    /**
     * @inherited
     */
    public function isNeedGenerate()
    {
        return false;
    }

    /**
     * @inherited
     */
    public static function getModuleName()
    {
        return 'Pages';
    }

    /**
     * @inherited
     */
    public function annotate()
    {

    }
}
