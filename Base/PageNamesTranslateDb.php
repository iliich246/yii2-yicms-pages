<?php

namespace Iliich246\YicmsPages\Base;

use Iliich246\YicmsCommon\Languages\LanguagesDb;
use yii\db\ActiveRecord;

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
class PageNamesTranslateDb extends ActiveRecord
{
    /** @var array buffer of translates in view $buffer[<page-id>][<language-id>] */
    private static $buffer;

    /**
     * Return buffered translation
     * @param $pageId
     * @param $languageId
     * @return null|self
     */
    public static function getTranslate($pageId, $languageId) {
        if (isset(self::$buffer[$pageId][$languageId]))
            return self::$buffer[$pageId][$languageId];

        $translation = self::find()->where([
            'page_id'            => $pageId,
            'common_language_id' => $languageId,
        ])->one();

        return self::$buffer[$pageId][$languageId] = $translation;
    }

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
    public function rules()
    {
        return [
            ['name', 'string', 'max' => '50', 'tooLong' => 'Name of page must be less than 50 symbols'],
            ['description', 'string'],
            [
                ['common_language_id'], 'exist', 'skipOnError' => true,
                'targetClass' => LanguagesDb::className(), 'targetAttribute' => ['common_language_id' => 'id']
            ],
            [
                ['page_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Pages::className(), 'targetAttribute' => ['page_id' => 'id']
            ],
        ];
    }
}
