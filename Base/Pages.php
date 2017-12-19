<?php

namespace Iliich246\YicmsPages\Base;

use yii\db\ActiveRecord;
use Iliich246\YicmsCommon\Fields\FieldsHandler;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\FieldsInterface;
use Iliich246\YicmsCommon\Fields\FieldReferenceInterface;

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
    FieldReferenceInterface
{
    const SCENARIO_CREATE = 0;
    const SCENARIO_UPDATE = 1;

    /** @var FieldsHandler instance of field handler object */
    private $fieldHandler;

    /**
     * @var boolean if true standard field as title and seo field will be created
     */
    public $standardFields = true;

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
            'program_name' => 'Program name',
            'standardFields' => 'Create standard fields of page(seo)',
            'editable' => 'Editable',
            'visible' => 'Visible',
            'system_route' => 'System route'
        ];
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

            $pagesQuery = Pages::find()->where(['program_name' => $this->program_name]);

            if ($this->scenario == self::SCENARIO_UPDATE)
                $pagesQuery->andWhere(['not in', 'program_name', $this->getOldAttribute('program_name')]);

            $pages = $pagesQuery->all();
            if ($pages)$this->addError($attribute, 'Page with same name already exist in system');
        }
    }

    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        if ($this->hasErrors()) return;

        if ($this->scenario == self::SCENARIO_CREATE) {
            $this->field_template_reference = FieldTemplate::generateTemplateReference();
            $this->field_reference = $this->field_template_reference;
        }
    }

    /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->scenario == self::SCENARIO_UPDATE) return parent::save($runValidation, $attributeNames);

        if (!$this->standardFields) return parent::save($runValidation, $attributeNames);

        //create standard seo fields
        $fieldTemplate = new FieldTemplate();
        $fieldTemplate->field_template_reference = $this->field_template_reference;
        $fieldTemplate->scenario = FieldTemplate::SCENARIO_CREATE;
        $fieldTemplate->program_name = 'title';
        $fieldTemplate->type = FieldTemplate::TYPE_INPUT;
        $fieldTemplate->language_type = FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE;
        $fieldTemplate->visible = true;
        $fieldTemplate->editable = true;
        $fieldTemplate->is_main = true;
        $fieldTemplate->field_order = 1;

        $fieldTemplate->save(false);

        $fieldTemplate = new FieldTemplate();
        $fieldTemplate->field_template_reference = $this->field_template_reference;
        $fieldTemplate->scenario = FieldTemplate::SCENARIO_CREATE;
        $fieldTemplate->program_name = 'meta_description';
        $fieldTemplate->type = FieldTemplate::TYPE_TEXT;
        $fieldTemplate->language_type = FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE;
        $fieldTemplate->visible = true;
        $fieldTemplate->editable = true;
        $fieldTemplate->is_main = false;
        $fieldTemplate->field_order = 2;

        $fieldTemplate->save(false);

        $fieldTemplate = new FieldTemplate();
        $fieldTemplate->field_template_reference = $this->field_template_reference;
        $fieldTemplate->scenario = FieldTemplate::SCENARIO_CREATE;
        $fieldTemplate->program_name = 'meta_keywords';
        $fieldTemplate->type = FieldTemplate::TYPE_TEXT;
        $fieldTemplate->language_type = FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE;
        $fieldTemplate->visible = true;
        $fieldTemplate->editable = true;
        $fieldTemplate->is_main = false;
        $fieldTemplate->field_order = 3;

        $fieldTemplate->save(false);
        //TODO: makes create translates for standard fields

        return parent::save(false);
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
     */
    public function getFieldTemplateReference()
    {
        return $this->field_template_reference;
    }

    /**
     * @inheritdoc
     */
    public function getFieldReference()
    {
        return $this->field_reference;
    }
}
