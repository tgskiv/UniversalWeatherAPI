<?php

namespace UniversalWeatherAPI;


class WorldWeatherOnline implements WeatherImplementationInterface
{
    protected $_apikey = '';

    protected $_codeMapping = [
        113	=> 	113	,
        116	=> 	116	,
        119	=> 	119	,
        122	=> 	122	,
        143	=> 	143	,
        176	=> 	176	,
        179	=> 	179	,
        182	=> 	182	,
        185	=> 	185	,
        200	=> 	200	,
        227	=> 	227	,
        230	=> 	230	,
        248	=> 	248	,
        260	=> 	260	,
        263	=> 	263	,
        266	=> 	266	,
        281	=> 	281	,
        284	=> 	284	,
        293	=> 	293	,
        296	=> 	296	,
        299	=> 	299	,
        302	=> 	302	,
        305	=> 	305	,
        308	=> 	308	,
        311	=> 	311	,
        314	=> 	314	,
        317	=> 	317	,
        320	=> 	320	,
        323	=> 	323	,
        326	=> 	326	,
        329	=> 	329	,
        332	=> 	332	,
        335	=> 	335	,
        338	=> 	338	,
        350	=> 	350	,
        353	=> 	353	,
        356	=> 	356	,
        359	=> 	359	,
        362	=> 	362	,
        365	=> 	365	,
        368	=> 	368	,
        371	=> 	371	,
        374	=> 	374	,
        377	=> 	377	,
        386	=> 	386	,
        389	=> 	389	,
        392	=> 	392	,
        395	=> 	395	,
    ];




    public function __construct($config) {
        $this->_apikey = $config["key"];
    }

    public function getWeather($lat, $lon, $timestamp = null) {

        $past = false;

        if ($timestamp) {
            $date = date('Y-m-d', $timestamp);
            $past = true;
        } else {
            $date = "today";
            $past = false;
        }
        $date_safe = urlencode($date);//this SHOULD return the same value, but if malformed this will correct

        $loc_safe = [
            urlencode($lat),
            urlencode($lon)
        ];
        $search_safe = implode(",", $loc_safe);

        $data = $past ?
                $this->callPastWeather($search_safe, $date_safe) :
                $this->callCurrentWeather($search_safe, $date_safe);

        
        $this->checkResponseErrors($data);

        return  $past ?
                $this->convertPastConditions($data->data, $timestamp) :
                $this->convertCurrentConditions($data->data, $timestamp);
    }


    protected function checkResponseErrors($data) {
        
        if (is_array($data->data->error) && count($data->data->error) > 0) {
            throw new WeatherException("Weather API error: ".$data->data->error[0]->msg, WeatherException::SERVERERROR);
        }

        if (!$data) {
            throw new WeatherException("Unknown weather API error.", WeatherException::UNKNOWN);
        }

    }


    public function callCurrentWeather($loc_string) {
            //To add more conditions to the query, just lengthen the url string
            $premiumurl = sprintf('http://api.worldweatheronline.com/premium/v1/weather.ashx?key=%s&q=%s&date=today&format=json', 
                $this->_apikey, $loc_string);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $premiumurl);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1); 
            $jsonResponse = curl_exec($ch);
            
            curl_close($ch);

            if (!$jsonResponse) {
                throw new WeatherException("Can't get current weather conditions", WeatherException::SERVERERROR);
            }

            return json_decode($jsonResponse, false);
    }

    public function callPastWeather($loc_string, $date_safe) {
            
/**
 * See https://developer.worldweatheronline.com/premium-api-explorer.aspx for more
 * extrastringquery(Optional) It allows to request some additional information in the feed return. Possible values are localObsTime, isDayTime, utcDateTime. Two or more values can be passed as comma separated.
 * datestringquery(Optional) Get weather for a particular date within next 15 day. It supports today, tomorrow and a date in future. The date should be in the yyyy-MM-dd format. E.g:- date=today or date=tomorrow or date=2013-04-21
 * enddatestringquery(Optional) If you wish to get weather data between two dates, then you need to provide with 'enddate' parameter as well. The date should be in the yyyy-MM-dd format. Example:- 20th July, 2009 then the date will be formatted as 2009-07-20.
 * includelocationstringquery(Optional) Returns the nearest weather point for which the weather data is returned for a given postcode, zipcode and lat/lon values. The possible values are yes or no. By default it is no. E.g:- includeLocation=yes or includeLocation=no
 * tp    stringquery(Optional) Switch between weather forecast time interval from 1 hourly, 3 hourly, 6 hourly, 12 hourly (day/night) or 24 hourly (day average). E.g:- tp=24 or tp=12 or tp=6 or tp=3 or tp=1
 */
            $premiumurl = sprintf('http://api.worldweatheronline.com/premium/v1/past-weather.ashx?key=%s&q=%s&date=%s&format=json&tp=24', 
                $this->_apikey, $loc_string, $date_safe);

            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $premiumurl);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1); 
            $jsonResponse = curl_exec($ch);
            
            curl_close($ch);

            if (!$jsonResponse) {
                throw new WeatherException("Can't get past weather conditions", WeatherException::SERVERERROR);
            }

            return json_decode($jsonResponse, false);
    }



    protected function convertCurrentConditions($data, $timestamp) {
        $condition = new WeatherCondition();        

        if (is_array($data->current_condition) && count($data->current_condition) > 0) {
            $condition->setTemperature( $data->current_condition[0]->tempC, "C" );
            $condition->setConditionCode( $this->_codeMapping[$data->current_condition[0]->weatherCode] );
        } else {
            throw new WeatherException("Can't extract current weather conditions", WeatherException::SERVERERROR);
        }
        
        return $condition;
    }

    protected function convertPastConditions($data, $timestamp) {
        $condition = new WeatherCondition();

        /**
         * @todo We can extract user local time and get exact weather by exact time
         */
        if (!empty($data->weather) && count($data->weather) > 0 && count($data->weather[0]->hourly) > 0) {
            $condition->setTemperature( $data->weather[0]->hourly[0]->tempC, "C" );
            $condition->setConditionCode( $this->_codeMapping[$data->weather[0]->hourly[0]->weatherCode] );
        } else {
            throw new WeatherException("Can't extract past weather conditions", WeatherException::SERVERERROR);
        }
        
        return $condition;
    }
}