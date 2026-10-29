---
title: Velocity
audience: admin, developer
category: examples
order: 15
summary: Exponential Velocity liefert eine öffentliche Website auf den Ports 80 und 443 ohne separaten Webserver aus: das Override von velocity.ini, die erzeugte Site-Datei in /etc/vc, das Aktivieren von Sites, Konfigurationsschnipseln und Modulen, eine systemd-Unit und das Ausrollen einer Änderung.
keywords: Velocity, Einstellungen, Override, Konfigurationsbaum, Zertifikate, Worker, Antwort-Cache, Dienst, Deployment, ausrollen, Beispiel, vc, exp:velocity, qbix, engine, velocity.ini, settings/override, /etc/vc, sites-available, sites-enabled, conf-available, mods-available, HTTPS, Let's Encrypt, HSTS, workers, response cache, systemd, deploy, example
type: example
---
# Velocity

**Exponential Velocity** ist der Anwendungsserver, der für Exponential ausgeliefert wird, und in jeder Phase der
empfohlene Weg, es auszuliefern. Mit seiner Engine `qbix` wird **weder ein separater Webserver noch ein separater
HTTPS-Server gebraucht**: Velocity lauscht selbst auf den Ports für HTTP und HTTPS, hält die Anwendung in persistenten
Workern geladen, beantwortet wiederholte Seitenaufrufe aus seinem eigenen Antwort-Cache und verwaltet seine
Zertifikate. Sein Routing ist das von `.htaccess_root`, fest eingebaut (`expVelocity::STATIC_PATHS`,
`FRONT_CONTROLLERS`, `ENTRY_SCRIPTS`), daher gibt es keine Umschreiberegeln zu kopieren. Die Liste
liefert auch die Icon-Themes der Erweiterungen aus (`extension/<name>/icons/`, SVG-, PNG- und GIF-Bilder mit genau
einer Dateiendung); nach einem Upgrade gilt sie mit dem Neustart, den `exp:velocity deploy` ausführt.

Alles wird in `velocity.ini` eingestellt; der Baum `/etc/vc` wird bei jedem Start daraus erzeugt.

## 1. Die Einstellungen

[`velocity.ini.append.php.example`](../../../eng-us/examples/web-servers/velocity.ini.append.php.example) ist ein
Override für eine öffentliche Website auf den Standardports. Jede Einstellung darin gibt es in `settings/velocity.ini`,
wo jede einzelne beschrieben ist.

```ini
<?php /* #?ini charset="utf-8"?
# Exponential 6: Velocity serving a public site on ports 80 and 443
#
# Free documentation: GFDL 1.3 or later, see doc/eng-us/README-LICENSE.md.
#
# Copy to settings/override/velocity.ini.append.php (drop ".example"), or
# set each value with "exp:velocity config set <Block> <Setting> <value>",
# which writes the same file. Every setting is described in
# settings/velocity.ini; only what differs from the shipped value goes here.
# Explained in velocity.md next to this file.
#
# After a change: php bin/php/console exp:velocity restart --allow-root-user

[ServerSettings]
# qbix: the recommended engine (persistent workers, response cache, TLS,
# HTTP/2, the /etc/vc tree). The shipped default is php (development only).
Engine=qbix
# Every address, the standard ports. The shipped 127.0.0.1:8088 keeps the
# server reachable only locally or through a reverse proxy.
Host=0.0.0.0
Port=80
HTTPSPort=443
# Started as root (needed for ports below 1024), the workers become this
# user and group, the owner of the installation's files
User=example
Group=example
# Worker processes; SpareWorkers adds a dynamic pool for peaks
Workers=8
SpareWorkers=8

[HTTPSSettings]
# HTTPS is on only when Enabled is true AND both files exist
Enabled=true
Certificate=/etc/ssl/example.com/fullchain.pem
Key=/etc/ssl/example.com/privkey.pem
# Start short; raise to 31536000 once every name is served over HTTPS for good
HSTSMaxAge=300

[CacheSettings]
# The response cache for anonymous visitors (seconds a page is kept)
Enabled=enabled
DefaultTtl=30

[LayoutSettings]
# The name of the site file in /etc/vc/sites-available; empty: the host of
# site.ini [SiteSettings] SiteURL
SiteName=example.com
*/ ?>
```

Installieren Sie es als `settings/override/velocity.ini.append.php` (führen Sie es mit einer vorhandenen Datei
zusammen), oder setzen Sie die Werte einzeln, was dieselbe Datei schreibt:

