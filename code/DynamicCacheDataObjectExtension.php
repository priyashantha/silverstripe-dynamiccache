<?php

/**
 * Ensures that dataobjects are correctly flushed from the cache on save
 *
 * @author Damian Mooyman
 * @package dynamiccache
 */
class DynamicCacheDataObjectExtension extends DataExtension
{

    /**
     * Clear the entire dynamic cache once a dataobject has been saved.
     * Safe and dirty.
     *
     */
    public function onAfterWrite()
    {
        if (!DynamicCache::config()->cacheClearOnWrite) {
            return;
        }

        // Do not clear cache if object is Versioned. Only clear
        // when a user publishes.
        //
        // DynamicCacheControllerExtension already opts out of caching if 
        // on ?stage=Stage so this behaviour makes sense.
        //
        if ($this->hasLiveStage()) {
            return;
        }

        DynamicCache::inst()->clear();
    }

    /**
     * Clear the entire dynamic cache once a dataobject has been deleted.
     * Safe and dirty.
     *
     */
    public function onBeforeDelete()
    {
        if (!DynamicCache::config()->cacheClearOnWrite) {
            return;
        }
        DynamicCache::inst()->clear();
    }

    /**
     * Support Versioned::publish()
     * - Use case: SheaDawson Blocks module support
     */
    public function onBeforeVersionedPublish() {
        if (!DynamicCache::config()->cacheClearOnWrite) {
            return;
        }
        DynamicCache::inst()->clear();
    }

    /**
     * Support SiteTree::doPublish()
     */
    public function onAfterPublish() {
        if (!DynamicCache::config()->cacheClearOnWrite) {
            return;
        }
        DynamicCache::inst()->clear();
    }

    /**
     * Support HeyDay's VersionedDataObject extension
     * - Use case: DNADesign Elemental support
     */
    public function onAfterVersionedPublish() {
        if (!DynamicCache::config()->cacheClearOnWrite) {
            return;
        }
        DynamicCache::inst()->clear();
    }

    protected function hasLiveStage() {
        $class = $this->owner->class;
        // NOTE: Using has_extension over hasExtension as the former
        //       takes subclasses into account.
        $hasVersioned = $class::has_extension('Versioned');
        if (!$hasVersioned) {
            return false;
        }
        $stages = $this->owner->getVersionedStages();
        return $stages && in_array('Live', $stages);
    }
}
