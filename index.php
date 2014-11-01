<?
require 'constants.php';
require 'key.php';
require 'autoload.php';

use Podcast\NPRShow;
use Podcast\RSS;

$podcast_name = null;

if (isset($_SERVER['REQUEST_URI'])) {
    $podcast_name = $_SERVER['REQUEST_URI'];
    $podcast_name = preg_replace('/^\//', '', $podcast_name);
} else if ($argv && isset($argv[1])) {
    $podcast_name = $argv[1];
} else {
    http_response_code(404);
    error_log("Could not determine podcast name");
    exit(0);
}

error_log("Getting podcast $podcast_name");

$shows = NPRShow::get_shows();
if (!isset($shows[$podcast_name])) {
    http_response_code(404);
    error_log("No podcast named $podcast_name");
    exit(0);
} else {
    $show = $shows[$podcast_name];
    $rss = new RSS($show);
    header("Content-Type: application/xml; charset=ISO-8859-1");
    echo $rss->getFeed();
}


?>