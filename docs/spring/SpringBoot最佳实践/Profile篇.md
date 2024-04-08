`Spring`配置文件提供了一种隔离应用程序配置的方法，使其仅在特定环境中可用。任何`@Component`、`@Configuration`或`@ConfigurationProperties`都可以标记为`@Profile`。

## 配置特定文件

除了应用程序属性文件外，`Spring Boot`还会尝试使用命名约定的`application-{profile}`加载特定于配置文件的文件。例如，如果您的应用程序激活了名为`prod`的配置文件并使用`YAML`文件，那么`application.yaml`和`application-prod.yaml`都将被考虑。

### 激活特定文件

配置特定文件属性的加载位置与标准`application.properties`相同，特定文件总是优先于非特定文件。如果指定了多个特定文件，则采用后胜策略。例如，如果通过`spring.profiles.active`属性指定了配置文件`prod`、`live`，`application-prod.properties`中的值就会被`application-live.properties`中的值覆盖。

- 命令行参数激活

```shell
[home$] java -jar my-application.jar --spring.profiles.active=dev
```

- 应用程序属性文件激活

```
#application.properties

spring.profiles.active=dev
```

- 环境变量激活

```shell
[home$] export SPRING_PROFILES_ACTIVE=dev
```

- JVM系统属性激活

```shell

[home$] java -jar -Dspring.profiles.active=dev my-application.jar
```

- Web应用程序上下文参数激活

在`web.xml`文件中填写上下文参数。

```xml
<context-param>
    <param-name>spring.profiles.active</param-name>
    <param-value>dev</param-value>
</context-param>
```

### 激活特定Bean

```java

@Configuration
@Profile("dev")
public class DevelopmentConfiguration {
    // Development-specific configurations
}

@Configuration
@Profile("prod")
public class ProdConfiguration {
    // Prod-specific configurations
}

@Component
@Profile("dev")
public class DevelopmentComponent {
    // Development-specific component
}

```
