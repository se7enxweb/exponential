---
title: FAQ für Mobile-App-Entwickler
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 90
summary: Kurze Antworten auf die Fragen, die App-Entwickler am häufigsten zu Exponential stellen - 401 ohne Inhalt, welche Version aufrufen, PKCE, API-Schlüssel in Apps, Bildgrößen, Suche, Aktualisierungen, CORS, Sprachen, Grenzen der Paginierung, SDKs und wo man ein Problem meldet.
keywords: FAQ, Fragen, Fehlerbehebung, 401, 403, 501, OAuth, PKCE, API-Schlüssel, v1, v2, Bildalias, Suche, Aktualisieren, CORS, Übersetzung, Paginierung, SDK, expsite_app, Mobile App, questions, troubleshooting, API key, image alias, search, update, translation, paging, mobile app
type: help
---
# FAQ für Mobile-App-Entwickler

Kurze Antworten auf die Fragen, die App-Entwickler am häufigsten stellen. Jede Antwort verweist auf die Seite, die sie
erklärt.

## Inhalt

1. [Verbinden](#verbinden)
2. [Inhalte lesen](#inhalte-lesen)
3. [Inhalte schreiben](#inhalte-schreiben)
4. [Plattform und Werkzeuge](#plattform-und-werkzeuge)

## Verbinden

**Meine Anfrage antwortet mit 401 und leerem Inhalt. Warum?**
Keine gültigen Zugangsdaten haben PHP erreicht. Ohne sie antwortet die REST-API mit `401`, dem Header
`WWW-Authenticate: OAuth realm='Exponential REST'` und ohne Inhalt (beobachtet auf dem Demonstrationsserver). Prüfen
Sie Namen und Schema des Headers und ob ein Proxy `Authorization` verwirft
([Der Authorization-Header und Proxys](anmeldung/der-authorization-header-und-proxys.md)).

**Soll meine App v1 oder v2 aufrufen?**
Lesezugriffe antworten in beiden gleich; Schreibzugriffe gibt es nur in v2. Neue Apps können durchgehend v2 verwenden
([API-Versionen v1 und v2](rest-api/api-versionen-v1-und-v2.md)).

**Unterstützt der OAuth-Server PKCE oder öffentliche Clients?**
Nein. Der Token-Endpunkt verlangt `client_secret` und unterstützt nur die Grants `authorization_code` und
`refresh_token`. Halten Sie das Secret auf einem Server ([Ein Backend für Ihre App](anmeldung/ein-backend-fuer-ihre-app.md)).

**Kann ich einen persönlichen API-Schlüssel in meiner App ausliefern?**
Nein. Ein Schlüssel gehört einem Benutzer, handelt als dieser Benutzer und ist für dessen eigene Skripte gedacht. Jeder,
der die App entpackt, hätte ihn ([Persönliche API-Schlüssel für Werkzeuge](anmeldung/persoenliche-api-schluessel-fuer-werkzeuge.md)).

**Bekommt die App den Parameter `state` von der Autorisierungsseite zurück?**
Nein. Die View authorize sagt „state, not implemented yet“. Ordnen Sie die Weiterleitung selbst zu
([OAuth2-Autorisierungscode-Ablauf](anmeldung/oauth2-autorisierungscode-ablauf.md)).

**Wie lange lebt ein Token?**
`rest.ini [OAuthSettings] TokenTTL`, standardmäßig eine Stunde; die Token-Antwort nennt den Wert in `expires_in`
([OAuth2-Autorisierungscode-Ablauf](anmeldung/oauth2-autorisierungscode-ablauf.md#tokens-erneuern)).

## Inhalte lesen

**Wo ist Knoten 2, und bei welchem Knoten soll meine App beginnen?**
Knoten 2 ist in einer Standardinstallation die Wurzel des Inhaltsbaums. Fragen Sie die Redakteure nach dem Ordner, den
Ihre App zeigt, und verwenden Sie lieber seine Remote-ID als seine Nummer
([Knoten-IDs, Remote-IDs und Deep Links](inhaltsmodellierung/knoten-ids-remote-ids-und-deep-links.md)).

**Warum liefert eine Liste nur zehn Elemente?**
Das Standardlimit der Route `list` ist 10. Ergänzen Sie `/offset/<n>/limit/<n>`
([Unterknoten auflisten und Paginierung](rest-api/unterknoten-auflisten-und-paginierung.md)).

**Warum fehlen meine Felder in der Liste?**
Listen liefern nur Metadaten, außer Sie ergänzen `?ResponseGroups=Fields` ([Antwortgruppen](rest-api/antwortgruppen.md)).

**Wie bekomme ich ein Bild in der Größe meines Bildschirms?**
REST liefert nur den Pfad der Originaldatei. Fordern Sie mit `expimage::url` eine Alias-URL an
([Bildaliase für Bildschirmgrößen](medien-und-bilder/bildaliase-fuer-bildschirmgroessen.md)).

**Gibt es eine Suchroute?**
Nicht in REST. Die Remote-Dienste haben `expsearch::search` ([Inhalte suchen](json-endpunkte/inhalte-suchen.md)).

**Wie lese ich eine andere Sprache?**
Ergänzen Sie `?Translation=<locale>` (zum Beispiel `ger-DE`) bei Lesezugriffen auf Knoten, Objekte und Felder
([Mehrsprachige Inhalte](inhaltsmodellierung/mehrsprachige-inhalte.md)).

**Mein Artikeltext hat seine Formatierung verloren.**
Rich-Text-Felder werden in REST-Antworten als reiner Text geliefert. Siehe [Rich Text in Apps](inhaltsmodellierung/rich-text-in-apps.md).

## Inhalte schreiben

**Kann die App ein vorhandenes Element über REST aktualisieren?**
Noch nicht: Die Aktualisierungsroute prüft `content/edit` und antwortet dann mit `501`. Verwenden Sie `expnode::update`
([Über expservices schreiben](json-endpunkte/ueber-expservices-schreiben.md)).

**Mein Anlegen antwortet mit 400.**
`languageLocale` fehlt, oder `parentNodeID` oder `classIdentifier` fehlt oder ist unbekannt
([Inhalte anlegen und entfernen](rest-api/inhalte-anlegen-und-entfernen.md)).

**Können Benutzer aus der App Fotos hochladen?**
Keine dokumentierte Route nimmt eine Datei von einem Gerät entgegen ([Dateien und Uploads](medien-und-bilder/dateien-und-uploads.md)).

## Plattform und Werkzeuge

**Gibt es ein SDK?**
Nein. Es gibt Shell- und Python-Clients für expservices und handgeschriebene Client-Beispiele für andere Plattformen
([Mobile-App-Entwickler: hier beginnen](mobile-app-entwickler-hier-beginnen.md#was-es-nicht-gibt)).

**Ist `expsite_app` ein Framework für mobile Apps?**
Nein. Es ist der Ort für die Design-Overrides und Einstellungen eines Website-Projekts, über das Design `simple`
gelegt (laut seinem `README.md`).

**Meine Browser-App bekommt einen CORS-Fehler.**
Exponential sendet keine CORS-Header. Liefern Sie die App vom selben Origin aus oder setzen Sie die Header im Webserver
([Web-Apps, CORS und CSP](offline-und-leistung/web-apps-cors-und-csp.md)).

**Wo teste ich, ohne eine echte Website zu beschädigen?**
Am besten in einer eigenen lokalen Installation; auf einer öffentlichen Demo ist möglicherweise nicht jede API aktiv
([Testen](testen/README.md)).

**Wo frage ich nach oder melde etwas?**
Auf der Seite **Fehler melden** der Hilfe und unter [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md); die FAQ
für die Serverseite ist [Anwendungsentwickler: häufig gestellte Fragen](../ersteller/anwendungsentwickler/faq-fuer-anwendungsentwickler.md).

## Verwandte Seiten

- [Mobile-App-Entwickler: hier beginnen](mobile-app-entwickler-hier-beginnen.md)
- [Was die APIs nicht können](was-die-apis-nicht-koennen.md)
- [Glossar für Mobile-App-Entwickler](glossar-fuer-mobile-app-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
