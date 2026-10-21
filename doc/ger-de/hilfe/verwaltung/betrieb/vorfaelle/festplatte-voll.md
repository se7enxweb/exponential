---
title: Festplatte voll
audience: government, admin, hosting
category: public-sector
order: 130
summary: Runbook für einen Server, dessen Festplatte unter einer öffentlichen Website vollgelaufen ist - herausfinden, was gewachsen ist, Platz nur mit den sicheren Befehlen schaffen (Caches, Log-Archive), Uploads, Einstellungen und die Datenbank nie von Hand anfassen und prüfen, was der Audit-Trail in der Zwischenzeit getan hat.
keywords: Festplatte voll, kein Speicherplatz, df, du, Caches, Log-Archive, exp:logs prune, ezcache, var/storage, SQLite WAL, Audit OnWriteFailure, Zustand der Logs, DiskBudget, Vorfall, disk full, no space left, log archives, Log health, incident
type: help
---
# Festplatte voll

Dieses Runbook richtet sich an die Administration, wenn die Festplatte unter einer öffentlichen Website voll ist. Eine
volle Festplatte verursacht die meisten Symptome einer „nicht erreichbaren Website“. Das ausführliche Vorgehen
beschreibt [Festplatte voll (Hosting-Runbook)](../../../hosting/runbooks/festplatte-voll.md); diese Seite ergänzt die
Prüfungen, die eine öffentliche Stelle danach braucht.

## Inhalt
1. [Herausfinden, was gewachsen ist](#herausfinden-was-gewachsen-ist)
2. [Sicher Platz schaffen](#sicher-platz-schaffen)
3. [Was Sie nie entfernen dürfen](#was-sie-nie-entfernen-dürfen)
4. [Der Audit-Trail, während die Festplatte voll war](#der-audit-trail-während-die-festplatte-voll-war)
5. [Vorbeugen](#vorbeugen)
6. [Verwandte Seiten](#verwandte-seiten)

## Herausfinden, was gewachsen ist

```bash
df -h
du -sh var/* | sort -h | tail -15
./console exp:logs --list
```

Sehen Sie auch außerhalb der Installation nach: Datensicherungen, die auf dieselbe Festplatte geschrieben werden, das
Datenverzeichnis der Datenbank, Systemlogs.

## Sicher Platz schaffen

```bash
php bin/php/ezcache.php --clear-all --allow-root-user   # caches are regenerated
./console exp:logs --archives                           # see the log archives
./console exp:logs --prune --dry-run                    # what pruning would remove
./console exp:logs --prune
./console exp:logs --compress
```

Ein Log, das um Megabytes pro Minute wächst, ist ein Symptom: Finden Sie heraus, warum, bevor Sie es mit
`./console exp:logs --truncate=<key>` leeren (der Inhalt bleibt im Archiv `.1` erhalten). Wärmen Sie die Caches wieder
vor, sobald Platz ist: `php bin/php/warm.php --allow-root-user`.

## Was Sie nie entfernen dürfen

| Pfad | Warum |
|---|---|
| `var/storage`, `var/<site>/storage` | Uploads und Bilder: die Inhalte der Organisation |
| `settings/` | Konfiguration |
| `var/storage/sqlite3/` | die SQLite-Datenbank mit ihren Dateien `-wal` und `-shm` |
| `var/<site>/log/audit/` | der Audit-Trail: Beweismittel; archivieren Sie ihn, löschen Sie ihn nicht |

Führen Sie während eines Vorfalls kein `rm -rf` unter `var/` von Hand aus; verwenden Sie die oben genannten Befehle.

## Der Audit-Trail, während die Festplatte voll war

Mit `[AuditSettings] OnWriteFailure=continue` (Standard) lief die Website weiter, und nicht geschriebene Einträge wurden
in `error.log` als `AUDIT-UNWRITTEN` vermerkt; das Audit-Dashboard warnt. Mit `refuse` wurden sicherheitsrelevante
Aktionen verweigert (`AUDIT-REFUSED`)
([Audit-Trail, „When the audit cannot write“](../../../../../eng-us/features/6.0/audit-trail.md#when-the-audit-cannot-write-onwritefailure)).
Danach:

```bash
./console exp:logs --search=AUDIT-UNWRITTEN --file=var/log/error.log
./console exp:audit verify --allow-root-user
```

Halten Sie die Lücke im Audit-Trail in Ihren Notizen zum Vorfall fest: Prüfende werden danach fragen.

## Vorbeugen

- **Zustand der Logs** warnt ab 80 % von `[HealthSettings] DiskBudget` für die Logs; **Setup > Systeminformationen**
  warnt unter 5 GB oder 10 % freiem Platz und meldet einen Fehler unter 1 GB oder 5 %
  ([Systeminformationen und Zustandsprüfungen](../ueberwachung/systeminformationen-und-zustandspruefungen.md)).
- Aufbewahrung je Log festgelegt ([Aufbewahrung und Rotation von Logs](../ueberwachung/aufbewahrung-und-rotation-von-protokollen.md)).
- Datensicherungen auf eine andere Festplatte oder ein anderes System geschrieben
  ([Externe und verschlüsselte Kopien](../datensicherung-und-wiederherstellung/externe-und-verschluesselte-kopien.md)).
- Festplattenwarnungen Ihres Überwachungssystems auf jedem Datenträger.

## Verwandte Seiten
- [Website nicht erreichbar](website-nicht-erreichbar.md)
- [Logs rotieren und bereinigen](../../../../../eng-us/help/government/tasks/operations/rotate-and-prune-logs.md)
- [Den Zustand der Logs prüfen](../../../../../eng-us/help/government/tasks/operations/check-log-health.md)
- [Speicheraufteilung (Hosting)](../../../hosting/betrieb/aufbau-des-speichers.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
