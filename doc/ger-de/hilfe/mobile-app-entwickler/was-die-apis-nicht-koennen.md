---
title: Was die APIs nicht können
audience: mobile-app-developer, app-creator, developer, project-manager
category: mobile-apps
order: 12
summary: Die bekannten Lücken der HTTP-Schnittstellen von Exponential für App-Entwickler, jede im Code geprüft - kein SDK, keine Aktualisierungs- oder Suchroute in REST, keine Bildaliase oder Uploads in REST-Antworten, kein PKCE und keine öffentlichen OAuth-Clients, kein zurückgegebener state-Parameter, keine CORS-Header, keine Push-Benachrichtigungen, listAtom antwortet mit 500 - mit dem Umweg für jede.
keywords: Einschränkungen, Lücken, bekannte Probleme, nicht implementiert, 501, listAtom, PKCE, state, Client-Secret, CORS, Push-Benachrichtigungen, SDK, Upload, Bildalias, Suche, Mobile App, limitations, gaps, known issues, not implemented, client secret, push notifications, image alias, search, mobile app
type: help
---
# Was die APIs nicht können

Diese Seite richtet sich an App-Entwickler und an die Personen, die mit ihnen eine App planen. Lesen Sie sie, bevor Sie
Bildschirme entwerfen und Aufwände schätzen: Jede Lücke unten wurde im Code geprüft, und für jede gibt es einen Umweg.
Nichts hier ist ein Grund, keine App zu bauen; es ist eine Liste von Dingen, die Sie einplanen, statt sie im letzten
Sprint zu entdecken.

## Inhalt

