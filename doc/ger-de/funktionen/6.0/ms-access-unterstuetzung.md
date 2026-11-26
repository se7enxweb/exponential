# MS-Access-Unterstützung

Diese Seite richtet sich an Administratoren mit Daten in Microsoft-Access-Dateien (`.accdb`, `.mdb`) und an
Entwickler, die am Access-Treiber mitarbeiten. Ab Exponential 6.0.15 hat der Kernel zwei Teile für Access:

1. **Ein Import-Werkzeug**, `exp:access:import`, das die Tabellen einer Access-Datei in die Datenbank der Site oder in
   eine Datentabellen-Verbindung kopiert, unter Linux mit MDBTools (ohne Access) und unter Windows mit Microsofts
   Access-ODBC-Treiber.
2. **Einen kleinen Access-Treiber**, `expMSAccessDB` (`databaseName()` ist `MSAccess`), über ODBC: Windows mit der
   Access Database Engine (ACE, x64) liest und schreibt; unter Linux liest nur der Import Access-Dateien (der
   ODBC-Treiber von MDBTools ist aus PHP nicht nutzbar).

**Empfehlung: importieren, nicht auf Access betreiben.** Access ist eine Dateidatenbank für einen Arbeitsplatz. Für
alles Echte den Import nehmen; den Treiber für das Lesen einer Access-Datei an Ort und Stelle oder für eine kleine,
meist gelesene Datentabelle unter Windows. Eine Site auf Access wird nicht unterstützt; wie weit man kommt, ist unten
gemessen.

## Inhalt

