<?php
namespace app\utils;

use Yii;

/**
 * Time util provides the time related functions
 */
class TimeUtil
{
    const SECONDS_OF_MINUTE = 60;
    const SECONDS_OF_HOUR = 3600;
    const MILLI_OF_SECONDS = 1000;
    const SECONDS_OF_DAY = 86400;
    const MILLI_SECONDS_OF_MINUTE = 60000;
    const MILLI_SECONDS_OF_HOUR = 3600000;
    const MILLI_SECONDS_OF_DAY = 86400000;
    const DAYS_OF_WEEK = 7;
    const HOURS_OF_DAY = 24;

    public static $timezones = [
        '-12' => 'Pacific/Kwajalein',
        '-11' => 'Pacific/Samoa',
        '-10' => 'Pacific/Honolulu',
        '-9' => 'America/Juneau',
        '-8' => 'America/Los_Angeles',
        '-7' => 'America/Denver',
        '-6' => 'America/Mexico_City',
        '-5' => 'America/New_York',
        '-4' => 'America/Caracas',
        '-3.5' => 'America/St_Johns',
        '-3' => 'America/Argentina/Buenos_Aires',
        '-2' => 'Atlantic/Azores', // no cities here so just picking an hour ahead
        '-1' => 'Atlantic/Azores',
        '0' => 'Europe/London',
        '1' => 'Europe/Paris',
        '2' => 'Europe/Helsinki',
        '3' => 'Europe/Moscow',
        '3.5' => 'Asia/Tehran',
        '4' => 'Asia/Baku',
        '4.5' => 'Asia/Kabul',
        '5' => 'Asia/Karachi',
        '5.5' => 'Asia/Calcutta',
        '6' => 'Asia/Colombo',
        '7' => 'Asia/Bangkok',
        '8' => 'Asia/Singapore',
        '9' => 'Asia/Tokyo',
        '9.5' => 'Australia/Darwin',
        '10' => 'Pacific/Guam',
        '11' => 'Asia/Magadan',
        '12' => 'Asia/Kamchatka'
    ];

    /**
     * Get the timestamp of 0:00 today.
     * Without considering timezone related issues till now
     * @return timestamp
     */
    public static function today()
    {
        //consider issues caused by timezone difference in the future
        return $todayTimeStamp = strtotime(date('Y-m-d'));
    }

    /**
     * Get the timestamp of 0:00 yesterday.
     * Without considering timezone related issues till now
     * @return timestamp
     */
    public static function yesterday()
    {
        return strtotime(date('Y-m-d')) - self::SECONDS_OF_DAY;
    }

    /**
     * Get the timestamp of 01-01 today.
     * @return timestamp
     */
    public static function thisYear()
    {
        return $todayTimeStamp = strtotime(date('Y-01-01'));
    }

    /**
     * Get the millisecond timestamp
     * @return int
     */
    public static function msTime($time = null)
    {
        return empty($time) ? round(microtime(true) * self::MILLI_OF_SECONDS) : $time * self::MILLI_OF_SECONDS;
    }

    /**
     * Get milliseconds of time
     * @param timestamp $mstime
     * @return int
     */
    public static function ms2sTime($mstime)
    {
        return $mstime / self::MILLI_OF_SECONDS;
    }

    /**
     * Get duration days between two timestamp
     * @param $timeFrom timestamp
     * @param $timeTo timestamp
     * @return int
     **/
    public static function durationDays($timeFrom, $timeTo)
    {
        return intval(abs($timeFrom - $timeTo) / self::SECONDS_OF_DAY);
    }

    /**
     * Formate millisecond timestamp to string
     * @param timestamp $time
     * @param string $formate
     */
    public static function msTime2String($mstime, $format = 'Y-m-d H:i:s', $timezoneOffset = null)
    {
        $timezone = self::getDefaultTimezone();
        if (!is_null($timezoneOffset)) {
            $timezoneOffset = -$timezoneOffset;
            $timezone = self::$timezones[(string)$timezoneOffset];
        }

        date_default_timezone_set($timezone);

        return date($format, $mstime / self::MILLI_OF_SECONDS);
    }

    /**
     * Formate second timestamp to string
     * @param timestamp $time
     * @param string $formate
     */
    public static function sTime2String($stime, $format = 'Y-m-d H:i:s', $timezoneOffset = null)
    {
        return self::msTime2String($stime * self::MILLI_OF_SECONDS, $format, $timezoneOffset);
    }

