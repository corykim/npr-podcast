<?php namespace Podcast;
/**
 * Created by IntelliJ IDEA.
 * User: cory
 * Date: 10/30/14
 * Time: 8:23 PM
 */

class MyDB extends \SQLite3 {
    function __construct()
    {
        $this->open(DB_FILE);
        $this->busyTimeout(500);
    }

    function log_entry($msg, $type='INFO', $other=null) {
        $sql = "INSERT INTO audit_log (type, message, other) VALUES(?, ?, ?)";
        $stmt = $this->prepare($sql);
        $stmt->bindParam(1, $type);
        $stmt->bindParam(2, $msg);
        $stmt->bindParam(3, $other);
        $result = $stmt->execute();
        if (!$result) {
            error_log(error_get_last());
        }
    }
}

?>