<?php
namespace GDO\DogIRCSpider\Method;

use GDO\Dog\DOG_Message;
use GDO\DogIRC\DOG_IRCCommand;
use GDO\Dog\DOG_Server;
use GDO\DB\GDT_UInt;
use GDO\Dog\DOG_Room;
use GDO\Core\Logger;

/**
 * Initiate a crawl.
 * @author gizmore
 *
 */
final class Crawl extends DOG_IRCCommand
{
    public $priority = 90;
    public $trigger = 'crawl_network';
    
    public function getPermission() { return 'admin'; }
    
    ##############
    ### Config ###
    ##############
    public function getConfigServer()
    {
        return array(
            GDT_UInt::make('crawl_min_users')->notNull()->initial('5'),
        );
    }
    
    
    ############
    ### Exec ###
    ############
    public function dogExecute(DOG_Message $message)
    {
        if ($message->server->tempGet('irc_crawler'))
        {
            $message->rply('err_dog_already_crawling', [$message->server->displayName()]);
        }
        else
        {
            $message->server->tempSet('irc_crawler', '1');
            $this->getConnector($message)->send("LIST");
            $message->rply('msg_dog_crawl_inited', [$message->server->displayName()]);
        }
    }
    
    ##############
    ### Events ###
    ##############
    public function irc_322(DOG_Server $server, $me, $roomName, $userCount, $description)
    {
        echo $userCount."\n";
        if ($userCount >= $this->getConfigValueServer($server, 'crawl_min_users'))
        {
            $room = DOG_Room::getOrCreate($server, $roomName, $description);
        }
    }
    
    public function irc_323(DOG_Server $server, $endOfList)
    {
        $server->tempUnset('irc_crawler');
        Logger::logCron('DONE!');
    }
    
}
