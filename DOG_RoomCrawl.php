<?php
namespace GDO\DogIRCSpider;

use GDO\Core\GDO;
use GDO\Dog\GDT_Room;
use GDO\DB\GDT_UInt;

final class DOG_RoomCrawl extends GDO
{
    public function gdoColumns()
    {
        return array(
            GDT_Room::make('crawl_room')->primary(),
            GDT_UInt::make('crawl_status')->bytes(1)->initial("0"),
        );
    }
    
}
