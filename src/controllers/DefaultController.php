<?php


namespace bestyii\openapiReader\controllers;


use app\modules\api\models\UserIdentity;
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
    public function actionIndex($doc = null, $uid = null)
    {
        if(is_null($doc) && is_array($this->module->path)){
            $doc = $this->module->defaultDoc;
        }
        $this->layout = '_clear';
        return $this->render('index', [
            'url' => Url::to(['default/yaml','doc'=>$doc], true),
            'accessToken' => !is_null($uid) ? (UserIdentity::findIdentity($uid))->access_token : null
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
    public function actionJson($doc = null)
    {
        $docPath = $this->module->path;
        $path = !is_null($doc) && isset($docPath[$doc]) ? \Yii::getAlias($docPath[$doc]) : \Yii::getAlias($this->module->path);

        return $this->getContent($path)->toJson();
    }

    /**
     * @param $doc
     * @return string 返回Yaml格式的描述文档
     */
    public function actionYaml($doc = null)
    {
        $docPath = $this->module->path;
        $path = !is_null($doc) && isset($docPath[$doc]) ? \Yii::getAlias($docPath[$doc]) : \Yii::getAlias($this->module->path);

        return $this->getContent($path)->toYaml();
    }

    /**
     * The target OpenApi annotation.
     * @param $path
     * @return  \OpenApi\Annotations\OpenApi
     */
    public function getContent($path)
    {

        return \OpenApi\scan($path);

    }

}