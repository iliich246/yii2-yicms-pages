<?php

use yii\db\Migration;

/**
 * Class m171114_174807_pages_init
 */
class m171114_174807_pages_init extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        /**
         * pages table
         */
        $this->createTable('{{%pages}}', [
            'id'                           => $this->primaryKey(),
            'program_name'                 => $this->string(50),
            'editable'                     => $this->boolean(),
            'visible'                      => $this->boolean(),
            'system_route'                 => $this->string(),
            'ruled_route'                  => $this->string(),
            'pages_order'                  => $this->integer(),
            'field_template_reference'     => $this->string(),
            'field_reference'              => $this->string(),
            'file_template_reference'      => $this->string(),
            'file_reference'               => $this->string(),
            'image_template_reference'     => $this->string(),
            'image_reference'              => $this->string(),
            'condition_template_reference' => $this->string(),
            'condition_reference'          => $this->string(),
        ]);

        /**
         * pages_config table
         */
        $this->createTable('{{%pages_config}}', [
            'id'                      => $this->primaryKey(),
            'imagesPatch'             => $this->string(),
            'filesPatch'              => $this->string(),
            'thumbNailsDirectoryName' => $this->string(),
        ]);

        $this->insert('{{%pages_config}}', [
            'id' => 1,
            'imagesPatch' => DIRECTORY_SEPARATOR .
                             'web' . DIRECTORY_SEPARATOR .
                             'files' . DIRECTORY_SEPARATOR .
                             'pages' . DIRECTORY_SEPARATOR,
            'filesPatch' => DIRECTORY_SEPARATOR .
                            'web' . DIRECTORY_SEPARATOR .
                            'images' . DIRECTORY_SEPARATOR .
                            'pages' . DIRECTORY_SEPARATOR,
            'thumbNailsDirectoryName' => 'thumb'
        ]);

        /**
         * pages_names_translates table
         */
        $this->createTable('{{%pages_names_translates}}', [
            'id'                 => $this->primaryKey(),
            'page_id'            => $this->integer(),
            'common_language_id' => $this->integer(),
            'name'               => $this->string(),
            'description'        => $this->string(),
        ]);

        $this->addForeignKey('pages_names_translates-to-pages',
            '{{%pages_names_translates}}',
            'page_id',
            '{{%pages}}',
            'id'
        );

        $this->addForeignKey('pages_names_translates-to-common_languages',
            '{{%pages_names_translates}}',
            'common_language_id',
            '{{%common_languages}}',
            'id'
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('pages_names_translates-to-common_languages', '{{%pages_names_translates}}');
        $this->dropForeignKey('pages_names_translates-to-pages', '{{%pages_names_translates}}');
        $this->dropTable('{{%pages_names_translates}}');

        $this->dropTable('{{%pages_config}}');
        $this->dropTable('{{%pages}}');
    }
}
