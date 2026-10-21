---
title: Inhalts-Jobs
audience: app-creator, developer, admin
category: app-creation
order: 320
summary: Wie der Kernel große Inhaltsoperationen im Hintergrund ausführt - Entfernen, Kopieren, Verschieben, Verbergen, Sektionen, Zustände, Platzierungen, Paketinstallationen und Audit-Exporte - mit Fortschrittsseite, Befehl, Cronjob-Teil, Einstellungen und dem, was eine Anwendung davon nutzen kann.
keywords: Inhalts-Jobs, Hintergrundjobs, Stapel, großer Teilbaum, Entfernen, Kopieren, Verschieben, Anwendungsentwickler, content jobs, background jobs, expcontentjob, content/job, content/jobs, ContentJobSettings, batch, large subtree, remove, copy, move, contentjobs cronjob, app creator
type: help
---
# Inhalts-Jobs

Diese Seite ist für Anwendungsentwickler, die mit großen Teilbäumen arbeiten. Der Kernel macht aus großen
Inhaltsoperationen **Inhalts-Jobs**, die im Hintergrund in Stapeln laufen, mit einer Fortschrittsseite, statt in einer
einzigen langen Anfrage ([Inhalts-Jobs](../../../bc/6.0/inhaltsjobs.md)).

![Das Dashboard der Job-Warteschlange, das die Inhalts-Jobs und ihren Zustand auflistet](../../../../eng-us/help/images/creators/job-queue-dashboard.webp)

## Inhalt

1. [Was zu einem Job wird](#was-zu-einem-job-wird)
2. [Der Befehl](#der-befehl)
3. [Einstellungen](#einstellungen)
4. [Zuverlässigkeit](#zuverlässigkeit)
5. [Jobs aus einer Anwendung nutzen](#jobs-aus-einer-anwendung-nutzen)

## Was zu einem Job wird

Entfernen, Kopieren, Verschieben, Verbergen, Einblenden, Sektionszuweisung, Zustände für Teilbäume, Hinzufügen und
Entfernen von Platzierungen, Paketinstallationen und Audit-Exporte (`JobTypes[]`). Die Redaktion wählt "jetzt" oder
"im Hintergrund"; ab `SynchronousLimit` Elementen ist der Hintergrund vorausgewählt, oberhalb von `NowLimit` ist er die
einzige Wahl. Fortschritt: `content/job/<id>`; Liste: `content/jobs`.

## Der Befehl

```bash
php bin/php/expcontentjob.php list [--all]
php bin/php/expcontentjob.php show <id>
php bin/php/expcontentjob.php remove <node>[,<node>...] [--trash|--delete] [--background]
php bin/php/expcontentjob.php copy <source> <new parent> [--background]
php bin/php/expcontentjob.php move <node> <new parent>
php bin/php/expcontentjob.php run <id> | resume <id> | cancel <id>
```

Ebenso `php bin/php/console exp:expcontentjob ...`.

## Einstellungen

`content.ini [ContentJobSettings]`:

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `SynchronousLimit` | 50 | ab so vielen Elementen ist der Hintergrund vorausgewählt |
| `NowLimit` | 1000 | oberhalb dieser Anzahl wird "jetzt" nicht angeboten |
| `BatchSize` | 50 | Knoten pro Stapel, jeweils eine Transaktion |
| `BatchPause` | 0 | Millisekunden zwischen den Stapeln (100 bis 200 bei SQLite) |
| `QueuedGrace` | 60 | Sekunden, bevor der Cronjob-Teil einen Job startet, den niemand gestartet hat |
| `MaxAttempts` | 5 | Worker-Starts ohne Fortschritt, bevor der Job fehlschlägt |
| `KeepDays` | 30 | abgeschlossene Jobs werden nach so vielen Tagen entfernt |

## Zuverlässigkeit

Der Cronjob-Teil `contentjobs` (in der Gruppe frequent) startet Jobs, deren Worker nie gestartet ist, und setzt Jobs
fort, deren Worker abgebrochen ist; so geht ein Job nicht verloren, wenn der Webserver keinen Worker starten kann. In
einem Cluster muss das Job-Verzeichnis geteilt werden.

## Jobs aus einer Anwendung nutzen

- Rufen Sie für große Operationen auf Inhalten den Befehl (oder seinen Dienst) auf, statt in einer Anfrage über
  Tausende Knoten zu iterieren.
- Verwenden Sie für Ihre eigene Hintergrundarbeit die Job-Warteschlange `expqueue`, die von den Inhalts-Jobs getrennt
  und für Anwendungs-Jobs gebaut ist ([Warteschlangen mit expqueue](warteschlangen-mit-expqueue.md)).
- Geben Sie Verantwortlichen `content/jobs`, damit sie die Jobs aller Benutzer sehen.

## Verwandte Seiten

- [Hintergrundarbeit: Warteschlangen, Cronjobs und Inhalts-Jobs](muster-fuer-hintergrundarbeit.md)
- [Inhalte im Code anlegen und aktualisieren](inhalte-im-code-anlegen.md)
- [Das Inhaltsmodell und das Bearbeiten: große Operationen](../../../anleitungen/inhaltsmodell-und-bearbeitung.md#8-große-operationen-als-inhaltsjobs)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
