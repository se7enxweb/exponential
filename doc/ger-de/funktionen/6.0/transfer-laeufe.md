# Transfer-Läufe: ein Ablauf und eine Lauf-Historie für alle Import- und Export-Werkzeuge (6.0.15)

Diese Seite ist für alle, die ein Import- oder Export-Werkzeug schreiben oder pflegen, oder eine Seite, die dessen
Läufe auflistet. Die Werkzeuge selbst sind im [Transfer-Register](transfer-register.md) angemeldet; Anwender finden
ihre Läufe in der Lauf-Liste und auf der Statistik-Seite von Imports.

## Kurz gesagt

| | |
|---|---|
| Was es ist | Ein Weg für jedes Werkzeug des Transfer-Registers, seine Arbeit im Hintergrund auszuführen, auf dem Ablauf, den die Installation hat, und eine Tabelle, die jeden Lauf jedes Werkzeugs festhält. |
| Abläufe | **expqueue** (die Job-Warteschlange), wenn sie aktiv ist und ein Worker läuft; sonst die **Inhalts-Jobs** des Kernels, die jede Installation hat; sonst der eigene **Prozess** des Werkzeugs wie vor 6.0.15. Der Code des Werkzeugs ist für alle drei derselbe. |
| Historie | Die Tabelle `exp_transfer_run`: eine Zeile je Lauf mit Werkzeug, Richtung, Art, Umfang, Benutzer, Zustand, Zählern, Dauer, Fehlern, Dateiverweis, dem Ablauf und seinem Job, und dem Link auf die eigene Seite des Werkzeugs für diesen Lauf. |
| Wo Läufe erscheinen | Die Lauf-Liste und die Statistik-Seite von Imports (`sqliimport/list`, `sqliimport/statistics`) haben einen Werkzeug-Filter: Imports, Export, CIE oder alle Import- und Export-Werkzeuge. Die eigenen Seiten der Werkzeuge (Jobs und Verlauf von Export, die Export-Seite von CIE) behalten ihre Listen und verlinken dorthin. Kein neuer Reiter. |
| Klassen | `expKernelTransferRunner`, `expKernelTransferTask`, `expKernelTransferContext`, `expKernelTransferHistory`, `expKernelTransferRun`, `expKernelTransferRunSchema`, `expKernelTransferQueueHandler`, `expContentJobTransfer` |
| Einstellungen | `transfer.ini [TransferSettings]`; je Werkzeug `Engine=`, `QueueJobType=`, `HistoryAllPermission=` in seinem Register-Block |

## Welcher Ablauf einen Lauf ausführt

`expKernelTransferRunner::engine( $toolID )` entscheidet für jeden Lauf:

1. Der gewünschte Ablauf: `transfer.ini [TransferSettings] ToolEngine[<Werkzeug-ID>]`, sonst `Engine=` im
   Register-Block des Werkzeugs, sonst `[TransferSettings] Engine`. `auto` (Vorgabe) heißt „der beste verfügbare“.
2. **expqueue** ist verfügbar, wenn die Extension aktiv ist, ihre Tabellen angelegt sind, das Werkzeug seinen
   Job-Typ nennt (`QueueJobType=`) und ihn in `expqueue.ini` mit `Handler=expKernelTransferQueueHandler` anmeldet, und
   ein Warteschlangen-Worker läuft. Ohne Worker würde ein Lauf warten, bis einer startet; er geht daher an den
   nächsten Ablauf (`QueueNeedsWorker=disabled` stellt ihn trotzdem in die Warteschlange).
3. **contentjob** ist verfügbar, wenn `content.ini [ContentJobSettings] JobTypes[transfer]=expContentJobTransfer`
   gesetzt ist (Vorgabe). Der Inhalts-Job startet für den Lauf einen eigenen Worker-Prozess; der Cronjob-Teil
   `contentjobs` startet ihn neu, wenn dieser Worker gestorben ist.
4. **process**: `submit()` liefert `engine => process` und startet nichts; das Werkzeug startet seine Arbeit wie
   bisher.

Ein genannter Ablauf, der nicht verfügbar ist, fällt in derselben Reihenfolge zurück; eine Einstellung hält ein
Werkzeug also nie an.

## In einem Werkzeug verwenden

### Die Aufgabe

Die Arbeit eines Laufs ist eine Klasse, die `expKernelTransferTask` implementiert:

```php
class bccieTransferTask implements expKernelTransferTask
{
    public function run( array $params, expKernelTransferContext $context )
    {
        $job = bccieJob::load( $params['job'] );
        foreach ( ... as $i => $row )
        {
            ...
            $context->progress( $i + 1, $total );
            $context->throwIfCancelled();
        }
        return array( 'rows_done' => $total, 'bytes' => filesize( $file ), 'file_ref' => basename( $file ), 'format' => 'csv' );
    }
}
```

- `run()` wird einmal aufgerufen, in einem Worker-Prozess, als der Benutzer, der den Lauf angefordert hat, außerhalb
  jeder Datenbank-Transaktion (der Inhalts-Job führt sie deshalb in seinem Schritt `prepare()` aus, vor der
  Transaktion seines Batches).