    /**
     * Transform the time string to milliseconds int
     * @param time string $timeStr
     * @param int milliseconds for time string
     */
    public static function string2MsTime($timeStr)
    {
        return strtotime($timeStr) * self::MILLI_OF_SECONDS;
    }

    /**
     * Transform the time string to milliseconds int by using DateTime
     * @param string $timeStr
     * @param int $timeZone(Asia/Shanghai)
     * @return int milliseconds for time string
     */
    public static function string2MsTimeByUsingDateTime($timeStr = null)
    {
        $dateTime = new \DateTime($timeStr);
        $dateTime->setTimezone(new \DateTimeZone('Asia/Shanghai'));
        $dateTime = json_decode(json_encode($dateTime), true);
        $date = $dateTime['date'];
        list($timestampStr, $msTime) = explode('.', $date);

        return self::string2MsTime($timestampStr) + intval(substr($msTime, 0, 3));
    }

    /**
     * Get current time format to ISO8061
     * @return string time(UTC time string) e.g.: 2017-11-14T14:00:34.410Z
     */
    public static function currentTime2ISO8061UTCTimeStr()
    {
        $time = microtime();
        // get millSeconds from time string
        $millSeconds = substr($time, 2, 3);

        return gmdate("Y-m-d\TH:i:s.$millSeconds\Z");
    }

    /**
     * Check if the time hour is in the specific hour interval
     * @param  string $fromTime in format of "H:i"
     * @param  string $toTime   in format of "H:i"
     * @param  int $timezoneOffset the offset hours with UTC
     * @param  string $time     in format of "H:i"
     * @return bool
     */
    public static function isTimeBetween($fromTime, $toTime, $timezoneOffset, $time = 0)
    {
        if (empty($time)) {
            $time = strtotime(date('H:i'));
        }
        $startTimestamp = strtotime($fromTime) + $timezoneOffset * self::SECONDS_OF_HOUR;
        $endTimestamp = strtotime($toTime) + $timezoneOffset * self::SECONDS_OF_HOUR;
        // eg. 15:00 ~ 13:00, means 15:00 ~ 24:00 and 00:00 ~ 13:00
        // fromTime < time < 24:00 ||　00:00 < time < toTime
        if ($endTimestamp <= $startTimestamp) {
            return ($startTimestamp < $time) || ($time < $endTimestamp);
        }

        LogUtil::info('Check hour interval', 'utils-time', ['startTimestamp' => $startTimestamp, 'endTimestamp' => $endTimestamp]);

        return ($startTimestamp < $time) && ($endTimestamp > $time);
    }

    /**
     * Get the offset in milliseconds between Default and UTC.
     * @return int milliseconds
     */
    public static function getTimezoneOffset()
    {
        $appTimezone = new \DateTimeZone(Yii::$app->getTimezone());
        $timeInUTC = new \DateTime('now', new \DateTimeZone('UTC'));

        return $appTimezone->getOffset($timeInUTC) * self::MILLI_OF_SECONDS;
    }

    public static function getDefaultTimezone()
    {
        return Yii::$app->timeZone;
    }

    /**
     * Get the quarter value from timestamp value
     * @param int $timestamp
     * @return int
     */
    public static function getQuarter($timestamp)
    {
        return intval((date('n', $timestamp) + 2) / 3);
    }

    /**
     * Get the datetime from time string, use current datetime as fallback
     * @param string $dateStr
     * @return int
     */
    public static function getDatetime($dateStr)
    {
        if (empty($dateStr)) {
            $today = strtotime(date('Y-m-d'));
            $datetime = strtotime('-1 day', $today);
        } else {
            $datetime = strtotime($dateStr);
        }

        return $datetime;
    }

