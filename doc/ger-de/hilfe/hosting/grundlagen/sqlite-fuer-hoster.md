---
title: SQLite für Hoster
audience: hosting, admin
category: hosting
order: 70
summary: SQLite 3, die Standarddatenbank von Exponential, aus Sicht eines Hosters - was der Treiber hinzufügt, die vier Dateien und ihre Berechtigungen, die gemeinsamen Einstellungen, Backups und Wiederherstellungen ohne Kopieren einer Datei in Benutzung, Überwachung und wann eine Website ihr entwachsen ist.
keywords: SQLite, SQLite 3, sqlite3, Standarddatenbank, WAL, Schreibsperre, SQLiteTransactionWait, SQLitePragmas, var/storage/sqlite3, Backup, VACUUM INTO, integrity_check, Berechtigungen, umask, setgid, Shared Hosting, eine Maschine, Grenzen, default database, writer lock, permissions, shared hosting, one machine, limits
type: help
---
# SQLite für Hoster

Diese Seite ist für Systemadministratoren und Support-Mitarbeitende bei einem Hosting-Anbieter, deren Kunden Exponential
auf SQLite 3 betreiben, der Standarddatenbank. Sie beantwortet: was SQLite vom Server braucht, wie Sie seine Dateien so
einrichten, dass jeder Prozess sie schreiben kann, wie Sie sicher sichern und wiederherstellen, worauf Sie achten müssen
und wann eine Website auf einen Datenbankserver umziehen sollte. Alles hier stammt aus
[Kapitel 9.2 des Installationsbuchs](../../../installation/09-datenbanken.md#92-sqlite-3-eine-produktionsdatenbank-in-einer-datei),
das alle Details enthält.

## Inhalt

1. [Warum SQLite für einen Hoster zählt](#warum-sqlite-für-einen-hoster-zählt)
2. [Anforderungen](#anforderungen)
3. [Was der Treiber hinzufügt](#was-der-treiber-hinzufügt)
4. [Dateien und Berechtigungen](#dateien-und-berechtigungen)
5. [Einstellungen](#einstellungen)
6. [Backup und Wiederherstellung](#backup-und-wiederherstellung)
7. [Überwachung](#überwachung)
8. [Wann eine Website SQLite entwachsen ist](#wann-eine-website-sqlite-entwachsen-ist)
9. [Checkliste](#checkliste)
10. [Verwandte Seiten](#verwandte-seiten)

## Warum SQLite für einen Hoster zählt

SQLite ist die erste in der offiziellen Reihenfolge und der Standard der Installer (`settings/setup.ini`,
`[DatabaseSettings] DefaultType=sqlite3`; `exp:install` ohne `--db`). Für einen Hoster bedeutet das:

- **Kein Datenbankserver und kein Datenbankkonto**, das bereitgestellt, abgesichert oder abgerechnet werden muss.
- **Die Datenbank gehört zu den Dateien der Website**, in `var/storage/sqlite3/`; sie zieht also mit um, wird auf die
  Quota angerechnet und ist wie der Rest der Website abgeschottet.
- **Sie ist eine vollwertige Produktionsdatenbank** für eine Website, die von einer Maschine ausgeliefert wird: Das
  Projekt beschreibt sie so, und der Treiber wurde dafür unter Last getestet
  ([Datenbankunterstützung](../../datenbankunterstuetzung.md#1-sqlite-3-der-standard)).

## Anforderungen

- Die PHP-Erweiterung `sqlite3` (`php -m | grep -i sqlite3`).
- Eine SQLite-Bibliothek der Version **3.33 oder später** in PHP (`php --ri sqlite3` gibt `SQLite Library => ...` aus).
  Der Assistent akzeptiert 3.0.1, aber der Treiber nutzt `UPDATE ... FROM`, das SQLite ab 3.33 hat.
- Ein beschreibbares Verzeichnis für die Datenbank, standardmäßig `var/storage/sqlite3/`.
- Optional, aber nützlich: das Kommandozeilenprogramm `sqlite3`, für Backups und Prüfungen.

## Was der Treiber hinzufügt

Aus [Datenbankunterstützung](../../datenbankunterstuetzung.md#1-sqlite-3-der-standard):

- **Write-Ahead Logging (WAL)**, sodass Seitenaufrufe weiterlesen, während ein Redakteur veröffentlicht.
- **Verbindungseinstellungen für einen Webserver**: `synchronous=NORMAL`, ein Seitencache von 64 MB, eine Memory Map von
  256 MB, ein Busy-Timeout von 5 Sekunden.
- **Eine Schreibwarteschlange** (seit 6.0.15): Jede Transaktion nimmt eine exklusive Sperre auf `<database>.writer-lock`
  und führt `BEGIN IMMEDIATE` aus, sodass gleichzeitige Veröffentlichungen der Reihe nach warten, bis zu
  `SQLiteTransactionWait` Sekunden (25 mitgeliefert), statt mit `database is locked` zu scheitern.

Die meisten Anfragen erreichen die Datenbank nie: Der Antwort-Cache von Velocity, der Content-View-Cache und der
Template-Block-Cache antworten zuerst ([Kapitel 9.2](../../../installation/09-datenbanken.md#was-die-leselast-trägt-die-cache-schichten)).

## Dateien und Berechtigungen

Eine SQLite-Datenbank besteht aus vier Dateien in einem Verzeichnis:

| Datei | Wer sie schreiben muss |
|---|---|
| `exponential.db` | jeder Prozess, der schreibt |
| `exponential.db-wal` | jeder Prozess, der schreibt |
| `exponential.db-shm` | jeder Prozess, auch Leser |
| `exponential.db.writer-lock` | jeder Prozess (Lesezugriff genügt für `flock()`) |

SQLite legt die Dateien `-wal` und `-shm` an und entfernt sie; das **Verzeichnis** muss daher für jeden Prozess
beschreibbar sein: das PHP des Webservers, die Worker von Velocity, Cronjobs und Konsolenbefehle. Wenn einer von ihnen
`-shm` mit dem Modus `0644` anlegt, scheitern die anderen mit `attempt to write a readonly database`. Richten Sie es
einmal ein:

```bash
chgrp -R <web group> var/storage/sqlite3
chmod 2775 var/storage/sqlite3
chmod 664 var/storage/sqlite3/*
```

und geben Sie jedem Prozess eine umask von `0002` (`UMask = 0002` in einer systemd-Unit, `umask 002` am Anfang einer
Cron-Zeile). Noch besser: Führen Sie jeden Prozess als eigenen Benutzer der Website aus
([Beschreibbare Verzeichnisse und Berechtigungen](beschreibbare-verzeichnisse-und-berechtigungen.md)).

> **Warnung**
> Verwenden Sie `bin/modfix.sh` nicht, um Berechtigungen von SQLite zu korrigieren: Es macht `var/storage` und andere
> Verzeichnisse für alle beschreibbar, und das Skript selbst warnt, dass das nicht sicher ist.

**Liefern Sie die Datenbank nie über HTTP aus.** Die mitgelieferten Rewrite-Regeln schicken `var/storage` an
`index.php`, und die Liste statischer Dateien von Velocity lässt es aus. Prüfen Sie jede Host-Konfiguration:

```bash
curl -sI https://www.example.com/var/storage/sqlite3/exponential.db | head -1
```

Alles außer `200` ist richtig.

## Einstellungen

```ini
[DatabaseSettings]
DatabaseImplementation=sqlite3
# a plain name lives in var/storage/sqlite3/; a name starting with / is an absolute path
Database=exponential.db
# seconds a transaction waits for the writers ahead of it; keep it below the web server's request timeout
SQLiteTransactionWait=25
# PRAGMAs over the driver's defaults, one name=value per line (empty keeps the defaults)
SQLitePragmas[]
```

`SQLiteTransactionWait` muss unter dem Zeitlimit der Anfrage bleiben: `requestTimeout` von Velocity beträgt 30 Sekunden,
sofern nicht geändert; unter PHP-FPM ist es das `request_terminate_timeout` des Pools. Erhöhen Sie beide gemeinsam, die
Wartezeit einige Sekunden darunter.

**Arbeitsspeicher.** Rechnen Sie mit bis zu 64 MB Seitencache pro Prozess, der die Datenbank geöffnet hat;
`SQLitePragmas[]=cache_size=-16384` senkt ihn auf 16 MB, was bei Tarifen mit vielen kleinen Websites hilft.

## Backup und Wiederherstellung

> **Warnung**
> Kopieren Sie nie eine Datenbankdatei, die in Benutzung ist. Eine Kopie, die während eines Schreibvorgangs oder ohne ihre
> Datei `-wal` erstellt wird, kann inkonsistent sein. Nutzen Sie das Online-Backup von SQLite.

```bash
sqlite3 var/storage/sqlite3/exponential.db ".backup '/backup/exponential-$(date +%F).db'"
sqlite3 /backup/exponential-2026-10-05.db "PRAGMA integrity_check"     # prints ok
```

`VACUUM INTO '/backup/<file>.db'` ist eine Alternative, die eine verdichtete Kopie schreibt. **Stellen Sie wieder her**
über die eigenen Sperren von SQLite, im Wartungsmodus:

```bash
php bin/php/maintenance.php on --message="Restoring" --allow-root-user
sqlite3 var/storage/sqlite3/exponential.db ".restore '/backup/exponential-2026-10-05.db'"
php bin/php/maintenance.php off --allow-root-user
php bin/php/ezcache.php --clear-all --allow-root-user
php bin/php/console exp:velocity cache clear --allow-root-user
```

Wenn `.restore` einen Fehler meldet, ist die Live-Datenbank unverändert; halten Sie dann jeden Prozess an, der die Datei
geöffnet haben könnte, verschieben Sie die Datenbank und ihre Dateien `-wal` und `-shm` gemeinsam zur Seite, legen Sie
das Backup an ihre Stelle, korrigieren Sie Eigentümer und Modus und starten Sie wieder
([Kapitel 9.2](../../../installation/09-datenbanken.md#sqlite-sicherung-und--wiederherstellung)). Ein vollständiges Backup enthält außerdem
`var/storage`, `settings/override` und `settings/siteaccess`.

## Überwachung

| Was | Wie | Gesund |
|---|---|---|
| Zeitüberschreitungen bei Sperren | `grep -c "database is busy" var/log/error.log` | `0`, oder selten und erklärt |
| Integrität | `sqlite3 var/storage/sqlite3/exponential.db "PRAGMA quick_check"` | `ok` |
| Journalmodus | `sqlite3 var/storage/sqlite3/exponential.db "PRAGMA journal_mode"` | `wal` |
| WAL-Größe | `ls -l var/storage/sqlite3/` | die Datei `-wal` wächst nach geschäftigen Zeiten nicht weiter |
| Speicherplatz | `df -h var/storage/sqlite3` | Platz für die doppelte Größe der Datenbank |
| Eigentum | `ls -l var/storage/sqlite3/` | alle Dateien für die Gruppe beschreibbar, eine Gruppe |

Eine große Datei `-wal`, die nach starkem Schreiben übrig bleibt, schrumpft mit `SQLitePragmas[]=journal_size_limit=67108864`
oder einem manuellen `PRAGMA wal_checkpoint(TRUNCATE)`. `VACUUM` braucht etwa die doppelte Größe der Datenbank als freien
Speicherplatz und hält die Schreibsperre; führen Sie es daher im Wartungsmodus aus
([Kapitel 9.2](../../../installation/09-datenbanken.md#analyze-vacuum-und-die-wal-datei)).

## Wann eine Website SQLite entwachsen ist

Ziehen Sie die Website auf einen Datenbankserver um (zuerst PostgreSQL), wenn:

- **mehr als eine Maschine** in die Datenbank schreiben muss: WAL braucht gemeinsamen Speicher auf einem Host, ein
  Netzwerkdateisystem ist daher kein Ersatz, und mehrere Container können keine Datenbankdatei teilen;
- **viele Redakteure** den ganzen Tag **gleichzeitig große Änderungen veröffentlichen** und die Warteschlange regelmäßig
  länger ist als `SQLiteTransactionWait`;
- der Kunde **Replikation**, einen Hot Standby oder Wiederherstellung zu einem Zeitpunkt aus der Datenbank selbst braucht;
- der Kunde **Datenbankbenutzer und Rechte** braucht.

Die Größe der Datenbank ist selten der Grund. Siehe [Die Datenbank-Engine wechseln](../migrationen/die-datenbank-engine-einer-kunden-website-wechseln.md).

## Checkliste

- [ ] Erweiterung `sqlite3`, Bibliothek 3.33 oder später, im Web-PHP und im PHP der Kommandozeile.
- [ ] `var/storage/sqlite3/` für die Gruppe beschreibbar und mit setgid, jeder Prozess in einer Gruppe mit umask `0002`.
- [ ] Die Datenbankdatei ist über HTTP nicht erreichbar.
- [ ] `SQLiteTransactionWait` unter dem Zeitlimit der Anfrage.
- [ ] Backups mit `.backup` oder `VACUUM INTO`, geprüft mit `PRAGMA integrity_check`.
- [ ] Überwachung auf `database is busy` in `var/log/error.log`.

**Öffentliche Demos auf SQLite.** Eine Demo, die alle paar Stunden neu aufgebaut wird, passt gut zu SQLite: Jeder Aufbau installiert eine frische Datenbankdatei mit `exp:install --db=sqlite` im Demomodus (`EXP_DEMO_MODE=1`), und der alte Aufbau wird mit seiner Datei entfernt. [Rezept: eine öffentliche Demo auf LiteSpeed und Plesk betreiben](../server/rezept-eine-oeffentliche-demo-auf-litespeed-und-plesk-betreiben.md).

## Verwandte Seiten

- [Datenbanken in der offiziellen Reihenfolge](datenbanken-in-der-offiziellen-reihenfolge.md)
- [Datenbankunterstützung: SQLite 3](../../datenbankunterstuetzung.md#1-sqlite-3-der-standard)
- [Kapitel 9.2: SQLite 3](../../../installation/09-datenbanken.md#92-sqlite-3-eine-produktionsdatenbank-in-einer-datei)
- [Unterstützung für SQLite-Datenbanken](../../../funktionen/6.0/sqlite-datenbank.md) und
  [SQLite-Transaktionen](../../../bc/6.0/sqlite-transaktionen.md)
- [Datenbank-Backups je Engine](../betrieb/datenbank-backups-je-engine.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
