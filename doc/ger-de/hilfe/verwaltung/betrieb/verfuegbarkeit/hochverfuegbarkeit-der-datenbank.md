---
title: Hochverfügbarkeit der Datenbank
audience: government, hosting, admin
category: public-sector
order: 50
summary: Für Datenbank- und Infrastrukturteams im öffentlichen Sektor. Was Exponential selbst für die Datenbankverfügbarkeit bietet (Verbindungswiederholungen, Lese-Replikate unter MySQL und MariaDB, Failover-Einstellungen für Oracle), was der eigenen Replikation der Datenbank überlassen bleibt und wie Sie eine Website auf eine Bereitschaftsdatenbank umschalten.
keywords: Hochverfügbarkeit der Datenbank, Replikation, Bereitschaftsdatenbank, Ausfallumschaltung, zeitpunktgenaue Wiederherstellung, PostgreSQL, MariaDB, MySQL, Oracle, Failover, ConnectRetries, UseSlaveServer, Grenzen von SQLite, Kontinuität, database high availability, replication, standby, failover, point-in-time recovery, SQLite limits, continuity
type: help
---
# Hochverfügbarkeit der Datenbank

Diese Seite richtet sich an Datenbankadministratorinnen und -administratoren sowie Infrastrukturteams in öffentlichen
Stellen, die die Datenbank einer Exponential-Website verfügbar halten müssen, wenn eine Datenbankmaschine ausfällt.
Sie beantwortet, was Exponential selbst leistet, was es der Datenbank-Engine überlässt und was das Umschalten auf eine
Bereitschaftsdatenbank umfasst. Die Referenzen sind [9 Datenbanken](../../../../installation/09-datenbanken.md) und
[Datenbankunterstützung](../../../datenbankunterstuetzung.md).

## Inhalt

