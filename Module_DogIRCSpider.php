<?php
namespace GDO\DogIRCSpider;

use GDO\Core\GDO_Module;

/**
 * Crawls IRC networks for channels and joins them.
 * @author gizmore
 * @version 6.10
 * @since 6.10
 */
final class Module_DogIRCSpider extends GDO_Module
{
    public function getDependencies() { return ['DogIRC']; }
    
    public function onLoadLanguage() { return $this->loadLanguage('lang/spider'); }
    
    public function getClasses()
    {
        return array(
            'GDO\\DogIRCSpider\\DOG_RoomCrawl',
        );
    }
    
}
