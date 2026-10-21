---
title: "Kopf- und Fußzeilenmenü planen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1008
summary: Im Theme von Fit & Healthy sind das Kopfmenü und die erste Zeile der Fußzeile Listen in der menu.ini, die zweite Zeile der Fußzeile ist Inhalt; planen Sie, wer was pflegt.
keywords: Menüs, Kopfmenü, Fußzeilenmenü, MainMenuID, FooterMenuID, Fußzeilen-Links, Planung, menus, header menu, footer menu, footer links, planning
type: help
---
# Kopf- und Fußzeilenmenü planen

Vor dem Livegang wissen, wie jeder Menüeintrag geändert wird.

*Lebenszyklus: Planen, Beispiel 9 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Einstellungen und Templates](#mit-einstellungen-und-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Der Layout-Editor enthält diese Menüs nicht: Kopf- und Fußzeile sind Template-Blöcke.

## Mit Einstellungen und Templates

1. Kopfzeile: `menu.ini [SiteInfo] MainMenuID[]`. Erste Zeile der Fußzeile: `FooterMenuID[]`. Zweite Zeile der Fußzeile: Menu items unter **Media > Configuration > Footer links**.
2. Für ein Menü, das dem Baum folgt, planen Sie einen Template-Override von `pagelayout/header/main_menu.tpl` ([Ein Kopfmenü, das dem Baum folgt](../../kopfmenue-aus-dem-baum.md)).

## Welchen Weg Sie wählen

Listen von Knoten-IDs passen zu stabilen Menüs; inhaltsgesteuerte Menüs passen zu Bereichen, die Redakteure hinzufügen.

## Prüfen

- Jeder Menüeintrag des Designs hat im Plan eine Quelle.

## Verwandte Seiten

- [Einen Eintrag zum Kopfmenü hinzufügen](../../kopfmenue-eintrag-hinzufuegen.md)
- [Die Einträge des Fußzeilenmenüs](../../eintraege-des-fusszeilenmenues.md)
- [Beispiele zur Planung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
