---
title: PHP-Auswahl und FPM-Pools
audience: hosting, admin
category: hosting
order: 70
summary: Wie Sie einer Exponential-Website auf einem Server mit Panel das richtige PHP geben - das Web-PHP und das PHP der Kommandozeile müssen übereinstimmen, ein PHP-FPM-Pool pro Website, der als Benutzer der Website läuft, die Einstellungen, die im Pool zählen, und wie Sie den PHP-FPM-Master neu laden, der die Website wirklich ausliefert (von exp:velocity deploy automatisch erkannt).
keywords: PHP-Version, PHP-Auswahl, PHP der Kommandozeile, Web-PHP, PHP-FPM, Pool, php_admin_value, memory_limit, upload_max_filesize, OPcache, validate_timestamps, request_terminate_timeout, disable_functions, open_basedir, neu laden, PhpFpmService, plesk-php85-fpm, Remi, Debian, PHP version, PHP selector, command line PHP, web PHP, pool, reload
type: help
---
# PHP-Auswahl und FPM-Pools

Diese Seite ist für Systemadministratoren und Support-Mitarbeitende bei Hosting-Anbietern, die PHP für Exponential-Websites auf
Servern mit Panel auswählen und konfigurieren. Sie beantwortet, welche PHP-Versionen Sie anbieten, warum das PHP der Kommandozeile
genauso zählt wie das Web-PHP, wie ein PHP-FPM-Pool für eine Exponential-Website aussehen sollte und wie Sie nach einer Änderung das
richtige PHP neu laden, was auf Servern mit mehreren PHP-Versionen der häufigste Fehler ist.

## Inhalt

