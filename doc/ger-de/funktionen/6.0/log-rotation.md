# Rotation und Bereinigung der Protokolle

Exponential schreibt seine Protokolle nach `var/log` (oder `var/<site>/log` jeder Website): `error.log`, `warning.log`,
`notice.log`, `debug.log`, `deprecated.log`, `storage.log`, die Cronjob-Protokolle und die Protokolle von Extensions. Diese
Seite beschreibt, wie sie rotiert werden, wie Sie sie bei Bedarf rotieren und wie Sie alte Archive entfernen, auf der
Kommandozeile und unter **Setup > Logdateien** ([der Log-Betrachter](log-betrachter.md)).

## Kurz gesagt

- **Eine Rotation für den ganzen Kernel.** Wenn ein Protokoll über `site.ini [FileSettings] LogFileMaxSize` (2 MB)
  hinauswächst, rotiert es der nächste Schreibvorgang: `<file>.N-1` wird zu `<file>.N`, ..., `<file>` wird zu
  `<file>.1`, und die Archive jenseits von `LogFileRotateCount` (3) werden entfernt. eZDebug (die Debug-Protokolle) und
  eZLog (`storage.log`, Protokolle von Cronjobs und Extensions) verwenden sie beide (`expLogRotator`), ebenso die
  Kommandozeile und der Log-Betrachter.
- **Sicher mit zwei Schreibern.** Auf einem Host, auf dem Velocity als root und PHP-FPM als Benutzer der Website läuft,
  schreiben beide dieselben Protokolle. Eine Rotation nimmt eine exklusive Sperre auf die Datei, die sie rotiert, sodass
  nie zwei Prozesse sie gleichzeitig rotieren; eine Rotation wegen der Größe prüft die Größe unter der Sperre erneut,
  sodass zwei Anfragen, die das Protokoll beide zu groß fanden, es einmal rotieren. Das neue, leere Protokoll wird sofort
  mit Eigentümer, Gruppe und Modus des alten angelegt (dem Eigentümer, wenn die Rotation als root läuft), sodass keiner
  der beiden Benutzer ein Protokoll erhält, in das der andere nicht schreiben kann.
- **Bei Bedarf**: `./console exp:logs --apply --rotate=error,debug`, `--rotate-all` und `--prune` für die Archive jenseits der
  Grenzen; jeweils mit `--dry-run`.
- **Optionale Kompression** der Archive ab `.2` (`LogFileCompress=enabled`, standardmäßig aus). `.1` wird nie
  komprimiert: Eine Anfrage, die das Protokoll kurz vor der Rotation geöffnet hat, schreibt vielleicht noch hinein.

## Einstellungen

Alle in `site.ini [FileSettings]`; überschreiben Sie sie in `settings/override/site.ini.append.php`.

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `LogFileMaxSize` | `2097152` | die Größe in Bytes, bei der ein Protokoll vom nächsten Schreibvorgang rotiert wird. Die Konstanten `EZPUBLISH_LOG_MAX_FILE_SIZE` und `CUSTOM_LOG_MAX_FILE_SIZE` in `config.php` gewinnen. |
| `LogFileRotateCount` | `3` | wie viele Archive (`.1` .. `.N`) jedes Protokoll behält. `0` schaltet die Rotation nach Größe ab (das Protokoll wächst). `EZPUBLISH_LOG_ROTATE_FILES` und `CUSTOM_LOG_ROTATE_FILES` in `config.php` gewinnen. |
| `LogFileCompress` | `disabled` | `enabled`: Archive ab `.2` werden zu `.2.gz`, `.3.gz` ... komprimiert. Der Log-Betrachter liest und durchsucht sie. |
| `LogFileMaxAge` | `0` | Tage, nach denen eine Bereinigung ein Archiv entfernt, gleich, welche Nummer es hat. `0`: keine Altersgrenze. Eine Rotation entfernt nie nach Alter. |
| `LogFilePermissions` | `0666` | der Modus einer Protokolldatei, die angelegt wird, wo keine existierte (ein rotiertes Protokoll behält den Modus der alten Datei). |

