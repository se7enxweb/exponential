---
title: Anforderungen auf einen Blick
audience: hosting, admin
category: hosting
order: 30
summary: Jede Anforderung von Exponential 6.0.x auf einer Seite, mit dem Minimum, der Empfehlung und dem, was sie prüft - PHP, Erweiterungen, php.ini, Datenbanken in der offiziellen Reihenfolge, Composer, Webserver, Bildkonvertierung, beschreibbare Verzeichnisse und Zugang zur Kommandozeile.
keywords: Anforderungen, Systemanforderungen, Minimum, PHP 8.0, PHP 8.1, PHP 8.5, Erweiterungen, php.ini, memory_limit, Datenbank, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, Composer, Webserver, Velocity, ImageMagick, GD, Systemprüfung, Hosting, requirements, system requirements, extensions, database, web server, system check, hosting
featured: true
type: help
---
# Anforderungen auf einen Blick

> **Unterstützt: PHP 8.0 bis 8.5. Ohne Support nutzbar: PHP 7.4 (Exponential 6.0.15 und neuer).** [Warum Exponential PHP 7.4 unterstützt](../../warum-exponential-php-7-4-unterstuetzt.md)

Diese Seite ist für Systemadministratoren und Support-Mitarbeitende bei einem Hosting-Anbieter, die die Anforderungen von
Exponential 6.0.x an einer Stelle brauchen, und für Produktmanager, die prüfen müssen, ob ein Tarif es ausführen kann.
Sie beantwortet: was ein Server haben muss, was nur empfohlen ist und welcher Teil der Software jedes Element prüft. Das
vollständige Kapitel mit jeder Begründung ist [Kapitel 2 des Installationsbuchs, Anforderungen](../../../installation/02-voraussetzungen.md);
diese Seite ist seine Zusammenfassung für Hoster.

## Inhalt

