---
title: Abstimmung von PHP-FPM
audience: hosting, admin
category: hosting
order: 20
summary: Die Einstellungen von PHP-FPM, die für Exponential auf einem Hosting-Server zählen - php.ini-Werte pro Pool, Zeitlimits und die Einstellungen, die darunter bleiben müssen, Größe und Teilung von APCu, Sitzungen, den richtigen Master neu laden, ohne andere Websites aufzuhalten, und wie Sie einen Pool durch Messen dimensionieren.
keywords: PHP-FPM, FPM, Pool, php_admin_value, memory_limit, max_execution_time, request_terminate_timeout, process_control_timeout, neu laden, PhpFpmReload, PhpFpmService, APCu, apc.shm_size, ApcuReserve, Sitzungen, session.save_path, OPcache, Dimensionierung, pool, reload, sessions, sizing
type: help
---
# Abstimmung von PHP-FPM

Diese Seite ist für Systemadministratoren bei Hosting-Anbietern, die Exponential unter PHP-FPM betreiben, hinter Apache, nginx oder einem
anderen Webserver. Sie beantwortet: welche PHP-Werte Sie pro Pool setzen, welche Zeitlimits zusammenwirken, wie APCu geteilt wird, wie
Sie neu laden, ohne anderen Kunden zu schaden, und wie Sie einen Pool dimensionieren. Die Syntax eines Pools selbst ist die von PHP;
die Schlüssel stehen in der Referenz von PHP zur [Konfiguration von FPM](https://www.php.net/manual/en/install.fpm.configuration.php).

## Inhalt

1. [Ein Pool pro Website](#ein-pool-pro-website)
2. [PHP-Werte pro Pool](#php-werte-pro-pool)
3. [Zeitlimits, die zusammenpassen müssen](#zeitlimits-die-zusammenpassen-müssen)
4. [APCu](#apcu)
5. [Sitzungen](#sitzungen)
6. [Neu laden, ohne andere Websites aufzuhalten](#neu-laden-ohne-andere-websites-aufzuhalten)
7. [Einen Pool dimensionieren](#einen-pool-dimensionieren)
8. [Checkliste](#checkliste)

## Ein Pool pro Website

Ein Pool führt PHP als Benutzer der Website aus ([Kapitel 8, PHP-FPM](../../../installation/08-die-website-ausliefern.md#852-php-fpm)). Für einen
Hoster ist das die Einheit der Abschottung und der Abstimmung: ein Pool pro Kunden-Website, der als Benutzer dieses Kunden läuft, mit
eigenen PHP-Werten. Panels wie Plesk legen einen Pool pro Domain an (`/opt/plesk/php/<version>/etc/php-fpm.d/<domain>.conf`). Siehe
[Benutzer und Pools pro Kunde](../sicherheit/benutzer-und-pools-pro-kunde.md).

## PHP-Werte pro Pool

Aus [Kapitel 2, php.ini-Einstellungen](../../../installation/02-voraussetzungen.md#25-einstellungen-in-der-phpini) und Kapitel 8; setzen Sie sie in den
`php_admin_value`-Zeilen des Pools oder in der `php.ini` des Pools:

| Einstellung | Wert | Quelle |
|---|---|---|
| `memory_limit` | in der Produktion mindestens 256M (die Untergrenze des Setup-Assistenten ist 64M); mehr für große Importe | Kapitel 2 und 8 |
| `max_execution_time` | mindestens 30 (Assistent); 60 im Produktionsbeispiel von Kapitel 2 | Kapitel 2 |
| `date.timezone` | die Zeitzone der Website | kritischer Test des Assistenten |
| `file_uploads` | `On` | kritischer Test des Assistenten |
| `upload_max_filesize`, `post_max_size` | der größte Upload des Kunden | Kapitel 2 |
| `allow_url_fopen` | `On` | kritischer Test des Assistenten |
| `variables_order` | enthält `E`, zum Beispiel `EGPCS` | optionaler Test des Assistenten |
| `open_basedir` | leer | optionaler Test des Assistenten |
| OPcache | an, Zeitstempel geprüft | [OPcache](opcache.md) |

Das Beispiel aus Kapitel 2:

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

Die Werte über den Mindestwerten sind Vorschläge, keine Anforderungen. Das Web-PHP und das PHP der Kommandozeile lesen verschiedene
Dateien; eine Änderung, die „nicht wirkt“, ist meist eine Änderung an der Datei, die das andere PHP liest
([Kapitel 2](../../../installation/02-voraussetzungen.md#25-einstellungen-in-der-phpini)).

## Zeitlimits, die zusammenpassen müssen

| Grenze | Wo | Hinweise aus den Quellen |
|---|---|---|
| `max_execution_time` | PHP | der eigene Zeitgeber des Skripts |
| `request_terminate_timeout` | der FPM-Pool | vom FPM-Master durchgesetzt, was auch immer das Skript tut; die echte Grenze in Echtzeit unter FPM |
| `SQLiteTransactionWait` | `site.ini [DatabaseSettings]`, Standard 25 s | unter dem Zeitlimit der Anfrage halten und beide gemeinsam erhöhen ([SQLite-Transaktionen](../../../../eng-us/bc/6.0/sqlite-transactions.md)) |
| der Setup-Assistent auf MongoDB | — | der Konfigurationsschritt kann ein bis drei Minuten dauern; erhöhen Sie `request_terminate_timeout` oder nutzen Sie die Konsole ([Datenbankunterstützung](../../datenbankunterstuetzung.md)) |

> **Tipp**
> Lange Jobs gehören auf die Kommandozeile, wo das PHP der Kommandozeile standardmäßig keine Ausführungsgrenze hat: Installationen mit
> `exp:install` oder dem Kickstarter, Importe, Vorwärmen der Caches und Cronjobs. Bemessen Sie Web-Zeitlimits für Seiten.

## APCu

APCu wird vom SQL-Abfrage-Cache im Modus `shared` und von anderen Caches der Installation genutzt.

- Prüfen Sie es: Setup > Systeminformationen, Abschnitt PHP, oder `php -m | grep apcu`, und `php_admin_value[apc.enabled]` des Pools
  ([SQL-Abfrage-Cache, einschalten](../../../../eng-us/bc/6.0/sql-query-cache.md#switching-it-on)).
- APCu wird geteilt: `querycache.ini` sagt, dass APCu ein Speicher für jede Website des PHP-FPM-Pools ist; voll mit gültigen Einträgen
  leert es sich vollständig, und jeder andere Cache und Zähler darin geht verloren. Auf einer Maschine, auf der sich mehrere Kunden
  einen FPM-Master teilen, kann eine Website die Einträge der anderen verdrängen.
- `[QueryCacheSettings] ApcuReserve=25` hält ein Viertel von APCu für die anderen Nutzer des Pools frei; halten Sie es über 0.
- Bemessen Sie `apc.shm_size` für das, was die Websites dort halten. Velocity liefert für seinen eigenen Prozess `apc.shm_size=256M`
  aus, weil sich mit 32 MB der Antwort-Cache und der Abfrage-Cache gegenseitig verdrängten (`settings/velocity.ini`).

## Sitzungen

Der standardmäßige Session-Handler des Kernels lässt PHP die Sitzungen halten (Dateien, wie `session.save_handler` in `php.ini` sagt;
`site.ini [Session] Handler` leer). Der rollenbewusste HTTP-Cache braucht Sitzungen in **Dateien, die jeder Server lesen kann, der die
Website ausliefert**, im selben Verzeichnis ([HTTP-Cache, einschalten](../../../../eng-us/bc/6.0/httpcache.md#switching-it-on)). Wenn Sie Sitzungen
für einen Tarif anderswohin verlegen, speichert der HTTP-Cache nichts für angemeldete Besucher und sagt das in `X-Exp-Cache`.

## Neu laden, ohne andere Websites aufzuhalten

Nach einer Änderung an einer PHP-Klasse, einem Operator oder einer INI-Datei muss der Pool den neuen Code ausführen. Ein Neuladen
beschränkt sich nicht auf eine Website: Der Master bittet jedes Kind jedes Pools, seine Anfrage zu beenden, und startet kein neues Kind,
bis das letzte fertig ist, für bis zu `process_control_timeout` (300 s auf Plesk). Gemessen auf einem Host ließ ein offener Ereignisstrom
von 25 s die nächste Seite nach einem Neuladen 22,4 s warten
([Engines von Velocity](../../../../eng-us/bc/6.0/velocity-engines.md#deploying-a-php-change-expvelocity-deploy)).

Deshalb lädt `exp:velocity deploy` (auf Websites nur mit Apache mit `--no-velocity` nutzbar) nur neu, wenn es muss:

| `[DeploySettings] PhpFpmReload` | Verhalten |
|---|---|
| `auto` (mitgeliefert) | wenn das OPcache des Pools Dateien neu prüft (`validate_timestamps` an, `revalidate_freq` höchstens 60 s), so lange warten, statt neu zu laden |
| `always` | jedes Mal neu laden |
| `never` | weder neu laden noch warten |

Erzwingen Sie ein Neuladen (`deploy --fpm-reload`) nach einer Änderung an `php.ini` oder am Pool, einer neuen PHP-Erweiterung oder an
Ort und Stelle entfernten oder umbenannten PHP-Dateien. `[DeploySettings] PhpFpmService=auto` findet den richtigen Master:
`plesk-php<XY>-fpm` von Plesk, `php-fpm` oder `php<X.Y>-fpm` der Distribution, `php<XY>-php-fpm` von Remi.

```bash
php bin/php/console exp:velocity deploy --no-velocity --dry-run --allow-root-user   # which FPM, reload or wait
```

## Einen Pool dimensionieren

Die Quellen nennen keine Anzahl von Kindern für PHP-FPM; sie geben eine Methode und einiges gemessene Verhalten:

- Eine gerenderte Seite kostet sowohl PHP- als auch Datenbank-CPU. Auf der Testinstallation von FrankenPHP (12 Kerne) waren es pro Seite
  etwa 0,25 s PHP- und 0,25 s Datenbank-CPU, und mehr PHP-Threads als etwa doppelt so viele wie Kerne konkurrierten nur miteinander
  ([FrankenPHP, Threads](../../../../eng-us/bc/6.0/frankenphp.md#threads-classic-mode)). Dieselbe Überlegung gilt für einen Pool von FPM-Kindern;
  bestätigen Sie sie auf Ihren eigenen Websites.
- Zwischengespeicherte Seiten kosten weit weniger: Der HTTP-Cache maß unter Apache 508 anonyme Seiten pro Sekunde gegenüber 28 gerenderten
  ([HTTP-Cache](../../../../eng-us/bc/6.0/httpcache.md#measured-on-a-reference-installation-2026-09-26-one-content-page-8-concurrent)). Wie viel
  einer Website aus Caches ausgeliefert wird, entscheidet, wie viele Kinder sie braucht.
- Messen Sie mit `exp:benchmark --cold` bei steigender Gleichzeitigkeit und wählen Sie den Punkt, an dem p90 zu steigen beginnt
  ([Benchmark einer gehosteten Website](benchmark-einer-gehosteten-website.md)).

## Checkliste

- [ ] Ein Pool pro Website, als Benutzer der Website.
- [ ] `memory_limit`, `max_execution_time`, Upload-Grenzen und `date.timezone` im Pool gesetzt.
- [ ] `request_terminate_timeout` mit `SQLiteTransactionWait` und der längsten Web-Anfrage, die Sie erlauben, abgestimmt.
- [ ] APCu eingeschaltet und bemessen; `ApcuReserve` über 0, wo sich Websites einen Master teilen.
- [ ] OPcache an mit Zeitstempelprüfungen ([OPcache](opcache.md)).
- [ ] Ausrollen nutzt `exp:velocity deploy` (oder dieselbe Reihenfolge von Hand) und lädt den richtigen Master neu.

## Verwandte Seiten

- [OPcache](opcache.md)
- [Apache mit PHP-FPM](../server/apache-mit-php-fpm.md)
- [PHP-Einstellungen für Exponential](../grundlagen/php-einstellungen-fuer-exponential.md)
- [PHP-Auswahl und FPM-Pools](../control-panels/php-auswahl-und-fpm-pools.md)
- [SQL-Abfrage-Cache](../../../../eng-us/bc/6.0/sql-query-cache.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
