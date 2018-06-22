<?php
require 'constants.php';
require 'key.php';
require 'autoload.php';

use Podcast\NPRShow;
use Podcast\RSS;

error_log(strftime("%F  %T", time()) . " trim begins.");
error_log("----------------------------------------------");

$date = isset($argv[1]) ? date_create($argv[1]) : default_date();  // YYYY-MM-DD
error_log("Trimming shows for date: " . $date->format('Y-m-d'));

$shows = NPRShow::get_shows();

foreach ($shows as $show) {
    $rss = new RSS($show);
    echo $rss->trimFeed($date);
}

error_log(strftime("%F  %T", time()) . " update completes.\n\n");

function default_date() {
    $today = new \DateTime();
    $default_interval = '-30 day';
    $date = $today->modify($default_interval);
    error_log('Set trim to default date of today ' . $default_interval);
    return $date;
}

?>
