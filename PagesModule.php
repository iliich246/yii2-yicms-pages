<?php

namespace Iliich246\YicmsPages;

use Yii;
use Iliich246\YicmsCommon\Base\AbstractConfigurableModule;
use Iliich246\YicmsCommon\Base\YicmsModuleInterface;
use Iliich246\YicmsCommon\CommonModule;

/**
 * Class PagesModule
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class PagesModule extends AbstractConfigurableModule implements YicmsModuleInterface
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
     * @inherited
     */
    public function getNameSpace()
    {
        return __NAMESPACE__;
    }

    /**
     * @inherited
     */
    public static function getModuleName()
    {
        return 'Pages';
    }
}
