## Change Log


### 🎉 v2.0 - [19-08-2022]
- This is a major ***breaking*** update
- Switched to PHP 7.4 for type declaration compatibility
- Added PHPDoc for contributors and users
- Added "template" engine for views
- Added support for route chaining via the `->route()` method **(experimental)**
- Refactored and improved code - SnakeCase is used for uniformity and improved readability
- `render` has now replaced the Response class method `use` with support for data and template variables
- The `Helper` sub-namespace is now `HTTP`.
- Added (multi-layer) middleware support for all verbs/methods
- Added support for custom 405 error page
- Added support for components that have access to the data tree using the `@component()` directive
- Added the `append` method to the request object to allow for appending of data to the request object for use in the middleware functions
- Added the `data` array to serve as a custom data store in the request object