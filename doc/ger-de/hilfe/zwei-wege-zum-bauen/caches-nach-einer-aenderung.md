---
title: Caches nach einer Änderung
audience: admin, website-creator, designer, developer, editor
category: build-two-ways
order: 170
summary: Welcher Cache nach welcher Art von Änderung zu leeren ist (Menü-Einstellungen, Templates, Layouts, Inhalte), im Kasten Cache leeren der Administration und mit exp:cache, und was jede Auswahl abdeckt.
keywords: Cache, Cache leeren, Template & Inhalt, exp:cache, exphttpcache, kompilierte Templates, template-block, View-Cache, INI-Cache, Velocity, Layouts-Resolver, cache, clear cache, Template & content, compiled templates, content view cache, ini cache, layouts resolver
type: help
---
# Caches nach einer Änderung

Exponential hält mehrere Caches, damit Seiten schnell sind. Nach einer Änderung muss der richtige weg. Diese Tabelle zeigt,
was beim Schreiben dieser Seiten auf der Site Fit & Healthy nötig war.

## Inhalt

1. [Die Tabelle](#die-tabelle)
2. [Der Kasten Cache leeren](#der-kasten-cache-leeren)
3. [Die Konsole](#die-konsole)
4. [Velocity](#velocity)

## Die Tabelle

| Sie haben geändert | Leeren | Warum |
|---|---|---|
| Eine Inhaltsseite (veröffentlicht) | meistens nichts | Das Veröffentlichen leert die Caches der Seite; auf der Testkopie wurden direkt nach einem Veröffentlichen auch andere Seiten frisch dargestellt (Seiten-Cache verfehlt) |
| `menu.ini` (IDs des Kopf- oder Fußzeilenmenüs) | Template & Inhalt | Die Menü-Templates werden mit den Werten der Einstellung darin kompiliert |
| Eine andere INI-Einstellung, die in einem Template mit `ezini()` gelesen wird | Template & Inhalt | Derselbe Grund |
| Eine INI-Einstellung, die nur in PHP gelesen wird | INI-Einstellungen (das Speichern in der Administration oder `exp:ini` erledigt das) | Der INI-Cache |
| Eine vorhandene `.tpl`-Datei | Template & Inhalt | Kompiliertes Template, Template-Blöcke, View-Cache, Seiten-Cache |
| Eine neue `.tpl`-Datei (ein neuer Override) | Template & Inhalt | Die Override-Liste (`template-override`) gehört zum Tag `template` |
| Ein Layout, im Layout-Editor veröffentlicht | Template & Inhalt, wenn eine Seite noch die alten Blöcke zeigt | Siehe [Warum erscheint meine Änderung nicht?](warum-erscheint-meine-aenderung-nicht.md) |
| Ein Stylesheet oder Skript des Designs | Template & Inhalt | Die gepackten Dateien (`ezjscore-packer`) und die Seiten, die sie verlinken |

## Der Kasten Cache leeren

Der Kasten **Cache leeren** rechts in der Administration bietet:

| Auswahl | Leert |
|---|---|
| Alle Caches | jeden Cache |
| Inhalte | die Caches mit dem Tag `content` |
| Inhalt - Knoten, Inhalt - Teil der Baumstruktur | den View-Cache einer Seite oder eines Zweigs (aus der Ansicht einer Seite) |
| Template | die Caches mit dem Tag `template` |
| Template & Inhalt | die Tags `template` und `content` |
| INI-Einstellungen | den Tag `ini` |
| Statisch | den statischen Seiten-Cache, neu erzeugt |

![Der Kasten Cache leeren mit der Auswahlliste und der Schaltfläche Leeren](../../../eng-us/help/build-two-ways/images/hm-07-clear-cache-panel.webp)

Mit den Tags `template` und `content` zusammen werden diese geleert (aus einem echten Lauf): `exphttpcache` (Seiten-Cache),
`template` (kompilierte Templates), `template-block`, `template-override`, `texttoimage`, `design_base`,
`ezjscore-packer`, `content` (View-Cache), `querycache`, `classid`, `sortkey`, `urlalias`, `rss_cache`,
`content_tree_menu`, `state_limitations`, `content_language` und `rest`: 17 Caches.

## Die Konsole

```text
./console exp:cache clear --tag=template,content     # = Template & content
./console exp:cache clear --id=exphttpcache          # one cache by id
./console exp:cache list                             # every cache, id, tags
./console exp:cache clear --tag=template --dry-run   # what would be cleared
```

`exp:cache` endet mit `PASS` oder `FAIL`. Auf der Testkopie dauerte das Leeren von `template,content` 2,0 bis 2,5 Sekunden,
die Darstellung der ersten Seite danach 2,9 bis 3,4 Sekunden, die der nächsten 22 bis 102 Millisekunden
([Gemessene Schritte und Zeiten](gemessene-schritte-und-zeiten.md)).

## Velocity

Eine Site, die Exponential Velocity ausliefert, hat einen eigenen Antwort-Cache und Worker, die die Einstellungen ihres
Warm-ups behalten. Nach einer Änderung an Einstellungen rät `exp:ini` dort selbst zu `./console exp:velocity restart`.

## Verwandte Seiten

- [Fehlerbehebung beim Kopfmenü](kopfmenue-fehlerbehebung.md)
- [Warum erscheint meine Änderung nicht?](warum-erscheint-meine-aenderung-nicht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
