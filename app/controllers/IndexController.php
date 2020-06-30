<?php

use EasyWeChat\Factory;

class IndexController extends ControllerBase
{

    /**
     * 主页
     *
     * @return void
     */
    public function indexAction()
    {
        $app   = Factory::officialAccount((array) config('wexin'));
        $jssdk = $app->jssdk->buildConfig(['updateAppMessageShareData', 'updateTimelineShareData', 'hideMenuItems'], false);

        $share = [
            'title'  => '合肥学院2020“云毕业照”',
            'desc'   => '我生成了合院“云毕业照”，你也快来试试吧~',
            'link'   => 'http://graduation.nivin.cn',
            'imgUrl' => 'http://graduation.nivin.cn/favicon.ico',
        ];
        $share = json_encode($share);

        $this->view->pick('index/index');
        $this->view->setVar('jssdk', $jssdk);
        $this->view->setVar('share', $share);
        $this->view->setVar('models', MergeFace::$models);
        $this->view->setVar('bgs', MergeFace::$bgs);

        $this->view->setVar('tokenKey', $this->security->getTokenKey());
        $this->view->setVar('tokenVal', $this->security->getToken());
    }
}
