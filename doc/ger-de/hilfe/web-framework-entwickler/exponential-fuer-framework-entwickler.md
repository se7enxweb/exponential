---
title: Exponential für Web-Framework-Entwickler
audience: web-framework-developer, developer
category: web-frameworks
order: 10
summary: Was Exponential ist, wenn Sie es mit den Augen eines Symfony-, Laravel-, Django- oder Rails-Entwicklers betrachten – ein PHP-Anwendungsframework mit eingebautem Inhaltsmodell, Berechtigungen, Templates und Server –, welche Teile vertraut wirken, welche anders sind und wo jeder Teil in der Installation liegt.
keywords: Framework, framework, Anwendungsframework, application framework, CMF, CMS, Überblick, overview, Architektur, architecture, Kernel, kernel, Erweiterung, extension, Modul, module, View, view, Siteaccess, siteaccess, INI, Template-Sprache, template language, Velocity, Web-Framework-Entwickler, web framework developer
type: help
---
# Exponential für Web-Framework-Entwickler

Diese Seite richtet sich an Entwickler, die ein Web-Framework kennen und sich ein erstes, ehrliches Bild von Exponential
machen möchten. Die README beschreibt es als „ein professionelles PHP-Anwendungsframework mit fortgeschrittener
CMS-Funktionalität (Content-Management-System)“, und beide Hälften dieses Satzes zählen: Darunter liegt ein Framework
(Front Controller, Routing zu Modulen und Views, Templates, eine Datenbankschicht, Einstellungen, eine Konsole), und
darüber ein Inhaltsmodell mit Versionen, Übersetzungen, Berechtigungen und einer Administrationsoberfläche, die Sie
bekommen, ohne Code zu schreiben.

## Inhalt

