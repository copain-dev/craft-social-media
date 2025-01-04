<?php

namespace copain\craftsocialmedia\gql\permissions;

use craft\gql\base\Permissions;

class SocialMediaPermissions extends Permissions
{
    public const VIEW_SOCIAL_MEDIA = 'viewSocialMedia';

    protected static function defineRootPermissions(): array
    {
        return [
            self::VIEW_SOCIAL_MEDIA => [
                'label' => 'View social media links',
            ],
        ];
    }
}