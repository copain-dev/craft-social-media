<?php

namespace copain\craftsocialmedia\records;

use craft\db\ActiveRecord;
use yii\db\ActiveQueryInterface;

class SocialMediaRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%socialmedia_links}}';
    }

    public function rules(): array
    {
        return [
            [['platform', 'name', 'url', 'siteId'], 'required'],
            ['enabled', 'boolean'],
            ['sortOrder', 'integer'],
            ['sortOrder', 'default', 'value' => 0],
        ];
    }
}