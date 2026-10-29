---
title: Beispiele zum Demomodus
audience: hosting, admin, developer
category: examples
order: 95
summary: Beispieldateien, die EXP_DEMO_MODE=1 für eine öffentliche Demo von Exponential setzen – SetEnv in .htaccess und im virtuellen Host von Apache, ein PHP-FPM-Pool, nginx, ein systemd-Drop-in für Velocity, eine Crontab, eine Umgebungsdatei für Container – und die allgemeine Form eines Skripts zum Neuaufbau der Demo.
keywords: Demomodus, Demo-Modus, öffentliche Demo, Beispiel, Umgebungsvariable, virtueller Host, Neuaufbau, demo mode, EXP_DEMO_MODE, public demo, example, htaccess, SetEnv, Apache, LiteSpeed, lsphp, PHP-FPM, env, nginx, fastcgi_param, systemd, drop-in, Velocity, crontab, docker, env_file, install-demo-sqlite.sh, exp:install
type: example
---
# Beispiele zum Demomodus

Eine öffentliche Demo von Exponential veröffentlicht ihre Anmeldedaten für den Administrator und läuft mit der
Umgebungsvariablen **`EXP_DEMO_MODE=1`**: Administratorpasswörter lassen sich weder ändern noch zurücksetzen, die
Installationsprogramme akzeptieren das veröffentlichte Demo-Passwort (`publish` oder `publishing`), und die
Administration zeigt ein Banner **Demo-Modus**. Die Variable muss jeden PHP-Prozess der Demo erreichen und keinen
Prozess einer anderen Website. Die Dateien hier zeigen, wie das geht, eine für jede Stelle, an der PHP läuft. Was der
Demomodus bewirkt und welche Risiken er hat: [Demomodus](../../bc/6.0/demomodus.md).

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../webserver/nginx.md), [LiteSpeed](../webserver/litespeed.md), [Caddy](../webserver/caddy.md), [IIS](../webserver/iis.md),
> [Velocity](../webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../../hilfe/hosting/control-panels/plesk.md), [cPanel](../../hilfe/hosting/control-panels/cpanel.md), [DirectAdmin](../../hilfe/hosting/control-panels/directadmin.md), [ISPConfig](../../hilfe/hosting/control-panels/ispconfig.md).

> **Warnung:** Kopieren Sie diese Dateien nur in die Konfiguration einer Demo, deren Inhalte, Benutzer und
> Einstellungen allesamt zum Wegwerfen sind. Niemals in einen gemeinsam genutzten Pool, eine serverweite
> Konfiguration, ein Basis-Image oder eine Vorlage, aus der andere Websites gebaut werden.

## Inhalt

