---
title: LiteSpeed und OpenLiteSpeed
audience: admin, developer
category: examples
order: 70
summary: Exponential 6 mit LiteSpeed Enterprise ausliefern (es liest .htaccess wie Apache) oder mit OpenLiteSpeed (die Regeln stehen im virtuellen Host), mit einem vollständigen virtuellen Host für OpenLiteSpeed, dessen Umschreiberegeln mit mod_rewrite von Apache getestet wurden.
keywords: LiteSpeed, OpenLiteSpeed, virtueller Host, Umschreiberegeln, Sicherheits-Header, Beispiel, LiteSpeed Enterprise, OLS, vhconf.conf, LSAPI, lsphp, rewrite, .htaccess, autoLoadHtaccess, context, extraHeaders, example
type: example
---
# LiteSpeed und OpenLiteSpeed

**LiteSpeed Web Server (Enterprise)** ist ein direkter Ersatz für Apache: Er liest die Konfiguration von Apache und
`.htaccess`-Dateien, einschließlich der Regeln von `mod_rewrite`. Liefern Sie Exponential genauso aus wie mit Apache:
`cp .htaccess_root .htaccess` ([Apache .htaccess](apache-htaccess.md)), PHP über LSAPI von LiteSpeed. Nichts auf dieser
Seite wird dafür gebraucht.

**OpenLiteSpeed**, die Open-Source-Ausgabe, liest von einer `.htaccess` nur die Umschreiberegeln, nur wenn
`autoLoadHtaccess` eingeschaltet ist, und nach einer Änderung der Datei erst nach einem Neustart. Das Beispiel unten
legt die Regeln stattdessen in den virtuellen Host, wo sie einmal gelesen werden.

> **Hinweis:** Dieses Beispiel ist abgeleitet, nicht unter OpenLiteSpeed getestet: Auf dem Rechner, auf dem die
> Beispiele geprüft werden, gibt es kein OpenLiteSpeed. Seine Umschreiberegeln verwenden die Syntax von `mod_rewrite`
> aus Apache, die OpenLiteSpeed umsetzt, und sie liefen in einem Wegwerf-Apache, wo sie jede Anfrage der
> [Testliste](README.md#welche-konfiguration) wie erwartet beantworteten. Den Rest der Datei (die eigenen Schlüssel des
> virtuellen Hosts) prüft die WebAdmin-Konsole von OpenLiteSpeed beim Laden. Prüfen Sie die Website mit den Anfragen
> unten, bevor sie öffentlich ist.

## Installieren

1. Legen Sie den virtuellen Host an und ordnen Sie ihn in WebAdmin (oder in `/usr/local/lsws/conf/httpd_config.conf`)
   den Listenern für die Ports 80 und 443 zu.
2. Legen Sie die Datei als `/usr/local/lsws/conf/vhosts/example.com/vhconf.conf` ab und ersetzen Sie `example.com`,
   `/var/www/example.com`, die Zertifikatspfade, den Pfad zu lsphp (`lsphp83` für PHP 8.3) sowie `extUser`/`extGroup`
   (den Eigentümer der Dateien der Installation).
3. Sanfter Neustart: `/usr/local/lsws/bin/lswsctrl restart` (oder WebAdmin > Graceful Restart).

## Die Datei

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

## Hinweise

- **`^/?` in jedem Muster.** Wie Apache vergleicht OpenLiteSpeed eine Regel mit dem Pfad mit oder ohne führenden
  Schrägstrich, je nachdem, wo die Regel steht (virtueller Host oder Kontext, `.htaccess`); mit dem optionalen
  Schrägstrich funktionieren dieselben Regeln an jeder Stelle.
- **Die Skript-Regel** prüft `%{DOCUMENT_ROOT}%{REQUEST_URI}` und greift daher nur bei einem Skript, das existiert, wie
  in `.htaccess_root`.
- **Ausdrückliche Sperren.** Die Blöcke `context` für `/.git/`, `/settings/`, `/vendor/` und `/var/log/` sind eine
  zusätzliche Schutzschicht: Mit eingeschalteten Umschreiberegeln werden diese Pfade auf `index.php` umgeschrieben,
  bevor ein Kontext gewählt wird; wären die Regeln je ausgeschaltet, verweigern die Kontexte sie trotzdem.
- **Header.** `extraHeaders` fügt dem, was OpenLiteSpeed sendet, die Standard-Sicherheits-Header des Kernels hinzu.
- **ACME-Challenges.** `.well-known` ist von der Punkt-Regel ausgenommen, steht aber, wie in `.htaccess_root`, nicht
  auf der Liste der statischen Dateien. Verwenden Sie die eigene Let's-Encrypt-Unterstützung von OpenLiteSpeed oder
  fügen Sie direkt nach der Punkt-Regel `RewriteRule ^/?\.well-known/acme-challenge/ - [L]` ein.

## Prüfen

```bash
for p in /.git/config /.env /kernel/classes/ezini.php /extension/ezjscore/ezinfo.php; do
  printf '%-34s %s\n' "$p" "$(curl -s -o /dev/null -w '%{http_code}' https://example.com$p)"     # 404 each
done
curl -s https://example.com/settings/site.ini | grep -c '^\[DatabaseSettings\]'   # 0: a page, not the file
curl -sI https://example.com/design/admin4/stylesheets/components.css | head -1   # 200
```

## Verwandte Seiten

- [Apache .htaccess](apache-htaccess.md): die Regeln, eine nach der anderen erklärt; LiteSpeed Enterprise verwendet diese Datei.
- [Kapitel 13, Abschnitt 13.2: Was der Webserver niemals ausliefern darf](../../installation/13-sicherheitshaertung.md#132-was-der-webserver-niemals-ausliefern-darf).
- [Betriebssystemunterstützung](../../hilfe/betriebssystemunterstuetzung.md): Webserver und PHP-SAPIs nach Plattform.
- [Webserver-Beispiele](README.md) und [alle Beispiele](../README.md).
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite dürfen Sie frei kopieren, übersetzen und verbessern.
