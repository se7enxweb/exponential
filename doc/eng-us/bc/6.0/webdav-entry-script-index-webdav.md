# The WebDAV entry script is now index_webdav.php

From 6.0.15 on, the WebDAV server of Exponential is the script **`index_webdav.php`** at the installation root. Until
6.0.14 it was `webdav.php`. `webdav.php` is still shipped, but only as a forwarding shim: it includes
`index_webdav.php` and writes a warning once a day. **It will be removed in a later major version.**

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../examples/web-servers/nginx.md), [LiteSpeed](../../examples/web-servers/litespeed.md),
> [Caddy](../../examples/web-servers/caddy.md), [IIS](../../examples/web-servers/iis.md), [Velocity](../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../../help/hosting/panels/plesk.md), [cPanel](../../help/hosting/panels/cpanel.md), [DirectAdmin](../../help/hosting/panels/directadmin.md), [ISPConfig](../../help/hosting/panels/ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../guides/litespeed-page-cache.md).

Read this page if WebDAV is switched on anywhere (`settings/webdav.ini [GeneralSettings] EnableWebDAV=true`), or if a
virtual host, a rewrite rule, a firewall rule, a monitoring check or a client bookmark names `webdav.php`. If WebDAV is
off everywhere and nothing names the script, nothing changes for you.

## Contents

