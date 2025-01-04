<?php

namespace copain\craftsocialmedia\models;

use Craft;
use craft\base\Model;
use copain\craftsocialmedia\SocialMedia as Plugin;

class SocialMedia extends Model
{
    public ?int $id = null;
    public string $platform = '';
    public string $name = '';
    public string $url = '';
    public bool $enabled = true;
    public int $sortOrder = 0;
    public ?int $siteId = null;

    protected function defineRules(): array
    {
        return [
            [['platform', 'name', 'url'], 'required', 'message' => '{attribute} cannot be blank.'],
            ['url', 'url', 'message' => 'Please enter a valid URL.'],
            ['enabled', 'boolean'],
            ['sortOrder', 'integer'],
            ['siteId', 'integer'],
            ['platform', 'validatePlatform'],
        ];
    }

    public function validatePlatform($attribute)
    {
        if (!array_key_exists($this->$attribute, \copain\craftsocialmedia\config\PlatformConfig::$platforms)) {
            $this->addError($attribute, Craft::t('social-media', 'Invalid platform selected.'));
        }
    }

    /**
     * Check if the platform is enabled in plugin settings
     */
    public function getPlatformEnabled(): bool
    {
        $settings = Plugin::getInstance()->getSettings();
        return $settings->platforms[$this->platform] ?? true;
    }

    public function attributeLabels(): array
    {
        return [
            'platform' => Craft::t('social-media', 'Platform'),
            'name' => Craft::t('social-media', 'Label'),
            'url' => Craft::t('social-media', 'URL'),
        ];
    }
}
