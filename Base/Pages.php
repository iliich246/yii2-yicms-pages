<?php

namespace Iliich246\YicmsPages\Base;

use Yii;
use yii\db\ActiveRecord;
use Iliich246\YicmsCommon\Base\SortOrderTrait;
use Iliich246\YicmsCommon\Base\FictiveInterface;
use Iliich246\YicmsCommon\Base\SortOrderInterface;
use Iliich246\YicmsCommon\Languages\Language;
use Iliich246\YicmsCommon\Languages\LanguagesDb;
use Iliich246\YicmsCommon\Fields\Field;
use Iliich246\YicmsCommon\Fields\FieldsHandler;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\FieldsInterface;
use Iliich246\YicmsCommon\Fields\FieldReferenceInterface;
use Iliich246\YicmsCommon\Files\File;
use Iliich246\YicmsCommon\Files\FilesBlock;
use Iliich246\YicmsCommon\Files\FilesHandler;
use Iliich246\YicmsCommon\Files\FilesInterface;
use Iliich246\YicmsCommon\Files\FilesReferenceInterface;
use Iliich246\YicmsCommon\Images\Image;
use Iliich246\YicmsCommon\Images\ImagesBlock;
use Iliich246\YicmsCommon\Images\ImagesHandler;
use Iliich246\YicmsCommon\Images\ImagesInterface;
use Iliich246\YicmsCommon\Images\ImagesReferenceInterface;
use Iliich246\YicmsCommon\Conditions\Condition;
use Iliich246\YicmsCommon\Conditions\ConditionTemplate;
use Iliich246\YicmsCommon\Conditions\ConditionsHandler;
use Iliich246\YicmsCommon\Conditions\ConditionsInterface;
use Iliich246\YicmsCommon\Conditions\ConditionsReferenceInterface;

