<?php

namespace UniversalWeatherAPI;


class WeatherManager
{

    protected static $_codeNames = [
        113=> "Clear/Sunny",
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

    protected $_sources = [
        "wwo" => [
            "class" => WorldWeatherOnline::class,
            "config" => [
                "key" => "291b6dff8c0a4c99a79101351183001"
            ]
        ]
    ];

    public static function stringCondition($conditionCode) {
        return empty(self::_codeNames[$conditionCode]) ? null : self::_codeNames[$conditionCode];
    }
    
    /**
     * Get weather by coordinates and time
     *
     * @param float $lat
     * @param float $lon
     * @param integer $timestamp
     * @return WeatherCondition
     */
    public function getWeather($lat, $lon, $timestamp = null) {
        
        $wwo = new $this->_sources["wwo"]["class"](
            $this->_sources["wwo"]["config"]
        );

        return $wwo->getWeather($lat, $lon, $timestamp)->setSource("wwo");
    }


}