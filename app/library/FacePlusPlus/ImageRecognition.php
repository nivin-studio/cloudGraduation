<?php
namespace FacePlusPlus;

use GuzzleHttp\Client;
use Phalcon\Di;
use Psr\Http\Message\ResponseInterface;

class ImageRecognition
{
    /**
     * 接口地址
     *
     * @var string[]
     */
    private static $url = [
        'mergeface' => 'https://api-cn.faceplusplus.com/imagepp/v1/mergeface',
    ];

    /**
     * 用户代理
     *
     * @var string
     */
    private static $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 Safari/537.36';

    /**
     * 网络请求客户端
     *
     * @var Client
     */
    private $client;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * 人脸融合
     *
     * @param  mixed               $mergeBase64
     * @param  mixed               $modelBase64
     * @return ResponseInterface
     */
    public function mergeFace($mergeBase64, $modelBase64)
    {
        $options = [
            'headers'     => [
                'User-Agent' => self::$userAgent,
            ],
            'form_params' => [
                'api_key'         => Di::getDefault()->getShared('config')->facePlusPlus['api_key'],
                'api_secret'      => Di::getDefault()->getShared('config')->facePlusPlus['api_secret'],
                'merge_base64'    => $mergeBase64,
                'template_base64' => $modelBase64,
                'merge_rate'      => 80,
                'feature_rate'    => 50,
            ],
        ];

        return $this->client->request('POST', self::$url['mergeface'], $options);
    }
}
