<?php

namespace Iliich246\YicmsPages\Base;

use Iliich246\YicmsCommon\Base\AbstractTranslate;
use Iliich246\YicmsPages\Base\Pages;
/**
 * Class PageTranslate
 *
 * Translates class for pages
 *
 * @property integer $id
 * @property integer $page_id
 * @property integer $common_language_id
 * @property string $name
 * @property string $description
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class PageTranslate extends AbstractTranslate
{
    /** @var Pages page associated with this model */
    private $page;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pages_names_translates}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pageName' => 'Page name on language "' . $this->language->name . '"',
            'description' => 'Description of page on language "' . $this->language->name . '"',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'string', 'max' => '50', 'tooLong' => 'Name of page must be less than 50 symbols'],
            ['description', 'string'],
        ];
    }

    /**
     * Sets page of model
     * @param Pages $page
     */
    public function setPage(Pages $page)
    {
        $this->page = $page;
    }

}
