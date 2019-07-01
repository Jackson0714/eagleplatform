<?php

namespace app\controllers;

use Yii;
use app\utils\CurlUtil;
use app\utils\StringUtil;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\web\UnauthorizedHttpException;
use yii\web\BadRequestHttpException;

class WeController extends BaseController
{
    const DEFAULT_CUSTOM_REPLY = 'Hi~我是服务助手，请问有什么可以帮您的？';

    public function actionIndex()
    {
        $nonce = $_GET['nonce'];
        $token = 'newworld';
        $timestamp = $_GET['timestamp'];
        $echostr = isset($_GET['echostr']) ? $_GET['echostr'] : '';
        $signature = $_GET['signature'];
        $array = array();
        $array = array($nonce, $timestamp, $token);
        sort($array);

        $str = sha1(implode($array));
        if ($str  == $signature && $echostr) {
            echo  $echostr;
            exit;
        } else {
            $this->reponseMessage();
        }
    }

    public function reponseMessage()
    {
        $postArr = file_get_contents('php://input');
        $postObj = simplexml_load_string($postArr, 'SimpleXMLElement', LIBXML_NOCDATA);
		if (strtolower($postObj->MsgType) == 'text') {
            $content = trim($postObj->Content);
            $this->EchoMessage($postObj, $content);
		}
    }

    public function EchoMessage($postObj, $content)
    {
        $toUser = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $template = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[transfer_customer_service]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            </xml>";
        $time = time();
        $msgType  =  'text';
        $info = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
        echo $info;
    }

    /**
     * 网页授权
     * https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140842
     */
    public function actionAuthorize()
    {
        $domain = Yii::$app->request->get('domain');
        $page = Yii::$app->request->get('page');
        Yii::$app->weConnect->authorize($domain, $page);
    }

     /**
     * 获取授权用户openid
     * https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140842
     */
    public function actionGetOpenid()
    {
        $domain = Yii::$app->request->get('domain');
        $page = Yii::$app->request->get('page');
        $code = Yii::$app->request->get('code');
        $openid = Yii::$app->weConnect->getOpenid($code);
        header('Location:http://'.$domain.'/#/'.$page.'?openid='.$openid);
    }

    /**
     * For h5 page
     */
    public function actionJssdkSign()
    {
        $url = Yii::$app->request->get('url');
        if (empty($url)) {
            throw new BadRequestHttpException('miss parameter');
        }

        list($appid, $ticket) = Yii::$app->weConnect->getTicket();

        list($timestamp, $noncestr, $signature) = $this->generateSign($url, $ticket);

        return [
            'code' => 200,
            'data' => [
                'appid' => $appid,
                'timestamp' => $timestamp,
                'noncestr' => $noncestr,
                'signature' => $signature,
            ],
            'msg' => 'success'
        ];
    }

    public function actionNoticeJoinCustom()
    {
        $openid = Yii::$app->request->get('openid');
        if (empty($openid)) {
            throw new BadRequestHttpException('miss parameter');
        }

        $content = [
            'msgtype' => 'text',
            'text' => [
                'content' => self::DEFAULT_CUSTOM_REPLY
            ]
        ];

        $result = Yii::$app->weConnect->sendCustomMessage($openid, $content);

        return [
            'code' => 200,
            'data' => $result,
            'msg' => 'success'
        ];
    }

    private function generateSign($url, $ticket)
    {
        $data = [
            'url' => $url,
            'noncestr' => StringUtil::randomStr(16),
            'timestamp' => time(),
            'jsapi_ticket' => $ticket,
        ];

        ksort($data);

        $raw = '';
        foreach ($data as $key => $value) {
            $raw .= "&{$key}={$value}";
        }
        $raw = substr($raw, 1);

        $signature = sha1($raw);

        return [$data['timestamp'], $data['noncestr'], $signature];
    }
}
