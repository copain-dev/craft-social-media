<?php

namespace plugins\socialMedia\records;

use craft\db\ActiveRecord;

class SocialMediaLinkRecord extends ActiveRecord
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['siteId'], 'integer'],
            [['url', 'platform', 'label'], 'string'],
            [['enabled'], 'boolean'],
            [['dateCreated', 'dateUpdated'], 'safe'],
            [['uid'], 'string', 'max' => 36],
        ]);
    }

    public static function tableName(): string
    {
        return '{{%socialmedia_links}}';
    }
}