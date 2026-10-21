---
title: /rss/feed
audience: developer, admin
category: examples
summary: "Die Ansicht rss/feed: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, rss, feed, rss/feed, module, view, URL
type: generated
generated: exp:help:examples
---
# /rss/feed

Eine Ansicht des Moduls `rss`, definiert in `kernel/rss/module.php` und ausgeführt von `kernel/rss/feed.php`.

## Adresse

```text
/rss/feed/<RSSFeed>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `RSSFeed`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`rss/feed`](../../richtlinien/rss.md#feed)

Eine Richtlinie auf `rss/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/rss/feed/rssfeed'|ezurl}>rss/feed</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/rss/feed/rssfeed' );
```

## Siehe auch

- [Alle Ansichten von rss](README.md)
- [Die Richtlinien von rss](../../richtlinien/rss.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/rss/module.php`, `kernel/rss/feed.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
