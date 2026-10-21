---
title: Klassische Landingpages mit ezflow
audience: website-creator, developer
category: website-creation
order: 320
summary: Der Seitenbaukasten ezflow des klassischen Designs - der Datentyp page, Zonen-Layouts, Blöcke aus block.ini, Push to Block, die Zeitleiste und ihre Cronjobs - und wann Sie ihn statt des Layouts-Editors wählen.
keywords: ezflow, Seitenbaukasten, Datentyp page, Zonen-Layout, Blöcke, Zeitleiste, Startseite, Landingpage, Cronjob, Website-Ersteller, page datatype, zone layout, blocks, block.ini, zone.ini, push to block, timeline, frontpage, landing page, ezflowupdate, cronjob, website creator
type: help
---
# Klassische Landingpages mit ezflow

Diese Seite richtet sich an Website-Ersteller, die Websites auf dem klassischen Design mit `ezflow` pflegen oder bauen,
dem Seitenbaukasten, der vor dem Layouts-Editor kam. Sie beschreibt, wie ezflow-Seiten funktionieren, was zu
konfigurieren ist und wie Sie für neue Arbeiten zwischen ezflow und dem Layouts-Editor entscheiden. Referenz:
[ezflow](../../../funktionen/6.0/erweiterungen/ezflow.md).

## Inhalt

1. [Wie ezflow funktioniert](#wie-ezflow-funktioniert)
2. [Einrichten](#einrichten)
3. [Einen eigenen Block definieren](#einen-eigenen-block-definieren)
4. [Die Cronjobs](#die-cronjobs)
5. [ezflow oder der Layouts-Editor](#ezflow-oder-der-layouts-editor)

## Wie ezflow funktioniert

- Eine Klasse erhält ein Attribut **page** (die Klassen `frontpage` und `landing_page` der Demo haben eines).
- Beim Bearbeiten wählt der Redakteur ein **Zonen-Layout**; jede Zone enthält **Blöcke**.
- Ein Block listet Elemente auf: ausgewählt per **Push to Block** aus der Inhaltsansicht oder geliefert von seiner
  Quelle (ein Fetch, RSS, Schlüsselwörter).
- Elemente können zeitlich geplant werden; die **Zeitleiste** zeigt die Seite zu einem gewählten Zeitpunkt.

## Einrichten

1. Aktivieren Sie `ezflow` (es benötigt `ezjscore` und erweitert `ezwebin` und `ezwt`).
2. Erlaubte Zonen-Layouts stehen in `zone.ini [General] AllowedTypes[]`: `GlobalZoneLayout`, `2ZonesLayout1`,
   `2ZonesLayout2`, `2ZonesLayout3`, `3ZonesLayout1`, `3ZonesLayout2`, `CallForActionLayout`, pro Klasse eingeschränkt
   durch `AvailableForClasses[]`.
3. Blocktypen werden in `block.ini` definiert, ein Block pro Typ.
4. `ezflow.ini [eZFlowOperations] UpdateOnPublish=enabled` aktualisiert Blöcke, wenn Inhalte veröffentlicht werden.

## Einen eigenen Block definieren

Ein Blocktyp ist ein Eintrag in `block.ini` plus ein Template. In Ihrer Design-Erweiterung:

```ini
# extension/mysite_design/settings/block.ini.append.php
[General]
AllowedTypes[]=MyEvents

[MyEvents]
Name=Upcoming events
NumberOfValidItems=5
NumberOfArchivedItems=5
ManualAddingOfItems=enabled
ViewList[]=my_events
ViewName[my_events]=Upcoming events
```

Die Schlüssel sind die des mitgelieferten Blocks `[ItemList]` in `extension/ezflow/settings/block.ini`. Ein Block
rendert `block/view/view.tpl` (`extension/ezflow/design/standard/templates/block/view/view.tpl`, das
`$block.valid_nodes` auflistet); jede Ansicht erhält über einen Override ihr eigenes Template, wie es das mitgelieferte
`extension/ezflow/design/ezflow/override/templates/block/itemlist1.tpl` tut:

```ini
# settings/siteaccess/<site>/override.ini.append.php
[block_my_events]
Source=block/view/view.tpl
MatchFile=block/my_events.tpl
Subdir=templates
Match[view]=my_events
```

Das ist dieselbe Form wie die Regeln, die der Installer des Pakets `ezflow_site` für die mitgelieferten Blöcke schreibt
(zum Beispiel `[block_itemlist1]` mit `Source=block/view/view.tpl`, `MatchFile=block/itemlist1.tpl`,
`Match[view]=itemlist1`).

## Die Cronjobs

```bash
php runcronjobs.php ezflow          # ezflowupdate.php: rotates and updates blocks
php runcronjobs.php ezflow-cleanup  # ezflowcleanup.php
```

Ohne sie erscheinen geplante Elemente nicht. Nehmen Sie beide in cron auf ([Cronjobs](../../../anleitungen/cronjobs.md)).

## ezflow oder der Layouts-Editor

| | ezflow | Layouts-Editor |
|---|---|---|
| Wo die Seite liegt | in einem Inhaltsattribut des Objekts (mit ihm versioniert) | in eigenen Layout-Tabellen, zugeordnet durch Regeln |
| Einen Kopf-/Fußbereich wiederverwenden | pro Seite | gemeinsame Layouts und verknüpfte Zonen |
| Ein Layout für viele Seiten | nein, jede Seite hat ihr eigenes | ja, durch Regeln |
| Zeitplanung und Zeitleiste | ja | Zeitbedingungen an Regeln |
| Blockkatalog | `block.ini` | 46 Blocktypen plus Template-Blöcke |
| Wählen für | bestehende ezflow-Websites | neue Websites |

Bestehende ezflow-Websites funktionieren weiter; neue Landingpages sind mit dem Layouts-Editor einfacher
([Landingpages mit dem Layouts-Editor](landingpages-mit-dem-layouts-editor.md)).

## Verwandte Seiten

- [ezflow](../../../funktionen/6.0/erweiterungen/ezflow.md)
- [ezwebin](../../../funktionen/6.0/erweiterungen/ezwebin.md)
- [Cronjobs für Anwendungen](../anwendungsentwickler/cronjobs-fuer-anwendungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
