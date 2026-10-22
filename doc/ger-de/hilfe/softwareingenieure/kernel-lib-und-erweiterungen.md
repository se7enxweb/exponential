---
title: "Kernel, lib und Erweiterungen: die Schichtung"
audience: software-engineer, developer
category: software-engineering
order: 120
summary: Die Abhängigkeitsregeln zwischen lib/, kernel/classes, kernel/private, den Modul-Controllern, den Designs und Erweiterungen - welche Schicht welche aufrufen darf, wo die Regeln noch nicht erfüllt sind, wie Namen zeigen, zu welcher Schicht Code gehört, und wohin ein neues Stück Code gehört.
keywords: Schichtung, Abhängigkeiten, lib, kernel/classes, kernel/private, Controller, Designs, Erweiterungen, Benennung, Präfix exp, Namensräume, Architekturregeln, Softwareingenieur, layering, dependencies, controllers, extensions, naming, exp prefix, namespaces, architecture rules
type: help
---
# Kernel, lib und Erweiterungen: die Schichtung

Diese Seite richtet sich an Ingenieure, die entscheiden, wohin Code gehört, und prüfen, ob eine Änderung die Grenzen
einhält. Die Regeln stammen aus [Regeln zu Schichtung und Benennung](../../standards/architektur/schichten-und-benennung.md),
das auch jeden bekannten Verstoß auflistet.

## Inhalt

