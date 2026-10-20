---
title: "Thema: Betriebswerkzeuge für Administratoren"
audience: press
category: press-media
order: 92
summary: Ein Themenbeitrag über die Werkzeuge, die Exponential den Menschen gibt, die Websites betreiben - Wartungsmodus, Log-Viewer, Anfrage-Logs, Inhaltsprüfungen, die Prüfung der Dateikonsistenz, Steuerung der Cronjobs, die Debug-Leiste und der INI-Befehl - mit Fakten, Quellen und den Aussagen, die Sie vermeiden sollten.
keywords: Thema, Betrieb, Administratoren, Wartungsmodus, Log-Viewer, Anfrage-Logs, Inhaltsprüfungen, Cronjobs, Debug-Leiste, exp:ini, Systemadministration, DevOps, story, operations, administrators, maintenance mode, log viewer, request logs, content checks, cronjobs, debug bar, sysadmin
type: help
---
# Thema: Betriebswerkzeuge für Administratoren

Diese Seite ist für Journalisten, die für Systemadministratoren schreiben. Vieles aus der Arbeit an 6.0.x ist für
Besucher nicht sichtbar: Es sind Werkzeuge für die Menschen, die Websites am Laufen halten.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Die Werkzeuge](#die-werkzeuge)
3. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
4. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)

## Das Thema in einem Absatz

Der Wartungsmodus „nimmt die gesamte öffentliche Website vom Netz, zeigt Besuchern eine freundliche Seite und bringt
sie mit einem Befehl oder einer Schaltfläche zurück“, und die Installer nutzen ihn, damit „eine Website, die gerade
installiert wird, nie eine halb gebaute Seite ausliefert“ ([Wartungsmodus](../../../eng-us/features/6.0/maintenance-mode.md)).
Darum herum ergänzt die Linie 6.0.x einen Log-Viewer, Anfrage-Logs, Inhaltsprüfungen, eine Prüfung der
Dateikonsistenz, die Steuerung der Cronjobs, eine Debug-Leiste und einen Konsolenbefehl für Einstellungen, jedes mit
einer eigenen Seite dokumentiert.

## Die Werkzeuge

| Werkzeug | Was es tut | Seite |
|---|---|---|
| Wartungsmodus | Nimmt die Website vom Netz und wieder online, aus der Konsole oder der Administration | [Wartungsmodus](../../../eng-us/features/6.0/maintenance-mode.md) |
| Log-Viewer | Liest die Logs in der Administration | [Log-Viewer](../../../eng-us/features/6.0/log-viewer.md) |
| Anfrage-Logs | Zeichnet Anfragen zur Diagnose auf | [Anfrage-Logs](../../../eng-us/features/6.0/request-logs.md) |
| Log-Rotation | Hält Logdateien in Grenzen | [Log-Rotation](../../../eng-us/features/6.0/log-rotation.md) |
| Inhaltsprüfungen | Findet Probleme in Inhalten | [Inhaltsprüfungen](../../../eng-us/features/6.0/content-checks.md) |
| Prüfung der Dateikonsistenz | Vergleicht Dateien mit dem Manifest des Releases | [Prüfung der Dateikonsistenz](../../../eng-us/features/6.0/file-consistency-check.md) |
| Cronjobs | Geplante Jobs pausieren, filtern und ausführen | [Cronjob-Konsole](../../../eng-us/features/6.0/cronjobs-console.md), [Cronjobs pausieren](../../../eng-us/features/6.0/cronjob-pause.md) |
| Debug-Leiste | Debug-Informationen für Entwickler | [Exp-Debug-Leiste](../../../eng-us/features/6.0/exp-debug-bar.md) |
| `exp:ini` | Liest und ändert Einstellungen aus der Konsole | [Der Befehl exp:ini](../../../eng-us/features/6.0/exp-ini-command.md) |
| Reparatur aus dem Browser | Behebt bekannte Probleme ohne Shell | [Reparatur aus dem Browser](../../../eng-us/features/6.0/repair-from-the-browser.md) |

## Was Sie nicht behaupten sollten

| Sagen Sie nicht | Sagen Sie stattdessen |
|---|---|
| „Upgrades ohne Ausfallzeit“ | Der Wartungsmodus zeigt während der Arbeit eine freundliche Seite; er ist kein Mechanismus ohne Ausfallzeit |
| „Selbstheilend“ | Reparaturwerkzeuge beheben dokumentierte Probleme auf Anforderung |
| Alles davon „veröffentlicht“ | Mehreres gehört zur Linie 6.0.15; prüfen Sie jede Seite und den Changelog |

## Blickwinkel und Ideen für Überschriften

- „Die unspektakulären Funktionen, die Websites am Laufen halten.“
- „Ein CMS, das Administratoren sagt, was es gerade tut.“

## Verwandte Seiten

- [Thema: Audit-Trail](../../../eng-us/help/press/story-audit-trail.md)
- [Thema: das Velocity Control Panel](../../../eng-us/help/press/story-velocity-control-panel.md)
- [Thema: Inhaltsjobs](../../../eng-us/help/press/story-content-jobs.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
