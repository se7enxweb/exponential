---
title: "Mobile-App-Entwickler: hier beginnen"
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 1
summary: Die Einstiegsseite für Entwickler, die iOS-, Android-, plattformübergreifende oder Browser-Apps auf Basis von Exponential-Inhalten bauen - die drei HTTP-Schnittstellen (die REST-Inhalts-API in v1 und v2, die Remote-Dienste von expservices, die Serverfunktionen von ezjscore), wie sich Apps authentifizieren, was die APIs können und was nicht, und wo jede Seite dieses Abschnitts steht.
keywords: Mobile App, App-Entwicklung, iOS, Android, Flutter, React Native, Kotlin, Swift, Headless, REST-API, v1, v2, ezprestapi, OAuth2, API-Schlüssel, expservices, ezjscore, JSON, hier beginnen, Einstieg, mobile app, REST API, API key, start here, landing
featured: true
type: help
---
# Mobile-App-Entwickler: hier beginnen

Dieser Abschnitt richtet sich an Entwickler, die eine App bauen, die Exponential-Inhalte anzeigt oder schreibt: eine
native iOS- oder Android-App, eine plattformübergreifende App (Flutter, React Native, Kotlin Multiplatform und
Ähnliches), eine Progressive Web App, einen Kiosk, einen Uhren- oder TV-Client oder jedes andere Programm, das über
HTTP mit einer Exponential-Installation spricht. Sie brauchen keine PHP-Kenntnisse, um diese Seiten zu nutzen. Wenn Sie
die Serverseite ändern müssen, übernimmt der Abschnitt
[Anwendungsentwickler](../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md).

> **Hinweis**
> Jede Route, jeder Parameter und jede Einstellung, die dieser Abschnitt nennt, wurde im Code der Erweiterung
> `ezprestapi`, im REST-Framework des Kernels (`kernel/private/rest/`), in `ezjscore`, `expservices` und
> `settings/rest.ini` sowie in den verlinkten Anleitungen geprüft. Wo etwas nicht existiert (ein SDK, CORS-Header,
> Bildaliase in REST-Antworten, eine Suchroute in REST), sagen die Seiten das, statt einen Umweg als Funktion zu
> beschreiben.

## Inhalt

