<?php
declare(strict_types=1);
namespace GDO\DogIRCSpider;

use GDO\Core\GDO;
use GDO\Date\GDT_DateTime;
use GDO\Date\Time;
use GDO\Dog\DOG_Room;
use GDO\Dog\DOG_Server;
use GDO\Dog\GDT_Room;

final class DOG_RoomCrawl extends GDO
{

	public static function hasCrawled(DOG_Room $room): bool
	{
		return self::table()->countWhere("crawl_room={$room->getID()}") > 0;
	}

	public static function crawled(DOG_Room $room): self
	{
		return self::blank([
			'crawl_room' => $room->getID(),
			'crawl_joined' => Time::getDate(),
		])->replace();
	}

	public static function kicked(DOG_Room $room): ?self
	{
		if ($crawl = self::getById($room->getID()))
		{
			return $crawl->saveVar('crawl_kicked', Time::getDate());
		}
		return null;
	}

	public static function truncateServer(DOG_Server $server): bool
	{
		self::table()->deleteQuery()->
		where("SELECT 1 FROM dog_room WHERE room_id = crawl_room AND room_server={$server->getID()}")->
		exec();
		return true;
	}

	public function gdoCached(): bool { return false; }

	public function gdoColumns(): array
	{
		return [
			GDT_Room::make('crawl_room')->primary(),
			GDT_DateTime::make('crawl_joined'),
			GDT_DateTime::make('crawl_kicked'),
		];
	}

}
