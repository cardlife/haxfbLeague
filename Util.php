<?php

//print('<link rel="stylesheet" type="text/css" href="style.css"/>');



class Util {
	
	public static $currWeek = 2;

    private static $counter = 0;
    private static $pageList = array();


	/*public static function printNav() {
		print("<div id = 'navigation'>");
		print("<div class = 'title'> Menu </div>");
		print("<ul>");
		print("<li> <a href = 'home.php'> Home </a> </li>");
		print("<li> <a href = 'admin.php'> Admin </a> </li>");
		print("<li> <a href = 'schedule.php?DisplayWeek=true'> Schedule </a> </li>");
		print("</ul>");
		print("</div>");
		
	} */
	public static function getCurrWeek() {
		return Util::$currWeek;
	}

    public static function getCurrentPageUrl() {
        $pageURL = 'http';
        if( isset($_SERVER["HTTPS"]) ) {
            if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    public static function getURL($hostName, array $params) {
        $url = parse_url($hostName);
        $queryString = $url["query"];
        parse_str($queryString, $queryArr);
        $queryArr = array_merge($queryArr, $params);
        $url["query"] = http_build_query( $queryArr);
        return http_build_url(null, $url);

    }

    public static function buildCurrentURL(array $params = array()) {
        return self::getURL(self::getCurrentPageUrl(), $params);
    }

    public static function getPageUrl($pageName) {
        if(!isset(self::$pageList[$pageName]) && self::isEmpty(self::$pageList[$pageName])) {
            $pageUrl = get_option($pageName);
            self::$pageList[$pageName] = $pageUrl;
        } else {
            $pageUrl = self::$pageList[$pageName];
        }
        return $pageUrl;
    }

    public static function updatePageUrl($pageName, $pageUrl) {
        $origPageUrl = self::getPageUrl($pageName);
        if($origPageUrl != $pageUrl) {
            update_option($pageName, $pageUrl);
            self::$pageList[$pageName] = $pageUrl;
        }
    }

    public static function updatePageUrlToCurrent($pageName) {
        self::updatePageUrl($pageName, self::getCurrentPageUrl());
    }



    public static function isEmpty($val) {
        return !($val!=null && $val != "");
    }

    public function getCounter() {
        return self::$counter++;
    }


    public static function getDataTableOptions(array $optionArray) {
        $optionString = "";
        foreach($optionArray as $optionKey => $optionValue) {
            $optionString .= "\"".$optionKey."\" : " .$optionValue.",";
        }
        if($optionString != "") {
            $optionString = substr($optionString, 0, -1); //strip trailing comma
        }
        return $optionString;
    }


    public static function dispStats($queryList, $view, $paginate = false) {
        $statsTableId = "dispStats".self::getCounter();

        print("<table id=\"$statsTableId\">");

        /** @var View $view */
        $dummyStats = $view->getStatList();
        $statTableHeader = $view->displayTableHeader();


        print("<thead><th>Name</th>{$statTableHeader}</thead>");
        print("<tbody>");
        foreach($queryList as $row) {
            print("<tr>");
            if(isset($row['teamname'])) {
                $name = $row['teamname'];
                $id = $row['teamid'];
            }
            if(isset($row['playername'])) {
                $name = $row['playername'];
                $id = $row['playerid'];
            }
            if(isset($row['matchid'])) {
                $id = $row['matchid'];
            }

            $statistics = new Stats($row);

            $myView = $statistics->getView($view->getDisplayName());
            $statTableValue = $myView->displayTableValues();

            if(isset($row['teamname']) && isset($id)) {
                $team = new Team($id);
                echo("<td>{$team->getNameWithLink()}</td>");
            }
            if(isset($row['playername']) && isset($id)) {
                $player = new Player($id);
                print("<td>{$player->getNameWithLink()}</td>");
            } if(isset($row['matchid']) && isset($id)) {
                $match = new Matchup($id);
                print("<td>{$match->getMatchupWithLink()}</td>");
            }
            print($statTableValue);

            print("</tr>");
        }
        print("</tbody> </table>");

        $disableOptions = array();
        if(!$paginate) {
           $disableOptions = array("bPaginate" => "false", "bFilter" => "false", "bInfo" => "false" );
        }

        $disableOptions = $disableOptions + $view->getDefaultSortValue();

        print('<div id = "'.$statsTableId.'Pager"> </div>');


        //                    jQuery("#'.$statsTableId.' [title!=\"\"]").qtip();


        print('<script>
               jQuery(document).ready(function()
                 {
                    jQuery("#'.$statsTableId.'")
                    .dataTable({'.self::getDataTableOptions($disableOptions).'})
                }
            );
        </script>');


    }



}

if (!function_exists('http_build_url'))
{
    define('HTTP_URL_REPLACE', 1);              // Replace every part of the first URL when there's one of the second URL
    define('HTTP_URL_JOIN_PATH', 2);            // Join relative paths
    define('HTTP_URL_JOIN_QUERY', 4);           // Join query strings
    define('HTTP_URL_STRIP_USER', 8);           // Strip any user authentication information
    define('HTTP_URL_STRIP_PASS', 16);          // Strip any password authentication information
    define('HTTP_URL_STRIP_AUTH', 32);          // Strip any authentication information
    define('HTTP_URL_STRIP_PORT', 64);          // Strip explicit port numbers
    define('HTTP_URL_STRIP_PATH', 128);         // Strip complete path
    define('HTTP_URL_STRIP_QUERY', 256);        // Strip query string
    define('HTTP_URL_STRIP_FRAGMENT', 512);     // Strip any fragments (#identifier)
    define('HTTP_URL_STRIP_ALL', 1024);         // Strip anything but scheme and host

    // Build an URL
    // The parts of the second URL will be merged into the first according to the flags argument.
    //
    // @param   mixed           (Part(s) of) an URL in form of a string or associative array like parse_url() returns
    // @param   mixed           Same as the first argument
    // @param   int             A bitmask of binary or'ed HTTP_URL constants (Optional)HTTP_URL_REPLACE is the default
    // @param   array           If set, it will be filled with the parts of the composed url like parse_url() would return
    function http_build_url($url, $parts=array(), $flags=HTTP_URL_REPLACE, &$new_url=false)
    {
        $keys = array('user','pass','port','path','query','fragment');

        // HTTP_URL_STRIP_ALL becomes all the HTTP_URL_STRIP_Xs
        if ($flags & HTTP_URL_STRIP_ALL)
        {
            $flags |= HTTP_URL_STRIP_USER;
            $flags |= HTTP_URL_STRIP_PASS;
            $flags |= HTTP_URL_STRIP_PORT;
            $flags |= HTTP_URL_STRIP_PATH;
            $flags |= HTTP_URL_STRIP_QUERY;
            $flags |= HTTP_URL_STRIP_FRAGMENT;
        }
        // HTTP_URL_STRIP_AUTH becomes HTTP_URL_STRIP_USER and HTTP_URL_STRIP_PASS
        else if ($flags & HTTP_URL_STRIP_AUTH)
        {
            $flags |= HTTP_URL_STRIP_USER;
            $flags |= HTTP_URL_STRIP_PASS;
        }

        // Parse the original URL
        $parse_url = parse_url($url);

        // Scheme and Host are always replaced
        if (isset($parts['scheme']))
            $parse_url['scheme'] = $parts['scheme'];
        if (isset($parts['host']))
            $parse_url['host'] = $parts['host'];

        // (If applicable) Replace the original URL with it's new parts
        if ($flags & HTTP_URL_REPLACE)
        {
            foreach ($keys as $key)
            {
                if (isset($parts[$key]))
                    $parse_url[$key] = $parts[$key];
            }
        }
        else
        {
            // Join the original URL path with the new path
            if (isset($parts['path']) && ($flags & HTTP_URL_JOIN_PATH))
            {
                if (isset($parse_url['path']))
                    $parse_url['path'] = rtrim(str_replace(basename($parse_url['path']), '', $parse_url['path']), '/') . '/' . ltrim($parts['path'], '/');
                else
                    $parse_url['path'] = $parts['path'];
            }

            // Join the original query string with the new query string
            if (isset($parts['query']) && ($flags & HTTP_URL_JOIN_QUERY))
            {
                if (isset($parse_url['query']))
                    $parse_url['query'] .= '&' . $parts['query'];
                else
                    $parse_url['query'] = $parts['query'];
            }
        }

        // Strips all the applicable sections of the URL
        // Note: Scheme and Host are never stripped
        foreach ($keys as $key)
        {
            if ($flags & (int)constant('HTTP_URL_STRIP_' . strtoupper($key)))
                unset($parse_url[$key]);
        }


        $new_url = $parse_url;

        return
            ((isset($parse_url['scheme'])) ? $parse_url['scheme'] . '://' : '')
            .((isset($parse_url['user'])) ? $parse_url['user'] . ((isset($parse_url['pass'])) ? ':' . $parse_url['pass'] : '') .'@' : '')
            .((isset($parse_url['host'])) ? $parse_url['host'] : '')
            .((isset($parse_url['port'])) ? ':' . $parse_url['port'] : '')
            .((isset($parse_url['path'])) ? $parse_url['path'] : '')
            .((isset($parse_url['query'])) ? '?' . $parse_url['query'] : '')
            .((isset($parse_url['fragment'])) ? '#' . $parse_url['fragment'] : '')
            ;
    }
}



?>