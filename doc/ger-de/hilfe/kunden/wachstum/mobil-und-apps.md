---
title: Mobil und Apps
audience: customer, businesses, partner
category: customer-success
order: 8
summary: Die Sicht des Betreibers darauf, Menschen auf dem Smartphone und über Apps mit einer Exponential-Website zu erreichen - zuerst eine responsive Website, dann eine App oder ein Frontend auf der REST-API oder den Remote-Diensten, Feeds und wiederkehrende Besuche - mit den Fragen, die vor der Beauftragung einer App zu beantworten sind.
keywords: mobil, Apps, responsiv, Headless, REST-API, Remote-Dienste, API-Schlüssel, OAuth, Feeds, App-Backend, Website-Betreiber, mobile, apps, responsive, headless, REST API, remote services, API keys, feeds, app backend, site owner
type: help
---
# Mobil und Apps

Diese Seite ist für Betreiber, denen die Frage „Sollten wir eine App haben?“ gestellt wird oder die möchten, dass die
Website auf Smartphones besser funktioniert. Exponential bietet responsive Designs, eine REST-API für Inhalte,
persönliche API-Schlüssel und OAuth für Apps, Remote-Dienste, Feeds und einen optionalen Navigations-Cache im Browser
für wiederkehrende Besuche ([Mobil und Headless für Unternehmen](../../../../en/us/help/businesses/mobile-and-headless/README.md)). Die
meisten Organisationen sollten zuerst die Nutzung der Website auf dem Smartphone richtig hinbekommen.

## Inhalt

1. [Zuerst die Website](#zuerst-die-website)
2. [Die Kanäle im Überblick](#die-kanäle-im-überblick)
3. [Bevor Sie eine App beauftragen](#bevor-sie-eine-app-beauftragen)
4. [Wer was tut](#wer-was-tut)
5. [Den Betrieb sichern](#den-betrieb-sichern)

## Zuerst die Website

Eine responsive Website funktioniert auf jedem Smartphone ohne Installation. Prüfen Sie Ihre regelmäßig:

- [ ] Die Startseite, ein Artikel, ein Formular und die Suche funktionieren auf dem Smartphone.
- [ ] Bilder sind nicht größer als nötig; Seiten laden über eine Mobilverbindung schnell.
- [ ] Schaltflächen sind groß genug, und Formulare lassen sich leicht ausfüllen.
- [ ] Text lässt sich auf 200 % vergrößern, ohne seitliches Scrollen ([Barrierefreiheit pflegen](../alltag/barrierefreiheit-pflegen.md)).

Der Testplan für Unternehmen steht unter [Responsive Websites](../../../../en/us/help/businesses/mobile-and-headless/responsive-sites.md).
Auch Redakteure können auf dem Smartphone arbeiten: Die Administration hat responsive Designs
([admin3, die responsive Administration](../../../../en/us/features/6.0/admin3-responsive-admin.md)).

## Die Kanäle im Überblick

Aus [Mobil und Headless für Unternehmen](../../../../en/us/help/businesses/mobile-and-headless/README.md):

| Kanal | Was Exponential bietet | Für Betreiber |
|---|---|---|
| Besucher auf Smartphones | responsive öffentliche Designs | [Responsive Websites](../../../../en/us/help/businesses/mobile-and-headless/responsive-sites.md) |
| Ihre eigene App oder Ihr eigenes Frontend | die REST-API für Inhalte, Lesen in v1 und v2, Schreiben in v2 | [Die REST-API für Betreiber](rest-api-fuer-betreiber.md) |
| Programme und Integrationen | persönliche API-Schlüssel, OAuth2 für Apps, Tokens für Remote-Dienste | [Apps und API-Schlüssel](../../../../en/us/help/businesses/mobile-and-headless/apps-and-api-keys.md) |
| Fernverwaltung, Portale, Skripte | über tausend benannte Remote-Dienste | [Remote-Dienste (expservices)](remote-dienste-expservices.md) |
| Feed-Reader und Podcast-Verzeichnisse | Exporte als RSS, Atom, OPML und für Apple Podcasts | [RSS- und Podcast-Feeds](rss-und-podcast-feeds.md) |
| Wiederkehrende Besucher | ein optionaler Navigations-Cache im Browser | [Offline und wiederkehrende Besuche](../../../../en/us/help/businesses/mobile-and-headless/offline-and-repeat-visits.md) |

## Bevor Sie eine App beauftragen

| Frage | Warum |
|---|---|
| Was kann die App, was die Website nicht kann? | eine App ist ein zweites Produkt, das gepflegt werden muss |
| Liest sie nur Inhalte, oder schreibt sie auch (Kommentare, Bestellungen, Formulare)? | Schreiben braucht v2 der REST-API oder die Remote-Dienste, und sorgfältige Berechtigungen |
| Melden sich ihre Benutzer an? | Apps, deren Benutzer sich anmelden, verwenden OAuth2; ein Programm, das als ein Benutzer handelt, verwendet einen API-Schlüssel ([Apps und API-Schlüssel](../../../../en/us/help/businesses/mobile-and-headless/apps-and-api-keys.md)) |
| Wer baut und aktualisiert die App für jedes neue Smartphone-Betriebssystem? | die App ist nicht Teil von Exponential |
| Was geschieht, wenn die Website in Wartung ist? | die App ist darauf angewiesen, dass die API verfügbar ist ([Zuverlässigkeit des App-Backends](../../../../en/us/help/businesses/mobile-and-headless/app-backend-reliability.md)) |
| Welche Inhalte darf die App zeigen? | die API wendet dieselben Richtlinien an wie die Website |

Planen Sie sie wie jede Integration: [Eine Integration planen](eine-integration-planen.md).

## Wer was tut

| Aufgabe | Wer |
|---|---|
| Die Kanäle festlegen und wofür jeder da ist | Betreiber ([Mobile Strategie](../../../../en/us/help/businesses/mobile-and-headless/mobile-strategy.md)) |
| Die App bauen und veröffentlichen | App-Entwickler oder Partner |
| Die Zugangsdaten der App anlegen und einschränken | Administrator |
| Die Inhalte korrekt halten | das Redaktionsteam, wie für die Website |
| Die API verfügbar, überwacht und gesichert halten | Hoster oder Partner |

## Den Betrieb sichern

- Überprüfen Sie die Zugangsdaten der App einmal im Jahr mit der [Zugriffsüberprüfung](../../../../en/us/help/businesses/security/access-reviews.md):
  Schlüssel werden vom Besitzer oder einem Administrator widerrufen und enden, wenn sie ablaufen oder ihr Besitzer
  deaktiviert wird ([Persönliche API-Schlüssel](../../../anleitungen/api-schluessel.md)).
- Informieren Sie den App-Entwickler vor Upgrades; fragen Sie Ihren Partner, was sich für API-Clients ändert
  ([REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)).
- Verwenden Sie vor jedem Release der App die [Mobil-Checkliste](../../../../en/us/help/businesses/mobile-and-headless/mobile-checklist.md).

## Verwandte Seiten

- [Mobil und Headless für Unternehmen](../../../../en/us/help/businesses/mobile-and-headless/README.md)
- [Headless mit der REST-API](../../../../en/us/help/businesses/mobile-and-headless/headless-with-the-rest-api.md)
- [Die REST-API für Betreiber](rest-api-fuer-betreiber.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
