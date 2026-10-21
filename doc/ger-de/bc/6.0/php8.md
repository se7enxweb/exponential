# Unterstützung für PHP 8

Lesen Sie diese Seite, wenn Sie von einem Release vor 6.0.0 aktualisieren oder eigene Erweiterungen pflegen. Kernel und
Bibliotheken wurden zwischen Dezember 2023 und April 2026 Schritt für Schritt für PHP 8.0 bis 8.5 angepasst.
Kompatibilitätsfunktionen wurden überall beibehalten, wo sie bekanntermaßen gebraucht werden, sodass eigene
Erweiterungen nicht in fatalen Fehlern enden. Um Deprecation-Warnungen loszuwerden, müssen Sie Ihren eigenen Code
womöglich trotzdem anpassen, meist Klassen, die `eZPersistentObject` oder `eZDataType` erweitern.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | PHP-8-Syntax und -Prüfungen im ganzen Kernel; seit 6.0.8 verlangt Composer PHP 8.1 oder neuer. Seit 6.0.15 läuft der Code wieder unter PHP 8.0, der ältesten unterstützten Version ([Unterstützung für PHP 8.0](php-8.0-unterstuetzung.md)). |
| Wer betroffen ist | Websites, die noch unter PHP 7.4 laufen (müssen bei 6.0.7 bleiben); Autoren von Erweiterungen mit dynamischen Eigenschaften, ungeprüftem `count()` oder `null`, das an String-Funktionen übergeben wird. |
| Wie Sie es prüfen | Schalten Sie auf einer Entwicklungskopie die Debug-Ausgabe ein und suchen Sie nach Hinweisen „Deprecated“ aus den Dateien Ihrer Erweiterung. |
| Wie Sie es beheben | Folgen Sie „Was das für Ihren eigenen Code bedeutet“ unten. |

## Die Abfolge der Änderungen (Dezember 2023 bis April 2026)

Die erste Spalte ist das erste Release, das die Änderung enthält. Daran sehen Sie, welche Ihrer Erweiterungen
womöglich dieselbe Behandlung brauchen.

| Release | PHP-Thema | Was sich geändert hat |
|---|---|---|
| 6.0.0 (Dez. 2023) | 8.3 | Korrekturen für `eZTemplateForFunction` (die Template-Funktion `{for}`) und weitere Inkompatibilitäten mit PHP 8.3; der Setup-Assistent gibt bei der Installation keine PHP-8-Warnungen mehr aus; der Autoload-Code der Installation wurde neu geschrieben, sodass kein Fehlertext erscheint. |
| 6.0.1 (Jan. 2024) | 8.0 | `count()` wird nur noch nach `is_countable()` aufgerufen, an Stellen, die `null` oder Skalare erhielten; uneinheitliches Prüfen von Daten vor der Verwendung behoben. Gefunden beim Testen des [SQLite-Treibers](../../funktionen/6.0/sqlite-datenbank.md). |
| 6.0.4 (Aug. 2024) | 8.2 | Dynamische Eigenschaften von `eZPersistentObject`-Klassen verursachen keine Warnungen mehr in der Debug-Ausgabe. |
| 6.0.11 (Commit vom Dez. 2024, zuerst in 6.0.11) | 8.0 | Der Datentyp `ezmatrix` endet nicht mehr in einem fatalen Fehler (`f433a08cf9`; der Commit liegt auf einem Branch, der erst mit 6.0.11 ein Release erreichte). Eine kleinere Korrektur eines PHP-8-Hinweises vom Dezember 2024 (`21d2c6b4f5`) ist in 6.0.6. |
| 6.0.7 (Jan. 2025) | 8.0 und 8.2 | Die Warnung der Template-Schleife `{for}`, die Erweiterung für Sternbewertungen und Warnungen der PostgreSQL-Bibliothek behoben; das Löschen einer bereits entfernten Datei warnt nicht mehr. |
| 6.0.8 (Apr. 2025) | 8.4 | Die entfernte Konstante `E_STRICT` wird nicht mehr referenziert; erste Unterstützung für 8.4 in zehn Dateien von Kernel und Bibliotheken (`eZSession`, `eZINI`, `eZExtension`, `eZSys`, `eZOrder`, `eZDebug`, `eZContentObject`, der Autoload-Generator und andere, `86abc11907`). **Composer verlangt jetzt PHP 8.1 oder neuer** (`^8.1 \|\| ^8.2`); PHP 7.4 ist nicht mehr installierbar. |
| 6.0.11 (Sep. 2025) | 8.1 und 8.2 | `#[ReturnTypeWillChange]` an Methoden, die SPL-Schnittstellen implementieren (keine Verhaltensänderung); `utf8_decode()` in `ezldapuser` ersetzt (in 8.2 veraltet); öffentliche Eigenschaften an `eZURL` und anderen Klassen deklariert statt dynamischer; `null` wird nicht mehr an `mysqli_real_escape_string()` und `preg_match()` übergeben. |
| 6.0.11 (Dez. 2025) | 8.5 | Nullable-Typen, wo PHP 8.5 sie verlangt; Zeta Components auf Releases angehoben, die PHP 8.5 unterstützen; die README nennt die Unterstützung für PHP 8.5. |
| 6.0.13 (Feb. 2026) | 8.4 | Deprecations des Datentyps Zeit und von `eZOrder::fetchList()` behoben, siehe [die Spezifikation zur Härtung](../../spezifikationen/6.0/sicherheitshaertung-6.0.13.md). |
| 6.0.15 (Okt. 2026) | 8.0 | PHP 8.0 ist die älteste unterstützte Version (RHEL 9): `array_is_list()` wird definiert, wenn es fehlt (`lib/phpcompat.php`), `fputcsv()` unter 8.0 ohne `$eol`, die Funktionen zum HTML-Escaping erhalten die Standard-Flags von PHP 8.1 ausdrücklich, sodass `wash` auch unter 8.0 einfache Anführungszeichen escapt, eine Syntaxprüfung und ein Boot-Test für PHP 8.0 in der CI. Siehe [Unterstützung für PHP 8.0](php-8.0-unterstuetzung.md). |
| 6.0.13 (Apr. 2026) | 8.x | Der Session-Handler: `read()` gibt für eine unbekannte Sitzung eine leere Zeichenkette zurück, `gc()` verwendet `time()` und eine Startzeit, und die globale Variable des aktuellen Siteaccess wird vor der Verwendung geprüft. |

