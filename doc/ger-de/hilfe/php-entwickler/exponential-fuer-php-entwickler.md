---
title: Exponential für PHP-Entwickler
audience: php-developer, developer
category: php-development
order: 10
summary: Was einem PHP-Entwickler in Exponential begegnet - eine große, ausgereifte PHP-Codebasis mit eigenen Konventionen, generierten Klassen-Maps, geschichteten INI-Einstellungen, persistenten Objekten und einer Template-Engine - und wie sie sich von einem Framework unterscheidet, das Sie vielleicht kennen.
keywords: PHP-Entwickler, Übersicht, Kernel, Erweiterung, Konventionen, Klassen-Map, Template-Engine, gewachsene Codebasis, PHP developer, overview, kernel, lib, extension, conventions, class map, eZINI, eZDB, eZPersistentObject, template engine, legacy code base, PHP 8
type: help
---
# Exponential für PHP-Entwickler

Diese Seite richtet sich an PHP-Entwickler, die neu in der Codebasis von Exponential sind. Sie erklärt, welche Art von
PHP-Anwendung Exponential ist, welchen Konventionen Sie in der ersten Stunde begegnen und wo sie sich von den Frameworks
unterscheiden, die die meisten PHP-Entwickler heute kennen. Die ausführlichen Seiten dieses Abschnitts bauen darauf auf.

## Inhalt

