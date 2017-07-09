<?php
require 'constants.php';
require 'key.php';
require 'autoload.php';

use Podcast\NPRShow;
use Podcast\RSS;

$start_date = isset($argv[1]) ? $argv[1] : null;  // YYYY-MM-DD
$end_date   = isset($argv[2]) ? $argv[2] : null;  // YYYY-MM-DD

if (!empty($start_date)) {
    error_log("Getting shows for date: $date");
} else {
    fwrite(STDERR, "Usage: " . __FILE__ . " <start_date> <end_date>  (dates in YYYY-MM-DD format)");
}

// set the date cursor
if (empty($end_date)) {
    // default to today's date
    $date = new \DateTime();
} else {
    $date = date_create($end_date);
}

$stop = date_create($start_date);
$increment = new \DateInterval('P1D');

error_log(strftime("%F  %T", time()) . " backfill begins.");
error_log("----------------------------------------------");

error_log("Starting backfill from " .$stop->format('Y-m-d'). " to " .$date->format('Y-m-d'));
error_log("----------------------------------------------");

while ($date >= $stop) {
    $npr_date = $date->format('Ymd');
    error_log("Getting shows for date: " .$date->format('Y-m-d') . "  (" .$npr_date.")" );
    $shows = NPRShow::get_shows();

    foreach ($shows as $show) {
        $rss = new RSS($show);
        echo $rss->updateFeed($npr_date);
    }
    $date->sub($increment);
}

error_log(strftime("%F  %T", time()) . " backfill completes.\n\n");
?>