1. [Exponential für App-Entwickler in einer Minute](#exponential-für-app-entwickler-in-einer-minute)
2. [Die drei HTTP-Schnittstellen](#die-drei-http-schnittstellen)
3. [Wie sich eine App anmeldet](#wie-sich-eine-app-anmeldet)
4. [Ihre ersten zehn Minuten](#ihre-ersten-zehn-minuten)
5. [Seiten in diesem Abschnitt](#seiten-in-diesem-abschnitt)
6. [Was es nicht gibt](#was-es-nicht-gibt)
7. [Andere Abschnitte dieser Hilfe](#andere-abschnitte-dieser-hilfe)

## Exponential für App-Entwickler in einer Minute

Exponential speichert Inhalte als **Objekte** von **Inhaltsklassen** (Artikel, Ordner, Bild, Produkt, Ihre eigenen
Klassen), die als **Knoten** in einem Inhaltsbaum liegen. Redakteure arbeiten in der Administrationsoberfläche; Ihre
App liest das veröffentlichte Ergebnis über HTTP und erhält JSON. Jeder Aufruf läuft als ein Benutzer, und dessen
Rollen und Richtlinien entscheiden, was der Aufruf sehen oder ändern darf. Mehr Modell braucht eine App nicht;
[Wie Exponential-Inhalte in eine App gelangen](wie-exponential-inhalte-in-eine-app-gelangen.md)
erklärt es mit echten Antworten.

## Die drei HTTP-Schnittstellen

| Schnittstelle | URL-Muster | Am besten für | Lesen |
|---|---|---|---|
| REST-Inhalts-API (`ezprestapi`) | `/api/ezp/v1/...` und `/api/ezp/v2/...` | Knoten, Unterknoten und Felder lesen; Inhalte anlegen und entfernen (v2) | [REST-API](rest-api/README.md) |
| Remote-Dienste (`expservices`) | `/ezjscore/call/exp<domain>::<method>` | Suche, Bildalias-URLs, URL-Aliase, Sprachen, Aktualisierungen; über tausend Dienste | [JSON-Endpunkte](json-endpunkte/README.md) |
| Serverfunktionen von ezjscore | `/ezjscore/call/<name>::<function>` | ein kleiner JSON-Endpunkt, den ein PHP-Entwickler für Ihre App schreibt | [ezjscore-Serverfunktionen für Ihre App](json-endpunkte/ezjscore-serverfunktionen-fuer-ihre-app.md) |

Die meisten Apps lesen mit der REST-API und nutzen einen oder zwei Remote-Dienste für das, was REST nicht abdeckt. Die
Entscheidung ist in [Welche API für Ihre App](welche-api-fuer-ihre-app.md) beschrieben.

## Wie sich eine App anmeldet

| Zugangsdaten | Header | Für | Lesen |
|---|---|---|---|
| OAuth2-Zugriffstoken | `Authorization: OAuth <token>` (oder `Bearer <token>`) | eine App, deren Benutzer sich mit ihrem eigenen Exponential-Konto anmelden | [OAuth2-Autorisierungscode-Ablauf](anmeldung/oauth2-autorisierungscode-ablauf.md) |
| Persönlicher API-Schlüssel `expk_...` | `Authorization: Bearer expk_...` | das eigene Werkzeug oder Skript einer einzelnen Person, nie eine veröffentlichte App | [Persönliche API-Schlüssel für Werkzeuge](anmeldung/persoenliche-api-schluessel-fuer-werkzeuge.md) |
| expservices-Token `expt_...` | `Authorization: Bearer expt_...` und `X-Exp-Token: expt_...` | die Remote-Dienste als Benutzer aufrufen | [expservices-Tokens in Apps](anmeldung/expservices-tokens-in-apps.md) |

Die REST-API verlangt standardmäßig eine Authentifizierung (`rest.ini [Authentication] RequireAuthentication=enabled`).
Wie Sie wählen, erklärt [Zugangsdaten auswählen](anmeldung/zugangsdaten-auswaehlen.md).

## Ihre ersten zehn Minuten

1. Prüfen Sie die Installation, mit der Sie arbeiten werden: [Erst prüfen, dann bauen](testen/gegen-eine-demo-testen.md#erst-prüfen-dann-bauen).
2. Besorgen Sie sich Zugangsdaten für die Entwicklung: Ein persönlicher API-Schlüssel auf einer Testinstallation ist
   der schnellste Weg ([Persönliche API-Schlüssel für Werkzeuge](anmeldung/persoenliche-api-schluessel-fuer-werkzeuge.md)).
3. Stellen Sie Ihre erste Anfrage: [Ihre erste Anfrage](rest-api/ihre-erste-anfrage.md).
4. Listen Sie einen Ordner seitenweise auf: [Unterknoten auflisten und Paginierung](rest-api/unterknoten-auflisten-und-paginierung.md).
5. Lesen Sie die Lücken, bevor Sie Bildschirme entwerfen: [Was die APIs nicht können](was-die-apis-nicht-koennen.md).

Die vollständige Reihenfolge steht im [Lesepfad](lesepfad-fuer-mobile-app-entwickler.md). Kurze Antworten finden Sie in
den [FAQ](faq-fuer-mobile-app-entwickler.md), die Begriffe werden im [Glossar](glossar-fuer-mobile-app-entwickler.md) erklärt.

## Seiten in diesem Abschnitt

| Ordner | Was er abdeckt |
|---|---|
| [REST-API](rest-api/README.md) | Routen, Antwortgruppen, Listen und Seiten, Felder, XHTML-Ausgabe, Schreibzugriffe, Fehler, Versionen |
| [Authentifizierung](anmeldung/README.md) | OAuth2 und Refresh-Tokens, API-Schlüssel, expservices-Tokens, Geheimnisse auf Geräten, ein Backend für Ihre App |
| [JSON-Endpunkte](json-endpunkte/README.md) | expservices aufrufen, Suche, Schreibzugriffe, Ihre eigenen Serverfunktionen |
| [Inhaltsmodellierung](inhaltsmodellierung/README.md) | Inhaltsklassen für Apps, IDs und Deep Links, Sprachen, Rich Text |
| [Medien und Bilder](medien-und-bilder/README.md) | Bilder in REST-Antworten, Bildaliase für Bildschirmgrößen, Dateien und Uploads |
| [Offline und Leistung](offline-und-leistung/README.md) | Caching auf dem Server, Caching in der App, Synchronisation, Browser-Apps, CORS und CSP |
| [Veröffentlichungsablauf](veroeffentlichungsablauf/README.md) | Entwürfe und Vorschau, Berechtigungen für App-Benutzer, Nachvollziehbarkeit |
| [Testen](testen/README.md) | Gegen eine Demo testen, eine lokale Testinstallation |
| [Checklisten](checklisten/README.md) | Sicherheit, Start, Bereitschaft für die App-Store-Prüfung |

## Was es nicht gibt

Mit Exponential wird kein offizielles mobiles SDK ausgeliefert. Auch die Erweiterung `expsite_app` ist keines: Ihr
eigenes `README.md` beschreibt sie als Ort eines Website-Projekts für Design-Overrides und Projekteinstellungen.
Ausgeliefert werden die oben genannten HTTP-Schnittstellen, die Shell- und Python-Clients von `expservices` sowie
Client-Beispiele für Kotlin, Swift, Objective-C, Qt und GTK in der
[expservices-Referenz](../../bc/6.0/backend-dienste-ueber-ezjscore.md#clients), die angibt, dass die Beispiele für Kotlin,
Swift, Objective-C und Qt von Hand geprüft, aber nicht kompiliert wurden. Die vollständige Liste der Lücken steht in
[Was die APIs nicht können](was-die-apis-nicht-koennen.md).

## Andere Abschnitte dieser Hilfe

| Abschnitt | Für |
|---|---|
| [Anwendungsentwickler](../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md) | PHP-Entwickler, die den Server erweitern: REST-Provider, Module, Dienste |
| [Mobile Apps, Headless-Einsatz und Netzwerke](../unternehmen/mobil-und-headless/README.md) | Inhaber und Produktverantwortliche, die die App planen |
| [Website-Ersteller](../ersteller/website-ersteller/website-ersteller-hier-beginnen.md) | Die Personen, die die Website und ihre Inhaltsklassen bauen |
| [Hosting-Unternehmen](../hosting/hosting-anbieter-hier-beginnen.md) | Die Personen, die den Server betreiben, mit dem Ihre App spricht |

## Verwandte Seiten

- [Welche API für Ihre App](welche-api-fuer-ihre-app.md)
- [Wie Exponential-Inhalte in eine App gelangen](wie-exponential-inhalte-in-eine-app-gelangen.md)
- [Headless-Exponential](../ersteller/anwendungsentwickler/headless-exponential.md)
- [REST-API v1 und v2: eine Erweiterung](../../anleitungen/rest-api-v1-und-v2.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
