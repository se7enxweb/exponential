---
title: Einen RSS-Feed der Bekanntmachungen erstellen
audience: government, editor, admin
category: public-sector
order: 220
summary: Für Redakteure mit der Richtlinie rss/edit - unter Setup > RSS einen RSS-Export des Ordners der öffentlichen Bekanntmachungen anlegen, Format, Einträge und Attribute wählen, den Feed öffnen und ihn von der Seite der Bekanntmachungen aus verlinken.
keywords: RSS, Feed, Bekanntmachungen, RSS-Export, Setup RSS, Neuer Export, Zugriffs-URL, rss/feed, Quellen, Unterknoten, Nur Hauptknoten, CacheTime, notices, RSS export, New export, access URL, sources, subnodes, Main node only
type: help
---
# Einen RSS-Feed der Bekanntmachungen erstellen

Diese Seite ist für Redakteure oder Administratoren, die Medien, Partner-Sites und Bürgern einen Feed der öffentlichen
Bekanntmachungen anbieten. Ein Feed ist ein **RSS-Export** unter **Setup > RSS** (`/rss/list`); er braucht die
Richtlinie `rss/edit`, und der Siteaccess für Redakteure bietet das RSS-Modul nicht an
([RSS-Feeds](../../../../anleitungen/rss-feeds.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Sie können **Setup > RSS** in der Administration öffnen.
- Sie kennen den Ordner der Bekanntmachungen und den Bezeichner der Klasse für Bekanntmachungen.
- Wählen Sie eine kurze, beständige Zugriffs-URL, zum Beispiel `notices`.

## Schritte

1. **Setup > RSS**, **Neuer Export**.
2. **Der Feed**: Name „Öffentliche Bekanntmachungen“, eine Beschreibung, **Feed-Format** *RSS 2.0*, Zugriffs-URL
   `notices`, markieren Sie **Aktiv**.
3. **Was der Feed listet**: Anzahl der Einträge (zum Beispiel 20), markieren Sie **Nur Hauptknoten**.
4. **Quelle hinzufügen**: Wechseln Sie mit **Durchsuchen** zum Ordner der Bekanntmachungen und wählen Sie ihn aus;
   markieren Sie **Unterknoten**, wenn die Bekanntmachungen in Jahresordnern liegen; wählen Sie die Klasse der
   Bekanntmachungen und klicken Sie auf **Setzen**; wählen Sie das Titelattribut (die Überschrift) und das
   Beschreibungsattribut (den Text).
5. **OK**. Die Karte zeigt die Adresse des Feeds (`/rss/feed/notices`) und **Feed öffnen**.
6. Verlinken Sie den Feed von der Seite der Bekanntmachungen aus: Bearbeiten Sie die Beschreibung des Ordners und
   fügen Sie einen Link „Bekanntmachungen als RSS-Feed“ hinzu.

## Das Ergebnis prüfen

- [ ] **Feed öffnen** zeigt die neuesten Bekanntmachungen.
- [ ] Ein Feedreader abonniert die Adresse und zeigt Einträge.
- [ ] Eine neue Bekanntmachung erscheint nach der Cache-Zeit des Feeds (`site.ini [RSSSettings] CacheTime`) im Feed;
  für eine dringende Prüfung leert ein Administrator den RSS-Cache
  (`php bin/php/ezcache.php --clear-id=rss_cache`).

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Die Karte sagt **Braucht Aufmerksamkeit** | keine Adresse, keine Quelle oder eine Quelle, die nicht mehr existiert; bearbeiten und korrigieren |
| Die Zugriffs-URL später ändern | die Abonnenten verlieren den Feed; wählen Sie sie einmal |
| Versteckte Bekanntmachungen | öffnen Sie den Feed abgemeldet, um zu bestätigen, dass er nur listet, was Besucher sehen dürfen |
| Den Export entfernen | die Adresse antwortet nicht mehr; deaktivieren Sie ihn zuerst (**Aktiv** abwählen) und informieren Sie die Abonnenten |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [RSS-Feeds (Aufbau)](../../../../../eng-us/help/government/building/news-and-alerts/rss-feeds.md)
- [Änderungen mit Benachrichtigungen verfolgen](aenderungen-mit-benachrichtigungen-verfolgen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
