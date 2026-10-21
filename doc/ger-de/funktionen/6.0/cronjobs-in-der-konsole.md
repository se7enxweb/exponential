# Cronjob-Konsole

Diese Seite richtet sich an Administratoren, die Cronjobs ohne Shell sehen, ausführen und untersuchen möchten.
**Setup > Cronjobs** führt einen Cronjob-Teil oder eines seiner Skripte aus und zeigt dessen Ausgabe, während er läuft.
Niemand muss eine Shell öffnen, um herauszufinden, warum ein Job langsam ist oder ob ein neuer funktioniert.

Das vollständige Benutzerhandbuch mit jedem Teil der Seite, der Planung über die Crontab, den Shell-Befehlen, den
Protokollen und der Fehlersuche ist [Cronjobs: ausführen, planen und verfolgen](../../anleitungen/cronjobs.md).

## Einen Job im Browser ausführen

1. Öffnen Sie `/setup/cronjobs` oder **Setup > Cronjobs** (`Links[cronjobs]` in `settings/menu.ini`). Sie benötigen
   die Richtlinie `setup/managecronjobs` (nach demselben Muster wie `setup/managecache`).
2. Wählen Sie die Website unter **Für Website ausführen**.
3. Klicken Sie auf der Karte eines Teils auf **Teil starten**, oder öffnen Sie dessen Zeile **Skripte** und klicken Sie
   neben einem Skript auf **Starten**.
4. Beobachten Sie die **Ausgabe**: die Ausgabe des aktuellen oder letzten Laufs, laufend fortgeschrieben, während sie
   wächst. **Laufenden Job anhalten** beendet ihn.

Um einen Teil stattdessen in einer Shell auszuführen, klicken Sie neben seinem **Shell-Befehl** auf **Kopieren** und
fügen ihn in ein Terminal auf dem Server ein. Der Befehl nennt die unter **Für Website ausführen** gewählte Website.

## Was die Seite zeigt

Von oben nach unten:

- **Statusleiste**: *Untätig* oder *Läuft: Teil* mit der Website, der Prozess-ID und der bisherigen Laufzeit. Ihre
  Schaltflächen sind **Laufenden Job anhalten**, **Protokolle leeren** (leert die Protokolldateien) und **Alle Teile
  anhalten** (oder **Alle Teile fortsetzen**); solange alle Teile angehalten sind, sagt ein Hinweis über der Leiste,
  seit wann und von wem. Darunter: die Protokolldatei, aus der die Ausgabe stammt, und die PHP-Binärdatei, mit der Jobs
  laufen.
- **Übersicht**: wie viele Cronjob-Teile und Skripte es gibt, wie viele Teile die Crontab ausführt, wie viele nicht,
  wie viele **Aufmerksamkeit brauchen** (gesperrt, ein Skript fehlt oder Probleme im letzten Lauf), und der letzte von
  dieser Seite gestartete Lauf mit seinem Ergebnis.
- **Starten und filtern** (eingeklappt; seine Zusammenfassung nennt die Website, die die Start-Schaltflächen verwenden): **Für Website ausführen** (der Siteaccess, den jede Start-Schaltfläche der Seite verwendet), **Teil oder Skript suchen**
  (findet Teilnamen, Skriptnamen und das, was die Skripte über sich sagen), **Anzeigen** (*Alle*, *Geplant*, *Nicht geplant*,
  *Brauchen Aufmerksamkeit*) und **Cronjob-Teil** mit **Starten**: Mit *Alle Teile* führt es jeden ausführbaren Teil
  nacheinander aus, auf jeder Seite der Liste; mit einem gewählten Teil führt es diesen Teil aus und zeigt nur seine Karte.
