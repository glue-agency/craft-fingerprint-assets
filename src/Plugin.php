<?php

namespace GlueAgency\FingerprintAssets;

use craft\base\Element;
use craft\elements\Asset;
use craft\events\ModelEvent;
use yii\base\Event;

class Plugin extends \craft\base\Plugin
{
    public function init()
    {
        parent::init();

        Event::on(
            Asset::class,
            Element::EVENT_BEFORE_SAVE,
            function (ModelEvent $event) {
                if (!$event->isNew) {
                    $this->fingerprintAsset($event->sender);
                }
            }
        );
    }

    protected function fingerprintAsset(Asset $asset)
    {
        $location = $asset->newLocation;
        $locationParts = explode('.', $location);
        $locationExtension = array_pop($locationParts);
        $fingerprint = 'f'.time();

        // Filename has no extension.
        if (!count($locationParts)) {
            return;
        }

        // Remove existing fingerprint if necessary.
        if (
            preg_match("/f\d+/", end($locationParts)) === 1
            && count($locationParts) > 1
        ) {
            array_pop($locationParts);
        }

        array_push($locationParts, $fingerprint, $locationExtension);

        $asset->newLocation = implode('.', $locationParts);
    }
}