- Die Parameter sind das, was das Werkzeug an `submit()` übergeben hat: einfache JSON-Werte, nie ein Geheimnis (die
  Admin-Seiten der Warteschlange zeigen sie). Übergeben Sie die eigene Job-ID des Werkzeugs und halten Sie alles
  andere in dessen Job-Datei oder Tabelle.
- Sie liefert das Ergebnis des Laufs, das in die Historie geht: jede Spalte (`rows_done`, `rows_failed`,
  `rows_total`, `bytes`, `format`, `file_ref`, `title`, `detail_url`, `error`), `errors` (eine Liste von Meldungen,
  höchstens 20 werden behalten), `detail` (ein Array) und `state` (`failed` für einen Lauf, der ohne Ausnahme endete,
  aber seine Arbeit nicht getan hat).
- Sie wirft `expKernelTransferCancelledException`, wenn sie wegen eines Abbruchs aufgehört hat, und jede andere
  Ausnahme, wenn sie fehlgeschlagen ist. Ein fehlgeschlagener Lauf wird nicht von selbst wiederholt: die Historie
  sagt warum, und die Seite des Werkzeugs kann ihn neu starten.
- Nach einem beendeten Worker kann derselbe Lauf neu starten: die Aufgabe setzt fort oder beginnt sauber von vorn.

### Einen Lauf starten

```php
if ( class_exists( 'expKernelTransferRunner' ) )
{
    $run = expKernelTransferRunner::submit( 'transfer.bccie', 'bccieTransferTask', array( 'job' => $jobID ), array(
        'title' => 'Formular 42 als CSV',
        'history' => array( 'direction' => 'export', 'kind' => 'export', 'scope' => 'data', 'scope_ref' => '42',
                            'format' => 'csv', 'tool_ref' => $jobID, 'detail_url' => 'bccie/export/42/(job)/' . $jobID ) ) );
    // $run: engine (expqueue, contentjob, process), job_ref, run_id, error
    if ( $run['engine'] === 'process' )
        ...wie bisher starten...
}
```

`submit()` schreibt die Zeile der Historie (Zustand `queued`), übergibt den Lauf an den Ablauf und speichert Ablauf
und Job in der Zeile. Halten Sie `engine`, `job_ref` und `run_id` im eigenen Job-Datensatz des Werkzeugs fest: seine
Seiten brauchen sie zum Abbrechen und um die Zeile zu finden. Die Prüfung mit `class_exists()` hält das Werkzeug auf
einem älteren Kernel lauffähig.

### Den Job-Typ für die Warteschlange anmelden

In `settings/expqueue.ini.append.php` des Werkzeugs, damit das Dashboard der Warteschlange seine Läufe unter eigenem
Namen zeigt:

```ini
[JobType_bccie.export]
Handler=expKernelTransferQueueHandler
Class=bulk
MaxAttempts=1
Timeout=3600
Description=An export of CIE (form submissions)
```

und im Register-Block in `kernelservices.ini.append.php`:

```ini
QueueJobType=bccie.export
HistoryAllPermission=bccie/remove
```

`expKernelTransferQueueHandler` gibt es nur, wo expqueue aktiv ist; ohne die Extension bleibt die Anmeldung wirkungslos
und die Inhalts-Jobs übernehmen die Läufe. Nehmen Sie für Exporte und Importe die Prioritätsklasse `bulk`, damit ein
großer Lauf nie die Jobs der Redaktion aufhält.

### Abbrechen

`expKernelTransferRunner::cancel( $engine, $jobRef )` bricht über den Ablauf ab: einen wartenden Lauf sofort, einen
laufenden beim nächsten `throwIfCancelled()` der Aufgabe. Verbinden Sie den eigenen Abbruch des Werkzeugs damit, und
`isCancelled()` der Aufgabe mit dem eigenen Abbruch-Merker des Werkzeugs, wo es einen hat.

### Läufe, die nicht über den Runner gehen

Ein zeitgesteuerter Export eines Cronjobs, ein Download in der Anfrage oder der eigene Prozess des Werkzeugs (Ablauf
`process`) schreibt seine Zeile direkt:

```php
$id = expKernelTransferHistory::start( 'transfer.xrowextract', array( 'direction' => 'export', 'title' => ..., 'tool_ref' => $jobID ) );
expKernelTransferHistory::progress( $id, $done, $total );          // höchstens alle 2 Sekunden
expKernelTransferHistory::finish( $id, 'done', array( 'rows_done' => $n, 'bytes' => $bytes ) );

expKernelTransferHistory::record( 'transfer.bccie', array( ...ein beendeter Lauf... ) );
expKernelTransferHistory::findByToolRef( 'transfer.bccie', $jobID ); // die Zeile zum eigenen Job des Werkzeugs
```

oder führt seine Aufgabe im eigenen Prozess mit `expKernelTransferRunner::execute( $taskClass, $params, $context )`
aus, das die Zeile genauso führt wie die Abläufe. Ein Schreibvorgang wirft nie und bricht das Werkzeug nie: ohne die
Tabelle oder bei einem Datenbankfehler tut er nichts (und hinterlässt eine Debug-Warnung).

