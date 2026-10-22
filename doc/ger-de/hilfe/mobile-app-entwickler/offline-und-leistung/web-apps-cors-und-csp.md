---
title: Web-Apps, CORS und CSP
audience: mobile-app-developer, app-creator, developer, hosting
category: mobile-apps
order: 640
summary: Eine Browser-App (PWA, Single-Page-App) auf Exponential bauen - derselbe Origin oder ein anderer, das Fehlen von CORS-Headern und die Möglichkeiten drumherum, die Content Security Policy der Website und connect-src, Sitzungen und Formular-Tokens im Browser und wie sich der Service Worker der Website zu dem einer App verhält.
keywords: Web-App, PWA, Single-Page-Anwendung, CORS, Access-Control-Allow-Origin, gleicher Origin, Proxy, Content Security Policy, CSP, connect-src, Service Worker, index.js, sw.js, Sitzungscookie, Formular-Token, X-CSRF-Token, Browser, web app, single page application, same origin, service worker, session cookie, form token, browser
type: help
---
# Web-Apps, CORS und CSP

Diese Seite richtet sich an Entwickler, die eine App bauen, die im Browser läuft: eine Progressive Web App, eine
Single-Page-App oder ein JavaScript-Frontend auf einer eigenen Domain. Browser setzen Regeln durch, denen native Apps
nicht begegnen, und diese Seite erklärt, wie sie für Exponential gelten.

## Inhalt

1. [Derselbe Origin oder ein anderer](#derselbe-origin-oder-ein-anderer)
2. [CORS](#cors)
3. [Content Security Policy](#content-security-policy)
4. [Anmelden aus einem Browser](#anmelden-aus-einem-browser)
5. [Service Worker](#service-worker)

## Derselbe Origin oder ein anderer

| Aufbau | Was der Browser erlaubt |
|---|---|
| Die App wird mit demselben Schema, Host und Port wie die Exponential-Website ausgeliefert (zum Beispiel als Design oder Siteaccess der Website, wie die Beispielportale von expservices) | jeden API-Aufruf, mit dem Sitzungscookie der Website |
| Die App liegt auf einem anderen Host (`app.example.com` ruft `www.example.com` auf) | nur das, was CORS-Header des Servers erlauben |

Die Beispielportale von `expservices` (`/portaljq/`, `/portalreact/` und andere) sind Browser-Frontends, die nur auf den
Remote-Diensten aufbauen und von der Website selbst ausgeliefert werden, daher brauchen sie kein CORS
([Remote-Dienste und Apps, Abschnitt 7](../../../anleitungen/entfernte-dienste-und-apps.md#7-ein-portal-öffnen-das-auf-den-diensten-aufbaut-3-minuten)).

## CORS

Weder das REST-Framework (`kernel/private/rest/`) noch ezjscore noch expservices senden `Access-Control-Allow-*`-Header,
und keine Einstellung in den mitgelieferten INI-Dateien fügt sie hinzu. Eine Browser-App auf einem anderen Origin kann
die APIs daher nicht direkt aufrufen. Die Möglichkeiten:

1. **Liefern Sie die App vom selben Origin aus**, als Design der Website oder unter einem Pfad, den der Webserver auf die
   Dateien der App abbildet.
2. **Stellen Sie ein Backend auf den Origin der App**, das Exponential von Server zu Server aufruft
   ([Ein Backend für Ihre App](../anmeldung/ein-backend-fuer-ihre-app.md)). So bleiben auch Tokens aus dem Browser heraus.
3. **Setzen Sie CORS-Header im Webserver oder einem Proxy**, nur für die API-Pfade, mit einer ausdrücklichen Liste
   erlaubter Origins (nie `*` zusammen mit Zugangsdaten), und beantworten Sie dort den `OPTIONS`-Preflight. Das ist
   Arbeit des Hostings; prüfen Sie es als Sicherheitsänderung ([Sicherheitshärtung](../../sicherheitshaertung.md)).

Native Apps (iOS, Android, Desktop) und die Netzwerkfunktionen von React Native unterliegen CORS nicht.

## Content Security Policy

Exponential sendet eine Content Security Policy aus `site.ini [ContentSecurityPolicy]`; standardmäßig gilt sie in der
Administrationsoberfläche nur zur Berichterstattung und ist auf den Websites ausgeschaltet, und ihre mitgelieferten
Direktiven enthalten `connect-src 'self'`
([Content Security Policy](../../../funktionen/6.0/content-security-policy.md)). Wird Ihre App von der Website
ausgeliefert und setzt die Website die Richtlinie durch, brauchen Aufrufe anderer Hosts (Ihres Backends auf einer
anderen Domain, eines Analysedienstes) Einträge in `connect-src`, und Inline-Skripte sind nicht erlaubt: Laden Sie Daten
so, wie es [Content Security Policy für Ihren Code](../../ersteller/anwendungsentwickler/content-security-policy.md) beschreibt.

## Anmelden aus einem Browser

- **Derselbe Origin:** Melden Sie sich über die Website an und verwenden Sie das Sitzungscookie. Schreibzugriffe auf die
  Remote-Dienste brauchen dann das Formular-Token: Holen Sie es von `expsession::token` und senden Sie es als
  `X-CSRF-Token` und als Feld `ezxform_token`
  ([Über expservices schreiben](../json-endpunkte/ueber-expservices-schreiben.md#regeln-für-jeden-schreibzugriff)).
- **Ein anderer Origin:** Lassen Sie das Backend auf dem Origin der App die Zugangsdaten halten; legen Sie keine Tokens
  in `localStorage` ab ([Geheimnisse auf Geräten speichern](../anmeldung/geheimnisse-auf-geraeten-speichern.md#browser)).

## Service Worker

Der eigene Service Worker der Website, `/index.js` (mit `/sw.js` als altem Namen), ist ein Navigations-Cache für Seiten
der Website: Er behält nur `GET`-Navigationen desselben Origins, die ohne Sitzungscookie erfolgt sind, und nur
erfolgreiche Seiten ([Offline und wiederholte Besuche](../../unternehmen/mobil-und-headless/offline-und-wiederholte-besuche.md#was-er-nie-speichert)).
API-Antworten für Ihre App speichert er nicht. Eine PWA, die Offline-Daten braucht, registriert einen eigenen Worker mit
eigenem Geltungsbereich und eigenen Cache-Regeln und darf den Geltungsbereich der Website nicht übernehmen; stimmen Sie
die Geltungsbereiche mit dem Website-Team ab.

## Verwandte Seiten

- [Headless-Exponential](../../ersteller/anwendungsentwickler/headless-exponential.md)
- [Der Antwort-Cache, und dem Netz aus dem Weg gehen](../../../bc/6.0/antwort-cache-und-navigation.md)
- [Sicherheit für Anwendungen](../../ersteller/anwendungsentwickler/sicherheit-fuer-anwendungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
