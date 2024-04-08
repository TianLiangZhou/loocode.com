`Spring Boot`会在应用程序启动时自动从`classpath`根目录或者`classpath:/config`目录,以及当前目录或者当前目录的子目录`/config`查找并加载`application.properties`和`application.yaml`文件。

### 更改配置文件名

如果不喜欢使用`application`作为配置文件名，可以通过指定`spring.config.name`环境属性切换到其他文件名。

例如，要查找`myproject.properties`和`myproject.yaml`文件，可以按如下方式运行应用程序：

```bash
[$] java -jar myproject.jar --spring.config.name=myproject
```

### 指定配置文件

您还可以使用`spring.config.location`环境属性来引用明确的位置。该属性接受一个以逗号分隔的列表，其中包含一个或多个要检查的位置。

```bash
[$] java -jar myproject.jar --spring.config.location=\
    optional:classpath:/default.properties,\
    optional:classpath:/override.properties
```

> 如果位置是可选的，并且您不介意它们不存在，请使用前缀`optional:`。
> 如果`spring.config.location`是目录（而非文件），则应以`/`结尾: classpath:custom/, spring会根据`spring.config.name`来组成文件名。


使用`spring.config.location`配置的位置会取代默认位置。例如，如果在配置`spring.config.location`时使用了optional:classpath:/custom-config/,optional:file:./custom-config/值，则扫描的完整位置为:

1. optional:classpath:/custom-config/
2. optional:file:./custom-config/

如果要添加额外位置而不是替换它们，可以使用`spring.config.additional-location`。从附加位置加载的属性可以覆盖默认位置中的属性。

```bash
[$] java -jar myproject.jar --spring.config.additional-location=\
    optional:classpath:/custom-config/,\
    optional:file:./custom-config/
```

根据上面的配置扫描配置目录的列表为:

1. optional:classpath:/;optional:classpath:/config/
2. optional:file:./;optional:file:./config/;optional:file:./config/*/
3. optional:classpath:custom-config/
4. optional:file:./custom-config/

前面两条是系统默认的，后面是自己额外增加的。

### 通配符位置路径

如果配置文件位置的最后一个路径段包含`*`字符，则视为通配符位置。通配符会在加载配置时展开，因此直接子目录也会被检查。在`Kubernetes`等环境中，当配置属性有多个来源时，通配符位置尤其有用。

```bash
[$] java -jar myproject.jar --spring.config.additional-location=\
    optional:classpath:/custom-config/*,\
    optional:file:./custom-config/*
```

比如目录`custom-config/mysql/application.properties`和`custom-config/redis/application.properties`.这样就可以装不同的服务配置在不同的配置文件中。


> 通配符位置必须只包含一个 *，搜索目录位置时以 */ 结尾，搜索文件位置时以 */<filename> 结尾。带有通配符的位置会根据文件名的绝对路径按字母顺序排序。
> 通配符位置只适用于外部目录。不能在 classpath: 位置中使用通配符。

### 属性占位符

在使用`application.properties`和`application.yaml`中的值时，会通过现有的环境进行过滤，因此您可以引用以前定义的值（例如，从系统属性或环境变量中）。标准的`${name}`属性占位符语法可用于值的任何位置。属性占位符还可以指定默认值，使用 : 分隔默认值和属性名称，例如 ${name:default}。


```ini
app.name=MyApp
app.description=${app.name} is a Spring Boot application written by ${username:Unknown}
```

> 您应始终使用其规范形式（仅使用小写字母加-）来引用占位符中的属性名称。这将允许`Spring Boot`使用与放松绑定`@ConfigurationProperties`时相同的逻辑。
> 例如，${demo.item-price} 将从 application.properties 文件中获取 demo.item-price 和 demo.itemPrice 属性，并从系统环境中获取 DEMO_ITEMPRICE。如果使用 ${demo.itemPrice}，则不会考虑 demo.item-price 和 DEMO_ITEMPRICE。

### 多文档配置文件

`Spring Boot`允许您将单个文件分割成多个逻辑文档，每个文档都是独立添加的。文档按从上到下的顺序处理。后面的文档可以覆盖前面文档中定义的属性。

对于 application.yaml 文件，使用标准的 YAML 多文档语法。三个连续的连字符代表一个文档的结束和下一个文档的开始。

例如，以下文件有两个逻辑文档：

```yaml
spring:
  application:
    name: "MyApp"
---
spring:
  application:
    name: "MyCloudApp"
  config:
    activate:
      on-cloud-platform: "kubernetes"
```

对于`application.properties`文件，使用特殊的`#---`或`!---`注释来标记文档分割：

```ini
spring.application.name=MyApp
#---
spring.application.name=MyCloudApp
spring.config.activate.on-cloud-platform=kubernetes
```
