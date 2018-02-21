<?php

namespace SimpleUniversalWeatherAPI;


class WeatherCondition {
    
    protected static $_codeNames = [
        113=> "Clear",
        116=> "Partly Cloudy",
        119=> "Cloudy",
        122=> "Overcast",
        143=> "Mist",
        176=> "Patchy rain nearby",
        179=> "Patchy snow nearby",
        182=> "Patchy sleet nearby",
        185=> "Patchy freezing drizzle nearby",
        200=> "Thundery outbreaks in nearby",
        227=> "Blowing snow",
        230=> "Blizzard",
        248=> "Fog",
        260=> "Freezing fog",
        263=> "Patchy light drizzle",
        266=> "Light drizzle",
        281=> "Freezing drizzle",
        284=> "Heavy freezing drizzle",
        293=> "Patchy light rain",
        296=> "Light rain",
        299=> "Moderate rain at times",
        302=> "Moderate rain",
        305=> "Heavy rain at times",
        308=> "Heavy rain",
        311=> "Light freezing rain",
        314=> "Moderate or Heavy freezing rain",
        317=> "Light sleet",
        320=> "Moderate or heavy sleet",
        323=> "Patchy light snow",
        326=> "Light snow",
        329=> "Patchy moderate snow",
        332=> "Moderate snow",
        335=> "Patchy heavy snow",
        338=> "Heavy snow",
        350=> "Ice pellets",
        353=> "Light rain shower",
        356=> "Moderate or heavy rain shower",
        359=> "Torrential rain shower",
        362=> "Light sleet showers",
        365=> "Moderate or heavy sleet showers",
        368=> "Light snow showers",
        371=> "Moderate or heavy snow showers",
        374=> "Light showers of ice pellets",
        377=> "Moderate or heavy showers of ice pellets",
        386=> "Patchy light rain in area with thunder",
        389=> "Moderate or heavy rain in area with thunder",
        392=> "Patchy light snow in area with thunder",
        395=> "Moderate or heavy snow in area with thunder",
    ];

    protected $_tempC;
    protected $_condition;
    protected $_source;


    public function __construct($tempC = null, $weatherCode = null) {
        if ($tempC) { $this->setTemperature($tempC, "C"); }
        if ($weatherCode) { $this->setConditionCode($weatherCode); }
    }

    public static function fromModel($model) {
        $wc = new WeatherCondition();
        return $wc ->setTemperature($model->temperature)
                   ->setConditionCode($model->weather)
                   ->setSource($model->weather_source);
        
    }

    public static function codeToName($conditionCode) {
        return self::$_codeNames[$conditionCode];
    }

    public static function getList() {
        return self::$_codeNames;
    }

    public function setTemperature($newValue, $type = "C") {
        switch ($type) {
            case "C" : {
                $this->_tempC = $newValue;
                break;
            }
            case "K" : {
                $this->_tempC = $newValue - 273;
                break;
            }
            case "F" : {
                $this->_tempC = 5/9 * ($newValue - 32);
                break;
            }
            default : {
                throw new WeatherException("Unsupported temperature type", WeatherException::OTHER);
            }
        }

        return $this;
    }

    public static function convertCelsius($_tempC, $toType = "C") {
        switch ($toType) {
            case "C" : {
                return $_tempC;
                break;
            }
            case "K" : {
                return $_tempC + 273;
                break;
            }
            case "F" : {
                return 9/5 * $_tempC + 32;
                break;
            }
            default : {
                throw new WeatherException("Unsupported temperature type", WeatherException::OTHER);
            }
        }
    }

    public function getTemperature($type = "C") {
        return self::convertCelsius($this->_tempC, $type);
    }

    public function setConditionCode($condition) {
        $this->_condition = $condition;
        return $this;
    }

    public function getConditionCode() {
        return $this->_condition;
    }

    public function getConditionLabel() {
        return static::codeToName( $this->getConditionCode() );
    }

    public function setSource($source) {
        $this->_source = $source;
        return $this;
    }

    public function getSource() {
        return $this->_source;
    }

    public function __toString() {
        return "{$this->_tempC}*C, ".$this->getConditionLabel();
    }
}