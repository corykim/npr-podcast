<?php namespace Podcast;

class RSS
{
    /**
     * The database table where RSS items are stored
     * @var string
     */
    public $itemsTable = "webref_rss_items";

    /**
     * @param $show An NPRShow instance
     */
    function __construct($show)
    {
        $this->show = $show;
        $db = new MyDB();
        $this->myDB = $db;
    }

    function __destroy()
    {
        $this->myDB->close();
        unset($this->myDB);
    }

    public function updateFeed($date)
    {
        $msg = "Updating ".$this->show->id.': '.$this->show->url;
        $this->myDB->log_entry($msg);
        error_log($msg);
        $stories = $this->show->get_stories($date);

        $sql = "INSERT INTO webref_rss_items (story_id, rss_id, title, description, link, media_url, media_duration, pub_date, show_date, feature_order) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->myDB->prepare($sql);

        if (isset($stories['list']) && isset($stories['list']['story'])) {
            $counter = 1;
            foreach($stories['list']['story'] as $story) {
                $id = $story['id'];
                $title = $story['title']['$text'];
                error_log("story: $id: $title");

                $media_url = isset($story['audio'][0]['format']['mp3']) ? $story['audio'][0]['format']['mp3'][0]['$text'] : null;
                if ($media_url) {
                    $sql = "SELECT COUNT(*) FROM " . $this->itemsTable . " WHERE story_id=$id";
                    $query = $this->myDB->query($sql);
                    if ($query->fetchArray()[0] == 0) {
                        error_log("creating story.");

                        error_log("media_url: $media_url");
                        $mp3_url = $this->get_data($media_url);
                        error_log("mp3_url: $mp3_url");

                        $show_date = $this->to_sqldate(date_parse($story['pubDate']['$text']));
                        error_log($story['pubDate']['$text'] . ' parsed to '.$show_date);

                        $stmt->bindParam(1, $id); // story_id
                        $stmt->bindParam(2, $this->show->id); // rss_id
                        $stmt->bindParam(3, $title); // title
                        $stmt->bindParam(4, $story['teaser']['$text']); // description
                        $stmt->bindParam(5, $story['link'][0]['$text']); // link
                        $stmt->bindParam(6, $mp3_url); // media url
                        $stmt->bindParam(7, $story['audio'][0]['duration']['$text']); // media duration
                        $stmt->bindParam(8, $story['pubDate']['$text']); // pub_date
                        $stmt->bindParam(9, $show_date); // story_date
                        $stmt->bindParam(10, $counter); // order featured in show
                        $result = $stmt->execute();
                        if (!$result) {
                            error_log("Error executing query " . error_get_last());
                        }

                        $counter = $counter + 1;
                    } else {
                        error_log("story already exists.");
                    }
                } else {
                    error_log("story audio is not currently available.");
                }
            }
        } else {
            error_log("show has no stories");
        }
    }

    public function getFeed()
    {
        return $this->getDetails() . $this->getItems();
    }

    protected function getDetails()
    {
        $detailsTable = "webref_rss_details";
        $query = "SELECT * FROM ". $detailsTable." WHERE ID=".$this->show->id;
//        error_log("getDetails query: $query");

        $result = $this->myDB->query($query);

        while($row = $result->fetchArray())
        {
            $details = '<?xml version="1.0" encoding="UTF-8" ?>
    <rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0">
        <channel>
            <title><![CDATA['. $row["title"] .']]></title>
            <link>'. $this->escape_xml($row['link']) .'</link>
            <language>'. $row['language'] .'</language>
            <copyright><![CDATA[℗ &amp; © 2014 '. $row["author"] .']]></copyright>
            <itunes:subtitle>A podcast from NPR</itunes:subtitle>
            <itunes:author><![CDATA['. $row["author"] .']]></itunes:author>
            <itunes:summary><![CDATA['. $row["description"] .']]></itunes:summary>
            <description><![CDATA['. $row["description"] .']]></description>
            <itunes:owner>
                <itunes:name>C.K.</itunes:name>
                <itunes:email>ck@example.com</itunes:email>
            </itunes:owner>
            <itunes:image href="'.$this->escape_xml($row['image_url']).'"/>
            <itunes:explicit>no</itunes:explicit>

            <image>
                <title><![CDATA['. $row['image_title'] .']]></title>
                <url>'. $this->escape_xml($row['image_url']) .'</url>
                <link>'. $this->escape_xml($row['image_link']) .'</link>
                <width>'. $row['image_width'] .'</width>
                <height>'. $row['image_height'] .'</height>
             </image>';
        }

        return $details;
    }

    protected function getItems()
    {

        $query = "SELECT * FROM ". $this->itemsTable." WHERE RSS_ID=".$this->show->id. ' ORDER BY show_date DESC, id ASC';
//        error_log("getItems query: $query");

        $result = $this->myDB->query($query);
        $items = '';
        while($row = $result->fetchArray())
        {
            $items .= $this->outputItem($row) . "\n";
        }
        $items .= '</channel>
    </rss>';
        return $items;
    }

    public function outputItem($row) {
        $minutes = floor($row['media_duration']/60);
        $seconds = $row['media_duration'] - ($minutes * 60);

        // we alter the date so the podcast shows the episodes in the order they were aired
        $order_date = new \DateTime($row['pub_date']);
        $increment = ($row['feature_order'] + 0) * 5;
        $interval_spec = "PT" . $increment . "M";
        $interval = new \DateInterval($interval_spec);

        date_add(date_time_set($order_date, 0, 0, 0), $interval);

        $item = '<item>
            <title><![CDATA['. $row["title"] .']]></title>
            <itunes:summary><![CDATA['. $row["description"] .']]></itunes:summary>

            <enclosure
                url="'.$this->escape_xml($row['media_url']).'"
                type="audio/mpeg"
                length="'.($row['media_duration']*1000).'"
            />

            <pubDate>'.$order_date->format("D, d M Y H:i:s O").'</pubDate>

            <link>'. $this->escape_xml($row["link"]) .'</link>
            <description><![CDATA['. $row["description"] .']]></description>

            <itunes:subtitle>A podcast from NPR</itunes:subtitle>
            <guid>'.$this->generate_guid($row).'</guid>
            <itunes:duration>'.$minutes.':'.$seconds.'</itunes:duration>
            <itunes:keywords>radio, news</itunes:keywords>
        </item>';

        return $item;
    }

    private function generate_guid($row) {
        return 'corykim:'.$this->show->id.':'.$row['story_id'].':'.
            $this->escape_xml($row['media_url']);
    }

    private function escape_xml($string) {
        return str_replace('&', '&amp;', $string);
    }

    /* gets the data from a URL */
    private function get_data($url) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function to_sqldate($date) {
        return sprintf("%04d-%02d-%02d", $date['year'], $date['month'], $date['day']);
    }

    public function to_sqldatetime($date) {
        return sprintf("%04d-%02d-%02d %02d:%02d:%02d", $date['year'], $date['month'], $date['day'], $date['hour'], $date['minute'], $date['second']);
    }
}

?>