1. [Welche PHP-Versionen Sie anbieten](#welche-php-versionen-sie-anbieten)
2. [Web-PHP und PHP der Kommandozeile](#web-php-und-php-der-kommandozeile)
3. [Ein Pool pro Website](#ein-pool-pro-website)
4. [Pool-Einstellungen für Exponential](#pool-einstellungen-für-exponential)
5. [OPcache](#opcache)
6. [Das richtige PHP-FPM neu laden](#das-richtige-php-fpm-neu-laden)
7. [Einschränkungen, die Panels oft setzen](#einschränkungen-die-panels-oft-setzen)
8. [Checkliste](#checkliste)

## Welche PHP-Versionen Sie anbieten

Aus [2.2 PHP](../../../installation/02-voraussetzungen.md#22-php):

| PHP | Stand |
|---|---|
| 8.0 | die älteste unterstützte Version; läuft unter Apache mit mod_php oder PHP-FPM; Velocity läuft darauf nicht |
| 8.1 bis 8.4 | unterstützt, einschließlich Velocity |
| 8.5 | unterstützt; die neueste getestete Version |
| 7.4 und älter | ab 6.0.8 nicht unterstützt; eine Website, die auf PHP 7.4 bleiben muss, bleibt auf 6.0.7 |

Nutzen Sie das neueste PHP, das Ihr System anbietet: Jede Version bringt Geschwindigkeit, und die älteren verlieren zuerst die
Sicherheitsunterstützung. 64-Bit-PHP wird für die Unterstützung von mehr als 30 Sprachen empfohlen.

> **Hinweis**
> Die Unterstützung für PHP 8.0 ist neu in 6.0.15; die Releases `v6.0.8` bis `v6.0.14` verlangen PHP 8.1. Eine Kunden-Website auf
> einem älteren Release 6.0.x braucht mindestens 8.1 ([2.2](../../../installation/02-voraussetzungen.md#22-php)).

## Web-PHP und PHP der Kommandozeile

Exponential nutzt PHP zweimal: Das PHP des Webservers (PHP-FPM, mod_php oder die Worker von Velocity) liefert Seiten aus, und das PHP
der Kommandozeile (`php`) führt Composer, die Installer, die Konsole, die Cronjobs und Velocity selbst aus. Beide müssen eine
unterstützte Version sein und dieselben Erweiterungen haben. Auf Maschinen mit mehreren PHP-Versionen (Plesk, Remi) ist das `php` im
`PATH` oft nicht das, das der Webserver nutzt ([Kommandozeile und Web-PHP](../../../installation/02-voraussetzungen.md#php-auf-der-kommandozeile-und-im-web)).

Was schiefgeht, wenn sie sich unterscheiden: Composer wählt Paketversionen, die das Web-PHP nicht ausführen kann, oder eine
Installation gelingt auf der Kommandozeile, und der Browser zeigt eine weiße Seite.

```bash
php -v; php -m                       # the PATH's PHP
/opt/plesk/php/8.5/bin/php -v        # a Plesk PHP by its path
php --ini                            # the files the command line PHP reads
```

Vergleichen Sie mit der Webseite auf **Setup > Systeminformationen**, die zuerst sagt, welcher Server geantwortet hat und wie er PHP
ausführt ([Systeminformationen](../../../anleitungen/systeminformationen.md)).

> **Tipp**
> Wenn ein Panel die PHP-Version einer Website ändert, ändern Sie im selben Schritt das PHP der Kommandozeile in den Cron-Zeilen
> dieser Website und in `cronjob.ini [AdminSettings] PhpCliPath` ([Cron und Shell-Zugang](cron-und-shell-zugang.md)).

## Ein Pool pro Website

- Der Pool führt PHP als Benutzer der Website aus ([8.5.2](../../../installation/08-die-website-ausliefern.md#852-php-fpm)).
- Wenn mehrere Kunden auf einer Maschine voneinander auf Ebene des Betriebssystems abgeschottet sein müssen, nutzen Sie einen
  klassischen Server mit einem PHP-FPM-Pool pro Website ([8.1](../../../installation/08-die-website-ausliefern.md#81-die-wahl-in-einer-tabelle)).
- Alles, was `var/` schreibt (Pool, Cron, Konsole, Velocity-Worker), läuft als dieser eine Benutzer
  ([8.7.2](../../../installation/08-die-website-ausliefern.md#872-ein-benutzer-oder-eine-gruppe)).

Wo Pools liegen, laut den Quellen:

| System | Verzeichnis der Pools | Dienst |
|---|---|---|
| Plesk | `/opt/plesk/php/<version>/etc/php-fpm.d/<domain>.conf` | `plesk-php<XY>-fpm` |
| Red-Hat-Familie, mitgeliefertes PHP | `/etc/php-fpm.d` | `php-fpm` |
| Parallele Installationen von Remi | `/etc/opt/remi/php*/php-fpm.d` | `php<XY>-php-fpm` |
| Debian und Ubuntu | `/etc/php/<version>/fpm/pool.d/` | `php<version>-fpm` |

Quellen: [Kapitel 2, Plesk und Debian](../../../installation/02-voraussetzungen.md#29-hinweise-zu-betriebssystemen),
[Engines von Velocity, welches PHP-FPM](../../../../en/us/bc/6.0/velocity-engines.md).

## Pool-Einstellungen für Exponential

Einstellungen, die für Exponential zählen (in der Pool-Datei oder in `php.ini`): `memory_limit` (mindestens 256M; mehr für große
Importe), `max_execution_time`, `upload_max_filesize` und `post_max_size` (Ihr größter Upload), OPcache an
([8.5.2](../../../installation/08-die-website-ausliefern.md#852-php-fpm)).

Kapitel 2 nennt einen minimalen Satz für eine produktive `php.ini` (oder die `php_admin_value`-Zeilen eines PHP-FPM-Pools):

```ini
memory_limit = 256M
max_execution_time = 60
date.timezone = Europe/Berlin
file_uploads = On
upload_max_filesize = 64M
post_max_size = 64M
allow_url_fopen = On
variables_order = "EGPCS"
```

Die Werte über den Mindestwerten sind Vorschläge, keine Anforderungen. Die Untergrenzen des Setup-Assistenten sind `memory_limit` 64M
und `max_execution_time` 30 ([2.5](../../../installation/02-voraussetzungen.md#25-einstellungen-in-der-phpini)).

**Zeitgrenzen für Anfragen.** Neben `max_execution_time` kann ein Pool eine Anfrage nach einer Grenze in Echtzeit beenden
(`request_terminate_timeout`), die `set_time_limit()` von PHP nicht überschreiben kann. Die Notizen zur MongoDB-Portierung
beschreiben, wie der Setup-Assistent dadurch abgebrochen wird und die Grenze nur für die Installation erhöht wird
([MongoDB-Unterstützung im Kernel](../../../../en/us/bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md)). Unter Velocity ist das Gegenstück das
`requestTimeout` der Engine (30 Sekunden; der Client erhält 504); halten Sie `SQLiteTransactionWait` von SQLite (Standard 25) unter
dem Zeitlimit der Anfrage ([9.2](../../../installation/09-datenbanken.md#92-sqlite-3-eine-produktionsdatenbank-in-einer-datei)).

## OPcache

Unter PHP-FPM schalten Sie OPcache in der `php.ini` des Pools ein und lassen die Zeitstempelprüfungen an: Setzen Sie auf einer
Exponential-Website nie `opcache.validate_timestamps=0`; eine Cache-Datei, die nach dem Leeren neu geschrieben wird, würde bis zum
Neustart von PHP in ihrer alten Form ausgeliefert ([10.12 OPcache](../../../installation/10-nach-der-installation.md#opcache)).

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.interned_strings_buffer=32
opcache.validate_timestamps=1
opcache.revalidate_freq=2
```

Velocity setzt seine eigenen OPcache-Optionen in `settings/velocity.ini [PHPSettings] IniOptions[]`; siehe [OPcache](../leistung/opcache.md).

## Das richtige PHP-FPM neu laden

Nach einer Änderung an einer PHP-Klasse, einem Operator oder einer INI-Datei laden Sie den FPM-Master neu, der die Website ausliefert
([8.5.2](../../../installation/08-die-website-ausliefern.md#852-php-fpm)). Auf Maschinen mit mehreren PHP-Versionen ist der Master, der die
Website ausliefert, oft nicht der mit dem Namen `php-fpm`. Finden Sie ihn über seine Pool-Datei.

`exp:velocity deploy` erledigt das für Sie. `[DeploySettings] PhpFpmService=auto` (der Standard in `settings/velocity.ini`) findet in
den vier Pool-Verzeichnissen oben die Pool-Konfiguration, die nach der Hosting-Domain der Installation benannt ist
(`/var/www/vhosts/<domain>/...`, wie Plesk Pools benennt), oder eine, die das Verzeichnis der Installation nennt. Der Schritt ist ein
Neuladen, sodass keine Anfrage verloren geht. Er braucht root ([Engines von Velocity](../../../../en/us/bc/6.0/velocity-engines.md)).

```bash
php bin/php/console exp:velocity deploy --dry-run --allow-root-user                # shows which service it found
php bin/php/console exp:velocity deploy --no-velocity --allow-root-user            # Apache/nginx site: caches + FPM reload
```

Setzen Sie `PhpFpmService` auf den Namen des Dienstes, um ihn festzulegen, oder auf `disabled`, wenn Velocity die Website allein
ausliefert.

## Einschränkungen, die Panels oft setzen

| Einschränkung | Wirkung auf Exponential | Quelle |
|---|---|---|
| `open_basedir` | der Setup-Assistent warnt; ezupdate sucht Composer zuerst innerhalb der Installation | [2.4](../../../installation/02-voraussetzungen.md#24-der-systemtest-des-setup-assistenten), [ezupdate](../../../../en/us/features/6.0/extensions/ezupdate.md) |
| `disable_functions` mit `proc_open`, `exec` oder `posix_kill` | die Seite Cronjobs kann keinen Job starten, die Crontab nicht lesen oder einen Job nicht anhalten; sie sagt, welches davon | [Cronjobs](../../../anleitungen/cronjobs.md#2-bevor-sie-beginnen) |
| `disable_functions` mit `opcache_get_status` | **Systeminformationen** kann keine OPcache-Kennzahlen zeigen; der Cache funktioniert weiter | [Systeminformationen](../../../anleitungen/systeminformationen.md) |
| `variables_order` ohne `E` | der optionale Test meldet, dass PHP keine Umgebungsvariablen registriert | [Kapitel 12](../../../installation/12-fehlerbehebung.md) |

## Checkliste

- [ ] PHP 8.1 oder später angeboten (8.0 nur für Websites, die Velocity nicht nutzen und 6.0.15 oder später betreiben).
- [ ] Dieselbe Version und dieselben Erweiterungen für Web und Kommandozeile; Cron und `PhpCliPath` folgen Änderungen der PHP-Version.
- [ ] Ein Pool pro Website, der als Benutzer der Website läuft.
- [ ] Pool-Einstellungen und OPcache wie oben; `validate_timestamps` an.
- [ ] `exp:velocity deploy --dry-run` nennt den richtigen PHP-FPM-Dienst.
- [ ] Die Einschränkungen des Panels sind mit ihren Wirkungen dokumentiert.

**Demomodus in einem Pool.** Eine öffentliche Demo kann ihre Umgebungsvariable aus ihrem eigenen Pool bekommen: `env[EXP_DEMO_MODE] = 1` (funktioniert mit `clear_env = yes`), dann den Master dieser Version neu laden. Nie in einem Pool, den andere Websites teilen. [Demomodus für Hoster](../sicherheit/demomodus-fuer-hoster.md), [`php-fpm-pool-demo.conf`](../../../../en/us/examples/demo-mode/php-fpm-pool-demo.conf).

## Verwandte Seiten

- [Control Panels: Überblick](README.md), [Plesk](plesk.md)
- [PHP-Versionen und Erweiterungen](../grundlagen/php-versionen-und-erweiterungen.md), [PHP-Einstellungen für Exponential](../grundlagen/php-einstellungen-fuer-exponential.md)
- [Abstimmung von PHP-FPM](../leistung/abstimmung-von-php-fpm.md), [Benutzer und Pools pro Kunde](../sicherheit/benutzer-und-pools-pro-kunde.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
