<?php
namespace GDO\DogIRCSpider;

use GDO\Core\GDO;
use GDO\Dog\GDT_Room;
use GDO\DB\GDT_Enum;
use GDO\Dog\DOG_Room;

final class DOG_RoomCrawl extends GDO
{
    const DISCOVERED = 'discovered';
    const JOINED = 'JOINED';
    const KICKED = 'KICKED';
    
    public function gdoCached() { return false; }
    
    public function gdoColumns()
    {
        return array(
            GDT_Room::make('crawl_room')->primary(),
            GDT_Enum::make('crawl_status')->enumValues(self::DISCOVERED, self::JOINED, self::KICKED)->notNull()->initial(self::DISCOVERED),
        );
    }
    
    public static function crawled(DOG_Room $room)
    {
        
        
    }
    
}
