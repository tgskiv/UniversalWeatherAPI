<?php

namespace SimpleUniversalWeatherAPI;


use Yii;

class WeatherException extends \Exception {

    const UNKNOWN = 1;
    const SERVERERROR = 2;
    
    const OTHER = 100;
}