<?php
date_default_timezone_set('America/New_York');
include 'animation.php';
include 'settings/config.php';
include 'settings/settings.php';

$allQuotes = file("quotes/all.quotes"); 
$shortQuoteSelected = false;
while (!$shortQuoteSelected) {
    $selectedQuote = $allQuotes[rand(0, count($allQuotes) - 1)];
    if (strlen($selectedQuote) < 100) $shortQuoteSelected = true;
}

// get current temperature and current daily high
$currentConditions = json_decode(cURL($weatherAPIURL . $weatherAPIKey));
$currentTemperature = round($currentConditions->currently->temperature);
$tempColor = cURL("{$temperatureColorAPI}/?temperature=" . $currentTemperature);

// current / daily conditions
$currentWeatherSummary = $currentConditions->minutely->summary;
$currentWeatherDailySummary = $currentConditions->hourly->summary;
$sunriseTime = date("g:i a", $currentConditions->daily->data[0]->sunriseTime);
$sunsetTime = date("g:i a", $currentConditions->daily->data[0]->sunsetTime);

// high temp
$todaysHigh = round($currentConditions->daily->data[0]->temperatureHigh);
$todaysHighTime = date("g:i a", $currentConditions->daily->data[0]->temperatureHighTime);
$tempHighColor = cURL("{$temperatureColorAPI}/?temperature=" . $todaysHigh);

// low temp
$todaysLow = round($currentConditions->daily->data[0]->temperatureLow);
$todaysLowTime = date("g:i a", $currentConditions->daily->data[0]->temperatureLowTime);
$tempLowColor = cURL("{$temperatureColorAPI}/?temperature=" . $todaysLow);

// conditions this hour
$hourlyConditions = array_slice($currentConditions->hourly->data,0,11);

$hourlyConditionsParsed = array();
foreach($hourlyConditions as $hourlyCondition) {
    
  // clear
  $hourlyCondition->color = '#EDEEF0';

  // rain
  if ($hourlyCondition->icon == 'rain' || $hourlyCondition->icon == 'sleet' || $hourlyCondition->icon == 'snow') {
    $hourlyCondition->color = '#4A80C7';
  }
        
  // cloudy
  if ($hourlyCondition->icon == 'cloudy' || $hourlyCondition->icon == 'fog' || $hourlyCondition->icon == 'wind') $hourlyCondition->color = '#B5BECA'; 
    
  // partly cloudy
  if ($hourlyCondition->icon == 'partly-cloudy-day' || $hourlyCondition->icon == 'partly-cloudy-night') $hourlyCondition->color = '#D5DAE2';
  $hourlyConditionsParsed[] = $hourlyCondition;
}

// have to deal with if we're already past sunset etc for which sunrise / set to pass below
$now = time();
$nextSunsetTime = $currentConditions->daily->data[0]->sunsetTime;
$nextSunriseTime = $currentConditions->daily->data[0]->sunriseTime;
if ($now > $nextSunriseTime) $nextSunriseTime = $currentConditions->daily->data[1]->sunriseTime;

