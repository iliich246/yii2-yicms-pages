<?php

namespace Iliich246\YicmsPages\Base;

use Iliich246\YicmsCommon\Base\AbstractTranslateForm;

/**
 * Class PageDevTranslatesForm
 *
 * @property PageNamesTranslateDb $currentTranslateDb
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class PageDevTranslatesForm extends AbstractTranslateForm
{
    /** @var string name of page in current model language */
    public $name;
    /** @var string description of page on current model language */
    public $description;
    /** @var Pages page db associated with this model */
    private $page;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name'        => 'Page name on language "' . $this->language->name . '"',
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
     * @inheritdoc
     */
    public static function getViewName()
    {
        return '@yicms-pages/Views/translates/page_name_translate';
    }

    /**
     * Sets page of model
     * @param Pages $page
     */
    public function setPage(Pages $page)
    {
        $this->page = $page;
    }

    /**
     * @inheritdoc
     */
    protected function isCorrectConfigured()
    {
        if (!parent::isCorrectConfigured() || !$this->page) return false;
        return true;
    }

    /**
     * Saves new data in data base
     * @return bool
     */
    public function save()
    {
        $this->currentTranslateDb->name        = $this->name;
        $this->currentTranslateDb->description = $this->description;

        return $this->currentTranslateDb->save();
    }

    /**
     * @inheritdoc
     */
    public function getCurrentTranslateDb()
    {
        if ($this->currentTranslateDb) return $this->currentTranslateDb;

        $this->currentTranslateDb = PageNamesTranslateDb::find()
            ->where([
                'common_language_id' => $this->language->id,
                'page_id'            => $this->page->id,
            ])
            ->one();

        if (!$this->currentTranslateDb)
            $this->createTranslateDb();
        else {
            $this->name = $this->currentTranslateDb->name;
            $this->description = $this->currentTranslateDb->description;
        }

        return $this->currentTranslateDb;
    }

    /**
     * @inheritdoc
     */
    protected function createTranslateDb()
    {
        $this->currentTranslateDb = new PageNamesTranslateDb();
        $this->currentTranslateDb->common_language_id = $this->language->id;
        $this->currentTranslateDb->page_id = $this->page->id;

        return $this->currentTranslateDb->save();
    }
}
