<?php

namespace app\yicms\Pages\Widgets;

use Yii;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\AbstractModuleMenuWidget;
use Iliich246\YicmsPages\Base\Pages;

/**
 * Class ModuleMenuWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ModuleMenuWidget extends AbstractModuleMenuWidget
{
    /**
     * @inheritdoc
     */
    public static function getModuleName()
    {
        return strtolower(CommonModule::getModuleName());
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->route = Yii::$app->controller->action->getUniqueId();

        $pagesQuery = Pages::find()->orderBy(['pages_order' => SORT_ASC]);

        if (!CommonModule::isUnderDev())
            $pagesQuery->where([
                'editable' => true,
            ]);

        $pages = $pagesQuery->all();

        return $this->render('module_menu', [
            'widget' => $this,
            'pages'  => $pages,
        ]);
    }

    /**
     * Return true, if for this page element of menu must be active
     * @param Pages $page
     * @return bool
     */
    public function isActive(Pages $page)
    {
        if ($this->route == 'pages/admin/edit-page' && Yii::$app->request->get('id') == $page->id) return true;
        return false;
    }
}