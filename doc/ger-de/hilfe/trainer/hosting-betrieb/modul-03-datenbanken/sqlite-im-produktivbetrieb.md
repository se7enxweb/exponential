---
title: "Lektion 3.2: SQLite im Produktivbetrieb"
audience: trainer, hosting, admin
category: training
order: 32
summary: Die Teilnehmenden erklären den WAL-Modus und die Schreibwarteschlange, durch die SQLite zu einer redaktionellen Website passt, überwachen eine SQLite-Datenbank mit den Prüfungen aus Kapitel 9 und wissen, wann ein anderes Datenbanksystem die bessere Wahl ist. 45 Minuten.
keywords: hosting und betrieb, SQLite, WAL, schreibwarteschlange, BEGIN IMMEDIATE, writer-lock, SQLiteTransactionWait, integrity_check, ANALYZE, VACUUM, wal_checkpoint, database is busy, hosting and operations, write queue
type: help
---
# Lektion 3.2: SQLite im Produktivbetrieb

SQLite ist die Standarddatenbank von Exponential und eine Produktivdatenbank für Websites, die auf einem Rechner laufen.
Hosting-Mitarbeitende müssen wissen, wie es sich unter Last verhält, welche Dateien dazugehören, wie man es prüft und
welche ehrlichen Grenzen eine Website zu einem Datenbankserver führen. Genau das vermittelt diese Lektion, auf Grundlage
von Kapitel 9.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 3: Datenbanken](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Systemadministratoren und Support-Mitarbeitende im Hosting |
| Voraussetzungen | [Lektion 3.1](eine-datenbank-auswaehlen.md); eine Schulungsinstallation auf SQLite und das Kommandozeilenprogramm `sqlite3` |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, wie WAL Leser und einen Schreiber nebeneinander arbeiten lässt, und die zwei zusätzlichen Dateien nennen;
- die Schreibwarteschlange (die Datei `.writer-lock` und `BEGIN IMMEDIATE`) und die Einstellung beschreiben, die das
  Warten begrenzt;
- die Überwachungsprüfungen aus Kapitel 9 ausführen: Sperr-Zeitüberschreitungen, Integrität, Journalmodus, WAL-Größe,
  freier Speicherplatz, Eigentümer;
- sagen, wann `ANALYZE` und `VACUUM` laufen sollten und warum `VACUUM` in den Wartungsmodus gehört;
- die Situationen nennen, in denen ein anderes Datenbanksystem die bessere Wahl ist.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Kapitel 9, Abschnitt 9.2](../../../../installation/09-datenbanken.md#92-sqlite-3-eine-produktionsdatenbank-in-einer-datei)
und [SQLite für Hoster](../../../hosting/grundlagen/sqlite-fuer-hoster.md).

1. **Warum es passt** (3 Minuten). Die meisten Anfragen einer laufenden Website berühren die Datenbank nicht: Die
   Cache-Schichten beantworten sie vorher. Der Treiber ergänzt WAL, Verbindungseinstellungen für einen Webserver und
   eine Schreibwarteschlange.
2. **WAL** (4 Minuten). Schreibvorgänge gehen nach `<database>-wal`; Leser sehen einen konsistenten Schnappschuss.
   Leser blockieren den Schreiber nie und der Schreiber blockiert nie Leser, aber es gibt jeweils nur einen Schreiber.
   `-wal` und `-shm` gehören zur Datenbank; löschen Sie sie nie, solange ein Prozess sie geöffnet hat. Alle Prozesse
   müssen auf einem Rechner laufen.
3. **Die Schreibwarteschlange** (5 Minuten). Jede Transaktion nimmt zuerst eine exklusive Sperre auf
   `<database file>.writer-lock` und führt dann `BEGIN IMMEDIATE` aus. Schreiber warten, bis sie an der Reihe sind,
   statt fehlzuschlagen. Das Warten wird durch `[DatabaseSettings] SQLiteTransactionWait` (25 s) begrenzt, das unter
   der Anfrage-Zeitüberschreitung des Webservers bleiben muss (`requestTimeout` von Velocity ist 30 s). Eine
   Zeitüberschreitung lautet „database is busy: ... nothing was written“.
4. **Wartung** (4 Minuten). `ANALYZE` nach einer Installation oder einem großen Import; `PRAGMA optimize` wöchentlich.
   `VACUUM` nur, wenn die Datei viel größer ist als ihr Inhalt, im Wartungsmodus, mit doppelt so viel freiem
   Speicherplatz wie die Datenbank groß ist. Eine große `-wal`-Datei schrumpft mit `journal_size_limit` oder einem
   `TRUNCATE`-Checkpoint.
5. **Grenzen** (4 Minuten). Wählen Sie eine Server-Datenbank, wenn mehr als ein Rechner schreiben muss, wenn viele
   Redakteure den ganzen Tag große Änderungen veröffentlichen, wenn Sie Replikation oder Point-in-Time-Recovery
   brauchen, oder Datenbankbenutzer und Berechtigungen. Die Größe ist selten der Grund.

### Häufige Fragen

**Kann ich die Datenbank für zwei Webserver auf NFS legen?** Nein. WAL braucht gemeinsamen Speicher auf einem Rechner.

**Ab und zu erscheint „database is busy“. Was tue ich?** Sehen Sie in `var/log/error.log` und im Cron-Log nach, was
die Sperre gehalten hat: ein `VACUUM`, ein großer Import, ein Skript, das eine Transaktion offen hält. Erhöhen Sie die
Wartezeit nur zusammen mit der Anfrage-Zeitüberschreitung.

**Wem müssen die Dateien gehören?** Alle Prozesse (Web, Velocity-Worker, Cron) brauchen Schreibzugriff; geben Sie
ihnen eine gemeinsame Gruppe und für die Gruppe beschreibbare Dateien.

**Kann ich die Datenbankdatei für eine Sicherung kopieren?** Nie, während sie verwendet wird. Lektion 3.4 zeigt die
Online-Sicherung.

### Stolperfallen

- **`-wal` oder `-shm` löschen, um „aufzuräumen“.** Dabei gehen bestätigte Daten verloren. Unterbinden Sie es im Kurs
  sofort, wenn Sie es sehen.
- **`VACUUM` auf einer laufenden Website ausführen.** Es hält die Schreibsperre für seine ganze Laufzeit;
  Veröffentlichungen stauen sich und können in eine Zeitüberschreitung laufen.
- **Datenbankdateien, die root gehören**, nachdem eine Person `sqlite3` als root ausgeführt hat. Prüfen Sie am Ende
  `ls -l var/storage/sqlite3/`.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation auf SQLite. Führen Sie die Befehle im Wurzelverzeichnis der
Installation aus; ersetzen Sie `exponential.db` durch den Dateinamen, den `ls` zeigt.

1. Listen Sie das Datenbankverzeichnis auf: `ls -l var/storage/sqlite3/`. Bestimmen Sie die Datenbank, ihre Dateien
   `-wal` und `-shm` und, falls vorhanden, die Datei `.writer-lock`.
2. Prüfen Sie den Journalmodus:

   ```bash
   sqlite3 var/storage/sqlite3/exponential.db "PRAGMA journal_mode"
   ```

3. Führen Sie die schnelle Integritätsprüfung aus: `sqlite3 var/storage/sqlite3/exponential.db "PRAGMA quick_check"`.
4. Zählen Sie die Sperr-Zeitüberschreitungen: `grep -c "database is busy" var/log/error.log` (keine Datei oder `0` ist
   in Ordnung).
5. Prüfen Sie den freien Speicherplatz: `df -h var/storage/sqlite3`.
6. Lesen Sie die Einstellung für die Wartezeit:

   ```bash
   php bin/php/console exp:ini get site.ini/DatabaseSettings/SQLiteTransactionWait
   ```

7. Aktualisieren Sie die Statistiken des Abfrageplaners: `sqlite3 var/storage/sqlite3/exponential.db "ANALYZE"`.
8. Veröffentlichen Sie in der Administration eine kleine Änderung (bearbeiten Sie zum Beispiel den Namen eines Ordners
   in Ihrem Kursbereich), um einen Schreibvorgang auszulösen, wiederholen Sie dann Schritt 1 und vergleichen Sie die
   Größe der `-wal`-Datei.
9. Führen Sie einen abschneidenden Checkpoint aus und lesen Sie seine drei Zahlen:
   `sqlite3 var/storage/sqlite3/exponential.db "PRAGMA wal_checkpoint(TRUNCATE)"`.
10. Prüfen Sie, dass jede Datei in `var/storage/sqlite3/` noch denselben Eigentümer und dieselbe Gruppe hat wie zuvor.

## Erwartetes Ergebnis

- Schritt 2 gibt `wal` aus; Schritt 3 gibt `ok` aus.
- Schritt 4 gibt `0` aus oder meldet, dass es keine Datei gibt.
- Schritt 6 gibt `25` aus, sofern die Installation den Wert nicht überschreibt.
- Schritt 9 gibt drei Zahlen aus, die erste ist `0`, wenn der Checkpoint nicht blockiert war; die `-wal`-Datei ist
  danach klein.
- Schritt 10: Eigentümer unverändert (führen Sie jeden Befehl als Benutzer der Website aus, nicht als root).
- Der Trainer prüft das, indem er jede Person nach dem Ergebnis von `quick_check` und der Größe der `-wal`-Datei vor und
  nach Schritt 9 fragt.

## Quiz

1. Welche zwei Dateien liegen neben einer SQLite-Datenbank im WAL-Modus und dürfen nie gelöscht werden, solange sie
   geöffnet ist?
2. Was tut der Treiber vor `BEGIN IMMEDIATE`?
3. Warum muss `SQLiteTransactionWait` unter der Anfrage-Zeitüberschreitung des Webservers bleiben?
4. Wann sollte `VACUUM` laufen, und wie?
5. Nennen Sie zwei Situationen, in denen ein Datenbankserver die bessere Wahl ist als SQLite.

<details>
<summary>Antworten</summary>

1. `<database>-wal` und `<database>-shm`.
2. Er nimmt ein exklusives `flock()` auf `<database file>.writer-lock`, sodass sich Schreiber der Reihe nach anstellen
   ([Schreibwarteschlange](../../../../installation/09-datenbanken.md#schreibvorgänge-in-der-warteschlange-begin-immediate-und-die-schreibsperre)).
3. Damit eine wartende Anfrage mit einer klaren Meldung fehlschlägt, statt vorher vom Server beendet zu werden.
4. Nur wenn die Datei viel größer ist als ihr Inhalt, im Wartungsmodus, mit etwa doppelt so viel freiem Speicherplatz
   wie die Datenbank groß ist.
5. Zwei beliebige von: Mehr als ein Rechner muss schreiben; viele Redakteure veröffentlichen den ganzen Tag große
   Änderungen; Replikation oder Point-in-Time-Recovery wird gebraucht; Datenbankbenutzer und Berechtigungen werden
   gebraucht.

</details>

## Weiterführende Informationen

- [Kapitel 9, SQLite 3](../../../../installation/09-datenbanken.md#92-sqlite-3-eine-produktionsdatenbank-in-einer-datei): die
  vollständige Erklärung.
- [SQLite für Hoster](../../../hosting/grundlagen/sqlite-fuer-hoster.md): die Zusammenfassung für Hoster.
- [Unterstützung der SQLite-Datenbank](../../../../../eng-us/features/6.0/sqlite-database.md): die Funktionsseite.
- [Eine SQLite-Website überwachen](../../../../installation/09-datenbanken.md#überwachung-einer-sqlite-site): die Tabelle der
  Prüfungen.

## Verwandte Seiten

- [Modul 3: Datenbanken](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
