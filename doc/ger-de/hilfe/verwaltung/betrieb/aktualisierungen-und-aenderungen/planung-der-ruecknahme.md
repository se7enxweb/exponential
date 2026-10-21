---
title: Rücknahme planen
audience: government, admin, project-manager, hosting
category: public-sector
order: 100
summary: Wie Sie vor jeder Änderung an einer öffentlichen Website den Weg zurück planen - welche Änderungen sich durch erneutes Bereitstellen umkehren lassen, welche eine Wiederherstellung aus der Datensicherung brauchen, weil sich die Datenbank geändert hat, der Entscheidungspunkt im Wartungsfenster und was Exponential nicht für Sie rückgängig macht.
keywords: Rücknahme, zurücknehmen, rückgängig machen, Wiederherstellung, Datensicherung, erneut bereitstellen, vorherige Version, Datenbankaktualisierung, Entscheidungspunkt, Wartungsfenster, Änderungsplan, Rückfallebene, rollback, roll back, back out, restore, backup, redeploy, previous version, database update, decision point, maintenance window, change plan, fallback
type: help
---
# Rücknahme planen

Diese Seite richtet sich an Administratoren und Change-Manager, die vor einer Änderung angeben müssen, wie sie sie
rückgängig machen würden. Änderungsbeiräte fragen üblicherweise danach. Exponential hat keine Schaltfläche
„Aktualisierung rückgängig machen“; der Weg zurück hängt davon ab, was die Änderung berührt hat.

## Inhalt
1. [Um welche Art von Änderung handelt es sich?](#um-welche-art-von-änderung-handelt-es-sich)
2. [Die Datensicherung ist die Rücknahme](#die-datensicherung-ist-die-rücknahme)
3. [Der Entscheidungspunkt](#der-entscheidungspunkt)
4. [Schritt für Schritt zurücknehmen](#schritt-für-schritt-zurücknehmen)
5. [Was nicht rückgängig gemacht wird](#was-nicht-rückgängig-gemacht-wird)
6. [Verwandte Seiten](#verwandte-seiten)

## Um welche Art von Änderung handelt es sich?

| Änderung | Weg zurück |
|---|---|
| Template, Stylesheet | den vorherigen Commit erneut bereitstellen; Template-Caches und den Antwort-Cache von Velocity leeren |
| Einstellungen (INI) | die vorherigen Einstellungen erneut bereitstellen; den INI-Cache leeren (`--clear-tag=ini`); PHP neu laden |
| PHP-Code, Erweiterung ohne Datenbankänderungen | den vorherigen Commit und die vorherige `composer.lock` erneut bereitstellen; Autoloads neu erzeugen; `exp:velocity deploy` |
| Erweiterung mit neuen Tabellen | wie oben; die neuen Tabellen bleiben, sofern sie nicht gelöscht werden |
| Aktualisierung von Exponential mit Datenbankaktualisierung | **die Datensicherung** von Datenbank und Dateien wiederherstellen, die unmittelbar davor erstellt wurde |
| Rollen, Workflows, Sektionen | von Hand zurückändern; das Prüfprotokoll zeigt für viele davon die alten Werte |
| Inhalte | Versionsseite: **Neuen Entwurf machen** aus der alten Version; aus dem Papierkorb wiederherstellen |

## Die Datensicherung ist die Rücknahme

Der Leitfaden zum Aktualisieren ist eindeutig: Wenn eine SQL-Datei auf halbem Weg fehlgeschlagen ist, „stellen Sie
die Datensicherung aus Schritt 1 wieder her und wenden Sie die Datei erneut von Anfang an; reparieren Sie niemals von
Hand und führen Sie sie dann erneut aus“
([Aktualisieren, „Wenn etwas schiefgeht“](../../../../anleitungen/aktualisieren.md#wenn-etwas-schiefgeht)). Daher gilt für jede
Änderung, die die Datenbank berührt:

1. Erstellen Sie die Datensicherung innerhalb des Wartungsfensters, nachdem der Wartungsmodus eingeschaltet ist, damit
   Datenbank und Dateien zusammenpassen ([Was zu sichern ist](../datensicherung-und-wiederherstellung/was-gesichert-werden-muss.md)).
2. Wissen Sie, wie lange eine Wiederherstellung dauert: Sie haben es bei Ihrem letzten Wiederherstellungstest gemessen
   ([Wiederherstellungstests](../datensicherung-und-wiederherstellung/wiederherstellungstests.md)).
3. Halten Sie den Code des vorherigen Releases (den vorherigen Commit oder das vorherige Release-Verzeichnis) bereit.

## Der Entscheidungspunkt

Schreiben Sie in den Plan: „Wenn die Prüfungen in Schritt N fehlschlagen und bis zum Zeitpunkt T nicht behoben werden
können, nehmen wir die Änderung zurück.“ T lässt genug Zeit für die Wiederherstellung, bevor das Wartungsfenster
endet. Die entscheidende Person ist im Änderungsdatensatz benannt.

## Schritt für Schritt zurücknehmen

1. Die Website ist noch im Wartungsmodus (`php bin/php/maintenance.php status --allow-root-user`).
2. Spielen Sie den vorherigen Code zurück (Bereitstellung des vorherigen Commits oder das vorherige Release-Verzeichnis).
3. Stellen Sie die Datenbank mit dem Werkzeug der Engine wieder her und die Dateien (`var/storage`, Einstellungen),
   falls sie sich geändert haben ([Datensicherungen je Datenbank-Engine](../datensicherung-und-wiederherstellung/datensicherungen-je-datenbank-engine.md)).
4. Erzeugen Sie die Autoloads neu und leeren Sie die Caches:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ./console exp:velocity deploy --allow-root-user
   ```

5. Prüfen Sie von einer zugelassenen Adresse aus; **Setup > Systeminformationen** zeigt die alte Version.
6. Schalten Sie die Wartung aus; halten Sie die Rücknahme und den Grund fest.

## Was nicht rückgängig gemacht wird

- Inhalte, die zwischen der Änderung und der Rücknahme veröffentlicht, E-Mails, die versendet, und Arbeit, die von
  Cronjobs erledigt wurde. Die Wiederherstellung einer Datensicherung verliert Inhalte, die danach bearbeitet wurden;
  deshalb hält das Wartungsfenster die Redaktion fern.
- Die Dateien des Prüfprotokolls aus dem Wartungsfenster: Sie liegen in `var/<site>/log/audit/`; bewahren Sie sie auf
  und überschreiben Sie sie nicht mit der wiederhergestellten Kopie
  ([Archive des Prüfprotokolls und Datensicherungen](../datensicherung-und-wiederherstellung/audit-trail-archive-und-datensicherungen.md)).
- Ein veröffentlichtes Release lässt sich nicht „zurückziehen“; die Korrektur ist die nächste Version.

## Verwandte Seiten
- [Rücknahme einer fehlgeschlagenen Bereitstellung](../vorfaelle/fehlerhafte-bereitstellung-zuruecknehmen.md)
- [Wartungsfenster](wartungsfenster.md)
- [Aus einer Datensicherung wiederherstellen (Hosting-Runbook)](../../../hosting/runbooks/aus-einem-backup-wiederherstellen.md)
- [Plan zur Notfallwiederherstellung](../datensicherung-und-wiederherstellung/notfallwiederherstellungsplan.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