Welche Dateien sich rotieren und bereinigen lassen, ist die Freigabeliste des Log-Betrachters: die Verzeichnisse aus
`logviewer.ini [LogViewerSettings] Directories[]` (`var/log`, das Protokollverzeichnis, in das eZDebug schreibt, und
jedes `var/*/log`), echte Pfade innerhalb der Installation. Die dort per Schlüssel konfigurierten Protokolle
(`LogList[]`, etwa das des Webservers) werden nie rotiert oder bereinigt: Das Programm, das sie schreibt, ist für ihre
Rotation zuständig.

### Pro Protokoll: logviewer.ini [RotationSettings]

Ein Protokoll kann eigene Grenzen haben, nach seinem Schlüssel (dem Schlüssel der Permalinks des Log-Betrachters):

```ini
# settings/override/logviewer.ini.append.php
[RotationSettings]
MaxSize[debug]=10485760
Keep[debug]=1
Compress[error]=enabled
MaxAgeDays[error]=30
```

Sie gelten für Rotation, Bereinigung und Kompression bei Bedarf, den Cronjob-Teil und die Aktionen des Log-Betrachters.
Ein Protokoll ohne Eintrag nimmt die Werte aus `site.ini [FileSettings]` oben. Die Rotation nach Größe beim Schreiben
eines Protokolls verwendet immer `site.ini`.

Die Anfrageprotokolle bringen eigene Einträge mit: `404.log` und `security.log` behalten 5 Archive, komprimiert, mit einer
Größengrenze von 10 MB, bereinigt nach 30 (404) und 90 (security) Tagen ([Anfrageprotokolle](anfrageprotokolle.md)).

## Auf der Kommandozeile

```bash
./console exp:logs --archives                    # each log with its archives, their sizes, and the limits
./console exp:logs --apply --rotate=error,debug          # rotate these now, whatever their size
./console exp:logs --apply --rotate=var/site/log/storage.log
./console exp:logs --apply --rotate-all                  # every log that is not empty
./console exp:logs --rotate-all --dry-run        # what would be rotated
./console exp:logs --prune --dry-run             # archives beyond LogFileRotateCount and LogFileMaxAge, and bare .gz files
./console exp:logs --apply --prune                       # remove them (all logs)
./console exp:logs --apply --prune=error,debug           # only these logs
./console exp:logs --apply --compress                    # compress the plain archives from .2 on
./console exp:logs --apply --truncate=debug              # empty a log by rotating it (its content stays in debug.log.1)
./console exp:logs --apply --truncate=debug --in-place   # empty it in place, keeping no copy
./console exp:logs --apply --rotate-all --force          # also the logs under their MaxSize
./console exp:logs --apply --prune --days=7              # an age limit for this run instead of MaxAgeDays
./console exp:logs --archives --json             # the same as JSON
```

`--rotate-all` rotiert die Protokolle über ihrem `MaxSize` (mit `--force`: jedes Protokoll, das nicht leer ist);
`--rotate=<keys>` rotiert die genannten unabhängig von ihrer Größe. Jede Operation ist zuerst ein Plan (`--dry-run` gibt
ihn aus) und wird dann Schritt für Schritt ausgeführt, jeder Schritt unter der Sperre erneut gegen die Platte geprüft.

Ein Protokoll wird mit seinem **Schlüssel** benannt, wie in den Permalinks des Log-Betrachters
(`setup/logs/view/<key>`): `error` für `var/log/error.log`, `<site>-<name>` für `var/<site>/log/<name>.log`
(`site-storage`), oder mit seiner **ID**, dem Pfad relativ zum Wurzelverzeichnis der Installation. Ein Name, der kein
Protokoll der Freigabeliste ist (ein Pfad außerhalb der Protokollverzeichnisse, `..`, ein Archiv wie `error.log.1`, ein
symbolischer Link aus den Verzeichnissen heraus, ein Protokoll des Webservers), wird als
`FAIL: not a log that can be rotated` gemeldet, und nichts wird angefasst.