1. [Die Tabelle](#die-tabelle)
2. [Erforderliche PHP-Erweiterungen](#erforderliche-php-erweiterungen)
3. [Datenbanken](#datenbanken)
4. [Was der Installer für Sie prüft](#was-der-installer-für-sie-prüft)
5. [Was keine Anforderung ist](#was-keine-anforderung-ist)
6. [Einen Server in zwei Minuten prüfen](#einen-server-in-zwei-minuten-prüfen)
7. [Checkliste](#checkliste)
8. [Verwandte Seiten](#verwandte-seiten)

## Die Tabelle

| Bedarf | Minimum | Empfohlen | Geprüft von |
|---|---|---|---|
| PHP | 8.0 unterstützt, einschließlich Layouts (7.4 ohne Support nutzbar) | das neueste 8.x, das das System anbietet; 8.5 ist das neueste getestete | Composer (`composer.json` `require.php`) und der Setup-Assistent (`settings/setup.ini`, `[phpversion] MinimumVersion=7.4.0`) |
| PHP für Exponential Velocity | 8.0 mit `sockets` (8.1 vor Release 0.0.4.54) | zusätzlich mit `pcntl` und `openssl` | Composer (das Velocity-Paket verlangt `"php": ">=8.0"` und `ext-sockets`) |
| PHP-Erweiterungen | siehe [unten](#erforderliche-php-erweiterungen) | zusätzlich `curl`, `gd`, `openssl`, `pcntl`, `opcache` | Composer (`ext-*`) und die kritischen Tests des Setup-Assistenten |
| `php.ini` | `memory_limit` 64M, `max_execution_time` 30, `date.timezone` gesetzt, `file_uploads` an, `allow_url_fopen` an | mehr Speicher für Importe und Bildarbeit; Upload-Grenzen für Ihre Kunden bemessen | die kritischen Tests des Setup-Assistenten |
| Datenbank | eine der sechs, SQLite 3 braucht keinen Server | in der offiziellen Reihenfolge: SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | der Datenbankschritt des Installers |
| Composer | 2.x | ein aktuelles 2.x (2.2 oder später liest `config.allow-plugins`) | Sie |
| Webserver | keiner (Velocity oder der eingebaute Server von PHP) | Velocity, oder Apache oder nginx mit PHP-FPM | Sie |
| Bildkonvertierung | GD (`imagegd2()`) oder `convert` von ImageMagick | beides | kritischer Test `image_conversion` |
| 64-Bit-PHP | empfohlen | 64 Bit | `composer.json` schlägt `php-64bit` für die Unterstützung von mehr als 30 Sprachen vor |
| Beschreibbare Verzeichnisse | `var/`, `settings/` und die anderen, die der Assistent auflistet | ein Benutzer oder eine Gruppe für jeden Prozess | kritischer Test `directory_permissions` |
| Kommandozeile | eine Shell mit demselben PHP wie im Web | ja, für Composer, Cron und die Konsole | Sie |

Quellen: [Kapitel 2, die Checkliste](../../../installation/02-voraussetzungen.md#21-die-checkliste) und `composer.json`.

> **Hinweis**
> Die Unterstützung für PHP 8.0 ist neu in der Linie 6.0.15. Die veröffentlichten Tags `v6.0.8` bis `v6.0.14` verlangen
> in ihrer `composer.json` PHP 8.1; ein Server mit PHP 8.0 installiert daher die Linie 6.0.15
> ([Kapitel 3](../../../installation/03-den-code-beschaffen.md#31-versionen-tags-und-branches)).

## Erforderliche PHP-Erweiterungen

| Erweiterung | Warum |
|---|---|
| `dom`, `libxml`, `simplexml`, `xsl` | XML: Rich Text, Pakete, Werkzeuge für Einstellungen; `xsl` ist ein kritischer Test |
| `mbstring`, `iconv`, `intl` | Text in jeder Sprache; alle drei sind kritische Tests |
| `pcre`, `json`, `reflection`, `spl`, `session` | Teil von PHP; von `composer.json` verlangt |
| `zlib` | gzip für Pakete und Dateien; kritischer Test |
| eine Datenbankerweiterung | `sqlite3`, `pgsql`, `mongodb`, `mysqli` oder `oci8` |
| `gd` oder ImageMagick | Bildvarianten |

Die vollständige Liste, die empfohlenen Erweiterungen und die Paketnamen je System stehen auf
[PHP-Versionen und Erweiterungen](php-versionen-und-erweiterungen.md).

## Datenbanken

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Präferenz:** 1. SQLite 3 (der Standard) ·
> 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit bekanntermaßen mit
> zusätzlicher Arbeit möglich (kein Treiber in 6.0.x).

| Datenbank | PHP-Erweiterung | Praktisches Minimum |
|---|---|---|
| 1. SQLite 3 | `sqlite3` | Bibliothek 3.33 |
| 2. PostgreSQL | `pgsql` | 13 |
| 3. MongoDB | `mongodb` und die Bibliothek `mongodb/mongodb` | Server 6.0, PHP 8.2 |
| 4. MariaDB | `mysqli` | ein Release, das der Hersteller noch unterstützt |
| 5. Oracle | `oci8` | 19c |
| 6. MySQL | `mysqli` | ein Release, das der Hersteller noch unterstützt |

Die praktischen Mindestwerte stammen aus [Datenbankunterstützung](../../datenbankunterstuetzung.md#versionen-am-code-überprüft);
der Setup-Assistent akzeptiert ältere Untergrenzen, die kein Versprechen sind. Details je Engine stehen auf
[Datenbanken in der offiziellen Reihenfolge](datenbanken-in-der-offiziellen-reihenfolge.md).

## Was der Installer für Sie prüft

Die Seite **Systemtest** des Setup-Assistenten führt die kritischen Tests aus `settings/site.ini`, `[SetupSettings]
CriticalTests`, mit ihren Parametern aus `settings/setup.ini` aus. Der Kickstarter und `exp:install` führen dieselben
Tests in ihrem Schritt `SystemCheck` aus. Ein Fehler hält die Installation an und gibt Ursache und Abhilfe aus, bei
Berechtigungen die genauen Befehle `chmod` und `chown` ([Kapitel 2.4](../../../installation/02-voraussetzungen.md#24-der-systemtest-des-setup-assistenten)).

| Kritischer Test | Prüft |
|---|---|
| `directory_permissions` | die Verzeichnisse in `[directory_permissions] CheckList` sind beschreibbar |
| `phpversion` | PHP mindestens 8.0 |
| `database_extensions` | mindestens eine Datenbankerweiterung |
| `image_conversion` | GD oder ImageMagick |
| `memory_limit` | mindestens `64M` (oder `-1`) |
| `execution_time` | mindestens `30` (oder `0`) |
| `allow_url_fopen` | an |
| `php_session`, `file_upload`, `timezone` | Sitzungen, Uploads, eine Zeitzone |
| `zlib_extension`, `dom_extension`, `iconv_extension`, `mbstring_extension`, `intl_extension`, `xsl_extension` | die Erweiterung ist geladen |
| `ezcversion` | die Zeta Components sind installiert (`composer install` lief) |

> **Warnung**
> Der Verzeichnistest läuft als der Benutzer, der die Prüfung ausführt: im Browser der Benutzer des Webservers, auf der
> Kommandozeile, wer immer die Installation startet. Eine Installation über die Kommandozeile durch einen anderen Benutzer
> kann bestehen und trotzdem Dateien hinterlassen, die der Webserver nicht schreiben kann. Siehe
> [Beschreibbare Verzeichnisse und Berechtigungen](beschreibbare-verzeichnisse-und-berechtigungen.md).

## Was keine Anforderung ist

- **Ein bestimmtes Betriebssystem.** Exponential läuft überall, wo PHP 8 läuft; siehe
  [Betriebssysteme für Hoster](betriebssysteme-fuer-hoster.md).
- **Ein Datenbankserver.** SQLite 3, der Standard, ist eine Datei unter `var/storage/sqlite3/`.
- **Ein getrennter Webserver**, wenn Velocity die Website ausliefert.
- **Solr, Valkey oder Redis.** Sie sind optionale Erweiterungen für Suche und Caching
  ([Such-Backends](such-backends.md), [Valkey und Redis](../leistung/valkey-und-redis.md)).

## Einen Server in zwei Minuten prüfen

```bash
php -v                          # 8.0 or later, for Velocity too
php -m                          # the extensions listed above
php -i | grep "Integer Size"    # 64 bits recommended
composer --version              # Composer 2, and the PHP it runs with
```

Vergleichen Sie `php -v` und `php -m` mit den PHP-Informationen des PHP des Webservers: Auf Servern mit mehreren
PHP-Versionen unterscheiden sie sich oft. Mit installiertem Velocity-Paket vergleicht `php bin/php/console exp:velocity ext check`
die Maschine mit der Erweiterungsausstattung, die Velocity erwartet, und `exp:velocity ext install-hint <extension>` gibt
den Paketbefehl für das System aus ([Kapitel 2.3](../../../installation/02-voraussetzungen.md#herausfinden-was-fehlt)).

## Checkliste

- [ ] `php -v` zeigt 8.0 oder später sowohl für das Web-PHP als auch für das PHP der Kommandozeile (einschließlich Velocity).
- [ ] Jede erforderliche Erweiterung ist in beiden geladen.
- [ ] `memory_limit`, `max_execution_time`, `date.timezone`, `file_uploads` und `allow_url_fopen` gesetzt.
- [ ] Eine Datenbankerweiterung für die Datenbank, die der Tarif anbietet.
- [ ] GD oder ImageMagick installiert.
- [ ] Composer 2 für den Benutzer der Website verfügbar.
- [ ] Die beschreibbaren Verzeichnisse gehören dem Benutzer, der PHP für die Website ausführt.

## Verwandte Seiten

- [Grundlagen des Hostings](README.md)
- [PHP-Versionen und Erweiterungen](php-versionen-und-erweiterungen.md) und [PHP-Einstellungen für Exponential](php-einstellungen-fuer-exponential.md)
- [Datenbanken in der offiziellen Reihenfolge](datenbanken-in-der-offiziellen-reihenfolge.md)
- [Checkliste für einen neuen Server](../checklisten/checkliste-fuer-einen-neuen-server.md)
- [Kapitel 2: Anforderungen](../../../installation/02-voraussetzungen.md)
- [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md) und [Datenbankunterstützung](../../datenbankunterstuetzung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
