# Inhaltsaufträge: großes Entfernen, Kopieren und Verschieben im Hintergrund

Diese Seite ist für Redakteure und Administratoren, die große Teilbäume entfernen, kopieren oder verschieben. Eine solche
Operation lief früher innerhalb einer Webanfrage und konnte am Zeitlimit, am Speicherlimit oder an einer riesigen
Transaktion scheitern. Ab 6.0.15 bietet die Administrationsoberfläche an, sie als **Inhaltsauftrag** auszuführen: ein
Worker im Hintergrund, der in Teilschritten arbeitet, nach jedem Teilschritt einen Checkpoint setzt und genau dort
weitermacht, wo er aufgehört hat, selbst nach einem `kill -9`. Hinzugefügt am 02.10.2026. Vollständige Anleitung,
einschließlich des Hinzufügens eines eigenen Auftragstyps: [Inhaltsaufträge](../../bc/6.0/inhaltsjobs.md). Seit dem
12.10.2026 installiert dieselbe Engine auch Pakete im Hintergrund (`packageinstall`) und schreibt große Audit-Exporte in
eine Datei (`auditexport`); siehe
[Auftragstypen anderer Teile des Kernels](../../bc/6.0/inhaltsjobs.md#jobtypen-anderer-teile-des-kernels).
Eingereihte Arbeit von Extensions (zum Beispiel Exponential Intelligence) läuft in der getrennten
Auftragswarteschlange [expqueue](erweiterungen/expqueue.md).

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
`removelocation`, im Vordergrund oder mit `--background`. `section` nimmt `--scope=subtree|inherited|object`, welche
Objekte unterhalb des Knotens die Sektion erhalten ([Sektionen](../../anleitungen/sektionen.md#85-welche-objekte-darunter-die-sektion-erhalten)).
`php bin/php/expcontentjob.php` ist dasselbe Skript.

Der Cronjob-Teil `contentjobs` (Gruppe `frequent`) startet einen Worker neu, dessen Prozess gestorben ist, startet einen
eingereihten Auftrag, den niemand gestartet hat, setzt einen Auftrag fort, der an einer dauerhaft belegten Datenbank
gescheitert ist, und entfernt fertige Aufträge nach `KeepDays`.

`list` zeigt, wie lange der Herzschlag jedes Auftrags zurückliegt, und kennzeichnet einen Auftrag als `stalled`, wenn
er ohne Worker wartet. `list --resume` setzt jeden hängenden oder fehlgeschlagenen Auftrag im Hintergrund fort:

```bash
php bin/php/console exp:expcontentjob list
php bin/php/console exp:expcontentjob list --resume
```

## Eine belegte Datenbank

Ein Teilschritt, der auf eine belegte Datenbank trifft, geht nicht verloren. Unter SQLite geschieht das, wenn ein
anderer Prozess die Schreibsperre länger hält als `site.ini [DatabaseSettings] SQLiteTransactionWait`; unter MySQL ist
es eine Zeitüberschreitung beim Warten auf eine Sperre oder ein Deadlock. Der Worker

1. rollt den Teilschritt zurück, sodass nichts davon in der Datenbank bleibt;
2. liest den Auftrag erneut ab seinem letzten Checkpoint und lässt den Auftragstyp seinen Zustand neu aufbauen, wie
   beim Fortsetzen;
3. wartet 1, 2, 4 ... Sekunden (höchstens `BusyBackoffMax`) und schreibt währenddessen den Herzschlag;
4. versucht den Teilschritt erneut, bis zu `BusyRetries` Mal.

Ist die Datenbank danach immer noch belegt, schlägt der Auftrag mit einem vorübergehenden Fehler fehl. Der Cronjob-Teil
setzt ihn von selbst fort, höchstens `MaxAttempts` Mal hintereinander; jeder fertige Teilschritt beginnt diese Zählung
neu. Jedes Element wird genau einmal bearbeitet, wie oft ein Teilschritt auch versucht wird. Vor dieser Änderung konnte
der Worker eines Handbuch-Aufbaus der Hilfe an einer belegten SQLite-Datenbank enden und den Auftrag `running` ohne
Worker zurücklassen; er musste von Hand fortgesetzt werden.

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
| `MaxAttempts` | `5` | Neustarts des Workers, bevor ein Auftrag fehlschlägt; auch die automatischen Fortsetzungen in Folge eines Auftrags, der an einer belegten Datenbank scheiterte |
| `BusyRetries` | `8` | Erneute Versuche eines Teilschritts, der auf eine belegte oder gesperrte Datenbank traf; `0` = sofort fehlschlagen |
| `BusyBackoffMax` | `60` | Längste Pause zwischen diesen Versuchen, in Sekunden (die Pause verdoppelt sich ab 1) |
| `KeepDays` | `30` | Fertige Aufträge werden danach entfernt |
| `PhpBinary` | leer | PHP-Programm, mit dem Worker gestartet werden |
| `JobTypes[<type>]` | neun eingebaute Klassen | `remove`, `copy`, `move`, `hide`, `reveal`, `section`, `state`, `addlocation`, `removelocation` |

Aufträge liegen unter `var/<site var dir>/jobs/content/`; ein Cluster braucht ein gemeinsames var-Verzeichnis.

## Gut zu wissen

- Ein Teilbaum, an dem ein Auftrag arbeitet, wird für einen anderen Auftrag abgelehnt (Sperren).
- Das Audit erfasst `content.job.start` als übergeordnetes Ereignis von allem, was die Teilschritte getan haben; siehe
  [das Prüfprotokoll](pruefprotokoll.md).
- Entfernte Clients starten dieselben Aufträge über die [Inhaltsdomäne von expservices](entfernte-dienste-expservices.md).
- Am selben Tag behoben: Die Wahl „jetzt ausführen oder im Hintergrund“ einer Auswahlseite (Verschieben und Kopieren)
  wurde nie mit dem Auswahlformular gesendet, sodass immer die automatische Entscheidung gewann. Jetzt wird sie gesendet.

## Verwandte Seiten

- [Der Papierkorb: wer und wo](../../bc/6.0/papierkorb.md)
- [Objektzustände](../../anleitungen/objektzustaende.md#72-ein-ganzer-teilbaum): einen Zustand für einen Teilbaum setzen, jetzt oder als Auftrag
- [Spezifikation des Prüfprotokolls](../../spezifikationen/6.0/audit-ereignismodell.md)
- [Ausführbare Klassen](../../spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md)
- [Checkliste zur Aktualisierung vom 1. und 2. Oktober 2026](../../bc/6.0/verhaltensaenderungen-2026-10.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik Oktober 2026](../../geschichte/2026/2026-10.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
