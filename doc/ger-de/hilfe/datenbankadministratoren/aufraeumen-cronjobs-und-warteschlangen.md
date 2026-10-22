---
title: "Aufräumen: Cronjobs und Warteschlangentabellen"
audience: dba
category: database-administration
order: 37
summary: Die Cronjobs, die eine Exponential-Datenbank ordentlich und ihre Warteschlangen in Bewegung halten - die Teile von settings/cronjob.ini und was jedes Skript mit der Datenbank tut, die Skripte, die kein mitgelieferter Teil ausführt (session_gc.php, trashpurge.php, old_drafts_cleanup.php und andere), die zu beobachtenden Warteschlangentabellen und wie Sie sie einplanen und prüfen.
keywords: Cronjobs, runcronjobs.php, cronjob.ini, CronjobPart, frequent, infrequent, cache_cleanup, cluster_maintenance, indexcontent, internal_drafts_cleanup, basket_cleanup, session_gc, trashpurge, old_drafts_cleanup, ezpending_actions, ezworkflow_process, cleanupversions, Warteschlange, Aufräumen, cronjobs, queue, housekeeping
type: help
---
# Aufräumen: Cronjobs und Warteschlangentabellen

Diese Seite richtet sich an Datenbankadministratoren, die wissen möchten, welche geplanten Jobs in die Exponential-Datenbank
schreiben, welche Warteschlangen sich stauen, wenn die Jobs stehen bleiben, und wie Sie sie prüfen. Das Cronjob-System selbst
ist unter [Cronjobs](../../anleitungen/cronjobs.md) beschrieben; diese Seite betrachtet es von der Seite der Datenbank. Quelle:
`settings/cronjob.ini` und die Skripte in `cronjobs/`.

## Inhalt

