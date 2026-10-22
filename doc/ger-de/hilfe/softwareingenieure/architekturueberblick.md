---
title: Architekturüberblick
audience: software-engineer, developer
category: software-engineering
order: 100
summary: Die Schichten einer Exponential-Installation und wofür jede verantwortlich ist - Server, die Einstiegspunkte des Kernels, die Domänenklassen, die Bibliotheken, die Darstellungsschicht, Erweiterungen, Einstellungen und var/ - mit dem Verzeichnisaufbau und den Architekturentscheidungen, die ein Ingenieur kennen muss.
keywords: Architektur, Schichten, Kernel, lib, Erweiterungen, Siteaccess, Modul, View, Inhaltsmodell, Persistenz, Template-Engine, var, Verzeichnisaufbau, Softwareingenieur, architecture, layers, extensions, module, content model, persistence, template engine, directory layout, software engineer
type: help
---
# Architekturüberblick

Diese Seite ist die Landkarte einer Exponential-Installation für Ingenieure: welche Schicht was tut, wo sie auf der
Festplatte liegt und welche Architekturentscheidungen alles andere prägen. Die maßgebliche Beschreibung, mit einer Regel
für jeden Baustein, ist [Kernel-Architektur](../../standards/architektur/kernel-architektur.md); diese Seite fasst sie
zusammen und ergänzt die Begründungen.

## Inhalt

