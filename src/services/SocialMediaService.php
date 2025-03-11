<?php

namespace copain\craftsocialmedia\services;

use Craft;
use copain\craftsocialmedia\SocialMedia;
use copain\craftsocialmedia\records\SocialMediaRecord;
use copain\craftsocialmedia\models\SocialMedia as SocialMediaModel;
use craft\base\Component;
use craft\db\Query;
use craft\elements\Entry;
use copain\craftsocialmedia\config\PlatformConfig;
use craft\db\Connection;

class SocialMediaService extends Component
{
    /**
     * Get all social media links for the current site
     */
    public function getLinks(Entry $entry = null): array
    {
        $query = (new Query())
            ->select([
                'platform',
                'url',
                'enabled',
                'sortOrder'
            ])
            ->from('{{%socialmedia_links}}')
            ->where([
                'enabled' => true,
                'siteId' => Craft::$app->getSites()->getCurrentSite()->id
            ]);

        // Only get links for enabled platforms
        $query->andWhere(['platform' => array_keys(array_filter(SocialMedia::getInstance()->getSettings()->platforms, function($enabled) {
            return $enabled;
        }))]);

        if ($entry) {
            $query->andWhere(['entryId' => $entry->id]);
        }

        $query->orderBy(['sortOrder' => SORT_ASC]);

        $links = $query->all();

        // Format the links with platform info
        return array_map(function($link) {
            return [
                'platform' => $link['platform'],
                'url' => $link['url'],
                'label' => PlatformConfig::getLabel($link['platform']),
                'color' => PlatformConfig::getColor($link['platform']),
                'icon' => PlatformConfig::getIcon($link['platform']),
                'enabled' => (bool)$link['enabled'],
                'sortOrder' => (int)$link['sortOrder']
            ];
        }, $links);
    }

    public function getAllLinks(): array
    {
        $query = SocialMediaRecord::find()
            ->select([
                'id',
                'platform',
                'name',
                'url',
                'enabled',
                'sortOrder'
            ])
            ->orderBy(['sortOrder' => SORT_ASC]);

        $links = $query->all();

        // Convert records to arrays and add platform enabled status
        return array_map(function($link) {
            return [
                'id' => $link->id,
                'platform' => $link->platform,
                'name' => $link->name,
                'url' => $link->url,
                'enabled' => $link->enabled,
                'sortOrder' => $link->sortOrder,
                'platformEnabled' => SocialMedia::getInstance()->isPlatformEnabled($link->platform)
            ];
        }, $links);
    }

    public function getUsedPlatforms(?int $siteId = null): array
    {
        // Use direct query to avoid any caching issues
        $query = (new Query())
            ->select(['platform'])
            ->from('{{%socialmedia_links}}');

        if ($siteId !== null) {
            $query->where(['siteId' => $siteId]);
        }

        return $query->column();
    }

    public function isValidPlatform(string $platform, ?int $excludeId = null, ?int $siteId = null): bool
    {
        if (SocialMedia::getInstance()->getSettings()->allowMultipleLinks) {
            return true;
        }

        $query = SocialMediaRecord::find()->where(['platform' => $platform]);

        if ($excludeId !== null) {
            $query->andWhere(['not', ['id' => $excludeId]]);
        }

        if ($siteId !== null) {
            $query->andWhere(['siteId' => $siteId]);
        } else {
            $query->andWhere(['siteId' => Craft::$app->getSites()->getCurrentSite()->id]);
        }

        return !$query->exists();
    }

    public function reorderLinks(array $ids): bool
    {
        foreach ($ids as $sortOrder => $id) {
            Craft::$app->getDb()->createCommand()
                ->update('{{%socialmedia_links}}',
                    ['sortOrder' => $sortOrder + 1],
                    ['id' => $id])
                ->execute();
        }
        return true;
    }

    public function getLinksBySiteId(int $siteId): array
    {
        return SocialMediaRecord::find()
            ->where(['siteId' => $siteId])
            ->orderBy(['sortOrder' => SORT_ASC])
            ->all();
    }
}