<?php
namespace GDO\DogIRCSpider\Test;

use GDO\DogIRC\Test\IRCTestCase;
use GDO\DogIRCSpider\Method\Crawl;
use function PHPUnit\Framework\assertStringContainsString;

/**
 * Try to crawl.
 * Requires a server with some rooms on irc.giz.org
 *
 * @author gizmore
 */
final class CrawlerTest extends IRCTestCase
{

	public function testCrawler()
	{
		$server = $this->getServer();
		$method = Crawl::make();
		$method->setConfigValueServer($server, 'crawl_min_users', 1);
		$response = $this->ircPrivmsg('irc.crawl', null, 1500000);
		assertStringContainsString('You can kick me or use', $response, 'Test if crawler discovers a new channel.');
	}

}