1. [In einem Absatz](#in-einem-absatz)
2. [Was vertraut wirkt](#was-vertraut-wirkt)
3. [Was anders ist](#was-anders-ist)
4. [Wo die Dinge liegen](#wo-die-dinge-liegen)
5. [Wie Sie Code hinzufügen](#wie-sie-code-hinzufügen)
6. [Wie es weitergeht](#wie-es-weitergeht)

## In einem Absatz

Exponential 6 ist eine PHP-Codebasis mit einer langen Geschichte: Sie wurde ab 1999 entwickelt, zuerst unter dem Namen
eZ Publish, den sein Hersteller nach fast 18 Jahren eingestellt hat; 7x hat die Entwicklung fortgesetzt, und seit
August 2025 heißt das Produkt Exponential ([Wer ist 7x?](../wer-ist-7x.md)). Es wird mit Composer installiert, läuft
unter PHP 8, speichert Daten in SQLite, PostgreSQL, MySQL/MariaDB, Oracle oder MongoDB
([Datenbankunterstützung](../datenbankunterstuetzung.md)) und wird von Apache oder nginx mit PHP-FPM, von Exponential Velocity
(einem in PHP geschriebenen Server mit persistenten Workern, der seit 6.0.15 Teil des Produkts ist) oder von FrankenPHP
ausgeliefert ([Velocity ist Teil von Exponential 6.0.15](../velocity-ist-teil-von-exponential-6-0-15.md)).

## Was vertraut wirkt

| Sie kennen | Exponential hat |
|---|---|
| Einen Front Controller (`public/index.php`) | `index.php`, das `ezpKernel( new ezpKernelWeb() )` aufbaut und dessen Inhalt zurückgibt |
| Routen zu Controller-Actions | Module und Views: `/<module>/<view>/<params>`, deklariert in `module.php` ([Routing](routing-mit-modulen-und-views.md)) |
| Eine Template-Engine | die Template-Sprache (`.tpl`-Dateien, die `eZTemplate` zu PHP kompiliert) ([Die View-Schicht](templates-im-vergleich-zu-twig-und-blade.md)) |
| Ein ORM oder Active Record | `eZPersistentObject` für Ihre eigenen Tabellen und die Content-API für Inhalte ([ORM](orm-und-ezpersistentobject.md)) |
| Eine Konsole | `bin/php/console` mit den Befehlen `exp:*`, `ext:*` und `cron:*` ([Kommandozeile](cli-und-konsolenbefehle.md)) |
| Pakete | Erweiterungen, installiert als Composer-Pakete ([Pakete](pakete-erweiterungen-und-composer.md)) |
| Konfigurationsdateien | INI-Dateien in Ebenen ([Konfiguration](konfiguration-ini-dateien.md)) |
| Tests | PHPUnit, die meisten Suiten ohne Datenbank ([Testen](testen.md)) |

## Was anders ist

- **Inhalte sind ein vollwertiges Modell.** Die meisten Daten, die ein Redakteur bearbeitet, sind keine Tabelle, die Sie
  entwerfen, sondern eine *Inhaltsklasse* (ein Typ mit Attributen), deren *Objekte* *Versionen* und *Übersetzungen*
  haben und an *Knoten* in einem Baum hängen. Sie definieren Klassen in der Administrationsoberfläche und bekommen
  Bearbeitung, Historie, Berechtigungen und Suche ohne Zusatzaufwand
  ([Das Inhaltsmodell für ORM-Nutzer](das-inhaltsmodell-fuer-orm-nutzer.md)).
- **Siteaccesses statt einer einzigen Anwendungskonfiguration.** Ein Request wird zuerst einem *Siteaccess* zugeordnet
  (einer Site, einer Sprachversion, der Administrationsoberfläche), und die Einstellungen dieses Siteaccess werden vor
  dem Routing geladen ([Siteaccesses](siteaccesses-hosts-und-mandanten.md)).
- **Kein allgemeiner Dependency-Injection-Container.** Der Legacy-Kernel findet Klassen über INI-Einstellungen,
  Namenskonventionen und eine erzeugte Klassenzuordnung. Läuft er über die Legacy Bridge innerhalb von Symfony, kann er
  den Container von Symfony erhalten, aber der Kernel selbst definiert darin keine Dienste
  ([Dependency Injection](dependency-injection-und-dienste.md)).
- **Templates werden überschrieben, nicht erweitert.** Eine Designkette und Regeln in `override.ini` entscheiden,
  welches Template eine View rendert; Blockvererbung wie in Twig oder Blade gibt es nicht
  ([Template-Auflösung](template-aufloesung-designs-und-overrides.md)).
- **Berechtigungen sind Daten.** Rollen aus Richtlinien (Modul, Funktion, Einschränkungen) werden in der Datenbank
  gespeichert und in der Administrationsoberfläche zugewiesen; Ihr Modul deklariert die Funktionen, Administratoren
  vergeben sie ([Autorisierung](autorisierung-rollen-und-richtlinien.md)).
- **Schemaänderungen sind keine Migrationsklassen.** Tabellen werden in datenbankneutralen `.dba`-Dateien beschrieben;
  Upgrades sind Skripte, die Sie mitliefern ([Migrationen](migrationen-und-dba-schemadateien.md)).

## Wo die Dinge liegen

Aus [Architektur für Anwendungsersteller](../ersteller/anwendungsentwickler/architektur-fuer-anwendungsentwickler.md):

```text
kernel/, lib/      the kernel: modules, classes, the template engine, the database layer
autoload/          the kernel's class map (autoload/ezp_kernel.php)
extension/         extensions: your application lives here
design/            designs: templates, stylesheets, images
settings/          shipped settings; settings/siteaccess/ and settings/override/ hold yours
var/               runtime: caches, logs, storage, the SQLite database
bin/php/console    one entry point for every command
cronjobs/          the scripts runcronjobs.php runs
index.php          the front controller
```

## Wie Sie Code hinzufügen

Sie bearbeiten nie `kernel/` oder `lib/`. Alles, was Sie schreiben, kommt in eine **Erweiterung**, die Einheit, die die
Rolle eines Symfony-Bundles, eines Laravel-Pakets oder einer Django-App übernimmt: Module (Ihre Routen und Bildschirme),
Klassen, Templates, Einstellungen, Datentypen, Workflow-Ereignistypen, Cronjob-Teile und Konsolenbefehle.
**Setup > RAD** (`/setup/rad`) listet jeden Erweiterungspunkt auf und schreibt für die meisten davon den Grundcode
([RAD-Werkzeuge](../../funktionen/6.0/rad-werkzeuge.md), [Erweiterungspunkte](../../bc/6.0/rad-erweiterungspunkte.md)).

## Wie es weitergeht

1. [Begriffswörterbuch](begriffswoerterbuch.md): die Begriffe Ihres Frameworks und ihre Gegenstücke in Exponential.
2. [Der Request-Lebenszyklus](der-request-lebenszyklus.md): was zwischen `index.php` und der Response geschieht.
3. [Aufgabe: ein kleines CRUD-Modul bauen](ihr-erstes-modul-schritt-fuer-schritt.md): der ganze Weg einmal von Hand.
4. Die Umstiegsseiten: [Symfony](umstieg-von-symfony.md), [Laravel](umstieg-von-laravel.md),
   [Slim, CodeIgniter, Yii, CakePHP oder Laminas](umstieg-von-micro-und-klassischen-php-frameworks.md),
   [Django und Rails](umstieg-von-django-und-rails.md), [Express und Spring](umstieg-von-express-und-spring.md).

## Verwandte Seiten

- [Web-Framework-Entwickler: hier beginnen](web-framework-entwickler-hier-beginnen.md)
- [Exponential als Anwendungsplattform](../ersteller/anwendungsentwickler/exponential-als-anwendungsplattform.md)
- [Kernel-Architektur](../../standards/architektur/kernel-architektur.md)
- [Erweiterungsentwicklung: das Buch](../erweiterungsentwicklung/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