## Die Historie

### Die Tabelle und das Update

`exp_transfer_run` ist in `share/db_schema.dba` angelegt (das alle Datenbanken installiert: MySQL und MariaDB,
PostgreSQL, SQLite, Oracle mit ezoracle, MongoDB) und in den Kernel-Schemas von MySQL, PostgreSQL und SQLite. Eine
Installation von vor 6.0.15 bekommt sie aus den Update-Dateien `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`
(abgesichert, sie können erneut laufen), oder auf jeder Datenbank mit

```bash
php update/common/scripts/6.0/createtransferruntable.php --dry-run   # sagt, ob sie fehlt
php update/common/scripts/6.0/createtransferruntable.php
```

Solange die Tabelle fehlt, arbeiten die Werkzeuge wie bisher und schreiben keine Historie.

| Spalte | Inhalt |
|---|---|
| `tool` | Die Register-ID: `transfer.xrowextract`, `transfer.bccie`, `transfer.sqliimport` ... |
| `direction`, `kind` | `import` oder `export`; `export`, `import`, `dry_run`, `retry` oder `undo` |
| `title`, `scope`, `scope_ref`, `format` | Was der Lauf getan hat: „Artikel als CSV“, `class` / `article`, `csv` |
| `user_id`, `siteaccess` | Wer ihn ausgeführt hat, von wo |
| `state` | `queued`, `running`, `done`, `failed`, `cancelled` |
| `created`, `started`, `finished`, `duration`, `modified` | Zeitstempel; die Dauer in Sekunden |
| `rows_total`, `rows_done`, `rows_failed`, `bytes` | Die Zähler (`rows_done` enthält die fehlgeschlagenen Zeilen) |
| `file_ref` | Die Ergebnisdatei, relativ zum Job des Werkzeugs (nie ein Pfad) |
| `engine`, `job_ref` | `expqueue` (die UUID des Jobs), `contentjob` (seine ID), `process`, `inline` |
| `tool_ref`, `detail_url` | Der eigene Job oder Lauf des Werkzeugs und der Link auf seine Seite (eine Modul-URL dieser Installation) |
| `error`, `detail` | Ein kurzer Fehler; JSON mit bis zu 20 Fehlermeldungen und was das Werkzeug ergänzt |

Beendete Läufe bleiben `[TransferSettings] KeepDays` Tage (365); der Cronjob-Teil `contentjobs` entfernt ältere. Die
Werkzeuge behalten ihre eigenen Daten (Zeilen, Protokolle, Dateien) so lange, wie ihre eigenen Einstellungen sagen.

### Wer welche Läufe sieht

`expKernelTransferHistory::visibleFilter()` gibt einem Benutzer die Läufe der Werkzeuge, die das Register ihm
anzeigt (das Werkzeug ist aktiv und seine Richtlinie erteilt). Davon sieht er die Läufe aller Benutzer nur, wenn die
`HistoryAllPermission=` des Werkzeugs erteilt ist (Export: `xrowextract/all_jobs`), sonst nur seine eigenen.

### Lesen

```php
expKernelTransferHistory::listVisible( array( 'tool' => 'transfer.bccie', 'from' => $from, 'to' => $to ), 50 );
expKernelTransferHistory::statisticsRows( array( 'from' => $from, 'to' => $to ) );   // was der Benutzer sehen darf
expKernelTransferHistory::toolOptions();                                              // für einen Werkzeug-Filter
expKernelTransferHistory::listRuns( $filter, $offset, $limit );                       // ohne Rechteprüfung
expKernelTransferHistory::count( $filter );
```

Ein Filter nimmt `tool` (eine ID oder eine Liste), `direction`, `kind`, `state` (einer oder eine Liste), `user_id`,
`from` (eingeschlossen) und `to` (ausgeschlossen) auf den Start. `statisticsRows()` liefert die Zeilen in der Form
eines Laufs von Imports (`id`, `import_id` 0, `tool`, `user_id`, `run_kind`, `status` als `completed`,
`completed_with_errors`, `failed`, `cancelled` oder `running`, `started_at`, `finished_at` und die Zähler `*_count`,
mit `processed_count` als erledigte Zeilen ohne die fehlgeschlagenen und `failed_count` als fehlgeschlagene Zeilen),
damit die Statistik-Seite sie mit denselben Funktionen zählt wie ihre eigenen Läufe.

## Tests

`expKernelTransferRunsTest` (ohne Datenbank): die Tabelle ist in jedem Schema und jeder Update-Datei gleich, die
Bezeichner-Grenzen von Oracle, das SQLite-Update zweimal ausgeführt, die Werte, die eine Zeile halten kann, die
Statistik-Zeile, die Filter, die Wahl des Ablaufs und sein Rückfall, die Prüfung der Nutzlast, ein Lauf einer Aufgabe
mit Fortschritt, ein Abbruch und ein Fehler, die neuen Register-Schlüssel und wer welche Läufe sieht, die
Einstellungen und der Inhalts-Job-Typ.