    /**
     *Format milliseconds to time
     * eg:587326.923076 => 9m 47s
     * eg:67127372 => 18h 38m 47s
     * @param int $milliseconds
     * @return string $dateStr
     */
    public static function formatMillisecond2Time($milliseconds)
    {
        $hours = floor($milliseconds / (self::MILLI_OF_SECONDS * self::SECONDS_OF_HOUR));
        $minutes = floor(($milliseconds % (self::MILLI_OF_SECONDS * self::SECONDS_OF_HOUR)) / (self::MILLI_OF_SECONDS * self::SECONDS_OF_MINUTE));
        $seconds = sprintf('%.1f', ($milliseconds % (self::MILLI_OF_SECONDS * self::SECONDS_OF_MINUTE)) / self::MILLI_OF_SECONDS);
        $time = '';

        if ($hours > 0) {
            $time .= $hours . 'h';
        }
        if ($minutes > 0) {
            $time .= $minutes . 'm';
        }
        if (empty($time) && $seconds > 0) {
            $time .= $seconds . 's';
        } elseif (empty($time) || $seconds > 0) {
            $time .= round($seconds) . 's';
        }

        return $time;
    }

    /**
     * Get duration hour between two timestamp
     * @param $msTimeFrom timestamp
     * @param $msTimeTo timestamp
     * @return int
     */
    public static function durationHours($msTimeFrom, $msTimeTo)
    {
        return ($msTimeTo - $msTimeFrom) / self::MILLI_SECONDS_OF_HOUR;
    }

    /**
     * @param from, int,(unixtime)
     * @param to, int, (unixtime)
     * @param dateType, string
     * @return array example:['2016-11-01', '2016-11-02']
     */
    public static function getDayTicks($from, $end, $dateType = 'Y-m-d')
    {
        $begin = strtotime(date('Y-m-d', $from));
        $end = strtotime(date('Y-m-d', $end));

        $result = [];
        for ($unixtime = $begin; $unixtime <= $end; $unixtime += self::SECONDS_OF_DAY) {
            $result[] = date($dateType, $unixtime);
        }

        return $result;
    }

    public static function getMonday($time)
    {
        $dayOfWeek = date('N', $time) - 1;
        $monday = strtotime("-$dayOfWeek days", $time);

        return date('Y-m-d', $monday);
    }

    /**
     * Get date string list by start and end time
     * @param msTime $startMsTime
     * @param msTime $endMsTime
     * @return array
     */
    public static function getDateStringListByMsStartAndEndTime($startMsTime, $endMsTime)
    {
        $startMsTime = intval($startMsTime);
        $endMsTime = intval($endMsTime);
        $dateList = [];

        for ($index = 0; $startMsTime <= $endMsTime; $index++) {
            $dateList[] = self::msTime2String($startMsTime, 'Y-m-d');
            $startMsTime += self::MILLI_SECONDS_OF_DAY;
        }

        return $dateList;
    }

    /**
     * Get milliseconds of specified date time.
     * @param \DateTime $dateTime
     * @return milliseconds of $dateTime
     */
    public static function getMilliseconds(\DateTime $dateTime)
    {
        return $dateTime->getTimestamp() * self::MILLI_OF_SECONDS;
    }

    /**
     * Add specified interval to selected date time.
     * @param  \DateTime $dateTime
     * @param  string    $interval_spec, 'P1Y', P1D', etc
     * @return \DateTime the added date time
     */
    public static function addTimeByInterval(\DateTime $dateTime, string $interval_spec)
    {
        $dateTime->add(new \DateInterval($interval_spec));

        return $dateTime;
    }

    /**
     * Subtract specified interval to selected date time.
     * @param \DateTime $dateTime
     * @param string $interval_spec, 'P1Y', P1D', etc
     * @return \DateTime the substracted date time
     */
    public static function subTimeByInterval(\DateTime $dateTime, string $interval_spec)
    {
        $dateTime->sub(new \DateInterval($interval_spec));

        return $dateTime;
    }

    public static function getDayHourArray()
    {
        return [
            '00:00',
            '01:00',
            '02:00',
            '03:00',
            '04:00',
            '05:00',
            '06:00',
            '07:00',
            '08:00',
            '09:00',
            '10:00',
            '11:00',
            '12:00',
            '13:00',
            '14:00',
            '15:00',
            '16:00',
            '17:00',
            '18:00',
            '19:00',
            '20:00',
            '21:00',
            '22:00',
            '23:00',
        ];
    }

    public static function getDateStrArrayByTimeRange($startMsTime, $endMsTime)
    {
        $dateStrRange = [];
        while ($startMsTime <= $endMsTime) {
            $dateStr = self::msTime2String($startMsTime, 'Y-m-d');
            $dateStrRange[] = $dateStr;
            $startMsTime += self::MILLI_SECONDS_OF_DAY;
        }

        return $dateStrRange;
    }
}