Exit-Codes: `0` erledigt, `1` nichts zu tun (die genannten Protokolle waren leer, nichts zu bereinigen), `2` ein
unbekannter Name oder ein Fehlschlag. Führen Sie es als der Benutzer aus, dem die Protokolle gehören, oder als root (die
neuen Protokolle behalten dann ihren Eigentümer).

Rotationen werden im Prüfprotokoll als `system.log.rotate` erfasst, Bereinigungen als `system.log.delete` (Kanal system),
mit denselben Namen wie die Aktionen des Log-Betrachters.

### Als Cronjob-Teil

Die Rotation nach Größe braucht keinen Auftrag. Der Cronjob-Teil `logs` (`cronjobs/explogrotate.php`, in keinem
Standardteil, er läuft also nur auf Anforderung) rotiert jedes Protokoll über seinem `MaxSize`, bereinigt die Archive
jenseits von `Keep` und `MaxAgeDays` und komprimiert die Archive der Protokolle mit eingeschaltetem `Compress`; jede
Änderung wird im Prüfprotokoll erfasst:

```bash
php runcronjobs.php logs
# or every night, as the site's user (or root, the new logs keep their owner):
15 3 * * * www-data cd /path/to/exponential && php runcronjobs.php -q logs
```

### Mit logrotate Wenn stattdessen `logrotate` des Systems `var/log` verwaltet, setzen Sie `LogFileRotateCount=0`,
damit der Kernel nicht zusätzlich rotiert, und verwenden Sie dort `copytruncate` oder `create <mode> <owner> <group>`.

## Im Log-Betrachter

**Setup > Logdateien** bietet für das geöffnete Protokoll **Jetzt rotieren** an (`setup/managelogs`, eingeschaltet mit
`logviewer.ini [ActionSettings] Rotate=enabled`), das dieselbe Rotation verwendet. Siehe
[den Log-Betrachter](log-betrachter.md), „Aktionen“.

## Für Entwickler

- `expLogRotator` (`lib/ezfile/classes/explogrotator.php`): `rotate( $path, $keep, $options )` (`compress`, `minSize`,
  `wait`), `archives( $path )`, `prune( $path, $keep, $maxAge, $dryRun )`, `recreate()`, `gzip()`. Reines Dateisystem,
  keine Einstellungen: Es läuft auf den frühesten Fehlerpfaden.
- `eZDebug::rotateLog( $file, $minSize = 0 )` und `eZLog::rotateLog( $file, $minSize = 0 )` rufen es mit
  `LogFileRotateCount` auf; ein Prozess, der seine Klassen geladen hat, bevor es `expLogRotator` gab (ein lange laufender
  Worker), rotiert auf die alte Weise, bis er neu startet.
- `expLogManager` (`kernel/classes/logviewer/explogmanager.php`), die API der Kommandozeile, des Cronjob-Teils und der
  Aktionen des Log-Betrachters: `logs()` (jedes Protokoll mit seinen Archiven, Größen, `growth` in Bytes pro Stunde seit
  der letzten Rotation, `limits` mit ihren Quellen, `writable`, `owner`), `effectiveSettings( $key )`,
  `select( $names )`, `plan( $keys|'all', 'rotate'|'prune'|'compress'|'truncate', $opts )` (der Probelauf: Schritte
  `{key, op, from, to, bytes}`), `apply( $plan )` (jeder Schritt erneut über die Freigabeliste aufgelöst; ein Schritt, der
  eine andere Datei als das Protokoll oder eines seiner Archive nennt, wird abgelehnt) und die Kurzformen
  `rotate( $key )`, `prune( $key, $days )`, `compress( $key )`, `truncate( $key, $archiveFirst = true )`.
- Tests (ohne Datenbank): `tests/tests/lib/ezfile/expLogRotatorTest.php`,
  `tests/tests/kernel/logviewer/expLogManagerTest.php`.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
