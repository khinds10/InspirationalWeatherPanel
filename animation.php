<?php
/**
 * canvas tag animation
 
 * @author Kevin Hinds <kevin@kevinhinds.com>
 */
class Animation {

    public $conditionsList = array('clear-day', 'clear-night', 'partly-cloudy-day', 'partly-cloudy-night', 'cloudy', 'rain', 'sleet', 'snow', 'wind', 'fog');
    public $currentCondition = 'clear-day';
    
    public $timeOfDayList = array('sunrise', 'day', 'sunset', 'night');
    public $timeOfDay = 'day';
    
    public $timeOfYearList = array('summer', 'winter');
    public $timeOfYear = 'summer';
    
    public $currentTempList = array('cold', 'medium', 'hot');
    public $currentTemp = 'medium';
    
    public $currentAnimation = array('JungleWaterfall-Afternoon');
    public $currentSound = 'Ambient';
    public $soundMapping = array('ApproachingStorm-Day' => 'Ambient',
        'Castle-Day-Rain' => 'LightRain',
        'DeepForest-Day' => 'Creek',
        'DeepForest-Rain' => 'LightRain',
        'DesertHeatWave' => 'Heat',
        'ForestEdge-Day' => 'Lake',
        'HarborTown-Night' => 'Ambient',
        'HighlandRuins-Rain' => 'LightRain',
        'IceWind-Day' => 'Ambient',
        'IslandFires-Dusk' => 'Ambient',
        'JungleWaterfall-Afternoon' => 'RiverStream',
        'JungleWaterfall-Morning' => 'RiverStream',
        'JungleWaterFall-Night' => 'RiverStream',
        'JungleWaterfall-Rain' => 'RiverStream',
        'MagicMarsh-Night' => 'Ambient',
        'MirrorPond-Afternoon' => 'Ripples',
        'MirrorPond-Morning' => 'Ripples',
        'MirrorPond-Rain' => 'LightRain',
        'MountainFortress-Dusk' => 'Ambient',
        'MountainStorm-Day' => 'Ambient',
        'MountainStream-Day' => 'Ambient',
        'MountainStream-Morning' => 'RiverStream',
        'MoutainStream-Afternoon' => 'RiverStream',
        'MoutainStream-Night' => 'RiverStream',
        'PondRipples-Morning' => 'Ripples',
        'RoughSeas-Day' => 'Waves',
        'SeaScapeDay' => 'Seas',
        'Seascape-Fog' => 'Seas',
        'SeascapeSunset' => 'Seas',
        'WaterCityGates-Fog' => 'Seas',
        'WinterForest-Snow' => 'Ambient');
    
    /**
     * construct animation class with known info from darksky about outside weather
     * 
     * @param int $currentTemp, current temp *F
     * @param int $currentTime, current timestamp
     * @param int $nextSunsetTime, next coming sunset timestamp
     * @param int $nextSunriseTime, next coming sunrise timestamp
     * @param int $dayOfYear, day of the year 0-365
     * @param string $icon, darksky icon returned from API
     */
    public function __construct($currentTemp, $currentTime, $nextSunsetTime, $nextSunriseTime, $dayOfYear, $icon) {
    
        // temp extremes
        if ($currentTemp > 90) $this->currentTemp = 'hot';
        if ($currentTemp < 10) $this->currentTemp = 'cold';
    
        // flag is winter / summber by day of the year number
        if ($dayOfYear > 305) $this->timeOfYear = 'winter';
        if ($dayOfYear < 122) $this->timeOfYear = 'winter';

        // current skycon shown
        $this->currentCondition = $icon;
    
        // flag what part of the day we're in by compare current timestamps against sunrise and set
        $sunsetBegin = $nextSunsetTime - (60 * 60);
        $sunsetEnd = $nextSunsetTime + (60 * 60);
        $sunriseBegin = $nextSunriseTime - (60 * 60);
        $sunriseEnd = $nextSunriseTime + (60 * 60);
        if ($currentTime >= $sunriseBegin && $currentTime <= $sunriseEnd) $this->timeOfDay = 'sunrise';
        if ($currentTime >= $sunsetBegin && $currentTime <= $sunsetEnd) $this->timeOfDay = 'sunset';
        if ($currentTime > $sunsetEnd && $currentTime < $sunriseBegin) $this->timeOfDay = 'night';
    }
    
