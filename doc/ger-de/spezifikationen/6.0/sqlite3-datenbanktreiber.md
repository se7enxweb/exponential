# Spezifikation: der SQLite3-Datenbanktreiber

Diese Seite ist die Referenz für den SQLite-Treiber des Legacy-Kernels, `eZSQLite3DB`, und seinen Schema-Handler
`eZSQLiteSchema`: wie sie registriert werden, wie eine Verbindung geöffnet und abgestimmt wird, wie Transaktionen auf den
einzigen Schreiber warten und welches SQL der Treiber übersetzt. Lesen Sie sie, wenn Sie eine Installation unter SQLite
betreiben oder abstimmen oder SQL schreiben, das auf jeder Engine laufen muss. Die aufgabenorientierte Einführung ist
[SQLite-Datenbankunterstützung](../../funktionen/6.0/sqlite-datenbank.md).

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/site.ini` | `DatabaseSettings` | `ImplementationAlias[sqlite3]` | `eZSQLite3DB` | Installation |
| `settings/site.ini` | `DatabaseSettings` | `SQLitePragmas[]` | keiner; Einträge `name=value` überschreiben die [PRAGMA-Standardwerte](#pragma-standardwerte-und-overrides) | Installation |
| `settings/site.ini` | `DatabaseSettings` | `SQLiteTransactionWait` | `25` (Sekunden) | Installation |
| `settings/dbschema.ini` | `SchemaSettings` | `SchemaPaths[sqlite]`, `SchemaPaths[sqlite3]` | `lib/ezdbschema/classes/ezsqliteschema.php` | Installation |
| `settings/dbschema.ini` | `SchemaSettings` | `SchemaHandlerClasses[sqlite]`, `SchemaHandlerClasses[sqlite3]` | `eZSQLiteSchema` | Installation |
| `settings/setup.ini` | `DatabaseSettings` | `DefaultType` | `sqlite3` | Setup-Assistent |

Einen Wert lesen Sie so:

```bash
./console exp:ini get site.ini/DatabaseSettings/SQLiteTransactionWait --allow-root-user
```

Erwartete Ausgabe: `60`, sofern kein Override den Wert ändert.

## Klassen und Dateien

| Klasse | Datei | Rolle |
|---|---|---|
| `eZSQLite3DB` (erweitert `eZDBInterface`) | `lib/ezdb/classes/ezsqlite3db.php` | Verbindung, Abfragen, Transaktionen, Escaping, Auflisten der Datenbanken. |
| `eZSQLiteSchema` | `lib/ezdbschema/classes/ezsqliteschema.php` | Liest ein laufendes Schema (`fetchTableFields`, `fetchTableIndexes`, nutzt die PRAGMA-API von SQLite), erzeugt SQL (`generateDropIndexSql`, Create- und Alter-Anweisungen). |
| SQL-Dateien | `kernel/sql/sqlite/schema.sql`, `kernel/sql/sqlite/cleandata.sql` | Schema und Daten einer leeren Installation. |

Beide Klassen sind in den Autoload-Arrays registriert; der Eintrag für `eZSQLite3DB` wurde
im April 2026 zu `autoload/ezp_kernel.php` hinzugefügt (`cefe8f13a2`). Ohne diesen
Eintrag endete der Alias `sqlite3=eZSQLite3DB` in einem fatalen Autoload-Fehler und einer
leeren Datenbankverbindung, wenn der Kernel in einem Bridge-Aufbau geladen wurde.

## Setup-Assistent

Die Datenbanktabelle des Setup-Assistenten (`kernel/setup/ezsetupcommon.php`) führt den
Typ `sqlite3` mit dem Treiber `sqlite3`, dem Namen „SQLite“, der erforderlichen Version `3.0.1`,
verfügbaren Demodaten und Unicode-Unterstützung.

## Verbindung

- Der Konstruktor benötigt die PHP-Extension `sqlite3`. Ohne sie meldet der Handler
  `ERROR_MISSING_EXTENSION` über die Warnungsliste und bleibt
  ohne Verbindung.
- `connect( $fileName )` löst den Dateinamen so auf:
  - `:memory:` bleibt eine Datenbank im Arbeitsspeicher;
  - ein Name, der mit `/` beginnt, ist ein absoluter Pfad und wird unverändert verwendet;
  - jeder andere Name wird in `var/storage/sqlite3/` abgelegt (Konstante
    `STORAGE_DIRECTORY`); ein fehlendes Verzeichnis wird rekursiv angelegt.
- Eine nicht erreichbare Datei löst `eZDBNoConnectionException` aus, wie beim MySQL-Treiber,
  weil der Setup-Assistent und `index.php` von dieser Exception abhängen.
- Bei jeder Verbindung wendet der Treiber seine PRAGMAs an (`applyPragmas()`) und
  schaltet dann das Journal in den WAL-Modus (`useWAL()`).

### PRAGMA-Standardwerte und Overrides

| PRAGMA | Standard | Wirkung |
|---|---|---|
| `synchronous` | `NORMAL` | Mit WAL weiterhin absturzsicher; `FULL` synchronisiert bei jedem Commit. |
| `cache_size` | `-65536` | 64 MB Seiten-Cache (der SQLite-Standard ist 2 MB). |
| `mmap_size` | `268435456` | Lesen über 256 MB gemappten Speicher. |
| `temp_store` | `MEMORY` | Sortierungen und temporäre Tabellen im Arbeitsspeicher. |
| `busy_timeout` | `5000` | Bis zu 5 s auf eine Sperre warten, statt sofort fehlzuschlagen. |

Überschreiben oder ergänzen Sie Einträge mit `[DatabaseSettings] SQLitePragmas[]=name=value` in
`settings/site.ini`.

## Transaktionen und Schreiber

SQLite hat einen einzigen Schreiber. Seit 6.0.15 (1. bis 2. Oktober 2026) beginnt eine Transaktion mit `BEGIN IMMEDIATE`, sodass die Schreibsperre gleich zu Beginn genommen wird, wo der Busy-Timeout greift. Ein verzögertes `BEGIN` ließ eine Transaktion bei ihrem ersten Schreibzugriff mit „database is locked“ fehlschlagen, sobald eine andere Verbindung seit ihrem Lesen einen Commit ausgeführt hatte.

| Aspekt | Verhalten |
|---|---|
| Warten | `begin()` reiht sich hinter einem Schreiber-Tor ein (eine Sperrdatei namens `<database>.writer-lock` neben der Datenbankdatei, die das Betriebssystem freigibt, wenn ein Prozess endet), höchstens `[DatabaseSettings] SQLiteTransactionWait` Sekunden lang (Standard `25`); die Versuche werden seltener, je länger das Warten dauert, und die Schreiber, die am längsten gewartet haben, kommen zuerst an die Reihe. Das Warten an dieser Stelle ist sicher, weil noch nichts geschrieben wurde. Halten Sie den Wert unter dem Timeout der Anfrage: Velocity beendet eine Anfrage nach `Q.webserver.requestTimeout`, sofern nicht geändert 30 s, daher der Standard von 25. |
| Fehlschlag | Ein Start, der trotzdem nichts erhält, wird sofort als fehlgeschlagene Transaktion mit dem Grund gemeldet, und es wird nichts geschrieben. Früher zählte das generische `begin()` ihn als gestartet, und spätere Anweisungen liefen einzeln, jede mit eigenem Commit. |
| Rollback | `ROLLBACK` wird nur gesendet, wenn SQLite eine Transaktion offen hat (kein „cannot rollback - no transaction is active“ mehr). |
| Gemessen | Im Testlauf der Änderung mit 64 gleichzeitig veröffentlichenden Prozessen: vorher schlugen 6 von 192 fehl, danach wurden 192 von 192 vollständig abgeschlossen (für diese Seite nicht wiederholt). |

Die Wartezeit wird über `[DatabaseSettings] SQLiteTransactionWait` gesetzt (siehe [Einstellungen](#einstellungen)). Upgrade-Hinweise:
[SQLite-Transaktionen](../../bc/6.0/sqlite-transaktionen.md).

Weitere Korrekturen aus denselben Tagen:

- `subString( s, n )` ohne Länge gibt den Rest des Strings zurück, wie auf den anderen Engines. Beim Verschieben eines Knotens
  ging früher das letzte Zeichen von `path_string` und `path_identification_string` des verschobenen Teilbaums verloren.
- Der `path_identification_string` eines Knotens wird aus dem für ihn gespeicherten Wert neu geschrieben, nicht aus einer veralteten Kopie,
  die das Knotenobjekt hält.
- Der asynchrone Publisher schließt seine Datenbankverbindung, bevor er für jede Veröffentlichung einen Fork erzeugt. Eine Verbindung darf
  niemals über einen Fork hinweg weitergegeben werden.
- Das Datenbankfeld des Setup-Assistenten bietet nur echte SQLite-Dateien an (`eZSQLite3DB::availableDatabasesIn()`).

## SQL-Kompatibilitätsschicht

| Methode | Verhalten |
|---|---|
| `subString`, `concatString` | Werden auf `substr( s, n )` (der Rest des Strings, von 1 an gezählt wie bei den anderen Engines) und `\|\|` abgebildet. |
| `md5`, `md5UDF` | Eine bei SQLite registrierte Funktion `md5()`. |
| `bitAnd`, `bitOr` | SQL-Fragmente für bitweises AND und OR. Die Aggregate `BIT_OR` und `BIT_AND` werden beim Verbindungsaufbau als SQLite-Benutzerfunktionen registriert, weil die Content-Engine Sprachmasken mit `BIT_OR` zusammenfasst und SQLite keine mitbringt. |
| `rewriteJoinedUpdate` | Schreibt MySQLs `UPDATE ... JOIN` in eine Form um, die SQLite akzeptiert. |
| `lastSerialID` | Verwendet die letzte Insert-Row-ID der Verbindung und prüft bei Tabellen, deren Primärschlüssel mehr als eine Spalte hat, ob die Auto-Increment-Spalte mit der Row-ID übereinstimmt. |
| `escapeString` | SQLite-Escaping; `null` wird zu einem leeren String, wie im MySQL-Treiber. |
| `createDatabase`, `removeDatabase`, `availableDatabases` | Eine Datenbank ist eine Datei, die `connect()` bei Bedarf anlegt, daher stellt `createDatabase` nur sicher, dass das Verzeichnis existiert; `removeDatabase` leert sie, damit Installer ein Schema neu laden können; `availableDatabases` listet die Dateien des Speicherverzeichnisses auf. |
| `eZTableList`, `relationList`, `removeRelation` | Lesen `sqlite_master`. |

Der Code von `eZPersistentObject` und das SQL des Kernels wurden im Januar 2024 angepasst, damit
dieselben Anweisungen auf beiden Engines laufen; die Testläufe fanden außerdem fehlende
Absicherungen mit `is_countable()`, die im selben Zeitraum behoben wurden (siehe
[PHP-8-Unterstützung](../../bc/6.0/php8.md)).

## Bekannte Grenzen

- Ein Schreiber zur selben Zeit (siehe oben).
- Kein Benutzer und kein Passwort; die Zugriffskontrolle ist die des Dateisystems.
- Keine eigene Replikation und kein eigenes Clustering; ein gemeinsam genutztes Dateisystem eignet sich nur
  für Sites, auf denen überwiegend gelesen wird.

## Verwandte Seiten

- [SQLite-Datenbankunterstützung](../../funktionen/6.0/sqlite-datenbank.md), [SQLite für Exponential Platform](../../funktionen/6.0/plattform-sqlite-installation.md)
- [SQLite-Installer der Plattform](platform-sqlite-installer.md), [Datenbanktreiber und Installer](datenbanktreiber-2026-09.md)
- [SQLite-Transaktionen](../../bc/6.0/sqlite-transaktionen.md), [MongoDB-Unterstützung im Kernel](../../bc/6.0/mongodb-kernel-unterstuetzung.md) (die andere Engine außer MySQL), [SQL-Abfrage-Cache](../../bc/6.0/sql-abfrage-cache.md) (Abfrageergebnisse auf jeder Engine)
- Changelogs: [6.0.1](../../../changelogs/6.0/6.0.1.md), [6.0.13](../../../changelogs/6.0/6.0.13.md), [6.0.14](../../../changelogs/6.0/6.0.14.md), [6.0.15](../../../changelogs/6.0/6.0.15.md)
- Chroniken: [Januar 2024, erste Hälfte](../../geschichte/2024/2024-01a.md), [Januar 2024, zweite Hälfte](../../geschichte/2024/2024-01b.md), [April 2026](../../geschichte/2026/2026-04.md), [Juni 2026, zweite Hälfte](../../geschichte/2026/2026-06b.md)
