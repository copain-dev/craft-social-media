<?php

namespace copain\craftsocialmedia\gql\types;

use copain\craftsocialmedia\gql\interfaces\SocialMediaInterface;
use craft\gql\GqlEntityRegistry;
use craft\gql\base\ObjectType;
use GraphQL\Type\Definition\Type;

class SocialMediaType extends ObjectType
{
    public static function getName(): string
    {
        return 'SocialMediaType';
    }

    public static function getType(): Type
    {
        if ($type = GqlEntityRegistry::getEntity(self::getName())) {
            return $type;
        }

        return GqlEntityRegistry::createEntity(self::getName(), new self([
            'name' => self::getName(),
            'fields' => self::class . '::getFieldDefinitions',
            'interfaces' => [
                SocialMediaInterface::getType(),
            ],
        ]));
    }

    public static function getFieldDefinitions(): array
    {
        return SocialMediaInterface::getFieldDefinitions();
    }
}