1. [Wie Cronjobs laufen](#wie-cronjobs-laufen)
2. [Der Standardteil](#der-standardteil)
3. [Die anderen mitgelieferten Teile](#die-anderen-mitgelieferten-teile)
4. [Skripte, die kein mitgelieferter Teil ausführt](#skripte-die-kein-mitgelieferter-teil-ausführt)
5. [Zu beobachtende Warteschlangentabellen](#zu-beobachtende-warteschlangentabellen)
6. [Skripte außerhalb des Cron-Systems](#skripte-außerhalb-des-cron-systems)
7. [Eine Crontab](#eine-crontab)
8. [Checkliste](#checkliste)

## Wie Cronjobs laufen

`php runcronjobs.php [-s <siteaccess>] [<part>]` führt die Skripte eines Teils aus; ohne Teilnamen führt es die Standardliste
in `[CronjobSettings] Scripts[]` aus. Von jedem Skript läuft jeweils nur eine Instanz (`MaxScriptExecutionTime`, standardmäßig
12 Stunden). Teile lassen sich unter **Setup > Cronjobs** pausieren ([Cronjob-Konsole](../hosting/betrieb/die-cronjob-konsole.md)).

## Der Standardteil

`[CronjobSettings] Scripts[]` in der mitgelieferten Datei:

| Skript | Wirkung auf die Datenbank |
|---|---|
| `unpublish.php` | nimmt Objekte zurück, deren Datum für das Zurückziehen erreicht ist |
| `rssimport.php` | importiert konfigurierte RSS-Feeds als Inhalte |
| `indexcontent.php` | indiziert in `ezpending_actions` eingereihte Inhalte (verzögerte Indizierung) |
| `hide.php` | blendet Objekte aus, deren Datum für das Ausblenden erreicht ist |
| `internal_drafts_cleanup.php` | entfernt alte interne Entwürfe |

## Die anderen mitgelieferten Teile

| Teil | Skripte | Hinweise |
|---|---|---|
| `frequent` | `notification.php`, `workflow.php`, `contentjobs.php`, `audit.php`, `mailbounces.php` | in `ezworkflow_process` wartende Workflows, Benachrichtigungen, Inhaltsjobs, der Audit-Index |
| `infrequent` | `basket_cleanup.php`, `linkcheck.php`, `mailpreferences.php` | entfernt alte Shop-Warenkörbe; prüft Links in `ezurl`; Aufbewahrung der E-Mail-Einstellungen |
| `cache_cleanup` | `cachecleanup.php`, `httpcache_cleanup.php`, `querycache_cleanup.php` | nur Dateien; `querycache_cleanup.php` entfernt alte Ergebnisdateien des SQL-Abfrage-Cache |
| `cluster_maintenance` | `clusterpurge.php` | entfernt abgelaufene Einträge des DFS-Clusters; in `ForbiddenParts[]`, kann also nicht aus dem Browser gestartet werden |
| `unlock` | `unlock.php` | gibt Sperren frei; ebenfalls aus dem Browser verboten |
| `contentcheck`, `contentjobs`, `audit`, `logs`, `notification`, `mailpreferences`, `mailbounces`, `cleanuprss` | je ein Job | führen einen einzelnen Job nach eigenem Zeitplan aus |

## Skripte, die kein mitgelieferter Teil ausführt

Diese Skripte existieren in `cronjobs/`, stehen aber in keinem Teil der mitgelieferten `cronjob.ini` (einige sind dort als
Kommentare aufgeführt). Fügen Sie die benötigten in `settings/override/cronjob.ini.append.php` einem Teil hinzu:

| Skript | Zweck |
|---|---|
| `session_gc.php` | entfernt abgelaufene Zeilen aus `ezsession` (nötig mit `ezpSessionHandlerDB`) |
| `trashpurge.php` | löscht Objekte im Papierkorb endgültig (alle oder so alte, wie `[TrashSettings] KeepItemsForDays` sagt) |
| `old_drafts_cleanup.php` | entfernt Entwürfe von Benutzern, die älter als die konfigurierte Altersgrenze sind |
| `subtreeexpirycleanup.php` | räumt Einträge zum Ablauf von Teilbäumen auf (seit 4.6 in der Standardliste auskommentiert) |
| `staticcache_cleanup.php` | für den statischen Cache, der ausgeschaltet bleibt |
| `updateviewcount.php` | schreibt die gepufferten Aufrufzähler in die Datenbank (`ezview_counter`) |

Lesen Sie den Kopf jedes Skripts und seinen Dokumentationslink unter **Setup > Cronjobs**, bevor Sie es einschalten.

```ini
# settings/override/cronjob.ini.append.php
[CronjobPart-infrequent]
Scripts[]=session_gc.php
```

## Zu beobachtende Warteschlangentabellen

| Tabelle | Wächst, wenn | Prüfung |
|---|---|---|
| `ezpending_actions` | `indexcontent.php` nicht läuft | `SELECT action, COUNT(*), MIN(created) FROM ezpending_actions GROUP BY action;` |
| `ezworkflow_process` | `workflow.php` nicht läuft | Zeilen wartender Prozesse |
| `ezsession` | abgelaufene Zeilen nicht entfernt werden | Zeilen mit `expiration_time` in der Vergangenheit |
| `ezbasket`, `ezproductcollection*` | `basket_cleanup.php` nicht läuft | alte Warenkörbe |
| `ezcontentobject_version` | alte Versionen nie beschnitten werden | `bin/php/cleanupversions.php` |

`MIN(created)` ist ein Unix-Zeitstempel; ein Wert, der Stunden alt ist, bedeutet auf einer Website, die veröffentlicht, dass die
Warteschlange feststeckt.

## Skripte außerhalb des Cron-Systems

| Skript | Wann |
|---|---|
| `bin/php/cleanupversions.php` | archivierte Versionen jenseits der Grenzen von `VersionManagement` entfernen |
| `bin/php/ezsessiongc.php` | abgelaufene Sessions von Hand entfernen |
| `bin/php/updatesearchindex.php --clean` | den Suchindex neu aufbauen |

## Eine Crontab

```text
*/5 * * * *  cd /path/to/installation && umask 002 && php runcronjobs.php -q frequent
15 * * * *   cd /path/to/installation && umask 002 && php runcronjobs.php -q
30 3 * * *   cd /path/to/installation && umask 002 && php runcronjobs.php -q infrequent
45 3 * * *   cd /path/to/installation && umask 002 && php runcronjobs.php -q -s <siteaccess> cache_cleanup
```

Passen Sie die Zeiten an; **Setup > Cronjobs** schlägt Zeilen für Ihre Installation vor
([Cronjobs, Zeitplanung](../../anleitungen/cronjobs.md#12-planen-mit-der-crontab)). Führen Sie Cron als Benutzer der Website
aus, mit derselben Umask wie die Website, wenn die Datenbank SQLite ist.

## Checkliste

- [ ] Der Standardteil, `frequent` und `infrequent` sind eingeplant.
- [ ] Mit Datenbank-Sessions läuft `session_gc.php` aus einem Teil.
- [ ] `ezpending_actions` und `ezworkflow_process` wachsen nicht über Tage.
- [ ] Alte Versionen werden nach Plan beschnitten.
- [ ] **Setup > Cronjobs** zeigt jüngste Läufe ohne Probleme.

## Verwandte Seiten

- [Cronjobs](../../anleitungen/cronjobs.md)
- [Cronjobs](../hosting/betrieb/cronjobs.md) (für Hosting-Unternehmen)
- [Sessions in der Datenbank](sessions-in-der-datenbank.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
