<?php

namespace Iliich246\YicmsPages\Base;

use Iliich246\YicmsCommon\Base\AbstractModuleConfiguratorDb;

/**
 * Class PagesConfigDb
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class PagesConfigDb extends AbstractModuleConfiguratorDb
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pages_config}}';
    }
}
