<?php

namespace copain\craftsocialmedia\services;

use craft\base\Component;

class ProjectConfig extends Component
{
    public const PATH = 'plugins.social-media.settings';

    public function handleChangedSettings(): void
    {
        // Nothing to do here since we're just storing platform settings
    }
}