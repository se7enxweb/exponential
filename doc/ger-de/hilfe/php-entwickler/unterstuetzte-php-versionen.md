---
title: "Unterstützte PHP-Versionen: 8.0 bis 8.5"
audience: php-developer, developer
category: php-development
order: 50
summary: Welche PHP-Versionen Exponential 6 unterstützt und wie jede getestet wird - PHP 8.0 als älteste, 8.5 als neueste, die Einschränkung in composer.json, die CI-Jobs je Version, die Unterschiede unter 8.0 und was Sie in Ihrer eigenen Erweiterung angeben.
keywords: unterstützte PHP-Versionen, PHP-Einschränkung, CI-Matrix, PHPUnit-Versionen, PHP 8.0, PHP 8.1, PHP 8.2, PHP 8.3, PHP 8.4, PHP 8.5, supported PHP versions, composer.json, php constraint, RHEL 9, CI matrix, PHPUnit versions, Velocity 0.0.4.54, PHP 7.4
type: help
---
# Unterstützte PHP-Versionen: 8.0 bis 8.5

Diese Seite richtet sich an PHP-Entwickler, die wissen müssen, unter welchen PHP-Versionen ihr Code laufen muss.
Exponential 6 läuft unter PHP 8.0 bis 8.5. Die älteste Version bestimmt, welche Syntax und welche Funktionen Sie
verwenden dürfen; die neueste bestimmt, welche Deprecations Sie vermeiden müssen.

## Inhalt

