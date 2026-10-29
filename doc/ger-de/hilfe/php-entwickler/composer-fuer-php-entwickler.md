---
title: Composer für PHP-Entwickler
audience: php-developer, developer
category: php-development
order: 460
summary: Wie Exponential Composer verwendet - die Wurzel-composer.json mit PHP 8.0 bis 8.5 (PHP 7.4 ohne Support nutzbar), benötigte Erweiterungen und Pakete, der Hook legacy-scripts, der Autoloads neu generiert, das Installer-Plugin, das Erweiterungen nach extension/ legt, composer.dev.json für die Entwicklungswerkzeuge, die eigene composer.json einer Erweiterung, Versionseinschränkungen und was Sie mit vendor/ nicht tun sollten.
keywords: Composer, Versionseinschränkung, Tilde, PHP-Einschränkung, Entwicklungswerkzeuge, Erweiterung paketieren, composer.json, Packagist, se7enxweb, exponential-legacy-installer, ezpublish-legacy-extension, require, require-dev, suggest, post-install-cmd, legacy-scripts, composer.dev.json, .devtools, version constraint, tilde, PHP constraint, vendor
type: help
---
# Composer für PHP-Entwickler

Diese Seite richtet sich an PHP-Entwickler, die eine Abhängigkeit hinzufügen, eine Erweiterung paketieren oder die
Wurzel-`composer.json` lesen. Exponential wird mit Composer aus Packagist installiert, ist aber kein PSR-4-Projekt von
Composer: Composer installiert die Erweiterungen und die Bibliotheken, und die eigenen Klassen-Maps des Kernels laden die
Klassen.

## Inhalt