/**
 * Class Pages
 *
 * @property integer $id
 * @property string $program_name
 * @property bool $editable
 * @property bool $visible
 * @property string $system_route
 * @property string $ruled_route
 * @property integer $pages_order
 * @property string $field_template_reference
 * @property string $field_reference
 * @property string $file_template_reference
 * @property string $file_reference
 * @property string $image_template_reference
 * @property string $image_reference
 * @property string $condition_template_reference
 * @property string $condition_reference
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class Pages extends ActiveRecord implements
    FieldsInterface,
    FieldReferenceInterface,
    FilesInterface,
    FilesReferenceInterface,
    ImagesInterface,
    ImagesReferenceInterface,
    ConditionsReferenceInterface,
    ConditionsInterface,
    FictiveInterface,
    SortOrderInterface
{
    use SortOrderTrait;

    const SCENARIO_CREATE = 0;
    const SCENARIO_UPDATE = 1;

    /** @var self[] buffer array */
    private static $pagesBuffer = [];
    /** @var FieldsHandler instance of field handler object */
    private $fieldHandler;
    /** @var FilesHandler instance of file handler object*/
    private $fileHandler;
    /** @var ImagesHandler instance of image handler object*/
    private $imageHandler;
    /** @var ConditionsHandler instance of condition handler object*/
    private $conditionHandler;
    /** @var boolean if true standard field as title and seo field will be created */
    public $standardFields = true;
    /** @var PageNamesTranslateDb[] buffer for language */
    private $pageNameTranslations;

    /**
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->visible = true;
        $this->editable = true;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pages}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'program_name'   => 'Program name',
            'standardFields' => 'Create standard fields of page(seo)',
            'editable'       => 'Editable',
            'visible'        => 'Visible',
            'system_route'   => 'System route'
        ];
    }

    /**
     * Return instance of page by her name
     * @param $programName
     * @return self
     * @throws PagesException
     */
    public static function getByName($programName)
    {
        foreach(self::$pagesBuffer as $page)
            if ($page->program_name == $programName)
                return $page;

        /** @var self $page */
        $page = self::find()
            ->where(['program_name' => $programName])
            ->one();

        if ($page) {
            self::$pagesBuffer[$page->id] = $page;
            return $page;
        }

        Yii::error("Сan not find page with name " . $programName, __METHOD__);

        if (defined('YICMS_STRICT')) {
            throw new PagesException('Сan not find page with name ' . $programName);
        }

        return new self();
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'program_name', 'standardFields', 'editable', 'visible', 'system_route'
            ],
            self::SCENARIO_UPDATE => [
                'program_name', 'editable', 'visible', 'system_route'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['program_name', 'required', 'message' => 'Obligatory input field'],
            ['program_name', 'string', 'max' => '50', 'tooLong' => 'Program name must be less than 50 symbols'],
            ['program_name', 'validateProgramName'],
            [['standardFields', 'editable'], 'boolean']
        ];
    }

    /**
     * Validates the program name.
     * This method serves as the inline validation for page program name.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateProgramName($attribute, $params)
    {
        if (!$this->hasErrors()) {

            $pagesQuery = self::find()->where(['program_name' => $this->program_name]);

            if ($this->scenario == self::SCENARIO_UPDATE)
                $pagesQuery->andWhere(['not in', 'program_name', $this->getOldAttribute('program_name')]);

            $pages = $pagesQuery->all();
            if ($pages)$this->addError($attribute, 'Page with same name already exist in system');
        }
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function afterValidate()
    {
        if ($this->hasErrors()) return;

        if ($this->scenario == self::SCENARIO_CREATE) {
            $this->field_template_reference = FieldTemplate::generateTemplateReference();
            $this->field_reference = $this->field_template_reference;

            $this->file_template_reference = FilesBlock::generateTemplateReference();
            $this->file_reference = $this->field_template_reference;

            $this->image_template_reference = ImagesBlock::generateTemplateReference();
            $this->image_reference = $this->image_template_reference;

            $this->condition_template_reference = ConditionTemplate::generateTemplateReference();
            $this->condition_reference = $this->condition_template_reference;
        }
    }

    /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->scenario == self::SCENARIO_CREATE)
            $this->pages_order = $this->maxOrder();

        if ($this->scenario == self::SCENARIO_UPDATE) return parent::save($runValidation, $attributeNames);

        if (!$this->standardFields) return parent::save($runValidation, $attributeNames);

        //create standard seo fields

        $fieldTemplate                           = new FieldTemplate();
        $fieldTemplate->field_template_reference = $this->field_template_reference;
        $fieldTemplate->scenario                 = FieldTemplate::SCENARIO_CREATE;
        $fieldTemplate->program_name             = 'title';
        $fieldTemplate->type                     = FieldTemplate::TYPE_INPUT;
        $fieldTemplate->language_type            = FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE;
        $fieldTemplate->visible                  = true;
        $fieldTemplate->editable                 = true;
        $fieldTemplate->field_order              = 1;

        $fieldTemplate->save(false);

        $fieldTemplate                           = new FieldTemplate();
        $fieldTemplate->field_template_reference = $this->field_template_reference;
        $fieldTemplate->scenario                 = FieldTemplate::SCENARIO_CREATE;
        $fieldTemplate->program_name             = 'meta_description';
        $fieldTemplate->type                     = FieldTemplate::TYPE_TEXT;
        $fieldTemplate->language_type            = FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE;
        $fieldTemplate->visible                  = true;
        $fieldTemplate->editable                 = true;
        $fieldTemplate->field_order              = 2;

        $fieldTemplate->save(false);

        $fieldTemplate                           = new FieldTemplate();
        $fieldTemplate->field_template_reference = $this->field_template_reference;
        $fieldTemplate->scenario                 = FieldTemplate::SCENARIO_CREATE;
        $fieldTemplate->program_name             = 'meta_keywords';
        $fieldTemplate->type                     = FieldTemplate::TYPE_TEXT;
        $fieldTemplate->language_type            = FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE;
        $fieldTemplate->visible                  = true;
        $fieldTemplate->editable                 = true;
        $fieldTemplate->field_order              = 3;

        $fieldTemplate->save(false);
        //TODO: makes create translates for standard fields

        return parent::save(false);
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        /** @var FieldTemplate[] $fieldTemplates */
        $fieldTemplates = FieldTemplate::find()->where([
            'field_template_reference' => $this->getFieldTemplateReference(),
        ])->all();

        foreach($fieldTemplates as $fieldTemplate)
            $fieldTemplate->delete();

        /** @var FilesBlock[] $filesBlocks */
        $filesBlocks = FilesBlock::find()->where([
            'file_template_reference' => $this->getFileTemplateReference(),
        ])->all();

        foreach($filesBlocks as $fileBlock)
            $fileBlock->delete();

        /** @var ImagesBlock[] $imageBlocks */
        $imageBlocks = ImagesBlock::find()->where([
            'image_template_reference' => $this->getImageTemplateReference(),
        ])->all();

        foreach($imageBlocks as $imageBlock)
            $imageBlock->delete();

        /** @var ConditionTemplate[] $conditionTemplates */
        $conditionTemplates = ConditionTemplate::find()->where([
            'condition_template_reference' => $this->getConditionTemplateReference(),
        ])->all();

        foreach($conditionTemplates as $conditionTemplate)
            $conditionTemplate->delete();

        /** @var PageNamesTranslateDb[] $pageNames */
        $pageNames = PageNamesTranslateDb::find()->where([
            'page_id' => $this->id,
        ])->all();

        foreach($pageNames as $pageName)
            $pageName->delete();

        return parent::delete();
    }

    /**
     * Return true if page has any constraints
     * @return bool
     */
    public function isConstraints()
    {
        /** @var FieldTemplate[] $fieldTemplates */
        $fieldTemplates = FieldTemplate::find()->where([
            'field_template_reference' => $this->getFieldTemplateReference(),
        ])->all();

        foreach($fieldTemplates as $fieldTemplate)
            if ($fieldTemplate->isConstraints()) return true;

        /** @var FilesBlock[] $filesBlocks */
        $filesBlocks = FilesBlock::find()->where([
            'file_template_reference' => $this->getFileTemplateReference(),
        ])->all();

        foreach($filesBlocks as $fileBlock)
            if ($fileBlock->isConstraints()) return true;

        /** @var ImagesBlock[] $imageBlocks */
        $imageBlocks = ImagesBlock::find()->where([
            'image_template_reference' => $this->getImageTemplateReference(),
        ])->all();

        foreach($imageBlocks as $imageBlock)
            if ($imageBlock->isConstraints()) return true;

        /** @var ConditionTemplate[] $conditionTemplates */
        $conditionTemplates = ConditionTemplate::find()->where([
            'condition_template_reference' => $this->getConditionTemplateReference(),
        ])->all();

        foreach($conditionTemplates as $conditionTemplate)
            if ($conditionTemplate->isConstraints()) return true;

        return false;
    }

    /**
     * Returns name of page
     * @param LanguagesDb|null $language
     * @return string
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function name(LanguagesDb $language = null)
    {
        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        //language buffer empty
        if (is_null($this->pageNameTranslations[$language->id])) {
            $this->pageNameTranslations[$language->id] = PageNamesTranslateDb::find()->where([
                'page_id'            => $this->id,
                'common_language_id' => $language->id,
            ])->one();
        }

        if (!$this->pageNameTranslations[$language->id]) return $this->program_name;

        /** @var PageNamesTranslateDb $translate */
        $translate = $this->pageNameTranslations[$language->id];

        return $translate->name;
    }

    /**
     * Returns description of page
     * @param LanguagesDb|null $language
     * @return string
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function description(LanguagesDb $language = null)
    {
        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        //language buffer empty
        if (is_null($this->pageNameTranslations[$language->id])) {
            $this->pageNameTranslations[$language->id] = PageNamesTranslateDb::find()->where([
                'page_id'            => $this->id,
                'common_language_id' => $language->id,
            ])->one();
        }

        if (!$this->pageNameTranslations[$language->id]) return false;

        /** @var PageNamesTranslateDb $translate */
        $translate = $this->pageNameTranslations[$language->id];

        return $translate->description;
    }

    /**
     * @inheritdoc
     */
    public function getFieldHandler()
    {
        if (!$this->fieldHandler)
            $this->fieldHandler = new FieldsHandler($this);

        return $this->fieldHandler;
    }

    /**
     * @inheritdoc
     */
    public function getField($name)
    {
        return $this->getFieldHandler()->getField($name);
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getFieldTemplateReference()
    {
        if (!$this->field_template_reference) {
            $this->field_template_reference = FieldTemplate::generateTemplateReference();
            $this->save(false);
        }

        return $this->field_template_reference;
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getFieldReference()
    {
        if (!$this->field_reference) {
            $this->field_reference = Field::generateReference();
            $this->save(false);
        }

        return $this->field_reference;
    }

    /**
     * @inheritdoc
     */
    public function getFileHandler()
    {
        if (!$this->fileHandler)
            $this->fileHandler = new FilesHandler($this);

        return $this->fileHandler;
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getFileReference()
    {
        if (!$this->file_reference) {
            $this->file_reference = File::generateReference();
            $this->save(false);
        }

        return $this->file_reference;
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getFileTemplateReference()
    {
        if (!$this->file_template_reference) {
            $this->file_template_reference = FilesBlock::generateTemplateReference();
            $this->save(false);
        }

        return $this->file_template_reference;
    }

    /**
     * @inheritdoc
     */
    public function getFileBlock($name)
    {
        return $this->getFileHandler()->getFileBlock($name);
    }

    /**
     * @inheritdoc
     */
    public function getImagesHandler()
    {
        if (!$this->imageHandler)
            $this->imageHandler = new ImagesHandler($this);

        return $this->imageHandler;
    }

    /**
     * @inheritdoc
     */
    public function getImageBlock($name)
    {
        return $this->getImagesHandler()->getImageBlock($name);
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getImageTemplateReference()
    {
        if (!$this->image_template_reference) {
            $this->image_template_reference = ImagesBlock::generateTemplateReference();
            $this->save(false);
        }

        return $this->image_template_reference;
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getImageReference()
    {
        if (!$this->image_reference) {
            $this->image_reference = Image::generateReference();
            $this->save(false);
        }

        return $this->image_reference;
    }

    /**
     * @inheritdoc
     */
    public function getConditionsHandler()
    {
        if (!$this->conditionHandler)
            $this->conditionHandler = new ConditionsHandler($this);

        return $this->conditionHandler;
    }

    /**
     * @inheritdoc
     */
    public function getCondition($name)
    {
        return $this->getConditionsHandler()->getCondition($name);
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getConditionTemplateReference()
    {
        if (!$this->condition_template_reference) {
            $this->condition_template_reference = ConditionTemplate::generateTemplateReference();
            $this->save(false);
        }

        return $this->condition_template_reference;
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getConditionReference()
    {
        if (!$this->condition_reference) {
            $this->image_reference = Condition::generateReference();
            $this->save(false);
        }

        return $this->condition_reference;
    }

    /**
     * @inheritdoc
     */
    public function getOrderQuery()
    {
        return self::find();
    }

    /**
     * @inheritdoc
     */
    public static function getOrderFieldName()
    {
        return 'pages_order';
    }

    /**
     * @inheritdoc
     */
    public function getOrderValue()
    {
        return $this->pages_order;
    }

    /**
     * @inheritdoc
     */
    public function setOrderValue($value)
    {
        $this->pages_order = $value;
    }

    /**
     * @inheritdoc
     */
    public function configToChangeOfOrder()
    {
        $this->scenario = self::SCENARIO_UPDATE;
    }

    /**
     * @inheritdoc
     */
    public function getOrderAble()
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setFictive()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function clearFictive()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isFictive()
    {
        return false;
    }
}
