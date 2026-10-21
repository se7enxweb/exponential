---
title: PHP-Einstellungen für Exponential
audience: hosting, admin
category: hosting
order: 50
summary: Die php.ini-Werte, die der Installer von Exponential prüft, ein Produktionssatz für einen PHP-FPM-Pool, Upload-Grenzen, Zeitzone, OPcache, Fehleranzeige, die PHP-Funktionen, die manche Funktionen brauchen, und woher Velocity und FrankenPHP ihre PHP-Einstellungen nehmen.
keywords: php.ini, PHP-Einstellungen, memory_limit, max_execution_time, date.timezone, file_uploads, upload_max_filesize, post_max_size, allow_url_fopen, variables_order, open_basedir, disable_functions, display_errors, expose_php, OPcache, request_terminate_timeout, php_admin_value, PHP-FPM-Pool, Velocity IniOptions, FrankenPHP, PHP settings, PHP-FPM pool
type: help
---
# PHP-Einstellungen für Exponential

Diese Seite ist für Systemadministratoren, die PHP für Exponential-Websites konfigurieren, auf einem geteilten Server,
einem Panel oder einer dedizierten Maschine. Sie beantwortet: auf welchen `php.ini`-Werten der Installer besteht, was
ein Produktions-Pool setzen sollte, welche PHP-Funktionen Funktionen von Exponential aufrufen (und was nicht mehr
funktioniert, wenn ein Hoster sie deaktiviert) und wo die Einstellungen liegen, wenn Velocity oder FrankenPHP statt
PHP-FPM PHP ausführt.

![Setup, INI-Einstellungen: die INI-Einstellungen eines Siteaccess lesen und woher jeder Wert kommt](../../../../eng-us/help/images/hosting/ini-settings.webp)

## Inhalt

