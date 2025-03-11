<?php

namespace copain\craftsocialmedia\controllers;

use Craft;
use craft\web\Controller;
use yii\web\NotFoundHttpException;
use copain\craftsocialmedia\models\SocialMedia;
use copain\craftsocialmedia\records\SocialMediaRecord;
use yii\web\Response;

class SocialMediaController extends Controller
{
    protected array|bool|int $allowAnonymous = [];

    public function actionIndex(): Response
    {
        // Get current site from URL or fallback to default
        $currentSite = Craft::$app->getSites()->getCurrentSite();
        $siteHandle = $this->request->getQueryParam('site', $currentSite->handle);
        $site = Craft::$app->getSites()->getSiteByHandle($siteHandle) ?? $currentSite;

        // Get links ONLY for the current site
        $links = SocialMediaRecord::find()
            ->where(['siteId' => $site->id])
            ->orderBy(['sortOrder' => SORT_ASC])
            ->all();

        // Convert records to models for better template handling
        $linkModels = [];
        foreach ($links as $record) {
            $model = new SocialMedia();
            $model->setAttributes($record->getAttributes(), false);
            $linkModels[] = $model;
        }

        return $this->renderTemplate('social-media/index.twig', [
            'links' => $linkModels,
            'currentSite' => $site,
            'sites' => Craft::$app->getSites()->getAllSites()
        ]);
    }

    public function actionEdit(?int $id = null): Response
    {
        $link = new SocialMedia();

        // Get site from query param first, then fallback to current site
        $siteHandle = $this->request->getQueryParam('site')
            ?? Craft::$app->getSites()->getCurrentSite()->handle;

        $site = Craft::$app->getSites()->getSiteByHandle($siteHandle)
            ?? Craft::$app->getSites()->getCurrentSite();

        // Debug logging
        Craft::info(
            "Editing link in site: {$site->handle} (ID: {$site->id})",
            __METHOD__
        );

        if ($id) {
            $record = SocialMediaRecord::findOne($id);
            if (!$record) {
                throw new NotFoundHttpException('Link not found');
            }
            $link->setAttributes($record->getAttributes(), false);
            $template = 'social-media/edit.twig';
        } else {
            $link->siteId = $site->id;
            $template = 'social-media/new.twig';
        }

        return $this->renderTemplate($template, [
            'link' => $link,
            'settings' => \copain\craftsocialmedia\SocialMedia::getInstance()->getSettings(),
            'currentSite' => $site
        ]);
    }

    public function actionSave(): ?Response
    {
        $this->requirePostRequest();

        $id = $this->request->getBodyParam('id');
        $siteHandle = $this->request->getBodyParam('site')
            ?? $this->request->getQueryParam('site')
            ?? Craft::$app->getSites()->getCurrentSite()->handle;

        $site = Craft::$app->getSites()->getSiteByHandle($siteHandle)
            ?? Craft::$app->getSites()->getCurrentSite();

        $link = new SocialMedia();

        if ($id) {
            $record = SocialMediaRecord::findOne($id);
            if (!$record) {
                throw new NotFoundHttpException('Link not found');
            }
            $link->setAttributes($record->getAttributes(), false);
        }

        $link->setAttributes([
            'platform' => $this->request->getBodyParam('platform'),
            'name' => $this->request->getBodyParam('name'),
            'url' => $this->request->getBodyParam('url'),
            'enabled' => (bool)$this->request->getBodyParam('enabled', true),
            'siteId' => $site->id,
        ]);

        // Debug logging
        $settings = \copain\craftsocialmedia\SocialMedia::getInstance()->getSettings();

        if (!$id) {
            // Check for existing links with same platform
            $existingLink = SocialMediaRecord::find()
                ->where([
                    'platform' => $link->platform,
                    'siteId' => $link->siteId
                ])
                ->one();

            if ($existingLink && !$settings->allowMultipleLinks) {
                $link->addError('platform', Craft::t('social-media', 'This platform is already in use for this site.'));
                return $this->renderTemplate('social-media/new.twig', [
                    'link' => $link,
                    'settings' => $settings,
                    'currentSite' => $site
                ]);
            }
        }

        if (!$link->validate()) {
            $template = $id ? 'social-media/edit.twig' : 'social-media/new.twig';
            return $this->renderTemplate($template, [
                'link' => $link,
                'settings' => $settings,
                'currentSite' => $site
            ]);
        }

        try {
            if (!$id) {
                $record = new SocialMediaRecord();

                // Get the highest sort order across all sites
                $maxOrder = (int)SocialMediaRecord::find()
                    ->max('[[sortOrder]]');

                $record->sortOrder = $maxOrder + 1;
            } else {
                $record = SocialMediaRecord::findOne($id);
            }

            $record->setAttributes($link->getAttributes(), false);

            if (!$record->save(false)) {
                throw new \Exception('Could not save social media link');
            }

            Craft::$app->getSession()->setNotice(Craft::t('social-media', 'Link saved.'));
            return $this->redirect("social-media?site={$site->handle}");

        } catch (\Throwable $e) {
            Craft::error('Error saving social media link: ' . $e->getMessage(), __METHOD__);
            return null;
        }
    }

    public function actionDelete(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $id = $this->request->getRequiredBodyParam('id');
        $record = SocialMediaRecord::findOne($id);

        if (!$record || !$record->delete()) {
            return $this->asJson(['success' => false]);
        }

        return $this->asJson(['success' => true]);
    }

    public function actionReorder(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $ids = $this->request->getRequiredBodyParam('ids');
        if (is_string($ids)) {
            $ids = json_decode($ids, true);
        }

        if (!is_array($ids)) {
            return $this->asJson(['success' => false]);
        }

        try {
            $transaction = Craft::$app->getDb()->beginTransaction();

            foreach ($ids as $index => $id) {
                Craft::$app->getDb()->createCommand()
                    ->update(
                        '{{%socialmedia_links}}',
                        ['sortOrder' => $index + 1],
                        ['id' => $id]
                    )
                    ->execute();
            }

            $transaction->commit();
            return $this->asJson(['success' => true]);

        } catch (\Throwable $e) {
            if (isset($transaction)) {
                $transaction->rollBack();
            }
            return $this->asJson(['success' => false]);
        }
    }
}