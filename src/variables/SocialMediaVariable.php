<?php

namespace copain\craftsocialmedia\variables;

use Craft;
use copain\craftsocialmedia\SocialMedia;
use copain\craftsocialmedia\config\PlatformConfig;
use craft\helpers\Template;
use craft\elements\Entry;

class SocialMediaVariable
{
    public function getEnabledPlatforms(): array
    {
        $settings = SocialMedia::getInstance()->getSettings();
        $allPlatforms = array_keys(PlatformConfig::$platforms);

        return array_filter($allPlatforms, function($platform) use ($settings) {
            return $settings->platforms[$platform] ?? true;
        });
    }

    public function getPlatformLabel(string $platform): string
    {
        return PlatformConfig::getLabel($platform);
    }

    public function getPlatformConfig(): array
    {
        return PlatformConfig::$platforms;
    }

    public function getIcon(string $platform): \Twig\Markup
    {
        $icon = PlatformConfig::getIcon($platform);
        return Template::raw($icon);
    }

    public function getColor(string $platform): string
    {
        return PlatformConfig::getColor($platform);
    }

    public function getLinks(Entry $entry = null): array
    {
        return SocialMedia::getInstance()->socialMediaService->getLinks($entry);
    }

    public function getLinksBySiteId(int $siteId): array
    {
        return SocialMedia::getInstance()->socialMediaService->getLinksBySiteId($siteId);
    }

    public function getCurrentSiteLinks(): array
    {
        return $this->getLinksBySiteId(Craft::$app->getSites()->getCurrentSite()->id);
    }

    public function isPlatformEnabled(string $platform): bool
    {
        return SocialMedia::getInstance()->isPlatformEnabled($platform);
    }

    public function getUsedPlatforms(): array
    {
        return SocialMedia::getInstance()->socialMediaService->getUsedPlatforms();
    }
}