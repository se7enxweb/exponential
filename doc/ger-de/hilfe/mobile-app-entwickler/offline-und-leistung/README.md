---
title: Offline und Leistung für Apps
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 600
summary: Die Einstiegsseite der Seiten zu Offline-Betrieb und Leistung für App-Entwickler - wo Antworten zwischengespeichert werden (der REST-Antwort-Cache, Velocity, Ihr Backend, die App), wie frisch Inhalte sind, Synchronisation zum Offline-Lesen und die Browserregeln (CORS, CSP, der Service Worker) für Web-Apps.
keywords: Leistung, Caching, offline, Synchronisation, Aktualität, REST-Antwort-Cache, Velocity, CORS, CSP, Service Worker, Web-App, PWA, Mobile App, performance, caching, sync, freshness, REST answer cache, service worker, web app, mobile app
type: help
---
# Offline und Leistung für Apps

Dieser Ordner richtet sich an App-Entwickler, die eine App möchten, die sich in einem langsamen Netz schnell anfühlt, im
Tunnel weiterarbeitet und den Server nicht mehr belastet als nötig. Er erklärt die Caches zwischen der App und den
Inhalten und was die App selbst tun muss.

## Inhalt

1. [Die Schichten](#die-schichten)
2. [Seiten in diesem Ordner](#seiten-in-diesem-ordner)

## Die Schichten

| Schicht | Wer sie betreibt | Enthält | Lesen |
|---|---|---|---|
| Der eigene Speicher der App | Sie | Antworten, Bilder, wartende Schreibzugriffe | [Caching in der App](caching-in-der-app.md), [Synchronisation und Offline-Lesen](synchronisation-und-offline-lesen.md) |
| Ihr Backend, falls vorhanden | Sie | gemeinsame Antworten für jedes Telefon | [Ein Backend für Ihre App](../anmeldung/ein-backend-fuer-ihre-app.md) |
| Der REST-Antwort-Cache | der Server, `rest.ini [CacheSettings]` | `GET`-Antworten je Benutzer, standardmäßig zehn Minuten | [Caching auf dem Server](caching-auf-dem-server.md) |
| Der Antwort-Cache von Velocity | der Server, wenn Velocity die Website ausliefert | anonyme Seiten; nie Anfragen mit einem `Authorization`-Header | dieselbe Seite |
| Der Navigations-Cache des Browsers | der Service Worker der Website | Seiten der Website für wiederholte Besuche; keine App-Daten | [Web-Apps, CORS und CSP](web-apps-cors-und-csp.md) |

## Seiten in diesem Ordner

| Seite | Beantwortet |
|---|---|
| [Caching auf dem Server](caching-auf-dem-server.md) | Wie frisch REST-Antworten sind und warum |
| [Caching in der App](caching-in-der-app.md) | Was zwischenspeichern, Schlüssel, Aktualität mit `dateModified`, Ratenlimits |
| [Synchronisation und Offline-Lesen](synchronisation-und-offline-lesen.md) | Einen Ordner auf dem Gerät halten, wartende Schreibzugriffe |
| [Web-Apps, CORS und CSP](web-apps-cors-und-csp.md) | Browser-Apps: Origins, CORS, Content Security Policy, der Service Worker |

## Verwandte Seiten

- [Headless-Exponential: Caching und Leistung](../../ersteller/anwendungsentwickler/headless-exponential.md#caching-und-leistung)
- [Zuverlässigkeit des App-Backends (für Unternehmen)](../../unternehmen/mobil-und-headless/zuverlaessigkeit-des-app-backends.md)
- [Offline und wiederholte Besuche (für Unternehmen)](../../unternehmen/mobil-und-headless/offline-und-wiederholte-besuche.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
