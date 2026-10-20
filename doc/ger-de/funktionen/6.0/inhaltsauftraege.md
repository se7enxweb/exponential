# Inhaltsaufträge: großes Entfernen, Kopieren und Verschieben im Hintergrund

Diese Seite ist für Redakteure und Administratoren, die große Teilbäume entfernen, kopieren oder verschieben. Eine solche
Operation lief früher innerhalb einer Webanfrage und konnte am Zeitlimit, am Speicherlimit oder an einer riesigen
Transaktion scheitern. Ab 6.0.15 bietet die Administrationsoberfläche an, sie als **Inhaltsauftrag** auszuführen: ein
Worker im Hintergrund, der in Teilschritten arbeitet, nach jedem Teilschritt einen Checkpoint setzt und genau dort
weitermacht, wo er aufgehört hat, selbst nach einem `kill -9`. Hinzugefügt am 02.10.2026. Vollständige Anleitung,
einschließlich des Hinzufügens eines eigenen Auftragstyps: [Inhaltsaufträge](../../../eng-us/bc/6.0/content-jobs.md). Seit dem
12.10.2026 installiert dieselbe Engine auch Pakete im Hintergrund (`packageinstall`) und schreibt große Audit-Exporte in
eine Datei (`auditexport`); siehe
[Auftragstypen anderer Teile des Kernels](../../../eng-us/bc/6.0/content-jobs.md#job-types-of-other-parts-of-the-kernel).
Eingereihte Arbeit von Extensions (zum Beispiel Exponential Intelligence) läuft in der getrennten
Auftragswarteschlange [expqueue](extensions/expqueue.md).

## Eine Operation als Auftrag ausführen

1. Starten Sie wie bisher ein Entfernen, Kopieren, Verschieben, Verstecken oder eine Sektionszuweisung. Die
   Bestätigungsseite zeigt jetzt, was die Operation betrifft (Anzahlen, Sprachen, Objekte, die andere Platzierungen
   behalten), und eine Wahl: **jetzt ausführen** oder **im Hintergrund**. Welche Wahl zuerst angeboten wird, folgt der
   Größe: unter 50 Knoten „jetzt“, ab 50 „Auftrag“.
2. Wählen Sie **im Hintergrund**. Der Browser folgt `content/job/<id>`: wer ihn gestartet hat, was er tut, Fortschritt
   mit verbleibender Zeit, Teilschritte, wo er läuft, und Schaltflächen zum Abbrechen, Fortsetzen, Entfernen einer
   teilweisen Kopie oder Herunterladen des Protokolls.
3. Öffnen Sie `content/jobs`, um alle Aufträge zu sehen, mit Filtern nach Status, Operation und Benutzer (Richtlinie
   `content/jobs`).

Am selben Tag kamen zwei weitere Operationen hinzu:

- **Zustände für einen ganzen Teilbaum**: Das Zustandsformular des Reiters Details hat „Auch für alles unter diesem
  Knoten“.
- **Platzierungen für viele Elemente**: Das Menü „Weitere Aktionen“ der Unterelementliste hat „Für die Auswahl eine
  Platzierung hinzufügen“, und viele Platzierungen lassen sich auf einmal entfernen.

## Auf der Kommandozeile

```bash
./console exp:expcontentjob list --allow-root-user              # active and failed jobs; --all lists every job
./console exp:expcontentjob show <id> --allow-root-user
./console exp:expcontentjob run <id> --allow-root-user         # run a job in the foreground
./console exp:expcontentjob resume <id> --allow-root-user
./console exp:expcontentjob cancel <id> --allow-root-user
```

Derselbe Befehl legt Aufträge an: `remove`, `copy`, `move`, `hide`, `reveal`, `section`, `state`, `addlocation`,
`removelocation`, im Vordergrund oder mit `--background`. `php bin/php/expcontentjob.php` ist dasselbe Skript.

Der Cronjob-Teil `contentjobs` (Gruppe `frequent`) startet einen Worker neu, dessen Prozess gestorben ist, startet einen
eingereihten Auftrag, den niemand gestartet hat, und entfernt fertige Aufträge nach `KeepDays`.

## Einstellungen

Alle Schlüssel stehen in `settings/content.ini`, Block `ContentJobSettings`. Geltungsbereich: Installation
(Überschreibung in `settings/override/content.ini.append.php` oder einem Siteaccess).

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `SynchronousLimit` | `50` | Unter dieser Anzahl von Knoten wird zuerst „jetzt“ angeboten; `0` = immer „Auftrag“ |
| `NowLimit` | `1000` | Darüber wird „jetzt“ abgelehnt; `0` = keine Obergrenze |
| `BatchSize` | `50` | Knoten pro Teilschritt (jeweils eine Transaktion) |
| `BatchPause` | `0` | Sekunden zwischen den Teilschritten |
| `QueuedGrace` / `QueuedTimeout` | `60` / `600` | Sekunden, bevor ein eingereihter Auftrag von cron gestartet / aufgegeben wird |
| `MaxAttempts` | `5` | Neustarts des Workers, bevor ein Auftrag fehlschlägt |
| `KeepDays` | `30` | Fertige Aufträge werden danach entfernt |
| `PhpBinary` | leer | PHP-Programm, mit dem Worker gestartet werden |
| `JobTypes[<type>]` | neun eingebaute Klassen | `remove`, `copy`, `move`, `hide`, `reveal`, `section`, `state`, `addlocation`, `removelocation` |

Aufträge liegen unter `var/<site var dir>/jobs/content/`; ein Cluster braucht ein gemeinsames var-Verzeichnis.

## Gut zu wissen

- Ein Teilbaum, an dem ein Auftrag arbeitet, wird für einen anderen Auftrag abgelehnt (Sperren).
- Das Audit erfasst `content.job.start` als übergeordnetes Ereignis von allem, was die Teilschritte getan haben; siehe
  [das Prüfprotokoll](../../../eng-us/features/6.0/audit-trail.md).
- Entfernte Clients starten dieselben Aufträge über die [Inhaltsdomäne von expservices](../../../eng-us/features/6.0/remote-services-expservices.md).
- Am selben Tag behoben: Die Wahl „jetzt ausführen oder im Hintergrund“ einer Auswahlseite (Verschieben und Kopieren)
  wurde nie mit dem Auswahlformular gesendet, sodass immer die automatische Entscheidung gewann. Jetzt wird sie gesendet.

## Verwandte Seiten

- [Der Papierkorb: wer und wo](../../../eng-us/bc/6.0/trash.md)
- [Objektzustände](../../anleitungen/objektzustaende.md#72-ein-ganzer-teilbaum): einen Zustand für einen Teilbaum setzen, jetzt oder als Auftrag
- [Spezifikation des Prüfprotokolls](../../spezifikationen/6.0/audit-ereignismodell.md)
- [Ausführbare Klassen](../../spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md)
- [Checkliste zur Aktualisierung vom 1. und 2. Oktober 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-10.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik Oktober 2026](../../../eng-us/history/2026/2026-10.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
