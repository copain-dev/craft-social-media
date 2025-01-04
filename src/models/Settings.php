<?php

namespace copain\craftsocialmedia\models;

use craft\base\Model;

class Settings extends Model
{
    public array $platforms = [];
    public bool $allowMultipleLinks = false;

    protected function defineRules(): array
    {
        return [
            ['platforms', 'safe'],
            ['allowMultipleLinks', 'boolean'],
        ];
    }
}