1. [Die Wurzel-composer.json](#die-wurzel-composerjson)
2. [Wie Erweiterungen installiert werden](#wie-erweiterungen-installiert-werden)
3. [Die Entwicklungswerkzeuge](#die-entwicklungswerkzeuge)
4. [Die composer.json einer Erweiterung](#die-composerjson-einer-erweiterung)
5. [Versionseinschränkungen](#versionseinschränkungen)
6. [Regeln](#regeln)

## Die Wurzel-composer.json

| Teil | Enthält |
|---|---|
| `name`, `type` | `se7enxweb/exponential`, Typ `ezpublish-legacy` |
| `license` | `GPL-2.0-or-later` |
| `require` `php` | `^8.0 \|\| ^8.1 \|\| ... \|\| ^8.5` (und vorausschauende Bereiche bis 8.8) |
| `require` `ext-*` | `dom`, `libxml`, `mbstring`, `pcre`, `json`, `iconv`, `reflection`, `session`, `spl`, `simplexml` |
| `require` Pakete | das Installer-Plugin, Velocity (`se7enxweb/exponential-velocity`), die mitgelieferten `se7enxweb/*`-Erweiterungen, die Zeta Components |
| `require-dev` | `phpunit/phpunit` (`^9.6 \|\| ^10.5 \|\| ^11.5 \|\| ^12.0 \|\| ^13.4`) |
| `suggest` | Datenbankerweiterungen (`ext-sqlite3`, `ext-pgsql`, `ext-mysqli`, `mongodb/mongodb`), `ext-gd`, `ext-curl`, optionale Erweiterungen |
| `autoload` | `files: autoload.php`, sodass der Autoloader von Composer den des Kernels lädt |
| `scripts` | `legacy-scripts` führt nach `install` und `update` `@php bin/php/ezpgenerateautoloads.php` aus |

## Wie Erweiterungen installiert werden

Pakete vom Typ `ezpublish-legacy-extension` werden vom Installer-Plugin `se7enxweb/exponential-legacy-installer`, das
`config.allow-plugins` erlaubt, nach `extension/<name>/` gelegt (nicht nach `vendor/`). Nach der Installation generiert
der Hook `legacy-scripts` die Klassen-Maps neu, sodass die Klassen der Erweiterung gefunden werden. Bibliotheken
(`zetacomponents/*`, `mongodb/mongodb`) kommen nach `vendor/` und werden vom eigenen Autoloader von Composer geladen.

## Die Entwicklungswerkzeuge

PHP_CodeSniffer, PHPCompatibility und PHPStan stehen **nicht** im `require-dev` der Wurzel. Sie liegen in
`composer.dev.json`, aufgelöst für PHP 8.0.30 und von `make devtools` nach `.devtools/vendor` installiert
(`COMPOSER=composer.dev.json composer install`), sodass sie das `vendor/` der Site nie berühren
([Git-Hooks und make-Ziele](git-hooks-und-make-ziele.md)).

## Die composer.json einer Erweiterung

```json
{
    "name": "acme/acmereport",
    "description": "Monthly reports for Exponential",
    "type": "ezpublish-legacy-extension",
    "license": "GPL-2.0-or-later",
    "require": {
        "php": "^8.0",
        "se7enxweb/exponential-legacy-installer": "*"
    }
}
```

- `type` sorgt dafür, dass der Installer sie nach `extension/` legt; der Verzeichnisname ist der Teil des Paketnamens
  nach dem `/`, sofern `extra.ezpublish-legacy-extension-name` keinen anderen nennt (`LegacyExtensionInstaller::getInstallPath()`).
- Geben Sie `php` `^8.0` an, sofern Sie nicht mehr brauchen ([Unterstützte PHP-Versionen: 8.0 bis 8.5](unterstuetzte-php-versionen.md)).
- Geben Sie die Bibliotheken an, die Ihr Code verwendet; liefern Sie kein `vendor/` innerhalb der Erweiterung aus.
- Halten Sie die Versionen in `ezinfo.php` und `extension.xml` gleich dem Release
  ([composer.json für Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/composer-json-fuer-erweiterungen.md),
  [Composer-Pakete und Packagist](../ersteller/anwendungsentwickler/composer-und-packagist.md)).

## Versionseinschränkungen

Exponential-Pakete verwenden drei- und vierteilige Versionen (`6.0.15`, `0.0.4.54`). Die Tilde lässt die letzte Stelle
offen:

| Einschränkung | Erlaubt |
|---|---|
| `~6.0.15` | `>=6.0.15 <6.1.0` |
| `~0.0.4.57` | `>=0.0.4.57 <0.0.5.0` |
| `^8.0` | `>=8.0 <9.0` |

Nach `.9` kommt an der letzten Stelle `.10` (`0.0.4.9`, `0.0.4.10`). Packagist vergleicht jede Stelle als Zahl; eine
einfache lexikalische Sortierung von Tags tut das nicht, verwenden Sie also `sort -V` oder
`git tag --sort=version:refname`, wenn ein Skript das neueste Tag auswählt
([Regeln für Versionen](../erweiterungsentwicklung/teil-7-veroeffentlichen/regeln-fuer-versionsnummern.md)).

## Regeln

- **Eine veröffentlichte Version ändert sich nie.** Ein Tag auf Packagist wird einmal gelesen und zwischengespeichert;
  beheben Sie einen Fehler mit der nächsten Version ([Tags sind dauerhaft](../erweiterungsentwicklung/teil-7-veroeffentlichen/tags-sind-dauerhaft.md)).
- **Prüfen Sie Packagist, bevor Sie neu pinnen.** Der PHPUnit-Job der CI löst Abhängigkeiten frisch auf; eine
  Einschränkung, die auf ein Release zeigt, das noch nicht auf Packagist ist, lässt jeden Zweig fehlschlagen.
- **Bearbeiten Sie `vendor/` nicht**, und seien Sie vorsichtig mit `composer update` auf einer installierten Site:
  Manche Installationen halten Erweiterungen als lebendige Git-Checkouts, die Composer ersetzen würde.

## Verwandte Seiten

- [PHP-, PHP-FIG- und Composer-Standards](../../standards/php-und-composer.md)
- [composer.json modernisieren](../erweiterungsentwicklung/teil-4-aktualisieren/composer-json-modernisieren.md)
- [Der Packagist-Webhook](../erweiterungsentwicklung/teil-7-veroeffentlichen/der-packagist-webhook.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
