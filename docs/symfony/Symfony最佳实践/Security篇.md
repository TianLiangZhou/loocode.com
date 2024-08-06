`Symfony`提供了许多工具来保护您的应用程序。默认提供一些与`HTTP`相关的安全工具，例如安全会话`cookie`和`CSRF`保护。`Security`组件是`Symfony`框架为保护应用程序所需的所有身份验证和授权功能。`Symfony Security`的设计和`Java`的`Spring Security`是如出一辙的。

在启用安全组件之前，你需要安装组件：

```shell
$ composer require symfony/security-bundle
```

安装完之后会创建一个默认的配置文件`security.yaml`。建议将文件改成原生PHP配置`security.php`。

```php
// config/packages/security.php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security, ContainerConfigurator $configurator) {
    
    // 设置密码生成器的实现, 配置为自动后，它会自动选择并迁移最好的密码生成算法 
    // 可能的哈希算法(sodium, bcrypt, legacy)
    $security->passwordHasher(PasswordAuthenticatedUserInterface::class)->algorithm('auto');

    // 用户提供商: 支持内存、数据库、LDAP服务等提供商
    $memoryProvider = $security->provider('users_in_memory')
        ->memory()
    ;
    // $memoryProvider->user('admin')
    //                ->password('$2y$13$jxGxc ... IuqDju')
    //                ->roles(['ROLE_ADMIN']);

    // 防火墙及身份验证, symfony建议只创建一个主防火墙配置。
    // 防火墙是您的身份验证系统：防火墙定义了应用程序的哪些部分受到保护，以及用户如何进行身份验证（如登录表单、API 标记等）。
    $security->firewall('main')
        ->lazy(true)
        ->provider('users_in_memory')
    ;
    
    // 访问授权, roles必须是字符串ROLE_开头
    $security->accessControl()
        ->path("^/admin")
        ->roles("ROLE_ADMIN")
    ;
    $security->accessControl()
        ->path("^/profile")
        ->roles("ROLE_USER")
    ;
};

```

配置的依赖路径顺序: 

> 密码提供器<-用户提供商<-防火墙<-访问授权

#### 密码生成

您还可以通过运行以下命令手动散列密码：

```shell
$ php bin/console security:hash-password
```

## 用户提供商

`Symfony`中的权限总是与用户对象相关联。 如果需要确保应用程序（部分内容）的安全，就需要创建一个用户类。 这是一个实现`UserInterface`的类。 这通常是一个`Doctrine`实体，但也可以使用专用的安全用户类。

### 数据库提供商

在使用`doctrine`之前你可能需要安装`doctrine-bundle`: 

```shell
$ composer require doctrine/doctrine-bundle
```

生成用户实体类的最简单方法是使用`MakerBundle`中的`make:user`命令：


```shell
# 安装MakerBundle
$ composer require --dev symfony/maker-bundle
# 生成实体User类
$ php bin/console make:user
 The name of the security user class (e.g. User) [User]:
 > User

 Do you want to store user data in the database (via Doctrine)? (yes/no) [yes]:
 > yes

 Enter a property name that will be the unique "display" name for the user (e.g. email, username, uuid) [email]:
 > email

 Will this app need to hash/check user passwords? Choose No if passwords are not needed or will be checked/hashed by some other system (e.g. a single sign-on server).

 Does this app need to hash/check user passwords? (yes/no) [yes]:
 > yes

 created: src/Entity/User.php
 created: src/Repository/UserRepository.php
 updated: src/Entity/User.php
```

```php
// src/Entity/User.php
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * 唯一标识
     * The public representation of the user (e.g. a username, an email address, etc.)
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * 角色
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
```

> 如果您的用户是 Doctrine 实体，如上面的示例所示，请不要忘记通过创建并运行迁移来创建表：

```shell
$ php bin/console make:migration
# php bin/console doctrine:migrations:migrate
```

使用此提供商需要修改对应的`security.php`文件。

```php
// config/packages/security.php
use App\Entity\User;
use Symfony\Config\SecurityConfig;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (SecurityConfig $security, ContainerConfigurator $configurator): void {
    // ...

    $security->provider('app_user_provider')
        ->entity()
            ->class(User::class)
            ->property('email')
    ;
    $security->firewall('main')
        ->lazy(true)
        ->provider('app_user_provider') // 修改用户提供商
    ; 
    
};
```

### 内存提供商

内存提供商是直接将用户写在配置文件中，只需要提供用户名，密码，角色等信息就可以了。

