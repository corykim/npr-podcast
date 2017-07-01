<?php namespace Podcast;

class NPRShow extends RestClient {
    /**
     * @param $id   The database ID for the show
     * @param $url  The NPR url for the show
     * @param $order The order in which the show should be presented
     */
    function __construct($id, $url, $order) {
        $this->id = $id;
        $this->url = $url;
        $this->order = $order;
    }

    /**
     * Gets all the stories for a given show
     *
     * @param $date Optional, specifies the day of the show to retrieve
     * @return mixed|null An associative array representing the show data
     */
    public function get_stories($date) {
        $url = $this->url;
        if ($date) {
            $url = $url."&sort=featured&date=$date";
            error_log("Set date to $date");
        }

        error_log("Retrieving shows from: $url");
        return $this->rest_call($url);
    }

    /**
     * Gets a list of all the shows in the database
     *
     * @return array
     */
    public static function get_shows() {
        $shows = array();

        $db = new MyDB();
        $sql = 'SELECT id, npr_show_id, code FROM webref_rss_details';
        $results = $db->query($sql);

        $counter = 0;
        while ($row = $results->fetchArray()) {
            $shows[$row['code']] = new NPRShow($row['id'], 'http://api.npr.org/query?output=JSON&apiKey='.NPR_API_KEY.'&id='.$row['npr_show_id'], $counter);
            $counter = $counter + 1;
        }

        return $shows;
    }
}

// NPR API reference: http://www.npr.org/templates/apidoc/inputReference.php
// Programs: http://api.npr.org/list?id=3004

// Morning Edition:  http://api.npr.org/query?apiKey=API_KEY&sort=featured&id=3
// ATC:  http://api.npr.org/query?apiKey=API_KEY&sort=featured&id=2

?>