## Change Log


### [18-08-2022] - v2.0
- This is a major breaking update
- Switched to PHP 7.4 for type declaration compatibility
- Added PHPDoc for contributors and users
- Added "template" engine for views
- Added support for JSON input via file_get_contents()
- Added support for route chaining via the `->route()` method
- Refactored and improved code - SnakeCase is used for uniformity and improved readability
- `render` has now replaced the Response class method `use` with support for data and template variables
- `Helper` namespace is now `HTTP`.
- Added middleware support for all verbs/methods
- Added support for custom 405 err0r page
- Added support for components that have access to the data tree using the `@component()` directive
- Added the `append` method to the request object to allow for appending of data to the request object for use in the middleware functions
- Added the `data` array to inject custom data into the request object