<?php


namespace  bestyii\openapiReader\controllers;


use yii\helpers\Url;
use yii\web\Controller;

/**
 * Class DefaultController
 * @package  bestyii\openapiReader\controllers
 */
class DefaultController extends Controller
{

    /**
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = '_clear';
        return $this->render('index', [
            'url' => Url::to(['default/yaml'], true),
            'accessToken' =>  null
        ]);
    }

    public function actionRedoc()
    {
        $this->layout = '_clear';
        return $this->render('redoc', [
            'url' => Url::to(['default/json'], true),
        ]);
    }

    /**
     * 返回json格式的描述文档
     */
    public function actionJson()
    {
        return $this->getContent()->toJson();
    }

    /**
     * 返回Yaml格式的描述文档
     */
    public function actionYaml()
    {
        return $this->getContent()->toYaml();
    }
    /**
     * The target OpenApi annotation.
     *
     * @return  \OpenApi\Annotations\OpenApi
     */
    public function getContent()
    {
        $content = '';
        if ($this->module->path) {
            $path = \Yii::getAlias($this->module->path);
            $content = \OpenApi\scan($path);
            return $content;
        }

    }

}