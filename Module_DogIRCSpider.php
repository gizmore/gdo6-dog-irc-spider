<?php
namespace GDO\DogIRCSpider;

use GDO\Core\GDO_Module;

/**
 * Crawls IRC networks for channels and joins them.
 *
 * @version 6.10.4
 * @since 6.10.0
 * @author gizmore
 */
final class Module_DogIRCSpider extends GDO_Module
{

	public function getDependencies(): array { return ['DogIRC']; }

	public function onLoadLanguage(): void { $this->loadLanguage('lang/spider'); }

	public function getClasses(): array
	{
		return [
			DOG_RoomCrawl::class,
		];
	}

}