1. [Eine Access-Datei importieren](#eine-access-datei-importieren)
2. [Der Access-Treiber](#der-access-treiber)
3. [Was geht und was nicht](#was-geht-und-was-nicht)
4. [Eine minimale Site auf Access](#eine-minimale-site-auf-access)
5. [Die Treiber installieren](#die-treiber-installieren)
6. [Tests](#tests)

## Eine Access-Datei importieren

```bash
./console exp:access:import shop.accdb --list                       # Tabellen und Spalten
./console exp:access:import shop.accdb --prefix=shop_               # der Plan: anzulegende Tabellen, nichts geschrieben
./console exp:access:import shop.accdb --prefix=shop_ --apply       # Tabellen anlegen, Zeilen kopieren
./console exp:access:import shop.mdb --connection=archive --tables=Customers,Orders --mode=replace --apply --format=json
```

| Option | Bedeutung |
|---|---|
| `--connection` | `site` (Vorgabe) oder der Bezeichner einer Datentabellen-Verbindung (jede Engine mit Schema: MySQL/MariaDB, PostgreSQL, SQLite, SQL Server, Oracle, ODBC, Access) |
| `--tables` | nur diese Access-Tabellen (Komma-Liste, Namen wie in der Datei) |
| `--prefix` | vor jedem Tabellennamen, z. B. `shop_` |
| `--mode` | `create` (neue Tabellen; eine Tabelle mit Zeilen bleibt unberührt), `append`, `replace` (erst die Zeilen löschen) |
| `--reader` | `auto`, `mdbtools` (Linux, macOS, BSD) oder `odbc` (Windows, ACE) |
| `--apply` | schreiben; ohne zeigt der Befehl nur den Plan |

Exit-Code 0, wenn jede Tabelle und Zeile ankam, 1, wenn eine fehlschlug (die fehlgeschlagenen Zeilen werden
genannt, die anderen geschrieben), 2 bei falschen Optionen oder einer unlesbaren Datei.

**Namen** werden ASCII-Kleinbuchstaben mit `_` (`Order Details` -> `order_details`, `Umsätze` -> `umsaetze`, `Größe`
-> `groesse`); ein SQL-Schlüsselwort bekommt ein `_` angehängt (`Order` -> `order_`). **Typen**: AutoWert ->
`auto_increment` (der Schlüssel der Tabelle; ein Access-Schlüssel auf anderen Spalten bleibt ein eindeutiger Index),
Byte/Integer/Long Integer/Ja-Nein -> `int`, Große Zahl -> `bigint`, Single/Double -> `double`, Währung/Dezimal ->
`decimal`, Datum/Uhrzeit -> `varchar(19)` `Y-m-d H:i:s` (Access speichert keine Zeitzone), Kurzer Text ->
`varchar(n)`, Langer Text -> `longtext`, OLE-Objekt und Binär -> `longtext` in Base64, Replikations-ID -> `char(38)`.
Eine Schlüsselspalte ist `NOT NULL`. IDs werden übernommen (SQL Server: `IDENTITY_INSERT`; PostgreSQL: die Sequenzen
werden hinter die höchste ID gesetzt). Zeilen gehen in Transaktionen zu je 500; eine Zeile, die das Ziel ablehnt,
wird gemeldet und ihr Batch Zeile für Zeile geschrieben.

Gemessen am 25. November 2026 mit MDBTools 1.0.0 unter Linux, Import der Testdateien von MDBTools:

| Datei | Format | Tabellen | Zeilen | SQLite (PDO) | SQL Server, PostgreSQL, MariaDB, SQLite (ODBC) |
|---|---|---|---|---|---|
| `nwind.mdb` | JET3 (Access 97) | 9, eine heißt `Umsätze` | 4.032 | jede Zeile | jede Zeile |
| `DateTestDatabase.mdb` | JET4 (Access 2000-2003) | 1 | 6 | jede Zeile | jede Zeile |
| `ASampleDatabase.accdb` | ACE12 (Access 2007 und neuer) | 1 (`Asset Items`, ein Text-Schlüssel) | 65 | jede Zeile | jede Zeile |

Geprüfte Werte: Namen und Daten eines Mitarbeiters, ein Preis, eine Ja/Nein-Spalte, Fotos als Base64. Unterwegs
gefunden und behoben: MDBTools lehnt einen Tabellennamen wie `Umsätze` in der C-Locale ab (der Import ruft es in
`C.UTF-8` auf); SQL Server lehnt einen Schlüssel auf einer NULL-fähigen Spalte ab und MariaDB eine `NOT NULL`-Spalte
mit `DEFAULT NULL` (Schlüsselspalten sind jetzt `NOT NULL` mit Vorgabewert). Unter Windows importiert der Job
`msaccess-windows` dieselben Dateien über den ODBC-Leser.

## Der Access-Treiber

```ini
[DatabaseSettings]
DatabaseImplementation=msaccess
Database=C:\data\shop.accdb

[MSAccessSettings]
Driver=Microsoft Access Driver (*.mdb, *.accdb)
ExtendedAnsiSQL=enabled
```

`ImplementationAlias[msaccess]` und `[access]` nennen `expMSAccessDB`. `[MSAccessSettings]`: `Driver` (leer: ACE unter
Windows, sonst `MDBTools`), `ExtendedAnsiSQL` (ANSI-92-Modus: `DEFAULT` und `DECIMAL` in `CREATE TABLE`,
`BAND`/`BOR`; eingeschaltet), `ReadOnly`, `Exclusive`, `ConnectionString`, `Layer` (`auto` nimmt zuerst die
odbc-Erweiterung, die einzige Schicht, die die Tabellen von Access auflisten kann). Als Datentabellen-Verbindung:
`Engine=msaccess`, die Datei in einem Verzeichnis aus `datatable.ini [ConnectionSettings] SQLiteDirectories`.

Der Access-Dialekt (`expMSAccessDialect`):

| Kernel-Wort | Access |
|---|---|
| `LIMIT n` | `TOP n` nach `SELECT [DISTINCT]`; ein `UNION` oder eine Anweisung mit eigenem `TOP` wird umhüllt |
| `OFFSET m` | gibt es nicht: `arrayQuery()` liest `TOP m+n` und verwirft die ersten `m` Zeilen in PHP; `sqlLimit()` mit Offset wirft `expDBUnsupportedException` |
| `AUTO_INCREMENT` | `COUNTER` (AutoWert); die neue ID aus `SELECT @@IDENTITY` |
| `CONCAT()`, `\|\|` | `&` |
| `CASE WHEN` | `IIf()` |
| `SUBSTRING()`, `LENGTH()` | `Mid()`, `Len()` |
| Datumsliterale | `#jjjj-mm-tt hh:nn:ss#` |
| `` `name` `` | `[name]` (ein Name darf kein `]` enthalten, höchstens 64 Zeichen) |
| mehrere Anweisungen | eine je Aufruf: `query()` teilt ein Skript an den Semikolons und führt die Teile nacheinander aus (nicht atomar außer in einer Transaktion) |
| Kommentare | entfernt (Access nimmt keine) |
| `UNIX_TIMESTAMP()` | die Tage des Datums als Double mal 86400 (DateDiff in Sekunden läuft nach 2038 über); `Now()` ist die Ortszeit des Rechners |
| `&`, `\|` (Bits) | `BAND`/`BOR` im ANSI-92-Modus; ohne ihn exakte Ganzzahl-Arithmetik für eine konstante Maske |
| Upsert, `GROUP_CONCAT`, `MD5()` | gibt es nicht: `expDBUnsupportedException` (in PHP erledigen) |
| `FOR UPDATE` | entfällt: die Engine sperrt Seiten selbst |

## Was geht und was nicht

Windows: gemessen vom GitHub-Job `msaccess-windows` (windows-2022 und windows-2025, Access Database Engine 2016 x64,
PHP 8.4). Linux: gemessen am 25. November 2026 mit MDBTools 1.0.0 (Ubuntu 24.04).

| Was | Windows, ACE x64 (`msaccess-windows`) | Linux, MDBTools |
|---|---|---|
| Import (`exp:access:import`) | **geht** über den ODBC-Leser für Dateien ab Access 2000 (JET4 `.mdb`, ACE `.accdb`: DateTestDatabase.mdb, ASampleDatabase.accdb); **nicht** für Access-97-Dateien (JET3: ACE 2016 lehnt nwind.mdb ab, "created with a previous version"): diese mit MDBTools importieren | **geht**: die Programme von MDBTools, JET3-, JET4- und ACE-Dateien (oben) |
| `expMSAccessDB` verbinden, Tabellen auflisten (`SQLTables`), Spalten beschreiben | geht (AL-01) | **geht nicht**: der ODBC-Treiber von MDBTools verbindet über PDO_ODBC, kann aber keine Zeilen holen ("Driver does not support this function", SQLFetchScroll), und sein SQL-Parser lehnt `TOP`, `COUNT(*)`, `JOIN` und `INSERT` ab; mit der odbc-Erweiterung von PHP endete der Prozess ohne Meldung. Den Import nehmen |
| DDL aus `.dba` (COUNTER, TEXT(n), LONGTEXT, DECIMAL, DEFAULT, Indizes) | geht (AL-02) | - |
| Einfügen und `@@IDENTITY` | geht (AL-03); DECIMAL(19,0) behält 2^53 + 1 | - |
| Seiten (TOP, Offset in PHP verworfen; Gleichstände bei TOP) | geht (AL-04); ein bloßes `TOP 3` lieferte 4 Zeilen (Gleichstände), `arrayQuery()` 3 | - |
| `&`, `IIf`, `Mid`, `Len`, Datumswerte, Zeitstempel nach 2038, BAND/BOR und die Nachbildung | geht (AL-05); nach 2038 seit der Form mit Tagen als Double (DateDiff/DateAdd in Sekunden scheiterten: "Numeric value out of range") | - |
| Transaktionen | geht (AL-06) | - |
| Unicode, 1 MB langer Text | geht (AL-07) | - |
| Skripte aus mehreren Anweisungen, Kommentare | geht (AL-08) | - |
| abgelehnt: `LIMIT`, `OFFSET FETCH`, `FOR UPDATE`, `MERGE`, Upsert, `GROUP_CONCAT`, `MD5`, `TEXT(300)`; ein zweiter Schreiber auf einer gesperrten Zeile | alle abgelehnt (AL-09): "Syntax error in FROM clause" (LIMIT, FOR UPDATE), "Reserved error (-1001)" (OFFSET FETCH), "Invalid SQL statement" (MERGE), "Size of field is too long" (TEXT(300)), "Could not update; currently locked" (zweiter Schreiber) | - |
| Datentabellen-Verbindung (Engine `msaccess`) | nicht gemessen: die CI-Datei liegt außerhalb von `SQLiteDirectories` (AL-10 wird übersprungen) | - |

Gemessen am 26. November 2026 von `expMSAccessLiveTest` im Job `msaccess-windows` auf windows-2022 und windows-2025
(auf beiden dasselbe Ergebnis); der Job läuft mit jedem Push nach main, und sein Protokoll listet, was Access abgelehnt
hat.

**Grenzen von Access selbst** (Microsofts Spezifikationen, hier nicht gemessen): 2 GB je Datei einschließlich
Systemobjekten; 255 Spalten je Tabelle; 255 Zeichen in einem Kurzen Text; 32 Indizes je Tabelle; 255 gleichzeitige
Benutzer in der Theorie, in der Praxis weit weniger; eine Sperrdatei (`.laccdb`) neben der Datenbank, das Verzeichnis
muss also für jeden Benutzer beschreibbar sein; kein Serverprozess, jeder Client liest die Seiten über das
Dateisystem (über eine Netzwerkfreigabe kann eine abgebrochene Verbindung die Datei beschädigen).

## Eine minimale Site auf Access

Der Test `expMSAccessKernelSchemaTest` legt das Kernel-Schema (`share/db_schema.dba`) in einer leeren `.accdb` an,
schreibt die Kernel-Daten (`share/db_data.dba`) und führt dann Kernel-Lesezugriffe mit Access als Datenbank der Site
aus. Sein Bericht ist das Artefakt `msaccess-kernel-probe-<os>` des Jobs.

Noch nicht gemessen: die Probe läuft zum ersten Mal im Job `msaccess-windows`. Was der Dialekt schon sagt: der
Kernel liest Sprachmasken mit `bitAnd()` (BAND im ANSI-92-Modus, sonst nur eine konstante Maske), blättert mit
`LIMIT`/`OFFSET` über `arrayQuery()` (TOP und PHP) und schreibt stellenweise mit `sqlUpsert()` und `GROUP_CONCAT` (auf
Access abgelehnt); jeder Upgrade-, Cache- und Cluster-Pfad des Kernels setzt einen Server voraus. Eine Site auf Access
ist daher kein Ziel.

## Die Treiber installieren

- **Linux, nur Import**: `apt install mdbtools` (Debian, Ubuntu) oder `dnf install mdbtools` (EPEL); der Import
  braucht `mdb-tables`, `mdb-schema` und `mdb-json` (MDBTools 0.9 oder neuer). Das Paket `odbc-mdbtools` (Treiber
  `MDBTools`) ist aus PHP nicht nutzbar (oben).
- **Windows**: das *Microsoft Access Database Engine 2016 Redistributable*, x64 für ein 64-Bit-PHP
  (`accessdatabaseengine_X64.exe /quiet`); die PHP-Erweiterungen `odbc` und `pdo_odbc`. Eine neue leere Datei legt
  Access an, oder in PowerShell: `(New-Object -ComObject ADOX.Catalog).Create("Provider=Microsoft.ACE.OLEDB.12.0;Data Source=C:\data\neu.accdb")`.
  Ein 32-Bit-Office auf demselben Rechner blockiert das Installationsprogramm der x64-Engine (`/passive` erzwingt es,
  auf eigenes Risiko).

## Tests

| Datei | Wo | Was |
|---|---|---|
| `tests/tests/lib/ezdb/odbc/expMSAccessDialectTest.php` | ohne Datenbank | Token, Namen, Literale, TOP, Aufteilen, Funktionen, Bit-Nachbildung gegen PHP, DDL, die Helfer |
| `tests/tests/lib/ezdb/odbc/expODBCDBTest.php` | ohne Datenbank | Verbindungszeichenketten mit den Schaltern von ACE, MDBTools ohne, die Ausdrücke des Dialekts |
| `tests/tests/kernel/classes/msaccess/expAccessImportTest.php` | ohne Datenbank | Namen, Typen, Schema, der Schema-Parser für MDBTools, Literale, ein Import nach SQLite mit einer schlechten Zeile, die Modi |
| `tests/tests/kernel/classes/msaccess/live/expAccessImportLiveTest.php` | `EXP_TEST_ACCESS_FIXTURES` | die Testdateien von MDBTools nach SQLite und in jedes ODBC-Ziel |
| `tests/tests/lib/ezdb/odbc/live/expMSAccessLiveTest.php` | Windows, `EXP_TEST_MSACCESS_FILE` | der Treiber auf ACE, und was Access ablehnt |
| `tests/tests/lib/ezdb/odbc/live/expMSAccessKernelSchemaTest.php` | Windows, `EXP_TEST_MSACCESS_KERNEL_FILE` | die Probe einer minimalen Site |
