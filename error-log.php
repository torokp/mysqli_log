<?php
namespace torokp\mysqli_log;

class error_log {
    public static $db = NULL;
	
    public static function msg($error, $query = "") {
        global $sys;
        $debug_trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        //var_dump($debug_trace);
        $caller = $debug_trace[count($debug_trace)-1];
        $file = substr($caller["file"], strlen(MAIN_DIR)+1);
        $line = $caller["line"];

        $error_query = "INSERT INTO ERRmsg SET "
                . "appID = '{$sys["appID"]}', "
                . "errQuery = '" . ERR::$db->real_escape_string($query) . "', "
                . "errText  = '" . ERR::$db->real_escape_string($error) . "', "
                . "errDate  = NOW(), "
                . "errFile = '" . ERR::$db->real_escape_string($file) . "', "
                . "errLine = '" . ERR::$db->real_escape_string($line) . "' ";
                
        SYS::$db->query($error_query);
    }

}

/* SQL:

CREATE TABLE IF NOT EXISTS `ERRmsg` (
  `appID` enum('MozaHonlap','Telepesek','Verseny','Calendar','PhotoRepo') COLLATE utf8_hungarian_ci NOT NULL,
  `errID` int(10) unsigned NOT NULL,
  `errQuery` text COLLATE utf8_hungarian_ci NOT NULL,
  `errText` text COLLATE utf8_hungarian_ci NOT NULL,
  `errDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `errFile` varchar(50) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
  `errLine` mediumint(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

ALTER TABLE `ERRmsg`
  ADD PRIMARY KEY (`errID`), ADD KEY `appID` (`appID`);

ALTER TABLE `ERRmsg`
  MODIFY `errID` int(10) unsigned NOT NULL AUTO_INCREMENT;

*/