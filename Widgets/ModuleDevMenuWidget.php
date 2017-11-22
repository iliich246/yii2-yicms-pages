<?php

namespace Iliich246\YicmsPages\Widgets;

use Yii;
use Iliich246\YicmsPages\PagesModule;
use Iliich246\YicmsCommon\Base\AbstractModuleMenuWidget;

/**
 * Class ModuleDevMenuWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ModuleDevMenuWidget extends AbstractModuleMenuWidget
{
    /**
     * @inheritdoc
     */
    public static function getModuleName()
    {
        return strtolower(PagesModule::getModuleName());
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('module_dev_menu', [
            'widget' => $this,
        ]);
    }
}