1. [Die Schichten und die Richtung der Aufrufe](#die-schichten-und-die-richtung-der-aufrufe)
2. [Was jede Schicht darf](#was-jede-schicht-darf)
3. [Wo die Regeln nicht erfüllt sind](#wo-die-regeln-nicht-erfüllt-sind)
4. [Namen verraten die Schicht](#namen-verraten-die-schicht)
5. [Wohin ein neues Stück Code gehört](#wohin-ein-neues-stück-code-gehört)

## Die Schichten und die Richtung der Aufrufe

```text
 extension/<name>/          plugins: settings, modules, designs, classes, cronjobs
 design/<design>/templates  presentation: reads data through fetch functions and operators only
 kernel/<module>/           controllers: module.php, view entry files, function_definition.php
 kernel/private/            internal API: ezpKernel, views, commands, cronjobs, services, REST, ezpEvent
 kernel/classes/            domain: content, users, roles, datatypes, workflows, caches, persistence
 lib/                       generic libraries: eZDB, eZINI, eZTemplate, eZFile, eZSys, eZModule, sessions
```

Eine Schicht darf die Schichten unter ihr aufrufen. Aufrufe nach oben sind Ausnahmen, und neue sollten nicht
hinzukommen.

## Was jede Schicht darf

| Schicht | Darf | Darf nicht |
|---|---|---|
| `lib/` | Infrastruktur liefern, die nichts von Inhalten weiß | von einem Modul oder einem Design abhängen; neuer Code sollte nicht von `kernel/` abhängen |
| `kernel/classes/` | Inhalte, Benutzer, Rollen, Datentypen, Workflows, Caches modellieren | Seiten rendern oder Anfrageeingaben lesen; sie nimmt Werte und gibt Objekte zurück |
| `kernel/<module>/` und View-Klassen | Eingaben über `eZHTTPTool` lesen, die Domäne aufrufen, über `eZTemplate` rendern | selbst HTML ausgeben, außer Downloads und JSON |
| `kernel/private/` | den Kernel von Anfang bis Ende besitzen: Kernel-Handler, Views, Befehle, Cronjob-Teile, Dienste, REST | als stabile API für Erweiterungen behandelt werden; ihre Signaturen können sich ändern |
| `design/` | Daten darstellen, Ausgaben mit `\|wash` maskieren | die Datenbank abfragen; in den Administrationsdesigns Inline-CSS oder Inline-Skripte enthalten |
| `extension/` | alles darunter über dokumentierte Erweiterungspunkte verwenden | Änderungen an Kernel-Dateien verlangen |

Arbeit, die sich eine View, ein Befehl und ein Cronjob-Teil teilen, gehört in eine Dienstklasse in
`kernel/private/classes/services/` (Namensraum `Exponential\Service`), nicht in einen von ihnen
([Befehle, Cronjob-Teile und Modul-Views als Klassen](../../bc/6.0/cli-cronjob-und-view-abstraktionen.md)).

## Wo die Regeln nicht erfüllt sind

Der Standard hält diese offen fest; behandeln Sie sie als Schulden, nicht als Präzedenzfall:

- Mehrere Dateien in `lib/` rufen Kernel-Klassen auf (`eZUser`, `eZClusterFileHandler`, `eZPersistentObject`).
  Behandeln Sie `lib/` als "tiefer als der Kernel", nicht als unabhängige Bibliothekssammlung.
- Einige wenige View-Klassen lesen `$_POST` oder `$_GET` noch direkt statt über `eZHTTPTool`.
- `ezpEvent` ist als private API dokumentiert, dennoch verlassen sich Erweiterungen darauf.
- Views, Cronjob-Teile und Befehle als einfache Dateien laufen noch neben der klassenbasierten Form.

Siehe [Mit technischen Schulden umgehen](technische-schulden.md), wie solche Punkte festgehalten und abgebaut werden.

## Namen verraten die Schicht

| Name | Schicht |
|---|---|
| `eZ*`, `ezp*` (`eZContentObject`, `ezpKernelWeb`) | geerbter Kernel- und Bibliothekscode; nie umbenannt |
| `exp*` (`expContentSecurityPolicy`, `expAuditConfig`) | Code, der seit der Umbenennung des Produkts hinzukam; neuer Kernel-Code verwendet `exp` |
| `Exponential\View\Kernel\...`, `Exponential\Command\Kernel\...`, `Exponential\Cronjob\Kernel\...` | klassenbasierte Views, Befehle und Cronjob-Teile des Kernels |
| `Exponential\Command\Extension\<Ext>`, `Exponential\View\Extension\<Ext>\<Module>` | dasselbe für eine Erweiterung |
| `sevenx*` | das öffentliche PHP-Präfix von 7x, von einigen Erweiterungen verwendet ([Die Präfixregel](../../standards/namenspraefixe-sevenx-und-exp.md)) |

Eine Klasse je Datei, und der Dateiname ist der Klassenname in Kleinbuchstaben: `expContentSecurityPolicy` liegt in
`expcontentsecuritypolicy.php`. Erzeugen Sie nach dem Hinzufügen oder Umbenennen einer Klasse die Klassenzuordnungen neu
(`php bin/php/ezpgenerateautoloads.php -e`; `-k` für den Kernel).

## Wohin ein neues Stück Code gehört

| Sie schreiben | Legen Sie es ab in |
|---|---|
| Eine Funktion für eine Installation oder ein wiederverwendbares Plugin | eine Erweiterung: `extension/<ext>/` ([Aufbau einer Erweiterung](../ersteller/anwendungsentwickler/aufbau-einer-erweiterung.md)) |
| Ein neues Kernel-Subsystem | `kernel/classes/<subsystem>/` für die Domäne, `kernel/private/classes/` für Views, Befehle und Dienste |
| Infrastruktur ohne Wissen über Inhalte | `lib/`, ohne neue Abhängigkeiten zum Kernel |
| Eine Seite | eine Modul-View: eine Unterklasse von `ModuleView` plus ihr Template |
| Eine Einstellung | ihren Standard mit einem Kommentar in `settings/<file>.ini`, gelesen über `eZINI` |
| Text, der Benutzern gezeigt wird | `ezpI18n::tr( $context, $text )` in PHP, `i18n( $context )` in Templates |

## Verwandte Seiten

- [Architekturüberblick](architekturueberblick.md)
- [Events, Erweiterungspunkte und Plugins](events-erweiterungspunkte-und-plugins.md)
- [Erweiterungspunkte auf einen Blick](../erweiterungsentwicklung/teil-8-referenz/erweiterungspunkte-auf-einen-blick.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
