---
title: "Ein neues Aussehen für Listen, von Redakteuren gewählt"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1405
summary: Einen Ansichtstyp hinzufügen (eine INI-Zeile, ein Template), statt die vorhandenen zu ändern.
keywords: Ansichtstyp, neues Aussehen, list_compact, Neugestaltung, Redakteure wählen, view type, new look, redesign, editors choose
type: help
---
# Ein neues Aussehen für Listen, von Redakteuren gewählt

Redakteure können eine kompakte Liste wählen, wo sie wollen; die alten Ansichten bleiben.

*Lebenszyklus: Neugestaltung, Beispiel 6 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Im Reiter **Design** bietet **Ansichtstyp** `list_compact` an.

## Mit Templates

1. `[BlockDefinition_list] ViewTypes[]=list_compact` in der `explayouts.ini.append.php` Ihrer Erweiterung; `explayouts/block/list/list_compact.tpl` in Ihrem Design ([Einen Ansichtstyp hinzufügen](../../ansichtstyp-hinzufuegen.md)).

## Welchen Weg Sie wählen

Neugestaltungen, die nur hinzufügen, lassen vorhandene Seiten unverändert.

## Prüfen

- Eine auf `list_compact` umgestellte Liste erzeugt `<ul class="list-compact">`.

## Verwandte Seiten

- [Einen Ansichtstyp hinzufügen](../../ansichtstyp-hinzufuegen.md)
- [Beispiele zur Neugestaltung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
