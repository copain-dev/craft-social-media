<?php

namespace copain\craftsocialmedia\migrations;

use Craft;
use craft\db\Migration;

class m240321_000000_add_sort_order_column extends Migration
{
    public function safeUp(): bool
    {
        if (!$this->db->columnExists('{{%socialmedia_links}}', 'sortOrder')) {
            $this->addColumn('{{%socialmedia_links}}', 'sortOrder', $this->integer()->notNull()->defaultValue(0));

            // Set initial sort order based on ID
            $this->execute('UPDATE {{%socialmedia_links}} SET [[sortOrder]] = [[id]]');
        }

        return true;
    }

    public function safeDown(): bool
    {
        if ($this->db->columnExists('{{%socialmedia_links}}', 'sortOrder')) {
            $this->dropColumn('{{%socialmedia_links}}', 'sortOrder');
        }

        return true;
    }
}