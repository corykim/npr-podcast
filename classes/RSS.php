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

        $sql = "INSERT INTO webref_rss_items (story_id, rss_id, title, description, link, media_url, media_duration, pub_date) VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->myDB->prepare($sql);

        if (isset($stories['list']) && isset($stories['list']['story'])) {
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

                        $stmt->bindParam(1, $id); // story_id
                        $stmt->bindParam(2, $this->show->id); // rss_id
                        $stmt->bindParam(3, $title); // title
                        $stmt->bindParam(4, $story['teaser']['$text']); // description
                        $stmt->bindParam(5, $story['link'][0]['$text']); // link
                        $stmt->bindParam(6, $mp3_url); // media url
                        $stmt->bindParam(7, $story['audio'][0]['duration']['$text']); // media duration
                        $stmt->bindParam(8, $story['pubDate']['$text']); // pub_date
                        $result = $stmt->execute();
                        if (!$result) {
                            error_log("Error executing query " . error_get_last());
                        }
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

    private function getDetails()
    {
        $detailsTable = "webref_rss_details";
        $query = "SELECT * FROM ". $detailsTable." WHERE ID=".$this->show->id;
//        error_log("getDetails query: $query");

        $result = $this->myDB->query($query);

        while($row = $result->fetchArray())
        {
            // save some details for the item list
            $this->image_title = $row['image_title'];
            $this->image_url = $row['image_url'];
            $this->image_width = $row['image_width'];
            $this->image_height = $row['image_height'];
            $this->author = $row["author"];

            $details = '<?xml version="1.0" encoding="UTF-8" ?>
    <rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0">
        <channel>
            <title><![CDATA['. $row["title"] .']]></title>
            <link>'. $this->escape_xml($row['link']) .'</link>
            <language>'. $row['language'] .'</language>
            <copyright><![CDATA[℗ &amp; © 2014 '. $row["author"] .']]></copyright>
            <itunes:subtitle>A podcast from NPR</itunes:subtitle>
            <itunes:author><![CDATA['. $this->author .']]></itunes:author>
            <itunes:summary><![CDATA['. $row["description"] .']]></itunes:summary>
            <description><![CDATA['. $row["description"] .']]></description>
            <itunes:owner>
                <itunes:name>C.K.</itunes:name>
                <itunes:email>ck@example.com</itunes:email>
            </itunes:owner>
            <itunes:image href="'.$this->escape_xml($this->image_url).'"/>
            <itunes:explicit>no</itunes:explicit>

            <image>
                <title><![CDATA['. $this->image_title .']]></title>
                <url>'. $this->escape_xml($this->image_url) .'</url>
                <link>'. $this->escape_xml($row['image_link']) .'</link>
                <width>'. $this->image_width .'</width>
                <height>'. $this->image_height .'</height>
             </image>';
        }



        return $details;
    }

    private function getItems()
    {

        $query = "SELECT * FROM ". $this->itemsTable." WHERE RSS_ID=".$this->show->id;
//        error_log("getItems query: $query");

        $result = $this->myDB->query($query);
        $items = '';
        while($row = $result->fetchArray())
        {
            $minutes = floor($row['media_duration']/60);
            $seconds = $row['media_duration'] - ($minutes * 60);
            $items .= '
        <item>
            <title><![CDATA['. $row["title"] .']]></title>
            <itunes:author><![CDATA['. $this->author .']]></itunes:author>
            <itunes:summary><![CDATA['. $row["description"] .']]></itunes:summary>

            <enclosure
                url="'.$this->escape_xml($row['media_url']).'"
                type="audio/mpeg"
                length="'.($row['media_duration']*1000).'"
            />

            <pubDate>'.$row['pub_date'].'</pubDate>

            <link>'. $this->escape_xml($row["link"]) .'</link>
            <description><![CDATA['. $row["description"] .']]></description>

            <itunes:subtitle>A podcast from NPR</itunes:subtitle>
            <guid>'.$this->escape_xml($row['media_url']).'</guid>
            <itunes:duration>'.$minutes.':'.$seconds.'</itunes:duration>
            <itunes:keywords>radio, news</itunes:keywords>
        </item>';
        }
        $items .= '</channel>
    </rss>';
        return $items;
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

}

?>