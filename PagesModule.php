<?php

namespace Iliich246\YicmsPages;

use Iliich246\YicmsCommon\Base\AbstractConfigurableModule;

/**
 * Class PagesModule
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class PagesModule extends AbstractConfigurableModule
{
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
    public function getModuleName()
    {
        return 'Pages';
    }
}