1. [Die Dateien](#die-dateien)
2. [Welche Sie brauchen](#welche-sie-brauchen)
3. [Prüfen](#prüfen)
4. [Verwandte Seiten](#verwandte-seiten)

## Die Dateien

| Datei | Wohin sie gehört |
|---|---|
| [`htaccess-setenv.conf`](../../../eng-us/examples/demo-mode/htaccess-setenv.conf) | an die `.htaccess` der Demo angehängt: Apache (mod_php oder PHP-FPM über `mod_proxy_fcgi`) und LiteSpeed mit `lsphp` |
| [`apache-vhost-demo.conf`](../../../eng-us/examples/demo-mode/apache-vhost-demo.conf) | `SetEnv` im virtuellen Host der Demo unter Apache (auch ein Feld „Zusätzliche Apache-Direktiven“ in Plesk) |
| [`php-fpm-pool-demo.conf`](../../../eng-us/examples/demo-mode/php-fpm-pool-demo.conf) | `env[EXP_DEMO_MODE] = 1` im eigenen PHP-FPM-Pool der Demo |
| [`nginx-fastcgi-demo.conf`](../../../eng-us/examples/demo-mode/nginx-fastcgi-demo.conf) | `fastcgi_param EXP_DEMO_MODE 1` dort, wo nginx Anfragen an PHP-FPM weitergibt |
| `/etc/vc/sites-available/<site>.envvars` (hier keine Datei: eine Zeile) | **empfohlen für Exponential Velocity**: `export EXP_DEMO_MODE=1` in den Site-envvars der Demo (`<site>` zeigt `./console exp:velocity layout`); `exp:velocity start` liest die Datei bei jedem Start nur für den Server dieser Installation, sodass Neustarts und Deployments aus jeder Shell sie behalten. Nie in der gemeinsamen `/etc/vc/envvars`, die der Server jeder Installation liest und die ein Worker dafür ablehnt ([Velocity: nur eine Installation](../../bc/6.0/demomodus.md#velocity-nur-eine-installation)) |
| [`velocity-demo-mode.conf`](../../../eng-us/examples/demo-mode/velocity-demo-mode.conf) | ein systemd-Drop-in mit `Environment=EXP_DEMO_MODE=1` für die eigene Unit, die Exponential Velocity für diese Installation startet |
| [`crontab-demo`](../../../eng-us/examples/demo-mode/crontab-demo) | die Crontab des Demo-Benutzers: `EXP_DEMO_MODE=1` für die Cronjobs und den geplanten Neuaufbau |
| [`docker-demo.env`](../../../eng-us/examples/demo-mode/docker-demo.env) | eine Umgebungsdatei nur für den Container der Demo (`--env-file`, `env_file:`) |
| [`install-demo-sqlite.sh`](../../../eng-us/examples/demo-mode/install-demo-sqlite.sh) | die allgemeine Form eines Skripts zum Neuaufbau: klonen, `composer install`, `exp:install` auf SQLite im Demomodus, prüfen, dass das veröffentlichte Passwort wie angegeben übernommen wurde, `SetEnv` in `.htaccess`, austauschen, vorwärmen |

## Welche Sie brauchen

- **Web-Anfragen:** je nachdem, was PHP ausliefert, eine der Varianten `.htaccess`, virtueller Host, PHP-FPM-Pool,
  nginx oder (für Velocity) die Site-envvars `/etc/vc/sites-available/<site>.envvars` bzw. das systemd-Drop-in. PHP liest die Variable zuerst mit `getenv()`,
  dann aus `$_SERVER`; sowohl die FastCGI-Parameter von PHP-FPM als auch `SetEnv` von Apache kommen auf diesem Weg an.
- **Cron und die Konsole:** die Zeile in der Crontab sowie `export EXP_DEMO_MODE=1` in der Shell aller, die Befehle auf
  der Demo ausführen. Ein Prozess ohne die Variable läuft mit ausgeschaltetem Demomodus.
- **Die Installation:** `EXP_DEMO_MODE=1` in der Umgebung von `exp:install`, dem Kickstarter oder dem PHP des
  Setup-Assistenten. Ohne die Variable wird ein bekanntes oder kurzes Passwort durch ein erzeugtes ersetzt, was das
  Skript zum Neuaufbau als fehlgeschlagenen Build wertet.
- **Container:** die Umgebungsdatei, nur für den Dienst der Demo.
- **Zweistufige Anmeldung:** eine neue Installation verlangt sie von den Gruppen Administrator users und Editors
  (`settings/override/sevenxauthentication2fa.ini.append.php`, `Enforce2FA=enabled`). Die Besucher einer öffentlichen
  Demo teilen sich ein Administratorkonto; wer zuerst eine Authenticator-App einrichtet, sperrte alle anderen aus.
  Beide Skripte zum Neuaufbau schalten sie deshalb nach der Installation aus (`Enforce2FA=disabled`);
  `DEMO_ENFORCE_2FA=1` behält die Vorgabe, für eine Demo, die den zweiten Schritt zeigt und nicht geteilt wird.
- **Warteschlange und Importe:** `crontab-demo` führt die Cronjob-Teile `expqueue` und `sqliimport` jede Minute mit
  dem Siteaccess der Administration aus (`-s admin`), in dem sqliimport eingeschaltet ist.

## Prüfen

- Die Administration zeigt das Banner **Demo-Modus** auf jeder Seite; die Seite „Passwort vergessen“ teilt jedem
  Besucher mit, dass Administratorpasswörter nicht zurückgesetzt werden können.
- `grep 'demo/mode' var/log/security.log` listet eine Zeile pro Prozess, der im Demomodus gestartet ist, mit seiner
  SAPI (`fpm-fcgi`, `litespeed`, `cli` und so weiter). **Setup > Logdateien** fasst sie als `demo/mode` zusammen.
- Das Audit-Protokoll enthält pro Prozess ein Ereignis `system.demo.mode`.
- Wenn etwas nicht funktioniert: [Probleme mit dem Demomodus](../../hilfe/hosting/runbooks/probleme-mit-dem-demomodus.md).

## Verwandte Seiten

- [Demomodus: der Hinweis zum Upgrade](../../bc/6.0/demomodus.md)
- [Eine öffentliche Demo auf LiteSpeed und Plesk betreiben](../../hilfe/hosting/server/rezept-eine-oeffentliche-demo-auf-litespeed-und-plesk-betreiben.md)
- [Demomodus für Hoster](../../hilfe/hosting/sicherheit/demomodus-fuer-hoster.md), [Häufige Fragen zum Demomodus](../../hilfe/demomodus-faq.md)
- [Umgebungsvariablen](../../anleitungen/umgebungsvariablen.md)
- [Webserver-Beispiele](../webserver/README.md)
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.