1. [Die Schichten](#die-schichten)
2. [Der Verzeichnisaufbau](#der-verzeichnisaufbau)
3. [Fünf Architekturentscheidungen](#fünf-architekturentscheidungen)
4. [Wo Zustand liegt](#wo-zustand-liegt)
5. [Was die Architektur nicht hat](#was-die-architektur-nicht-hat)

## Die Schichten

```text
 Browser / CLI / cron
        |
 Server: Apache or nginx + PHP-FPM  |  Velocity (qbix)  |  FrankenPHP
        |
 Entry points: index.php  /  bin/php/console  /  runcronjobs.php
        |
 Kernel: ezpKernel -> ezpKernelWeb   settings -> extensions -> siteaccess -> session -> module
        |
 Modules and views: eZModule::run() -> view class  (kernel/<module>/, extensions)
        |
 Domain: kernel/classes  (content, users, roles, datatypes, workflows, caches)
        |
 Libraries: lib/ezdb (eZDB), lib/eztemplate (eZTemplate), lib/ezutils (eZINI, eZSys, ...)
```

| Schicht | Verantwortung | Wichtige Klassen |
|---|---|---|
| Server | die Verbindung annehmen, statische Dateien ausliefern, PHP ausführen | Velocity, PHP-FPM, FrankenPHP ([Anwendungsserver](anwendungsserver.md)) |
| Einstiegspunkte | den richtigen Kernel für Web, Konsole oder Cron starten | `index.php`, `bin/php/console`, `runcronjobs.php`, `eZScript` |
| Kernel | Einstellungen, Erweiterungen, Siteaccess, Session, Weiterleitung | `ezpKernel`, `ezpKernelWeb`, `eZSiteAccess`, `eZExtension` |
| Module und Views | URL-Namensräume und Seiten, Berechtigungsprüfungen | `eZModule`, `Exponential\Runnable\ModuleView` |
| Domäne | Inhalte, Benutzer, Rollen, Datentypen, Workflows, Benachrichtigungen, Caches | `eZContentObject`, `eZContentObjectTreeNode`, `eZUser`, `eZRole`, `eZDataType`, `eZCache` |
| Bibliotheken | Datenbank, Templates, Einstellungen, Dateien, Gebietsschema, E-Mail | `eZDB`, `eZTemplate`, `eZINI`, `eZClusterFileHandler`, `eZLocale` |
| Darstellung | Templates, Designs, Overrides | `design/<design>/templates`, `override.ini` |
| Erweiterungen | alles, was eine Installation hinzufügt | `extension/<name>/` |

## Der Verzeichnisaufbau

Aus dem Installationsbuch ([1.2 Die Teile einer Installation](../../installation/01-einfuehrung.md#12-die-bestandteile-einer-installation)):

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

## Fünf Architekturentscheidungen

1. **Ein Front-Controller, eine Weiterleitungsschleife.** Jede Webanfrage läuft über `index.php` und
   `ezpKernelWeb::run()`; für normale Seiten gibt es keinen zweiten Web-Einstiegspunkt. Besondere Einstiegspunkte gibt
   es für REST (`index_rest.php`), das Baummenü (`index_treemenu.php`) und die Auslieferung von Dateien im Cluster
   (`index_cluster.php`).
2. **Der Siteaccess ist die Einheit der Konfiguration.** Ein Siteaccess ist eine benannte Konfiguration, die je Anfrage
   gewählt wird (eine Website, die Administration, eine Sprache); die meisten Verhaltensunterschiede sind Einstellungen,
   kein Code ([Siteaccesses und Konfiguration](siteaccesses-und-konfiguration.md)).
3. **Alles Austauschbare wird in Einstellungen benannt.** Module, Datentypen, Workflow-Events, Template-Operatoren,
   Cronjob-Teile, Benachrichtigungs-Handler, Login-Handler und Cluster-Datei-Handler werden über INI-Listen gefunden; eine
   Erweiterung klinkt sich also ein, indem sie eine Klasse und eine Datei `.ini.append.php` mitliefert
   ([Events, Erweiterungspunkte und Plugins](events-erweiterungspunkte-und-plugins.md)).
4. **Ein zur Laufzeit festgelegtes Inhaltsmodell.** Inhaltsklassen sind Daten, kein Code: Administratoren fügen Klassen
   und Attribute ohne Bereitstellung hinzu, und Datentypen geben jedem Attribut Speicherung, Prüfung und Templates
   ([Das Inhaltsmodell für Ingenieure](inhaltsmodell-fuer-ingenieure.md)).
5. **Alle Cache-Dateien über den Cluster-Datei-Handler.** Caches werden über `eZClusterFileHandler` geschrieben, sodass
   derselbe Code auf einer Festplatte oder auf gemeinsamem Speicher für mehrere Webknoten läuft
   ([Skalierung und Clustering](skalierung-und-clustering.md)).

## Wo Zustand liegt

| Zustand | Wo | Zwischen Webknoten geteilt |
|---|---|---|
| Inhalte, Benutzer, Rollen, URL-Aliase | die Datenbank | ja |
| Hochgeladene Dateien und Bildvarianten | `var/<site>/storage` (oder das Cluster-Backend) | muss sein |
| Caches | `var/<site>/cache` oder Redis bzw. Valkey mit `sevenx_valkey_cache` | hängt vom Cache ab ([Caching-Ebenen für Ingenieure](caching-ebenen-fuer-ingenieure.md)) |
| Sessions | der Session-Speicher von PHP oder die Tabelle `ezsession` mit `ezpSessionHandlerDB` | muss sein, bei mehreren Knoten |
| Logs und Audit-Einträge | `var/log`, `var/<site>/log` | je Knoten, sofern nicht eingesammelt |
| Einstellungen | `settings/`, `settings/` der Erweiterungen | mit dem Code bereitgestellt |

## Was die Architektur nicht hat

- **Keinen eigenen Dependency-Injection-Container.** Klassen werden über die Klassenzuordnung und Einstellungen
  gefunden; Singletons wie `eZINI::instance()` und `eZDB::instance()` sind die Regel. Läuft der Kernel innerhalb von
  Symfony, geben `ezpKernel::hasServiceContainer()` und `getServiceContainer()` Zugriff auf den Container von Symfony.
- **Keinen Migrations-Runner.** Datenbankaktualisierungen sind SQL-Dateien je Treiber, von Hand angewendet
  ([Schemaänderungen und Migrationen](schemaaenderungen-und-migrationen.md)).
- **Keinen Message-Broker im Kernel.** Hintergrundarbeit läuft als Cronjob-Teile, Inhaltsjobs und über die Erweiterung
  `expqueue` ([Konsole, Cronjobs und Hintergrundarbeit](konsole-cron-und-hintergrundarbeit.md)).
- **Kein Symfony im Kernel.** Die Produkte der Symfony-Plattform betten den Kernel über die Legacy Bridge ein
  ([Symfony und die Legacy Bridge](../ersteller/anwendungsentwickler/symfony-und-die-legacy-bridge.md)).

## Verwandte Seiten

- [Der Lebenszyklus einer Anfrage](lebenszyklus-einer-anfrage.md)
- [Kernel, lib und Erweiterungen: die Schichtung](kernel-lib-und-erweiterungen.md)
- [Architektur für Anwendungsentwickler](../ersteller/anwendungsentwickler/architektur-fuer-anwendungsentwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
