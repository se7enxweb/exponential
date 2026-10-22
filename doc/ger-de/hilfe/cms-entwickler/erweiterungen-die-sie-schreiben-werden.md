---
title: Erweiterungen, die Sie schreiben werden
audience: cms-developer
category: cms-development
order: 38
summary: Für CMS-Entwickler die Erweiterungen, die ein typisches Website-Projekt braucht, aus Sicht des Projekts. Das sind eine Website-Erweiterung mit Design und Einstellungen und optional eine Code-Erweiterung mit Operatoren, Fetch-Funktionen, Modulen, Workflow-Events, Cronjobs oder Datentypen. Die Seite behandelt, wie sie aufgebaut, aktiviert und unter Versionsverwaltung gehalten werden und wo das Buch zur Erweiterungsentwicklung tiefer geht.
keywords: Erweiterung, Website-Erweiterung, Design-Erweiterung, Einstellungserweiterung, Projektstruktur, extension.xml, ezinfo.php, composer.json, ActiveExtensions, Autoloads, ezpgenerateautoloads, Template-Operator, Fetch-Funktion, Modul, RAD-Werkzeuge, Versionsverwaltung, extension, site extension, design extension, project structure, template operator, fetch function, module, RAD tools, version control
type: help
---
# Erweiterungen, die Sie schreiben werden

Diese Seite richtet sich an Entwickler, die den Code eines Website-Projekts planen. In Exponential liegt alles, was ein
Projekt hinzufügt, in **Erweiterungen** unter `extension/`: Designs, Einstellungen, PHP-Code, Templates,
Übersetzungen. Der Kernel und die mitgelieferten Erweiterungen bleiben unberührt, und das macht Aktualisierungen sicher.
Ein typisches Projekt braucht eine oder zwei eigene Erweiterungen.

## Inhalt

