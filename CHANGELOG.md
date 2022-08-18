## Change Log


### [18-08-2022] - v2.0
- This is a major breaking update
- Switched to PHP 7.4 for type declaration compatibility
- Added PHPDoc for contributors and users
- Added support for JSON input via file_get_contents()
- Refactored and improved code - SnakeCase is used for uniformity and improved readability
- Made get_route method public for unit testing purposes
- `render` has now replaced the Response class method `use` with support for data and template variables
- `Helper` namespace is now `HTTP` with a new `TemplateEngine` class
- Added middleware support for all routes
- Added the `append` method to the request object to allow for appending of data to the request object for use in the middleware functions
- Added the `data` array to the request object to allow for passing and getting of data to the middleware and callback functions