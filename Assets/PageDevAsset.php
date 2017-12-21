<?php

namespace Iliich246\YicmsPages\Assets;

use yii\web\AssetBundle;

/**
 * Class PageDevAsset
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class PageDevAsset extends AssetBundle
{
    public $sourcePath = '@yicms-pages/Assets/pages-dev';

    public $js = [
        'pages-dev.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'yii\widgets\PjaxAsset',
        'Iliich246\YicmsCommon\Assets\BootboxAsset',
    ];
}
