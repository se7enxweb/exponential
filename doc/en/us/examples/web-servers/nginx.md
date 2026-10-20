---
title: nginx
audience: admin, developer
category: examples
order: 40
summary: A complete nginx configuration for Exponential 6 with PHP-FPM, HTTPS, HTTP/2 and security headers: the rules of .htaccess_root translated location by location, checked with nginx -t and a throwaway server.
keywords: nginx, server block, location, try_files, fastcgi_pass, PHP-FPM, HTTPS, HTTP/2, add_header, map, rewrite rules, .htaccess_root, Let's Encrypt, example
type: example
---
# nginx

Exponential ships no nginx configuration of its own; [`nginx.conf`](nginx.conf) in this directory is a translation of
`.htaccess_root` (this release) into nginx directives, rule for rule. It extends the server block of
[chapter 8, section 8.6](../../install/08-serving-the-site.md#86-nginx-with-php-fpm) with HTTPS, HTTP/2, a port 80
redirect and the security headers. Compare it with `.htaccess_root` after every upgrade.

## Install it

1. Copy the file to `/etc/nginx/conf.d/example.com.conf`. It is read inside the `http {}` block: it holds `map` blocks
   as well as `server` blocks. (Debian's `sites-available` files are read there too.)
2. Replace `example.com`, `/var/www/example.com`, the PHP-FPM socket (`/run/php-fpm/www.sock`, five times) and the
   certificate paths.
3. Create the PHP-FPM pool ([PHP-FPM pool](php-fpm.md)); nginx's user must be able to open its socket.
4. Get the certificate, for example `certbot certonly --webroot -w /var/www/example.com -d example.com -d
   www.example.com`; the port 80 server serves `/.well-known/acme-challenge/` for it.
5. `nginx -t`, then `systemctl reload nginx`.

`http2 on;` needs nginx 1.25.1 or later; on older versions write `listen 443 ssl http2;` instead.

## The file

```nginx
# Exponential 6: nginx with PHP-FPM, HTTPS and HTTP/2
#
# Free documentation: GFDL 1.3 or later, see doc/en/us/README-LICENSE.md; rules derived from .htaccess_root (GPL v2 or later).
#
# Derived, not shipped by the kernel: a translation of .htaccess_root (this
# release) into nginx directives, rule for rule. Compare it with
# .htaccess_root after every upgrade. Explained in nginx.md next to this file.
#
# Install: copy to /etc/nginx/conf.d/example.com.conf (it is read inside the
# http {} block: it holds "map" and "server" blocks), replace example.com,
# /var/www/example.com, the PHP-FPM socket and the certificate paths, then
# "nginx -t" and "systemctl reload nginx".

# Security headers on what nginx serves itself, with the values of the
# kernel's site.ini [HTTPHeaderSettings] SecurityHeaders[]. The kernel sends
# them on its pages; each map yields an empty value (no header) when PHP
# already sent it, so a page never gets the header twice.
map $upstream_http_x_content_type_options $exp_xcto {
    ""      "nosniff";
    default "";
}
map $upstream_http_x_frame_options $exp_xfo {
    ""      "SAMEORIGIN";
    default "";
}
map $upstream_http_referrer_policy $exp_rp {
    ""      "strict-origin-when-cross-origin";
    default "";
}
map $upstream_http_permissions_policy $exp_pp {
    ""      "camera=(), microphone=(), payment=(), usb=()";
    default "";
}

# Plain HTTP: the ACME challenge files, and a redirect for everything else
server {
    listen 80;
    listen [::]:80;
    server_name example.com www.example.com;
    root /var/www/example.com;

    location ^~ /.well-known/acme-challenge/ {
        try_files $uri =404;
    }
    location / {
        return 301 https://$host$request_uri;
    }
}

server {
    listen 443 ssl;
    listen [::]:443 ssl;
    http2 on;
    server_name example.com www.example.com;

    ssl_certificate     /etc/ssl/example.com/fullchain.pem;
    ssl_certificate_key /etc/ssl/example.com/privkey.pem;

    # The installation root, where index.php is
    root /var/www/example.com;
    index index.php;

    # Uploads: nginx's default limit is 1 MB
    client_max_body_size 64m;

    add_header X-Content-Type-Options $exp_xcto always;
    add_header X-Frame-Options $exp_xfo always;
    add_header Referrer-Policy $exp_rp always;
    add_header Permissions-Policy $exp_pp always;
    # HSTS once every name is served over HTTPS for good; start short. Here or
    # in site.ini SecurityHeaders[Strict-Transport-Security], not both.
    #add_header Strict-Transport-Security "max-age=300" always;

    # 1. No dot file or dot directory, anywhere; .well-known stays reachable
    location ~ (^|/)\.(?!well-known(/|$)) {
        return 404;
    }

    # 2. No script below the root runs because its path was asked for
    #    (.htaccess_root answers 404 only when the file exists; this answers 404 always)
    location ~ ^/.+/[^/]+\.(php[0-9]?|phtml|phar)$ {
        return 404;
    }

    # 3. Front controllers
    location ^~ /api/ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index_rest.php;
        fastcgi_param SCRIPT_NAME /index_rest.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }
    location = /index_rest.php {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index_rest.php;
        fastcgi_param SCRIPT_NAME /index_rest.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }
    location ~ ^/([^/]+/)?content/treemenu {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index_treemenu.php;
        fastcgi_param SCRIPT_NAME /index_treemenu.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }

    # 4. Files served as they are (the "- [L]" rules of .htaccess_root);
    #    a listed path that does not exist is a 404, as under Apache.
    #    Uploaded SVG images first: they run nothing and load nothing when opened
    #    directly (.htaccess_root's FilesMatch for .svg; here for the storage,
    #    where the uploads are). A location with add_header of its own does not
    #    inherit the server's, so the other headers are repeated.
    location ~* ^/var/([^/]+/)?storage/(images(-versioned)?/.+\.svgz?|original/image/.+\.svg)$ {
        add_header Content-Security-Policy "sandbox; default-src 'none'; style-src 'unsafe-inline'" always;
        add_header X-Content-Type-Options "nosniff" always;
        add_header X-Frame-Options "SAMEORIGIN" always;
        add_header Referrer-Policy "strict-origin-when-cross-origin" always;
        try_files $uri =404;
    }
    location ~ ^/var/([^/]+/)?storage/images(-versioned)?/                                    { try_files $uri =404; }
    location ~ ^/var/([^/]+/)?storage/original/image/.+\.(png|jpe?g|gif|webp|svg)$             { try_files $uri =404; }
    location ~ ^/var/([^/]+/)?storage/pdf/[A-Za-z0-9][A-Za-z0-9._-]*\.pdf$                      { try_files $uri =404; }
    location ~ ^/var/([^/]+/)?cache/(texttoimage|public)/                                       { try_files $uri =404; }
    # With site.ini [FileSettings] CacheVarDir=var_cache (multi-site hosting):
    #location ~ ^/var_cache/([^/]+/)?cache/(texttoimage|public)/                               { try_files $uri =404; }
    location ~ ^/design/[^/]+/(stylesheets|images|javascript|fonts)/                            { try_files $uri =404; }
    location ~ ^/share/icons/                                                                   { try_files $uri =404; }
    location ~ ^/extension/[^/]+/design/[^/]+/(stylesheets|flash|images|lib|javascripts?|fonts|vendor|media)/ { try_files $uri =404; }
    location ~ ^/packages/styles/.+/(stylesheets|images|javascript)/[^/]+/                      { try_files $uri =404; }
    location ~ ^/packages/styles/.+/thumbnail/                                                  { try_files $uri =404; }
    location ~ ^/var/storage/packages/.+\.(png|jpe?g|gif|webp)$                                 { try_files $uri =404; }
    location = /favicon.ico  { try_files $uri =404; }
    location = /robots.txt   { try_files $uri =404; }
    location = /index.js     { try_files $uri =404; }
    location = /sw.js        { try_files $uri =404; }
    location = /w3c/p3p.xml  { try_files $uri =404; }

    # 5. Everything else goes to index.php, with the original REQUEST_URI:
    #    settings/, var/, vendor/, kernel/ and composer.json included, which
    #    index.php answers with a page or a 404, never with the file
    location / {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index.php;
        fastcgi_param SCRIPT_NAME /index.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }
}
```

## How it maps, and why it is ordered this way

- nginx chooses a location by its own precedence, not by file order: exact matches (`=`) first, then the longest `^~`
  prefix, then **regular expressions in the order they appear**, then the longest plain prefix. The dot-path and script
  rules are therefore the first regular expressions, as they are the first rules of `.htaccess_root`; `location /`
  catches the rest, like the final `RewriteRule .* index.php`.
- The front controllers are named in `SCRIPT_FILENAME`; the request is not rewritten, so `REQUEST_URI` (from
  `fastcgi_params`) still carries the address the visitor asked for, as with Apache.
- The static locations have no `fastcgi_pass`, so no PHP runs there, and the script rule before them makes sure a
  `.php` below an asset directory is not sent as text either. The script rule answers 404 for any such path, existing
  or not, which is a little stricter than `.htaccess_root`.
- `settings/`, `var/log/`, `vendor/`, `kernel/`, `composer.json` and every other path not on the list reach
  `location /` and are answered by `index.php`, never sent.
- **Headers.** `add_header` is inherited only by locations without an `add_header` of their own, and it cannot test
  whether PHP already sent a header. The `map` blocks solve the second: each yields an empty value, which nginx does
  not send, when the response from PHP already has the header. The SVG location repeats the headers because it has its
  own `add_header`.
- `fastcgi_params` of the nginx packages passes `HTTPS` on a TLS connection, which is what Exponential checks first.

## Behind a proxy

When TLS ends at a load balancer in front of nginx, keep one `server` on the port the balancer talks to (with the
locations of the 443 server). nginx passes every request header to PHP (`X-Forwarded-Proto` arrives as
`HTTP_X_FORWARDED_PROTO`); the kernel believes it only from an address listed in
`site.ini [HTTPHeaderSettings] TrustedProxies[]`
([chapter 8, section 8.8](../../install/08-serving-the-site.md#88-behind-a-reverse-proxy)).

## Check it

```bash
nginx -t
for p in /.git/config /.env /kernel/classes/ezini.php /extension/ezjscore/ezinfo.php; do
  printf '%-34s %s\n' "$p" "$(curl -s -o /dev/null -w '%{http_code}' https://example.com$p)"     # 404 each
done
curl -s https://example.com/settings/site.ini | grep -c '^\[DatabaseSettings\]'   # 0: a page, not the file
curl -sI https://example.com/design/admin4/stylesheets/components.css | grep -i -E '^(HTTP|x-content-type)'
```

How this example was checked: `nginx -t` with nginx 1.28, then a throwaway nginx with this file (ports, paths, socket
and a self-signed certificate substituted) answered every request of the [test list](README.md#which-one) as expected
over HTTPS, including the security headers and the SVG sandbox, redirected plain HTTP and served an ACME challenge
file on port 80.

## Related pages

- [Chapter 8, section 8.6: nginx with PHP-FPM](../../install/08-serving-the-site.md#86-nginx-with-php-fpm).
- [Chapter 13, section 13.2: What the web server must never hand out](../../install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out).
- [PHP-FPM pool](php-fpm.md), [Apache .htaccess](apache-htaccess.md) (the rules explained one by one).
- [Web server examples](README.md) and [all examples](../README.md).
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
