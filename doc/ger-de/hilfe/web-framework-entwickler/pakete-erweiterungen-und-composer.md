---
title: "Pakete: Erweiterungen, Composer und Bundles"
audience: web-framework-developer, developer
category: web-frameworks
order: 390
summary: Die Erweiterung ist die Code-Einheit von Exponential, vergleichbar mit einem Symfony-Bundle, einem Laravel-Paket, einer Django-App oder einer Rails-Engine - ihre Verzeichnisstruktur, Metadaten, die Aktivierung global oder pro Siteaccess, die Ladereihenfolge, die Installation mit Composer aus Packagist und die Verwendung gewöhnlicher Composer-Bibliotheken darin.
keywords: Erweiterung, Bundle, Paket, Django-App, Rails-Engine, Composer, Packagist, ezpublish-legacy-extension, extension.xml, ezinfo.php, ActiveExtensions, ActiveAccessExtensions, Autoloads, Ladereihenfolge, Web-Framework-Entwickler, extension, package, loading order, web framework developer
type: help
---
# Pakete: Erweiterungen, Composer und Bundles

Diese Seite richtet sich an Entwickler, die ihren Code in Bundles, Paketen, Apps oder Engines strukturieren. In
Exponential ist diese Einheit die **Erweiterung**: ein Verzeichnis unter `extension/<name>/`, das Module, Klassen,
Templates, Einstellungen, Datentypen, Workflow-Ereignistypen, Cronjob-Teile, Befehle, Übersetzungen, Schemadateien und
Tests enthalten kann. Erweiterungen werden als Composer-Pakete verteilt
([Aufbau einer Erweiterung](../ersteller/anwendungsentwickler/aufbau-einer-erweiterung.md)).

## Inhalt

1. [Die Verzeichnisstruktur](#die-verzeichnisstruktur)
2. [Metadaten](#metadaten)
3. [Aktivierung und Reihenfolge](#aktivierung-und-reihenfolge)
4. [Composer](#composer)
5. [Im Vergleich mit anderen Frameworks](#im-vergleich-mit-anderen-frameworks)

## Die Verzeichnisstruktur

| Verzeichnis | Enthält | Registriert durch |
|---|---|---|
| `modules/<module>/` | `module.php`, Views, `function_definition.php` | `module.ini [ModuleSettings] ExtensionRepositories[]`, `ModuleList[]` |
| `classes/` | PHP-Klassen | `php bin/php/ezpgenerateautoloads.php -e` |
| `autoloads/` | Registrierung von Template-Operatoren | `site.ini [TemplateSettings] ExtensionAutoloadPath[]` |
| `design/<design>/` | Templates, Overrides, Stylesheets, Skripte, Bilder | `design.ini [ExtensionSettings] DesignExtensions[]` |
| `settings/` | `*.ini.append.php`; `siteaccess/<name>/` pro Siteaccess | die Aktivierung der Erweiterung |
| `datatypes/`, `eventtypes/event/` | Feldtypen, Workflow-Ereignistypen | `content.ini`, `workflow.ini` |
| `cronjobs/` | Cronjob-Skripte | `cronjob.ini` |
| `bin/php/` | Befehle, gefunden als `ext:<ext>:<name>` | nichts |
| `share/`, `sql/<engine>/` | Schema- und Startdaten | Ihr Installationsbefehl |
| `translations/<locale>/` | Übersetzungen der Oberfläche | `site.ini [RegionalSettings] TranslationExtensions[]` |
| `tests/` | PHPUnit-Tests | Ihre CI |

## Metadaten

| Datei | Inhalt |
|---|---|
| `extension.xml` | `<metadata>` mit Name, Version, Copyright, Lizenz, Info-URL; `<dependencies>` auf andere Erweiterungen |
| `ezinfo.php` | Klasse `<extension>Info` mit einer statischen Methode `info()` (Name, Version, Copyright, License, Info_url) |
| `composer.json` | das Composer-Paket, Typ `ezpublish-legacy-extension`, mit `extra.ezpublish-legacy-extension-name` |

Die Info-Seite (`/ezinfo/about`) liest zuerst `extension.xml`, daher müssen beide Versionsfelder übereinstimmen
([Versionen und Releases](../ersteller/anwendungsentwickler/versionen-und-releases.md)).

## Aktivierung und Reihenfolge

- `site.ini [ExtensionSettings] ActiveExtensions[]` aktiviert eine Erweiterung für alle Siteaccesses;
  `ActiveAccessExtensions[]` in den Einstellungen eines Siteaccess aktiviert sie nur dort.
- Die Reihenfolge ist wichtig: Die Reihenfolge von `ActiveExtensions` entscheidet, welche Einstellungen, Templates und
  Design-Dateien gewinnen, wenn zwei Erweiterungen dasselbe bereitstellen; mit `ExtensionOrdering=enabled` bestimmen
  die deklarierten Abhängigkeiten den Großteil der tatsächlichen Ladereihenfolge
  ([Ladereihenfolge der Erweiterungen](../../funktionen/6.0/ladereihenfolge-der-extensions.md)).
- **Setup > Extensions** zeigt sie an und schaltet sie um ([Leitfaden zu Erweiterungen](../../anleitungen/extensions.md)).
- Nach dem Aktivieren: Autoloads neu erzeugen, Caches leeren, deployen.

## Composer

```bash
composer require yourvendor/expdemo:~1.0.0
php bin/php/ezpgenerateautoloads.php -e
```

Das Paket landet in `extension/<name>`, dem Namen aus `extra.ezpublish-legacy-extension-name`. Versionen stammen aus
Git-Tags; ein veröffentlichter Tag ist dauerhaft
([Composer-Pakete und Packagist](../ersteller/anwendungsentwickler/composer-und-packagist.md)). `autoload.php` lädt den
Autoloader von Composer, sodass gewöhnliche Bibliotheken, die in der `composer.json` Ihrer Erweiterung verlangt
werden, ihren Klassen zur Verfügung stehen.

Inhaltsklassen und Demo-Inhalte werden getrennt als Pakete (`.ezpkg`) transportiert
([Migrationen und Startdaten](migrationen-und-dba-schemadateien.md#startdaten-und-inhalte)).

## Im Vergleich mit anderen Frameworks

| Framework | Einheit | Registriert durch | Exponential |
|---|---|---|---|
| Symfony | Bundle | `config/bundles.php` | `ActiveExtensions[]` |
| Laravel | Paket mit Service Provider | Package Discovery | Composer plus `ActiveExtensions[]` |
| Django | App | `INSTALLED_APPS` | `ActiveExtensions[]` |
| Rails | Engine, Gem | `Gemfile`, `mount` | Composer, `ActiveExtensions[]`, Module |
| Spring | Starter | Auto-Configuration | Einstellungen in der Erweiterung |

Für die Aktivierung pro Siteaccess gibt es in anderen Frameworks keine gängige Entsprechung: Eine Installation kann
eine Erweiterung auf einer Website ausführen und auf einer anderen nicht.

## Verwandte Seiten

- [Ihre erste Erweiterung](../ersteller/anwendungsentwickler/ihre-erste-erweiterung.md)
- [Ihre Anwendung als Erweiterung paketieren](../ersteller/anwendungsentwickler/als-erweiterung-paketieren.md)
- [Erweiterungsentwicklung: das Buch](../erweiterungsentwicklung/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
