<?php
require 'constants.php';
require 'key.php';
require 'autoload.php';

use Podcast\NPRShow;
use Podcast\RSS;

error_log(strftime("%F  %T", time()) . " update begins.");
error_log("----------------------------------------------");

$date = isset($argv[1]) ? $argv[1] : null;  // YYYYMMDD
if (!empty($date)) {
    error_log("Getting shows for date: $date");
}

$shows = NPRShow::get_shows();

foreach ($shows as $show) {
    $rss = new RSS($show);
    echo $rss->updateFeed($date);
}

error_log(strftime("%F  %T", time()) . " update completes.\n\n");
?>
