---
title: Zugangsdaten auswählen
audience: mobile-app-developer, app-creator, developer, project-manager
category: mobile-apps
order: 210
summary: Die richtigen Zugangsdaten für jede Art von App auf Exponential wählen - eine öffentliche Lese-App, eine App mit angemeldeten Benutzern, eine interne App für Mitarbeiter, ein Kiosk, ein Entwicklungs-Build - mit den Gründen, den anzulegenden Konten und dem, was Sie nie tun sollten.
keywords: Zugangsdaten, auswählen, OAuth2, API-Schlüssel, expservices-Token, öffentliche App, Lese-App, Mitarbeiter-App, Kiosk, Integrationsbenutzer, anonym, Backend, Mobile App, credential, choose, API key, expservices token, public app, reader app, staff app, integration user, back end, mobile app
type: help
---
# Zugangsdaten auswählen

Diese Seite richtet sich an App-Entwickler und technische Leitungen, die entscheiden, wie sich eine App
authentifiziert. Die richtige Wahl hängt davon ab, für wen die App handelt und ob sich ein Geheimnis dort bewahren
lässt, wo der Code läuft.

## Inhalt

1. [Die Regel](#die-regel)
2. [Nach Art der App](#nach-art-der-app)
3. [Anzulegende Konten](#anzulegende-konten)
4. [Niemals](#niemals)

## Die Regel

Zugangsdaten sind ein Benutzer. Was auch immer eine App sendet, der Server führt den Aufruf als ein Benutzerkonto aus
und prüft die Richtlinien dieses Kontos
([Wie Exponential-Inhalte in eine App gelangen](../wie-exponential-inhalte-in-eine-app-gelangen.md#was-entscheidet-was-sie-sehen)).
Die Frage lautet also nicht „welches Token-Format“, sondern „als welcher Benutzer soll dieser Aufruf laufen, und wer
kann das Geheimnis sehen“.

## Nach Art der App

| App | Läuft als | Zugangsdaten | Wo das Geheimnis liegt |
|---|---|---|---|
| Öffentliche Lese-App (Nachrichten, Katalog), ohne Anmeldung | ein Integrationsbenutzer nur zum Lesen | ein persönlicher API-Schlüssel oder ein OAuth-Token dieses Benutzers, **von Ihrem Backend gehalten**, das Exponential für die App aufruft | Ihr Server |
| App, deren Benutzer sich mit ihrem Exponential-Konto anmelden | jeder Benutzer | OAuth2-Autorisierungscode-Ablauf über Ihr Backend | Client-Secret auf Ihrem Server; Tokens auf dem Gerät oder auf Ihrem Server |
| Interne App für Mitarbeiter auf verwalteten Geräten | jeder Mitarbeiter | OAuth2 oder expservices-Tokens, bei der ersten Anmeldung erzeugt | sicherer Speicher des Geräts |
| Kiosk oder Anzeigebildschirm | ein Benutzer nur zum Lesen für diesen Bildschirm | ein persönlicher API-Schlüssel mit dem Geltungsbereich `read`, einer je Gerät | das Gerät, von der IT verwaltet |
| Entwicklung und Tests | ein Testbenutzer | ein persönlicher API-Schlüssel auf einer Testinstallation | Umgebungsvariablen, ein CI-Secret |

Die erste Zeile verdient eine Erklärung: Eine veröffentlichte App ist öffentlicher Code. Alles, was darin gebündelt
ist, ein Schlüssel, ein Token oder ein Client-Secret, kann herausgelöst werden. Ein Benutzer nur zum Lesen begrenzt den
Schaden, beseitigt ihn aber nicht; ein Backend, das die Zugangsdaten hält, Caching ergänzt und ohne App-Release
geändert werden kann, ist der robuste Entwurf ([Ein Backend für Ihre App](ein-backend-fuer-ihre-app.md)).

## Anzulegende Konten

- **Ein Integrationsbenutzer je App**, nie das Konto einer Person und nie der Administrator.
- **Eine Rolle je App** mit nur den Richtlinien, die sie braucht: `content/read`, beschränkt auf die Bereiche oder
  Teilbäume, die die App zeigt, und Schreibrichtlinien nur unterhalb des Ordners, in den die App schreibt
  ([Berechtigungen für App-Benutzer](../veroeffentlichungsablauf/berechtigungen-fuer-app-benutzer.md)).
- **Eine Zugangsdatenvariante je Installation eines Werkzeugs**, damit sich jede einzeln widerrufen lässt
  ([OAuth und API-Schlüssel: gute Praxis](../../ersteller/anwendungsentwickler/oauth-und-api-schluessel.md#gute-praxis)).

## Niemals

- Liefern Sie nie einen persönlichen API-Schlüssel, ein expservices-Token oder ein OAuth-Client-Secret in einer App aus.
- Verwenden Sie für eine App nie das Administratorkonto oder ein Konto mit `setup`-Rechten.
- Senden Sie Zugangsdaten nie in einer URL: Ein API-Schlüssel im Query-String wird abgelehnt, selbst wenn er gültig ist,
  und URLs landen in Protokollen.
- Fragen Sie Benutzer nie in einem Bildschirm einer nativen App nach ihrem Exponential-Passwort, um es zu speichern;
  wenn sich Ihre App mit einem Passwort bei den Remote-Diensten anmeldet, senden Sie es einmal und behalten Sie nur das
  Token, das damit erzeugt wird ([expservices-Tokens in Apps](expservices-tokens-in-apps.md)).

## Verwandte Seiten

- [OAuth2-Autorisierungscode-Ablauf](oauth2-autorisierungscode-ablauf.md)
- [Persönliche API-Schlüssel für Werkzeuge](persoenliche-api-schluessel-fuer-werkzeuge.md)
- [OAuth und API-Schlüssel: welche Zugangsdaten](../../ersteller/anwendungsentwickler/oauth-und-api-schluessel.md#welche-zugangsdaten)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
