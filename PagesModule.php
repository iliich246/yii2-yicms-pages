<?php

namespace Iliich246\YicmsPages;

use Yii;
use yii\base\BootstrapInterface;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\YicmsModuleInterface;
use Iliich246\YicmsCommon\Base\Generator;
use Iliich246\YicmsCommon\Base\AbstractConfigurableModule;
use Iliich246\YicmsPages\Base\PagesConfigDb;

/**
 * Class PagesModule
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class PagesModule extends AbstractConfigurableModule implements
    BootstrapInterface,
    YicmsModuleInterface
{
    /** @var bool keeps true if for this module was generated changeable admin files */
    public $isGenerated = false;
    /** @var bool if true generator will be generate in strong mode, even existed files will be replaced */
    public $strongGenerating = false;

    /** @inheritdoc */
    public $configurable = [
        'isGenerated',
    ];

    /** @inheritdoc */
    public $controllerMap = [
        'dev' => 'Iliich246\YicmsPages\Controllers\DeveloperController'
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        Yii::setAlias('@yicms-pages', Yii::getAlias('@vendor') .
            DIRECTORY_SEPARATOR .
            'iliich246' .
            DIRECTORY_SEPARATOR .
            'yii2-yicms-pages');

        parent::init();

        $namespace = CommonModule::getInstance()->yicmsNamespace . '\Pages\Controllers\\';

        $this->controllerMap['admin'] = $namespace . 'AdminController';
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
    public function isGenerated()
    {
        return !!$this->isGenerated;
    }

    /**
     * @inherited
     */
    public function setAsGenerated()
    {
        $config = PagesConfigDb::getInstance();
        $config->isGenerated = true;

        $config->save(false);
    }

    /**
     * @inherited
     */
    public function isGeneratorInStrongMode()
    {
        return !!$this->strongGenerating;
    }

    /**
     * @inherited
     */
    public static function getModuleName()
    {
        return 'Pages';
    }
}