## So prüfen Sie Ihre Installation

```bash
php -v
grep -n '"php"' composer.json
```

Die erste Zeile von `php -v` muss 8.0 oder neuer zeigen, auch wenn die Website unter Exponential Velocity läuft (ab
dessen Release 0.0.4.54; siehe [Unterstützung für PHP 8.0](php-8.0-unterstuetzung.md)). Die Zeile aus `composer.json`
zeigt die PHP-Versionen, die das aktuelle Release akzeptiert.

## Was das für Ihren eigenen Code bedeutet

- **Deklarieren Sie die Eigenschaften, die Ihre Klassen verwenden.** PHP 8.2 erklärt ihr Anlegen zur Laufzeit für
  veraltet.
- **Prüfen Sie, bevor Sie zählen.** Verwenden Sie `is_countable()` oder `is_array()` vor `count()` oder `foreach`.
- **Übergeben Sie kein `null` an String-Funktionen.** Verwenden Sie `$value ?? ''`.
- **Kennzeichnen Sie SPL-Methoden.** Geben Sie Methoden, die `Iterator`, `ArrayAccess` oder `Countable`
  implementieren, das Attribut `#[ReturnTypeWillChange]` oder echte Rückgabetypen.
- **Heben Sie Ihre PHP-Anforderung an.** Setzen Sie die PHP-Anforderung Ihrer eigenen `composer.json` auf `^8.0` oder
  neuer, die älteste Version, die das Produkt seit 6.0.15 wieder unterstützt (6.0.8 bis 6.0.14 verlangten `^8.1`).
  Exponential Velocity läuft ab seinem Release 0.0.4.54 ebenfalls unter 8.0. Websites, die noch unter PHP 7.4
  laufen, müssen bei 6.0.7 bleiben.

Das PHP-Handbuch führt jede inkompatible Änderung pro Version auf:

- [Inkompatible Änderungen in PHP 8.0](https://www.php.net/manual/en/migration80.incompatible.php)
- [Inkompatible Änderungen in PHP 8.1](https://www.php.net/manual/en/migration81.incompatible.php)
- [Inkompatible Änderungen in PHP 8.2](https://www.php.net/manual/en/migration82.incompatible.php)
- [Inkompatible Änderungen in PHP 8.3](https://www.php.net/manual/en/migration83.incompatible.php)

## Verwandte Seiten

- [Sicherheitshärtung](haertung.md)
- [PHPUnit 10](phpunitv10.md), [PHPUnit 13](phpunitv13.md) und [PHPUnit 13 unter PHP 8.4](phpunitv13-fuer-php841.md)
- [Kompatibilitätskorrekturen und Tests von `ezpSessionHandlerDB` für PHP 8](ezpsessionhandlerdb-php8-korrektur-und-tests.md)
- [PHP 8.4 und 8.5 für den älteren Symfony-Stack: die Framework-Forks](../../funktionen/6.0/plattform-php85-framework-forks.md)
- Geschichte: [April 2025](../../geschichte/2025/2025-04.md), [September 2025](../../geschichte/2025/2025-09.md),
  [Dezember 2025](../../geschichte/2025/2025-12.md), [Februar 2026](../../geschichte/2026/2026-02.md),
  [März 2026](../../geschichte/2026/2026-03.md)