```bash
php bin/php/console exp:velocity config set --apply ServerSettings Engine qbix --allow-root-user
php bin/php/console exp:velocity config set --apply ServerSettings Host 0.0.0.0 --allow-root-user
php bin/php/console exp:velocity config set --apply ServerSettings Port 80 --allow-root-user
php bin/php/console exp:velocity config set --apply ServerSettings HTTPSPort 443 --allow-root-user
php bin/php/console exp:velocity config set --apply ServerSettings User example --allow-root-user
php bin/php/console exp:velocity config set --apply ServerSettings Group example --allow-root-user
php bin/php/console exp:velocity config set --apply HTTPSSettings Certificate /etc/ssl/example.com/fullchain.pem --allow-root-user
php bin/php/console exp:velocity config set --apply HTTPSSettings Key /etc/ssl/example.com/privkey.pem --allow-root-user
php bin/php/console exp:velocity config set --apply HTTPSSettings Enabled true --allow-root-user
php bin/php/console exp:velocity config list --allow-root-user      # * marks what you set
php bin/php/console exp:velocity restart --allow-root-user
php bin/php/console exp:velocity status --allow-root-user
```

Hinweise:

- **Die Datei ist in PHP eingepackt** (`<?php /* ... */ ?>`) wie jedes Override, damit sie ihren Inhalt nie ausgibt,
  wenn sie angefordert wird. Das Beispiel in diesem Verzeichnis endet auf `.example`, damit es im
  Dokumentationsbaum nie eine PHP-Datei ist.
- **Ports unter 1024 brauchen root.** Als root gestartet, behält der Elternprozess root (er bindet die Ports und liest
  den Schlüssel), und jeder Worker wechselt zu `User` und `Group`, bevor irgendein Anwendungscode läuft.
