<?php


namespace bestyii\openapiReader\controllers;


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
    public function actionIndex($uid = null)
    {

        $this->layout = '_clear';
        return $this->render('index', [
            'url' => Url::to(['default/yaml'], true),
            'accessToken' => !is_null($uid) &&  class_exists('\grazio\user\models\UserIdentity') ? (\grazio\user\models\UserIdentity::findIdentity($uid))->access_token : null
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
     * @param $doc
     * @return string 返回json格式的描述文档
     */
    public function actionJson()
    {
        return $this->getContent($this->module->path)->toJson();
    }

    /**
     * @param $doc
     * @return string 返回Yaml格式的描述文档
     */
    public function actionYaml()
    {
        $yaml = $this->getContent($this->module->path)->toYaml();
        if (is_callable($this->module->afterRender)) {
            $yaml = call_user_func($this->module->afterRender, $yaml);
        }

        return $yaml;
    }

    /**
     * The target OpenApi annotation.
     * @param $path
     * @return  \OpenApi\Annotations\OpenApi
     */
    public function getContent($path)
    {
        $path = is_array($path) ? $path : [$path];
        foreach ($path as $key => $value) {
            $path[$key] = \Yii::getAlias($value);
        }

        return \OpenApi\scan($path);
    }

}