1. [Der übliche Satz](#der-übliche-satz)
2. [Die Website-Erweiterung](#die-website-erweiterung)
3. [Eine Code-Erweiterung](#eine-code-erweiterung)
4. [Was eine Code-Erweiterung hinzufügen kann](#was-eine-code-erweiterung-hinzufügen-kann)
5. [Aktivieren und laden](#aktivieren-und-laden)
6. [Versionsverwaltung](#versionsverwaltung)
7. [Schnelle Starts: die RAD-Werkzeuge](#schnelle-starts-die-rad-werkzeuge)
8. [Wo das Buch tiefer geht](#wo-das-buch-tiefer-geht)

## Der übliche Satz

| Erweiterung | Enthält | Beispielname |
|---|---|---|
| Website-Erweiterung | das Design, Override-Regeln, Siteaccess-Einstellungen, Bildvarianten, eigene Tags, Klassen als Pakete | `exp_mysite` |
| Code-Erweiterung (bei Bedarf) | PHP: Operatoren, Fetch-Funktionen, Module, Workflow-Events, Cronjobs, Konsolenbefehle | `exp_mysite_tools` |

Kleine Projekte halten beides in einer Erweiterung. Trennen Sie sie, wenn der Code von anderen Websites
wiederverwendet wird oder wenn Designer und PHP-Entwickler in unterschiedlichen Release-Zyklen arbeiten. Benennen Sie
sie mit dem Präfix Ihres Projekts und in neuem Code mit `exp`
([Namenspräfixe](../../standards/namenspraefixe-sevenx-und-exp.md)).

## Die Website-Erweiterung

```text
extension/exp_mysite/
  extension.xml, ezinfo.php, composer.json, README.md
  settings/
    design.ini.append.php            DesignExtensions[]=exp_mysite
    site.ini.append.php              extension-wide settings
    image.ini.append.php, content.ini.append.php
    siteaccess/<public sa>/          site.ini, override.ini ... of the public siteaccess
  design/exp_mysite/
    templates/  override/templates/  stylesheets/  javascript/  images/
  translations/<locale>/translation.ts
  packages/                          classes exported as packages
```

Design-Arbeit: [Ihre eigene Design-Erweiterung](../zwei-wege-zum-bauen/eigene-design-erweiterung.md),
[Aufbau einer Design-Erweiterung](../erweiterungsentwicklung/teil-6-theme-entwickler/aufbau-einer-design-erweiterung.md) und
[Theme-Erweiterungen: hier beginnen](../designer/erweiterungen/theme-erweiterungen-hier-beginnen.md) der Designer.

## Eine Code-Erweiterung

```text
extension/exp_mysite_tools/
  extension.xml, ezinfo.php, composer.json
  classes/                    PHP classes, autoloaded
  autoloads/                  eztemplateautoload.php for template operators
  modules/exp_mysite_tools/   module.php, views, function_definition.php
  eventtypes/event/<type>/    workflow event types
  cronjobs/                   cronjob scripts
  bin/php/                    command line scripts
  settings/                   the registrations and your own .ini
  tests/
```

Jeder Ordner und die Einstellung, mit der der Kernel ihn findet:
[Verzeichnisaufbau einer Erweiterung](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/verzeichnisaufbau-einer-erweiterung.md).

## Was eine Code-Erweiterung hinzufügen kann

| Sie brauchen | Fügen Sie hinzu | Lesen |
|---|---|---|
| Einen berechneten Wert oder eine Formatierung in Templates | einen Template-Operator | [Template-Operatoren](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/template-operatoren.md) |
| Daten, die kein Fetch liefert | eine Fetch-Funktion | [Fetch-Funktionen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/fetch-funktionen.md) |
| Eine Seite mit eigener Adresse, einen Formular-Handler, einen JSON-Endpunkt | eine Modul-View mit einer Richtlinie | [Module und Views](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/module-und-views.md) |
| Einen Schritt beim Veröffentlichen oder an der Kasse | einen Workflow-Event-Typ | [Workflows und Auslöser](workflows-und-ausloeser.md) |
| Geplante Arbeit | einen Cronjob | [Cronjobs](cronjobs.md) |
| Ein Feld mit eigener Speicherung und Bearbeitung | einen Datentyp | [Datentypen wählen](datentypen-waehlen.md#wann-sie-einen-eigenen-datentyp-schreiben) |
| Daten, die keine Inhalte sind | eine Tabelle mit `eZPersistentObject` | [Persistente Objekte und Tabellen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/persistente-objekte-und-tabellen.md) |
| Einen Import oder eine Wartungsaufgabe | einen Konsolenbefehl oder ein Skript | [CLI-Skripte und Konsolenbefehle](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/cli-skripte-und-console-befehle.md) |

Versuchen Sie Einstellungen, Templates und vorhandene Erweiterungen vor eigenem Code; das Kapitel
[Wenn Einstellungen nicht genügen](../erweiterungsentwicklung/teil-2-builder/wenn-einstellungen-nicht-reichen.md) des Buchs
hilft bei der Entscheidung.

## Aktivieren und laden

1. Fügen Sie die Erweiterung zu `site.ini [ExtensionSettings] ActiveExtensions[]` hinzu (oder zu
   `ActiveAccessExtensions[]` in einem Siteaccess):

   ```bash
   ./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' exp_mysite global --allow-root-user
   ```

2. Erzeugen Sie die Autoloads neu, nachdem Sie Klassen hinzugefügt oder umbenannt haben:
   `php bin/php/ezpgenerateautoloads.php -e`.
3. Leeren Sie den INI-Cache und starten Sie die PHP-Worker neu, die die Website bedienen
   ([Eine Website bereitstellen](eine-website-bereitstellen.md)).

Die Reihenfolge zählt: Eine Erweiterung, die in `ActiveExtensions[]` früher steht, gewinnt bei Einstellungen und in der
Design-Kette ([Eine Erweiterung aktivieren](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/eine-erweiterung-aktivieren.md),
[Ladereihenfolge der Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/ladereihenfolge-der-erweiterungen.md)).

## Versionsverwaltung

Jede Ihrer Erweiterungen ist ein eigenes Git-Repository oder Teil Ihres Projekt-Repositorys; der Kernel und die
mitgelieferten Erweiterungen kommen aus Composer. Committen Sie Templates, Einstellungen, Pakete und Code; committen Sie
nie `settings/override/`, `var/` oder Zugangsdaten ([Mit Git entwickeln](../mit-git-entwickeln.md),
[Entwicklungsumgebung](entwicklungsumgebung.md)). Geben Sie der Erweiterung eine `extension.xml` und eine
`ezinfo.php` mit einer Version, damit die Info-Seite der Administration und die Aktualisierungsprüfungen erkennen, welches
Release installiert ist ([extension.xml](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/extension-xml.md),
[ezinfo.php](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/ezinfo-php.md)).

## Schnelle Starts: die RAD-Werkzeuge

Die Administration hat Assistenten, die ein funktionierendes Gerüst schreiben: eine Design-Erweiterung, ein Modul, einen
Template-Operator, einen Datentyp, ein Workflow-Event, eine Einstellungserweiterung, eine Inhaltserweiterung mit Klassen
([RAD-Werkzeuge](../../funktionen/6.0/rad-werkzeuge.md),
[Rundgang durch die RAD-Werkzeuge](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/tutorials/rundgang-durch-die-rad-werkzeuge.md)).
Lesen Sie, was sie erzeugen; von da an ist es Ihr Code.

## Wo das Buch tiefer geht

Das Buch [Erweiterungsentwicklung](../erweiterungsentwicklung/README.md) behandelt all das ausführlich, von der ersten
Erweiterung bis zu ihrer Veröffentlichung. Verwenden Sie seine [Lesepfade](../erweiterungsentwicklung/lesepfade.md):
Der Pfad für Website-Bauer (Einstellungen, Templates, Overrides) und der Pfad für PHP-Entwickler passen am besten zu
einem CMS-Entwickler. Die Veröffentlichung einer Erweiterung für andere folgt strengen Regeln für Versionen und Tags
([Veröffentlichen](../erweiterungsentwicklung/teil-7-veroeffentlichen/README.md)).

## Verwandte Seiten

- [Erweiterungspunkte auf einen Blick](../erweiterungsentwicklung/teil-8-referenz/erweiterungspunkte-auf-einen-blick.md)
- [Was eine Erweiterung ist](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/was-eine-erweiterung-ist.md)
- [Modul 7: Erweiterungen](../trainer/entwickler/modul-07-erweiterungen/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
