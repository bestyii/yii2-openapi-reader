Yii2 OpenApi Specification Reader 模块
============
原理是采用php的注释来写api文档，注释的语法采用php annotation方式进行解析。
解析后符合OpenAPI  Specification 规范，可以通过 swagger UI 或 Redoc 进行渲染成可读性强带有交互的api文档。

swagger UI

![alt swagger UI](https://static1.smartbear.co/swagger/media/images/tools/opensource/swagger_ui.png?ext=.png "swagger UI")

Redoc：

![alt redoc](https://raw.githubusercontent.com/Redocly/redoc/master/demo/redoc-demo.png "redoc")

这个模块集成了：
* [swagger-php](https://github.com/zircote/swagger-php) 
* [swagger-ui v3](https://github.com/swagger-api/swagger-ui)
* [Redocly/redoc](https://github.com/Redocly/redoc)


安装 Installation
------------

通过 [composer](http://getcomposer.org/download/)安装.

项目中直接运行

```
php composer.phar require bestyii/yii2-openapi-reader:dev-master
```

或者添加下面代码到 `composer.json`文件

```
"bestyii/yii2-openapi-reader": "dev-master"
```


使用 Usage
-----

Once the extension is installed, simply use it in your code by  :

You set url, where locate json file OR set path for scan

```php
'modules' => [
      ...
      'openapireader' => [
            'class' => \bestyii\openapiReader\Module::class,
            'defaultDoc'=>'api',
            'path' => [
                 'api'=>'@app/modules/api',
                 'openwork'=>'@app/modules/openwork',
                 ],
            // disable page with your logic
            'isDisable' => function () {
                return false;
            },
            // replace placeholders in swagger content
            'afterRender' => function ($content) {
                $content = str_replace('{{host}}', 'http://example.com', $content);
                $content = str_replace('{{basePath}}', '/api/v1', $content);
                return $content;
            }
       ]
        ...
    ],
```


现在就可以访问你的API文档了

```
# swagger 风格
http://yoururl.com/openapireader

# redoc 风格
http://yoururl.com/openapireader/default/redoc
```

示例 Module
```php

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="0.0.1",
 *         title="OpenApi",
 *         description="This is a sample server Petstore server.  You can find out more about Swagger at [http://swagger.io](http://swagger.io) or on [irc.freenode.net, #swagger](http://swagger.io/irc/).  For this sample, you can use the Bearer `access token` to test the authorization filters.",
 *     ),
 *     @OA\Server(
 *         description="Test",
 *         url="http://api.bestyii.com/api/"
 *     ),
 *     @OA\Server(
 *         description="Prod",
 *         url="http://api.bestyii.com/v2/"
 *     ),
 *     @OA\ExternalDocumentation(
 *         description="更多关于达卡拉的信息",
 *         url="http://bestyii.com"
 *     )
 * )
 */

/**
 * @OA\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="http",
 *   scheme="bearer",
 *   in="header",
 *   bearerFormat="JWT"
 * )
 * https://swagger.io/docs/specification/authentication/basic-authentication/
 */

```

示例 controller
```php
<?php

namespace app\modules\api\controllers;

use app\modules\api\models\UserIdentity;
use Yii;
use app\modules\api\models\User;
use yii\data\ActiveDataProvider;
use app\modules\api\components\ActiveController;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * @OA\Tag(
 *   name="Users",
 *   description="用户账号",
 *   @OA\ExternalDocumentation(
 *     description="更多相关",
 *     url="http://bestyii.com"
 *   )
 * )
 */
class UserController extends ActiveController
{
    public $modelClass = 'app\modules\api\models\UserIdentity';

    /**
     * @OA\Get(
     *     path="/users",
     *     summary="查询 User",
     *     tags={"Users"},
     *     description="",
     *     operationId="findUser",
     *     @OA\Parameter(
     *         name="ids",
     *         in="query",
     *         description="逗号隔开的 id",
     *         required=false,
     *         @OA\Schema(
     *           type="integer",
     *           @OA\Items(type="int20"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="查询成功",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="无效的id",
     *     ),
     *   security={{
     *     "bearerAuth":{}
     *   }}
     * )
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => UserIdentity::find(),
        ]);
        return $dataProvider;
    }

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="通过ID显示详情",
     *     description="",
     *     operationId="getUserById",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         description="id",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="无效的ID"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="没有找到相应资源"
     *     ),
     *   security={{
     *     "bearerAuth":{}
     *   }}
     * )
     */
    public function actionView($id)
    {
        return $this->findModel($id);
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     tags={"Users"},
     *     operationId="addUser",
     *     summary="添加",
     *     description="",
     *   @OA\RequestBody(
     *       required=true,
     *       description="创建 User 对象",
     *       @OA\JsonContent(ref="#/components/schemas/User"),
     *       @OA\MediaType(
     *           mediaType="application/x-www-form-urlencoded",
     *           @OA\Schema(
     *               type="object",
     *               ref="#/components/schemas/User"
     *          ),
     *       )
     *   ),
     *     @OA\Response(
     *         response=201,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="无效的输入",
     *     ),
     *   security={{
     *     "bearerAuth":{}
     *   }}
     * )
     */
    public function actionCreate()
    {
        $model = new UserIdentity();
        if ($model->load(Yii::$app->getRequest()->getBodyParams(), '') && $model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return $model;
    }

    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     tags={"Users"},
     *     operationId="updateUserById",
     *     summary="更新指定ID数据",
     *     description="",
     *     @OA\Parameter(
     *         description="id",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *     ),
     *   @OA\RequestBody(
     *       required=true,
     *       description="更新 User 对象",
     *       @OA\JsonContent(ref="#/components/schemas/User"),
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(ref="#/components/schemas/User")
     *       )
     *   ),
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="无效的ID",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="没有找到相应资源",
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="数据验证异常",
     *     ),
     *   security={{
     *     "bearerAuth":{}
     *   }}
     * )
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->getBodyParams(), '') && $model->save()) {
            Yii::$app->response->setStatusCode(200);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }
        return $model;
    }

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="删除User",
     *     description="",
     *     operationId="deleteUser",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         description="需要删除数据的ID",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="没有找到相应资源"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="无效的ID"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="没有找到相应资源"
     *     ),
     *   security={{
     *     "bearerAuth":{}
     *   }}
     * )
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->softDelete() === false) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }
        Yii::$app->getResponse()->setStatusCode(204);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserIdentity::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested User does not exist.');
    }
}

```

示例 model
```php

/**
 * @OA\Schema(
 *      schema="User",
 *      required={"username"},
 *     @OA\Property(
 *        property="id",
 *        description="User ID",
 *        type="integer",
 *        format="int64",
 *    ),
 *     @OA\Property(
 *        property="username",
 *        description="用户名",
 *        type="string",
 *        maxLength=100,
 *    ),
 *     @OA\Property(
 *        property="email",
 *        description="邮箱",
 *        type="string",
 *        maxLength=100,
 *    ),
 *     @OA\Property(
 *        property="password",
 *        description="密码",
 *        type="string",
 *        maxLength=64,
 *    ),
 *     @OA\Property(
 *        property="created_at",
 *        description="创建时间",
 *        type="string",
 *        default="0",
 *    ),
 *     @OA\Property(
 *        property="updated_at",
 *        description="更新时间",
 *        type="string",
 *        default="0",
 *    ),
 *     @OA\Property(
 *        property="last_login_at",
 *        description="最后登录时间",
 *        type="string",
 *        default="0",
 *    ),
 *     @OA\Property(
 *        property="ip",
 *        description="登录IP ip2long",
 *        type="integer",
 *        format="int64",
 *        default=0,
 *    ),
 *)
 */
```


### TODO
- add cache
- add customization