$animation = new Animation($currentTemperature, $now, $nextSunsetTime, $nextSunriseTime, date('z') + 1, $currentConditions->currently->icon);
$testAnimation = $_GET['testAnimation'];
list ($currentAnimation, $currentSound) = $animation->getImage($testAnimation);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
	    <title>Inspriational Weather Panel</title>
	    <meta http-equiv="X-UA-Compatible" content="chrome=1">
	    <meta http-equiv="refresh" content="300">
	    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	    <link rel="shortcut icon" href="favicon.ico" />
        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link rel="stylesheet" href="css/styles.css?v=1.0">
        <meta http-equiv="refresh" content="300">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
        <script>
            window.thisAnimation = 'animated/<?=$currentAnimation?>.js';
            window.thisSound = 'audio/<?=$currentSound?>.wav';
        </script>
	    <script type="text/javascript" src="files/oop.js"></script>
	    <script type="text/javascript" src="files/tools.js"></script>
	    <script type="text/javascript" src="files/cookie.js"></script>
	    <script type="text/javascript" src="files/palette.js"></script>
	    <script type="text/javascript" src="files/bitmap.js"></script>
	    <script type="text/javascript" src="files/scenes.js"></script>
	    <script type="text/javascript" src="files/tween.js"></script>
	    <script type="text/javascript" src="files/framecount.js"></script>
	    <script type="text/javascript" src="files/main.js"></script>
	    <script type="text/javascript" src="/js/skycons.js"></script>
	    <script type="text/javascript" src="/js/jquery.textfill.min.js"></script>
        <script>
            function startTime() {
              var date = new Date();
              var hours = date.getHours();
              var minutes = date.getMinutes();
              var ampm = hours >= 12 ? 'PM' : 'AM';
              hours = hours % 12;
              hours = hours ? hours : 12; // the hour '0' should be '12'
              minutes = minutes < 10 ? '0'+minutes : minutes;
              var strTime = hours + ':' + minutes + ' ' + ampm;
              document.getElementById('time').innerHTML =  strTime;
              setTimeout(startTime, 1000);
            }
            function checkTime(i) {
              if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
              return i;
            }
        </script>
        <style>
        #time {
            color:white;
            position: absolute;
            bottom: 10px;
            right: 20px;
            background: black;
            z-index: 10;
            padding: 10px;
            font-size: 100px
        }
        </style>
    </head>
    <body onload="startTime()">
        <div class="row">
            <div class="col-sm-6">
                <canvas id="mycanvas" width="650" height="500"></canvas>
	            <div id="weather-info-container"><br/><br/>
                    <h1 style="display:inline; color: <?=$tempColor?>"><?=$currentTemperature?>°</h1> 
                    <span id="daily-temps">[<span style="color: <?=$tempHighColor?>">HIGH: <?=$todaysHigh?>°</span> / <span style="color: <?=$tempLowColor?>">LOW: <?=$todaysLow?>°</span>]</span>
                    <h5 id="sunrise"><span class="sunriseText">Sunrise @ <?=$sunriseTime?></span> / <span class="sunsetText">Sunset @ <?=$sunsetTime?></span></h5>
                    <h4 style="max-width: 550px;"><?=$currentWeatherSummary?></h4><br/>
                    <h5 style="max-width: 550px;"><?=$currentWeatherDailySummary?></h5>                
	            </div>
	           
               <div id="hourly-precipitation" class="row">
                   <div class="col-sm-12" style="position: fixed; bottom: 50px;">
                       <h5>Hourly Precipitation</h5>
                   </div>
                   <div id="conditions-hourly-container" class="row">
                    <?php
                    foreach($hourlyConditionsParsed as $conditionHour) {
                    ?>
                      <div class="col-sm-1 hourly-time-container">
                         <div class="hourly-time" style="background-color: <?=$conditionHour->color?>; text-shadow: 0px 0px 0px #000;"><?=date('g a', $conditionHour->time)?></div>
                      </div>
                    <?php } ?>
                   </div>

                    <h2 id="today-date"><?=date('D, F jS Y')?></h2>

                   
                    <div id="quote-message">
                        <?=$selectedQuote?>
                    </div>
                   
                   <canvas id="skycon-canvas" width="128" height="128"></canvas>
               </div>
	            <div id="container" style="display:none;">
		            <div id="d_scene_selector" style="display:none;"></div>
		            <div id="palette_display"></div>
		            <div id="d_loading"></div>
		            <div id="d_options"></div>
	            </div>
            </div>
            <div class="shadow"></div>
            <div id="events-list" class="col-sm-6"></div>
        </div>
	    <script language="JavaScript">
		    if (document.addEventListener) {
		    
			    // Good browsers
			    document.addEventListener( "DOMContentLoaded", function() {
				    document.removeEventListener( "DOMContentLoaded", arguments.callee, false );
				    CC.init();
			    }, false );

			    // Just in case
			    window.addEventListener( "load", function() {
				    window.removeEventListener( "load", arguments.callee, false );
				    CC.init();
			    }, false );

			    window.addEventListener( "resize", function() {
				    CC.handleResize();
			    }, false );
		    } 
		    
            jQuery(document).ready(function(){
            
                // get the skycon animated
                var skycons = new Skycons({"color": "white"});
                skycons.add(document.getElementById("skycon-canvas"), '<?=$currentConditions->currently->icon?>');
                skycons.play();
                (function($) {
                    $.fn.textfill = function(maxFontSize) {
                        maxFontSize = parseInt(maxFontSize, 10);
                        return this.each(function(){
                            var ourText = $("span", this),
                                parent = ourText.parent(),
                                maxHeight = parent.height(),
                                maxWidth = parent.width(),
                                fontSize = parseInt(ourText.css("fontSize"), 10),
                                multiplier = maxWidth/ourText.width(),
                                newSize = (fontSize*(multiplier-0.1));
                            ourText.css(
                                "fontSize", 
                                (maxFontSize > 0 && newSize > maxFontSize) ? 
                                    maxFontSize : 
                                    newSize
                            );
                        });
                    };
                })(jQuery);
                jQuery.getJSON( "http://calendar.kevinhinds.net/events.json", function( data ) {
                  var items = [];
                  var dateKey = '';
                  var dateCount = 0;
                  jQuery.each( data, function( key, val ) {
                    if (dateCount < 2) {
                        items.push( "<li class='date-title'><h3>" + key + "</h3></li>");
                        jQuery.each( val, function( eventKey, eventVal ) {
                            if (eventVal[0] == '12:00 am') {
                                items.push( "<li class='date-details'><h4 class='all-day-event'>" + eventVal[1] + "</h4></li>" );
                            } else {
                                items.push( "<li class='date-details'><h5 class=''>" + eventVal[0] + "</h5>" + eventVal[1] + "</li>" );
                            }        
                        });
                    }             
                    dateCount = dateCount + 1;
                  });
                 
                  jQuery( "<ul/>", {
                    "class": "hinds-events",
                    html: items.join( "" )
                  }).appendTo( "#events-list" );
                }); 
            });
	    </script>
	    <h1 id="time"></h1>
    </body>
</html>
