<?php

namespace copain\craftsocialmedia\controllers;

use craft\web\Controller;
use yii\web\Response;

class ApiController extends Controller
{
    protected array|bool|int $allowAnonymous = true;

    public function actionGetLinks(): Response
    {
        $this->requireAcceptsJson();

        $links = \copain\craftsocialmedia\SocialMedia::getInstance()
            ->socialMediaService
            ->getLinks();

        return $this->asJson([
            'links' => array_map(function($link) {
                return [
                    'platform' => $link->platform,
                    'url' => $link->url,
                    'enabled' => $link->enabled
                ];
            }, $links)
        ]);
    }
}