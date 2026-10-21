---
title: Auf eine Standby-Datenbank umschalten
audience: government, admin, hosting
category: public-sector
order: 280
summary: Schritt für Schritt - wenn die primäre Datenbank verloren ist und das Datenbankteam einen Standby hochgestuft hat, Exponential mit einer Änderung an den Einstellungen darauf verweisen, den INI-Cache leeren, neu laden und prüfen; Exponential hat kein eigenes automatisches Failover.
keywords: Failover, Standby-Datenbank, hochstufen, Hochverfügbarkeit, DatabaseSettings Server, Datenbank ausgefallen, Notfallwiederherstellung, PostgreSQL, MariaDB, MySQL, manuelles Failover, failover, standby database, promote, high availability, database down, disaster recovery, manual failover
type: help
---
# Auf eine Standby-Datenbank umschalten

Diese Aufgabe ist für Administratoren während eines Datenbankausfalls, wenn ein Standby vorhanden ist. **Exponential
hat kein automatisches Failover der Datenbank** für PostgreSQL, MariaDB oder MySQL: Der Standby wird mit den Werkzeugen
des Datenbankherstellers hochgestuft, und Exponential wird dann darauf verwiesen. Hintergrund:
[Hochverfügbarkeit der Datenbank](../../../../../eng-us/help/government/operations/availability/database-high-availability.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Danach](#danach)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die Leitung des Vorfalls hat das Umschalten beschlossen
  ([Datenbank nicht erreichbar](../../../../../eng-us/help/government/operations/incidents/database-unreachable.md)).
- Das Datenbankteam hat den Standby hochgestuft und Ihnen seinen Hostnamen genannt; der Datenbankbenutzer der Site
  existiert dort mit denselben Rechten.
- Wissen Sie, wie weit der Standby zurücklag: Inhalte, die nach diesem Punkt bearbeitet wurden, sind verloren.

## Schritte

1. Wartungsmodus, damit während des Umschaltens nichts schreibt:

   ```bash
   php bin/php/maintenance.php on --message="This service is temporarily unavailable." --allow-admin --allow-root-user
   ```

2. Verweisen Sie die Site in `settings/override/site.ini.append.php` auf den neuen Primärserver:

   ```ini
   [DatabaseSettings]
   Server=db-standby.internal.example.gov
   ```

   Wenn Replikate konfiguriert sind (`UseSlaveServer`), prüfen Sie, ob sie dem neuen Primärserver folgen, oder schalten
   Sie die Aufteilung der Lesezugriffe aus. Mit dem DFS-Cluster braucht die Metadatenbank des Clusters
   (`file.ini [eZDFSClusteringSettings]`) dieselbe Aufmerksamkeit.
3. Leeren Sie den INI-Cache und lassen Sie PHP ihn lesen:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ./console exp:velocity deploy --allow-root-user
   ```

   Auf mehreren Webservern führen Sie die Schritte 2 und 3 auf jedem aus oder rollen die Einstellungen auf alle aus.

## Prüfen, ob es funktioniert hat

- Von einer erlaubten Adresse aus: Die Administration lädt, eine Seite wird aus der Datenbank dargestellt, eine
  Testveröffentlichung funktioniert.
- `./console exp:logs --tail=var/log/error.log --lines=20` zeigt keine Verbindungsfehler.
- Schalten Sie die Wartung aus und leeren Sie die Seiten-Caches ([Die Site wieder online bringen](die-site-wieder-online-bringen.md)).

## Danach

- Sagen Sie den Verantwortlichen für Inhalte, welcher Zeitraum verloren sein kann, und bitten Sie sie, die jüngsten
  Änderungen zu prüfen.
- Bauen Sie mit dem Datenbankteam einen neuen Standby auf; die Änderung an den Einstellungen kommt in die
  Versionsverwaltung.
- Halten Sie das Umschalten und seine Dauer für Ihre Berichte zur Betriebskontinuität fest.

## Verwandte Seiten
- [Ein Lese-Replikat hinzufügen](ein-lese-replikat-hinzufuegen.md)
- [Plan für die Notfallwiederherstellung](../../../../../eng-us/help/government/operations/backups-and-recovery/disaster-recovery-plan.md)
- [Datenbanken in der offiziellen Reihenfolge (Hosting)](../../../hosting/grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md)
- [PostgreSQL für Hoster](../../../hosting/grundlagen/postgresql-fuer-hoster.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
