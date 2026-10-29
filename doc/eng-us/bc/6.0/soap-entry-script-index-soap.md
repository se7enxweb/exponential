# The SOAP entry script is now index_soap.php

Read this page if an installation answers SOAP requests: `settings/soap.ini [GeneralSettings] EnableSOAP=true`, the
`syndication` extension exporting content to other installations, or a middleware product that calls functions an
extension registered. If SOAP is off (the default), nothing changes for you; the rules on this page are still the
ones to use the day you switch it on.

Changed on 29 October 2026, in the 6.0.15 line.

## Contents

1. [What changed](#what-changed)
2. [Why](#why)
3. [What keeps working for now](#what-keeps-working-for-now)
4. [What you must change](#what-you-must-change)
5. [Apache: .htaccess and the HTTPS virtual host](#apache-htaccess-and-the-https-virtual-host)
6. [Plesk](#plesk)
7. [nginx](#nginx)
8. [Other servers and Exponential Velocity](#other-servers-and-exponential-velocity)
9. [The SOAP clients](#the-soap-clients)
10. [Test it](#test-it)
11. [For developers](#for-developers)

## What changed

| Before (6.0.14 and older) | Now (6.0.15) |
|---|---|
| `soap.php` in the root of the installation was the SOAP server | `index_soap.php` is the SOAP server |
| | `soap.php` is a shim: it writes a notice at most once a day and includes `index_soap.php`, nothing else |
| `https://www.example.com/soap.php` | `https://www.example.com/index_soap.php` |

The server itself did not change: the same settings (`soap.ini`), the same extensions
(`[ExtensionSettings] SOAPExtensions[]`, `extension/<name>/soap/initialize.php`), the same sign-in with HTTP basic
credentials, the same answers. Only its file name changed.

A second sign-in step (two-factor authentication) still never applies to SOAP: an account that needs one signs in
over SOAP with an app password, as before. See
[the second step on the other sign-in paths](../../features/6.0/second-factor-on-other-sign-in-paths.md).

## Why

- **One name pattern for every front controller.** The scripts the web server may run are `index.php`,
  `index_rest.php`, `index_treemenu.php` and `index_cluster.php`. `soap.php` was the odd one out, so every set of
  rewrite rules, every nginx `location`, every list of allowed scripts needed a special case for it, and many
  installations that switched SOAP on simply forgot it: their SOAP requests ended at `index.php`, which answered with
  a page or a 404.
- **Hardened rules stay simple.** The shipped rules run no `.php` file because its path was asked for, except the front
  controllers in the root. With the SOAP server named like them, a rule such as `^index(_[a-z]+)?\.php$` describes all
  of them, and a scanner or an audit can tell a front controller from a stray script by its name.
- **The WebDAV server follows the same pattern** in the same release (`webdav.php` becomes `index_webdav.php`); see
  its own page in this directory.

## What keeps working for now

- **`soap.php` still answers.** It only includes `index_soap.php`, so a client or a rule that still names it gets the
  same answer as before. The URIs and the siteaccess are read relative to the name the request used.
- **Once a day** a request through `soap.php` writes one notice to `var/log/warning.log`:

  ```text
  [ Oct 29 2026 09:12:03 ] [192.0.2.10] soap.php:
  A request came through soap.php, the deprecated name of the SOAP entry script. It still answers, but change the
  web server rules and the SOAP clients to index_soap.php (Exponential 6.0.15). Requested URI: /soap.php
  ```

  The day is kept as the modification time of `var/log/soap-php-deprecated.stamp`; delete that file to get the notice
  again on the next request. If `var/log/` does not exist or cannot be written, the shim writes nothing and still
  answers.
- **The shim will be removed in a later major version.** Do not build anything new on `soap.php`.

## What you must change

1. **Update the code** to 6.0.15 (or later) as usual. `index_soap.php` and the shim arrive together.
2. **Let `index_soap.php` through in the web server**, in every place that lets `soap.php` through today: the
   `.htaccess` of the installation, the HTTPS virtual host (`*:443`) and, where both exist, the plain HTTP one
   (`*:80`), Plesk's additional directives, the nginx server block. The sections below give the lines.
3. **Point every SOAP client at `index_soap.php`**: the import settings of the `syndication` extension on every
   importing installation, middleware, scripts, monitoring checks. See [the SOAP clients](#the-soap-clients).
4. **Reload the web server** and [test](#test-it) both the new address and, until every client has moved, the old one.
5. **Watch `var/log/warning.log`** (and the web server's access log) for `soap.php`. When nothing has called it for a
   while, you may refuse it at the web server (`RewriteRule ^soap\.php$ - [F,L]`, nginx `location = /soap.php
   { return 403; }`) so a forgotten client fails loudly instead of using a name that will disappear.

Where the web server lets every existing file through (a `RewriteCond %{REQUEST_FILENAME} !-f` before the rule that
sends everything to `index.php`), `index_soap.php` is reached without a rule of its own. Test it all the same.

## Apache: .htaccess and the HTTPS virtual host

The shipped `.htaccess_root` (copied to `.htaccess`) and `.htaccess_root_static` carry the line, commented out,
right after the REST API's rules. Uncomment it when SOAP is on:

```apache
RewriteRule ^api/ index_rest.php [L]
RewriteRule ^index_rest\.php - [L]
# The SOAP server (settings/soap.ini EnableSOAP=true) is index_soap.php since
# Exponential 6.0.15; soap.php, its old name, only forwards to it. Uncomment
# when SOAP is switched on, and let the SOAP clients call index_soap.php:
RewriteRule ^index_soap\.php - [L]
```

If your `.htaccess` has a line for the old name, such as `RewriteRule ^soap\.php - [L]`, change it to the line above
(and keep the old one only while clients still call `soap.php`).

In a virtual host the rules are in server context, so the patterns start with `/`. In the `<VirtualHost *:443>` block
(and in `<VirtualHost *:80>` if SOAP is also served over plain HTTP, which it should not be):

```apache
<VirtualHost *:443>
    # ...
    RewriteEngine On
    RewriteRule ^/api/ /index_rest.php [L]
    RewriteRule ^/index_rest\.php - [L]
    RewriteRule ^/index_soap\.php - [L]
    # while old clients still call soap.php:
    RewriteRule ^/soap\.php - [L]
    # ...
    RewriteRule . /index.php [L]
</VirtualHost>
```

A virtual host that hands PHP to PHP-FPM only for a list of scripts (`<FilesMatch "^(index|index_rest|index_treemenu)\.php$">`
with `SetHandler "proxy:unix:...|fcgi://localhost"`) needs `index_soap` in that list too, or Apache sends the source
of `index_soap.php` as a file:

```apache
<FilesMatch "^(index|index_rest|index_treemenu|index_soap)\.php$">
    SetHandler "proxy:unix:/run/php-fpm/www.sock|fcgi://localhost"
</FilesMatch>
```

A restriction to the addresses of the importing installations, which the
[syndication page](../../help/integrators/syndication-between-installations.md) recommends, moves with the name:

```apache
<Location "/index_soap.php">
    Require ip 192.0.2.10 198.51.100.0/24
</Location>
```

Check the configuration and reload: `apachectl configtest && systemctl reload httpd` (`apache2` on Debian).

## Plesk

Plesk keeps the rules of a domain in **Websites & Domains > (domain) > Apache & nginx Settings > Additional
directives for HTTPS** (and **for HTTP**). It writes them into the domain's `vhost_ssl.conf` (`vhost.conf` for HTTP)
below `/var/www/vhosts/system/<domain>/conf/`; edit them in the panel, or in those files followed by
`plesk repair web <domain> -y`, not in the generated `httpd.conf`.

- In **Additional directives for HTTPS**, wherever `soap.php` appears (a `RewriteRule`, a `<Location>`, a
  `<Files>` or `<FilesMatch>` that names the scripts for PHP-FPM), add or change the same line for `index_soap.php`,
  as in the virtual host above.
- With **nginx proxy mode** on, also add the nginx part in **Additional nginx directives** if it names `soap.php`
  (see [nginx](#nginx)); with proxy mode the requests reach Apache unless nginx answers them itself.
- Plesk's PHP handler for the domain (PHP-FPM served by Apache, or by nginx) runs every `.php` file the rules let
  through; nothing to change there.

## nginx

nginx has no `.htaccess`. A server block in the style of the shipped example
([nginx](../../examples/web-servers/nginx.md), `doc/eng-us/examples/web-servers/nginx.conf`) lists each front
controller in its own `location`; the example carries the SOAP one commented out. With SOAP on:

```nginx
location = /index_soap.php {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/index_soap.php;
    fastcgi_param SCRIPT_NAME /index_soap.php;
    fastcgi_pass unix:/run/php-fpm/www.sock;
}
```

Change an existing `location = /soap.php` (or a `location ~ ^/(index|index_rest|soap)\.php$`) the same way: the
`location`, `SCRIPT_FILENAME` and `SCRIPT_NAME` all name `index_soap.php`. A `SCRIPT_FILENAME` that still points at
`soap.php` keeps working through the shim, but writes the daily notice. Keep a `location = /soap.php` with the same
three lines (all naming `soap.php`) only while old clients still call it, then answer it with `return 403;`.
An `allow`/`deny` list for the importing installations goes into the `index_soap.php` location.

Check and reload: `nginx -t && systemctl reload nginx`.

## Other servers and Exponential Velocity

- **LiteSpeed and OpenLiteSpeed** read the Apache rules: add `RewriteRule ^/?index_soap\.php - [L]` next to the
  `index_rest.php` rule (the [OpenLiteSpeed example](../../examples/web-servers/openlitespeed-vhost.conf) has it,
  commented out).
- **Caddy and FrankenPHP**: add `/index_soap.php` to the paths the `@front` matcher leaves alone
  (`@front not path /index_rest.php /index_treemenu.php /index_soap.php`).
- **IIS**: a rule that matches `^index_soap\.php` with the action **None**, next to `exp-rest-direct`.
- **Exponential Velocity** runs only `index.php`, `index_rest.php` and `index_treemenu.php` (the server's
  `webserver.scripts`); it never ran `soap.php` either. Serve SOAP through the Apache or nginx front end of the
  installation, which hands it to PHP-FPM, and keep the SOAP clients on that address.

## The SOAP clients

Every system that calls the SOAP server names its address; change it from `.../soap.php` to `.../index_soap.php`:

- **The `syndication` extension**, on every installation that imports: **Syndication > Imports > (import) > Edit**,
  the field for the server address, for example `https://export.example.com/index_soap.php`. From `syndication`
  1.3.14 on, the wizard gives `index_soap.php` as the example, and the dashboard and a feed's page of the exporting
  installation show `index_soap.php` when its kernel has it (`soap.php` on an older one). An exporting installation
  that still runs 6.0.14 or older is called at `soap.php`.
- **Middleware and scripts**: the endpoint address in their configuration.
- **Monitoring**: a check that posts to the SOAP server.

Until all of them have moved, `soap.php` keeps answering; the daily notice tells you that something still calls it.

## Test it

Run these from a machine that is allowed to reach the SOAP server, against the HTTPS name of the site. Replace
`www.example.com` with that name.

1. **A GET answers, not 404.** The Exponential SOAP server has no WSDL; a GET (with `?wsdl` or without) gets a short
   message, which proves that the request reached the SOAP server and not `index.php`:

   ```bash
   curl -sS -i 'https://www.example.com/index_soap.php?wsdl'
   ```

   Expect `HTTP/1.1 200` (or `HTTP/2 200`) and the body `Error: this web page does only understand POST methods`.
   With `EnableSOAP=false` the answer is `200` with an empty body. A `404`, the site's front page or its "page not
   found" HTML means the rewrite rule is missing and the request went to `index.php`. The PHP source in the body means
   the script was not handed to PHP (see the `FilesMatch` above).

2. **A POST of a minimal SOAP envelope answers with a SOAP fault**, here for a function that does not exist, so it
   changes nothing:

   ```bash
   curl -sS -i -X POST 'https://www.example.com/index_soap.php' \
        -H 'Content-Type: text/xml; charset=utf-8' -H 'SOAPAction: ""' \
        --data-binary '<?xml version="1.0" encoding="UTF-8"?><SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"><SOAP-ENV:Body><exp:ping xmlns:exp="http://example.com/exp"/></SOAP-ENV:Body></SOAP-ENV:Envelope>'
   ```

   Expect `200`, `Content-Type: text/xml` and a body with `<SOAP-ENV:Fault>` and `Method not found`. That is the right
   answer: the server parsed the envelope and looked the function up. With credentials (`-u login:password`) of an
   account that needs a second sign-in step and no app password, the answer is `401` with
   `WWW-Authenticate: Basic realm="Exponential SOAP"` and a fault that names the reason.

3. **The old name still answers** while you need it: repeat 1 and 2 with `soap.php`. The answers are the same, and
   `var/log/warning.log` gets one `soap.php:` notice for the day.

4. **Nothing calls the old name any more.** After the clients have moved:

   ```bash
   grep -c 'soap\.php' /var/log/httpd/ssl_access_log      # Apache; nginx: /var/log/nginx/access.log
   grep -A1 'soap\.php:$' var/log/warning.log | tail -4
   ```

   On Plesk the access log of a domain is `/var/www/vhosts/system/<domain>/logs/access_ssl_log`. Requests for
   `/soap.php` after the change are clients you have not moved yet; the line also shows their address. The requests
   for `/index_soap.php` are the ones that moved.

5. **A real call.** On an importing installation, open the import of the `syndication` extension and fetch the list of
   exported content (or run the import's cronjob): it should list the feeds of the exporting side as before.

## For developers

- `index_soap.php` is the script; `soap.php` holds no logic. Change `index_soap.php`.
- `eZSys::init()` gets the name of the script the request named (`index_soap.php`, or `soap.php` through the shim), so
  the URIs and the siteaccess match are read relative to it.
- `expUserSessionTracker::detectMethod()` counts a sign-in through either name as `soap`.
- Tests: `tests/tests/kernel/classes/expSoapEntryScriptTest.php` (no database) runs the shim in a PHP process of its
  own beside a stand-in `index_soap.php`: it forwards, writes one notice a day, and still answers without `var/log/`.

## Related pages

- [WebDAV and SOAP](../../help/integrators/webdav-and-soap.md)
- [Syndication between installations](../../help/integrators/syndication-between-installations.md)
- [Apache .htaccess](../../examples/web-servers/apache-htaccess.md) and [nginx](../../examples/web-servers/nginx.md)
- [Exponential 6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#2026-10-29-the-soap-entry-script-is-index_soapphp)
