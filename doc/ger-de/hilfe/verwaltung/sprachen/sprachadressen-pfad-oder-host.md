---
title: "Sprachadressen: Pfad oder Host"
audience: government, admin, website-creator
category: public-sector
order: 60
summary: Wie Sie jeder Sprache einer öffentlichen Website in Exponential eine eigene Adresse geben (ein Pfad wie /fr/, ein Host wie fr.example.gov oder beides), die Zuordnungseinstellungen für jede Form und wie eine öffentliche Stelle wählt.
keywords: Sprach-URL, Adresse pro Sprache, Pfad, Host, Subdomain, MatchOrder, URIMatchType, HostMatchMapItems, HostUriMatchMapItems, HostMatchMethod, Siteaccess-Zuordnung, language URL, address per language, path, subdomain, siteaccess matching
type: help
---
# Sprachadressen: Pfad oder Host

Diese Seite richtet sich an Administratoren und Websiteersteller, die entscheiden, unter welchen Adressen die
Sprachfassungen einer Website erreichbar sind. Exponential entscheidet anhand der Adresse, welcher Siteaccess eine
Anfrage beantwortet; die Einstellungen stehen in `site.ini [SiteAccessSettings]`
([Sprachumschalter und Adressen pro Sprache](../../../../eng-us/help/creators/website-creators/language-switcher-and-language-urls.md),
[10.2 Siteaccesses und Adressen der Website](../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).

## Inhalt

1. [Die drei Formen](#die-drei-formen)
2. [Die Wahl für eine öffentliche Stelle](#die-wahl-für-eine-öffentliche-stelle)
3. [Pfad pro Sprache](#pfad-pro-sprache)
4. [Host pro Sprache](#host-pro-sprache)
5. [Testhosts](#testhosts)
6. [Nach der Änderung](#nach-der-änderung)

## Die drei Formen

| Form | Beispiel | Eingerichtet mit |
|---|---|---|
| Pfad pro Sprache | `www.example.gov/fr/` | `MatchOrder=uri`, `URIMatchType=element`, Siteaccess namens `fr` oder eine Zuordnungstabelle |
| Host pro Sprache | `fr.example.gov` oder eine eigene Domain pro Sprache | `MatchOrder=host`, `HostMatchMapItems[]=fr.example.gov;site_fr` |
| Host und Pfad | `www.example.gov/fr` auf einem Host, andere Hosts anderswo | `host_uri` mit `HostUriMatchMapItems[]` |

## Die Wahl für eine öffentliche Stelle

| Gesichtspunkt | Pfad | Host |
|---|---|---|
| Zertifikate und DNS | ein Host | ein Name pro Sprache |
| Gleichrangigkeit der Amtssprachen | die Standardsprache hat oft kein Präfix; geben Sie jeder Sprache ein Präfix, damit keine an erster Stelle steht | jede Sprache hat ihren eigenen Namen |
| Webanalyse und Protokolle | ein Host, Filter nach Pfad | nach Host getrennt |
| Späterer Wechsel | ein Wechsel des Schemas braucht Weiterleitungen | ebenso |

Was Sie auch wählen, verwenden Sie es überall und ändern Sie es nach dem Start nicht ohne einen Weiterleitungsplan.

## Pfad pro Sprache

```ini
# settings/override/site.ini.append.php
[SiteAccessSettings]
AvailableSiteAccessList[]=en
AvailableSiteAccessList[]=fr
RelatedSiteAccessList[]=en
RelatedSiteAccessList[]=fr
MatchOrder=uri
URIMatchType=element
URIMatchElement=1
```

Mit Siteaccesses namens `en` und `fr` erreichen `/en/...` und `/fr/...` diese. Behalten Sie die Einträge, die das
Installationsprogramm für die Administration geschrieben hat.

## Host pro Sprache

```ini
[SiteAccessSettings]
MatchOrder=host
HostMatchType=map
HostMatchMapItems[]=en.example.gov;site_en
HostMatchMapItems[]=fr.example.gov;site_fr
```

Tragen Sie jeden Host im DNS, im Webserver oder in Velocity und im Zertifikat ein
([Livegang: Hosts, HTTPS und Siteaccesses](../../../../eng-us/help/creators/website-creators/going-live-hosts-https.md)).

## Testhosts

`HostMatchMethod=start` lässt einen Testhost wie `fr.example.gov.test.local` denselben Siteaccess erreichen wie
`fr.example.gov`; es gibt die Methoden `strict` (Voreinstellung), `start`, `end` und `part`, global oder pro Eintrag.
Verwenden Sie `start` und `part` nur dort, wo der Webserver ausschließlich für Ihre eigenen Hosts antwortet
([Siteaccess nach Host-Präfix](../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md)).

## Nach der Änderung

Leeren Sie den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`) und, mit Velocity, dessen Antwort-Cache. Öffnen
Sie jede Sprachadresse abgemeldet und prüfen Sie `<html lang="...">` auf der Seite.

## Verwandte Seiten

- [Weiterleitung in die Sprache des Besuchers](weiterleitung-in-die-sprache-des-besuchers.md)
- [Der Sprachumschalter](der-sprachumschalter.md)
- [Mehrere Websites einrichten](../../../../eng-us/help/creators/website-creators/multisite-setup.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
