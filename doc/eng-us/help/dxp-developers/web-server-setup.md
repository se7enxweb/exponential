---
title: Web server setup
audience: dxp-developer
category: dxp-development
order: 13
summary: Serve a DXP project with Apache 2.4 or Nginx and PHP-FPM, or the Symfony CLI for development - the document root, the rewrite to public/index.php, the APP_ENV, APP_DEBUG and APP_HTTP_CACHE settings, HTTPS and the reload commands.
keywords: web server, Apache, Nginx, PHP-FPM, Symfony CLI, virtual host, document root, public/index.php, APP_HTTP_CACHE, HTTPS, certbot, reload
type: help
---
# Web server setup

This page is for developers and administrators who put a DXP project behind a web server. The examples are the
skeleton guide's (section 6), shortened; adapt host names and paths.

## Contents

1. [What every setup needs](#what-every-setup-needs)
2. [Apache 2.4](#apache-24)
3. [Nginx](#nginx)
4. [Symfony CLI for development](#symfony-cli-for-development)
5. [HTTPS](#https)
6. [Reloading after a change](#reloading-after-a-change)

## What every setup needs

- The document root is `public/`, never the project root.
- Every request that is not an existing file goes to `public/index.php`.
- `APP_ENV=prod`, `APP_DEBUG=0` on production. `APP_HTTP_CACHE=1` turns on the Symfony HTTP cache; set it to `0`
  when Varnish caches in front ([HTTP cache and Varnish](http-cache-and-varnish.md)).
- No other `.php` file under `public/` is executed.

## Apache 2.4

Enable the modules: `a2enmod rewrite deflate headers expires`. Run Apache with the event MPM and PHP-FPM.

```apache
<VirtualHost *:80>
    ServerName exponential.local
    DocumentRoot /var/www/exponential/public
    DirectoryIndex index.php

    SetEnv APP_ENV "prod"
    SetEnv APP_DEBUG "0"
    SetEnv APP_HTTP_CACHE "1"

    <Directory /var/www/exponential/public>
        AllowOverride None
        Require all granted
        FallbackResource /index.php
    </Directory>
</VirtualHost>
```

## Nginx

```nginx
server {
    listen 80;
    server_name exponential.local;
    root /var/www/exponential/public;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        fastcgi_param APP_ENV prod;
        fastcgi_param APP_DEBUG 0;
        fastcgi_param APP_HTTP_CACHE 1;
        internal;
    }

    location ~ \.php$ {
        return 404;
    }
}
```

Test and reload: `nginx -t && systemctl reload nginx`.

## Symfony CLI for development

```bash
symfony server:start          # HTTPS on https://127.0.0.1:8000
symfony server:start -d       # in the background
symfony server:log
symfony server:stop
```

Do not use it in production.

## HTTPS

Serve production over HTTPS only. The guide uses Certbot as one way to get a certificate
(`certbot --apache -d <your host>`). Set `TRUSTED_PROXIES` when a proxy or Varnish terminates TLS in front of the
application, so Symfony builds correct URLs.

## Reloading after a change

| Changed | Command |
|---|---|
| Apache configuration | `systemctl reload apache2` |
| Nginx configuration | `systemctl reload nginx` |
| PHP-FPM settings or PHP code with OPcache | `systemctl reload php8.3-fpm` (your version's unit) |
| Application code | Clear and warm the Symfony cache ([Day-to-day commands](day-to-day-commands.md)) |

## Related pages

- [Deploying](deploying-dxp.md)
- [File permissions](file-permissions.md)
- [Security hardening (Exponential 6)](../security-hardening.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
