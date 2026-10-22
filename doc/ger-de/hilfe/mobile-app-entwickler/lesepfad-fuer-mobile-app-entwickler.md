---
title: Lesepfad für Mobile-App-Entwickler
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 2
summary: Die Reihenfolge, in der Sie den Abschnitt Mobile-App-Entwickler lesen - ein Pfad von einer Stunde bis zum ersten funktionierenden Listenbildschirm, dann vier Etappen (verstehen, verbinden, bauen, ausliefern) und zusätzliche Seiten für Apps, die Inhalte schreiben, offline arbeiten oder im Browser laufen.
keywords: Lesepfad, Reihenfolge, Lernpfad, wo beginnen, erste Stunde, Mobile App, REST, OAuth, expservices, offline, Start, Checkliste, reading path, order, learning path, where to begin, first hour, mobile app, launch, checklist
type: help
---
# Lesepfad für Mobile-App-Entwickler

Diese Seite richtet sich an App-Entwickler, die wissen möchten, was sie in welcher Reihenfolge lesen, um von null zu
einer ausgelieferten App mit Exponential-Inhalten zu kommen. Sie brauchen nicht jede Seite des Abschnitts. Folgen Sie
zuerst dem Pfad für die erste Stunde, dann der Etappe, in der Sie sich befinden.

## Inhalt

1. [Die erste Stunde](#die-erste-stunde)
2. [Etappe 1: verstehen](#etappe-1-verstehen)
3. [Etappe 2: verbinden](#etappe-2-verbinden)
4. [Etappe 3: bauen](#etappe-3-bauen)
5. [Etappe 4: ausliefern](#etappe-4-ausliefern)
6. [Zusätzliche Pfade](#zusätzliche-pfade)
7. [Wer was lesen sollte](#wer-was-lesen-sollte)

## Die erste Stunde

Sechs Seiten, jeweils etwa zehn Minuten, am Ende steht ein Listenbildschirm, der echte Daten lädt:

1. [Mobile-App-Entwickler: hier beginnen](mobile-app-entwickler-hier-beginnen.md)
2. [Wie Exponential-Inhalte in eine App gelangen](wie-exponential-inhalte-in-eine-app-gelangen.md)
3. [Gegen eine Demo testen](testen/gegen-eine-demo-testen.md), um eine Installation zu finden, die antwortet
4. [Persönliche API-Schlüssel für Werkzeuge](anmeldung/persoenliche-api-schluessel-fuer-werkzeuge.md), für Zugangsdaten zur Entwicklung
5. [Ihre erste Anfrage](rest-api/ihre-erste-anfrage.md)
6. [Unterknoten auflisten und Paginierung](rest-api/unterknoten-auflisten-und-paginierung.md)

## Etappe 1: verstehen

- [Welche API für Ihre App](welche-api-fuer-ihre-app.md)
- [Was die APIs nicht können](was-die-apis-nicht-koennen.md)
- [Inhaltsklassen für Apps](inhaltsmodellierung/inhaltsklassen-fuer-apps.md)
- [API-Versionen v1 und v2](rest-api/api-versionen-v1-und-v2.md)

## Etappe 2: verbinden

- [Zugangsdaten auswählen](anmeldung/zugangsdaten-auswaehlen.md)
- [OAuth2-Autorisierungscode-Ablauf](anmeldung/oauth2-autorisierungscode-ablauf.md)
- [Ein Backend für Ihre App](anmeldung/ein-backend-fuer-ihre-app.md)
- [Geheimnisse auf Geräten speichern](anmeldung/geheimnisse-auf-geraeten-speichern.md)
- [Der Authorization-Header und Proxys](anmeldung/der-authorization-header-und-proxys.md)

## Etappe 3: bauen

- [Einen Knoten lesen](rest-api/einen-knoten-lesen.md), [Antwortgruppen](rest-api/antwortgruppen.md) und
  [Felder und Feldwerte](rest-api/felder-und-feldwerte.md)
- [Bilder in REST-Antworten](medien-und-bilder/bilder-in-rest-antworten.md) und
  [Bildaliase für Bildschirmgrößen](medien-und-bilder/bildaliase-fuer-bildschirmgroessen.md)
- [expservices aus einer App aufrufen](json-endpunkte/expservices-aus-einer-app-aufrufen.md) und
  [Inhalte suchen](json-endpunkte/inhalte-suchen.md)
- [Knoten-IDs, Remote-IDs und Deep Links](inhaltsmodellierung/knoten-ids-remote-ids-und-deep-links.md)
- [Fehler und Statuscodes](rest-api/fehler-und-statuscodes.md)
- [Caching in der App](offline-und-leistung/caching-in-der-app.md)

## Etappe 4: ausliefern

- [Sicherheitscheckliste](checklisten/sicherheitscheckliste.md)
- [Checkliste für den Start](checklisten/checkliste-fuer-den-start.md)
- [Bereit für die App-Store-Prüfung](checklisten/bereit-fuer-die-app-store-pruefung.md)
- [Berechtigungen für App-Benutzer](veroeffentlichungsablauf/berechtigungen-fuer-app-benutzer.md)

## Zusätzliche Pfade

| Ihre App | Ergänzen Sie |
|---|---|
| schreibt Inhalte (Außendienstberichte, Einsendungen von Benutzern) | [Inhalte anlegen und entfernen](rest-api/inhalte-anlegen-und-entfernen.md), [Über expservices schreiben](json-endpunkte/ueber-expservices-schreiben.md), [Dateien und Uploads](medien-und-bilder/dateien-und-uploads.md) |
| arbeitet offline | [Synchronisation und Offline-Lesen](offline-und-leistung/synchronisation-und-offline-lesen.md), [Caching auf dem Server](offline-und-leistung/caching-auf-dem-server.md) |
| läuft im Browser (PWA, Single-Page-App) | [Web-Apps, CORS und CSP](offline-und-leistung/web-apps-cors-und-csp.md) |
| bedient mehrere Sprachen | [Mehrsprachige Inhalte](inhaltsmodellierung/mehrsprachige-inhalte.md) |
| zeigt Artikel | [Rich Text in Apps](inhaltsmodellierung/rich-text-in-apps.md), [Gerenderte XHTML-Ausgabe](rest-api/gerenderte-xhtml-ausgabe.md) |
| braucht eine Vorschau für Redakteure | [Entwürfe, Vorschau und Veröffentlichung](veroeffentlichungsablauf/entwuerfe-vorschau-und-veroeffentlichung.md) |

## Wer was lesen sollte

| Rolle | Seiten |
|---|---|
| App-Entwickler | den ganzen Pfad oben |
| Technische Leitung | Etappe 1, [Ein Backend für Ihre App](anmeldung/ein-backend-fuer-ihre-app.md), die Checklisten |
| PHP-Entwickler auf der Serverseite | [Anwendungsentwickler: hier beginnen](../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md), dann [REST-API für Anwendungen](../ersteller/anwendungsentwickler/rest-api.md) |
| Product Owner | [Mobile Apps, Headless-Einsatz und Netzwerke](../unternehmen/mobil-und-headless/README.md) und die [Checkliste für den Start](checklisten/checkliste-fuer-den-start.md) |

## Verwandte Seiten

- [FAQ für Mobile-App-Entwickler](faq-fuer-mobile-app-entwickler.md)
- [Glossar für Mobile-App-Entwickler](glossar-fuer-mobile-app-entwickler.md)
- [Der Lesepfad für Anwendungsentwickler](../ersteller/anwendungsentwickler/lesepfad-fuer-anwendungsentwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
