<?php

namespace copain\craftsocialmedia\migrations;

use craft\db\Migration;

class m240102_000000_add_platform_column extends Migration
{
    public function safeUp(): bool
    {
        if (!$this->db->columnExists('{{%socialmedia_links}}', 'platform')) {
            $this->addColumn('{{%socialmedia_links}}', 'platform', $this->string()->notNull()->after('id'));
        }
        return true;
    }

    public function safeDown(): bool
    {
        if ($this->db->columnExists('{{%socialmedia_links}}', 'platform')) {
            $this->dropColumn('{{%socialmedia_links}}', 'platform');
        }
        return true;
    }
}