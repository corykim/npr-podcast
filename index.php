<?
require 'constants.php';
require 'key.php';
require 'autoload.php';

use Podcast\NPRShow;
use Podcast\RSS;

$shows = NPRShow::get_shows();
$podcast_name = null;

if (isset($_SERVER['REQUEST_URI'])) {
    $podcast_name = $_SERVER['REQUEST_URI'];
    $podcast_name = preg_replace('/^\//', '', $podcast_name);
} else if ($argv && isset($argv[1])) {
    $podcast_name = $argv[1];
}

error_log("Comparing $podcast_name with ". basename(__FILE__));

if ($podcast_name && !endswith($podcast_name, basename(__FILE__))) {
    error_log("Getting podcast $podcast_name");

    if (!isset($shows[$podcast_name])) {
        http_response_code(404);
        error_log("No podcast named $podcast_name");
        exit(0);
    } else {
        $show = $shows[$podcast_name];
        $rss = new RSS($show);
        header("Content-Type: application/xml; charset=utf-8");
        echo $rss->getFeed();
        exit(0);
    }
}


function endswith($string, $test) {
    $strlen = strlen($string);
    $testlen = strlen($test);
    if ($testlen > $strlen) return false;
    return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
}

?>

<html>
    <h1>Podcasts</h1>
    <ul>
        <?
    foreach (array_keys($shows) as $show_code) {
        echo "<li><a href='/{$show_code}'>$show_code</a></li>\n";
    }
        ?>
    </ul>
</html>