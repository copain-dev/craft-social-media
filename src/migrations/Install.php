<?php

namespace copain\craftsocialmedia\migrations;

use Craft;
use craft\db\Migration;

class Install extends Migration
{
    public function safeUp(): bool
    {
        if (!$this->db->tableExists('{{%socialmedia_links}}')) {
            $this->createTable('{{%socialmedia_links}}', [
                'id' => $this->primaryKey(),
                'platform' => $this->string()->notNull(),
                'name' => $this->string()->notNull(),
                'url' => $this->string()->notNull(),
                'enabled' => $this->boolean()->defaultValue(true)->notNull(),
                'sortOrder' => $this->integer()->notNull()->defaultValue(0),
                'siteId' => $this->integer()->notNull(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
            ]);

            $this->addForeignKey(
                null,
                '{{%socialmedia_links}}',
                ['siteId'],
                '{{%sites}}',
                ['id'],
                'CASCADE',
                'CASCADE'
            );

            // Set initial records to the primary site if any exist
            if ($this->db->tableExists('{{%socialmedia_links}}')) {
                $this->update(
                    '{{%socialmedia_links}}',
                    ['siteId' => Craft::$app->sites->getPrimarySite()->id],
                    '',
                    [],
                    false
                );
            }
        }

        return true;
    }

    public function safeDown(): bool
    {
        if ($this->db->tableExists('{{%socialmedia_links}}')) {
            // Get the foreign key name first
            $foreignKeys = $this->db->schema->getTableSchema('{{%socialmedia_links}}')->foreignKeys;
            foreach ($foreignKeys as $name => $foreignKey) {
                if (isset($foreignKey['sites'])) {
                    // Drop the foreign key if it exists
                    $this->dropForeignKey($name, '{{%socialmedia_links}}');
                    break;
                }
            }

            // Then drop the table
            $this->dropTable('{{%socialmedia_links}}');
        }

        return true;
    }
}