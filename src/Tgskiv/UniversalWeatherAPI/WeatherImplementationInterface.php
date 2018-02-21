<?php

namespace UniversalWeatherAPI;


interface WeatherImplementationInterface
{

    public function __construct($key);

    public function getWeather($lat, $lon, $timestamp = null);

}
