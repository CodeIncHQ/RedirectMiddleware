# Redirect Middleware

The PSR 7.1 library provides PSR-15 middleware to handle redirections. `RedirectMiddleware` is the base middleware using redirect URIs as `go?to=https://www.example.org` while `SecureRedirectMiddleware` uses the `firebase/php-jwt` library to encrypt the redirect URL in a JSON web token.

 
## Installation

This library is available through [Packagist](https://packagist.org/packages/codeinc/redirect-middleware) and can be installed using [Composer](https://getcomposer.org/): 

```bash
composer require codeinc/redirect-middleware
```

## License

The library is published under the MIT license (see [`LICENSE`](LICENSE) file).