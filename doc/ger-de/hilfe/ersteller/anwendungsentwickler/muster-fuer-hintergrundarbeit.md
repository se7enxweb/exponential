---
title: "Hintergrundarbeit: Warteschlangen, Cronjobs und Inhalts-Jobs"
audience: app-creator, developer
category: app-creation
order: 430
summary: Wählen Sie, wo die langsame oder regelmäßige Arbeit einer Anwendung läuft - in der Anfrage, in einem Workflow, einem Cronjob-Teil, einem Job von expqueue oder einem Inhalts-Job des Kernels - mit einer Entscheidungstabelle und Mustern für Importe, Benachrichtigungen und Integrationen.
keywords: Hintergrundarbeit, asynchron, Warteschlange, Cronjob, Inhalts-Jobs, Workflow, Entscheidung, Muster, Importe, Integrationen, Anwendungsentwickler, background work, async, queue, cronjob, content jobs, workflow, expqueue, decision, patterns, imports, integrations, app creator
type: help
---
# Hintergrundarbeit: Warteschlangen, Cronjobs und Inhalts-Jobs

Diese Seite ist für Anwendungsentwickler, die entscheiden, wo Arbeit läuft. Eine Web-Anfrage sollte schnell antworten;
alles, was langsam, regelmäßig oder umfangreich ist, gehört woandershin. Exponential bietet dafür vier Orte.

![Das Dashboard der Job-Warteschlange, in dem die von einer Anwendung eingereihte Hintergrundarbeit erscheint](../../../../eng-us/help/images/creators/job-queue-dashboard.webp)

## Inhalt

1. [Die vier Orte](#die-vier-orte)
2. [Entscheidungstabelle](#entscheidungstabelle)
3. [Muster](#muster)
4. [Häufige Fehler](#häufige-fehler)

## Die vier Orte

| Ort | Läuft | Lesen Sie |
|---|---|---|
| Cronjob-Teil | nach Zeitplan, als Stapel | [Cronjobs für Anwendungen](cronjobs-fuer-anwendungen.md) |
| Job von expqueue | sobald ein Worker frei ist, pro Job, mit Wiederholungen | [Warteschlangen mit expqueue](warteschlangen-mit-expqueue.md) |
| Inhalts-Job | große Inhaltsoperationen des Kernels (Entfernen, Kopieren, Verschieben ...) in Stapeln | [Inhalts-Jobs](inhalts-jobs.md) |
| Auf Cron verschobenes Workflow-Ereignis | ein Schritt einer Inhaltsoperation, der warten muss | [Workflows, Trigger und Ereignisse](workflows-und-ereignisse.md) |

## Entscheidungstabelle

| Die Arbeit | Ort |
|---|---|
| Stündlich einen Feed importieren | Cronjob-Teil |
| Nach jedem Veröffentlichen einen externen Dienst aufrufen | Workflow oder Event-Listener reiht einen Job von expqueue ein |
| Das Veröffentlichen anhalten, bis jemand freigibt | auf Cron verschobenes Workflow-Ereignis |
| Einen Teilbaum mit 50 000 Knoten entfernen oder verschieben | Inhalts-Job |
| Morgen um 9 Uhr eine Erinnerungsmail senden | Job von expqueue mit `not_before` |
| Jede Nacht einen Bericht neu erstellen | Cronjob-Teil |
| Alternativtexte für einen ganzen Teilbaum erzeugen | Jobs von expqueue in einer Gruppe (wie es expai tut) |

## Muster

- **Vom Ereignis in die Warteschlange**: Ein Listener oder ein Workflow-Ereignis reiht einen Job mit einem
  Idempotenzschlüssel (`"archive:<object>:<version>"`) ein und kehrt sofort zurück; der Handler übernimmt den langsamen
  Aufruf.
- **Von Cron in die Warteschlange**: Ein Cronjob-Teil findet, was fällig ist, und reiht pro Element einen Job ein; die
  Worker führen sie parallel und mit Wiederholungen aus.
- **Stapel mit Checkpoint**: Ein langer Job speichert seinen Fortschritt, sodass eine Wiederholung weitermacht, statt
  von vorn zu beginnen.
- **Auffächern mit einer Gruppe**: viele Jobs in einer Gruppe, mit Fortschritt und Abbruch für das Ganze.

## Häufige Fehler

- Langsame HTTP-Aufrufe innerhalb der Veröffentlichungsanfrage: Die Redaktion wartet, und ein Fehler bricht das
  Veröffentlichen ab.
- Ein Cronjob-Teil, der länger läuft als sein Intervall.
- Geheimnisse in den Nutzdaten von Jobs.
- Arbeit, die nicht idempotent ist und nach einer Wiederholung doppelt läuft.

## Verwandte Seiten

- [Warteschlangen mit expqueue](warteschlangen-mit-expqueue.md)
- [Cronjobs für Anwendungen](cronjobs-fuer-anwendungen.md)
- [Anwendungen skalieren](anwendungen-skalieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
