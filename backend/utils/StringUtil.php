<?php

namespace app\utils;

class StringUtil
{
    /**
     * mt_rand ( int $min , int $max )：用于生成随机整数
     * 其中 $min – $max 为 ASCII 码的范围，这里取 33 -126
     * 可以根据需要调整范围，如ASCII码表中 97 – 122 位对应 a – z 的英文字母
     *
     * chr ( int $ascii )：用于将对应整数 $ascii 转换成对应的字符
     */
    public static function randomStr($length)
    {
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $type = mt_rand(1, 3);
            if ($type === 1) {
                $str .= chr(mt_rand(48, 57)); // 0-9
            } elseif ($type === 2) {
                $str .= chr(mt_rand(65, 90)); // A-Z
            } else {
                $str .= chr(mt_rand(97, 122)); // a-z
            }
        }
        return $str;
    }

    /**
     * UUID 的标准型式包含 32 个 16 进制数字，以连字号分为五段，形式为 8-4-4-4-12 的 32 个字符
     * 例：550e8400-e29b-41d4-a716-446655440000
     *
     * uniqid ()：获取一个带前缀、基于当前时间微秒数的唯一ID
     */
    public static function uuid($prefix = '')
    {
        $str = md5(uniqid(mt_rand(), true));
        $uuid = substr($str, 0, 8) . '-';
        $uuid .= substr($str, 8, 4) . '-';
        $uuid .= substr($str, 12, 4) . '-';
        $uuid .= substr($str, 16, 4) . '-';
        $uuid .= substr($str, 20, 12);

        return $prefix . $uuid;
    }
}
