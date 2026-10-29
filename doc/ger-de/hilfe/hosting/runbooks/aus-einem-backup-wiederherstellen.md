---
title: "Runbook: aus einem Backup wiederherstellen"
audience: hosting, admin
category: hosting
order: 50
summary: Eine Exponential-Website während eines Vorfalls aus einem Backup wiederherstellen - entscheiden, was wiederhergestellt wird, Wartungsmodus, Velocity anhalten, die Datenbank mit dem Werkzeug der Engine und die Dateien wiederherstellen, Eigentümer korrigieren, Caches leeren, Autoloads neu erzeugen, prüfen, wieder öffnen.
keywords: Runbook, Wiederherstellung, Backup, Datenverlust, Wartungsmodus, Velocity anhalten, Wiederherstellung der Datenbank, sqlite restore, pg_restore, mongorestore, mariadb, mysql, Oracle impdp, var/storage, settings, Caches, Autoloads, runbook, restore, backup, recovery, data loss, maintenance mode, database restore, caches, autoloads
type: help
---
# Runbook: aus einem Backup wiederherstellen

Für Systemadministratoren, die eine Kunden-Website aus einem Backup zurückholen müssen: nach Datenverlust, einem kaputten Update oder
einem Einbruch. Das Verfahren ist das aus [10.9 Backups und Wiederherstellung](../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung);
diese Seite ordnet es für einen Vorfall.

## Inhalt

1. [Zuerst entscheiden](#zuerst-entscheiden)
2. [Vorbereiten](#vorbereiten)
3. [Wiederherstellen](#wiederherstellen)
4. [Prüfen und wieder öffnen](#prüfen-und-wieder-öffnen)
5. [Nach dem Vorfall](#nach-dem-vorfall)

## Zuerst entscheiden

| Frage | Warum |
|---|---|
| Welches Backup? | Das neueste von vor dem Beginn des Problems; bei einem Einbruch eines von vor der ersten Änderung des Angreifers ([Kompromittierte Website](kompromittierte-website.md)). |
| Alles, oder nur die Datenbank, oder nur manche Dateien? | Eine Wiederherstellung von allem verliert alle Inhalte, die seit dem Backup veröffentlicht wurden. |
| Was geht verloren? | Das Veröffentlichte zwischen dem Backup und jetzt. Sagen Sie es dem Kunden, bevor Sie beginnen. |
| Wo? | An Ort und Stelle, oder zuerst auf einer zweiten Maschine, um das Backup zu prüfen (bevorzugt, wenn die Zeit es erlaubt). |

> **Tipp**
> Behalten Sie den beschädigten Zustand. Bevor Sie darüber wiederherstellen, erstellen Sie eine Kopie der aktuellen Datenbank und der
> Dateien; sie wird vielleicht gebraucht, um Inhalte zurückzuholen, die nach dem Backup veröffentlicht wurden, oder für die Untersuchung.

## Vorbereiten

1. Kopieren Sie den aktuellen Zustand zur Seite (Datenbank-Dump und Dateien), wie oben.
2. Wartungsmodus an, damit Besucher eine Seite 503 bekommen und niemand veröffentlicht:

   ```bash
   php bin/php/maintenance.php on --message="Restoring the site" --until=1h --allow-root-user
   ```

3. Unter Velocity halten Sie den Server an, damit kein Worker die alte Datenbank hält:

   ```bash
   php bin/php/console exp:velocity stop --allow-root-user
   ```

## Wiederherstellen

1. **Die Datenbank**, mit dem Werkzeug ihrer Engine (offizielle Reihenfolge):

   | Engine | Wiederherstellung |
   |---|---|
   | 1. SQLite 3 | `sqlite3 var/storage/sqlite3/exponential.db ".restore '/backup/db.sqlite'"`; kopieren Sie nie über eine Datei in Benutzung |
   | 2. PostgreSQL | `pg_restore -U USER -d DATABASE --clean /backup/db.dump` |
   | 3. MongoDB | `mongorestore --uri="..." --drop /backup/mongo` |
   | 4. MariaDB | `mariadb -u USER -p DATABASE < /backup/db.sql` |
   | 5. Oracle | `php bin/php/console ext:ezoracle:datapump --import=db.dmp` oder Oracles `impdp` |
   | 6. MySQL | `mysql -u USER -p DATABASE < /backup/db.sql` |

   Quellen: [10.9](../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung) und
   [Datenbankunterstützung](../../datenbankunterstuetzung.md#backup-und-wiederherstellung). `-p` lässt den Client nach dem Passwort fragen; schreiben Sie
   es nie auf die Kommandozeile.

2. **Die Dateien**: Entpacken Sie `var/storage` (und `var/<site>/storage`) und bei Bedarf `settings/override` und `settings/siteaccess`
   über die Installation.
3. **Eigentümer und Modi**: Die Dateien müssen dem Benutzer der Website gehören, wie die Engine oder der PHP-FPM-Pool es erwartet
   ([Beschreibbare Verzeichnisse und Berechtigungen](../grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md)).
4. **Caches und Autoloads**:

   ```bash
   php bin/php/ezcache.php --clear-all --allow-root-user
   php bin/php/ezpgenerateautoloads.php -e
   ```

5. **Starten Sie Velocity** und leeren Sie seinen Antwort-Cache:

   ```bash
   php bin/php/console exp:velocity start --allow-root-user
   php bin/php/console exp:velocity cache clear --apply --allow-root-user
   ```

   Unter PHP-FPM laden Sie den PHP-FPM-Dienst der Website neu.

## Prüfen und wieder öffnen

1. Öffnen Sie die Startseite und die Administration über einen Eintrag in der hosts-Datei oder mit einem Wartungsmodus, der Ihre Adresse
   durchlässt (`--allow-ip`, siehe [Wartungsmodus](../betrieb/wartungsmodus.md)).
2. Melden Sie sich an, öffnen Sie kürzlich geänderte Inhalte, laden Sie ein Testbild hoch, führen Sie eine Suche aus.
3. Prüfen Sie `var/log/error.log` auf neue Fehler: `./console exp:logs --tail=var/log/error.log --lines=50`.
4. Wartung aus:

   ```bash
   php bin/php/maintenance.php off --allow-root-user
   ```

## Nach dem Vorfall

- [ ] Sagen Sie dem Kunden, was wiederhergestellt wurde, von welchem Zeitpunkt und was erneut veröffentlicht werden muss.
- [ ] Wenn der Suchindex unvollständig aussieht, bauen Sie ihn neu auf (`php bin/php/updatesearchindex.php`; siehe [Such-Backends](../grundlagen/such-backends.md)).
- [ ] Halten Sie fest, wie lange die Wiederherstellung gedauert hat: Das ist Ihre echte Wiederherstellungszeit für das SLA ([SLA-Optionen](../geschaeft/sla-optionen-fuer-exponential-hosting.md)).
- [ ] Bewahren Sie die Kopie des beschädigten Zustands so lange auf, wie Ihre Richtlinie sagt, und löschen Sie sie dann.

## Verwandte Seiten

- [Wiederherstellung und Wiederherstellungstests](../betrieb/wiederherstellung-und-wiederherstellungstests.md)
- [Datenbank-Backups je Engine](../betrieb/datenbank-backups-je-engine.md)
- [Kompromittierte Website](kompromittierte-website.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
