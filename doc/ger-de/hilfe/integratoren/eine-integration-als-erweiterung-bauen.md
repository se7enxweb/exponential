---
title: Eine Integration als Erweiterung bauen
audience: integrator, developer
category: integration
order: 410
summary: Eine Integration als Exponential-Erweiterung verpacken - die Verzeichnisstruktur einer typischen Integration, Einstellungen mit Geheimnissen in settings/override, Klassen, Befehle, Cronjob-Teile, Workflow-Ereignisse, Schema, Tests, composer.json, ezinfo.php und extension.xml, Aktivierung und Veröffentlichung.
keywords: Erweiterung, Integrationserweiterung, Verzeichnisstruktur, Einstellungen, Klassen, bin/php, Cronjobs, eventtypes, login_handler, Schema, dba, sql, Tests, composer.json, ezinfo.php, extension.xml, ActiveExtensions, Autoloads, Veröffentlichung, extension, integration extension, directory layout, settings, release
type: help
---
# Eine Integration als Erweiterung bauen

Diese Seite ist für Integrationsentwickler. Alles, was eine Integration braucht, passt in eine **Erweiterung**:
Einstellungen, Klassen, Befehle, Cronjob-Teile, Workflow-Ereignisse, Login-Handler, REST-Controller,
Serverfunktionen, Tabellen und Tests. Eine Erweiterung wird in einem eigenen Git-Repository versioniert, mit Composer
installiert, mit einer Zeile aktiviert und kann veröffentlicht und zurückgegeben werden. Die vollständige Anleitung zu
Erweiterungen ist [Erweiterungsentwicklung: das Buch](../erweiterungsentwicklung/README.md).

## Inhalt