1. [Was Exponential leistet und was nicht](#was-exponential-leistet-und-was-nicht)
2. [Je Engine](#je-engine)
3. [Auf eine Bereitschaftsdatenbank umschalten](#auf-eine-bereitschaftsdatenbank-umschalten)
4. [Es testen](#es-testen)
5. [Checkliste](#checkliste)

## Was Exponential leistet und was nicht

Exponential verbindet sich je Website mit einem Datenbankserver, der in `site.ini [DatabaseSettings] Server` benannt
ist. Es bietet:

| Funktion | Einstellung | Engines |
|---|---|---|
| Wiederholungen, wenn die erste Verbindung scheitert | `[DatabaseSettings] ConnectRetries` (ausgeliefert `0`) | MySQL, PostgreSQL, SQLite |
| Lesezugriffe an Replikate | `UseSlaveServer` mit `SlaveServerArray[]` (ausgeliefert `disabled`) | nur MySQL und MariaDB |
| Ein Deskriptor über mehrere Listener, Failover, Wiederholungen | `ezoracle.ini [ConnectionSettings] Hosts[]`, `Failover`, `RetryCount` | Oracle |

Es hat **keine eingebaute automatische Ausfallumschaltung** für PostgreSQL, MariaDB oder MySQL: Fällt der in `Server`
benannte Server aus, kann die Website ihre Datenbank nicht erreichen, bis diese Adresse wieder funktioniert. Die
Datenbankverfügbarkeit ist daher Aufgabe der Datenbankplattform: ihrer Replikation, einer virtuellen IP oder eines
DNS-Namens, die auf die Bereitschaftsdatenbank wechseln, oder eines Proxys vor der Datenbank. Wählen Sie, was Ihr
Datenbankteam bereits betreibt.

## Je Engine

| Engine | Verfügbarkeitsoptionen | Hinweise |
|---|---|---|
| 1. SQLite 3 | keine eigenen | ein Host; keine Replikation; Wiederherstellung aus Datensicherungen ([9.2](../../../../installation/09-datenbanken.md#grenzen-und-wann-eine-andere-engine-die-bessere-wahl-ist)) |
| 2. PostgreSQL | eigene Replikation und zeitpunktgenaue Wiederherstellung | Einrichtung nach der PostgreSQL-Dokumentation |
| 3. MongoDB | siehe die Dokumentation des Herstellers | lesen Sie zuerst die [Grenzen von MongoDB](../../../datenbankunterstuetzung.md#grenzen-von-mongodb) |
| 4. MariaDB, 6. MySQL | die Replikation des Servers; Exponential kann von Replikaten lesen | [Lese-Replikate](lese-replikate.md) |
| 5. Oracle | `[ConnectionSettings]`-Failover über mehrere Listener, DRCP, Wiederholungen | jede Funktion ist standardmäßig ausgeschaltet ([9.6](../../../../installation/09-datenbanken.md#96-oracle-19c-und-neuer-ezoracle)) |

Für eine öffentliche Stelle mit einer Pflicht zur Aufrechterhaltung des Betriebs ist SQLite die falsche Engine, sobald
diese Pflicht eine Bereitschaftsdatenbank verlangt; planen Sie den Umstieg auf einen Datenbankserver
([Die Datenbank-Engine wechseln](../../../hosting/migrationen/die-datenbank-engine-einer-kunden-website-wechseln.md)).

## Auf eine Bereitschaftsdatenbank umschalten

Wenn die Plattform keine wandernde Adresse hat und Sie von Hand umschalten, ist die Änderung auf der Seite von
Exponential eine einzige Einstellung. Die Datenbankschritte (das Hochstufen der Bereitschaftsdatenbank) folgen der
Dokumentation Ihrer Engine.

1. Versetzen Sie die Website in den Wartungsmodus, damit während der Umschaltung niemand schreibt:

   ```bash
   php bin/php/maintenance.php on --message="Switching database" --until=30m --allow-root-user
   ```

2. Stufen Sie die Bereitschaftsdatenbank mit den eigenen Werkzeugen der Datenbank hoch.
3. Ändern Sie `Server` (und bei Bedarf `Port`) in `settings/override/site.ini.append.php`, `[DatabaseSettings]`; bei
   einem Cluster auch die Datenbankeinstellungen des Clusters und `CLUSTER_STORAGE_HOST`.
4. Leeren Sie den INI-Cache und lassen Sie die Engine den neuen Wert lesen:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ./console exp:velocity deploy --allow-root-user
   ```

   Unter PHP-FPM ohne Velocity laden Sie das PHP-FPM neu, das die Website ausliefert.
5. Prüfen Sie die Website und beenden Sie dann den Wartungsmodus:

   ```bash
   php bin/php/maintenance.php off --allow-root-user
   ```

Ein DNS-Name oder eine virtuelle Adresse, die das Datenbankteam verschiebt, macht Schritt 3 überflüssig und ist die
bessere Architektur.

## Es testen

Eine Bereitschaftsdatenbank, auf die nie umgeschaltet wurde, ist eine Hoffnung. Testen Sie die Umschaltung in einer
Testumgebung ([Umgebungen für Entwicklung, Test und Produktion](../hosting/umgebungen-entwicklung-test-produktion.md)), messen Sie,
wie lange sie dauert, und halten Sie die Zeit zusammen mit Ihren Wiederherstellungszielen fest. Prüfen Sie nach einer
echten Umschaltung die Inhalte, die kurz vor dem Ausfall veröffentlicht wurden: Eine Replikationsverzögerung kann die
letzten Schreibvorgänge kosten.

## Checkliste

- [ ] Die Engine trägt die Pflicht zur Aufrechterhaltung des Betriebs (nicht SQLite, wenn eine
  Bereitschaftsdatenbank verlangt ist).
- [ ] Das Datenbankteam betreibt die Replikation und ist für das Verfahren zum Hochstufen verantwortlich.
- [ ] Die Website erreicht die Datenbank über eine Adresse, die wandern kann, oder die manuelle Umschaltung ist
  dokumentiert.
- [ ] Über `ConnectRetries` ist entschieden.
- [ ] Die Umschaltung wurde getestet und ihre Dauer festgehalten.

## Verwandte Seiten

- [Verfügbarkeit](README.md): Übersicht dieses Ordners
- [Eine Datenbank in der offiziellen Reihenfolge wählen](../hosting/datenbankwahl-in-der-offiziellen-reihenfolge.md)
- [Datensicherung je Datenbank-Engine](../../../../../eng-us/help/government/operations/backups-and-recovery/backups-per-database-engine.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
