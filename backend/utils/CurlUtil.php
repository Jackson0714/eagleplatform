<?php

namespace app\utils;

class CurlUtil
{
    public static function post($url, $data)
    {
        // 初始化 cURL 句柄
        $ch = curl_init();
        // 设置参数
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);        // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // 执行请求
        $output = curl_exec($ch);
        if ($output === false) {
            $output = curl_error($ch);
        }
        // 关闭 cURL 句柄
        curl_close($ch);
        // 过滤bom头部字符
        $charset[1] = substr($output, 0, 1);
        $charset[2] = substr($output, 1, 1);
        $charset[3] = substr($output, 2, 1);
        if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
            $output = substr($output, 3);
        }

        return json_decode($output, true);
    }

    public static function get($url)
    {
        // 初始化 cURL 句柄
        $ch = curl_init();
        // 设置参数
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);        // 从证书中检查SSL加密算法是否存在
        // 执行请求
        $output = curl_exec($ch);
        if ($output === false) {
            $output = curl_error($ch);
        }
        // 关闭 cURL 句柄
        curl_close($ch);
        // 过滤bom头部字符
        $charset[1] = substr($output, 0, 1);
        $charset[2] = substr($output, 1, 1);
        $charset[3] = substr($output, 2, 1);
        if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
            $output = substr($output, 3);
        }

        return json_decode($output, true);
    }
}
