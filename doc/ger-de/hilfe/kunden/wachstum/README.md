---
title: Ihre Website ausbauen
audience: customer, businesses, project-manager
category: customer-success
order: 1
summary: Die Einstiegsseite für Website-Betreiber, die mehr aus einer bereits laufenden Exponential-Website herausholen wollen - Newsletter, Kampagnen-Landingpages, weitere Sprachen und Websites, Apps und APIs, der Schreibassistent, Feeds, Formulare und Mitgliederbereiche - mit jeder Seite des Bereichs und einem Weg, den nächsten Schritt zu planen.
keywords: Wachstum, Ausbau, Website-Betreiber, Newsletter, Landingpages, Layouts, Sprachen, Multisite, Mobil, REST-API, Remote-Dienste, Exponential Intelligence, Feeds, Formulare, Mitglieder, Roadmap, growth, site owner, landing pages, languages, multisite, mobile, REST API, remote services, feeds, forms, members, roadmap
type: help
---
# Ihre Website ausbauen

Dieser Bereich ist für Betreiber und Leitungen, deren Website gut läuft und die möchten, dass sie mehr leistet:
Leserinnen und Leser per E-Mail erreichen, Kampagnen durchführen, eine Sprache oder eine Marke hinzufügen, eine App
versorgen, einen Schreibassistenten Entwürfe vorbereiten lassen, Feeds und Podcasts veröffentlichen, Informationen
sammeln oder einen Mitgliederbereich eröffnen. Vieles davon kommt mit Exponential und muss nur eingeschaltet und mit
einer Routine versehen werden. Jede Seite sagt, was die Software bietet, was zu entscheiden ist, wer im Team die Arbeit
macht, was Sie Ihren Partner fragen sollten, und verweist auf die Seiten mit den technischen Schritten.

## Inhalt

1. [Bevor Sie ausbauen](#bevor-sie-ausbauen)
2. [Die Seiten dieses Bereichs](#die-seiten-dieses-bereichs)
3. [Empfohlene Lesereihenfolge](#empfohlene-lesereihenfolge)
4. [Was mitgeliefert wird, wofür ein Partner nötig ist](#was-mitgeliefert-wird-wofür-ein-partner-nötig-ist)

## Bevor Sie ausbauen

Jeder neue Kanal ist eine neue Routine für Ihr Team. Bevor Sie einen hinzufügen, prüfen Sie, dass die Grundlagen aus
[Ihre Website im Alltag betreiben](../alltag/README.md) vorhanden sind: Rollen, Freigaben wo nötig und ein
wöchentlicher Blick auf die Website. Planen Sie dann mit dem [Arbeitsblatt für die Wachstums-Roadmap](arbeitsblatt-wachstums-roadmap.md).

## Die Seiten dieses Bereichs

| Bereich | Seiten |
|---|---|
| E-Mail | [Newsletter](newsletter.md), [Newsletter-Routine](newsletter-routine.md) |
| Kampagnen | [Landingpages mit Layouts](landingpages-mit-layouts.md), [Checkliste für Kampagnen-Landingpages](checkliste-kampagnen-landingpages.md) |
| Mehr Zielgruppen | [Eine Sprache hinzufügen](eine-sprache-hinzufuegen.md), [Eine Website hinzufügen](eine-website-hinzufuegen.md) |
| Apps und Integrationen | [Mobil und Apps](mobil-und-apps.md), [Die REST-API für Betreiber](rest-api-fuer-betreiber.md), [Remote-Dienste (expservices)](remote-dienste-expservices.md), [Eine Integration planen](eine-integration-planen.md) |
| Schreibassistent | [Exponential Intelligence](exponential-intelligence.md), [KI-Entwürfe verantwortungsvoll nutzen](ki-entwuerfe-verantwortungsvoll-nutzen.md) |
| Syndikation | [RSS- und Podcast-Feeds](rss-und-podcast-feeds.md) |
| Besucher, die mitmachen | [Formulare und gesammelte Informationen](formulare-und-gesammelte-informationen.md), [Mitgliederbereiche und Registrierung](mitgliederbereiche-und-registrierung.md) |
| Planung | [Arbeitsblatt für die Wachstums-Roadmap](arbeitsblatt-wachstums-roadmap.md) |

## Empfohlene Lesereihenfolge

1. [Arbeitsblatt für die Wachstums-Roadmap](arbeitsblatt-wachstums-roadmap.md): Entscheiden Sie, was Sie wollen, in welcher Reihenfolge.
2. Die Seite des gewählten Bereichs.
3. Ihre Checkliste oder Routineseite, vor der ersten echten Nutzung.
4. [Eine Integration planen](eine-integration-planen.md), wann immer ein anderes System beteiligt ist.

## Was mitgeliefert wird, wofür ein Partner nötig ist

| Fähigkeit | Wird mit Exponential geliefert | Braucht meist einen Partner für |
|---|---|---|
| Newsletter (`cjw_newsletter`) | ja, in `composer.json` ([Newsletter für Unternehmen](../../unternehmen/newsletter/README.md)) | Klassen, Cronjobs, E-Mail-Transport, Absenderdomain |
| Landingpages (Exponential Layouts) | ja ([Der Layouts-Editor](../../layout-editor.md)) | neue Layout-Typen oder Block-Typen |
| Weitere Inhaltssprachen | ja ([Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)) | Siteaccess-Einstellungen, Sprachumschalter |
| Weitere Websites in einer Installation | ja ([Eine Installation, viele Websites](../../unternehmen/multisite/eine-installation-viele-websites.md)) | Siteaccess, DNS, Zertifikate |
| REST-API und API-Schlüssel | ja ([REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)) | die App oder das Frontend selbst |
| Remote-Dienste (`expservices`) | ja, in der Standarddistribution ([Remote-Dienste und Apps](../../../anleitungen/entfernte-dienste-und-apps.md)) | Skripte und Portale |
| Schreibassistent (`expai`) | ja, auf neuen Installationen aktiv; ohne Schlüssel eines Anbieters untätig (`README.md`) | Wahl des Anbieters, Cron, Datenrichtlinie |
| RSS-, Atom- und Podcast-Feeds | ja ([RSS-Feeds](../../../anleitungen/rss-feeds.md)) | Templates für die Feed-Ausgabe, falls nötig |
| Formulare und Registrierung von Mitgliedern | ja ([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)) | Formularklassen, CAPTCHA, Templates |

## Verwandte Seiten

- [Ihre Website im Alltag betreiben](../alltag/README.md)
- [Mobil und Headless für Unternehmen](../../unternehmen/mobil-und-headless/README.md)
- [Was Sie bauen können](../../../../eng-us/help/creators/website-creators/what-you-can-build.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