1. [Was die Codebasis ist](#was-die-codebasis-ist)
2. [Fünf Dinge, die PHP-Entwickler überraschen](#fünf-dinge-die-php-entwickler-überraschen)
3. [Die Bausteine, die Sie schreiben werden](#die-bausteine-die-sie-schreiben-werden)
4. [Was das Projekt bei jeder Änderung prüft](#was-das-projekt-bei-jeder-änderung-prüft)
5. [Wie es weitergeht](#wie-es-weitergeht)

## Was die Codebasis ist

Exponential ist ein quelloffenes Content-Management-System und PHP-Anwendungs-Framework, lizenziert unter der GNU GPL,
Version 2 oder später (`composer.json`, `"license": "GPL-2.0-or-later"`). Die Codebasis wird seit 1999 entwickelt,
zuerst unter dem Namen eZ Publish, einem Produkt, das sein Hersteller nach fast 18 Jahren eingestellt hat; 7x hat es am
Leben erhalten, und das Produkt heißt seit August 2025 Exponential ([Wer ist 7x?](../wer-ist-7x.md)).

Für einen PHP-Entwickler ist diese Geschichte in einer Hinsicht wichtig: Der Code ist älter als Composer, PSR-4 und
Namespaces, und er hat seine eigenen Konventionen behalten. Sie sind konsistent und dokumentiert, und die Prüfungen des
Projekts setzen sie bei neuem Code durch.

| Teil | Verzeichnis | Was es enthält |
|---|---|---|
| Kernel | `kernel/` | Inhaltsmodell, Module und Views, Datentypen, Workflows, Benutzer und Rollen, der Anfrage-Kernel (`kernel/private/classes/ezpkernelweb.php`) |
| Bibliotheken | `lib/` | allgemeine Bausteine: `lib/ezutils` (`eZINI`, `eZDebug`, `eZSys`, `eZHTTPTool`, `eZExecution`), `lib/ezdb` (`eZDB` und die Treiber), `lib/eztemplate` (die Template-Engine), `lib/ezfile`, `lib/ezi18n` und mehr |
| Erweiterungen | `extension/` | alles, was darauf aufsetzt, mitgeliefert oder eigenes: Module, Datentypen, Operatoren, Designs, Einstellungen |
| Einstellungen | `settings/` | die INI-Standardwerte; Installationen überschreiben sie in `settings/override/` und `settings/siteaccess/` |
| Einstiegspunkte | `index.php`, `bin/php/*.php`, `runcronjobs.php` | der Web-Front-Controller, die Kommandozeilenskripte und der Cronjob-Runner |

Einen Rundgang durch jedes Verzeichnis finden Sie unter [Ein Rundgang durch den Quellbaum](ein-rundgang-durch-den-quellbaum.md).

## Fünf Dinge, die PHP-Entwickler überraschen

1. **Klassen-Maps, nicht PSR-4.** Klassen werden über generierte Arrays gefunden: `autoload/ezp_kernel.php` für den
   Kernel und `var/autoload/ezp_extension.php` für die Erweiterungen, geschrieben von `php bin/php/ezpgenerateautoloads.php`.
   Eine neue Klasse, die nicht in der Map steht, existiert nicht ([Autoloads und Klassen-Maps](autoloads-und-klassen-maps.md)).
2. **Der Kernel-Stil kennt keine Typdeklarationen.** `phpcs.xml.dist` lehnt in neuem Code `declare( strict_types=1 )`,
   skalare Parametertypen, Rückgabetypen und Eigenschaftstypen ab, damit er zum vorhandenen Code passt und PHP 8.0
   weiter funktioniert ([Warum der Code keine Typdeklarationen hat](warum-keine-typdeklarationen.md)).
3. **`array( ... )`, nicht `[ ... ]`, und Leerzeichen innerhalb von Klammern.** `foo( $a, $b )`, `if ( $a )`, die
   öffnende geschweifte Klammer einer Funktion in einer eigenen Zeile ([Der Coding-Standard](der-coding-standard.md)).
4. **Einstellungen sind INI-Dateien in Schichten.** `eZINI::instance( 'site.ini' )->variable( 'Block', 'Key' )` liest
   den Wert, nachdem die Kernel-Standardwerte, die Erweiterungen, der Siteaccess und die Überschreibungen der Installation
   zusammengeführt wurden ([Einstellungen mit eZINI lesen](einstellungen-mit-ezini-lesen.md)).
5. **Viel Zustand ist statisch.** Singletons wie `eZINI::instance()`, `eZDB::instance()` und In-Memory-Caches leben in
   statischen Eigenschaften. Unter PHP-FPM beginnt jede Anfrage frisch; unter den persistenten Workern von Velocity
   überleben sie zwischen Anfragen, sofern der Kernel sie nicht zurücksetzt ([Fallen durch statischen Zustand](fallen-durch-statischen-zustand.md)).

## Die Bausteine, die Sie schreiben werden

| Sie möchten | Sie schreiben | Seite |
|---|---|---|
| einen neuen Bildschirm unter `/<module>/<view>` | ein Modul mit Views | [Schreiben Sie Ihre erste Modul-View](ihre-erste-modul-view-schreiben.md) |
| ein Kommandozeilenwerkzeug | eine Klasse, die `Exponential\Runnable\Command` erweitert | [Einen Konsolenbefehl schreiben](einen-konsolenbefehl-schreiben.md) |
| zeitlich geplante Arbeit | einen Cronjob-Teil | [Einen Cronjob-Teil schreiben](einen-cronjob-teil-schreiben.md) |
| Daten für Templates | eine Fetch-Funktion | [Eine Fetch-Funktion schreiben](eine-fetch-funktion-schreiben.md) |
| einen Template-Filter oder eine Hilfsfunktion | eine Template-Operator-Klasse | [Einen Template-Operator in PHP schreiben](einen-template-operator-in-php-schreiben.md) |
| einen neuen Feldtyp | eine Klasse, die `eZDataType` erweitert | [Einen Datentyp schreiben](einen-datentyp-schreiben.md) |
| einen Schritt in einem Workflow | eine Klasse, die `eZWorkflowEventType` erweitert | [Einen Workflow-Ereignistyp schreiben](einen-workflow-ereignistyp-schreiben.md) |
| Daten in einer eigenen Tabelle | eine Klasse, die `eZPersistentObject` erweitert | [Definitionen persistenter Objekte im Detail](definitionen-persistenter-objekte-im-detail.md) |
| JSON für Ihr eigenes JavaScript | eine ezjscore-Serverfunktion | [ezjscore-JSON-Endpunkte in PHP](ezjscore-json-endpunkte-in-php.md) |
| eine Reaktion auf etwas, das der Kernel tut | einen Event-Listener | [Ereignisse, Listener und Filter](ereignisse-listener-und-filter.md) |

## Was das Projekt bei jeder Änderung prüft

| Prüfung | Werkzeug | Seite |
|---|---|---|
| PHP-Syntax, auch mit PHP 8.0 | `php -l`, `make lint80` | [Code schreiben, der unter PHP 8.0 läuft](code-der-unter-php-8-0-laeuft.md) |
| Code-Stil geänderter Dateien | `make phpcs` (`phpcs.xml.dist`) | [Der Coding-Standard](der-coding-standard.md) |
| Statische Analyse | `make phpstan` (Level 1 mit einer Baseline) | [Statische Analyse mit PHPStan](statische-analyse-mit-phpstan.md) |
| Unit-Tests | `vendor/bin/phpunit` | [PHPUnit ohne Datenbank](phpunit-ohne-datenbank.md) |
| Das Datei-Manifest | `make manifest-check` | [Git-Hooks und make-Ziele](git-hooks-und-make-ziele.md) |

## Wie es weitergeht

- Folgen Sie [dem Lesepfad für PHP-Entwickler](lesepfad-fuer-php-entwickler.md) in Reihenfolge.
- Richten Sie einen Checkout ein: [Einen PHP-Entwicklungs-Checkout einrichten](ein-php-entwicklungs-checkout.md).
- Lernen Sie die Anfrage kennen: [Wie eine Anfrage in PHP abläuft](wie-eine-anfrage-in-php-ablaeuft.md).

## Verwandte Seiten

- [PHP-Entwickler: hier beginnen](php-entwickler-hier-beginnen.md)
- [Kernel-Architektur](../../standards/architektur/kernel-architektur.md)
- [Anwendungsentwickler: hier beginnen](../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md),
  [das Buch für Erweiterungsentwickler](../erweiterungsentwicklung/README.md)
- [Software-Ingenieure: hier beginnen](../../../eng-us/help/software-engineers/software-engineers-start-here.md),
  [Web-Framework-Entwickler: hier beginnen](../../../eng-us/help/web-framework-developers/web-framework-developers-start-here.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