1. [Die Struktur einer Integrationserweiterung](#die-struktur-einer-integrationserweiterung)
2. [Einstellungen und Geheimnisse](#einstellungen-und-geheimnisse)
3. [Eigene Tabellen](#eigene-tabellen)
4. [Metadaten und Composer](#metadaten-und-composer)
5. [Aktivieren und deployen](#aktivieren-und-deployen)
6. [Eine Erweiterung oder mehrere](#eine-erweiterung-oder-mehrere)

## Die Struktur einer Integrationserweiterung

```text
extension/myintegration/
    composer.json                       package name, requirements, license
    ezinfo.php                          name, version, license, website (the about page)
    extension.xml                       the same metadata in XML
    README.md, CHANGELOG.md, LICENSE
    settings/
        myintegration.ini               your settings with safe defaults, no secrets
        site.ini.append.php             [Event] Listeners[], [UserSettings] ExtensionDirectory[] ...
        cronjob.ini.append.php          [CronjobSettings] ExtensionDirectories[], [CronjobPart-...]
        workflow.ini.append.php         [EventSettings] ExtensionDirectories[], AvailableEventTypes[]
        ezjscore.ini.append.php         [ezjscServer_...] for server functions or an expservices domain
        rest.ini.append.php             [ApiKeySettings] RouteScopes[] for REST routes of your own
    classes/                            the work: client of the other system, mapping, importer, sender
        runnable/cronjobs/              cronjob parts as classes
    bin/php/                            commands (ext:myintegration:<name>), including the monitoring check
    cronjobs/                           one-line files that call the cronjob classes
    eventtypes/event/<type>/            workflow event types
    login_handler/                      login handlers, if any
    share/db_schema.dba, sql/<engine>/  tables of your own
    tests/                              unit tests without a database, test fixtures
    doc/                                the integration's documentation and runbook
```

Nötig sind nur die Teile, die die Integration nutzt. Namen folgen den Regeln des Kernels: Klassen `myIntegration...`
in gleichnamigen Dateien in Kleinbuchstaben
([Autoloads und Klassenbenennung](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/autoloads-und-klassennamen.md)).
Neue Namen verwenden Ihr eigenes Präfix, nie `ez`.

## Einstellungen und Geheimnisse

| Art | Wo |
|---|---|
| Standardwerte, die veröffentlicht werden dürfen (Endpunkt des Testsystems, Stapelgrößen, Bezeichner übergeordneter Knoten) | `extension/myintegration/settings/myintegration.ini` |
| Werte pro Installation (Produktionsendpunkt, Knoten-IDs) | `settings/override/myintegration.ini.append.php` oder die Einstellungen eines Siteaccess |
| Geheimnisse (API-Schlüssel, Passwörter, Signaturgeheimnisse) | `settings/override/myintegration.ini.append.php`, außerhalb von Git; oder Umgebungsvariablen |

Lesen Sie sie mit `eZINI::instance( 'myintegration.ini' )->variable( 'Block', 'Name' )`. Dokumentieren Sie jede
Einstellung in der mitgelieferten INI-Datei mit einer Kommentarzeile darüber
([Einstellungen in einer Erweiterung](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/einstellungen-in-einer-erweiterung.md)).

## Eigene Tabellen

Outbox, Inbox, Wasserstände und Zuordnungen liegen in Tabellen Ihrer Erweiterung, einmal in `share/db_schema.dba`
definiert (und, wo nötig, in `sql/<engine>/schema.sql`), mit einem kleinen Installationsbefehl, der sie idempotent
anlegt ([Datenbankschema und dba-Dateien](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/datenbankschema-und-dba-dateien.md)).
Greifen Sie über `eZPersistentObject`-Klassen oder `eZDB` mit gecasteten IDs und maskierten Zeichenketten darauf zu.

## Metadaten und Composer

- `ezinfo.php` und `extension.xml` nennen Name, Version (in beiden gleich, mit jeder Veröffentlichung erhöht),
  Lizenz und Website; die Info-Seite liest sie
  ([ezinfo.php](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/ezinfo-php.md),
  [extension.xml](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/extension-xml.md)).
- `composer.json` nennt das Paket, seinen Typ, die Lizenz und seine Anforderungen an den Kernel und andere
  Erweiterungen ([composer.json für Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/composer-json-fuer-erweiterungen.md)).
- Eine kundenspezifische Integration liegt in einem privaten Repository; ein wiederverwendbarer Konnektor kann
  veröffentlicht werden ([Integrationen zurückgeben](integrationen-zurueckgeben.md)).

## Aktivieren und deployen

```ini
# settings/override/site.ini.append.php
[ExtensionSettings]
ActiveExtensions[]=myintegration
```

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/console exp:velocity deploy --allow-root-user     # or: clear the INI cache, reload PHP-FPM, restart Velocity
```

Aktivieren Sie Integrationserweiterungen global (`ActiveExtensions`), wenn Cronjobs und Befehle sie in jedem
Siteaccess brauchen; eine Access-Erweiterung (`ActiveAccessExtensions`) sehen nur die Siteaccesses, die sie
auflisten, und jede Cron-Zeile muss dann mit `-s` einen solchen Siteaccess nennen
([Eine Erweiterung aktivieren](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/eine-erweiterung-aktivieren.md)).

## Eine Erweiterung oder mehrere

| Eine Erweiterung | Mehrere |
|---|---|
| Ein Kunde, ein Fremdsystem, ein Team | Ein wiederverwendbarer Konnektor (zum Beispiel „ein CRM-Client"), den mehrere Kundenerweiterungen nutzen |
| Einfache Veröffentlichung und Übergabe | Der allgemeine Teil wird veröffentlicht; der Kundenteil bleibt privat |

Den allgemeinen Konnektor von der Kundenzuordnung zu trennen ist das, was das Zurückgeben möglich macht.

## Verwandte Seiten

- [Erweiterungsentwicklung: das Buch](../erweiterungsentwicklung/README.md)
- [Verzeichnisstruktur einer Erweiterung](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/verzeichnisaufbau-einer-erweiterung.md)
- [Als Erweiterung verpacken, für App-Ersteller](../ersteller/anwendungsentwickler/als-erweiterung-paketieren.md)
- [Mit Git entwickeln](../mit-git-entwickeln.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
