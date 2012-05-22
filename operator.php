<?php
class Operator {
    const PAGER = 50;

    /**
     * help
     */
    public function execHelp() {
        echo 'List of commands:<br />';

        $ref = new ReflectionClass($this);
        $methods = $ref->getMethods();
        foreach($methods as $method) {
            /**
             * @var ReflectionMethod $method
             */
            $name = $method->getName();
            if(!preg_match('#exec.*#', $name))
                continue;
            echo mb_strtolower(substr($name, mb_strlen('exec'))).'<br />';
        }
    }

    /**
     * favorites
     * @param string $username
     * @param int $start
     */
    public function execFav($username = '', $start = 0) {

        $page = round(1 + (($start - 1) / self::PAGER));

        echo "Favorites of '$username' Page $page: <br />";

        // get xml
        $xml = simplexml_load_file("https://gdata.youtube.com/feeds/api/users/$username/favorites?max-results=".self::PAGER."&start-index=".($start + 1)."&v=2");
        foreach($xml->entry as $entry) {
            $id = (String)$entry->link['href'];

            // get correct id
            $url = parse_url($id);
            if(!array_key_exists('query', $url))
                continue;
            if(strpos($id, 'www.youtube.com') !== false) {
                parse_str($url['query'], $params);
                $id = $params['v'];
            } else {
                $parts = explode('/', $url['path']);
                $id = array_pop($parts);
            }

            $title = (String)$entry->title;
            echo '<a href="#" onclick="play(\''.$id.'\', $(this).text())">'.htmlspecialchars($title).'</a><br />';
        }

        if($start != 0)
            echo '<a href="#" onclick="exec(\'fav '.$username.' '.($start - self::PAGER).'\');"><< prev</a>&nbsp;&nbsp;&nbsp;';
        echo '<a href="#" onclick="exec(\'fav '.$username.' '.($start + self::PAGER).'\');">next >></a>';
    }

    /**
     * uploads
     * @param string $username
     * @param int $start
     */
    public function execUps($username = '', $start = 0) {

        $page = round(1 + (($start - 1) / self::PAGER));

        echo "Uploads of '$username' Page $page: <br />";

        // get xml
        $xml = simplexml_load_file("https://gdata.youtube.com/feeds/api/users/$username/uploads?max-results=".self::PAGER."&start-index=".($start + 1)."&v=2");
        foreach($xml->entry as $entry) {
            $id = (String)$entry->link['href'];

            // get correct id
            $url = parse_url($id);
            if(!array_key_exists('query', $url))
                continue;
            parse_str($url['query'], $params);
            $id = $params['v'];

            $title = (String)$entry->title;
            echo '<a href="#" onclick="play(\''.$id.'\', $(this).text())">'.htmlspecialchars($title).'</a><br />';
        }

        if($start != 0)
            echo '<a href="#" onclick="exec(\'ups '.$username.' '.($start - self::PAGER).'\');"><< prev</a>&nbsp;&nbsp;&nbsp;';
        echo '<a href="#" onclick="exec(\'ups '.$username.' '.($start + self::PAGER).'\');">next >></a>';
    }

    /**
     * search
     */
    public function execQuery() {

        // build search string
        $query = array();
        $start = 0;
        $i = 0;
        foreach(func_get_args() as $arg) {
            $i++;
            if($i == func_num_args() && is_numeric($arg))
                $start = $arg;
            else
                $query [] = $arg;
        }
        $query = implode(' ', $query);

        $page = round(1 + ($start / self::PAGER));

        echo "Search results for '$query' Page $page: <br />";

        $search = urlencode($query);

        // get xml
        $xml = simplexml_load_file("https://gdata.youtube.com/feeds/api/videos?max-results=".self::PAGER."&start-index=".($start + 1)."&v=2&q=$search");
        foreach($xml->entry as $entry) {
            $id = (String)$entry->link['href'];

            // get correct id
            $url = parse_url($id);
            if(!array_key_exists('query', $url))
                continue;
            parse_str($url['query'], $params);
            $id = $params['v'];

            $title = (String)$entry->title;
            echo '<a href="#" onclick="play(\''.$id.'\', $(this).text())">'.htmlspecialchars($title).'</a><br />';
        }

        if($start != 0)
            echo '<a href="#" onclick="exec(\'query '.$query.' '.($start - self::PAGER).'\');"><< prev</a>&nbsp;&nbsp;&nbsp;';
        echo '<a href="#" onclick="exec(\'query '.$query.' '.($start + self::PAGER).'\');">next >></a>';
    }
}