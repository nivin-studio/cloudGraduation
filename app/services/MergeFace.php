<?php

use FacePlusPlus\ImageRecognition;
use GuzzleHttp\Exception\GuzzleException;

class MergeFace extends ServiceBase
{
    /**
     * 模特素材
     *
     * @var array
     */
    public static $models = [
        'boy1'  => 'boy1.png',
        'girl1' => 'girl1.png',
    ];

    /**
     * 背景素材
     *
     * @var array
     */
    public static $bgs = [
        'bg1' => 'bg1.jpg',
        'bg2' => 'bg2.jpg',
        'bg3' => 'bg3.jpg',
        'bg4' => 'bg4.jpg',
    ];

    /**
     * 处理模板图
     *
     * @param  string  $modelFile 模特图片文件
     * @param  string  $bgimgFile 背景图片文件
     * @param  string  $drainFile 引流图片文件
     * @return mixed
     */
    public function handleTemplate($modelFile, $bgimgFile, $drainFile = '')
    {
        // 判断文件存不存
        if (!file_exists($modelFile) || !file_exists($bgimgFile)) {
            return false;
        }

        $modelIm = new Imagick($modelFile);
        $bgimgIm = new Imagick($bgimgFile);
        // 模特照片固定宽高
        $modelW = 520;
        $modelH = 750;
        // 模特贴合背景图的位置参数
        $marginL = ($bgimgIm->getImageWidth() - $modelW) / 2;
        $marginT = ($bgimgIm->getImageHeight() - $modelH);
        // 将模特贴合到背景图上
        $bgimgIm->compositeImage($modelIm, Imagick::COMPOSITE_OVER, $marginL, $marginT);

        if ($drainFile != '' && file_exists($drainFile)) {
            $drainIm = new Imagick($drainFile);
            $finalIm = new Imagick();
            $finalIm->newImage(1080, 1380, new ImagickPixel('white'));
            $finalIm->setImageFormat('png');

            $finalIm->compositeImage($bgimgIm, Imagick::COMPOSITE_OVER, 0, 0);
            $finalIm->compositeImage($drainIm, Imagick::COMPOSITE_OVER, 0, 1080);

            $modelBase64 = chunk_split(base64_encode($finalIm));
            $modelIm->destroy();
            $bgimgIm->destroy();
            $drainIm->destroy();
            $finalIm->destroy();

        } else {
            $modelBase64 = chunk_split(base64_encode($bgimgIm));
            $modelIm->destroy();
            $bgimgIm->destroy();
        }

        return $modelBase64;
    }

    /**
     * 人脸融合生成
     *
     * @param  mixed              $mergeBase64
     * @param  mixed              $modelBase64
     * @throws GuzzleException
     * @return mixed
     */
    public function make($mergeBase64, $modelBase64)
    {
        try {
            $imageRecognition = new ImageRecognition();

            $req = $imageRecognition->mergeFace($mergeBase64, $modelBase64);
            if ($req->getStatusCode() == 200) {
                return json_decode($req->getBody())->result;
            }
        } catch (GuzzleException $e) {
            $this->log->log($e->getMessage());
        }

        return false;
    }
}
