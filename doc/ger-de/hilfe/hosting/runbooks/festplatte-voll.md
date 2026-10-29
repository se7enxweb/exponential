---
title: "Runbook: Festplatte voll"
audience: hosting, admin
category: hosting
order: 40
summary: Was zu tun ist, wenn die Festplatte einer Exponential-Website voll läuft - herausfinden, was gewachsen ist (Speicher, Caches, Logs, Datenbank, Backups), sicher Platz schaffen mit den Befehlen für Caches und Logs, nie Speicher oder Einstellungen löschen, und eine Wiederholung verhindern.
keywords: Runbook, Festplatte voll, kein Platz mehr, Speicher, var, Cache, Logs, Log-Rotation, Bereinigung, Archive, Datenbank, WAL, Backups, Kontingent, du, df, runbook, disk full, no space left, storage, cache, logs, log rotation, prune, archives, database, backups, quota
type: help
---
# Runbook: Festplatte voll

Für die Bereitschaft, wenn ein Server oder ein Kundenkonto keinen Speicherplatz mehr hat. Symptome: Uploads scheitern, Veröffentlichen
scheitert, Seiten zeigen Fehler, Logs wachsen nicht mehr, die Datenbank verweigert Schreibvorgänge.

## Inhalt

1. [Die ersten fünf Minuten](#die-ersten-fünf-minuten)
2. [Was entfernt werden darf, und was nie](#was-entfernt-werden-darf-und-was-nie)
3. [Sicher Platz schaffen](#sicher-platz-schaffen)
4. [Nach dem Vorfall](#nach-dem-vorfall)

## Die ersten fünf Minuten

1. Bestätigen Sie es und finden Sie das Dateisystem:

   ```bash
   df -h /var/www/vhosts/<domain>
   ```

2. Finden Sie heraus, was in der Installation gewachsen ist:

   ```bash
   du -sh var/* | sort -h | tail -15
   du -sh var/storage var/cache var/log var/vc 2>/dev/null
   ```

3. Prüfen Sie außerhalb der Installation: Backups, die auf dieselbe Festplatte geschrieben werden, das Datenverzeichnis des
   Datenbankservers, Logs des Systems, Sitzungsdateien von PHP.

## Was entfernt werden darf, und was nie

| Pfad | Entfernen? | Warum |
|---|---|---|
| `var/cache`, `var/<site>/cache` | ja, über den Cache-Befehl | Caches brauchen kein Backup: Sie werden neu erzeugt ([10.9](../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung)) |
| Rotierte Log-Archive (`*.log.1` ... und komprimierte Archive) | ja, über `exp:logs --prune` | sie sind alte Kopien |
| `var/log/*.log` (die aktuellen Logs) | leeren Sie sie über `exp:logs --truncate`, nicht durch Löschen | behält die Dateien und ihre Berechtigungen |
| `var/storage`, `var/<site>/storage` | **nie** | Uploads und Bilder: Kundendaten |
| `settings/` | **nie** | die Konfiguration der Installation |
| `var/storage/sqlite3/` | **nie** von Hand | die SQLite-Datenbank und ihre Dateien `-wal` und `-shm` |
| Alte Backups auf derselben Festplatte | nach Ihrer Aufbewahrungsrichtlinie | und verlegen Sie künftige Backups von der Maschine |

## Sicher Platz schaffen

**Caches.** Leeren Sie sie mit dem Cache-Befehl; er kennt die Pfade jeder Website:

```bash
php bin/php/ezcache.php --clear-all --allow-root-user
```

Seiten sind langsamer, bis die Caches wieder warm sind; wärmen Sie sie mit `php bin/php/warm.php --allow-root-user` vor, wenn wieder
Platz da ist.

**Log-Archive.** Ansehen, planen, dann bereinigen:

```bash
./console exp:logs --archives                 # each log with its archives and their sizes
./console exp:logs --prune --dry-run          # the archives beyond the site.ini [FileSettings] limits
./console exp:logs --apply --prune                    # remove them
./console exp:logs --apply --compress                 # compress the archives from .2 on
```

**Ein Log, das schnell wächst.** Finden Sie heraus, warum, bevor Sie es leeren; ein Log, das um Megabyte pro Minute wächst, ist ein
Symptom. `./console exp:logs --list` zeigt Zeilen pro Minute je Datei. Dann:

```bash
./console exp:logs --apply --truncate=debug           # rotate it (the content stays in .1)
```

**Die Logs von Velocity** liegen unter `var/vc/qbix/log` und rotieren mit einem Datum im Namen; alte können nach Ihrer Log-Richtlinie
archiviert oder entfernt werden ([Log-Viewer und Log-Rotation](../betrieb/log-viewer-und-log-rotation.md)).

**SQLite.** Eine große Datei `-wal` schrumpft bei einem Checkpoint; `VACUUM` gewinnt nach großen Löschungen Platz zurück, braucht aber selbst
freien Platz. Lesen Sie [ANALYZE, VACUUM und die WAL-Datei](../../../installation/09-datenbanken.md#analyze-vacuum-und-die-wal-datei), bevor Sie
etwas ausführen.

> **Warnung**
> Führen Sie während eines Vorfalls kein `rm -rf` von Hand auf Verzeichnissen unter `var/` aus. Ein falscher Pfad entfernt Uploads von
> Kunden, und ein Cache-Verzeichnis, das entfernt wird, während Worker laufen, kann Dateien mit dem falschen Eigentümer hinterlassen.
> Nutzen Sie die Befehle oben.

## Nach dem Vorfall

- [ ] Prüfen Sie, dass die Website funktioniert: hochladen, veröffentlichen, anmelden.
- [ ] Prüfen Sie die Logs, die während des Vorfalls aufgehört haben; etwas ist vielleicht still gescheitert (ein Cronjob, ein Backup).
- [ ] Richten Sie einen Alarm für die Festplatte unterhalb des erreichten Stands ein ([Überblick über die Überwachung](../betrieb/ueberblick-ueber-die-ueberwachung.md)).
- [ ] Prüfen Sie Log-Rotation und Ort der Backups ([Log-Viewer und Log-Rotation](../betrieb/log-viewer-und-log-rotation.md),
      [Backup-Zeitpläne und Aufbewahrung](../betrieb/backup-zeitplaene-und-aufbewahrung.md)).
- [ ] Wenn Kundendaten über den Tarif hinaus gewachsen sind, sagen Sie es dem Kunden ([Abrechnungs- und Messoptionen](../geschaeft/abrechnungs-und-messoptionen.md)).

## Verwandte Seiten

- [Aufbau des Speichers](../betrieb/aufbau-des-speichers.md)
- [Langsame Website](langsame-website.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