1. [Was der Installer prüft](#was-der-installer-prüft)
2. [Ein Produktionssatz für einen Pool](#ein-produktionssatz-für-einen-pool)
3. [Zeitzone](#zeitzone)
4. [Fehleranzeige](#fehleranzeige)
5. [OPcache unter PHP-FPM](#opcache-unter-php-fpm)
6. [Funktionen, die manche Funktionen aufrufen](#funktionen-die-manche-funktionen-aufrufen)
7. [Zeitlimits](#zeitlimits)
8. [Velocity und FrankenPHP](#velocity-und-frankenphp)
9. [Eine Änderung anwenden](#eine-änderung-anwenden)
10. [Checkliste](#checkliste)
11. [Verwandte Seiten](#verwandte-seiten)

## Was der Installer prüft

Aus [Kapitel 2.5](../../../installation/02-voraussetzungen.md#25-einstellungen-in-der-phpini) und `settings/setup.ini`:

| Einstellung | Wert | Geprüft von |
|---|---|---|
| `memory_limit` | mindestens `64M` (`-1` besteht) | kritischer Test `memory_limit`, `[memory_limit] MinMemoryLimit=64M` |
| `max_execution_time` | mindestens `30` (`0` besteht) | kritischer Test `execution_time`, `[execution_time] MinExecutionTime=30` |
| `date.timezone` | gesetzt | kritischer Test `timezone` |
| `file_uploads` | `On`, und das Upload-Verzeichnis existiert und ist beschreibbar | kritischer Test `file_upload` |
| `upload_tmp_dir` | ein beschreibbares Verzeichnis oder leer | wenn leer, prüft der Test `TMPDIR` oder `/tmp` |
| `allow_url_fopen` | `On` | kritischer Test `allow_url_fopen` |
| `variables_order` | enthält `E`, zum Beispiel `EGPCS` | optionaler Test `variables_order` |
| `open_basedir` | leer | optionaler Test `open_basedir` (nur eine Warnung) |
| `upload_max_filesize`, `post_max_size` | so groß wie die Dateien, die Redakteure hochladen | nicht getestet; PHP lehnt größere Uploads ab |

Die Untergrenze von 64M ist, was der Installer braucht, nicht was eine laufende Website haben sollte. Die
Zustandsprüfung der Administration unter **Setup > Systeminformationen** meldet ein `memory_limit` unter 128M als Fehler
und unter 256M als Warnung, und ein `max_execution_time` unter 30 Sekunden auf einem Webserver
([Systeminformationen, Zustandsprüfungen](../../../anleitungen/systeminformationen.md#3-zustandsprüfungen)).

## Ein Produktionssatz für einen Pool

Der minimale Produktionssatz aus [Kapitel 2](../../../installation/02-voraussetzungen.md#25-einstellungen-in-der-phpini), als
`php_admin_value`-Zeilen eines PHP-FPM-Pools oder in `php.ini`:

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

Die Werte über den Mindestwerten sind Vorschläge, keine Anforderungen: Bemessen Sie sie für die größte Datei, die ein
Kunde hochlädt, und den längsten Import, der über den Browser läuft. Ein Tarif kann seine Upload-Grenze als eine seiner
Eigenschaften nennen.

> **Hinweis**
> `open_basedir` ist auf Shared Hosting verbreitet. Der Installer warnt nur davor (die Einstellung könne wegen Fehlern in einigen PHP-Versionen
> Probleme beim Betrieb von Exponential verursachen). Wenn Sie es setzen, schließen Sie das
> Installationsverzeichnis, das temporäre Upload-Verzeichnis und das Verzeichnis von `[FileSettings] TemporaryDir`
> (mitgeliefert `/tmp/`) ein.

## Zeitzone

`index.php` setzt UTC, wenn `php.ini` kein `date.timezone` hat; nur in diesem Fall schlägt der Test des Installers
fehl. Eine Website kann auch ihre eigene Zone in `settings/site.ini`, `[TimeZoneSettings] TimeZone`, setzen, die der
Kernel bei jeder Anfrage anwendet. Für einen Hoster genügt es, `date.timezone` in jedem Pool auf die Zone des Servers
(oder UTC) zu setzen; Kunden setzen ihre eigene in den Einstellungen der Website.

## Fehleranzeige

Aus [Kapitel 13.4](../../../installation/13-sicherheitshaertung.md#134-debug-ausgabe-und-fehleranzeige): Setzen Sie in der
`php.ini` von PHP-FPM

```ini
display_errors = Off
log_errors = On
expose_php = Off
```

Die Seite Systeminformationen kennzeichnet `display_errors` als Zustandsproblem. Die eigene Debug-Ausgabe von
Exponential ist ein getrennter Schalter, `site.ini [DebugSettings] DebugOutput`, der in der Produktion ebenfalls aus
sein muss.

## OPcache unter PHP-FPM

Aus [Kapitel 10.12](../../../installation/10-nach-der-installation.md#opcache):

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.interned_strings_buffer=32
; keep timestamp checks on: Exponential rewrites PHP cache files (INI caches,
; compiled templates, expiry.php) under the same name
opcache.validate_timestamps=1
opcache.revalidate_freq=2
```

> **Warnung**
> Setzen Sie auf einer Exponential-Website nie `opcache.validate_timestamps=0`. Eine Cache-Datei, die nach dem Leeren neu
> geschrieben wird, würde bis zum Neustart von PHP in ihrer alten Form ausgeliefert. Hoster, die Zeitstempelprüfungen
> der Geschwindigkeit wegen global abschalten, müssen Exponential-Pools ausnehmen. Mehr in [OPcache](../leistung/opcache.md).

## Funktionen, die manche Funktionen aufrufen

Hoster führen oft Funktionen in `disable_functions` auf. Diese Funktionen von Exponential rufen PHP-Funktionen auf, die
häufig auf solchen Listen stehen; die Website läuft ohne sie weiter, aber die Funktion arbeitet nicht:

| Funktion | Genutzt von | Ohne sie | Quelle |
|---|---|---|---|
| `system` | dem ImageMagick-Konverter (`eZImageShellFactory`) | ImageMagick-Konvertierungen schlagen fehl; GD funktioniert weiter | `lib/ezimage/classes/ezimageshellhandler.php`, `settings/image.ini [ImageMagick]` |
| `proc_open` | **Setup > Cronjobs**: einen Job aus der Administration starten | die Seite sagt es; führen Sie Cron aus einer Shell aus | [Anleitung zu Cronjobs](../../../anleitungen/cronjobs.md) |
| `exec` | **Setup > Cronjobs**: die Crontab lesen | die Seite sagt, welche Funktion fehlt | [Anleitung zu Cronjobs](../../../anleitungen/cronjobs.md) |
| `posix_kill` | **Setup > Cronjobs**: **Laufenden Job anhalten** | die Schaltfläche kann keinen Job anhalten | [Anleitung zu Cronjobs](../../../anleitungen/cronjobs.md) |
| `opcache_get_status` | **Setup > Systeminformationen**: OPcache-Kennzahlen | der Cache funktioniert; nur seine Kennzahlen werden nicht gezeigt | [Systeminformationen](../../../anleitungen/systeminformationen.md) |
| `pcntl_fork` | asynchrones Veröffentlichen (`bin/php/ezasynchronouspublisher.php`) | asynchrones Veröffentlichen ist nicht verfügbar | `composer.json` schlägt `ext-pcntl` vor |

Ein PHP der Kommandozeile für Cron und Velocity hat meist eine eigene `php.ini` ohne eine solche Liste; prüfen Sie mit
`php --ini`.

## Zeitlimits

- `max_execution_time` begrenzt die eigene CPU-Zeit von PHP; unter PHP-FPM ist die harte Grenze einer Anfrage das
  `request_terminate_timeout` des Pools (aus, wenn nicht gesetzt) ([Kapitel 9.2](../../../installation/09-datenbanken.md#92-sqlite-3-eine-produktionsdatenbank-in-einer-datei)).
- Halten Sie mit SQLite `[DatabaseSettings] SQLiteTransactionWait` (mitgeliefert 25 Sekunden) unter dem Zeitlimit der
  Anfrage, sonst wird ein wartender Schreiber beendet, bevor der Treiber berichten kann ([SQLite für Hoster](sqlite-fuer-hoster.md)).
- Der Konfigurationsschritt des Setup-Assistenten kann auf MongoDB ein bis drei Minuten dauern; erhöhen Sie
  `request_terminate_timeout` für diese Installation oder nutzen Sie die Konsole ([MongoDB für Hoster](mongodb-fuer-hoster.md)).
- Sitzungen: `site.ini [Session] SessionTimeout` (mitgeliefert `259200` Sekunden, drei Tage) überschreibt
  `session.gc_maxlifetime` beim eigenen Sitzungs-Handler von PHP.

## Velocity und FrankenPHP

**Velocity** führt PHP als Kommandozeilenprozesse aus und übergibt seine eigenen Optionen aus `settings/velocity.ini`,
`[PHPSettings] IniOptions[]`. Die mitgelieferte Liste schaltet OPcache für die Kommandozeile ein
(`opcache.enable_cli=1`), setzt `memory_consumption=256`, `max_accelerated_files=20000`, `interned_strings_buffer=32`,
`opcache.revalidate_freq=0`, `opcache.file_update_protection=0` und `apc.shm_size=256M`. Die beiden OPcache-Werte
weichen absichtlich von PHP-FPM ab: Ein Velocity-Worker ist eine lange Anfrage, und mit den Werten von PHP-FPM würde er
weiter veraltete Cache-Dateien ausliefern ([Kapitel 10.12](../../../installation/10-nach-der-installation.md#opcache)). Schreiben
Sie Änderungen in `settings/override/velocity.ini.append.php`, nie in die mitgelieferte Datei.

**FrankenPHP** liest keine `php.ini` der Maschine. `settings/velocity.ini`, `[FrankenPHPSettings] IniOptions[]` liefert
`display_errors=Off`, `log_errors=On`, `memory_limit=512M`, `opcache.enable=1`, `post_max_size=64M` und
`upload_max_filesize=32M` mit; `PHPIniFile` nennt stattdessen eine eigene `php.ini`
([Kapitel 8.4](../../../installation/08-die-website-ausliefern.md#84-frankenphp)).

## Eine Änderung anwenden

Nachdem Sie `php.ini` oder einen Pool bearbeitet haben, laden Sie neu, was PHP ausführt: PHP-FPM, Apache mit mod_php
oder Velocity (`php bin/php/console exp:velocity restart`). `php --ini` zeigt, welche Dateien das PHP der Kommandozeile
liest; das Web-PHP liest vielleicht andere. Eine Änderung, die „nicht wirkt“, ist meist eine Änderung an der Datei, die
das andere PHP liest.

## Checkliste

- [ ] `memory_limit` 256M oder mehr für Produktions-Pools (64M ist nur die Untergrenze des Installers).
- [ ] `max_execution_time`, `date.timezone`, `file_uploads`, `allow_url_fopen` und `variables_order` gesetzt.
- [ ] Upload-Grenzen im Tarif genannt und im Pool gesetzt.
- [ ] `display_errors=Off`, `log_errors=On`, `expose_php=Off`.
- [ ] OPcache an, mit eingeschalteten Zeitstempelprüfungen.
- [ ] `disable_functions` anhand der Tabelle oben geprüft.
- [ ] Einstellungen von Velocity und FrankenPHP nur in `settings/override/velocity.ini.append.php` geändert.

## Verwandte Seiten

- [PHP-Versionen und Erweiterungen](php-versionen-und-erweiterungen.md)
- [Abstimmung von PHP-FPM](../leistung/abstimmung-von-php-fpm.md) und [OPcache](../leistung/opcache.md)
- [PHP-Auswahl und FPM-Pools](../control-panels/php-auswahl-und-fpm-pools.md)
- [Kapitel 2.5: php.ini-Einstellungen](../../../installation/02-voraussetzungen.md#25-einstellungen-in-der-phpini)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
