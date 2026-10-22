---
title: Sicherheitscheckliste für Apps
audience: mobile-app-developer, app-creator, developer, admin
category: mobile-apps
order: 910
summary: Eine Checkliste für die Sicherheit einer App auf Exponential - Zugangsdaten und wo sie liegen, die OAuth-Einrichtung und ihre Grenzen, Rollen der Integrationsbenutzer, HTTPS und der Authorization-Header, Protokolle, Schreibzugriffe, Besonderheiten von Web-Apps und die Überwachung von Ablehnungen im Prüfprotokoll.
keywords: Sicherheit, Checkliste, Zugangsdaten, OAuth, Client-Secret, API-Schlüssel, expservices-Token, Rollen, minimale Rechte, HTTPS, RequireHTTPS, Protokolle, Prüfprotokoll, CORS, CSP, Mobile App, security, checklist, credentials, client secret, API key, roles, least privilege, logs, audit, mobile app
type: help
---
# Sicherheitscheckliste für Apps

Diese Checkliste richtet sich an App-Entwickler und an den Administrator der Installation, mit der die App spricht.
Gehen Sie sie vor dem ersten Release durch und erneut, wann immer sich Zugangsdaten, Rollen oder Endpunkte ändern.

## Inhalt

1. [Zugangsdaten](#zugangsdaten)
2. [Konten und Rollen](#konten-und-rollen)
3. [Transport](#transport)
4. [In der App](#in-der-app)
5. [Schreibzugriffe](#schreibzugriffe)
6. [Browser-Apps](#browser-apps)
7. [Überwachung](#überwachung)

## Zugangsdaten

- [ ] Kein OAuth-Client-Secret, kein persönlicher API-Schlüssel und kein expservices-Token steht im Code, in den
      Ressourcen oder in der Konfiguration der App ([Zugangsdaten auswählen](../anmeldung/zugangsdaten-auswaehlen.md#niemals)).
- [ ] Der OAuth-Codeaustausch und das Erneuern laufen in einem Backend, das das Client-Secret hält
      ([Ein Backend für Ihre App](../anmeldung/ein-backend-fuer-ihre-app.md)).
- [ ] Die App ordnet den OAuth-Rückruf selbst zu, da `state` nicht zurückgegeben wird
      ([OAuth2-Autorisierungscode-Ablauf](../anmeldung/oauth2-autorisierungscode-ablauf.md#grenzen-der-implementierung)).
- [ ] `response_type=token` wird nicht verwendet.
- [ ] Schlüssel von Backends und Werkzeugen haben die kleinsten Geltungsbereiche und eine Laufzeit, und ein Termin für
      den Austausch steht im Kalender.
- [ ] `expk_` und `expt_` stehen im Secret-Scanner jedes Repositorys.

## Konten und Rollen

- [ ] Jede App hat ihren eigenen Integrationsbenutzer; keine verwendet den Administrator oder das Konto einer Person.
- [ ] `content/read` ist auf die Bereiche oder Teilbäume beschränkt, die die App zeigt.
- [ ] Schreibrichtlinien sind auf einen Ordner und die Klassen beschränkt, die die App anlegt.
- [ ] Keine Richtlinien für `setup`, `class` oder `user` für App-Benutzer.
- [ ] `oauth/authorize` nur für die Rollen der Personen, die die App benutzen
      ([Berechtigungen für App-Benutzer](../veroeffentlichungsablauf/berechtigungen-fuer-app-benutzer.md)).

## Transport

- [ ] Jede Anfrage verwendet HTTPS; erwägen Sie `rest.ini [Authentication] RequireHTTPS=enabled` auf dem Server.
- [ ] Der Header `Authorization` erreicht PHP auf jedem Server und durch jeden Proxy
      ([Der Authorization-Header und Proxys](../anmeldung/der-authorization-header-und-proxys.md)).
- [ ] Proxys und CDNs speichern Antworten auf Anfragen mit `Authorization` nicht zwischen.
- [ ] Zugangsdaten werden nie in einer URL gesendet.

## In der App

- [ ] Tokens liegen in Keychain oder Keystore, nicht in Einstellungen oder Dateien ([Geheimnisse auf Geräten speichern](../anmeldung/geheimnisse-auf-geraeten-speichern.md)).
- [ ] Die HTTP-Protokollierung entfernt `Authorization` und `X-Exp-Token`, in jedem Build, der die Maschine des
      Entwicklers verlässt.
- [ ] Das Abmelden löscht Tokens und widerruft expservices-Tokens.
- [ ] Gerendertes HTML (`renderedOutput`) wird in einer Web View ohne Skripte angezeigt, die es nicht braucht, und
      externe Links öffnen sich im Systembrowser ([Gerenderte XHTML-Ausgabe](../rest-api/gerenderte-xhtml-ausgabe.md#anzeige-in-der-app)).

## Schreibzugriffe

- [ ] Beim Entfernen bittet die App den Benutzer um Bestätigung ([Inhalte anlegen und entfernen](../rest-api/inhalte-anlegen-und-entfernen.md#entfernen)).
- [ ] Wiederholte Anlegeversuche können keine Inhalte verdoppeln (Remote-IDs, `409`).
- [ ] Jeder Upload-Endpunkt, der für die App gebaut wird, prüft Größe, Typ anhand des Inhalts und Berechtigungen
      ([Dateien und Uploads](../medien-und-bilder/dateien-und-uploads.md#ein-auftrag-für-einen-upload-endpunkt)).

## Browser-Apps

- [ ] CORS-Header, die im Webserver ergänzt werden, nennen ausdrückliche Origins und decken nur die API-Pfade ab
      ([Web-Apps, CORS und CSP](../offline-und-leistung/web-apps-cors-und-csp.md#cors)).
- [ ] Die Content Security Policy erlaubt die Verbindungen der App und nichts darüber hinaus.

## Überwachung

- [ ] Nach jedem Release liest jemand den Kanal `access` des Prüfprotokolls: `access.apikey.use.failed`,
      `access.expservices.token.failed` ([App-Aktivität nachvollziehen](../veroeffentlichungsablauf/berechtigungen-fuer-app-benutzer.md#app-aktivität-nachvollziehen)).
- [ ] Es gibt einen Plan für durchgesickerte Zugangsdaten: widerrufen, austauschen, Prüfprotokoll kontrollieren.

## Verwandte Seiten

- [Sicherheit für Anwendungen](../../ersteller/anwendungsentwickler/sicherheit-fuer-anwendungen.md)
- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Persönliche API-Schlüssel: Sicherheitshinweise](../../../anleitungen/api-schluessel.md#11-sicherheitshinweise)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
