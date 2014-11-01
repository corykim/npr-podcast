<?php namespace Podcast;

class NPRShow extends RestClient {
    function __construct($id, $url) {
        $this->id = $id;
        $this->url = $url;
    }

    public function get_stories($date) {
        $url = $this->url;
        if ($date) {
            $url = $url."&date=$date";
            error_log("Set date to $date");
        }

        error_log("Retrieving shows from: $url");
        return $this->rest_call($url);
    }

    public static function get_shows() {
        $shows = array(
            'npr-morning-edition' => new NPRShow(1, 'http://api.npr.org/query?id=3&output=JSON&apiKey='.NPR_API_KEY),
            'npr-all-things-considered' => new NPRShow(2, 'http://api.npr.org/query?id=2&output=JSON&apiKey='.NPR_API_KEY)
        );

        return $shows;
    }

}

// NPR API reference: http://www.npr.org/templates/apidoc/inputReference.php
// Programs: http://api.npr.org/list?id=3004

// Morning Edition:  http://api.npr.org/query?apiKey=MDE2MzQ5ODY0MDE0MDg4MTMwNzMxMzNkYQ001&id=3
// ATC:  http://api.npr.org/query?apiKey=MDE2MzQ5ODY0MDE0MDg4MTMwNzMxMzNkYQ001&id=2

?>