1. [Die REST-API](#die-rest-api)
2. [Authentifizierung](#authentifizierung)
3. [Browser und Web-Apps](#browser-und-web-apps)
4. [Dinge außerhalb der APIs](#dinge-außerhalb-der-apis)
5. [Wie Sie eine Lücke melden](#wie-sie-eine-lücke-melden)

## Die REST-API

| Lücke | Beleg | Umweg |
|---|---|---|
| Das Aktualisieren von Inhalten antwortet mit `501` | `doUpdateContentNode()` prüft `content/edit` und antwortet dann mit `not_implemented` | `expnode::update` von expservices ([Über expservices schreiben](json-endpunkte/ueber-expservices-schreiben.md)) |
| Keine Suchroute | die Routentabelle von `ezp7xRestApiProvider` enthält keine | `expsearch::search` ([Inhalte suchen](json-endpunkte/inhalte-suchen.md)) |
| Bilder kommen nur als Pfad der Originaldatei | `attributeOutputData()` schneidet die `ezimage`-Zeichenkette bei `\|` ab | `expimage::url` für einen Alias ([Bilder in REST-Antworten](medien-und-bilder/bilder-in-rest-antworten.md)) |
| Rich Text kommt als reiner Text | `ezxmltext`-Werte laufen durch `strip_tags()` | `OutputFormat=xhtml` mit einem Template der Website oder ein eigener Endpunkt ([Rich Text in Apps](inhaltsmodellierung/rich-text-in-apps.md)) |
| `listAtom` antwortet in v1 und v2 mit `500` | [Bekanntes Problem: listAtom](../../anleitungen/rest-api-v1-und-v2.md#8-bekanntes-problem-listatom) | die Route `list` |
| Keine Liste von Entwürfen oder Versionen | gelesen werden nur veröffentlichte Inhalte | die Versionsdienste von expservices, als Redakteur ([Entwürfe, Vorschau und Veröffentlichung](veroeffentlichungsablauf/entwuerfe-vorschau-und-veroeffentlichung.md)) |
| Kein Filter nach Klasse in einer Liste | die Route `list` nimmt nur Offset, Limit und Sortierung | `expnode::byClass` oder `expnode::children` mit einem Filter |
| Die Sortierung muss in der URL auf die Paginierung folgen | der reguläre Ausdruck der Route: `list(/offset/<n>)(/limit/<n>)(/sort/<key>(/asc\|desc))` | URLs in dieser Reihenfolge bauen ([Unterknoten auflisten und Paginierung](rest-api/unterknoten-auflisten-und-paginierung.md)) |

## Authentifizierung

| Lücke | Beleg | Umweg |
|---|---|---|
| Der Token-Endpunkt braucht `client_secret` | `doHandleRequest()` verlangt `grant_type`, `client_id`, `client_secret` | das Secret auf einem eigenen Server halten ([Ein Backend für Ihre App](anmeldung/ein-backend-fuer-ihre-app.md)) |
| Nur zwei Grants | `authorization_code` und `refresh_token` (`validateGrantType()`) | kein Password Grant, kein Client Credentials Grant; nutzen Sie den Code-Ablauf |
| Kein PKCE | der Token-Endpunkt liest keinen `code_verifier` | das Backend hält das Secret und führt den Austausch durch |
| `state` wird nicht zurückgegeben | die View authorize: „state, not implemented yet“ | einen eigenen Korrelationswert in der App oder im Backend halten |
| Persönliche API-Schlüssel sind persönlich | von einem Benutzer unter **Mein Konto > API-Zugang** erzeugt, einmal angezeigt | nie in einer App ausliefern ([Persönliche API-Schlüssel für Werkzeuge](anmeldung/persoenliche-api-schluessel-fuer-werkzeuge.md)) |

## Browser und Web-Apps

| Lücke | Beleg | Umweg |
|---|---|---|
| Keine CORS-Header | weder das REST-Framework noch ezjscore noch expservices senden `Access-Control-*`-Header | die Web-App vom selben Origin ausliefern oder die Header im Webserver oder einem Proxy setzen ([Web-Apps, CORS und CSP](offline-und-leistung/web-apps-cors-und-csp.md)) |
| Der Service Worker ist für Seiten der Website | `/index.js` speichert nur GET-Navigationen desselben Origins ohne Sitzungscookie | eine App braucht einen eigenen Offline-Speicher ([Synchronisation und Offline-Lesen](offline-und-leistung/synchronisation-und-offline-lesen.md)) |

Native Apps sind keine Browser und von CORS nicht betroffen.

## Dinge außerhalb der APIs

- **Kein mobiles SDK.** Schreiben Sie einen eigenen schlanken Client um die Routen, die Sie nutzen; die
  [expservices-Referenz](../../bc/6.0/backend-dienste-ueber-ezjscore.md#clients) enthält Ausgangspunkte für Kotlin und
  Swift, die ihre Autoren auf ihrer Build-Maschine nicht kompilieren konnten, also kompilieren Sie sie zuerst in Ihrem
  Projekt.
- **Keine Push-Benachrichtigungen.** Nichts in den APIs sendet Nachrichten an Geräte. Inhaltsereignisse auf dem Server
  können Ihren Push-Dienst über ein Workflow-Ereignis oder einen Listener aufrufen, den ein Anwendungsentwickler
  schreibt ([Workflows, Trigger und Ereignisse](../ersteller/anwendungsentwickler/workflows-und-ereignisse.md)).
- **Keine Uploads.** Weder REST noch expservices dokumentieren einen Weg, ein Bild oder eine Datei von einem Gerät zu
  senden. expservices lehnt entfernte Schreibzugriffe auf Felder vom Typ `ezimage`, `ezbinaryfile`, `ezmedia`,
  `ezuser`, `ezpassword` und `ezflowblock` ab ([Dateien und Uploads](medien-und-bilder/dateien-und-uploads.md)).
- **Kein Änderungs-Feed.** Es gibt keine Route „alles, was sich seit ... geändert hat“ für den ganzen Baum; was es
  gibt, steht in [Synchronisation und Offline-Lesen](offline-und-leistung/synchronisation-und-offline-lesen.md).

## Wie Sie eine Lücke melden

Wenn eine Lücke Ihre App blockiert, melden Sie sie mit der Route, der Anfrage, der Antwort und dem, was Sie erwartet
haben. Die Seite **Fehler melden** der Hilfe und [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md) sagen, wo.
Bis sie geschlossen ist, kann ein Anwendungsentwickler die Route in Ihrer Installation ergänzen
([Ihr eigener REST-Provider](../ersteller/anwendungsentwickler/rest-api.md#ein-eigener-rest-provider)).

## Verwandte Seiten

- [Welche API für Ihre App](welche-api-fuer-ihre-app.md)
- [REST-API v1 und v2 für Unternehmen: bekannte Lücken](../unternehmen/mobil-und-headless/rest-api-v1-und-v2-fuer-unternehmen.md#bekannte-lücken)
- [FAQ für Mobile-App-Entwickler](faq-fuer-mobile-app-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
