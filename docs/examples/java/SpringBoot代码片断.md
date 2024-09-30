### 获取所有已注册方法

```java
class Snippet {

    private final ApplicationContext ctx;

    
    public Snippet(ApplicationContext ctx) {
        this.ctx = ctx;
    }
    
    // 获取所有已注册的方法
    public void code() {
        RequestMappingHandlerMapping requestMappingHandlerMapping = (RequestMappingHandlerMapping)ctx.getBean("requestMappingHandlerMapping");
       
        Map<RequestMappingInfo, HandlerMethod> handlerMethodMap = requestMappingHandlerMapping.getHandlerMethods();
    }
}
    
```

### 获取原始路由地址

```java
    public void code(HttpServletRequest request) {
        String matchingPattern = (String) request.getAttribute(HandlerMapping.BEST_MATCHING_PATTERN_ATTRIBUTE)
    }
```
