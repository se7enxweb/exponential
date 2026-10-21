---
title: Warum erscheint meine Änderung nicht?
audience: editor, website-creator, designer, admin, developer
category: build-two-ways
order: 310
summary: Eine Checkliste für eine Änderung, die auf der Site nicht erscheint (Entwurf nicht veröffentlicht, verknüpfte Zone, falsche Layout-Zuordnung, falscher Siteaccess oder falsche Einstellungsdatei, kompiliertes Template, Seiten-Cache, Velocity), mit dem Befehl oder Bildschirm, der jeden Punkt belegt.
keywords: Änderung erscheint nicht, nicht aktualisiert, Entwurf, Layout veröffentlichen, verknüpfte Zone, Layout-Zuordnung, Regel, Siteaccess, Cache, exp:ini where, exp:layouts list, Fehlersuche, change not showing, not updated, draft, publish layout, linked zone, layout mapping, rule, siteaccess, cache, troubleshooting
type: help
---
# Warum erscheint meine Änderung nicht?

Arbeiten Sie von oben nach unten: Die ersten Ursachen sind die häufigsten.

| # | Prüfung | Wie man es sieht | Abhilfe |
|---|---|---|---|
| 1 | Die Layoutänderung ist noch ein **Entwurf** | Der Editor zeigt **Verwerfen** und **Layout veröffentlichen**; `exp:layouts list --drafts` listet Entwürfe | **Layout veröffentlichen** |
| 2 | Sie haben eine Zone bearbeitet, die **verknüpft** ist | Die Zone des Seitenlayouts ist mit einem gemeinsamen Layout verknüpft (`exp:layouts list`, Spalte *Links to*) | Das gemeinsame Layout bearbeiten oder die Verknüpfung der Zone aufheben ([Gemeinsame Layouts und Zonenverknüpfung](gemeinsame-layouts-und-zonenverknuepfung.md)) |
| 3 | Die Seite verwendet ein **anderes Layout** | Layouts > Layout-Zuordnungen: welche Zuordnung auf die Seite passt | Die Zuordnung ändern oder das passende Layout bearbeiten |
| 4 | Die Änderung steckt gar nicht in einem Layout | Kopf- und Fußzeilenmenü sind Listen in der `menu.ini`, dargestellt von Template-Blöcken | [Einen Eintrag zum Kopfmenü hinzufügen](kopfmenue-eintrag-hinzufuegen.md), [Die Einträge des Fußzeilenmenüs](eintraege-des-fusszeilenmenues.md) |
| 5 | Eine Einstellung landete in einer **Datei, die nicht gewinnt** | `./console exp:ini where <file>/<Block>/<Variable> <siteaccess>`; in der Administration *In N Dateien gesetzt* | Den als wirksam markierten Geltungsbereich schreiben |
| 6 | Sie haben einen **anderen Siteaccess** geändert | Die Auswahl des Siteaccess unter Setup > INI-Einstellungen; die Adresse der Seite | Den Siteaccess der Seite ändern |
| 7 | Das **kompilierte Template** enthält einen alten Wert | Mit `ezini()` gelesene Werte werden in das Template kompiliert | Den Cache **Template & Inhalt** leeren |
| 8 | Der **Seiten-Cache** antwortet | Antwort-Header `X-Exp-Cache: HIT` | Dasselbe Leeren; oder `./console exp:cache httpcache clear` |
| 9 | Die Worker von **Velocity** haben alte Einstellungen behalten | Die Site läuft auf Exponential Velocity; `exp:ini` hat seinen Hinweis ausgegeben | `./console exp:velocity restart` |
| 10 | Der **Browser** zeigt eine alte Kopie | Ein anderer Browser oder ein privates Fenster zeigt die Änderung | Ohne Browser-Cache neu laden |

## Der Beleg für jede Schicht

- **Datenbank (Layouts)**: `exp:layouts list` zeigt die veröffentlichten Layouts und ihre Verknüpfungen; `exp:layouts export
  --only=<identifier> --output=<file>.zip` schreibt ein Layout mit seinen Zonen, Blöcken, Parametern und Einträgen der
  Sammlungen (`layouts.json` im Archiv).
- **Einstellungen**: `exp:ini get <file>/<Block>/<Variable> -s <siteaccess>` zeigt den wirksamen Wert.
- **Templates**: das HTML der Site; Menüeinträge, Blöcke und Zonen tragen IDs und Klassen wie
  `menu-item-main_menu-location-id-<id>`, `ngl-block ngl-<block type>` und `zone zone-<identifier>`.
- **Caches**: `./console exp:cache list` listet sie; `X-Exp-Cache` sagt, ob eine Seite aus dem Seiten-Cache kam.

## Verwandte Seiten

- [Caches nach einer Änderung](caches-nach-einer-aenderung.md)
- [Fehlerbehebung beim Kopfmenü](kopfmenue-fehlerbehebung.md)
- [Gemeinsame Layouts und Zonenverknüpfung](gemeinsame-layouts-und-zonenverknuepfung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