- **Ausgabe** (einklappbar): die Ausgabe des aktuellen oder letzten Laufs. Sie öffnet sich von selbst, wenn ein Job startet.
- **Cronjob-Teile**, in zwei Ansichten, die neben der Überschrift mit zwei Umschaltflächen gewählt und pro Benutzer
  gespeichert werden (die Präferenz `admin_cronjobs_view`, geschrieben durch einen POST an `user/preferences/set` mit
  dem Formular-Token; die aktuelle Ansicht ist die gedrückte Schaltfläche):
  - **Kompakt** (Standard): eine Tabellenzeile pro Teil, alle Teile auf einer Seite - der Teil (mit dem Schlüssel als
    Titel) mit *n Skripte* darunter, das die Skripte einzeln pro Zeile in Spalten öffnet - *Kern* oder die Extension,
    aus der es stammt, sein Pfad mit dem Dateinamen, was es tut, und **Doku** - in einer Zeile unter dem Teil, quer über
    die Tabelle, sodass mehrere Teile gleichzeitig geöffnet sein können (ohne JavaScript untereinander in der Zelle; ein
    fehlendes Skript zeigt *fehlt* statt seines Pfads); die Kennzeichen
    (*Geplant* oder *Nicht geplant*, *Gesperrt*, *n fehlen*, *Angehalten*); der Zeitplan in Worten (der Cron-Ausdruck
    oder der vorgeschlagene als Titel) mit dem nächsten Lauf darunter; der letzte Lauf von hier mit seinem Ergebnis; und
    **Anhalten** oder **Fortsetzen** sowie **Starten**. Die Spalten brechen um, sodass die Tabelle neben die Menüs
    passt. Eine Zeile ist wie eine Karte am linken Rand markiert. Die Suche und **Anzeigen** filtern die Zeilen wie die
    Karten.
  - **Ausführlich**: eine Karte pro Teil, wie unten beschrieben, seitenweise.

  In der ausführlichen Ansicht gibt es eine Karte pro Teil, in der Reihenfolge von `cronjob.ini`. Jede Karte zeigt
  - den Namen des Teils, seinen Schlüssel, *Geplant* oder *Nicht geplant* (aus der Crontab gelesen) und *Aktiviert*,
    *Gesperrt* (`ForbiddenParts[]`) oder *n fehlen* (nicht gefundene Skripte); *Angehalten* mit **Angehalten**: seit
    wann und von wem, und **Anhalten** oder **Fortsetzen** neben **Teil starten**
    ([Cronjob-Teile anhalten](cronjobs-pausieren.md));
  - **Zeitplan**: den Zeitplan der Crontab-Zeile, die ihn ausführt, in Worten (*Alle 5 Minuten*, *Täglich um
    03:30*) und als Cron-Ausdruck; für einen Teil, den nichts ausführt, stattdessen den **Vorgeschlagenen Zeitplan**;
  - **Nächste Ausführung**: wann diese Crontab-Zeile ihn das nächste Mal ausführt, in der Zeit dieses Servers; *Nur bei
    manuellem Start* für einen Teil, den die Crontab nicht ausführt;
  - **Zuletzt von hier gestartet**: wann er zuletzt von dieser Seite gestartet wurde, wie lange er dauerte und *OK*
    oder *n Probleme*;
  - **Shell-Befehl**: den genauen Befehl, der ihn jetzt ausführt, mit **Kopieren**;
  - **Skripte (n)**: die Skriptnamen in einer Zeile; aufgeklappt jedes Skript mit dem, was es tut (die `@description`
    seines Kopfes) und **Doku** (siehe unten), dem Verzeichnis, in dem es gefunden wurde, **Befehl kopieren** (führt
    dieses Skript allein aus) und **Starten**.

  Eine Karte mit einem Problem hat einen roten Rand, die Karte des laufenden Teils einen grünen. Die Liste ist
  seitenweise aufgeteilt (siehe Einstellungen).
- **Verfügbar, aber nicht aktiviert** (einklappbar): Skripte in einem Cronjob-Verzeichnis, die kein Teil nennt, sodass
  sie nie laufen.
- **Pausen** (einklappbar): wer welchen Teil wann angehalten und fortgesetzt hat.
- **Letzte Ausführungen** (einklappbar): Cronjob, Website, Startzeit, Dauer und Ergebnis. *n Probleme* zählt die Zeilen
  des Protokolls des Laufs, die einen Fehler, ein Scheitern oder eine Warnung erwähnen.
- **Crontab** (einklappbar): was jetzt in der Crontab installiert ist, und die vorgeschlagenen Einträge für die Teile,
  die nichts ausführt, mit **Vorgeschlagene Einträge kopieren**.

Die Seite funktioniert ohne JavaScript: Jede Schaltfläche zum Starten, Anhalten und Leeren sendet das Formular ab, und
die Seite kommt mit dem Ergebnis zurück. Mit JavaScript starten Läufe an Ort und Stelle, und die Ausgabe wird live
verfolgt; die Suche, der Filter **Anzeigen**, **Starten** für alle Teile und die Kopier-Schaltflächen benötigen es.
Jedes Bedienelement hat eine Beschriftung, die einklappbaren Abschnitte sind `<details>`, und Meldungen werden
Screenreadern angesagt.

Das Starten endet mit einer Weiterleitung, sodass ein Neuladen der Seite nur die Ausgabe erneut liest. (Eine frühe
Version startete einen Job bei jedem Neuladen erneut, weil die Seite die Antwort auf das Absenden des Formulars war,
das ihn gestartet hatte.)

## Ein Skript in der Shell ausführen

Die Konsole startet `runcronjobs.php`, das die Option `--script` erhalten hat:

```bash
php runcronjobs.php --siteaccess=user --script=notification.php --allow-root-user
php runcronjobs.php --siteaccess user --script notification.php --allow-root-user
php runcronjobs.php --help --allow-root-user     # lists --script and --list
```

Nur der Dateiname wird verwendet, sodass ein Pfad kein Skript außerhalb der in `cronjob.ini` genannten Verzeichnisse
erreichen kann. Das Skript nimmt denselben Mutex und schreibt dieselbe Ausgabe wie bei einem Lauf als Teil seines
Cronjob-Teils, sodass die beiden nicht kollidieren können. Sowohl `--option value` als auch `--option=value`
funktionieren jetzt für jede Option, die einen Wert erwartet.

