<?php

namespace copain\craftsocialmedia\gql\queries;

use copain\craftsocialmedia\SocialMedia;
use copain\craftsocialmedia\gql\types\SocialMediaType;
use copain\craftsocialmedia\gql\permissions\SocialMediaPermissions;
use craft\gql\base\Query;
use GraphQL\Type\Definition\Type;

class SocialMediaQuery extends Query
{
    public static function getQueries(): array
    {
        return [
            'socialMediaLinks' => [
                'type' => Type::listOf(SocialMediaType::getType()),
                'description' => 'Get all enabled social media links',
                'resolve' => function($source, $arguments) {
                    // Check permissions
                    self::requirePermission(SocialMediaPermissions::VIEW_SOCIAL_MEDIA);

                    return SocialMedia::getInstance()->socialMediaService->getLinks();
                }
            ]
        ];
    }
}