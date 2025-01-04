<?php

namespace copain\craftsocialmedia\variables;

use Craft;
use copain\craftsocialmedia\SocialMedia;
use copain\craftsocialmedia\config\PlatformConfig;
use craft\helpers\Template;
use copain\craftsocialmedia\records\SocialMediaRecord;

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

    /**
     * Get all social media links for the current site
     */
    public function getLinks(): array
    {
        $links = SocialMediaRecord::find()
            ->where([
                'enabled' => true,
                'siteId' => Craft::$app->getSites()->getCurrentSite()->id
            ])
            ->orderBy(['sortOrder' => SORT_ASC])
            ->all();

        // Format the links with platform info
        return array_map(function($link) {
            return [
                'id' => $link->id,
                'platform' => $link->platform,
                'name' => $link->name,
                'url' => $link->url,
                'label' => PlatformConfig::getLabel($link->platform),
                'color' => PlatformConfig::getColor($link->platform),
                'icon' => PlatformConfig::getIcon($link->platform),
                'enabled' => (bool)$link->enabled,
                'sortOrder' => (int)$link->sortOrder
            ];
        }, $links);
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