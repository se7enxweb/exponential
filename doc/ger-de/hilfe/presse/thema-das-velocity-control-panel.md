---
title: "Thema: das Velocity Control Panel"
audience: press
category: press-media
order: 93
summary: Ein Themenbeitrag über die Browseransichten, die jeder Exponential-Velocity-Server von sich selbst hat - ein Live-Dashboard für Anfragen, Worker und Speicher und ein passwortgeschütztes Control Panel für Apps, Domains, Zertifikate, Cache, Logs und Einstellungen - mit Fakten, Quellen und Aussagen, die Sie vermeiden sollten.
keywords: Thema, Velocity, Control Panel, Dashboard, Serververwaltung, Zertifikate, Überwachung, Worker, WebSocket, Betrieb, Administratoren, story, control panel, server management, certificates, monitoring, workers, operations, administrators
type: help
---
# Thema: das Velocity Control Panel

Diese Seite ist für Journalisten, die über Betrieb und Hosting berichten. Velocity ist nicht nur ein Webserver; es
zeigt und verwaltet sich selbst im Browser.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
3. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
4. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)

## Das Thema in einem Absatz

„Every Velocity server has two built-in web views of itself“: ein **Dashboard** unter `/Q/dashboard` mit Anfragen,
Workern, Speicher, Statuscodes und den häufigsten Pfaden live über einen WebSocket, und ein **Control Panel** unter
`/Q/panel`, „a password-protected admin page that manages apps, domains, certificates, the response cache, logs,
workers and settings“ ([Velocity Control Panel und Dashboard](../../../eng-us/features/6.0/velocity-control-panel.md)). „They
answer to the server, not to your application, so they keep working when the application is broken.“

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| Dashboard und Control Panel sind in jeden Velocity-Server eingebaut | [Velocity Control Panel und Dashboard](../../../eng-us/features/6.0/velocity-control-panel.md) |
| Eine neue Installation verlangt ein echtes Passwort vor jeder anderen Aktion im Panel | [Erste Anmeldung](../../../eng-us/features/6.0/velocity-control-panel.md#first-sign-in) |
| Für das Panel ist eine Zwei-Faktor-Authentifizierung verfügbar | [Zwei-Faktor-Authentifizierung](../../../eng-us/features/6.0/velocity-control-panel.md#two-factor-authentication-optional) |
| Administratoren können im Browser ein Zertifikat erneuern, den Cache leeren oder gefilterte Logs lesen | [Velocity Control Panel und Dashboard](../../../eng-us/features/6.0/velocity-control-panel.md) |
| Die Seiten des Servers lassen sich umgestalten, ohne die Engine anzufassen | [Die Seiten des Servers umgestalten](../../../eng-us/features/6.0/velocity-control-panel.md#restyle-the-servers-pages-designs) |
| Gilt für Exponential Velocity 0.0.4.x | ebenda |

## Was Sie nicht behaupten sollten

| Sagen Sie nicht | Sagen Sie stattdessen |
|---|---|
| „Ersetzt ein Hosting-Panel“ | Es verwaltet den Velocity-Server selbst |
| „Für jeden offen“ | Passwortgeschützt, optional mit zweitem Faktor |
| Bildschirmfotos mit echten Domains oder Logzeilen | Verwenden Sie einen Demoserver ([Geheimnisse vom Bildschirm fernhalten](geheimnisse-vom-bildschirm-fernhalten.md)) |

## Blickwinkel und Ideen für Überschriften

- „Ein Webserver, der Ihnen live zeigt, was er tut.“
- „Wenn die App ausfällt, funktioniert das eigene Panel des Servers weiter.“

## Verwandte Seiten

- [Thema: Exponential Velocity](thema-exponential-velocity.md)
- [Thema: Betriebswerkzeuge für Administratoren](thema-betriebswerkzeuge-fuer-administratoren.md)
- [Demo-Skript: Velocity](../../../eng-us/help/press/demo-script-velocity.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
