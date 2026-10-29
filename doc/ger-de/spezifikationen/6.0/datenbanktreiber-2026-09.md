# Spezifikation: Datenbanktreiber und Installer (SQLite, PostgreSQL, MySQL, Oracle)

Diese Seite beschreibt, was sich vom 16. bis 30. September 2026 an den Datenbanktreibern, den Schema-Handlern und dem
Setup-Assistenten geändert hat (dazu die Arbeit am Oracle-Abfrage-Cache vom 1. und 2. Oktober), warum das wichtig ist
und wie Sie es in Ihrer Installation prüfen. Lesen Sie sie, wenn Sie Exponential auf SQLite, PostgreSQL oder Oracle
installieren oder betreiben oder wenn sich ein Upgrade von einer älteren 6.0-Version auf Ihrer Datenbank anders verhält.

An anderer Stelle:

- MongoDB: [MongoDB-Unterstützung im Kernel](../../bc/6.0/mongodb-kernel-unterstuetzung.md), Abschnitt „Addendum:
  driver and kernel fixes of 18 to 20 September 2026“.
- SQLite-Transaktionen (Oktober 2026): [SQLite3-Datenbanktreiber, Transaktionen und Schreiber](sqlite3-datenbanktreiber.md#transaktionen-und-schreiber).
- Die Installer: [Installer-Logs und Startdaten](installer-protokolle-und-startdaten.md) und
  [Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md).

## Einstellungen auf dieser Seite

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/site.ini` | `DatabaseSettings` | `SQLitePragmas[]` | leer; es gelten die Standardwerte des Treibers (`synchronous=NORMAL`, `cache_size=-65536`, `mmap_size=268435456`, `temp_store=MEMORY`, `busy_timeout=5000`) | Installation, nur SQLite |
| `settings/site.ini` | `DatabaseSettings` | `SQLiteTransactionWait` | `25` | Installation, nur SQLite |
| `settings/setup.ini` | `DatabaseSettings` | `DefaultType` | `sqlite3` | Setup-Assistent |
| `settings/setup.ini` | `DatabaseSettings` | `DefaultPort_pgsql`, `DefaultUser_pgsql` | `5432`, `postgres` | Setup-Assistent |

## SQLite

| Änderung | Was sie behebt | Wo nachsehen |
|---|---|---|
| **Indexnamen werden datenbankweit eindeutig gemacht.** Indexnamen gelten in SQLite für die ganze Datenbank, nicht wie in MySQL pro Tabelle, und Schemadateien verwenden Namen wie `contentobject_id` in mehreren Tabellen. Ein Index wird als `<table>__<name>` angelegt, sofern sein Name nicht schon mit dem der Tabelle beginnt; der Schemavergleich verwendet dieselben Namen. Ein früher angelegter Index behält seinen einfachen Namen und wird unter diesem gelöscht. | Die zweite Tabelle mit demselben Namen schlug fehl, und das Einspielen des Schemas brach ab: `cjw_newsletter` verlor auf einer Standard-SQLite-Installation vier Tabellen. | `lib/ezdbschema/classes/ezsqliteschema.php` |
| **Ein zusammengesetzter Schlüssel bleibt vollständig erhalten.** SQLite erlaubt `AUTOINCREMENT` nur auf einem einspaltigen Primärschlüssel, deshalb wurden die 13 Tabellen mit dem Schlüssel `(id, version)` (darunter `ezcontentclass`, `ezcontentclass_attribute`, `ezcontentobject_attribute`, `ezworkflow`) nur mit `id` als Schlüssel angelegt, und das Speichern von Version N+1 ersetzte Version N (der Kernel schreibt auf SQLite mit `INSERT OR REPLACE`). Die Auto-Increment-Spalte eines zusammengesetzten Schlüssels ist jetzt ein einfaches `INTEGER`; der Treiber nummeriert neue Zeilen wie bisher (`MAX + 1`). | Eine Klasse oder ein Attribut verlor ihre früheren Versionen. | dieselbe Datei |
| **Die Datenbankprüfung** rief drei Schema-Methoden auf, die es nicht gab, und antwortete mit 500. | Setup > System Upgrade > Datenbankprüfung auf SQLite. | |
| **Verbindungseinstellungen für eine Website.** Jede Verbindung erhält `synchronous=NORMAL` (mit WAL weiterhin absturzsicher), einen Seiten-Cache von 64 MB, eine Speicherabbildung von 256 MB, temporäre Tabellen im Arbeitsspeicher und ein Busy-Timeout von 5 Sekunden statt der SQLite-Standardwerte mit einem Busy-Timeout von 0 (eine Anfrage, die auf die Schreibsperre einer anderen traf, schlug sofort fehl). Die Logzeile einer fehlgeschlagenen Abfrage enthält die Meldung von SQLite selbst. Seitenaufrufe wurden in der Messung nicht schneller (die 12-MB-Datenbank lag bereits im Datei-Cache); die Einstellungen zielen auf Schreibvorgänge, gleichzeitigen Zugriff und Sperrfehler unter Last. | „database is locked“ unter Last. | `settings/site.ini` `[DatabaseSettings] SQLitePragmas[]` |
| **Eine Datenbankdatei, die sich nicht öffnen lässt, ist eine fehlgeschlagene Verbindung.** Ein Verzeichnis, das SQLite nicht anlegen konnte, ein Verzeichnis an der Stelle der Datei oder fehlende Schreibrechte führten früher zu einer Warnung und einer nicht abgefangenen Ausnahme; jetzt wird wie beim MySQL-Treiber `eZDBNoConnectionException` geworfen, sodass der Setup-Assistent und `index.php` eine nicht erreichbare Datenbank melden. `availableDatabases()` listet nur Datenbanken auf, nicht die Dateien `-wal`, `-shm` und `-journal`. `eZSQLite3DB::filePath()` und `STORAGE_DIRECTORY` (`var/storage/sqlite3`) liefern die Datei, die ein `Database`-Wert benennt. | | `lib/ezdb/classes/ezsqlite3db.php` |
| `escapeString( null )` gibt wie der MySQL-Treiber eine leere Zeichenkette zurück (eine Deprecation in PHP 8.1). | Der Setup-Assistent löste eine Deprecation aus. | |
| **Die REST-API schlägt OAuth-Token auf einer SQLite-Site nach.** Die verzögerte Datenbankeinrichtung der REST-Schicht kannte nur die Implementierungen für MySQL, PostgreSQL und Oracle und warf bei `sqlite3` eine Ausnahme; jetzt öffnet sie dieselbe Datei wie der Treiber der Site (oder eine In-Memory-Datenbank bei `:memory:`). | Ein unbekanntes OAuth-Token wurde mit einem Serverfehler statt mit 401 beantwortet. | |
| **Die Suchindizierung nimmt den gebündelten Weg** (ein mehrzeiliges `INSERT`, eine `IN`-Liste und ein arithmetisches `UPDATE`; SQLite akzeptiert ein mehrzeiliges `INSERT` seit 3.7.11, PostgreSQL seit 8.2), ebenso wie auf MySQL und MongoDB. | Gemessen an 20 Objekten mit 12.546 Wortverknüpfungen auf SQLite: vorher 1,62 s, nachher 0,88 s (81 ms bzw. 44 ms pro Objekt); der Index ist identisch. | Such-Plugin `ezsearchengine` |
| **Das eigene SQLite-Schema und die Grunddaten des Kernels lassen sich laden.** `kernel/sql/sqlite/schema.sql` legte `sqlite_sequence` an, das SQLite für sich selbst verwaltet; `kernel/sql/sqlite/cleandata.sql` verwendete MySQL-Escapes in Zeichenketten. Die Literale verwenden jetzt die Quotierung von SQLite; alle 2.022 Zeilen werden geladen, und jeder serialisierte Wert lässt sich wieder lesen. Die nicht verwendeten Demodaten-Dateien (`workingdata.sql`, `workingdataandschema.sql`, `workingexample.db`) wurden entfernt. | Eine SQLite-Installation aus den SQL-Dateien. | `kernel/sql/sqlite/` |

SQLite-Einstellungen (Datei `settings/site.ini`, Block `[DatabaseSettings]`, Geltungsbereich: die
Installation, nur SQLite):

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `SQLitePragmas[]` | leer (es gelten die Standardwerte des Treibers: `synchronous=NORMAL`, `cache_size=-65536`, `mmap_size=268435456`, `temp_store=MEMORY`, `busy_timeout=5000`) | Ein `name=value` pro Zeile, das die Standardwerte des Treibers überschreibt. Führen Sie nach einer Installation einmal `ANALYZE` aus, damit der Abfrageplaner Statistiken hat. |
| `SQLiteTransactionWait` | `25` | Siehe [Transaktionen und Schreiber](sqlite3-datenbanktreiber.md#transaktionen-und-schreiber). |

Prüfen Sie die Werte, die Ihre Site verwendet:

```bash
./console exp:ini get site.ini/DatabaseSettings/SQLitePragmas --allow-root-user
grep -n 'SQLitePragmas' settings/site.ini
```

## Der Setup-Assistent

| Änderung | Einstellung |
|---|---|
| **SQLite steht an erster Stelle und ist vorausgewählt**, und die Extension `sqlite3` wird zuerst geprüft. Fehlt die PHP-Extension, wählt die Seite die erste verfügbare Datenbank vor und erklärt, warum SQLite fehlt und wie Sie es aktivieren. Ein Absenden ohne Auswahl übernimmt die vorausgewählte Standardvorgabe; ein Kickstart-`Type` von `mysql` oder `sqlite` wird auf seinen Treiber abgebildet, wie es bei `postgresql` bereits der Fall war. | `settings/setup.ini [DatabaseSettings] DefaultType=sqlite3` |
| **Die SQLite-Datei wird geprüft, bevor irgendetwas sie öffnet**: ein einfacher Name mit der Endung `.db`, `.db3`, `.sqlite` oder `.sqlite3`, abgelegt in `var/storage/sqlite3` (ein absoluter Pfad nur aus `kickstart.ini`); das Verzeichnis beschreibbar oder anlegbar; eine vorhandene Datei beschreibbar und eine SQLite-Datenbank. Jede Ablehnung nennt die Datei, das Verzeichnis und den Benutzer des Webservers. Bei einer Datei, die bereits Tabellen enthält, wird auf der Seite **Seitendetails** mit denselben Auswahlmöglichkeiten nachgefragt wie bei einer nicht leeren MySQL-Datenbank (behalten und ergänzen, entfernen, behalten und überspringen, eine andere wählen). Der gewählte Name bleibt zwischen den Seiten erhalten. | |
| Das SQLite-Formular sendet weder Server noch Port, Benutzer oder Passwort; sie werden als leere Werte geführt, sodass eine Web-Installation nicht mehr 14 Warnungen „Undefined array key“ protokolliert. Die Zeitzonenprüfung schlägt nur fehl, wenn `php.ini` keine Zone setzt und `index.php` auf UTC zurückgefallen ist (vorher schlug sie auf jedem UTC-Server fehl). | |
| **PostgreSQL-Standardwerte**: Die Datenbankseite füllt Port 5432 und den Benutzer `postgres` vor. Ein `Default<Setting>_<type>` ersetzt `Default<Setting>` für dieses System. | `setup.ini [DatabaseSettings] DefaultPort_pgsql=5432`, `DefaultUser_pgsql=postgres` |

## PostgreSQL

- **`pgcrypto` wird bei Bedarf angelegt.** Der Installer braucht `digest()`, das
  aus `pgcrypto` stammt (seit PostgreSQL 13 eine vertrauenswürdige Extension, die der
  Eigentümer der Datenbank anlegen darf). Er legt die Extension an, wenn der Server sie
  mitliefert (`CREATE EXTENSION IF NOT EXISTS pgcrypto`), und lehnt nur ab, wenn das nicht
  möglich ist. Die Meldungen nennen `pgcrypto` und die heutigen Ursachen (Server, Port, Benutzer,
  Passwort, Datenbank, `listen_addresses`, `pg_hba.conf`) mit Links zur
  PostgreSQL-Dokumentation; der alte Hinweis auf `postmaster -i` und PostgreSQL
  7.2 ist entfallen.
- **Der Assistent installiert in die Datenbank, die auf der Datenbankseite genannt ist.** Früher
  verband er sich mit `template1`, listete jede Datenbank auf dem Server auf und wählte die
  erste vor, was auf einem gemeinsam genutzten Server die Datenbank eines anderen ist. Die Anmeldung wird jetzt
  gegen die eingegebene Datenbank getestet (sodass ein Benutzer, dem eine Datenbank gehört und der sich
  mit nichts anderem verbinden darf, durchkommt), und nur ohne Namen wird `template1`
  geöffnet und eine Liste angeboten (Datenbanken, mit denen sich der Benutzer verbinden darf, ohne
  Vorlagen, nach Namen sortiert).
- **Der Schema-Handler** legt Spalten vom Typ `tinyint`, `smallint` und `mediumint`
  (von `cjw_newsletter` verwendet) als `smallint` und `integer` ohne Länge an (sie
  beendeten die Anfrage mit `die()`); ein unbekannter Spaltentyp protokolliert einen Fehler und
  wirft eine Ausnahme. Ein Index, dessen Name bereits an einer anderen Tabelle existiert, wird als
  `<table>__<name>` angelegt, sodass die Indexnamen des Kernels selbst erhalten bleiben.
- Ein PostgreSQL-SQL-Dump, der mit `ezsqldumpschema.php` auf einer Site erstellt wird, die
  eine andere Datenbank verwendet, fragt dort nicht mehr die PostgreSQL-Kataloge (`pg_class`,
  `pg_index`) ab.
- `expInfo::kernelInfo()` las `ezsite_data.value` mit der Spalte in Backticks,
  was MySQL und SQLite akzeptieren, PostgreSQL und Oracle aber nicht, sodass die Installationsversion
  auf diesen beiden leer zurückkam. Die Spalte wird jetzt ohne Quotierung genannt.

## MySQL

Seit PHP 8.1 wirft `mysqli` standardmäßig `mysqli_sql_exception`. Der Treiber
fing nur `ErrorException` ab, sodass ein falsches Passwort oder eine unbekannte Datenbank
die Anfrage mit einer nicht abgefangenen Ausnahme beendete; jetzt wirft er
`eZDBNoConnectionException`, die der Setup-Assistent und `index.php` als
nicht erreichbare Datenbank melden.

## SQL-Dumps aus Schema- und Datendateien

`php bin/php/ezsqldumpschema.php` wandelt `.dba`-Dateien ohne Datenbankverbindung
in SQL um, und jede Schemaklasse fiel dabei auf ihr eigenes Escaping zurück, das bei
Werten mit einem Apostroph durchweg versagte (die ISBN-Gruppennamen „China, People's Republic“ und
„Lao People's Democratic Republic“):

| Datenbank | Vorher | Jetzt |
|---|---|---|
| MySQL | `mysqli_real_escape_string()` ohne Verbindung: ein fataler Fehler beim ersten Wert, sodass sich kein MySQL-Datendump schreiben ließ. | Maskiert dieselben Zeichen wie diese Funktion. |
| SQLite | Gab den Wert unverändert zurück, sodass das Anführungszeichen das Literal beendete. | Verdoppelt das Anführungszeichen. |
| PostgreSQL | `pg_escape_string()` ohne Verbindung (seit PHP 8.1 veraltet). | Verwendet die verbundene Datenbank, wenn es eine gibt, und verdoppelt sonst das Anführungszeichen. |

`kernel/sql/common/cleandata.sql` wurde auf dieselbe Weise geändert (Standard-SQL-Quotierung),
sodass auch PostgreSQL die Datei lädt.

Probieren Sie es aus (nur lesend: die Ausgabe geht auf die Standardausgabe):

```bash
php bin/php/ezsqldumpschema.php --help --allow-root-user
```

## Oracle

Zwei Abfragen schrieben einen Tabellenalias mit `AS` (die Kindanzahl pro Ort in
`eZContentObject::assignedNodes()`, wenn Orte nach der Anzahl ihrer
Kinder sortiert werden, und der Attribut-Join der eingebauten Suchmaschine, wenn Ergebnisse
nach einem Klassenattribut sortiert werden). Oracle erlaubt `AS` nur für Spaltenaliase
(`ORA-00933`), deshalb schlugen beide dort fehl; die Aliase werden jetzt ohne `AS` geschrieben, was
auf jeder Datenbank gleich ist. Installieren Sie auf Oracle mit
`./console exp:install --apply --db=oracle` (siehe
[Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md)).

### Oracle und der SQL-Abfrage-Cache (1. und 2. Oktober 2026)

Der [SQL-Abfrage-Cache](../../bc/6.0/sql-abfrage-cache.md) kennt die Anweisungen von Oracle, sodass der Oracle-Treiber (`extension/ezoracle`, Version in seiner `ezinfo.php`) SELECTs daraus beantworten kann:

- Oracle schreibt veränderliche Werte ohne Klammern, deshalb werden `<sequence>.NEXTVAL` und `.CURRVAL` (der Treiber liest die ID einer neuen Zeile mit `SELECT <sequence>.currval FROM DUAL`), `SYSDATE`, `SYSTIMESTAMP`, die SCN, `SYS_GUID`, `SYS_CONTEXT`/`USERENV` und `DBMS_RANDOM` nie gecacht (eine gecachte Antwort würde eine ID zweimal vergeben).
- Der Katalog von Oracle (`USER_*`, `ALL_*`, `DBA_*`, `CDB_*`, `V$*`) zählt als Systemtabelle.
- Ein anonymer PL/SQL-Block (`DECLARE ... BEGIN ... END;`) ist ein Schreibvorgang, dessen Tabellen sich nicht ermitteln lassen, und macht jedes Ergebnis ungültig; ein alleinstehendes `BEGIN` ist weiterhin der Beginn einer Transaktion.
- Das Cache-Verzeichnis wird dem Benutzer der Site überlassen (ein Root-Prozess hinterließ es früher im Besitz von root, sodass der Cronjob des Site-Benutzers `state.ser` nicht schreiben konnte), und Anweisungen mit langen Zeichenkettenliteralen werden korrekt gelesen.

PHP-8.5-Hinweise, die auf Oracle durch das Lesen des Debug-Berichts jeder Administrationsseite behoben wurden: Eine Präferenz ohne Namen wird weder gespeichert noch gelesen; `createGroupedDataMap()` liest eine leere Kategorie eines Klassenattributs als Zeichenkette (Oracle gibt `''` als NULL zurück); `ezpExtension::getInfo`, `eZTemplateCompiler`, `eZHTTPTool::redirect()`, der INI-Einstellungs-Datentyp, der Operator `ristring` und `{if}` ohne Dateiposition übergeben nicht mehr null, wo PHP 8.5 das als veraltet markiert.

Das SQLite-Gegenstück zu dieser Änderung: [Transaktionen und Schreiber](sqlite3-datenbanktreiber.md#transaktionen-und-schreiber).

## So prüfen Sie es

1. SQLite ist die Standardvorgabe des Installers. Dies gibt die Konfiguration aus und installiert nichts:

   ```bash
   ./console exp:install --print --allow-root-user
   ```

   Erwartet: `Type=sqlite3` im Abschnitt `[database_choice]`.
2. Indexnamen in einer bestehenden SQLite-Datenbank: Eine von einer Extension angelegte Tabelle hat Indizes mit dem Namen
   `<table>__<name>`. Wenn das Werkzeug `sqlite3` installiert ist, listen Sie sie mit
   `sqlite3 var/storage/sqlite3/<file> ".indexes"` auf.
3. MySQL-Anmeldefehler: Setzen Sie auf einem Test-Siteaccess ein falsches Datenbankpasswort. Erwartet: die Seite „Datenbank
   nicht erreichbar“, keine PHP-Ausnahme.

## Verwandte Seiten

- [Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md): die unterstützten Datenbanken in ihrer offiziellen Reihenfolge (SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL), Versionen, Installer und CI-Abdeckung
- Spezifikationen: [SQLite3-Datenbanktreiber](sqlite3-datenbanktreiber.md), [Installer-Logs und Startdaten](installer-protokolle-und-startdaten.md)
- Funktionen: [SQLite-Datenbank](../../funktionen/6.0/sqlite-datenbank.md), [MongoDB als Datenbank](../../funktionen/6.0/mongodb-datenbankunterstuetzung.md), [Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md)
- Upgrade-Hinweise: [Verhaltensänderungen, 16. bis 30. September 2026](../../bc/6.0/verhaltensaenderungen-2026-09b.md), [SQL-Abfrage-Cache](../../bc/6.0/sql-abfrage-cache.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md), Chroniken [16. bis 30. September 2026](../../geschichte/2026/2026-09b.md) und [Juni 2026, erste Hälfte](../../geschichte/2026/2026-06a.md)
