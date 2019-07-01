<?php

return [
    'adminEmail' => 'admin@example.com',
    'getUserlistUrl' => 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=',
    'getAccessTokenUrl' => 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=',
    'getUserInfoUrl' => 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=',
    'redirect_url' => 'https://api.jayh.club/we/get-openid',
    'request_url' => 'https://api.jayh.club/index.php?app=api&mod=',
    'authorize_url' => 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=',
    'authorize_url_end' => '&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect',
    'oauth_url' => 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=',
    'userinfo_url' => 'https://api.weixin.qq.com/sns/userinfo?access_token=',
    'create_menu_url' => 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='
];
