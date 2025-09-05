# 项目初始化

## 初始化命令

##### 1. 安装 composer 依赖包

```php
composer install
```

##### 2. 发布 JWT 配置文件到项目 config/autoload 目录
php bin/hyperf.php vendor:publish hyperf-extension/jwt


##### 3. 生成 JWT 密钥（secret）和非对称密钥对（公钥/私钥）

```php
php bin/hyperf.php gen:jwt-secret
php bin/hyperf.php gen:jwt-keypair
```

##### 4. 执行数据库迁移，创建表结构

```php
php bin/hyperf.php migrate
```

##### 5. 数据库填充

```php
php bin/hyperf.php db:seed
```

# 使用方法

##### 获取jwt存储的用户信息
```php
// 数据是用户模型定义的 getJwtCustomClaims
$user = Context::get('user');
$user['id']
```

##### 通用资源类

```php
use App\Resource\Common\BaseResource;
return new BaseResource($list);
```

##### 分页返回数据

```php
use App\Resource\User\IndexResource;
return IndexResource::collection($list)->additional(withAdditional());
```

##### 通用异常
```php
throw new BusinessException(409, '手机号已存在');
```