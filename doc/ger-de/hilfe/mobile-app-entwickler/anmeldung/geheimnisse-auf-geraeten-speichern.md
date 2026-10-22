---
title: Geheimnisse auf Geräten speichern
audience: mobile-app-developer, developer
category: mobile-apps
order: 260
summary: Zugangsdaten für Exponential auf Telefonen und in Browsern sicher aufbewahren - was überhaupt auf einem Gerät liegen darf, die sicheren Speicher der Plattformen (Keychain, Keystore), was Sie nie speichern, Protokolle und Absturzberichte, Backups, Abmelden und Widerruf.
keywords: Geheimnisse, sicherer Speicher, Keychain, Keystore, EncryptedSharedPreferences, Token-Speicherung, Refresh-Token, Protokollierung, Absturzberichte, Backups, abmelden, widerrufen, Browserspeicher, Mobile App, Sicherheit, secrets, secure storage, token storage, refresh token, logging, crash reports, sign out, revoke, browser storage, mobile app, security
type: help
---
# Geheimnisse auf Geräten speichern

Diese Seite richtet sich an App-Entwickler, die entscheiden, wo Tokens in der App liegen. Sie verbindet, was die
Quellen von Exponential über Zugangsdaten sagen, mit allgemeiner Plattformpraxis; die Namen der Plattformen sind
Hinweise, keine Anleitung für eine bestimmte SDK-Version.

## Inhalt

1. [Was auf einem Gerät liegen darf](#was-auf-einem-gerät-liegen-darf)
2. [Wo es hingehört](#wo-es-hingehört)
3. [Aus Protokollen heraushalten](#aus-protokollen-heraushalten)
4. [Abmelden](#abmelden)
5. [Browser](#browser)

## Was auf einem Gerät liegen darf

| Geheimnis | Auf dem Gerät? |
|---|---|
| OAuth-Client-Secret | **nie**: Es weist Ihre App gegenüber dem Server aus, und der Token-Endpunkt braucht es ([Ein Backend für Ihre App](ein-backend-fuer-ihre-app.md)) |
| Ein persönlicher API-Schlüssel (`expk_`) einer Person oder eines Integrationsbenutzers | **nie** in einer veröffentlichten App; nur auf verwalteten Kiosken nach Entscheidung des Website-Inhabers |
| Ein OAuth-Zugriffstoken des angemeldeten Benutzers | vertretbar, im sicheren Speicher; es lebt `TokenTTL` Sekunden (standardmäßig eine Stunde) |
| Ein OAuth-Refresh-Token | besser im Backend halten; wenn auf dem Gerät, dann im sicheren Speicher |
| Ein expservices-Token (`expt_`) des angemeldeten Benutzers, eines je Gerät | vertretbar, im sicheren Speicher ([expservices-Tokens in Apps](expservices-tokens-in-apps.md)) |
| Das Passwort des Benutzers | **nie** gespeichert; einmal senden, um ein Token zu erzeugen, dann vergessen |

Die Quellen sind eindeutig: „Store the token in your platform's secret store (keychain, keystore, a CI secret), never
in source code or a URL“
([Remote-Dienste und Apps](../../../anleitungen/entfernte-dienste-und-apps.md#5-ein-persönliches-api-token-für-skripte-und-apps-3-minuten)),
und die expservices-Referenz nennt Keychain, Keystore, KWallet und libsecret
([Authentifizierung mit einem API-Token](../../../bc/6.0/backend-dienste-ueber-ezjscore.md#authentifizierung-mit-einem-api-token)).

## Wo es hingehört

| Plattform | Speicher |
|---|---|
| iOS, iPadOS, macOS | die Keychain, mit einer Zugriffsklasse, die zur App passt (zum Beispiel verfügbar nach dem ersten Entsperren, nur dieses Gerät) |
| Android | der Android Keystore, direkt oder über eine Bibliothek für verschlüsselte Einstellungen, die ihn nutzt |
| Plattformübergreifende Frameworks | ein Plugin für sicheren Speicher, das auf Keychain und Keystore abbildet; prüfen Sie, dass es das auf beiden wirklich tut |
| Linux-Desktop | libsecret oder KWallet |

Nicht in einfachen Einstellungen, Dateien, SQLite-Tabellen oder App-Ressourcen.

## Aus Protokollen heraushalten

- Entfernen Sie die Header `Authorization` und `X-Exp-Token` aus HTTP-Logging-Interceptors, auch in Debug-Builds, die
  Tester verwenden.
- Legen Sie Zugangsdaten nie in eine URL: Sie landen in Serverprotokollen, Proxy-Protokollen und der Analyse. REST lehnt
  einen persönlichen API-Schlüssel im Query-String ab, selbst wenn er gültig ist.
- Entfernen Sie Tokens aus Absturzberichten und Analyse-Ereignissen.
- Jeder Schlüssel beginnt mit `expk_` und jedes expservices-Token mit `expt_`: Nehmen Sie beide Präfixe in die
  Secret-Scanner des App-Repositorys auf.

## Abmelden

- Löschen Sie die gespeicherten Tokens aus dem sicheren Speicher.
- Widerrufen Sie auf dem Server, wo Sie können: `expsession::tokenRevoke` für ein expservices-Token. Bei OAuth verwerfen
  Sie die Tokens und lassen das Zugriffstoken ablaufen; das Entfernen der ganzen Anwendung unter
  **Setup > oAuth-Verwaltung** entfernt auch ihre Autorisierungen, Tokens und Codes, für jeden Benutzer.
- Schließen Sie den sicheren Speicher von Gerätebackups aus, wenn die Voreinstellung Ihrer Plattform ihn einschließen
  würde, damit ein wiederhergestelltes Backup keine Sitzung auf einem anderen Gerät wiederbelebt.

## Browser

Eine Web-App hat keinen sicheren Speicher, der mit Keychain oder Keystore vergleichbar wäre. Bevorzugen Sie ein Backend
auf demselben Origin, das Tokens auf dem Server hält und dem Browser ein eigenes Sitzungscookie gibt
([Web-Apps, CORS und CSP](../offline-und-leistung/web-apps-cors-und-csp.md)). Legen Sie langlebige Tokens nicht in
`localStorage` ab.

## Verwandte Seiten

- [Sicherheitscheckliste](../checklisten/sicherheitscheckliste.md)
- [Zugangsdaten auswählen](zugangsdaten-auswaehlen.md)
- [Sicherheit für Anwendungen](../../ersteller/anwendungsentwickler/sicherheit-fuer-anwendungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
