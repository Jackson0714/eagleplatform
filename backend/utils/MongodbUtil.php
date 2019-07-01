<?php
namespace app\utils;

use MongoDB\BSON\ObjectID;
use MongoDB\BSON\Regex;
use MongoDB\BSON\UTCDateTime;
use Yii;
use yii\web\BadRequestHttpException;

/**
 * This is class file for mongodb utils
 * @author Devin Jin
 **/
class MongodbUtil
{
    /**
     * This is needed to keep documents unique that have the same timestamp.
     * @var int
     * @see $timestamp
     */
    public static $_mongoIdFromTimestampCounter = 0;

    /**
     * Transfer the MongoDate to unix timestamp
     * @param $mongoDate, mongoDate object
     * @return timestamp
     * @author Devin Jin
     **/
    public static function MongoDate2TimeStamp($mongoDate)
    {
        return empty($mongoDate) ? 0 : (string)$mongoDate / TimeUtil::MILLI_OF_SECONDS;
    }

    /**
     * Transfer the MongoDate to ms unix timestamp
     * @param $mongoDate, mongoDate object
     * @return int
     * @author Rex Chen
     **/
    public static function MongoDate2msTimeStamp($mongoDate)
    {
        return empty($mongoDate) ? 0 : (string)$mongoDate;
    }

    /**
     * Transfer ms unix timestamp to MongoDate
     * @param int
     * @return MongoDate object
     * @author Rex Chen
     **/
    public static function msTimetamp2MongoDate($millsecond)
    {
        return new UTCDateTime($millsecond);
    }

    /**
     * Transfer MongoDate to string
     * @param $mongoDate, mongoDate object
     * @return string
     * @author Devin Jin
     **/
    public static function MongoDate2String($mongoDate, $format = 'Y-m-d H:i:s', $timezoneOffset = null)
    {
        if (empty($mongoDate)) {
            return '';
        }

        return TimeUtil::msTime2String((string)$mongoDate, $format);
    }

    /**
     * Check whether the mongodate is expired(compare it to current time)
     * @param $mongoDate, mongoDate object
     * @return bool
     * @author Devin Jin
     **/
    public static function isExpired($mongoDate)
    {
        return self::MongoDate2TimeStamp($mongoDate) <= time();
    }

    /**
     * Transfer id list to mongoId List
     * @param array $params
     */
    public static function toMongoIdList($params)
    {
        if (!is_array($params)) {
            return $params;
        }
        foreach ($params as &$param) {
            $param = self::convertToMongoId($param);
        }

        return $params;
    }

    /**
     * Transfer Mongo id list to string List
     * @param array $params
     * @return array(string list)
     */
    public static function toStringIdList($params)
    {
        if (!is_array($params)) {
            return $params;
        }
        foreach ($params as &$param) {
            $param = (string)$param;
        }

        return $params;
    }

    /**
     * Get duration from two MongoDate
     *  @param $mongoDate, mongoDate object
     */
    public static function diffTimeMillis($startTime, $endTime)
    {
        if (!empty($startTime) && !empty($endTime)) {
            return self::MongoDate2msTimeStamp($endTime) - self::MongoDate2msTimeStamp($startTime);
        }
        throw new BadRequestHttpException(Yii::t('common', 'parameters_missing'));
    }

    /**
     * @param id. string or object
     * @return bool
     */
    public static function isObjectId($id)
    {
        if (is_array($id)) {
            return false;
        }

        if ($id instanceof ObjectID || preg_match('/^[a-f\d]{24}$/i', $id)) {
            return true;
        }

        return false;
    }

    public static function isMongoDate($date)
    {
        return $date instanceof UTCDateTime;
    }

    /**
     * @param condition, array
     * @return array
     */
    public static function buildCondition($condition)
    {
        return Yii::$app->mongodb->getQueryBuilder()->buildCondition($condition);
    }

    /**
     * @param id, string
     * @return MongoId
     */
    public static function convertToMongoId($id = null)
    {
        if ($id instanceof ObjectID) {
            return $id;
        }

        return new ObjectID($id);
    }

    /**
     * @param millisecond, mill seconds
     * @return MongoDate
     */
    public static function convertToMongoDate($millisecond = null)
    {
        if ($millisecond instanceof UTCDateTime) {
            return $millisecond;
        }

        //if the time is timeStamp,need to convert to mill second
        if (strlen(intval($millisecond)) == 10) {
            $millisecond *= TimeUtil::MILLI_OF_SECONDS;
        }

        return new UTCDateTime($millisecond);
    }

    /**
     * Mongo Id From Timestamp
     * @param int $timestamp
     * @return MongoID
     * @see http://docs.mongodb.org/manual/reference/object-id/
     */
    public static function createMongoIdWithTime($timestamp)
    {
        // build binary Id
        $binaryTimestamp = pack('N', $timestamp); // unsigned long
        $machineId = substr(md5(gethostname()), 0, 3); // 3 bit machine identifier
        $binaryPID = pack('n', posix_getpid()); // unsigned short
        $counter = substr(pack('N', self::$_mongoIdFromTimestampCounter++), 1, 3); // counter
        $binaryId = "{$binaryTimestamp}{$machineId}{$binaryPID}{$counter}";

        // convert to ASCII
        $id = '';
        for ($i = 0; $i < 12; $i++) {
            $id .= sprintf('%02X', ord($binaryId[$i]));
        }

        return new ObjectID($id);
    }

    /**
     * @param pattern, string
     * @param flags, string
     * @return regex
     */
    public static function convertToMongoRegex($pattern, $flags = '')
    {
        return new Regex($pattern, $flags);
    }

    /**
     * exact query and not case sensitive
     * @param pattern, string
     * @param flags, string
     * @return regex
     */
    public static function convertToMongoExactRegex($pattern)
    {
        return new Regex('^' . $pattern . '$', 'i');
    }

    public static function parseMongoDSN($dsn)
    {
        $match = preg_match('/^mongodb:\\/\\/((.+):(.+)@)?([\w:,.-]+)\\/([^?&]+)/s', $dsn, $matches);
        if (!$match) {
            return false;
        }

        return [
            'username' => $matches[2],
            'password' => $matches[3],
            'host' => $matches[4],
            'db' => $matches[5],
        ];
    }
}
