<?php namespace Podcast;

require 'constants.php';
require 'autoload.php';

use Podcast\MyDB;
use Podcast\RSS;

class RSSTest extends \PHPUnit_Framework_TestCase
{
    public function testOutputItem() {
        $db = new MyDB();
        $rss = new RSS(null);

        $story_id = 358120407;
        $row = $db->query("SELECT * from webref_rss_items WHERE story_id=$story_id")->fetchArray();
        echo "Title: ".$row['title'];

        $xml = $rss->outputItem($row);
        echo "XML: ".$xml;
    }
}
?>