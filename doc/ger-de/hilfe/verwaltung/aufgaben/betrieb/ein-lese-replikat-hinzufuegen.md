---
title: Ein Lese-Replikat hinzufügen
audience: government, admin, hosting
category: public-sector
order: 270
summary: Schritt für Schritt - Leseabfragen einer MySQL- oder MariaDB-Site mit UseSlaveServer an ein oder mehrere Replikate schicken, nachdem das Datenbankteam die Replikation eingerichtet hat, und die Wirkung prüfen; mit den Grenzen (nur MySQL und MariaDB, Replikationsverzögerung).
keywords: Lese-Replikat, Replikation, UseSlaveServer, SlaveServerArray, MySQL, MariaDB, Skalierung von Lesezugriffen, Datenbanklast, Replikationsverzögerung, DatabaseSettings, read replica, replication, read scaling, database load, replication lag
type: help
---
# Ein Lese-Replikat hinzufügen

Diese Aufgabe ist für Administratoren stark besuchter Sites auf MySQL oder MariaDB. Mit `site.ini [DatabaseSettings]
UseSlaveServer=enabled` gehen Lesezugriffe an ein je Anfrage zufällig gewähltes Replikat und Schreibzugriffe an
`Server`. Es ist die einzige Datenbank-Engine mit eingebauter Aufteilung der Lesezugriffe
([Datenbankunterstützung](../../../datenbankunterstuetzung.md); [Kapitel 9, Replikate](../../../../installation/09-datenbanken.md#replikate)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Grenzen](#grenzen)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Das Datenbankteam hat die Replikation mit den Werkzeugen des Herstellers eingerichtet; Exponential tut das nicht.
- Ein Datenbankbenutzer auf dem Replikat mit Lesezugriff.
- Eine genehmigte und auf Staging getestete Änderung.

## Schritte

1. In `settings/override/site.ini.append.php` (die Schlüssel für Benutzer, Passwort und Datenbank werden wirklich
   `Slaver...` geschrieben):

   ```ini
   [DatabaseSettings]
   UseSlaveServer=enabled
   SlaveServerArray[]=db-replica1.internal.example.gov
   SlaveServerPort[]=3306
   SlaverServerUser[]=<read user>
   SlaverServerPassword[]=<password>
   SlaverServerDatabase[]=<database>
   ```

2. Leeren Sie den INI-Cache und rollen Sie aus:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ./console exp:velocity deploy --allow-root-user
   ```

## Prüfen, ob es funktioniert hat

- Die Site funktioniert; das Veröffentlichen funktioniert weiterhin (Schreibzugriffe gehen an den Primärserver).
- Die eigene Abfragestatistik des Replikats zeigt ankommende Lesezugriffe.
- **Setup > Systeminformationen**, Datenbankverbindung, für die Verbindungsdaten, wie die Seite sie zeigt.

## Grenzen

- **Replikationsverzögerung**: Eine Seite kann Inhalte zeigen, die einige Sekunden alt sind; ein Redakteur, der gerade
  veröffentlicht hat, sieht die Änderung auf einer Seite, die vom Replikat gelesen wird, vielleicht nicht sofort.
- **Nur MySQL und MariaDB**; andere Engines skalieren Lesezugriffe mit ihren eigenen Werkzeugen.
- Ein Replikat ist keine Sicherung und kein Failover: Fällt der Primärserver aus, schlagen Schreibzugriffe fehl
  ([Auf eine Standby-Datenbank umschalten](auf-eine-standby-datenbank-umschalten.md)).
- Das Passwort steht in einer Einstellungsdatei: Halten Sie `settings/override` aus der Versionsverwaltung heraus oder
  verwenden Sie Ihren Prozess für Geheimnisse.

## Verwandte Seiten
- [Lese-Replikate](../../../../../eng-us/help/government/operations/availability/read-replicas.md)
- [Hochverfügbarkeit der Datenbank](../../../../../eng-us/help/government/operations/availability/database-high-availability.md)
- [Mehrere Webknoten (Hosting)](../../../hosting/betrieb/mehrere-web-knoten.md)
- [MariaDB für Hoster](../../../hosting/grundlagen/mariadb-fuer-hoster.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
