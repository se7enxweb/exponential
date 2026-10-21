---
title: LiteSpeed and OpenLiteSpeed
audience: admin, developer
category: examples
order: 70
summary: Serving Exponential 6 with LiteSpeed Enterprise (it reads .htaccess like Apache) or OpenLiteSpeed (the rules in the virtual host), with a complete OpenLiteSpeed virtual host whose rewrite rules were tested in Apache's mod_rewrite.
keywords: LiteSpeed, LiteSpeed Enterprise, OpenLiteSpeed, OLS, vhconf.conf, LSAPI, lsphp, rewrite, .htaccess, autoLoadHtaccess, context, extraHeaders, example
type: example
---
# LiteSpeed and OpenLiteSpeed

**LiteSpeed Web Server (Enterprise)** is a drop-in replacement for Apache: it reads Apache's configuration and
`.htaccess` files, including `mod_rewrite` rules. Serve Exponential exactly as with Apache: `cp .htaccess_root
.htaccess` ([Apache .htaccess](apache-htaccess.md)), PHP through LiteSpeed's LSAPI. Nothing on this page is needed.

**OpenLiteSpeed**, the open source edition, reads only the rewrite rules of a `.htaccess`, only when
`autoLoadHtaccess` is on, and only after a restart when the file changes. The example below puts the rules in the
virtual host instead, where they are read once.