1. [Der Bereich](#der-bereich)
2. [Warum PHP 8.0 die älteste ist](#warum-php-80-die-älteste-ist)
3. [Wie jede Version getestet wird](#wie-jede-version-getestet-wird)
4. [Was unter PHP 8.0 anders ist](#was-unter-php-80-anders-ist)
5. [Die Geschichte der Anforderung](#die-geschichte-der-anforderung)
6. [Was Sie in Ihrer Erweiterung angeben](#was-sie-in-ihrer-erweiterung-angeben)

## Der Bereich

`composer.json` gibt an:

```json
"php": "^8.0 || ^8.1 || ^8.2 || ^8.3 || ^8.4 || ^8.5 || ^8.6 || ^8.7 || ^8.8"
```

Die Bereiche für 8.6 bis 8.8 sind vorausschauend; diese Versionen gibt es noch nicht, und sie werden nicht getestet
([PHP-, PHP-FIG- und Composer-Standards](../../standards/php-und-composer.md#unterstützte-php-versionen)). Die benötigten
PHP-Erweiterungen sind `dom`, `libxml`, `mbstring`, `pcre`, `json`, `iconv`, `reflection`, `session`, `spl` und
`simplexml`; Datenbank- und Bilderweiterungen stehen unter `suggest`.

Exponential Velocity, der Anwendungsserver, läuft ab seinem Release 0.0.4.54 unter PHP 8.0 bis 8.5 und wird mit
`~0.0.4.57` vorausgesetzt, sodass der Kernel, die mitgelieferten Erweiterungen und der Server ein gemeinsames Minimum
haben ([Velocity ist Teil von Exponential 6.0.15](../velocity-ist-teil-von-exponential-6-0-15.md)).

## Warum PHP 8.0 die älteste ist

PHP 8.0 ist das Standard-PHP von Red Hat Enterprise Linux 9, AlmaLinux 9 und Rocky Linux 9. Es zu unterstützen heißt,
dass eine Site auf diesen Systemen keine PHP-Pakete von Drittanbietern braucht. Der Preis für Entwickler ist eine Regel:
keine Syntax und keine ungeschützten Funktionen im Code, die neuer als 8.0 sind
([Code schreiben, der unter PHP 8.0 läuft](code-der-unter-php-8-0-laeuft.md)).

## Wie jede Version getestet wird

| Prüfung | PHP | Wo |
|---|---|---|
| Syntax jeder PHP-Datei, produktives `composer install --no-dev`, Boot-Smoke-Test (`tests/bin/bootsmoke.php`) | 8.0 | `quality.yml`, Job `php80` |
| PHPCompatibility mit `testVersion` `8.0-` | statisch | `phpcs.xml.dist` (eine Warnung in der CI) |
| PHP-Syntax geänderter Dateien | 8.3 | `quality.yml` |
| PHPUnit ohne Datenbank | 8.1, 8.2, 8.3, 8.4, 8.5 | `phpunit.yml`, mit PHPUnit 10.5 unter 8.1, 11.5 unter 8.2 und 8.3, 13 unter 8.4 und 8.5 |
| PHPUnit mit MySQL 8.0 und PostgreSQL 15 | 8.3 | `phpunit.yml`, Datenbanktests |
| PHPStan (Analyse als `phpVersion: 80100`) | 8.3 | `quality.yml` |
| Mikro-Benchmark | 8.5 | `performance.yml` |

PHPUnit läuft nicht unter PHP 8.0, weil PHPUnit 10 und neuer 8.1 brauchen. Unter 8.0 ist der Code nur durch Syntax,
Installation und Boot abgedeckt, weshalb die Regeln für 8.0 im Review so wichtig sind
([CI und das Nachstellen einer roten Prüfung](ci-und-eine-rote-pruefung-nachstellen.md)).

## Was unter PHP 8.0 anders ist

Der Kernel verhält sich unter jeder Version gleich; einige Funktionen brauchen ein neueres PHP und greifen still auf
einen Ersatz zurück:

| Funktion | Unter PHP 8.0 |
|---|---|
| `array_is_list()` | definiert in `lib/phpcompat.php` |
| `fputcsv()` mit `$eol` | ohne `$eol` geschrieben, Zeilenenden werden danach ersetzt |
| `fsync()` | hinter `function_exists()`; die Daten werden mit `fflush()` geschrieben |
| `memory_reset_peak_usage()` | hinter `function_exists()` |
| `str_increment()`, `str_decrement()` (8.3) | die String-Operatoren geben ihre Eingabe zurück |
| `hash( 'xxh128' )` | der SQL-Abfrage-Cache verwendet md5 |
| AVIF-Bildausgabe unter Velocity | braucht 8.1; die Aushandlung endet bei WebP |

Die vollständige Tabelle mit den Dateien finden Sie unter [Unterstützung für PHP 8.0](../../bc/6.0/php-8.0-unterstuetzung.md).

## Die Geschichte der Anforderung

| Release | Anforderung |
|---|---|
| bis 6.0.7 | PHP 7.4 noch installierbar; Sites unter 7.4 müssen bei 6.0.7 bleiben |
| 6.0.8 (April 2025) | Composer verlangt PHP 8.1 oder neuer; erste Unterstützung für 8.4 |
| 6.0.11 (Dezember 2025) | Unterstützung für PHP 8.5 |
| 6.0.15 | PHP 8.0 wieder als älteste Version unterstützt, in der CI geprüft |

Jeder Schritt ist unter [Unterstützung für PHP 8](../../bc/6.0/php8.md) aufgeführt.

## Was Sie in Ihrer Erweiterung angeben

- Geben Sie in der `composer.json` Ihrer Erweiterung `"php": "^8.0"` (oder eine Liste wie die des Kernels) an, sofern
  Ihre Erweiterung nicht wirklich mehr braucht. Ein höheres Minimum blockiert die Installation mit dem Standard-PHP von
  RHEL 9, und der CI-Job `php80` des Kernels schlägt fehl, wenn ein benötigtes Paket sein Minimum anhebt.
- Wenn Sie eine neuere PHP-Funktion brauchen, sichern Sie sie so ab wie der Kernel, statt das Minimum anzuheben.
- Testen Sie unter der ältesten und der neuesten Version, die Sie angeben.

## Verwandte Seiten

- [Code schreiben, der unter PHP 8.0 läuft](code-der-unter-php-8-0-laeuft.md)
- [Hinweise zur PHP-8-Migration für Ihren Code](hinweise-zur-php-8-migration-fuer-ihren-code.md)
- [composer.json modernisieren](../erweiterungsentwicklung/teil-4-aktualisieren/composer-json-modernisieren.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
