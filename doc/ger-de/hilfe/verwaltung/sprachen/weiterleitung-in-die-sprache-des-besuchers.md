---
title: Weiterleitung in die Sprache des Besuchers
audience: government, admin, website-creator
category: public-sector
order: 70
summary: Wie die Startseite einer mehrsprachigen öffentlichen Website jeden Besucher einmalig in seine Sprache schicken kann, nach Host und Browsersprache, mit DefaultHostUriMatchMapItems, ohne Cookies, und wie die Wahl beim Besucher bleibt.
keywords: Sprachweiterleitung, Accept-Language, Browsersprache, DefaultHostUriMatchMapItems, DefaultHostUriRedirect, Startseite, Sprachauswahl, kein Cookie, 302, language redirect, browser language, start page, language selection, no cookie
type: help
---
# Weiterleitung in die Sprache des Besuchers

Diese Seite richtet sich an Administratoren und Websiteersteller, deren Website ihre Sprachen unter eigenen Pfaden
führt (`/fr`, `/en`) und die möchten, dass `www.example.gov/` jeden Besucher zu einer Sprache führt. Exponential tut
das mit `site.ini [SiteAccessSettings] DefaultHostUriMatchMapItems[]`
([Siteaccess nach Host-Präfix und die Weiterleitung zur Sprache](../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md)).

## Inhalt

1. [Wie entschieden wird](#wie-entschieden-wird)
2. [Einrichten](#einrichten)
3. [Anzeigen statt weiterleiten](#anzeigen-statt-weiterleiten)
4. [Die Wahl des Besuchers respektieren](#die-wahl-des-besuchers-respektieren)
5. [Prüfen](#prüfen)

## Wie entschieden wird

Wenn keine Regel von `MatchOrder` auf eine Adresse passt (zum Beispiel `/` oder `/contact` ohne Sprachsegment), werden
die Einträge von `DefaultHostUriMatchMapItems[]` geprüft. Es gewinnt der erste Eintrag für den Host, dessen Sprache der
Browser akzeptiert, wobei die Sprachen des Browsers von der meistgewünschten an durchprobiert werden; passt keiner, gilt
der erste Eintrag für den Host ohne Sprache. Die Weiterleitung ist ein 302, einmalig, und behält den Rest der Adresse
und die Abfrage bei: `/` führt zu `/fr`, `/news/an-article` zu `/fr/news/an-article`. Es wird kein Cookie gesetzt, und
jede Seite hinter der Weiterleitung hat eine Adresse pro Sprache, was Caches gut verarbeiten.

## Einrichten

```ini
# settings/override/site.ini.append.php
[SiteAccessSettings]
DefaultHostUriMatchMapItems[]=www.example.gov;fr;site_fr;strict;fr
DefaultHostUriMatchMapItems[]=www.example.gov;en;site_en
```

Das Format ist `host;uri;siteaccess[;method[;language]]`. Hier geht ein Browser, der Französisch akzeptiert, nach
`/fr`; jeder andere Browser geht nach `/en`, dem Eintrag ohne Sprache. `fr` erfasst auch `fr-CA`. Ein Eintrag, dessen
Siteaccess nicht in `AvailableSiteAccessList` steht, wird ignoriert und protokolliert.

Bei zwei gleichrangigen Amtssprachen entscheiden Sie, welche die Voreinstellung für Browser ist, die keine von beiden
akzeptieren, und halten Sie diese Entscheidung schriftlich fest.

## Anzeigen statt weiterleiten

`DefaultHostUriRedirect=disabled` zeigt die Seite unter der Adresse selbst an, statt weiterzuleiten. Manche Stellen
bevorzugen eine neutrale Startseite, die alle Amtssprachen nebeneinander anbietet; das ist eine eigene Seite in einem
Siteaccess für `/`, nicht diese Einstellung.

## Die Wahl des Besuchers respektieren

Die Sprache eines Browsers ist eine Vermutung. Bieten Sie den Sprachumschalter immer auf jeder Seite an, damit ein
Besucher, dessen Browser auf eine andere Sprache eingestellt ist, die gewünschte in einem Schritt erreicht (siehe „Der
Sprachumschalter“). Da die Weiterleitung nur für Adressen ohne Sprachsegment gilt, werden Links auf `/fr/...` oder
`/en/...` nie umgeleitet.

## Prüfen

```bash
curl -sI -H 'Accept-Language: fr-CA,fr;q=0.9' https://www.example.gov/ | grep -i '^location'
curl -sI -H 'Accept-Language: de' https://www.example.gov/ | grep -i '^location'
```

Der erste Befehl sollte auf `/fr` zeigen, der zweite auf `/en`.

## Verwandte Seiten

- [Sprachadressen: Pfad oder Host](sprachadressen-pfad-oder-host.md)
- [Sprachumschalter und Adressen pro Sprache](../../../../eng-us/help/creators/website-creators/language-switcher-and-language-urls.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
