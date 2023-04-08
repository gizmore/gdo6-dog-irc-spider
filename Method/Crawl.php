<?php
declare(strict_types=1);
namespace GDO\DogIRCSpider\Method;

use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_UInt;
use GDO\Core\Logger;
use GDO\Dog\Dog;
use GDO\Dog\DOG_Message;
use GDO\Dog\DOG_Room;
use GDO\Dog\DOG_Server;
use GDO\Dog\DOG_User;
use GDO\DogIRC\DOG_IRCCommand;
use GDO\DogIRC\Method\Join;
use GDO\DogIRCSpider\DOG_RoomCrawl;

/**
 * Initiate an IRC Network crawl.
 *
 * @author gizmore
 */
final class Crawl extends DOG_IRCCommand
{

	private static ?DOG_Message $CRAWL_MESSAGE;

	##############
	### Method ###
	##############

	public int $priority = 90;

	public function getPermission(): ?string { return 'admin'; }


	public function gdoParameters(): array
	{
		return [
			GDT_Checkbox::make('reset')->initial('0'),
		];
	}

	##############
	### Config ###
	##############
	protected function getConfigServer(): array
	{
		return [
			GDT_UInt::make('crawl_min_users')->notNull()->initial('5'),
		];
	}

	############
	### Exec ###
	############

	public function irc_322(DOG_Server $server, DOG_User $me, string $userName, string $roomName, int $userCount, ?string $description): void
	{
		$room = DOG_Room::getOrCreate($server, $roomName, $description);
		if ($userCount >= $this->getConfigValueServer($server, 'crawl_min_users'))
		{
			if (!DOG_RoomCrawl::hasCrawled($room))
			{
				if (!$server->hasRoom($room))
				{
					$msg = self::$CRAWL_MESSAGE;
					$room->tempSet('irc_crawler', $msg->user);
					Join::make()->dogExecute($msg, $roomName);
				}
			}
		}
	}

	##############
	### Events ###
	##############

	public function dogExecute(DOG_Message $message, $reset = false): void
	{
		if ($message->server->tempGet('irc_crawler') !== null)
		{
			$message->rply('err_dog_already_crawling', [$message->server->renderName()]);
		}
		elseif ($reset)
		{
			DOG_RoomCrawl::truncateServer($message->server);
			$message->rply('msg_crawler_cleared');
		}
		else
		{
			self::$CRAWL_MESSAGE = $message;
			$message->server->tempSet('irc_crawler', $message->user);
			$this->getConnector($message)->send('LIST');
			$message->rply('msg_dog_crawl_inited', [$message->server->renderName()]);
		}
	}

	/**
	 * End of channel list.
	 * Reset temp vars.
	 */
	public function irc_323(DOG_Server $server, DOG_User $me, string $endOfList): void
	{
		/**
		 * @var DOG_User $user
		 */
		if ($user = $server->tempGet('irc_crawler'))
		{
			Logger::logCron('DONE!');
			$server->tempUnset('irc_crawler');
			$user->send(t('msg_dog_crawl_done'));
		}
	}

	/**
	 * If we join a channel, test if channel was discovered by crawler.
	 * If discovered, send a welcome message.
	 */
	public function dog_join(DOG_Server $server, DOG_User $user, DOG_Room $room): void
	{
		/** @var DOG_User $initiator * */
		if ($initiator = $room->tempGet('irc_crawler'))
		{
			DOG_RoomCrawl::crawled($room);
			$room->tempUnset('irc_crawler');
			$room->send(t('msg_crawler_joined', [
				$room->getName(), $server->getNickname(),
				$initiator->renderName()]));
			Dog::instance()->event('irc_crawler_joined', $server, $room);
		}
	}

	/**
	 * If dog get's kicked of a room, mark his crawl status
	 */
	public function dog_kicked(DOG_Server $server, DOG_User $user, DOG_Room $room): void
	{
		DOG_RoomCrawl::kicked($room);
	}

}
