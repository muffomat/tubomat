$(function() {
    var $playBar = $('#playbar');
    $playBar.hide();
    var $input = $('#inputline');
    $input.keydown(function(e) {
        if(e.keyCode != 13)
            return;

        exec($input.val());
    });

    var $pause = $('#pause');
    var $resume = $('#resume');
    var $search = $('#search_overlay');
    var $player = $('#player_box');
    $pause.click(function() {
        pause();
    });
    $resume.click(function() {
        resume();
    });
    $search.mousemove(function(e) {
        var $info = $(this).find('.info');
        var left = e.pageX - 70;
        var width = $search.width();
        var percentage = left/width;
        $info.show();
        $info.css('left', left + 3 + 'px');

        // calculate text
        seconds = Math.floor(percentage * getPlayer().getDuration());
        $info.text(getMinuteStringBySeconds(seconds));
    });
    $search.mouseout(function() {
        $(this).find('.info').hide();
    });
    $search.click(function(e) {
        var left = e.pageX - 70;
        var width = $search.width();

        // calculate position
        searchToPercentage(left/width);
    });
    $player.mouseover(function() {
        $(this).data('hovered', true);
        $(this).addClass('hover');
    });
    $player.mouseout(function() {
        var $that = $(this);
        $that.data('hovered', false);
        setTimeout(
            function() {
                if($that.data('hovered'))
                    return;
                $that.removeClass('hover');
            },
            2000
        );
    });
});

var exec = function(command) {
    var $input = $('#inputline');
    $input.val(command);
    $('#results_2').html($('#results_1').html());
    $('#results_1').html('loading').load(
        'console.php',
        {
            command: command
        },
        function() {
            var $links = $('#results_1 .video_link');
            $links.each(function() {
                $(this).click(function() {
                    play($(this).attr('rel'), $(this).text());
                    return false;
                });
                $(this).mouseover(function() {
                    $('#player_box').addClass('hover').css('right', '0px');
                    $('#image').show().find('img').attr('src', 'http://img.youtube.com/vi/'+$(this).attr('rel')+'/hqdefault.jpg');
                });
                $(this).mouseout(function() {
                    $('#player_box').removeClass('hover');
                    if(!$('#player_box').data('shown'))
                        $('#player_box').css('right', '-1000px');
                    $('#image').hide();
                });
            });
        }
    );
}

var initBar = function() {
    var $playBar = $('#playbar');
    if($playBar.data('inited'))
        return;
    $playBar.show();
    $('#player_box').css('bottom', '50px').css('right', '0px').data('shown', true);
    $playBar.data('inited', true);
    setInterval('updateNavigator();', 150);
}

var getPlayer = function() {
    return document.getElementById('player');
}

var play = function(videoId, title) {
    if(typeof(title) == 'undefined')
        title = '';
    initBar();
    var player = getPlayer();
    player.loadVideoById(videoId, 0, 'small');
    $('#progress .middle').text(title);
}

var pause = function() {
    getPlayer().pauseVideo();
    updateNavigator();
}

var resume = function() {
    getPlayer().playVideo();
    updateNavigator();
}

var searchToPercentage = function(percentage) {
    var second = getPlayer().getDuration() * percentage;
    searchToSecond(second);
}

var searchToSecond = function(second) {
    getPlayer().seekTo(second, true);
}

var getMinuteStringBySeconds = function(seconds) {
    var mins = Math.floor(seconds / 60);
    var secs = Math.floor(seconds - (mins * 60));
    if(secs < 10)
        secs = "0" + secs;
    return mins + ':' + secs;
}

var updatePlayerButtons = function() {
    var status = getPlayer().getPlayerState();
    var $resume = $('#resume');
    var $pause = $('#pause');
    if(status == 1 || status == 3) {
        $pause.show();
        $resume.hide();
    } else {
        $pause.hide();
        $resume.show();
    }
}

var updateProgressBar = function() {
    var $total = $('#total');
    var $loaded = $('#loaded');
    var $playing = $('#playing');

    // get percentage of played
    var player = getPlayer();
    var duration = player.getDuration();
    var current = player.getCurrentTime();
    var percentage = current / duration;

    // resize played bar
    $playing.width($total.width() * percentage);

    // get percentage of loaded
    var total = player.getVideoBytesTotal();
    var loaded = player.getVideoBytesLoaded() + player.getVideoStartBytes();
    percentage = loaded / total;

    // resize loaded bar
    $loaded.width($total.width() * percentage);

    // reorder right positions
    var durationText = getMinuteStringBySeconds(duration);
    $rights = $('#progress .right');
    $rights.each(function() {
        $(this).css('left', ($total.width() - 10 - $(this).width()) + 'px').text(durationText);
    });

    // reorder middle positions
    $middles = $('#progress .middle');
    $middles.each(function() {
        $(this).css('left', (($total.width() / 2) - ($(this).width() / 2)) + 'px');
    });

    // change left tests
    $lefts = $('#progress .left');
    $lefts.text(getMinuteStringBySeconds(current));

}

var updateNavigator = function() {
    if(!$('body').data('loaded')) {
        getPlayer().addEventListener('onError', 'onPlayerError');

        $('body').data('loaded', true);
    }
    updatePlayerButtons();
    updateProgressBar();
}

var onPlayerError = function(code) {
    console.log(code);
    if(code == 2)
        alert('Invalid video parameters given');
    else if(code == 100)
        alert('Requested video could not be found');
    else if(code == 101 || code == 105)
        alert('Playback not allowed');
    else if(code == 150)
        alert('Video not available in this country');
    else
        alert('Error ' + code);
}
