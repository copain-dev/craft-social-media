# craft-social-media

A Craft CMS plugin for managing and organizing social media links directly from the Control Panel.

## Requirements

This plugin requires Craft CMS 5.5.0 or later, and PHP 8.2 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

### From the Plugin Store

Go to the Plugin Store in your project's Control Panel and search for "Social Media". Then press "Install".

### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project

# tell Composer to load the plugin
composer require copain/craft-social-media

# tell Craft to install the plugin
./craft plugin/install social-media
```

## Features

- Manage social media links in the control panel
- Multi-site support
- Drag and drop reordering
- Enable/disable specific platforms
- Allow multiple links per platform
- Customizable platform configurations

## Configuration

### Custom Platforms

You can add custom platforms or override existing ones by creating a `config/social-media.php` file:

```php
<?php

return [
    'customPlatforms' => [
        // Override existing platform
        'facebook' => [
            'label' => 'FB',
            'color' => '#1877F2',
            'icon' => '<svg>...</svg>'
        ],
        // Add new platform
        'custom' => [
            'label' => 'My Platform',
            'color' => '#FF0000',
            'icon' => '<svg>...</svg>'
        ]
    ]
];
```

Each platform requires:

- `label`: Display name
- `color`: Background color for the icon (hex format)
- `icon`: SVG markup for the icon

### Plugin Settings

In the Control Panel, go to Settings → Social Media to:

- Enable/disable specific platforms
- Allow multiple links per platform

## Usage

### Control Panel

1. Go to Social Media in the CP sidebar
2. Click "New Link" to add a social media link
3. Select platform, enter label and URL
4. Enable/disable the link as needed
5. Drag and drop to reorder links

### Template Usage

#### Get All Links

```twig
{# Get all enabled links for current site #}
{% set links = craft.socialMedia.getLinks() %}

{% for link in links %}
    <a href="{{ link.url }}"
       style="background-color: {{ link.color }}"
       title="{{ link.label }}">
        {{ link.icon|raw }}
    </a>
{% endfor %}
```

#### Get Links by Site

```twig
{# Get links for a specific site #}
{% set links = craft.socialMedia.getLinksBySiteId(currentSite.id) %}
{% set links = craft.socialMedia.getLinksBySiteId(1) %}
```

#### Check Platform Status

```twig
{# Check if a platform is enabled #}
{% if craft.socialMedia.isPlatformEnabled('facebook') %}
    {# Show Facebook-specific content #}
{% endif %}
```

#### Platform Information

```twig
{# Get platform details #}
{% set platform = 'facebook' %}
{{ craft.socialMedia.getPlatformLabel(platform) }}
{{ craft.socialMedia.getIcon(platform)|raw }}
{{ craft.socialMedia.getColor(platform) }}
```

## API

### Twig Variables

| Method                        | Description                            |
| ----------------------------- | -------------------------------------- |
| `getLinks()`                  | Get all enabled links for current site |
| `getLinksBySiteId(id)`        | Get links for specific site            |
| `isPlatformEnabled(platform)` | Check if platform is enabled           |
| `getPlatformLabel(platform)`  | Get platform display name              |
| `getIcon(platform)`           | Get platform SVG icon                  |
| `getColor(platform)`          | Get platform color                     |
| `getEnabledPlatforms()`       | Get list of enabled platforms          |
| `getPlatformConfig()`         | Get full platform configuration        |

### Events

The plugin dispatches events for:

- Before/after saving links
- Before/after deleting links
- Before/after reordering links

## Support

Create an issue on GitHub or contact dev@copain.dev

## License

This plugin is licensed under the MIT License.
