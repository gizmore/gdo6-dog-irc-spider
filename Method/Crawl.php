<?php
namespace GDO\DogIRCSpider\Method;

use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;

final class Crawl extends DOG_Command
{
    public $priority = 90;
    public $group = "IRC";
    public $trigger = 'crawl';
    
    public function getPermission() { return 'admin'; }
    
    public function dogExecute(DOG_Message $message)
    {
        
    }
    
}
