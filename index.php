<?php
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', 'On');
?>
<html>
    <head>
        <title>YT</title>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <script type="text/javascript" src="/jquery.js"></script>
        <script type="text/javascript" src="/yt.js"></script>
    </head>
    <body>
        <div id="main">
            <div id="console">
                <input type="text" id="inputline" />
                <div id="results">
                    <div id="results_1">
                    </div>
                    <div id="results_2">
                    </div>
                </div>
            </div>
            <div id="playbar">
                <div id="pause" class="btn pause" style="display:none;">=</div>
                <div id="resume" class="btn resume">&raquo;</div>
                <div id="progress_block">
                    <div id="progress">
                        <div id="total">
                            <div class="left">0:00</div>
                            <div class="middle"></div>
                            <div class="right">0:00</div>
                        </div>
                        <div id="loaded"></div>
                        <div id="playing">
                            <div class="left">0:00</div>
                            <div class="middle"></div>
                            <div class="right">0:00</div>
                        </div>
                        <div id="search_overlay">
                            <div class="info" style="display:none;">1:23</div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="player_box">
                <object type="application/x-shockwave-flash" id="player" data="http://www.youtube.com/apiplayer?version=3&amp;video_id=u1zgFlCw8Aw&amp;enablejsapi=1&amp;playerapiid=ytplayer" width="640" height="360">
                    <param name="allowScriptAccess" value="always">
                    <param name="bgcolor" value="#ffffff">
                </object>
            </div>
        </div>
    </body>
</html>