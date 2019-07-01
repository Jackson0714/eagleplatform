<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\web\ServerErrorHttpException;
use app\models\Config;
use app\utils\CurlUtil;
use app\utils\MongodbUtil;

class WeConnect extends Component
{
    const ACCESS_TOKEN_EXPIRES_TIME = 6000; // 秒
    const TICKET_EXPIRES_TIME = 6000; // 秒
    const WECHAT_DOMAIN = 'https://api.weixin.qq.com/cgi-bin';

    public function getTicket()
    {
        $config = Config::findOne();
        if (empty($config)) {
            return null;
        }

        $jsTicket = $config->jsTicket;
        $jsTicketTime = $config->jsTicketTime;
        if (!empty($jsTicket)) {
            $jsTicketTime = MongodbUtil::MongoDate2TimeStamp($jsTicketTime);
            $currentTime = time();
            if (($currentTime - $jsTicketTime) > self::TICKET_EXPIRES_TIME) {
                $accessToken = $this->getAccessToken();
                $jsTicket = $this->jsTicket($accessToken);
                $config->jsTicket = $jsTicket;
                $config->jsTicketTime = MongodbUtil::convertToMongoDate();
                $config->save();
            }
        } else {
            $accessToken = $this->getAccessToken();
            $jsTicket = $this->jsTicket($accessToken);
            $config->jsTicket = $jsTicket;
            $config->jsTicketTime = MongodbUtil::convertToMongoDate();
            $config->save();
        }

        return [$config->appid, $jsTicket];
    }

    public function getAccessToken()
    {
        $config = Config::findOne();
        if (empty($config)) {
            return null;
        }

        $appid = $config->appid;
        $secret = $config->secret;
        $accessToken = $config->accessToken;
        $accessTokenTime = $config->accessTokenTime;
        if (!empty($accessToken)) {
            $accessTokenTime = MongodbUtil::MongoDate2TimeStamp($accessTokenTime);
            $currentTime = time();
            if (($currentTime - $accessTokenTime) > self::ACCESS_TOKEN_EXPIRES_TIME) {
                $accessToken = $this->accessToken($appid, $secret);
                $config->accessToken = $accessToken;
                $config->accessTokenTime = MongodbUtil::convertToMongoDate();
                $config->save();
            }
        } else {
            $accessToken = $this->accessToken($appid, $secret);
            $config->accessToken = $accessToken;
            $config->accessTokenTime = MongodbUtil::convertToMongoDate();
            $config->save();
        }

        return $accessToken;
    }

    /**
     * 发送客服消息
     * https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140547
     */
    public function sendCustomMessage($openid, $content)
    {
        $accessToken = $this->getAccessToken();
        $url = self::WECHAT_DOMAIN . '/message/custom/send?access_token=' . $accessToken;
        $data = array_merge(['touser' => $openid], $content);
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        return CurlUtil::post($url, $data);
    }

    /**
     * 获取 access_token
     * https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140183
     */
    private function accessToken($appid, $secret)
    {
        $url = self::WECHAT_DOMAIN . '/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
        $result = CurlUtil::get($url);

        Yii::error('bbbb'.json_encode($result));

        if (isset($result['errcode'])) {
            throw new ServerErrorHttpException('wechat api request error');
        }

        return $result['access_token'];
    }

    /**
     * JS-SDK 使用权限签名算法
     * https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141115
     */
    private function jsTicket($accessToken)
    {
        $url = self::WECHAT_DOMAIN . '/ticket/getticket?access_token=' . $accessToken . '&type=jsapi';
        $result = CurlUtil::get($url);
        if (isset($result['errmsg']) && $result['errmsg'] === 'ok') {
            return $result['ticket'];
        }

        throw new ServerErrorHttpException('wechat api request error');
    }

     /**
     * 网页授权
     * https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140842
     */
    public function authorize($domain, $page)
    {
        $config = Config::findOne();
        $appid = $config->appid;
        $redirect_uri = Yii::$app->params['redirect_url'];
        $url = Yii::$app->params['authorize_url'].$appid.'&redirect_uri='
            .urlEncode($redirect_uri.'?domain='.$domain.'&page='.$page).Yii::$app->params['authorize_url_end'];

        header('Location:'.$url);
        exit;
    }

    /**
     * 获取授权用户openid
     * https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140842
     */
    public function getOpenid($code)
    {
        $config = Config::findOne();
        $appid = $config->appid;
        $appsecret = $config->secret;
        $url = Yii::$app->params['oauth_url'].$appid.'&secret='.$appsecret
            .'&code='.$code.'&grant_type=authorization_code';
        $result = CurlUtil::get($url);
        $openid = $result['openid'];

        return $openid;
    }
}