```php
// config/packages/security.php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security, ContainerConfigurator $configurator) {
    // ...
    
    $memoryProvider = $security->provider('users_in_memory')
        ->memory()
    ;
    $memoryProvider->user('admin')
                   ->password('$2y$13$jxGxc ... IuqDju')
                   ->roles(['ROLE_ADMIN']);
    $memoryProvider->user('guest')
                   ->password('$2y$13$jxGxc ... IuqDju')
                   ->roles(['ROLE_USER']);
    $security->firewall('main')
        ->lazy(true)
        ->provider('users_in_memory')
    ;                    
                   
};

```

> 密码的生成可以通过[此处](#密码生成)

### LDAP提供商

本人未使用过`LDAP`服务, 可以从[此处](https://symfony.com/doc/current/security/ldap.html#security-ldap-user-provider)查看更新的文档信息。

## 防火墙

防火墙部分是安全组件是最重要的部分。 防火墙是您的身份验证系统：防火墙定义了应用程序的哪些部分受到保护，以及用户如何进行身份验证（如登录表单、API 标记等）。

你可以为不同的环境建立不同的防火墙规则，就像是这样的:

```php
// config/packages/security.php
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    // ...
    // 定义防火墙的顺序非常重要，
    // 因为请求将由模式匹配的第一个防火墙处理
    $security->firewall('dev')
        ->pattern('^/(_(profiler|wdt)|css|images|js)/')
        ->security(false)
    ;
    
    // 没有模式的防火墙应该最后定义，因为它将匹配所有请求
    $security->firewall('main')
        ->lazy(true)
    ;
};

```

当匹配多个路由时，您还可以使用一组更简单的正则表达式来匹配每个路由，而不是创建长正则表达式：

```php
    $security->firewall('dev')
        ->pattern([
            '^/_profiler/',
            '^/_wdt/',
            '^/css/',
            '^/images/',
            '^/js/',
        ])
        ->security(false)
    ;

```

### 用户身份验证

在身份验证过程中，系统尝试为网页访问者找到匹配的用户。传统上，这是使用登录表单或HTTP基本对话框来完成的。

身份验证目前支持多种方式，如： [表单验证](#表单验证)，[JSON](#JSON验证)，[HTTP Basic](#HTTP-Basic), [Login Link](#Login-Link), [自定义验证](#Custom-Authenticators)

#### 表单验证

表单验证就是传统的登录表单，用户可以使用标识符（例如电子邮件地址或用户名）和密码进行身份验证。

可以通过下面命令来生成一个登录表单需要的文件和配置：

```shell
$ php bin/console make:security:form-login
```

生成的控制器文件和模板文件。

```php
// src/Controller/LoginController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // 控制不需要写任何验证密码和用户逻辑，安全组件会根据配置的checkPath拦截处理
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
}

```

```twig
{# templates/login/index.html.twig #}
{% extends 'base.html.twig' %}

{# ... #}

{% block body %}
    {% if error %}
        <div>{{ error.messageKey|trans(error.messageData, 'security') }}</div>
    {% endif %}

    <form action="{{ path('app_login') }}" method="post">
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" value="{{ last_username }}" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="_password" required>

        {# If you want to control the URL the user is redirected to on success
        <input type="hidden" name="_target_path" value="/account"> #}

        <button type="submit">login</button>
    </form>
{% endblock %}
```

```php
// config/packages/security.php
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    // ...

    $mainFirewall = $security->firewall('main');

    // “app_login”是之前创建的路由的名称
    $mainFirewall->formLogin()
        ->loginPath('app_login')
        ->checkPath('app_login')
        // 设置模板中的用户名参数名
        ->usernameParameter("email") 
        // 设置模板中的密码参数名
        ->passwordParameter("password")
    ;
};
```
> 你还可以为表单开启csrf功能，以防止受到Csrf跨站攻击。

#### JSON验证

在前后端分离时，我们常常使用`ajax` JSON来提交数据。安全组件也内置了这种验证方式，这种就不需要配置模板文件了。

```php
// config/packages/security.php
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    // ...

    $mainFirewall = $security->firewall('main');
    $mainFirewall->jsonLogin()
        ->checkPath('api_login')
        // 同表单的意义一样，用户名的json路径
        ->usernamePath('email')
        // 同表单的意义一样，密码的json路径
        ->passwordPath('password')
    ;
};
```

```php
// src/Controller/LoginController.php
   #[Route('/api/login', name: 'api_login', method: ['POST'])]
    public function index(#[CurrentUser] ?User $user): Response
    {
        if ($user == null) {
            return $this->json([
                'message' => 'missing credentials'
            ],  Response::HTTP_UNAUTHORIZED);
        }
        // 控制不需要写任何验证密码和用户逻辑，安全组件会根据配置的checkPath拦截处理
        $token = "create token";
        return $this->json([
            'message' => 'success',
            'user'  => $user->getUserIdentifier(),
            'token' => $token,
        ]);
    }
```

根据上面的配置信息，客户端（例如前端）使用`Content-Type: application/json`标头向`/api/login`发出`POST`请求，并使用用户名（即使您的标识符实际上是电子邮件）和密码密钥: 

```json
{
    "email": "admin@example.com",
    "password": "MyPassword"
}
```

安全组件拦截请求，检查用户提交的凭据并对用户进行身份验证。如果凭据不正确，则会返回`HTTP 401 Unauthorized JSON`响应，否则您的控制器将运行；

```json
{
    "user": "admin@example.com",
    "token": "你创建的token"
}

```


#### HTTP Basic

[HTTP Basic](https://en.wikipedia.org/wiki/Basic_access_authentication)身份验证是一个标准化的HTTP身份验证框架。它使用浏览器中的对话框询问凭据（用户名和密码），Symfony的HTTP基本身份验证器将验证这些凭据。

```php
// config/packages/security.php
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    $mainFirewall = $security->firewall('main');
    $mainFirewall->httpBasic()
        ->realm('Secured Area')
    ;
};

```

每当未经身份验证的用户尝试访问受保护的页面时，Symfony就会通知浏览器需要启动HTTP基本身份验证（使用 WWW-Authenticate 响应头）。 然后，验证器会验证凭证并对用户进行身份验证。


#### Login Link

登录链接是一种无密码身份验证机制。用户将收到一个短暂的链接（例如通过电子邮件），该链接将向网站验证他们的身份。常用的比如从邮箱链接点过来直接登录。

您可以在[如何使用无密码登录链接身份验证](https://symfony.com/doc/current/security/login_link.html)中了解有关此身份验证器的所有信息。

#### Access Tokens

访问令牌通常在 API 上下文中使用。用户从授权服务器接收一个令牌，该令牌对他们进行身份验证。通过此方式可以集成`Oauth2.0`的提供商集成。

您可以在[如何使用访问令牌身份验证](https://symfony.com/doc/current/security/access_token.html)中了解有关此身份验证器的所有信息。

```php
// config/packages/security.php
use App\Security\AccessTokenHandler;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    $security->firewall('main')
        ->accessToken()
            ->tokenHandler(AccessTokenHandler::class)
    ;
};
```

#### X.509 Client Certificates

使用客户端证书时，您的Web服务器会自行完成所有身份验证。 `Symfony`提供的 X.509 身份验证器从客户端证书的“专有名称”(DN) 中提取电子邮件。
然后，它使用该电子邮件作为用户提供程序中的用户标识符。 首先，配置您的 Web 服务器以启用客户端证书验证并将证书的 DN 公开给 Symfony 应用程序：

```nginx
server {
    # ...

    ssl_client_certificate /path/to/my-custom-CA.pem;

    # enable client certificate verification
    ssl_verify_client optional;
    ssl_verify_depth 1;

    location / {
        # pass the DN as "SSL_CLIENT_S_DN" to the application
        fastcgi_param SSL_CLIENT_S_DN $ssl_client_s_dn;

        # ...
    }
}
```

```php
// config/packages/security.php
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    $mainFirewall = $security->firewall('main');
    $mainFirewall->x509()
        ->provider('your_user_provider')
    ;
};

```

默认情况下，Symfony 会通过两种不同方式从 DN 中提取电子邮件地址：
首先，它会尝试使用Apache公开的 SSL_CLIENT_S_DN_Email 服务器参数；
如果未设置该参数（例如使用 Nginx 时），则会使用 SSL_CLIENT_S_DN，并匹配`emailAddress`后面的值。


#### Remote Users

除了客户端证书身份验证之外，还有更多的Web服务器模块可以对用户进行预身份验证（例如 kerberos）。远程用户验证器为这些服务提供了基本的集成。
这些模块通常会在`REMOTE_USER`环境变量中公开经过身份验证的用户。远程用户验证器使用该值作为用户标识符来加载相应的用户。

```php
// config/packages/security.php
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    $mainFirewall = $security->firewall('main');
    $mainFirewall->remoteUser()
        ->provider('your_user_provider')
    ;
};
```

#### Custom Authenticators

在组件内置的验证方法不支持的时候，你可以使用自定义的验证程序来应用程序的验证逻辑。比如微信小程序的手机号登录，公众号登录等等。

在后面的实践中，笔者将会实现一个自定义的小程序手机号验证方法。

### 注销

要启用注销，请在防火墙下激活注销配置参数：

```php
// config/packages/security.php
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    // ...
    // 不需要编写任何注销逻辑，安全组件会自动拦截配置文件path参数的值来处理.
    $mainFirewall = $security->firewall('main');
    // ...
    $mainFirewall->logout()
        ->path('/logout')
        // 注销后跳转的路由
        // ->target('app_any_route')
    ;
};
```

> 如果你需要改变退出的返回结果，你可以订阅`LogoutEvent`事件来实现.


## 访问控制

用户在使用上面的验证方式登录到您的应用程序后。 你可以通过此配置让用户访问那些资源。 这就是所谓的授权，其任务是决定用户是否可以访问某些资源（URL、模型对象、方法调用......）。

授权过程有两个不同的方面：
1. 用户登录时会收到一个特定的角色（如 ROLE_ADMIN）。 
2. 您可以添加代码，使资源（如 URL、控制器）需要特定的 "属性"（如 ROLE_ADMIN 等角色）才能访问。

### 角色

当用户登录时，`Symfony`会调用`User`对象上的`getRoles()`方法来确定该用户的角色。 在之前生成的`User`类中，角色是一个存储在数据库中的数组，每个用户总是至少有一个角色： 如`ROLE_USER`：

```php
// src/Entity/User.php

// ...
class User
{
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    // ...
    public function getRoles(): array
    {
        $roles = $this->roles;
        // 默认角色 ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }
}

```

但你也可以根据自己的需要来决定用户应该拥有哪些角色。 唯一的规则是，每个角色都必须以`ROLE_`前缀开头，否则就无法正常工作。 除此之外，角色只是一个字符串，你可以根据需要随意编造（例如ROLE_PRODUCT_ADMIN）。

### 层级角色

您可以通过创建角色层次结构来定义角色继承规则，而不是为每个用户提供许多角色：

```php
// config/packages/security.php
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    // ...

    $security->roleHierarchy('ROLE_ADMIN', ['ROLE_USER']);
    $security->roleHierarchy('ROLE_SUPER_ADMIN', ['ROLE_ADMIN', 'ROLE_ALLOWED_TO_SWITCH']);
};
```

### 资源访问权限

```php
// config/packages/security.php
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    $security->enableAuthenticatorManager(true);

    // ...
    $security->firewall('main')
    // ...
    ;

    $security->accessControl()
        ->path('^/admin')
        ->roles(['ROLE_ADMIN']);

    $security->accessControl()
        ->path('^/admin')
        ->roles(['ROLE_ADMIN', 'IS_AUTHENTICATED_FULLY']);

    $security->accessControl()
        ->path('^/api/(post|comment)/\d+$')
        ->roles(['ROLE_USER']);
};

```

您可以根据需要定义任意数量的`URL`模式 - 每个模式都是正则表达式。但是，每个请求只会匹配一个：`Symfony`从列表顶部开始，并在找到第一个匹配时停止：

你也可以通过代码层面来限制访问。

```php
// src/Controller/AdminController.php
// ...

public function adminDashboard(): Response
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');
}
```

你也可以合使用属性的方式来限制访问工作。

```php

// src/Controller/AdminController.php
// ...

use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    // Optionally, you can set a custom message that will be displayed to the user
    #[IsGranted('ROLE_SUPER_ADMIN', message: 'You are not allowed to access the admin dashboard.')]
    public function adminDashboard(): Response
    {
        // ...
    }
}
```

在模板中使用访问限制：

```html
{% if is_granted('ROLE_ADMIN') %}
    <a href="...">Delete</a>
{% endif %}
```

### 匿名用户

部分资源需要公开给匿名用户访问: 

```php

// config/packages/security.php
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    $security->enableAuthenticatorManager(true);
    // ....

    // 允许未授权访问登录表单
    $security->accessControl()
        ->path('^/admin/login')
        ->roles([AuthenticatedVoter::PUBLIC_ACCESS])
    ;

    // but require authentication for all other admin routes
    $security->accessControl()
        ->path('^/admin')
        ->roles(['ROLE_ADMIN'])
    ;
};
```

你肯定有一个疑问，这些授权的资源都是写入到配置文件中，如果我要实现一个动态的资源权限授权呢。比如后台管理人员不同角色不同的权限，随时都可以编辑改变。

动态权限您可以使用自定义[投票者](https://symfony.com/doc/current/security/voters.html)，可以通过检查令牌上的用户是否有权限访问。我们将在实践示例中实现一个动态权限的控制投票者。

以上也是安全组件中比较重要的功能配置项了，下面我们将通过实现几个完整的示例来学习下。

## 实践

1. [微信小程序验证器](https://loocode.com/post/symfony-security-xiao-cheng-xu-deng-lu-shou-quan-shi-jian)
2. GitHub验证器
3. 动态授权访问验证
