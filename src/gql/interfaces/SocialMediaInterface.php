<?php

namespace copain\craftsocialmedia\gql\interfaces;

use Craft;
use craft\gql\GqlEntityRegistry;
use craft\gql\base\InterfaceType;
use craft\gql\TypeManager;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InterfaceType as GqlInterfaceType;

class SocialMediaInterface extends InterfaceType
{
    public static function getTypeGenerator(): string
    {
        return SocialMediaType::class;
    }

    public static function getName(): string
    {
        return 'SocialMediaInterface';
    }

    public static function getFieldDefinitions(): array
    {
        return [
            'platform' => [
                'name' => 'platform',
                'type' => Type::string(),
                'description' => 'The social media platform'
            ],
            'url' => [
                'name' => 'url',
                'type' => Type::string(),
                'description' => 'The social media link URL'
            ],
            'enabled' => [
                'name' => 'enabled',
                'type' => Type::boolean(),
                'description' => 'Whether the link is enabled'
            ]
        ];
    }
}