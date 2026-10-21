---
title: Eine Integration planen
audience: customer, businesses, partner
category: customer-success
order: 11
summary: Ein Planungsleitfaden und eine Vorlage für Website-Betreiber, die eine Exponential-Website mit einem anderen System verbinden - CRM, Shopsystem, Newsletter-Werkzeug, App, Intranet-Verzeichnis, Analyse -, die Wahl der Schnittstelle, Konto und Zugangsdaten, Datenschutz, Umgang mit Ausfällen und Zuständigkeit.
keywords: Integration, Planung, CRM, API, REST, Remote-Dienste, Feeds, Syndikation, API-Schlüssel, Datenschutz, Zuständigkeit, Vorlage, integration, planning, remote services, syndication, API key, data protection, ownership, template
type: help
---
# Eine Integration planen

Diese Seite ist für den Betreiber, der gebeten wird, die Website mit einem anderen System zu verbinden. Jede
Integration ist ein kleines Projekt: Sie braucht einen Zweck, eine Schnittstelle, ein Konto, einen Plan für den Fall,
dass sie ausfällt, und eine zuständige Person. Exponential bietet mehrere Schnittstellen; Ihr Partner wählt die
technische, aber die Entscheidungen auf dieser Seite treffen Sie.

## Inhalt

1. [Die Schnittstellen, die Exponential bietet](#die-schnittstellen-die-exponential-bietet)
2. [Sieben Fragen](#sieben-fragen)
3. [Datenschutz](#datenschutz)
4. [Wenn sie ausfällt](#wenn-sie-ausfällt)
5. [Vorlage für einen Integrationsplan](#vorlage-für-einen-integrationsplan)

## Die Schnittstellen, die Exponential bietet

| Schnittstelle | Geeignet für | Lesen Sie |
|---|---|---|
| REST-API (`ezprestapi`) | Apps und Frontends, die Inhalte lesen; Schreiben ab v2 | [Die REST-API für Betreiber](rest-api-fuer-betreiber.md) |
| Remote-Dienste (`expservices`) | Skripte und Portale, die tun, was die Administration tut | [Remote-Dienste (expservices)](remote-dienste-expservices.md) |
| RSS-, Atom- und Podcast-Feeds | andere Websites, Aggregatoren und Verzeichnisse, die Ihre Neuigkeiten lesen | [RSS- und Podcast-Feeds](rss-und-podcast-feeds.md) |
| RSS-Import | den Feed einer anderen Website als Inhalt übernehmen | [RSS-Feeds](../../../anleitungen/rss-feeds.md) |
| Die Erweiterung `syndication` | Inhalte zwischen Installationen teilen, in eine Richtung oder in beide | [Feeds und Syndikation](../../unternehmen/mobil-und-headless/feeds-und-syndication.md) |
| Anmeldung über ein Verzeichnis (LDAP und andere) | Mitarbeitende melden sich mit ihrem Firmenkonto an | [Anmeldung über Verzeichnisse und LDAP](../../unternehmen/intranets/verzeichnisanmeldung-und-ldap.md) |
| Payment-Gateways | die Zahlungen des Shops | [Zahlungsanbieter für Unternehmen](../../unternehmen/shops/zahlungsanbieter-fuer-unternehmen.md) |
| CSV-Export | Formulareinsendungen, Kontenlisten, Listen von Unterelementen | [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md) |

## Sieben Fragen

1. **Wozu dient sie?** Ein Satz; was sich für Besucher oder Mitarbeitende ändert.
2. **In welche Richtung?** Die Website liest, die Website wird gelesen, oder beides.
3. **Welche Daten?** Inhalte, Benutzer, Bestellungen, Formulareinsendungen; wie viel, wie oft.
4. **Welches Konto?** Ein eigenes Konto mit einer eng gefassten Rolle, nicht das Konto einer Person
   ([Die REST-API für Betreiber](rest-api-fuer-betreiber.md)).
5. **Welche Zugangsdaten?** OAuth2 für Apps, deren Benutzer sich anmelden, ein persönlicher API-Schlüssel oder ein
   Token für Remote-Dienste für ein Programm ([Apps und API-Schlüssel](../../unternehmen/mobil-und-headless/apps-und-api-schluessel.md)).
6. **Wer ist zuständig?** Eine Person in Ihrer Organisation, die darüber entscheidet, und ein Partner, der sie pflegt.
7. **Was kostet der Betrieb?** Hosting, Lizenzen des anderen Systems, die Wartungszeit des Partners.

## Datenschutz

- Halten Sie fest, welche personenbezogenen Daten von wo nach wo gehen, und warum. Dies ist keine Rechtsberatung;
  fragen Sie die Person, die für den Datenschutz verantwortlich ist ([Personenbezogene Daten in Exponential](../../unternehmen/sicherheit/personenbezogene-daten-in-exponential.md)).
- Halten Sie Zugangsdaten aus Dokumenten und E-Mails heraus; Schlüssel und Tokens werden einmal angezeigt, und
  gespeichert wird nur ein Hash ([Persönliche API-Schlüssel](../../../anleitungen/api-schluessel.md)).
- Jedes Ereignis zu API-Schlüsseln und jeder Schreibvorgang über Remote-Dienste steht im Prüfprotokoll, das Ihnen einen
  Nachweis dessen gibt, was die Integration getan hat ([Das Prüfprotokoll](../../../../eng-us/features/6.0/audit-trail.md)).

## Wenn sie ausfällt

| Frage | Zu vereinbarende Antwort |
|---|---|
| Wie bemerken wir es? | ein Alarm, ein täglicher Bericht oder eine Person, die nachsieht |
| Was tut das andere System, wenn die Website nicht antwortet (Wartung, Upgrade)? | später erneut versuchen, in eine Warteschlange stellen oder zwischengespeicherte Daten zeigen ([Zuverlässigkeit des App-Backends](../../unternehmen/mobil-und-headless/zuverlaessigkeit-des-app-backends.md)) |
| Was tut die Website, wenn das andere System nicht antwortet? | |
| Wer kann sie abschalten, und wie? | die Zugangsdaten widerrufen, das Konto deaktivieren oder der Schalter des Dienstes |
| Wer wird informiert? | |

Informieren Sie die Person, die die Integration pflegt, vor jedem Upgrade der Website
([Release- und Upgrade-Planung](../../fuehrung/release-und-upgrade-planung.md)).

## Vorlage für einen Integrationsplan

**Vorlage.**

```text
INTEGRATION PLAN
Name: ______________________   Owner (our side): ______________   Maintainer: ______________
Purpose (one sentence): ________________________________________________
Other system: ______________   Direction: site reads / site is read / both
Interface: REST API / remote services / feed / import / syndication / other: __________
Data exchanged: ________________________________   Personal data? yes / no
Account used on the site: ______________   Role: ______________
Credential: OAuth2 / API key / token   Expires: __________   Stored in: ______________
Failure: how noticed ______________   who switches off ______________
Reviewed with data protection on: __________   Go-live date: __________
Next review: __________
```

## Verwandte Seiten

- [Mobil und Apps](mobil-und-apps.md)
- [Über Netzwerke und Partner verkaufen](../../unternehmen/mobil-und-headless/verkaufen-ueber-netzwerke-und-partner.md)
- [Schätzen und Abgrenzen mit Erweiterungen](../../fuehrung/schaetzen-und-abgrenzen-mit-erweiterungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