    /**
     * get the sound for the image selected
     */
    private function getSoundForImage($chosenAnimation) {
        $this->currentSound = $this->soundMapping[$chosenAnimation];
    }
    
    /**
     * get current animation based on all the known outside weather factors
     */
    public function getImage($testAnimation='') {
    
        // if test animation set just use that
        if ($testAnimation) {
            $this->getSoundForImage($testAnimation);
            return array($testAnimation, $this->currentSound);
        } 
        
        // get live animation based on current weather
        $currentWeather = "{$this->timeOfYear}-{$this->timeOfDay}-{$this->currentCondition}-{$this->currentTemp}";
        switch ($currentWeather) {
            case 'summer-sunrise-clear-day-cold':
            case 'summer-sunrise-clear-day-medium':
            case 'summer-sunrise-clear-day-hot':
                $this->currentAnimation = array('SeascapeSunset', 'MountainFortress-Dusk', 'IslandFires-Dusk', 'MagicMarsh-Night', 'MirrorPond-Morning', 'PondRipples-Morning');
                break;

            case 'summer-sunrise-clear-night-cold':
            case 'summer-sunrise-clear-night-medium':
            case 'summer-sunrise-clear-night-hot':
            case 'summer-sunrise-partly-cloudy-night-cold':
            case 'summer-sunrise-partly-cloudy-night-medium':
            case 'summer-sunrise-partly-cloudy-night-hot':
                $this->currentAnimation = array('JungleWaterfall-Night', 'HarborTown-Night', 'MagicMarsh-Night');
                break;

            case 'summer-sunrise-partly-cloudy-day-cold':
            case 'summer-sunrise-partly-cloudy-day-medium':
            case 'summer-sunrise-partly-cloudy-day-hot':
                $this->currentAnimation = array('ApproachingStorm-Day', 'MountainStream-Day');
                break;

            case 'summer-sunrise-cloudy-medium':
            case 'summer-sunrise-cloudy-cold':
            case 'summer-sunrise-cloudy-hot':
                $this->currentAnimation = array('ApproachingStorm-Day', 'PondRipples-Morning', 'IslandFires-Dusk', 'MountainFortress-Dusk', 'SeascapeSunset');
                break;

        
            case 'summer-sunrise-rain-cold':
            case 'summer-sunrise-rain-medium':
            case 'summer-sunrise-rain-hot':
            case 'summer-sunrise-sleet-cold':
            case 'summer-sunrise-sleet-medium':
            case 'summer-sunrise-sleet-hot':
                $this->currentAnimation = array('DeepForest-Rain', 'Castle-Day-Rain', 'JungleWaterfall-Rain', 'HighlandRuins-Rain', 'MirrorPond-Rain');
                break;                break;

            case 'summer-sunrise-snow-medium':
            case 'summer-sunrise-snow-cold':
            case 'summer-sunrise-snow-hot':
                $this->currentAnimation = array('WinterForest-Snow');
                break;

            case 'summer-sunrise-wind-cold':
            case 'summer-sunrise-wind-medium':
            case 'summer-sunrise-wind-hot':
                $this->currentAnimation = array('ApproachingStorm-Day', 'MountainFortress-Dusk', 'SeascapeSunset');
                break;

            case 'summer-sunrise-fog-medium':
            case 'summer-sunrise-fog-cold':
            case 'summer-sunrise-fog-hot':
                $this->currentAnimation = array('RoughSeas-Day', 'WaterCityGates-Fog', 'Seascape-Fog');
                break;

            case 'summer-day-clear-day-cold':
            case 'summer-day-clear-day-medium':
                $this->currentAnimation = array('DeepForest-Day', 'ForestEdge-Day', 'JungleWaterfall-Afternoon', 'JungleWaterfall-Morning', 'MirrorPond-Afternoon', 'MirrorPond-Morning');
                break;
                
            case 'summer-day-clear-day-hot':
                $this->currentAnimation = array('DesertHeatWave');
                break;

            case 'summer-day-clear-night-cold':
            case 'summer-day-clear-night-medium':
            case 'summer-day-clear-night-hot':
                $this->currentAnimation = array('JungleWaterfall-Night', 'HarborTown-Night', 'MagicMarsh-Night');
                break;

            case 'summer-day-partly-cloudy-day-cold':
            case 'summer-day-partly-cloudy-day-medium':
                $this->currentAnimation = array('DeepForest-Day', 'ForestEdge-Day', 'JungleWaterfall-Afternoon', 'JungleWaterfall-Morning', 'MirrorPond-Afternoon', 'MirrorPond-Morning');
                break;

            case 'summer-day-partly-cloudy-day-hot':
                $this->currentAnimation = array('DesertHeatWave');
                break;
                
            case 'summer-day-partly-cloudy-night-cold':
            case 'summer-day-partly-cloudy-night-medium':
            case 'summer-day-partly-cloudy-night-hot':
                $this->currentAnimation = array('JungleWaterfall-Night', 'HarborTown-Night', 'MagicMarsh-Night');
                break;

            case 'summer-day-cloudy-cold':
            case 'summer-day-cloudy-medium':
                $this->currentAnimation = array('DeepForest-Day', 'JungleWaterfall-Afternoon', 'MountainStream-Day');
                break;

            case 'summer-day-cloudy-hot':
                $this->currentAnimation = array('DesertHeatWave');
                break;

            case 'summer-day-rain-cold':
            case 'summer-day-rain-hot':
            case 'summer-day-rain-medium':
                $this->currentAnimation = array('DeepForest-Rain', 'Castle-Day-Rain', 'JungleWaterfall-Rain', 'HighlandRuins-Rain', 'MirrorPond-Rain');
                break;
                
            case 'summer-day-sleet-cold':
            case 'summer-day-sleet-medium':
            case 'summer-day-sleet-hot':
                $this->currentAnimation = array('DeepForest-Rain', 'Castle-Day-Rain', 'JungleWaterfall-Rain', 'HighlandRuins-Rain', 'MirrorPond-Rain');
                break;

            case 'summer-day-snow-cold':
            case 'summer-day-snow-medium':
            case 'summer-day-snow-hot':
                $this->currentAnimation = array('WinterForest-Snow');
                break;
                
            case 'summer-day-wind-cold':
            case 'summer-day-wind-medium':
            case 'summer-day-wind-hot':
                $this->currentAnimation = array('DeepForest-Day', 'ForestEdge-Day', 'JungleWaterfall-Afternoon', 'JungleWaterfall-Morning', 'MirrorPond-Afternoon', 'MirrorPond-Morning');
                break;

            case 'summer-day-fog-cold':
            case 'summer-day-fog-medium':
            case 'summer-day-fog-hot':
                $this->currentAnimation = array('RoughSeas-Day', 'WaterCityGates-Fog', 'Seascape-Fog');
                break;

            case 'summer-sunset-clear-day-cold':
            case 'summer-sunset-clear-day-medium':
            case 'summer-sunset-clear-day-hot':
                $this->currentAnimation = array('IslandFires-Dusk', 'MountainFortress-Dusk', 'SeascapeSunset');
                break;

            case 'summer-sunset-clear-night-cold':
            case 'summer-sunset-clear-night-medium':
            case 'summer-sunset-clear-night-hot':
                $this->currentAnimation = array('IslandFires-Dusk', 'MagicMarsh-Night', 'MountainFortress-Dusk', 'SeascapeSunset');
                break;

            case 'summer-sunset-partly-cloudy-day-cold':
            case 'summer-sunset-partly-cloudy-day-medium':
            case 'summer-sunset-partly-cloudy-day-hot':
                $this->currentAnimation = array('IslandFires-Dusk', 'MountainFortress-Dusk', 'SeascapeSunset');
                break;

            case 'summer-sunset-partly-cloudy-night-cold':
            case 'summer-sunset-partly-cloudy-night-medium':
            case 'summer-sunset-partly-cloudy-night-hot':
                $this->currentAnimation = array('IslandFires-Dusk', 'MagicMarsh-Night', 'MountainFortress-Dusk', 'SeascapeSunset');
                break;

            case 'summer-sunset-cloudy-cold':
            case 'summer-sunset-cloudy-hot':
            case 'summer-sunset-cloudy-medium':
                $this->currentAnimation = array('IslandFires-Dusk', 'MountainFortress-Dusk', 'SeascapeSunset');
                break;

            case 'summer-sunset-rain-cold':
            case 'summer-sunset-rain-hot':
            case 'summer-sunset-rain-medium':
                $this->currentAnimation = array('DeepForest-Rain', 'Castle-Day-Rain', 'JungleWaterfall-Rain', 'HighlandRuins-Rain', 'MirrorPond-Rain');
                break;

            case 'summer-sunset-sleet-cold':
            case 'summer-sunset-sleet-medium':
            case 'summer-sunset-sleet-hot':
                $this->currentAnimation = array('DeepForest-Rain', 'Castle-Day-Rain', 'JungleWaterfall-Rain', 'HighlandRuins-Rain', 'MirrorPond-Rain');
                break;

            case 'summer-sunset-snow-cold':
            case 'summer-sunset-snow-medium':
            case 'summer-sunset-snow-hot':
                $this->currentAnimation = array('WinterForest-Snow');
                break;

            case 'summer-sunset-wind-cold':
            case 'summer-sunset-wind-medium':
            case 'summer-sunset-wind-hot':
                $this->currentAnimation = array('IslandFires-Dusk', 'MagicMarsh-Night', 'MountainFortress-Dusk', 'SeascapeSunset');
                break;
                
            case 'summer-sunset-fog-cold':
            case 'summer-sunset-fog-medium':
            case 'summer-sunset-fog-hot':
                $this->currentAnimation = array('RoughSeas-Day', 'WaterCityGates-Fog', 'Seascape-Fog');
                break;

            case 'summer-night-clear-day-cold':
            case 'summer-night-clear-day-medium':
            case 'summer-night-clear-day-hot':
            case 'summer-night-clear-night-cold':
            case 'summer-night-clear-night-medium':
            case 'summer-night-clear-night-hot':
                $this->currentAnimation = array('MagicMarsh-Night', 'JungleWaterfall-Night');
                break;

            case 'summer-night-partly-cloudy-day-cold':
            case 'summer-night-partly-cloudy-day-medium':
            case 'summer-night-partly-cloudy-day-hot':
            case 'summer-night-partly-cloudy-night-cold':
            case 'summer-night-partly-cloudy-night-medium':
            case 'summer-night-partly-cloudy-night-hot':
                $this->currentAnimation = array('HarborTown-Night', 'JungleWaterfall-Night');
                break;

            case 'summer-night-cloudy-cold':
            case 'summer-night-cloudy-medium':
            case 'summer-night-cloudy-hot':
                $this->currentAnimation = array('HarborTown-Night', 'JungleWaterfall-Night');
                break;
                
            case 'summer-night-rain-cold':
            case 'summer-night-rain-medium':
            case 'summer-night-rain-hot':
            case 'summer-night-sleet-cold':
            case 'summer-night-sleet-medium':
            case 'summer-night-sleet-hot':
                $this->currentAnimation = array('HarborTown-Night', 'JungleWaterfall-Night');
                break;

            case 'summer-night-snow-cold':
            case 'summer-night-snow-medium':
            case 'summer-night-snow-hot':
                $this->currentAnimation = array('WinterForest-Snow');
                break;

            case 'summer-night-wind-cold':
            case 'summer-night-wind-medium':
            case 'summer-night-wind-hot':
                $this->currentAnimation = array('HarborTown-Night', 'JungleWaterfall-Night');
                break;

            case 'summer-night-fog-cold':
            case 'summer-night-fog-medium':
            case 'summer-night-fog-hot':
                $this->currentAnimation = array('HarborTown-Night', 'JungleWaterfall-Night');
                break;

            case 'winter-sunrise-clear-day-cold':
            case 'winter-sunrise-clear-day-medium':
            case 'winter-sunrise-clear-day-hot':
            case 'winter-sunrise-clear-night-cold':
            case 'winter-sunrise-clear-night-medium':
            case 'winter-sunrise-clear-night-hot':
            case 'winter-sunrise-partly-cloudy-day-cold':
            case 'winter-sunrise-partly-cloudy-day-medium':
            case 'winter-sunrise-partly-cloudy-day-hot':
            case 'winter-sunrise-partly-cloudy-night-cold':
            case 'winter-sunrise-partly-cloudy-night-medium':
            case 'winter-sunrise-partly-cloudy-night-hot':
            case 'winter-sunrise-cloudy-cold':
            case 'winter-sunrise-cloudy-medium':
            case 'winter-sunrise-cloudy-hot':
                $this->currentAnimation = array('MountainFortress-Dusk', 'ApproachingStorm-Day', 'SeascapeSunset');
                break;

            case 'winter-sunrise-rain-cold':
            case 'winter-sunrise-rain-medium':
            case 'winter-sunrise-rain-hot':
            case 'winter-sunrise-sleet-cold':
            case 'winter-sunrise-sleet-medium':
            case 'winter-sunrise-sleet-hot':
                $this->currentAnimation = array('Seascape-Fog', 'WaterCityGates-Fog', 'RoughSeas-Day');
                break;

            case 'winter-sunrise-snow-cold':
            case 'winter-sunrise-snow-medium':
            case 'winter-sunrise-snow-hot':
                $this->currentAnimation = array('WinterForest-Snow');
                break;

            case 'winter-sunrise-wind-cold':
            case 'winter-sunrise-wind-medium':
            case 'winter-sunrise-wind-hot':
            case 'winter-sunrise-fog-cold':
            case 'winter-sunrise-fog-medium':
            case 'winter-sunrise-fog-hot':
                $this->currentAnimation = array('Seascape-Fog', 'WaterCityGates-Fog', 'RoughSeas-Day');
                break;

            case 'winter-day-clear-day-cold':
            case 'winter-day-clear-day-medium':
            case 'winter-day-clear-day-hot':
            case 'winter-day-clear-night-cold':
            case 'winter-day-clear-night-medium':
            case 'winter-day-clear-night-hot':
                $this->currentAnimation = array('MountainStream-Morning', 'MountainStream-Day');
                break;
        
            case 'winter-day-partly-cloudy-day-medium':
            case 'winter-day-partly-cloudy-day-hot':
            case 'winter-day-partly-cloudy-night-cold':
            case 'winter-day-partly-cloudy-night-medium':
            case 'winter-day-partly-cloudy-night-hot':
            case 'winter-day-cloudy-cold':
            case 'winter-day-cloudy-medium':
            case 'winter-day-cloudy-hot':
            case 'winter-day-partly-cloudy-day-cold':
                $this->currentAnimation = array('ApproachingStorm-Day', 'MountainStream-Day');
                break;

            case 'winter-day-rain-cold':
            case 'winter-day-rain-medium':
            case 'winter-day-rain-hot':
                $this->currentAnimation = array('Seascape-Fog', 'WaterCityGates-Fog', 'RoughSeas-Day');
                break;

            case 'winter-day-sleet-cold':
            case 'winter-day-sleet-medium':
            case 'winter-day-sleet-hot':
                $this->currentAnimation = array('Seascape-Fog', 'WaterCityGates-Fog', 'RoughSeas-Day', 'IceWind-Day');
                break;

            case 'winter-day-snow-cold':
            case 'winter-day-snow-medium':
            case 'winter-day-snow-hot':
                $this->currentAnimation = array('WinterForest-Snow', 'IceWind-Day');
                break;

            case 'winter-day-wind-cold':
            case 'winter-day-wind-medium':
            case 'winter-day-wind-hot':
            case 'winter-day-fog-cold':
            case 'winter-day-fog-medium':
            case 'winter-day-fog-hot':
                $this->currentAnimation = array('Seascape-Fog', 'WaterCityGates-Fog', 'RoughSeas-Day');
                break;

            case 'winter-sunset-clear-day-cold':
            case 'winter-sunset-clear-night-hot':
            case 'winter-sunset-clear-day-medium':
            case 'winter-sunset-clear-day-hot':
            case 'winter-sunset-clear-night-cold':
            case 'winter-sunset-clear-night-medium':
                $this->currentAnimation = array('IslandFires-Dusk', 'SeascapeSunset', 'MountainFortress-Dusk');                
                break;


            case 'winter-sunset-partly-cloudy-day-cold':
            case 'winter-sunset-partly-cloudy-day-medium':
            case 'winter-sunset-partly-cloudy-day-hot':
            case 'winter-sunset-partly-cloudy-night-cold':
            case 'winter-sunset-partly-cloudy-night-medium':
            case 'winter-sunset-partly-cloudy-night-hot':
                $this->currentAnimation = array('ApproachingStorm-Day', 'SeascapeSunset', 'MountainFortress-Dusk');
                break;

            case 'winter-sunset-cloudy-cold':
            case 'winter-sunset-cloudy-medium':
            case 'winter-sunset-cloudy-hot':
                $this->currentAnimation = array('ApproachingStorm-Day', 'Seascape-Fog', 'WaterCityGates-Fog', 'MountainStream-Day');
                break;

            case 'winter-sunset-rain-cold':
            case 'winter-sunset-sleet-hot':
            case 'winter-sunset-rain-medium':
            case 'winter-sunset-rain-hot':
            case 'winter-sunset-sleet-cold':
            case 'winter-sunset-sleet-medium':
                $this->currentAnimation = array('Seascape-Fog', 'WaterCityGates-Fog');
                break;

            case 'winter-sunset-snow-cold':
            case 'winter-sunset-snow-medium':
            case 'winter-sunset-snow-hot':
                $this->currentAnimation = array('WinterForest-Snow', 'IceWind-Day');
                break;

            case 'winter-sunset-wind-cold':
            case 'winter-sunset-fog-cold':
            case 'winter-sunset-wind-medium':
            case 'winter-sunset-wind-hot':
            case 'winter-sunset-fog-medium':
            case 'winter-sunset-fog-hot':
                $this->currentAnimation = array('MountainFortress-Dusk', 'SeascapeSunset', 'IslandFires-Dusk');
                break;

            case 'winter-night-clear-day-cold':
            case 'winter-night-clear-night-hot':
            case 'winter-night-clear-day-medium':
            case 'winter-night-clear-day-hot':
            case 'winter-night-clear-night-cold':
            case 'winter-night-clear-night-medium':
                $this->currentAnimation = array('HarborTown-Night', 'MountainStream-Night');
                break;

            case 'winter-night-partly-cloudy-day-cold':
            case 'winter-night-partly-cloudy-day-medium':
            case 'winter-night-partly-cloudy-day-hot':
            case 'winter-night-partly-cloudy-night-cold':
            case 'winter-night-partly-cloudy-night-medium':
            case 'winter-night-partly-cloudy-night-hot':
            case 'winter-night-cloudy-cold':
            case 'winter-night-cloudy-medium':
            case 'winter-night-cloudy-hot':
            case 'winter-night-rain-cold':
            case 'winter-night-rain-medium':
            case 'winter-night-rain-hot':
            case 'winter-night-sleet-cold':
            case 'winter-night-sleet-medium':
            case 'winter-night-sleet-hot':
                $this->currentAnimation = array('Seascape-Fog', 'WaterCityGates-Fog', 'HarborTown-Night', 'MountainStream-Night');
                break;
                
            case 'winter-night-snow-cold':
            case 'winter-night-snow-hot':
            case 'winter-night-snow-medium':
                $this->currentAnimation = array('WinterForest-Snow', 'IceWind-Day');
                break;

            case 'winter-night-wind-cold':
            case 'winter-night-fog-hot':
            case 'winter-night-wind-medium':
            case 'winter-night-wind-hot':
            case 'winter-night-fog-cold':
            case 'winter-night-fog-medium':
                $this->currentAnimation = array('Seascape-Fog', 'WaterCityGates-Fog', 'HarborTown-Night', 'MountainStream-Night');
                break;
        }
        $chosenCondition = array_rand($this->currentAnimation);
        $this->getSoundForImage($this->currentAnimation[$chosenCondition]);
        return array($this->currentAnimation[$chosenCondition], $this->currentSound);
    }
    
    /**
     * get current mapping of all possible condition combinations
     */
    public function getMapping() {
        foreach ($this->timeOfYearList as $timeOfYear) {
            foreach ($this->timeOfDayList as $timeOfDay) {
                foreach ($this->conditionsList as $condition) {
                    foreach ($this->currentTempList as $temp) print "\ncase '$timeOfYear-$timeOfDay-$condition-$temp':\n\t\$this->currentAnimation = array('');
\n\tbreak;\n";
                }
            }
        }
    }
}