1. [What changed](#what-changed)
2. [Why](#why)
3. [Compatibility: webdav.php forwards until a later major version](#compatibility-webdavphp-forwards-until-a-later-major-version)
4. [What you must change](#what-you-must-change)
5. [Apache: .htaccess](#apache-htaccess)
6. [Apache: the virtual hosts, HTTP and HTTPS](#apache-the-virtual-hosts-http-and-https)
7. [Plesk: Additional Apache directives](#plesk-additional-apache-directives)
8. [nginx](#nginx)
9. [Microsoft IIS: web.config](#microsoft-iis-webconfig)
10. [Velocity and FrankenPHP](#velocity-and-frankenphp)
11. [WebDAV clients](#webdav-clients)
12. [Test it](#test-it)
13. [Confirm that the old name is no longer used](#confirm-that-the-old-name-is-no-longer-used)
14. [Sign-in does not change](#sign-in-does-not-change)
15. [For developers](#for-developers)

## What changed

| | Until 6.0.14 | From 6.0.15 |
|---|---|---|
| The WebDAV server | `webdav.php` | `index_webdav.php` |
| `webdav.php` | the server | a shim: `require __DIR__ . '/index_webdav.php';`, one warning a day in `warning.log` |
| URL on the main host | `https://www.example.com/webdav.php/<siteaccess>/` | `https://www.example.com/index_webdav.php/<siteaccess>/` |
| URL on a host of its own | `https://webdav.example.com/<siteaccess>/` | unchanged; only the rewrite rule behind it changes |
| `.htaccess_root`, `.htaccess_root_static` | no WebDAV rule | a commented rule `#RewriteRule ^index_webdav\.php - [L]` to switch on |

Everything else stays as it was: `settings/webdav.ini`, the content backend in `kernel/private/classes/webdav/`, the
permissions, the log `var/log/webdav.log` (with `[GeneralSettings] Logging=enabled`).

## Why

Every other entry script at the root is named `index_<what>.php`: `index.php`, `index_rest.php`,
`index_treemenu.php`, `index_cluster.php`. Only WebDAV and SOAP broke the pattern. One pattern means one rule for the
web server allowlists that decide which PHP files at the root may run at all: `index(_[a-z]+)?\.php` covers every
web entry point, and a hardening rule written for the front controllers no longer forgets WebDAV, or lets it through
by accident. (Exponential's ancestor named the script `index_webdav.php` too, before version 3.2 renamed it.)

## Compatibility: webdav.php forwards until a later major version

- `webdav.php` contains nothing but a constant naming itself and `require __DIR__ . '/index_webdav.php';`. A request to
  `webdav.php` is answered exactly as before, with the same paths (`/webdav.php/<siteaccess>/...`), so existing
  virtual hosts, rewrite rules and mounted drives keep working after the upgrade.
- The first request through `webdav.php` on a day writes one line to `warning.log` (the log directory of the site,
  `var/log/` or `var/<site>/log/`):

  ```text
  WebDAV request through webdav.php, the old name of the WebDAV entry script. webdav.php only forwards to
  index_webdav.php and will be removed in a later major version: change the virtual host and rewrite rules ...
  ```

  The day is kept in `webdav-entry-script-deprecated.stamp` next to the log, so a busy WebDAV share does not fill the
  log. The line is written only while WebDAV is switched on.
- **`webdav.php` will be removed in a later major version.** After that, a rule that still names it sends WebDAV
  requests to a file that does not exist. Change the rules now, while the shim still answers.

## What you must change

Go through this list on **every** server and in **every** configuration layer. The rewrite rules are the part most
often forgotten, and of those the HTTPS virtual host: many setups have a `<VirtualHost *:80>` that only redirects and a
`<VirtualHost *:443>` that does the real work, and only the first one gets edited.

1. `.htaccess` in the installation root (if Apache reads it).
2. The Apache virtual host for HTTP (`*:80`) **and** the one for HTTPS (`*:443`), including a separate WebDAV host
   such as `webdav.example.com`.
3. Plesk: **Additional Apache directives** for HTTP **and** for HTTPS (`vhost.conf` and `vhost_ssl.conf`), and any
   file they include (a shared docroot protection file with an allowlist of root scripts, for example).
4. nginx: the `location` that matches the script, and its `fastcgi_param SCRIPT_FILENAME` and `SCRIPT_NAME`.
5. IIS: the rewrite rules in `web.config`.
6. Anything else that names the file: a firewall or WAF rule, a load balancer health check, a monitoring check, a
   backup exclusion, an allowlist in a hardening include.
7. WebDAV clients that use the main host's URL (`/webdav.php/...`).

Then [test](#test-it) and [confirm that the old name is no longer used](#confirm-that-the-old-name-is-no-longer-used).

## Apache: .htaccess

`.htaccess_root` (the file you copy to `.htaccess`) sends every request that is not a listed asset to `index.php`. It
never had a rule for WebDAV, so WebDAV on the main host needed a rule of your own. From 6.0.15 on the file carries it,
commented out, right after the tree menu rule:

```apache
# Before (a rule you added yourself)
RewriteRule ^webdav\.php - [L]

# After (uncomment the shipped line in .htaccess)
RewriteRule ^index_webdav\.php - [L]
```

The rule must stand **before** the last rule (`RewriteRule .* index.php`), as the `index_rest.php` rule does. Merge
`.htaccess_root` into `.htaccess` after the upgrade as usual, and keep a WebDAV rule only if WebDAV is used on this
host.

## Apache: the virtual hosts, HTTP and HTTPS

In server context (a `<VirtualHost>` without `<Directory>`), the pattern of a `RewriteRule` starts with a slash.

**WebDAV on the main host**, next to the site. Inside the `<VirtualHost *:443>` block, before the rule that sends
everything to `index.php`:

```apache
# Before
RewriteRule ^/webdav\.php - [L]

# After
RewriteRule ^/index_webdav\.php - [L]
```

Inside a `<Directory /var/www/example.com>` block (as in the shipped
[Apache virtual host example](../../examples/web-servers/apache-virtual-host.md)), the pattern has no leading
slash: `RewriteRule ^index_webdav\.php - [L]`.

**WebDAV on a host of its own** (recommended: the WebDAV host then needs no other rule and can be limited by address).
Every request goes to the script; the client's path (`/<siteaccess>/...`) is kept in `REQUEST_URI`:

```apache
# Before
<VirtualHost *:443>
    ServerName webdav.example.com
    DocumentRoot /var/www/example.com
    SSLEngine on
    SSLCertificateFile    /etc/ssl/example.com/fullchain.pem
    SSLCertificateKeyFile /etc/ssl/example.com/privkey.pem
    RewriteEngine On
    RewriteRule . /webdav.php [L]
    <FilesMatch "\.php$">
        SetHandler "proxy:unix:/run/php-fpm/www.sock|fcgi://localhost/"
    </FilesMatch>
</VirtualHost>

# After: only the rewrite target changes
<VirtualHost *:443>
    ServerName webdav.example.com
    DocumentRoot /var/www/example.com
    SSLEngine on
    SSLCertificateFile    /etc/ssl/example.com/fullchain.pem
    SSLCertificateKeyFile /etc/ssl/example.com/privkey.pem
    RewriteEngine On
    RewriteRule . /index_webdav.php [L]
    <FilesMatch "\.php$">
        SetHandler "proxy:unix:/run/php-fpm/www.sock|fcgi://localhost/"
    </FilesMatch>
</VirtualHost>
```

If the same host also answers on port 80 with WebDAV (only on a closed network: basic authentication sends the
password readable), change the `<VirtualHost *:80>` block the same way. If port 80 only redirects to HTTPS, there is
nothing to change in it, and **the `*:443` block is the one that matters.**

If an allowlist decides which files at the root may run (a rule such as
`RewriteCond %{REQUEST_URI} !^/(index(_rest|_treemenu|_cluster)?\.php|soap\.php|webdav\.php)$`), add
`index_webdav.php` to it: `index(_rest|_treemenu|_cluster|_webdav)?\.php`. Keep `webdav\.php` in the list until every
client and rule has moved, then take it out.

Check with `apachectl configtest` (`httpd -t`), reload with `systemctl reload apache2` (`httpd` on Red Hat style
systems).

## Plesk: Additional Apache directives

Plesk writes the field **Additional directives for HTTP** to `vhost.conf` and **Additional directives for HTTPS** to
`vhost_ssl.conf` (Websites & Domains > the domain > Apache & nginx Settings). The two fields are separate: a rule in
the HTTP field does nothing for `https://` requests. Change the WebDAV rule in **both** fields, in the same place as on
a plain Apache (before the rule that sends everything to `index.php`):

```apache
# Before
RewriteRule ^webdav\.php - [L]

# After
RewriteRule ^index_webdav\.php - [L]
```

(These fields are applied inside the domain's `<Directory>`, so the pattern has no leading slash.) If the fields
`Include` a shared file, change the file too. When Plesk runs nginx in front of Apache as a proxy, nothing needs to
change on the nginx side unless **Additional nginx directives** name `webdav.php`.

## nginx

The script is named twice: in the `location` that matches it and in the parameters for PHP-FPM. Change both.

```nginx
# Before
location ^~ /webdav.php {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/webdav.php;
    fastcgi_param SCRIPT_NAME /webdav.php;
    fastcgi_pass unix:/run/php-fpm/www.sock;
}

# After
location ^~ /index_webdav.php {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/index_webdav.php;
    fastcgi_param SCRIPT_NAME /index_webdav.php;
    fastcgi_pass unix:/run/php-fpm/www.sock;
}
```

For a WebDAV host of its own (`server_name webdav.example.com;`), every request goes to the script:

```nginx
location / {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/index_webdav.php;
    fastcgi_param SCRIPT_NAME /index_webdav.php;
    fastcgi_pass unix:/run/php-fpm/www.sock;
}
```

`SCRIPT_FILENAME` must name the file that really runs: Exponential takes the installation root from it. A
`SCRIPT_FILENAME` that still says `webdav.php` keeps working through the shim (and writes the daily warning); one that
names `index_webdav.php` while `SCRIPT_NAME` says `webdav.php` is inconsistent: change both together. Check with
`nginx -t`, reload with `systemctl reload nginx`.

## Microsoft IIS: web.config

The shipped [IIS example](../../examples/web-servers/iis.md) has no WebDAV rule. If you added one, change its
pattern and its target:

```xml
<!-- Before -->
<rule name="exp-webdav" stopProcessing="true">
  <match url="^webdav\.php" />
  <action type="None" />
</rule>

<!-- After -->
<rule name="exp-webdav" stopProcessing="true">
  <match url="^index_webdav\.php" />
  <action type="None" />
</rule>
```

On a WebDAV site of its own the rule sends everything to the script: `<match url=".*" />` with
`<action type="Rewrite" url="index_webdav.php" />`. IIS's own WebDAV module must stay off for that site, or it answers
the WebDAV methods before PHP sees them.

## Velocity and FrankenPHP

Velocity's Exponential preset runs only `index.php`, `index_rest.php` and `index_treemenu.php`; a request for any
other `.php` file is answered by `index.php`. That was true for `webdav.php` and is true for `index_webdav.php`: **neither
Velocity nor the FrankenPHP configuration that `exp:velocity` writes serves WebDAV.** Serve WebDAV through Apache or
nginx with PHP-FPM, preferably on a host of its own such as `webdav.example.com`, next to Velocity serving the site.
Nothing in `settings/velocity.ini` names the WebDAV script, so there is nothing to change there.

## WebDAV clients

- A client mounted at a host of its own (`https://webdav.example.com/<siteaccess>/`) keeps its address.
- A client mounted at the main host (`https://www.example.com/webdav.php/<siteaccess>/`) keeps working through the
  shim. Mount it again at `https://www.example.com/index_webdav.php/<siteaccess>/` before `webdav.php` is removed.
  The addresses in the server's answers (`href`) follow the name the client used.

## Test it

Run these from a machine outside the server, against the address the clients use. Replace `www.example.com`, the
siteaccess (`site` below) and the user. On a host of its own, leave out `/index_webdav.php`.

**1. OPTIONS: the server answers as WebDAV.**

```bash
curl -i -X OPTIONS https://www.example.com/index_webdav.php/
```

Expected: `HTTP/1.1 200 OK` with the headers `DAV: 1` and
`Allow: GET, HEAD, PROPFIND, PROPPATCH, OPTIONS, DELETE, COPY, MOVE, MKCOL, PUT, LOCK, UNLOCK`. An HTML page or a
missing `DAV` header means the request reached `index.php`: the rewrite rule is missing or stands after the catch-all.
`404` with the text `WebDAV functionality is disabled!` means the rule works and `EnableWebDAV` is not `true` for this
installation.

**2. PROPFIND on the root: the list of siteaccesses, without signing in.**

```bash
curl -i -X PROPFIND -H 'Depth: 1' https://www.example.com/index_webdav.php/
```

Expected: `HTTP/1.1 207 Multi-Status` and an XML `<D:multistatus>` whose `<D:href>` values start with
`https://www.example.com/index_webdav.php/`.

**3. PROPFIND on a siteaccess: the sign-in is asked.**

```bash
curl -i -X PROPFIND -H 'Depth: 1' https://www.example.com/index_webdav.php/site/
```

Expected: `HTTP/1.1 401 Unauthorized` with `WWW-Authenticate: Basic realm="..."`. `404` means the siteaccess is not
in `site.ini [SiteSettings] SiteList[]`.

**4. PROPFIND with a user: the content.**

```bash
curl -i -u 'webdav-user' -X PROPFIND -H 'Depth: 1' https://www.example.com/index_webdav.php/site/
```

curl asks for the password. Expected: `207 Multi-Status` listing the folders below the WebDAV start node. `403` means
the user lacks `user/login` for that siteaccess.

**5. A real client.** Mount the address in the client your users use and open, save and delete a test file:

- `cadaver https://www.example.com/index_webdav.php/site/` (Linux command line), then `ls`, `put test.txt`,
  `rm test.txt`;
- Windows Explorer: **Map network drive** with `https://www.example.com/index_webdav.php/site/`;
- macOS Finder: **Go > Connect to Server** with the same address;
- GNOME Files and KDE Dolphin: `davs://www.example.com/index_webdav.php/site/`.

Repeat test 1 against `https://www.example.com/webdav.php/` once: until the old rules are gone it should answer the
same way (through the shim); after you removed them on purpose it should not reach WebDAV any more.

## Confirm that the old name is no longer used

1. **The web server's access log**: after the change, no new WebDAV requests for `webdav.php`.

   ```bash
   grep -c 'webdav\.php' /var/log/apache2/example.com-ssl_access.log
   grep 'webdav\.php' /var/log/nginx/access.log | tail
   ```

   Look at the HTTPS log too, not only the HTTP one. Requests that still arrive tell you which client (address,
   user agent) has to be mounted again.
2. **Exponential's `warning.log`**: no new line `WebDAV request through webdav.php`.

   ```bash
   grep 'old name of the WebDAV entry script' var/log/warning.log | tail -3
   ```

   With a log directory per site (`site.ini [FileSettings] UseGlobalLogDir=disabled`) the file is
   `var/<site>/log/warning.log`.
3. **The stamp file** `webdav-entry-script-deprecated.stamp` in the same directory holds the last day a request came
   through `webdav.php`. A date older than your change means nothing used the old name since.

When all three stay quiet for a while, take `webdav.php` out of every allowlist and rule.

## Sign-in does not change

The rename changes no sign-in rule. WebDAV signs in with basic authentication against Exponential users (serve it over
HTTPS only), the policies of the user apply, and `user/login` for the siteaccess is needed. WebDAV never asks for a
second sign-in step (two-factor authentication) by default and never for "Verify it's you": the second step is asked
only where a site opts WebDAV in (`Guard[]=webdav`), see
[second sign-in step on other sign-in paths](../../features/6.0/second-factor-on-other-sign-in-paths.md). A sign-in
through either name is recorded as a WebDAV session.

## For developers

- `expWebDAVEntryScript` (`kernel/private/classes/webdav/expwebdaventryscript.php`) knows both names: `current()`
  returns the script the request came in through (needed by `eZSys::init()`, which takes the installation root from
  `SCRIPT_FILENAME`), `isEntryScript()` and `isRootRequest()` accept both, `noteDeprecatedEntry()` writes the daily
  warning.
- The shim defines `EXP_WEBDAV_ENTRY_SCRIPT` as `webdav.php` before including `index_webdav.php`.
- `expUserSessionTracker::detectMethod()` records a sign-in through `index_webdav.php` or `webdav.php` as `webdav`.
- Tests: `tests/tests/kernel/private/expWebDAVEntryScriptTest.php` (no database).

## Related pages

- [WebDAV and SOAP](../../help/integrators/webdav-and-soap.md): switching WebDAV on, permissions, known gaps
- [How do I work with content over WebDAV?](../../help/manual/how-do-i-work-with-content-over-webdav.md)
- [Serving the site](../../install/08-serving-the-site.md) and the [web server examples](../../examples/web-servers/README.md)
- [Behaviour changes of 1 to 2 October 2026 and later](behaviour-changes-2026-10.md)
- [Exponential 6.0.15](../../../changelogs/6.0/6.0.15.md), section Upgrading
