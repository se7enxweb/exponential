---
title: Ein Backend für Ihre App
audience: mobile-app-developer, app-creator, developer, project-manager
category: mobile-apps
order: 230
summary: Warum die meisten mobilen Apps auf Exponential mit einem kleinen eigenen Backend sprechen sollten (einem Backend for Frontend) statt direkt mit Exponential - das Client-Secret, Zugangsdaten nur zum Lesen, Caching, das Zuschneiden von Antworten und Verhaltensänderungen ohne App-Release - und was ein solches Backend Schritt für Schritt tut.
keywords: Backend, Backend for Frontend, BFF, Proxy, Client-Secret, OAuth, Token-Austausch, Caching, API-Schlüssel, Server, Mobile App, Architektur, back end, backend for frontend, client secret, token exchange, API key, mobile app, architecture
type: help
---
# Ein Backend für Ihre App

Diese Seite richtet sich an App-Entwickler und technische Leitungen, die die Architektur einer App auf Exponential
entwerfen. Sie erklärt, warum ein kleiner Server zwischen der App und Exponential meist der richtige Entwurf ist und was
dieser Server tut. Es ist ein allgemeiner Architekturrat; Exponential liefert keinen solchen Server aus.

## Inhalt

1. [Warum ein Backend](#warum-ein-backend)
2. [Was es tut](#was-es-tut)
3. [Der Anmeldeablauf über das Backend](#der-anmeldeablauf-über-das-backend)
4. [Eine öffentliche App ohne Anmeldung](#eine-öffentliche-app-ohne-anmeldung)
5. [Wann Sie darauf verzichten können](#wann-sie-darauf-verzichten-können)

## Warum ein Backend

| Grund | Einzelheit |
|---|---|
| Der OAuth-Token-Endpunkt braucht das Client-Secret | `client_secret` ist für beide Grants Pflicht ([OAuth2-Autorisierungscode-Ablauf](oauth2-autorisierungscode-ablauf.md#grenzen-der-implementierung)); ein Secret in einer App ist nicht geheim |
| Zugangsdaten nur zum Lesen gehören auf einen Server | eine öffentliche Lese-App würde sonst einen Schlüssel ausliefern, den jeder herauslösen kann |
| Antworten lassen sich zuschneiden | eine REST-Liste mit Bildalias-URLs aus `expimage::url` zu einer Antwort für einen Bildschirm verbinden |
| Caching | ein Cache vor Exponential bedient jedes Telefon ([Caching auf dem Server](../offline-und-leistung/caching-auf-dem-server.md)) |
| Änderungen ohne App-Release | ein neues Feld, ein neuer Ordner oder ein Umweg für eine Lücke geht sofort live |
| CORS für Web-Apps | das Backend kann auf demselben Origin wie die Web-App liegen ([Web-Apps, CORS und CSP](../offline-und-leistung/web-apps-cors-und-csp.md)) |

## Was es tut

- Es hält das OAuth-Client-Secret oder die Integrationszugangsdaten nur zum Lesen in einem eigenen Geheimnisspeicher
  ([Umgebungseinstellungen und Geheimnisse](../../ersteller/anwendungsentwickler/umgebungseinstellungen-und-geheimnisse.md) beschreibt
  dieselbe Disziplin auf der Seite von Exponential).
- Es stellt eine kleine eigene API bereit, die für die Bildschirme der App entworfen ist.
- Es ruft die REST-API und die Remote-Dienste von Exponential mit den Zugangsdaten auf, gibt Fehler als klare Status
  weiter und speichert zwischen, was zwischengespeichert werden darf.
- Es authentifiziert die Benutzer der App, wenn die App eigene Konten hat, oder reicht das OAuth-Token des Benutzers
  durch.

Es kann in jeder Sprache geschrieben und überall betrieben werden, von wo aus die Exponential-Installation über HTTPS
erreichbar ist. Es kann auch innerhalb der Exponential-Installation als Erweiterung mit einer ezjscore-Serverfunktion
oder einem REST-Provider leben; das ist Arbeit für Anwendungsentwickler
([JSON-Endpunkte mit ezjscore](../../ersteller/anwendungsentwickler/json-endpunkte-mit-ezjscore.md),
[Ihr eigener REST-Provider](../../ersteller/anwendungsentwickler/rest-api.md#ein-eigener-rest-provider)).

## Der Anmeldeablauf über das Backend

1. Die App öffnet die Autorisierungsseite im Systembrowser, mit der Adresse des Backends als `redirect_uri` (genau so
   unter **Setup > oAuth-Verwaltung** registriert).
2. Vorher gibt das Backend der App einen Einmalwert, der an die Sitzung der App gebunden ist; die Autorisierungsseite
   gibt `state` nicht zurück, also muss das Backend den Rückruf auf anderem Weg der Sitzung zuordnen, die ihn
   ausgelöst hat, zum Beispiel über ein kurzlebiges Cookie, das in derselben Browsersitzung gesetzt wurde.
3. Die Website leitet den Browser mit `code` an das Backend weiter. Das Backend tauscht ihn mit dem Secret bei
   `POST /api/auth/v1/oauth/token` ein.
4. Das Backend gibt der App eine eigene Sitzung (oder das Zugriffstoken, wenn Sie Tokens auf Geräten akzeptieren) und
   behält das Refresh-Token auf dem Server.
5. Wenn das Zugriffstoken abläuft, erneuert das Backend es; die App sieht das Secret nie.

Die Weiterleitung vom Backend zurück in die App (ein Universal Link oder App Link Ihrer App) ist gewöhnliche Arbeit auf
der mobilen Plattform und kein Teil von Exponential.

## Eine öffentliche App ohne Anmeldung

1. Legen Sie einen Integrationsbenutzer mit einer Rolle an, die nur lesen darf, was die App zeigt.
2. Geben Sie dem Backend Zugangsdaten dieses Benutzers (ein persönlicher API-Schlüssel mit dem Geltungsbereich `read`
   funktioniert und lässt sich leicht austauschen).
3. Das Backend ruft `GET /api/ezp/v2/...` auf, speichert die Antworten kurz zwischen und bedient die App.
4. Die App hat überhaupt keine Zugangsdaten für Exponential.

## Wann Sie darauf verzichten können

- Interne Apps auf verwalteten Geräten, bei denen sich jeder Benutzer anmeldet und das Risiko eines herausgelösten
  Schlüssels schriftlich akzeptiert ist.
- Ein Kiosk mit einem eigenen Schlüssel nur zum Lesen, von der IT verwaltet.
- Prototypen und Tests gegen eine Testinstallation.

Planen Sie auch dann, wo das Backend hinkäme; es später hinzuzufügen, ändert die Netzwerkschicht der App.

## Verwandte Seiten

- [Zugangsdaten auswählen](zugangsdaten-auswaehlen.md)
- [OAuth und API-Schlüssel](../../ersteller/anwendungsentwickler/oauth-und-api-schluessel.md)
- [Zuverlässigkeit des App-Backends (für Unternehmen)](../../unternehmen/mobil-und-headless/zuverlaessigkeit-des-app-backends.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
