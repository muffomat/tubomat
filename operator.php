<?php
class Operator {
    const PAGER = 50;

    /**
     * display this help
     */
    public function execHelp() {
        echo 'List of commands:<br />-------------------------<br />';

        $ref = new ReflectionClass($this);
        $methods = $ref->getMethods();
        foreach($methods as $method) {
            /**
             * @var ReflectionMethod $method
             */
            $name = $method->getName();
            if(!preg_match('#exec.*#', $name))
                continue;
            $command = mb_strtolower(substr($name, mb_strlen('exec')));
            $descriptions = explode("\n", $method->getDocComment());
            array_shift($descriptions);
            array_pop($descriptions);
            foreach($descriptions as $key => $desc) {
                $desc = trim($desc);
                if(($pos = strpos($desc, '@')) !== false && $pos <= 2)
                    unset($descriptions[$key]);
                else {
                    $descriptions[$key] = trim(mb_substr($desc, strpos($desc, '*') + 1));
                }
            }
            echo $command;
            foreach($descriptions as $desc)
                echo '<br />&nbsp;&nbsp;&nbsp;'.$desc;
            echo '<br />';
        }
    }

    private function showList($titlePrefix, $commandPrefix, $commandFormat, $query, $offset) {
        $page = round(1 + ($offset / self::PAGER));

        echo "$titlePrefix '$query' Page $page: <br />";

        // build command
        $command = 'https://gdata.youtube.com'.sprintf($commandFormat, urlencode($query), $offset + 1, self::PAGER);

        // get xml
        $xml = simplexml_load_file($command);
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
            echo '<a class="video_link" rel="'.$id.'" href="#"">'.htmlspecialchars($title).'</a>';
        }

        if($offset != 0)
            echo '<a href="#" onclick="return exec(\''.$commandPrefix.' '.$query.' '.($offset - self::PAGER).'\');"><< prev</a>&nbsp;&nbsp;&nbsp;';
        echo '<a href="#" onclick="return exec(\''.$commandPrefix.' '.$query.' '.($offset + self::PAGER).'\');">next >></a>';
    }

    /**
     * list favorites of user
     * usage: fav [username] [entry offset]
     * @param string $username
     * @param int $start
     */
    public function execFav($username = '', $start = 0) {
        $this->showList('Favorites of', 'fav', '/feeds/api/users/%1$s/favorites?max-results=%3$s&start-index=%2$s&v=2', $username, $start);
    }

    /**
     * list uploads of user
     * usage: ups [username] [entry offset]
     * @param string $username
     * @param int $start
     */
    public function execUps($username = '', $start = 0) {
        $this->showList('Uploads of', 'ups', '/feeds/api/users/%1$s/uploads?max-results=%3$s&start-index=%2$s&v=2', $username, $start);
    }

    /**
     * query by a search term
     * usage: query [term ...] [(int)entry offset]
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

        $this->showList('Search results for', 'query', '/feeds/api/videos?max-results=%3$s&start-index=%2$s&v=2&q=%1$s', $query, $start);
    }
}