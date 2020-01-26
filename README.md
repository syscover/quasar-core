# Quasar Core App for Laravel

[![Total Downloads](https://poser.pugx.org/quasar/core/downloads)](https://packagist.org/packages/quasar/core)
[![Latest Stable Version](http://img.shields.io/github/release/syscover/quasar-core.svg)](https://packagist.org/packages/quasar/core)

Quasar is a application that generates a control panel where you can create custom solutions.

---

## Installation

**1 - After install Laravel framework, execute on console:**
```
composer require quasar/core
```

**2 - Execute publish command**
```
php artisan vendor:publish --provider="Quasar\Core\CoreServiceProvider"
```

**3 - create link to storage folder**
```
php artisan storage:link
```

## Tips
**1 - Don't forget config environment variables database**
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=33001
DB_DATABASE=quasar
DB_USERNAME=root
DB_PASSWORD=123456
```

**1 - Don't forget to register CORS in your server, the following example is for apache server**
```
Header add Access-Control-Allow-Origin "*"
Header add Access-Control-Allow-Headers "authorization, origin, x-requested-with, content-type"
Header add Access-Control-Expose-Headers "authorization"
Header add Access-Control-Allow-Methods "PUT, GET, POST, DELETE, OPTIONS"
```

**2 - You may need to extend both the PHP memory on your server as well as the upload limit**
```
php_value post_max_size 1000M
php_value upload_max_filesize 1000M
php_value memory_limit 256M
```

