<?php

namespace Iliich246\YicmsPages;

use Iliich246\YicmsCommon\Base\AbstractConfigurableModule;
use Iliich246\YicmsCommon\Base\YicmsModuleInterface;

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
