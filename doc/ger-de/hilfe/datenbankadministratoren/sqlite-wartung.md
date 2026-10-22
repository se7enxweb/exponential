---
title: "SQLite-Wartung: ANALYZE, VACUUM und die WAL-Datei"
audience: dba
category: database-administration
order: 10
summary: Die routinemäßige Wartung einer SQLite-Datenbank von Exponential - Statistiken mit ANALYZE und PRAGMA optimize, Verdichtung mit VACUUM im Wartungsmodus, die WAL-Datei mit journal_size_limit und einem TRUNCATE-Checkpoint im Griff halten, und Integritätsprüfungen - mit den Befehlen und ihrer erwarteten Ausgabe.
keywords: SQLite, Wartung, ANALYZE, PRAGMA optimize, VACUUM, VACUUM INTO, WAL, wal_checkpoint, TRUNCATE, journal_size_limit, integrity_check, quick_check, journal_mode, Speicherplatz, Cron, maintenance, disk space, cron
type: help
---
# SQLite-Wartung: ANALYZE, VACUUM und die WAL-Datei

Diese Seite richtet sich an Datenbankadministratoren, die eine Exponential-Website auf SQLite betreuen. Sie beantwortet:
welche Wartung SQLite braucht, wann jede Aufgabe läuft, wie Sie sie ausführen, ohne Redakteure zu stören, und wie eine
gesunde Ausgabe aussieht. Quelle: [ANALYZE, VACUUM und die WAL-Datei](../../installation/09-datenbanken.md#analyze-vacuum-und-die-wal-datei)
und [Eine SQLite-Website überwachen](../../installation/09-datenbanken.md#überwachung-einer-sqlite-site). Das Kommandozeilenprogramm
`sqlite3` ist für die Website optional, für diese Aufgaben aber nötig (`sqlite3 --version`).

## Inhalt

1. [Statistiken: ANALYZE](#statistiken-analyze)
2. [Verdichtung: VACUUM](#verdichtung-vacuum)
3. [Die WAL-Datei](#die-wal-datei)
4. [Integritätsprüfungen](#integritätsprüfungen)
5. [Ein Wartungsplan](#ein-wartungsplan)
6. [Checkliste](#checkliste)

Die Beispiele verwenden `var/storage/sqlite3/exponential.db`; verwenden Sie die Datei, die Ihre Einstellung `Database` nennt.

## Statistiken: ANALYZE

Der Abfrageplaner von SQLite wählt Indizes mit Statistiken besser. Führen Sie `ANALYZE` einmal nach einer Installation oder
einem großen Import aus und erneut, nachdem die Inhalte stark gewachsen sind:

```bash
sqlite3 var/storage/sqlite3/exponential.db "ANALYZE"
```

Einer Website, die direkt nach einer Installation oder einem Import langsam ist, fehlen sie meist
([12.10](../../installation/12-fehlerbehebung.md#1210-datenbanken)). `PRAGMA optimize` führt nur die nötige Analyse aus und ist
günstig genug für einen wöchentlichen Cron-Eintrag:

```bash
sqlite3 var/storage/sqlite3/exponential.db "PRAGMA optimize"
```

## Verdichtung: VACUUM

Gelöschte Inhalte hinterlassen freie Seiten in der Datei. SQLite verwendet sie wieder, gibt sie aber nicht an das
Dateisystem zurück. `VACUUM` baut die Datei kompakt neu auf. Es braucht etwa die doppelte Größe der Datenbank an freiem
Speicherplatz und hält die Schreibsperre während seines ganzen Laufs, Schreiber reihen sich also dahinter ein und können
nach `SQLiteTransactionWait` in ein Timeout laufen. Führen Sie es im Wartungsmodus aus, und nur, wenn die Datei viel größer
geworden ist als ihr Inhalt (nachdem ein großer Teil der Website entfernt wurde):

```bash
php bin/php/maintenance.php on --allow-root-user
sqlite3 var/storage/sqlite3/exponential.db "VACUUM"
php bin/php/maintenance.php off --allow-root-user
```

`VACUUM INTO '<file>'` schreibt stattdessen eine verdichtete Kopie und ist eine gute Sicherungsmethode
([Sicherungen je Engine](sicherungen-je-engine.md)).

## Die WAL-Datei

Nach einem Checkpoint verwendet SQLite die WAL-Datei von ihrem Anfang an wieder, verkleinert sie aber nicht. Ein Schub
starker Schreibzugriffe kann eine große `-wal`-Datei hinterlassen; in den Lasttests des Projekts wuchs sie auf weit über
hundert Megabyte und wurde danach per Checkpoint zurückgeführt. Zwei Abhilfen:

- In `settings/override/site.ini.append.php` kürzt `SQLitePragmas[]=journal_size_limit=67108864` das WAL nach jedem
  Checkpoint, der es leert, auf 64 MB.
- Ein manueller Checkpoint, der die Datei kürzt:

  ```bash
  sqlite3 var/storage/sqlite3/exponential.db "PRAGMA wal_checkpoint(TRUNCATE)"
  ```

  Er gibt drei Zahlen aus; eine erste Zahl `0` bedeutet, dass der Checkpoint nicht blockiert wurde.

Löschen Sie nie die Datei `-wal` oder `-shm`, solange ein Prozess die Datenbank geöffnet hat: Sie gehören zur Datenbank.

## Integritätsprüfungen

| Prüfung | Befehl | Gesund |
|---|---|---|
| Schnell | `sqlite3 var/storage/sqlite3/exponential.db "PRAGMA quick_check"` | `ok` |
| Gründlich | `sqlite3 var/storage/sqlite3/exponential.db "PRAGMA integrity_check"` | `ok` |
| Journal-Modus | `sqlite3 var/storage/sqlite3/exponential.db "PRAGMA journal_mode"` | `wal` |
| Speicherplatz | `df -h var/storage/sqlite3` | Platz für die doppelte Datenbank |

Führen Sie dieselbe Integritätsprüfung auf jeder Sicherung aus, bevor Sie sich auf sie verlassen.

## Ein Wartungsplan

| Wann | Aufgabe |
|---|---|
| Nach Installation, großem Import, Engine-Wechsel | `ANALYZE` |
| Wöchentlich (Cron, außerhalb der Spitzenzeiten) | `PRAGMA optimize`; `PRAGMA quick_check`; die Größe von `-wal` prüfen |
| Nach dem Entfernen eines großen Teils der Website | `VACUUM` im Wartungsmodus |
| Nachdem ein Schub von Schreibzugriffen ein großes WAL hinterlassen hat | `PRAGMA wal_checkpoint(TRUNCATE)` oder `journal_size_limit` setzen |
| Täglich | Online-Sicherung und `PRAGMA integrity_check` der Sicherung |

Führen Sie Cron-Einträge mit derselben Gruppe und einer Umask von `0002` aus wie die Website, damit von ihnen angelegte
Dateien für die Gruppe schreibbar bleiben ([SQLite-Dateien, Eigentümer und Rechte](sqlite-dateien-eigentuemer-und-rechte.md)).
`exp:benchmark kernel` misst unter seinen Proben den Weg zur Datenbank und zurück; mit `--save` und `--baseline` erkennt es
eine Verlangsamung zwischen zwei Läufen ([Benchmark](../../funktionen/6.0/benchmark.md)).

## Checkliste

- [ ] `ANALYZE` wurde nach der Installation und nach großen Importen ausgeführt.
- [ ] Ein wöchentliches `PRAGMA optimize` und `quick_check` sind eingeplant.
- [ ] `VACUUM` läuft nur im Wartungsmodus, mit genug freiem Speicherplatz.
- [ ] Die Datei `-wal` wächst nach Lastspitzen nicht immer weiter.

## Verwandte Seiten

- [SQLite 3 für Datenbankadministratoren](sqlite-fuer-datenbankadministratoren.md)
- [SQLite: die Schreibwarteschlange und Sperren](sqlite-schreibwarteschlange-und-sperren.md)
- [Die Datenbank überwachen](die-datenbank-ueberwachen.md)
- [Wartungsmodus](../../anleitungen/wartungsmodus.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