> **Note:** This example is derived, not tested on OpenLiteSpeed: there is no OpenLiteSpeed on the machine the
> examples are verified on. Its rewrite rules use Apache's `mod_rewrite` syntax, which OpenLiteSpeed implements, and
> they were run in a throwaway Apache, where they answered every request of the [test list](README.md#which-one) as
> expected. The rest of the file (the virtual host's own keys) is checked by OpenLiteSpeed's WebAdmin when you load it.
> Check the site with the requests below before it is public.

## Install it

1. Define the virtual host and map it to the listeners for ports 80 and 443 in WebAdmin (or in
   `/usr/local/lsws/conf/httpd_config.conf`).
2. Put the file as `/usr/local/lsws/conf/vhosts/example.com/vhconf.conf`, replace `example.com`,
   `/var/www/example.com`, the certificate paths, the lsphp path (`lsphp83` for PHP 8.3) and `extUser`/`extGroup`
   (the owner of the installation's files).
3. Graceful restart: `/usr/local/lsws/bin/lswsctrl restart` (or WebAdmin > Graceful Restart).

## The file

```apache
# Exponential 6: an OpenLiteSpeed virtual host (vhconf.conf)
#
# Free documentation: GFDL 1.3 or later, see doc/eng-us/README-LICENSE.md; rules derived from .htaccess_root (GPL v2 or later).
#
# Derived, not shipped by the kernel, and NOT syntax-checked here (no
# OpenLiteSpeed on the machine the examples are verified on): a translation
# of .htaccess_root (this release). Check it in the WebAdmin console
# (Virtual Hosts > example.com, then a graceful restart) and with the requests
# in litespeed.md before the site is public. Compare it with .htaccess_root
# after every upgrade.
#
# LiteSpeed Enterprise reads .htaccess like Apache: there, copy
# .htaccess_root to .htaccess and you need none of this file.
# OpenLiteSpeed reads only the rewrite rules of .htaccess, and only with
# autoLoadHtaccess; this file puts the rules in the virtual host instead.
#
# Install: /usr/local/lsws/conf/vhosts/example.com/vhconf.conf, with the
# virtual host and its listener mapping defined in httpd_config.conf (or
# through WebAdmin). Replace example.com, /var/www/example.com and the
# lsphp version.

docRoot                   /var/www/example.com
vhDomain                  example.com
vhAliases                 www.example.com
enableGzip                1
enableBr                  1

errorlog $VH_ROOT/logs/error.log {
  useServer               0
  logLevel                WARN
  rollingSize             10M
}

accesslog $VH_ROOT/logs/access.log {
  useServer               0
  rollingSize             10M
  keepDays                30
}

index  {
  useServer               0
  indexFiles              index.php
  autoIndex               0
}

# PHP through LSAPI; the external app "lsphp" is defined at server level
# (WebAdmin > Server Configuration > External App), or here:
extprocessor lsphp {
  type                    lsapi
  address                 uds://tmp/lshttpd/example.com.sock
  maxConns                10
  env                     PHP_LSAPI_CHILDREN=10
  initTimeout             60
  retryTimeout            0
  respBuffer              0
  autoStart               2
  path                    /usr/local/lsws/lsphp83/bin/lsphp
  backlog                 100
  instances               1
  extUser                 example
  extGroup                example
  memSoftLimit            2047M
  memHardLimit            2047M
}

scripthandler  {
  add                     lsapi:lsphp php
}

# Response headers: the values of the kernel's site.ini [HTTPHeaderSettings]
# SecurityHeaders[] for the files OpenLiteSpeed serves itself
context / {
  location                $DOC_ROOT/
  allowBrowse             1
  extraHeaders            <<<END_extraHeaders
X-Content-Type-Options nosniff
X-Frame-Options SAMEORIGIN
Referrer-Policy strict-origin-when-cross-origin
Permissions-Policy camera=(), microphone=(), payment=(), usb=()
  END_extraHeaders
}

# Defense in depth: never served even if the rewrite rules were switched off.
# With the rules on, these paths are rewritten to index.php before any
# context is chosen.
context /.git/ {
  allowBrowse             0
}
context /settings/ {
  allowBrowse             0
}
context /vendor/ {
  allowBrowse             0
}
context /var/log/ {
  allowBrowse             0
}

rewrite  {
  enable                  1
  autoLoadHtaccess        0
  logLevel                0
  # The rules of .htaccess_root, in its order. "^/?" accepts the URL with
  # and without its leading slash, as the virtual host and a context see it.
  rules                   <<<END_rules
# No dot file or dot directory; .well-known stays reachable
RewriteRule (^|/)\.(?!well-known(/|$)) - [R=404,L]
# No script below the root runs because its path was asked for
RewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI} -f
RewriteRule ^/?.+/[^/]+\.(php[0-9]?|phtml|phar)$ - [R=404,L]
RewriteRule ^/?api/ /index_rest.php [L]
RewriteRule ^/?index_rest\.php - [L]
RewriteRule ^/?([^/]+/)?content/treemenu.* /index_treemenu.php [L]
RewriteRule ^/?var/([^/]+/)?storage/images(-versioned)?/.* - [L]
RewriteRule ^/?var/([^/]+/)?storage/pdf/[A-Za-z0-9][A-Za-z0-9._-]*\.pdf$ - [L]
RewriteRule ^/?var/([^/]+/)?storage/original/image/.+\.(png|jpe?g|gif|webp|svg)$ - [L]
RewriteRule ^/?var/([^/]+/)?cache/(texttoimage|public)/.* - [L]
#RewriteRule ^/?var_cache/([^/]+/)?cache/(texttoimage|public)/.* - [L]
RewriteRule ^/?design/[^/]+/(stylesheets|images|javascript|fonts)/.* - [L]
RewriteRule ^/?share/icons/.* - [L]
# Icon themes of extensions (icon.ini [ExtensionSettings] IconExtensions[]): images with exactly one extension
RewriteRule ^/?extension/[^/]+/icons/[A-Za-z0-9_/-]+\.(svg|png|gif)$ - [L]
RewriteRule ^/?extension/[^/]+/design/[^/]+/(stylesheets|flash|images|lib|javascripts?|fonts|vendor|media)/.* - [L]
RewriteRule ^/?packages/styles/.+/(stylesheets|images|javascript)/[^/]+/.* - [L]
RewriteRule ^/?packages/styles/.+/thumbnail/.* - [L]
RewriteRule ^/?var/storage/packages/.+\.(png|jpe?g|gif|webp)$ - [L]
RewriteRule ^/?favicon\.ico - [L]
RewriteRule ^/?design/standard/images/favicon\.ico - [L]
RewriteRule ^/?robots\.txt - [L]
RewriteRule ^/?index\.js$ - [L]
RewriteRule ^/?sw\.js$ - [L]
RewriteRule ^/?w3c/p3p\.xml - [L]
# Everything else, settings/, var/, vendor/ and composer.json included
RewriteRule .* /index.php [L]
  END_rules
}

vhssl  {
  keyFile                 /etc/ssl/example.com/privkey.pem
  certFile                /etc/ssl/example.com/fullchain.pem
  certChain               1
  enableSpdy              15
}
```

## Notes

- **`^/?` in every pattern.** Like Apache, OpenLiteSpeed matches a rule against the path with or without its leading
  slash depending on where the rule sits (virtual host or context, `.htaccess`); the optional slash makes the same
  rules work in each place.
- **The script rule** tests `%{DOCUMENT_ROOT}%{REQUEST_URI}`, so it fires only for a script that exists, as in
  `.htaccess_root`.
- **Explicit denies.** The `context` blocks for `/.git/`, `/settings/`, `/vendor/` and `/var/log/` are defense in
  depth: with the rewrite rules on, those paths are rewritten to `index.php` before a context is chosen; if the rules
  were ever switched off, the contexts still refuse them.
- **Headers.** `extraHeaders` adds the kernel's default security headers to what OpenLiteSpeed sends.
- **ACME challenges.** `.well-known` is exempt from the dot rule but, as in `.htaccess_root`, not on the asset list.
  Use OpenLiteSpeed's own Let's Encrypt support, or add `RewriteRule ^/?\.well-known/acme-challenge/ - [L]` directly
  after the dot rule.

## Check it

```bash
for p in /.git/config /.env /kernel/classes/ezini.php /extension/ezjscore/ezinfo.php; do
  printf '%-34s %s\n' "$p" "$(curl -s -o /dev/null -w '%{http_code}' https://example.com$p)"     # 404 each
done
curl -s https://example.com/settings/site.ini | grep -c '^\[DatabaseSettings\]'   # 0: a page, not the file
curl -sI https://example.com/design/admin4/stylesheets/components.css | head -1   # 200
```

## Related pages

- [Apache .htaccess](apache-htaccess.md): the rules explained one by one; LiteSpeed Enterprise uses this file.
- [Chapter 13, section 13.2: What the web server must never hand out](../../install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out).
- [Operating system support](../../help/os-support.md): web servers and PHP SAPIs by platform.
- [Web server examples](README.md) and [all examples](../README.md).
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
