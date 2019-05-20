<?php

namespace Iliich246\YicmsPages\Base;

use Iliich246\YicmsCommon\Base\AbstractModuleConfiguratorDb;

/**
 * Class PagesConfigDb
 *
 * @property integer $isGenerated
 * @property integer $strongGenerating
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['isGenerated', 'strongGenerating'], 'boolean'],
        ];
    }
}
