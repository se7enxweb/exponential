---
title: Die Startseite auf die Sprache des Besuchers umleiten
audience: government, admin
category: public-sector
order: 60
summary: Aufgabe für Administratoren - www.example.gov/ jeden Besucher einmal in die Sprache schicken lassen, die sein Browser verlangt, mit DefaultHostUriMatchMapItems, und das mit curl testen.
keywords: Sprachumleitung, Startseite, Browsersprache, language redirect, start page, Accept-Language, DefaultHostUriMatchMapItems, DefaultHostUriRedirect, browser language
type: help
---
# Die Startseite auf die Sprache des Besuchers umleiten

Diese Aufgabe richtet sich an Administratoren einer Site, deren Sprachen unter Pfaden liegen (`/en`, `/fr`).
Hintergrund: [Auf die Sprache des Besuchers umleiten](../../sprachen/weiterleitung-in-die-sprache-des-besuchers.md).

## Bevor Sie beginnen

- Ein Siteaccess je Sprache, erreichbar über ein Pfadsegment (siehe „Einer Sprache eine eigene Adresse geben“).
- Eine Entscheidung, welche Sprache Browser erhalten, die keine Ihrer Sprachen verlangen.

## Schritte

1. In `settings/override/site.ini.append.php`:

   ```ini
   [SiteAccessSettings]
   DefaultHostUriMatchMapItems[]=www.example.gov;fr;fr;strict;fr
   DefaultHostUriMatchMapItems[]=www.example.gov;en;en
   ```

   Format: `host;uri;siteaccess[;method[;language]]`. Der Eintrag ohne Sprache ist die Voreinstellung.
2. Leeren Sie den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini`.

## Prüfen

```bash
curl -sI -H 'Accept-Language: fr-CA,fr;q=0.9' https://www.example.gov/ | grep -i '^location'
curl -sI -H 'Accept-Language: de' https://www.example.gov/ | grep -i '^location'
```

Der erste zeigt auf `/fr`, der zweite auf `/en`. Adressen mit einem Sprachsegment werden nie umgeleitet.

## Probleme

| Problem | Lösung |
|---|---|
| Keine Umleitung | der Siteaccess eines Eintrags steht nicht in `AvailableSiteAccessList` (ignoriert und protokolliert), oder eine andere Prüfung der `MatchOrder` hat `/` zugeordnet |
| Sie möchten stattdessen eine neutrale Seite zur Sprachwahl | `DefaultHostUriRedirect=disabled` und eine eigene Seite |

## Verwandte Seiten

- [Siteaccess über Host-Präfix und die Umleitung auf die Sprache](../../../../funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
