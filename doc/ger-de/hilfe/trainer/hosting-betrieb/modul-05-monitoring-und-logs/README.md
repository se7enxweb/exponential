---
title: "Modul 5: Monitoring und Logs"
audience: trainer, hosting, admin
category: training
order: 50
summary: Das fünfte Modul von Hosting und Betrieb, Monitoring und Zustandsprüfungen, der Log-Viewer und die Rotation, Anfrage-Logs und das Sicherheitslog sowie Runbooks für eine Website, die nicht erreichbar oder langsam ist. Vier Lektionen, 2 Stunden 55 Minuten.
keywords: hosting und betrieb, modul 5, monitoring, zustandsprüfung, logs, log-viewer, rotation, 404.log, security.log, runbook, website nicht erreichbar, langsame website, hosting and operations, module 5, health check, log viewer, site down, slow site
type: help
---
# Modul 5: Monitoring und Logs

Ein Hoster erfährt von Problemen durch das Monitoring und löst sie mit den Logs. Dieses Modul vermittelt die vier
Schichten des Monitorings und die Statusbefehle dahinter, den Log-Viewer und seine Rotation, die Anfrage- und
Sicherheitslogs und endet mit einer praktischen Übung: vorbereitete Fehler mit den Runbooks für eine nicht erreichbare
und eine langsame Website diagnostizieren.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Dauer | 2 Stunden 55 Minuten (vier Lektionen) |
| Lektionen | 4 |
| Niveau | Fortgeschrittene |
| Form | Praxisorientiert; Lektion 5.4 in Paaren |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [5.1 Monitoring und Zustandsprüfungen](monitoring-und-zustandspruefungen.md) | 45 Min. | Die Statusbefehle ausführen, `/Q/health` lesen, ein 503 im Wartungsmodus sehen |
| [5.2 Der Log-Viewer und die Log-Rotation](log-viewer-und-rotation.md) | 45 Min. | Logs verfolgen und durchsuchen, Permalinks verwenden, ein Aufräumen vorab ansehen |
| [5.3 Anfrage-Logs und das Sicherheitslog](anfrage-und-sicherheitslogs.md) | 40 Min. | 404- und Sondierungszeilen lesen, einen Ausschluss für den Klassifizierer entwerfen |
| [5.4 Runbooks für eine Website, die nicht erreichbar oder langsam ist](runbooks-website-ausgefallen-und-langsam.md) | 45 Min. | Einen vorbereiteten Fehler diagnostizieren und eine Störungsnotiz schreiben |
| [5.5 Die Systemkarte](uebung-zur-systemkarte.md) (optional) | 25 Min. | Einen Server mit `exp:expfetch` lesen, nach seinen Hinweisen handeln, ihn mit `--watch` verfolgen, zwei `exp:info --json` vergleichen |

## Dieses Modul vorbereiten

- Eine Schulungsinstallation pro Person, ausgeliefert von Velocity, mit einem Administratorkonto.
- Für Lektion 5.4: ein vorbereiteter Fehler pro Paar (Wartungsmodus angelassen, die Datenbank unerreichbar gemacht,
  `vendor/` umbenannt oder das View-Caching in einem Override ausgeschaltet). Schreiben Sie jeden Fehler auf und machen
  Sie ihn nach der Lektion rückgängig.
- Sagen Sie den Teilnehmenden, dass die Sondierungsanfragen aus Lektion 5.3 nur an ihre eigene Installation gehen.
- Proben Sie einen Benchmark auf den Schulungsrechnern und wählen Sie einen Wert für `--requests`, der die anderen nicht
  ausbremst.

## Zeitplan

| Zeit | Punkt |
|---|---|
| 0:00 | Lektion 5.1 (45 Min.) |
| 0:45 | Lektion 5.2 (45 Min.) |
| 1:30 | Pause (15 Min.) |
| 1:45 | Lektion 5.3 (40 Min.) |
| 2:25 | Lektion 5.4 (45 Min.) |
| 3:10 | Ende (2 Std. 55 Min. Lektionen plus eine Pause) |

## Verwandte Seiten

- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Überblick über das Monitoring](../../../hosting/betrieb/ueberblick-ueber-die-ueberwachung.md)
- [Runbooks](../../../hosting/runbooks/README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
