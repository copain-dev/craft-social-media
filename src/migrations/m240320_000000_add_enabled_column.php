<?php

namespace copain\craftsocialmedia\migrations;

use craft\db\Migration;

class m240320_000000_add_enabled_column extends Migration
{
    public function safeUp(): bool
    {
        if (!$this->db->columnExists('{{%socialmedia_links}}', 'enabled')) {
            $this->addColumn('{{%socialmedia_links}}', 'enabled', $this->boolean()->notNull()->defaultValue(true));
        }
        return true;
    }

    public function safeDown(): bool
    {
        if ($this->db->columnExists('{{%socialmedia_links}}', 'enabled')) {
            $this->dropColumn('{{%socialmedia_links}}', 'enabled');
        }
        return true;
    }
}