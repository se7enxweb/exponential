---
title: SQLite 3 für Datenbankadministratoren
audience: dba
category: database-administration
order: 8
summary: Was ein Datenbankadministrator über SQLite 3, die Standarddatenbank von Exponential 6.0.x, wissen muss - was der Treiber eZSQLite3DB der Bibliothek hinzufügt, die vier Dateien einer Datenbank, die PRAGMAs der Verbindung und warum jedes gewählt wurde, Write-Ahead-Logging und wo SQLite passt und wo nicht mehr.
keywords: SQLite, SQLite 3, sqlite3, eZSQLite3DB, WAL, Write-Ahead-Logging, PRAGMA, synchronous NORMAL, cache_size, mmap_size, busy_timeout, temp_store, var/storage/sqlite3, exponential.db, Standarddatenbank, eine Datei, write-ahead logging, default database, one file
type: help
---
# SQLite 3 für Datenbankadministratoren

Diese Seite richtet sich an Datenbankadministratoren, die eine Exponential-Website auf SQLite 3, dem Standard des
Installers, betreiben sollen und Serverdatenbanken besser kennen als Dateidatenbanken. Sie beantwortet: was der Treiber der
SQLite-Bibliothek hinzufügt, aus welchen Dateien eine Datenbank besteht, welche Verbindungseinstellungen angewendet werden
und warum, wie Leser und Schreiber nebeneinander bestehen und wo SQLite nicht mehr passt. Quelle:
[9.2 SQLite 3](../../installation/09-datenbanken.md#92-sqlite-3-eine-produktionsdatenbank-in-einer-datei) und der Treiber
`lib/ezdb/classes/ezsqlite3db.php`.

## Inhalt

1. [Warum SQLite der Standard ist](#warum-sqlite-der-standard-ist)
2. [Wo die Datenbank liegt](#wo-die-datenbank-liegt)
3. [Was der Treiber bei jeder Verbindung tut](#was-der-treiber-bei-jeder-verbindung-tut)
4. [Die PRAGMA-Standards](#die-pragma-standards)
5. [WAL: Leser und Schreiber nebeneinander](#wal-leser-und-schreiber-nebeneinander)
6. [Die SQL-Kompatibilitätsschicht](#die-sql-kompatibilitätsschicht)
7. [Wo SQLite passt](#wo-sqlite-passt)
8. [Checkliste](#checkliste)

## Warum SQLite der Standard ist

Der Einrichtungsassistent wählt es vor (`settings/setup.ini [DatabaseSettings] DefaultType=sqlite3`), `exp:install`
verwendet es, wenn kein `--db` angegeben ist, und die Kickstarter-Vorlage nennt es zuerst. Es braucht keinen
Serverprozess, kein Netzwerk und keine Konten. Mit dem Treiber, den Exponential mitliefert, ist es eine vollwertige
Produktionsdatenbank für eine Website, die von einer Maschine ausgeliefert wird: Die meisten Seitenaufrufe werden von Caches
beantwortet, bevor sie sie erreichen, und gleichzeitige Veröffentlichungen reihen sich ein, statt zu scheitern.

## Wo die Datenbank liegt

Die Einstellung `Database` ist ein Dateiname:

- ein einfacher Name (`exponential.db`) liegt in `var/storage/sqlite3/` (die Konstante `eZSQLite3DB::STORAGE_DIRECTORY`);
- ein Name, der mit `/` beginnt, ist ein absoluter Pfad;
- `:memory:` ist eine Wegwerf-Datenbank (nur für Tests).

`exp:install` nennt die Datei `exponential.db`, der Einrichtungsassistent schlägt `sqlite.db` vor. Ein fehlendes
Verzeichnis wird mit Modus `0775` angelegt; eine fehlende Datei wird leer angelegt. Ein falscher Wert für `Database`
scheitert daher nicht beim Verbinden: Er öffnet eine neue, leere Datenbank, und die Website meldet fehlende Tabellen.

```bash
php bin/php/console exp:ini get site.ini/DatabaseSettings/Database --allow-root-user
ls -l var/storage/sqlite3/
```

Eine Datenbank besteht aus vier Dateien: der Datenbank, `-wal`, `-shm` und dem `.writer-lock` des Treibers
([SQLite-Dateien, Eigentümer und Rechte](sqlite-dateien-eigentuemer-und-rechte.md)).

## Was der Treiber bei jeder Verbindung tut

1. **Er löst die Datei auf** wie oben. Eine Datei, die sich (nach `ConnectRetries` weiteren Versuchen) nicht öffnen lässt,
   löst `eZDBNoConnectionException` mit dem vollständigen Pfad aus.
2. **Er registriert Funktionen**, die SQLite fehlen: `md5()` und die Aggregate `BIT_OR` und `BIT_AND`, die die
   Inhalts-Engine verwendet, um Sprachmasken zusammenzufassen.
3. **Er wendet die PRAGMAs an**, das Busy-Timeout zuerst.
4. **Er schaltet auf WAL um**, nur wenn die Datei noch nicht im WAL-Modus ist (der Modus wird in der Datei gespeichert).

## Die PRAGMA-Standards

| PRAGMA | Standard des Treibers | Eigener Standard von SQLite | Warum |
|---|---|---|---|
| `synchronous` | `NORMAL` | `FULL` | Mit WAL weiterhin sicher gegen Beschädigung bei einem Absturz; nur die letzten Commits vor einem Stromausfall können verloren gehen |
| `cache_size` | `-65536` (64 MB) | etwa 2 MB | Der Seiten-Cache jeder Verbindung |
| `mmap_size` | `268435456` (256 MB) | `0` | Lesen über speicherabgebildete Ein-/Ausgabe, geteilt über den Dateicache des Betriebssystems |
| `temp_store` | `MEMORY` | Datei | Sortierungen und temporäre Tabellen bleiben im Speicher |
| `busy_timeout` | `5000` ms | `0` | Eine Anweisung, die auf eine Sperre trifft, wartet, statt sofort zu scheitern |

Ändern oder ergänzen Sie PRAGMAs mit `SQLitePragmas[]`, ein `name=value` je Eintrag. Der Treiber akzeptiert im Namen nur
Buchstaben und Unterstriche und im Wert Buchstaben, Ziffern, `_` und `-` und überspringt alles andere. WAL gewinnt immer:
Ein Eintrag `journal_mode` wird auf WAL zurückgestellt.

```ini
[DatabaseSettings]
SQLitePragmas[]
SQLitePragmas[]=cache_size=-16384
SQLitePragmas[]=journal_size_limit=67108864
```

`cache_size` gilt je Verbindung: Rechnen Sie mit bis zu 64 MB für jeden PHP-FPM-Worker und jeden Velocity-Worker, der die
Datenbank geöffnet hat, oder senken Sie den Wert wie oben (16 MB).

## WAL: Leser und Schreiber nebeneinander

- **Leser blockieren den Schreiber nie, und der Schreiber blockiert Leser nie.** Seitenaufrufe laufen weiter, während ein
  Redakteur veröffentlicht.
- **Es gibt jeweils einen Schreiber.** Der Treiber lässt den nächsten Schreiber warten, bis er an der Reihe ist
  ([SQLite: die Schreibwarteschlange und Sperren](sqlite-schreibwarteschlange-und-sperren.md)).
- **Ein Checkpoint** kopiert das WAL in die Datenbank zurück, automatisch bei etwa 1000 Seiten; bei ständigem Lesen kann
  die WAL-Datei eine Weile wachsen ([SQLite-Wartung](sqlite-wartung.md)).
- **Alle Prozesse müssen auf einer Maschine laufen.** WAL verwendet gemeinsamen Speicher und funktioniert daher nicht über
  ein Netzwerkdateisystem.

## Die SQL-Kompatibilitätsschicht

Das SQL des Kernels wurde zuerst für MySQL geschrieben. Der Treiber überbrückt die Lücken: Er schreibt `UPDATE ... JOIN` in
`UPDATE ... FROM` um (SQLite 3.33 oder neuer) und akzeptiert und ignoriert MySQL-Session-Anweisungen (`SET NAMES`,
`SET FOREIGN_KEY_CHECKS`, `SET CHARACTER SET`, `SET AUTOCOMMIT`, `SET SQL_MODE`, `SET UNIQUE_CHECKS`). SQLite vergleicht
bei `=` mit Beachtung der Groß-/Kleinschreibung und ignoriert sie bei ASCII-Buchstaben in `LIKE`.

## Wo SQLite passt

Es passt zu einer Maschine, beliebig vielen Lesern und einem Redaktionsteam. In den Lasttests des Projekts skalierten
gleichzeitige Leser mit der Zahl der Prozesse, und 16 gleichzeitige Schreiber wurden ohne Fehler abgeschlossen, in der
Warteschlange. Wählen Sie einen Datenbankserver, wenn mehr als eine Maschine schreiben muss, wenn viele Redakteure den
ganzen Tag gleichzeitig große Änderungen veröffentlichen oder wenn Sie Replikation oder Benutzer und Rechte auf
Datenbankebene brauchen ([Eine Engine wählen](eine-engine-waehlen.md)).

## Checkliste

- [ ] Die SQLite-Bibliothek von PHP ist 3.33 oder neuer (`php --ri sqlite3`).
- [ ] `PRAGMA journal_mode` gibt `wal` aus.
- [ ] Jeder Prozess, der die Website verwendet, kann das Verzeichnis und die Dateien schreiben.
- [ ] Die Datenbank ist nicht über HTTP erreichbar.
- [ ] Sicherungen verwenden `.backup` oder `VACUUM INTO`, nie eine einfache Kopie einer beschäftigten Datei
      ([Sicherungen je Engine](sicherungen-je-engine.md)).
- [ ] Der Speicher ist für `cache_size` je Worker bemessen.

## Verwandte Seiten

- [SQLite: die Schreibwarteschlange und Sperren](sqlite-schreibwarteschlange-und-sperren.md)
- [SQLite-Wartung: ANALYZE, VACUUM und die WAL-Datei](sqlite-wartung.md)
- [SQLite für Hoster](../hosting/grundlagen/sqlite-fuer-hoster.md)
- [SQLite-Datenbankunterstützung](../../funktionen/6.0/sqlite-datenbank.md), [Der SQLite3-Datenbanktreiber](../../spezifikationen/6.0/sqlite3-datenbanktreiber.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