## Ein Skript aktivieren

Nennen Sie es in `[CronjobSettings] Scripts[]` oder in einem Block `[CronjobPart-<name>]` von `cronjob.ini`; siehe
[Befehle, Cronjob-Teile und Modulansichten als Klassen](../../bc/6.0/cli-cronjob-und-view-abstraktionen.md).

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich | Bedeutung |
|---|---|---|---|---|---|
| `settings/cronjob.ini` | `AdminSettings` | `ForbiddenParts[]` | `cluster_maintenance`, `unlock` | global | Teile, die nicht aus einem Browser gestartet werden können. Beide sind destruktiv: Der eine leert den Cluster-Speicher, der andere gibt Sperren frei, die andere Prozesse halten. |
| `settings/cronjob.ini` | `AdminSettings` | `PhpCliPath` | leer | global | Vollständiger Pfad der PHP-Kommandozeilen-Binärdatei, mit der `runcronjobs.php` ausgeführt wird. Leer probiert die üblichen Orte. Es muss eine CLI-Binärdatei sein; diejenige, die die Webanfrage beantwortet, kann kein Skript ausführen. |
| `settings/cronjob.ini` | `AdminSettings` | `LogFile` | `cronjobs/output.log` | Siteaccess | Wohin die Ausgabe eines gestarteten Jobs geht, relativ zum var-Verzeichnis des Siteaccess |
| `settings/cronjob.ini` | `AdminSettings` | `ErrorFile` | `cronjobs/error.log` | Siteaccess | Wohin seine Fehler gehen |
| `settings/cronjob.ini` | `AdminSettings` | `CrontabSchedule_<part>` | `*/5 * * * *` (frequent), `*/15 * * * *` (global), `17 * * * *` (infrequent), `0 * * * *` (andere) | global | Der Zeitplan, den die Seite für einen Teil vorschlägt, den die Crontab nicht ausführt |
| `settings/cronjob.ini` | `AdminSettings` | `HistoryLength` | `100` | global | Wie viele Läufe der Verlauf behält |
| `settings/cronjob.ini` | `PauseSettings` | `Pause`, `Directory` | `enabled`, leer | global | Anhalten von Teilen, siehe [Cronjob-Teile anhalten](cronjobs-pausieren.md) |
| `settings/cronjob.ini` | `ScriptDocumentation` | `Link[<script>]` | leer | global | Der Link **Doku** eines Skripts, nach seinem Dateinamen: eine URL, ein Pfad unterhalb von `DocumentationBase` oder `none` für keinen Link. Hat Vorrang vor dem Tag `@link` im Kopf des Skripts ([Leitfaden](../../anleitungen/cronjobs.md), Abschnitt 11) |
| `settings/cronjob.ini` | `ScriptDocumentation` | `DocumentationBase` | leer: `https://github.com/se7enxweb/exponential/blob/main/` | global | Wo ein Pfad in `Link[]` oder `@link` nachgeschlagen wird. Nur Links mit `http` und `https` werden angezeigt |
| `settings/admininterface.ini` | `PaginationSettings` | `ItemsPerPage[setup/cronjobs]` | siehe [Seitengrößen](../../bc/6.0/einstellungen-fuer-seitengroessen.md) | global | Wie viele Karten von Teilen eine Seite zeigt |

## Verwandte Seiten

- Das Benutzerhandbuch: [Cronjobs: ausführen, planen und verfolgen](../../anleitungen/cronjobs.md)
- [Cronjob-Teile anhalten](cronjobs-pausieren.md),
  [einen Teil aus einer Extension auslassen (der Filter)](filter-fuer-cronjob-teile.md)
- Der Workflow-Cronjob (`workflow.php`) und die Prozesse, die er fortsetzt: [Workflows: Freigaben, Auslöser und die Prozesse, die warten](../../anleitungen/workflows.md)
- [Ausführbare Befehle, Cronjobs und Ansichten (Spezifikation)](../../spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md), [Befehle, Cronjob-Teile und Modulansichten als Klassen](../../bc/6.0/cli-cronjob-und-view-abstraktionen.md)
- [Inhaltsjobs](inhaltsauftraege.md), [Velocity-Scheduler](velocity-zeitplaner.md), [Benachrichtigungen (der Cronjob-Teil `notification`)](benachrichtigungen.md)
- [Cronjob-Teil zur Bereinigung des RSS-Imports](../../bc/6.0/cleanuprss.md), [Konsolenbefehle](../../bc/6.0/konsole.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Chronik: [September 2026, erste Hälfte: Cronjob-Konsole](../../geschichte/2026/2026-09a.md#13-september-caches-die-sie-sehen-cronjobs-die-sie-ausführen-können), [die Neugestaltung der Seite am 14. September](../../geschichte/2026/2026-09a.md#14-september-pdf-rss-und-die-rad-werkzeuge), [Juni 2026, erste Hälfte](../../geschichte/2026/2026-06a.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
