---
title: Ihren Feed prüfen
audience: podcaster
category: podcasting-streaming
order: 21
summary: Wie Sie einen Podcast-Feed von Exponential selbst prüfen - im Browser, in der RSS-Liste und auf der Kommandozeile mit curl - mit Headern, Kanal, dem Enclosure jeder Episode, Range-Anfragen, Caching und den häufigsten Fehlern.
keywords: Feed prüfen, Feed validieren, curl, Enclosure, Range, Content-Type, application/rss+xml, Last-Modified, 304, Cache, rss_cache, Feed-Adresse, Test, abgemeldet, check feed, validate feed, cache, feed address, test, signed out
type: help
---
# Ihren Feed prüfen

Diese Seite ist für Produzenten und Administratoren, die sicher sein möchten, dass ein Podcast-Feed stimmt, bevor ein
Verzeichnis oder ein Hörer das Gegenteil feststellt. Führen Sie diese Prüfungen nach dem Aufbau des Feeds durch, nach
jeder Änderung an einem Export und nach jeder Änderung am Hostnamen, an HTTPS oder am Proxy der Website.

## Inhalt

1. [In der RSS-Liste](#in-der-rss-liste)
2. [Im Browser](#im-browser)
3. [Auf der Kommandozeile](#auf-der-kommandozeile)
4. [Die Episoden](#die-episoden)
5. [Wenn eine Änderung nicht erscheint](#wenn-eine-änderung-nicht-erscheint)
6. [Checkliste](#checkliste)

## In der RSS-Liste

**Setup > RSS** zeigt eine Karte pro Export ([RSS-Feeds](../../anleitungen/rss-feeds.md#3-eine-export-karte)). Prüfen Sie:

- **Aktiv** und das Format *Apple Podcasts (RSS 2.0 + iTunes)*;
- **Feed-Adresse**: der öffentliche Host, `https://`, dann `/rss/feed/<access URL>`;
- **Quellen**: der Ordner der Sendung, die Episodenklasse, kein Hinweis, dass etwas fehlt;
- keine Markierung **Braucht Aufmerksamkeit**. Sie nennt eine fehlende Adresse, keine Quelle oder eine Quelle, die
  nicht mehr existiert.

## Im Browser

Öffnen Sie die Feed-Adresse in einem privaten Fenster, damit Sie sie so sehen wie ein Besucher. Die meisten Browser
zeigen das XML an oder bieten an, es herunterzuladen; verwenden Sie bei Bedarf "Quelltext anzeigen". Achten Sie auf:

- `<rss version="2.0" ...>` mit dem Namespace `itunes`;
- `title`, `description`, `itunes:image href="..."`, `itunes:category`, `itunes:owner` des Kanals;
- ein `<item>` pro Episode, neueste zuerst;
- in jedem Element ein `<enclosure url="..." length="..." type="..."/>`.

## Auf der Kommandozeile

Der Feed wird mit `Content-Type: application/rss+xml` und einem Header `Last-Modified` ausgeliefert; eine Anfrage mit
passendem `If-Modified-Since` erhält `304 Not Modified` (`kernel/private/classes/views/rss/feed.php`).

```bash
# headers of the feed
curl -sI https://www.example.com/rss/feed/example-show

# the first lines of the feed
curl -s https://www.example.com/rss/feed/example-show | head -40

# headers of an episode file: size, type, range support
curl -sI "https://www.example.com/content/download/123/456/example-show-042.mp3"

# a range request, as players and apps make it: expect 206 Partial Content
curl -s -o /dev/null -w '%{http_code}\n' -H 'Range: bytes=1024-2047' \
  "https://www.example.com/content/download/123/456/example-show-042.mp3"
```

Ersetzen Sie Host, Zugriffs-URL und Download-Adresse durch Ihre eigenen; kopieren Sie die Download-Adresse aus der
`enclosure url` des Feeds. Die Header der Datei stammen aus `eZFile::downloadHeaders()`
(`lib/ezfile/classes/ezfile.php`): `Content-Length`, `Accept-Ranges: bytes`, `Content-Disposition` und
`Content-Type`.

## Die Episoden

Vergleichen Sie bei jeder neuen Episode den Feed mit der Datei:

| Im Feed | Muss übereinstimmen mit |
|---|---|
| `enclosure length` | der `Content-Length` einer vollständigen Anfrage nach der Datei |
| `enclosure type` | einem Audio- oder Videotyp (`audio/mpeg`, `video/mp4`), nie `application/octet-stream` |
| `enclosure url` | öffnet sich und spielt abgemeldet ab |
| `guid` | bei einer vorhandenen Episode derselbe Wert wie beim letzten Mal |
| `itunes:duration` | vorhanden bei einem CBR-MP3; kann sonst fehlen, was zulässig ist |
| `link` | öffnet die Episodenseite auf der öffentlichen Website |

## Wenn eine Änderung nicht erscheint

Der Feed wird `settings/site.ini [RSSSettings] CacheTime` Sekunden lang gecacht, im Auslieferungszustand 1200.
**Zuletzt geschrieben** auf der RSS-Karte sagt, wann die gecachte Kopie geschrieben wurde. Warten Sie, oder bitten Sie
einen Administrator, den RSS-Cache zu leeren:

```bash
php bin/php/ezcache.php --clear-id=rss_cache --allow-root-user
```

Podcast-Apps und Verzeichnisse cachen Feeds auch auf ihrer Seite, nach ihrem eigenen Zeitplan; Exponential kann sie
nicht zwingen, früher abzurufen.

## Checkliste

- [ ] Der Feed antwortet mit `200` und `application/rss+xml`.
- [ ] Adressen verwenden den öffentlichen Host und `https://`.
- [ ] Die Adresse des Titelbilds öffnet das Bild.
- [ ] Jedes Element hat ein Enclosure mit echtem Typ und echter Länge.
- [ ] Range-Anfragen antworten mit `206`.
- [ ] Alte Episoden haben ihre `guid` behalten.
- [ ] Keine Test- oder Entwurfsepisode ist aufgeführt.

## Verwandte Seiten

- [Der Podcast-Feed erklärt](der-podcast-feed-erklaert.md)
- [Fehlersuche bei Feeds und Medien](fehlersuche-bei-feeds-und-medien.md)
- [Bei Podcast-Verzeichnissen einreichen](bei-podcast-verzeichnissen-einreichen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
