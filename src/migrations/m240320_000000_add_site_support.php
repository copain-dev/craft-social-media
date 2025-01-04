<?php
namespace copain\craftsocialmedia\migrations;

use craft\db\Migration;

class m240320_000000_add_site_support extends Migration
{
    public function safeUp(): bool
    {
        if (!$this->db->columnExists('{{%socialmedia_links}}', 'siteId')) {
            $this->addColumn('{{%socialmedia_links}}', 'siteId', $this->integer()->after('id'));

            // Add foreign key
            $this->addForeignKey(
                null,
                '{{%socialmedia_links}}',
                ['siteId'],
                '{{%sites}}',
                ['id'],
                'CASCADE',
                'CASCADE'
            );

            // Set existing records to the primary site
            $this->update(
                '{{%socialmedia_links}}',
                ['siteId' => \Craft::$app->sites->getPrimarySite()->id],
                '',
                [],
                false
            );
        }

        return true;
    }

    public function safeDown(): bool
    {
        if ($this->db->columnExists('{{%socialmedia_links}}', 'siteId')) {
            $this->dropForeignKey(null, '{{%socialmedia_links}}');
            $this->dropColumn('{{%socialmedia_links}}', 'siteId');
        }

        return true;
    }
}