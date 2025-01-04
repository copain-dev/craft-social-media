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
                'sortOrder' => $this->integer()->notNull(),
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
        }

        return true;
    }

    public function safeDown(): bool
    {
        if ($this->db->tableExists('{{%socialmedia_links}}')) {
            $this->dropForeignKey(null, '{{%socialmedia_links}}');
            $this->dropTable('{{%socialmedia_links}}');
        }

        return true;
    }
}