- **HTTPS** ist nur eingeschaltet, wenn `Enabled` true ist *und* beide Dateien existieren; ein Zertifikat, das seine
  Prüfungen nicht besteht, wird durch einen selbstsignierten Ersatz vertreten, statt angezeigt zu werden. Ein
  erneuertes Zertifikat wird innerhalb einer Minute übernommen, ohne Neustart. Für Let's Encrypt, betrieben von
  Velocity selbst, siehe `exp:velocity ssl` in
  [Kapitel 8, Abschnitt 8.3.9](../../installation/08-die-website-ausliefern.md#839-https-direkt-von-velocity).
- **HSTS** beginnt mit 300 Sekunden; erhöhen Sie `HSTSMaxAge` erst dann auf ein Jahr, wenn jeder Name dauerhaft über
  HTTPS ausgeliefert wird.
- **Header.** `[ServerSettings] ResponseHeaders[]` bringt die Standard-Sicherheits-Header des Kernels für das mit, was
  Velocity selbst beantwortet, und `ResponseHeadersOnScripts` ergänzt sie bei Seiten, denen sie fehlen: Es gibt nichts
  einzustellen.

## 2. Der Baum /etc/vc

Die Konfiguration von Velocity auf der Festplatte ist wie `/etc/apache2` unter Debian aufgebaut:

```
/etc/vc/
├── vc.conf                   settings shared by every site        (apache2.conf)
├── ports.conf                listen ports                         (ports.conf)
├── envvars                   environment of the server process    (envvars)
├── conf-available/*.conf     shared snippets                      -> conf-enabled/  (symlinks)
├── mods-available/*.conf     engine modules: http2, cache, ...    -> mods-enabled/  (symlinks)
├── sites-available/*.conf    one file per installation            -> sites-enabled/ (symlinks)
├── designs/                  the server's own page designs
└── ssl/                      certificates
```

`exp:velocity start` und `restart` schreiben `ports.conf`, die Moduldateien, die es verwaltet, und
`sites-available/<SiteName>.conf` (Modus `0600`) aus `velocity.ini`. Eine erzeugte Site-Datei sieht so aus (Pfade auf
eine Beispielinstallation gekürzt, die Liste der statischen Pfade abgeschnitten):

```json
{
    "_generated": "exp:velocity",
    "Q": {
        "web": {
            "static": {
                "paths": [
                    "^/(design/[^/]+/(stylesheets|images|javascript|fonts)/|share/icons/|...|w3c/p3p\\.xml$)"
                ]
            },
            "cache": {
                "dir": "/var/www/example.com/var/cache/qbix-reverse"
            }
        },
        "webserver": {
            "scripts": ["/index.php", "/index_rest.php", "/index_treemenu.php"],
            "frontControllers": {
                "^/(api/|index_rest\\.php)": "index_rest.php",
                "^/([^/]+/)?content/treemenu": "index_treemenu.php"
            },
            "log": {
                "dir": "/var/www/example.com/var/vc/qbix/log"
            },
            "headers": {
                "X-Content-Type-Options": "nosniff",
                "X-Frame-Options": "SAMEORIGIN",
                "Referrer-Policy": "strict-origin-when-cross-origin",
                "Permissions-Policy": "camera=(), microphone=(), payment=(), usb=()"
            },
            "headersOnScripts": true,
            "hsts": {
                "maxAge": 300
            }
        }
    }
}
```

Bearbeiten Sie keine erzeugte Datei: Ändern Sie `velocity.ini` und starten Sie neu. Um eine eigene Datei zu behalten,
entfernen Sie ihre Markierung `"_generated"`; dann wird sie nie überschrieben, wie eine Conffile von dpkg. Aktivieren
und deaktivieren Sie wie mit `a2ensite`, `a2enconf` und `a2enmod`:

```bash
php bin/php/console exp:velocity layout --allow-root-user                 # every path, what is enabled, the files in use
php bin/php/console exp:velocity site enable --apply example.com --allow-root-user
php bin/php/console exp:velocity conf enable --apply reverse-proxy --allow-root-user
php bin/php/console exp:velocity mod disable --apply http2 --allow-root-user
php bin/php/console exp:velocity ctl configtest --allow-root-user         # the engine's own check of the tree
```

## 3. Beim Booten starten

`exp:velocity` installiert keinen Boot-Dienst.
[`exponential-velocity.service`](../../../eng-us/examples/web-servers/exponential-velocity.service) ist eine
systemd-Unit, die den Server über `exp:velocity` startet, sodass jede Einstellung in `velocity.ini` bleibt:

```ini
# Exponential 6: a systemd unit that starts Velocity through exp:velocity
#
# Free documentation: GFDL 1.3 or later, see doc/eng-us/README-LICENSE.md.
#
# Not shipped as a unit; derived from what expVelocity::start() does (it
# starts the server detached and returns once the port answers, leaving the
# pid in var/vc/qbix/run/server.pid) and from doc/eng-us/install/08-serving-the-site.md,
# 8.3.11. It keeps every setting in velocity.ini. Explained in velocity.md.
#
# Install: /etc/systemd/system/exponential-velocity-example.service, replace
# /var/www/example.com and the database unit, then
#   systemctl daemon-reload
#   systemctl enable --now exponential-velocity-example
#
# It runs as root so the server can bind ports 80 and 443 and read the key;
# the workers still drop to [ServerSettings] User and Group. With Instances
# above 1, PIDFile names instance 0 only. Do not combine it with the unit of
# the operating-system package for the same ports.

[Unit]
Description=Exponential Velocity for example.com
After=network-online.target mysqld.service
Wants=network-online.target

[Service]
Type=forking
WorkingDirectory=/var/www/example.com
ExecStart=/usr/bin/php bin/php/console exp:velocity start --allow-root-user
ExecStop=/usr/bin/php bin/php/console exp:velocity stop --allow-root-user
ExecReload=/usr/bin/php bin/php/console exp:velocity graceful --allow-root-user
PIDFile=/var/www/example.com/var/vc/qbix/run/server.pid
TimeoutStartSec=90
Restart=on-failure
RestartSec=5

[Install]
WantedBy=multi-user.target
```

## 4. Eine Änderung ausrollen

Worker halten Klassen, INI-Werte und kompilierte Templates im Speicher. Nach einer Änderung am Code oder an den
Einstellungen baut ein einziger Befehl die Autoloads neu, leert die Caches, lädt PHP-FPM neu, falls es die Website
ebenfalls ausliefert, startet Velocity neu und leert die Caches, die gerenderte Ausgaben halten, in dieser Reihenfolge:

```bash
php bin/php/console exp:velocity deploy --dry-run --allow-root-user   # what it would do, nothing else
php bin/php/console exp:velocity deploy --allow-root-user
php bin/php/console exp:velocity deploy --kernel --allow-root-user    # a kernel class was added or renamed
```

## Prüfen

```bash
curl -sI https://example.com/ | head -1                                         # HTTP/2 200 or a redirect
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/.git/config        # 403 from the qbix engine
curl -s https://example.com/settings/site.ini | grep -c '^\[DatabaseSettings\]' # 0: a page, not the file
php bin/php/console exp:velocity ssl show --allow-root-user                     # the certificate in use
```

So wurde dieses Beispiel geprüft: Jede `[Block] Setting` des Overrides existiert in der ausgelieferten
`settings/velocity.ini`; die Unit hat `systemd-analyze verify` bestanden; die Form der Site-Datei ist eine erzeugte
Datei, in der die Pfade dieses Rechners ersetzt wurden.

## Verwandte Seiten

- [Kapitel 8, Abschnitt 8.3: Exponential Velocity, der empfohlene Weg](../../installation/08-die-website-ausliefern.md#83-exponential-velocity-der-empfohlene-weg),
  [8.3.10 Der Konfigurationsbaum](../../installation/08-die-website-ausliefern.md#8310-der-konfigurationsbaum-etcvc-im-debian-stil),
  [8.3.11 Velocity als Dienst betreiben](../../installation/08-die-website-ausliefern.md#8311-velocity-als-dienst-betreiben).
- [Velocity-Engines](../../bc/6.0/velocity-engines.md) und [FrankenPHP](frankenphp.md).
- [Kapitel 13, Abschnitt 13.12: Velocity-spezifische Härtung](../../installation/13-sicherheitshaertung.md#1312-velocity-spezifische-härtung).
- [Webserver-Beispiele](README.md) und [alle Beispiele](../README.md).
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite dürfen Sie frei kopieren, übersetzen und verbessern.
