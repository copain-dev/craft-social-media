<?php

namespace copain\craftsocialmedia;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\web\UrlManager;
use yii\base\Event;
use copain\craftsocialmedia\models\Settings;
use copain\craftsocialmedia\services\SocialMediaService;
use copain\craftsocialmedia\services\ProjectConfig;
use copain\craftsocialmedia\config\PlatformConfig;
use craft\web\twig\variables\CraftVariable;
use copain\craftsocialmedia\variables\SocialMediaVariable;
use craft\services\Sites;
use craft\events\DeleteSiteEvent;
use copain\craftsocialmedia\records\SocialMediaRecord;
use craft\events\ModelEvent;

class SocialMedia extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;
    public bool $hasCpSection = true;
    public static SocialMedia $plugin;

    public static function config(): array
    {
        return [
            'components' => [
                'socialMediaService' => SocialMediaService::class,
                'projectConfig' => ProjectConfig::class,
            ],
        ];
    }

    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        // Initialize custom platforms first
        PlatformConfig::init();

        // Register event handlers
        $this->attachEventHandlers();
    }

    private function attachEventHandlers(): void
    {
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (Event $event) {
                $event->rules['social-media'] = 'social-media/social-media/index';
                $event->rules['social-media/new'] = 'social-media/social-media/edit';
                $event->rules['social-media/edit/<id:\d+>'] = 'social-media/social-media/edit';
            }
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function(Event $event) {
                $variable = $event->sender;
                $variable->set('socialMedia', SocialMediaVariable::class);
            }
        );

        // Listen for site deletion
        Event::on(
            Sites::class,
            Sites::EVENT_BEFORE_DELETE_SITE,
            function(DeleteSiteEvent $event) {
                // Delete all social media links for this site
                SocialMediaRecord::deleteAll([
                    'siteId' => $event->site->id
                ]);
            }
        );

        // Add new event handler for settings changes
        Event::on(
            Plugin::class,
            Plugin::EVENT_AFTER_SAVE_SETTINGS,
            function(Event $event) {
                if ($event->sender === $this) {
                    $this->handlePlatformSettingsChange();
                }
            }
        );
    }

    /**
     * Handle platform settings changes by disabling links for disabled platforms
     */
    private function handlePlatformSettingsChange(): void
    {
        $settings = $this->getSettings();
        $platforms = $settings->platforms;

        // Get all disabled platforms
        $disabledPlatforms = array_keys(array_filter($platforms, function($enabled) {
            return !$enabled;
        }));

        if (!empty($disabledPlatforms)) {
            // Disable all links for disabled platforms
            SocialMediaRecord::updateAll(
                ['enabled' => false],
                ['platform' => $disabledPlatforms]
            );

            // Log the action
            Craft::info(
                'Disabled social media links for platforms: ' . implode(', ', $disabledPlatforms),
                __METHOD__
            );
        }
    }

    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('social-media/_settings.twig', [
            'settings' => $this->getSettings(),
        ]);
    }

    public function beforeSaveSettings(): bool
    {
        if (!parent::beforeSaveSettings()) {
            return false;
        }

        Craft::$app->getProjectConfig()->set(
            ProjectConfig::PATH,
            $this->getSettings()->toArray()
        );

        return true;
    }

    protected function cpNavIconPath(): ?string
    {
        return __DIR__ . '/thumb.svg';
    }

    public function isPlatformEnabled(string $platform): bool
    {
        $settings = $this->getSettings();
        return $settings->platforms[$platform] ?? true;
    }
}