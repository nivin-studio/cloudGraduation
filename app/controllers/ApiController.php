<?php

class ApiController extends ControllerApi
{
    /**
     * 人脸融合接口
     *
     * @return json
     */
    public function mergeFaceAction()
    {
        if (!request()->isPost() || !$this->security->checkToken()) {
            return $this->JsonResponse([
                'code' => -1,
                'msg'  => '非法请求',
            ], 400);
        }

        $mergeBase64 = request('merge_base64');
        $mergeModel  = request('merge_model');
        $mergeBgimg  = request('merge_bgimg');

        $modelFile = config('application')['imagesDir'] . MergeFace::$models[$mergeModel];
        $bgimgFile = config('application')['imagesDir'] . MergeFace::$bgs[$mergeBgimg];
        $drainFile = config('application')['imagesDir'] . 'drain.jpg';

        $mergeFace   = new MergeFace();
        $modelBase64 = $mergeFace->handleTemplate($modelFile, $bgimgFile, $drainFile);
        $makeBase64  = $mergeFace->make($mergeBase64, $modelBase64);

        if ($makeBase64) {
            return $this->JsonResponse([
                'code'     => 0,
                'msg'      => 'ok',
                'data'     => $makeBase64,
                'tokenKey' => $this->security->getTokenKey(),
                'tokenVal' => $this->security->getToken(),
            ]);
        } else {
            return $this->JsonResponse([
                'code' => -1,
                'msg'  => '生成失败！',
            ]);
        }
    }
}
