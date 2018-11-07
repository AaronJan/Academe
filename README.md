# Academe

---

## 功能特性

Academe 是一个实用的 Data Mapper，有以下特性：

* 可以单独使用，也可以搭配 `Laravel` 使用

* 支持字段出、入库时进行转换(`Blueprint` 中的 `Cast Rule`)，例如将 MySQL 的 `datetime` 类型自动转化为 `Carbon` 对象。支持自定义扩展

* 简单直观的 `Query Builder`，IDE提示友好

* 查询条件和数据库连接分离，支持序列化

* 支持多种数据关系定义：一对一、一对多、多对多（可跨数据库类型关联）

* 可以自定义数据实体对象，想用`贫血模型`还是`充血模型`不受限制

* 以上功能同时支持 MySQL、PostgreSQL（基于 `DBAL`）和 MongoDB（基于 `mongodb/mongodb`）


## 安装

需要`mongodb`扩展支持(``>= 1.3`)。

使用`Composer`安装：

```
$ composer require aaronjan/academe
```


## 快速上手

### 安装

Academe 自带 Laravel 支持，以下以 Laravel 使用为例。

安装好之后，执行命令导入配置文件到 `config/academe.php`：

```
$ php artisan vendor:publish --provider=Academe\\Laravel\\AcademeServiceProvider
```

配置文件默认兼容 Laravel `.env` 中的数据库配置。

如果你的 Laravel 版本低于 `5.5`，那么还需要将 `AcademeServiceProvider` 加入到 `config/app.php`中。


### 生成第一个 `Blueprint`

你需要使用 `Blueprint` 来定义和使用数据。

例如有一个 MySQL 表：

```sql
CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

运行命令生成 `Blueprint`：

```
$ php artisan academe:make:blueprint User --primary=id --subject=user
```

*说明：MySQL 的 table 或者 MongoDB 的 Collection 在 Academe 中统一称作 `Subject`。*

打开刚生成的 Blueprint（默认路径：`app/Academe/Blueprints/User`），增加字段类型配置（位于 `castRules()`）：

```php
<?php

namespace App\Academe\Blueprints;

use Academe\BaseBlueprint;
use Academe\Casting\CasterMaker;

class User extends BaseBlueprint
{
    /**
     * @return string
     */
    public function primaryKey()
    {
        return 'id';
    }

    /**
     * @return string
     */
    public function subject()
    {
        return 'user';
    }

    /**
     * @return array
     */
    public function castRules()
    {
        // 增加两个字段的类型配置：
        return [
            'id'   => CasterMaker::integer(),
            'name' => CasterMaker::string(),
        ];
    }
}
```

### 编写查询

定义好 Blueprint 之后，就可以通过 `依赖注入` 的方式拿到 `Mapper` 来进行和数据库之间的交互了：

```php
<?php

use Academe\Contracts\Academe;
use Academe\Contracts\Writer;
use App\Academe\Blueprints\User;

class MyController {

    public function index(Academe $academe, Writer $writer) {
        $userMapper = $academe->getMapper(User::class);

        // 查询所有数据，按年龄正序排列
        $userMapper->query()->sort('age', 'asc')->all();

        // 条件查询并指定字段
        $userMapper->query()->equal('id', 1)->first(['name']);

        // 查询null值
        $userMapper->query()->isNull('money')->notNull('mortgage')->all();

        // 新增数据
        $userMapper->query()->create([
            'name' => 'John',
        ]);

        // 更新数据
        $userMapper->query()->equal('id', 1)->update([
            'score' => 100,
        ]);

        // 更新数据（更新值使用原生SQL，同样支持MongoDB）
        $userMapper->query()->equal('id', 1)->update([
            'score' => $writer->raw('`score` + 1'),
        ]);

        // 删除数据
        $userMapper->query()->equal('id', 2)->delete();
    }

}

```

所有数据操作都可以通过调用 `Mapper` 上的 `query()` 方法完成（所有数据库通用），部分 MongoDB 专有方法可以通过 `queryAsMongoDB()`完成。


### 独立生成查询条件

除了通过 `Mapper` 上的 `query()` 方法组织查询条件，还可以通过 `Writer` 生成条件，这适用于很多需要由某个函数生成部分或全部查询条件的情况：

```php
<?php

use Academe\Contracts\Academe;
use Academe\Contracts\Writer;
use App\Academe\Blueprints\User;

class MyController {

    // Writer 实例可以通过依赖注入的方式获取，或者调用 `$academe->getWriter()`
    public function index(Academe $academe, Writer $writer) {
        $userMapper = $academe->getMapper(User::class);

        // 单个查询条件
        $condition = $writer->equal('id', 1);

        $userMapper->query()->apply($condition)->all();

        // 组合查询条件：and
        $conditions = $writer->must([
            $writer->equal('id', 1),
            $writer->notEqual('name', 'John'),
        ]);

        // 组合查询条件：or
        $conditions = $writer->any([
            $writer->equal('id', 1),
            $writer->notEqual('name', 'John'),
        ]);

        // query builder形式，甚至可以在这里指定需要取出的字段
        $statement = $writer->query()->equal('id', 1)->fields(['id', 'name']);

        $userMapper->query()->apply($statement)->all();
    }

}

```


### 数据关系

数据关系的定义同样是写在 Blueprint 中的：

```php
<?php

namespace App\Academe\Blueprints;

use Academe\BaseBlueprint;
use Academe\Casting\CasterMaker;
use Academe\Relation;

class User extends BaseBlueprint
{
    /**
     * @return string
     */
    public function primaryKey()
    {
        return 'id';
    }

    /**
     * @return string
     */
    public function subject()
    {
        return 'user';
    }

    /**
     * 这里定义数据关系
     *
     * @return array
     **/
    public function relations()
    {
        return [
            // 假设我们还有一个名为 Post 的 Blueprint
            'posts' => new Relation\HasMany(Post::class, 'user_id', 'id'),
        ];
    }

    /**
     * @return array
     */
    public function castRules()
    {
        // 增加两个字段的类型配置：
        return [
            'id'   => CasterMaker::integer(),
            'name' => CasterMaker::string(),
        ];
    }
}
```

查询关系数据的方式和 Laravel 基本一致：

```php
<?php

use Academe\Contracts\Academe;
use Academe\Contracts\Writer;
use Academe\Statement\RelationSubStatement;
use App\Academe\Blueprints\User;

class MyController {

    // Writer 实例可以通过依赖注入的方式获取，或者调用 `$academe->getWriter()`
    public function index(Academe $academe, Writer $writer) {
        $userMapper = $academe->getMapper(User::class);

        // 关联所有数据
        $users = $userMapper->query()->with('posts')->all();
        $posts = array_first($users)->posts;

        // 带条件关联所有数据
        $users = $userMapper->query()->with([
            'posts' => function (RelationSubStatement $statement) {
                // 添加关联查询条件
                // 注意：这里如果要指定查询字段，一定要包含定义关系时所需要的字段，否则会导致数据无法关联
                //   同样，用来聚合的根数据也需要包含定义关系的字段
                $statement->eauql('state', 2)->fields(['id', 'user_id']);
            },
        ])->all();
    }

}
```

Academe 目前支持定义两种数据关系：一对一、一对多和多对多，其中又分为几种变体：

#### 一对一

* `HasOne`：一对一


#### 一对多

* `BelongsTo`：多对一，主、外键形式

* `HasMany`：一对多，主、外键形式

* `WithMany`：一对多，根数据上的关系字段为数组形式（MySQL 上为 JSON 化的数组），子数据依然是定义外键

* `WithManyPredefined`：一对多，不同于 `WithMany` 的在于子数据不是来自数据库，而是预先定义好的数组结构（后期也许会考虑通过新的 `Connection` 实现）


#### 多对多

* `BelongsToMany`：多对多，通过中间关系表关联，和 Laravel 一致

多对多因为需要一张中间表，所以也需要定义中间表的 Blueprint：`Bond`。运行 Artisan 命令进行生成：

```
$ php artisan academe:make:bond UserAndPost --primary=id --subject=relation_user_and_post --host=User:id --guest=Post:id
```

生成文件如下：

```php
<?php

namespace App\Academe\Bonds;

use Academe\BaseBond;
use Academe\Casting\CasterMaker;
use Academe\Relation;
use App\Academe\Blueprints;

class UserAndPost extends BaseBond
{
    /**
     * @return string
     */
    public function primaryKey()
    {
        return 'id';
    }

    /**
     * @return string
     */
    public function subject()
    {
        return 'relation_user_and_post';
    }

    /**
     * @return string
     **/
    public function hostBlueprintClass()
    {
        return Blueprints\User::class;
    }

    /**
     * @return string
     **/
    public function hostKeyField()
    {
        return 'id';
    }

    /**
     * @return string
     **/
    public function guestBlueprintClass()
    {
        return Blueprints\Post::class;
    }

    /**
     * @return string
     **/
    public function guestKeyField()
    {
        return 'id';
    }

    /**
     * @return array
     */
    public function castRules()
    {
        return [
            // eg: 'name' => CasterMaker::string()
        ];
    }
}

```


### 事务

事务是围绕 `Transaction` 对象展开的，以下是实例：

```php
<?php

use Academe\Contracts\Academe;
use Academe\Contracts\Writer;
use Academe\Statement\RelationSubStatement;
use App\Academe\Blueprints\User;

class MyController {

    // Writer 实例可以通过依赖注入的方式获取，或者调用 `$academe->getWriter()`
    public function index(Academe $academe, Writer $writer) {
        $userMapper = $academe->getMapper(User::class);

        // Academe 和 Writer 都可以创建 Transaction 对象
        $transaction = $writer->newTransaction();

        // 创建时可以指定隔离级别：
        $transaction = $academe->newTransaction(\Academe\Constant\TransactionConstant::READ_UNCOMMITTED);

        // Transaction 可以统一设置查询锁：
        $transaction = $transaction->lockSelect();

        // 将所有和事务相关的 Mapper 加入到事务对象中（关系数据的 Mapper 也需要手动加入）：
        $userMapper->involve($transaction);

        // 开始事务：
        $transaction->begin();

        try {
            // 对数据进行操作：
            // ...

            $transaction->commit();
        } catch (\Exception $exception) {
            $transaction->rollback();

            throw $exception;
        }


    }

}

```

事务操作目前对 MongoDB 是无效的，等到 `MongoDB 4.0` 正式发布之后会考虑提供支持。


## MongoDB

### `$elemMatch`

```php
<?php

$mapper->queryAsMongoDB()->elementMatch('array', $writer->query()->equal('inner_field', 'value'))->all();

```


## TODOs

* 单元测试

* 查询关系数据时传递父级的事务设置


## Credits

[Laravel](https://github.com/laravel/framework)

[Opulence](https://github.com/opulencephp/Opulence)

[Waterline](https://github.com/balderdashy/waterline)

[Laravel MongoDB](https://github.com/jenssegers/laravel-mongodb)

[Ran Ding](https://github.com/randing89)
