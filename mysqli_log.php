<?php

namespace torokp\mysqli_log;

class mysqli_log extends \mysqli {

    function log_error($query) {
        global $sys;
        $debug_trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        //var_dump($debug_trace);
        $caller = $debug_trace[1];
        $file = substr($caller["file"], strrpos($caller["file"], '/') + 1);
        $line = $caller["line"];
        $error = $this->error;

        $error_query = "INSERT INTO ERRsql SET "
                . "appID = '{$sys["appID"]}', "
                . "errQuery = '" . parent::real_escape_string($query) . "', "
                . "errText  = '" . parent::real_escape_string($error) . "', "
                . "errDate  = NOW(), "
                . "errFile = '" . parent::real_escape_string($file) . "', "
                . "errLine = '" . parent::real_escape_string($line) . "' ";
        parent::query($error_query);

        //echo $this->error;
    }

    function query($query, $resultmode = MYSQLI_STORE_RESULT) {
        try {
            return parent::query($query, $resultmode);
        } catch (Exception $e) {
            $this->log_error($query);
            return false;
        }
    }

    function queryDie($query, $resultmode = MYSQLI_STORE_RESULT) {
        try {
            return parent::query($query, $resultmode);
        } catch (Exception $e) {
            die($this->log_error($query));
            return false;
        }
    }

    function execute($query, $resultmode = MYSQLI_STORE_RESULT) {
        return $this->query($query, $resultmode);
    }

    function escape($s) {
        return $this->real_escape_string($s);
    }    
}

/* SQL:

CREATE TABLE IF NOT EXISTS `ERRsql` (
  `appID` enum('APP1','APP2','APP3') COLLATE utf8_general_ci NOT NULL,
  `errID` int(10) unsigned NOT NULL,
  `errQuery` text COLLATE utf8_general_ci NOT NULL,
  `errText` text COLLATE utf8_general_ci NOT NULL,
  `errDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `errFile` varchar(50) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `errLine` mediumint(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE `ERRsql`
  ADD PRIMARY KEY (`errID`), ADD KEY `appID` (`appID`);

ALTER TABLE `ERRsql`
  MODIFY `errID` int(10) unsigned NOT NULL AUTO_INCREMENT;

*/
