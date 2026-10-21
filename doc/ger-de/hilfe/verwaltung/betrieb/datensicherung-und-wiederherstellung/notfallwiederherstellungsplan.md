---
title: Ein Notfallwiederherstellungsplan
audience: government, hosting, admin, project-manager
category: public-sector
order: 100
summary: Für öffentliche Stellen, die dokumentieren müssen, wie eine Website nach einer Katastrophe zurückkommt: Verlust eines Servers, eines Rechenzentrums, der Datenbank oder der Daten selbst. Die Szenarien, die Teile eines Plans, eine Gliederung zum Übernehmen und die Schritte in Exponential, die jedes Szenario braucht.
keywords: Notfallwiederherstellung, Notfallplan, Kontinuitätsplan, Betriebskontinuität, Verlust des Rechenzentrums, Verlust des Servers, Ransomware, Kompromittierung, Wiederherstellung, Bereitschaftssystem, Kontakte, Szenario, Übung, disaster recovery, DR plan, continuity plan, business continuity, data center loss, server loss, compromise, restore, standby, contacts, scenario, exercise
type: help
---
# Ein Notfallwiederherstellungsplan

Diese Seite richtet sich an Kontinuitätsplanende, IT-Leitungen und Betriebsteams in öffentlichen Stellen, die den
Notfallwiederherstellungsplan einer Website schreiben. Sie führt die Szenarien auf, für die sich eine Planung lohnt,
die Teile, die jeder Plan braucht, eine Gliederung zum Übernehmen und die Schritte in Exponential hinter jedem
Szenario. Das Kontinuitätsrahmenwerk Ihrer Organisation bestimmt das Format; diese Seite füllt den technischen Teil.

## Inhalt

1. [Szenarien](#szenarien)
2. [Was jeder Plan braucht](#was-jeder-plan-braucht)
3. [Eine Gliederung zum Übernehmen](#eine-gliederung-zum-übernehmen)
4. [Die technische Wiederherstellung](#die-technische-wiederherstellung)
5. [Den Plan üben](#den-plan-üben)
6. [Checkliste](#checkliste)

## Szenarien

| Szenario | Wiederherstellung | Seiten |
|---|---|---|
| Ein Server fällt aus, die Daten sind anderswo intakt | Wiederherstellung auf einer neuen oder einer Reservemaschine | [Wiederherstellung auf einem Testserver](wiederherstellung-auf-einem-testserver.md) (dieselben Schritte, Produktionshost) |
| Der Datenbankserver fällt aus | Umschalten auf eine Bereitschaftsdatenbank oder Wiederherstellung der Datenbank | [Hochverfügbarkeit der Datenbank](../verfuegbarkeit/hochverfuegbarkeit-der-datenbank.md) |
| Das Rechenzentrum geht verloren | Neuaufbau an einem zweiten Standort aus Datensicherungen außer Haus | [Kopien außer Haus und verschlüsselte Kopien](externe-und-verschluesselte-kopien.md) |
| Daten von einem Angreifer zerstört oder verschlüsselt | Wiederherstellung aus einer Datensicherung von vor dem Angriff; zuerst untersuchen | [Kompromittierte Website](../../../hosting/runbooks/kompromittierte-website.md) |
| Ein großer inhaltlicher Fehler der Redaktion | Inhalte aus dem Papierkorb oder älteren Versionen wiederherstellen oder eine gezielte Wiederherstellung | [Inhaltsverlauf](../../../../anleitungen/inhaltsverlauf.md) |
| Der gemeinsame Speicher eines Clusters fällt aus | den NFS-Export und seine Metadatendatenbank wiederherstellen | [Gemeinsamer Speicher und der DFS-Cluster](../verfuegbarkeit/gemeinsamer-speicher-und-der-dfs-cluster.md) |

## Was jeder Plan braucht

- **Ziele**: Wiederherstellungspunkt und Wiederherstellungszeit, von der Organisation festgelegt
  ([RPO und RTO als Entscheidungen](rpo-und-rto-als-entscheidungen.md)).
- **Rollen**: wer einen Notfall ausruft, wer wiederherstellt, wer kommuniziert; Teamnamen mit Kontakten der
  Rufbereitschaft in einer separaten, aktuellen Liste.
- **Orte**: wo Datensicherungen, Schlüssel und der Plan selbst aufbewahrt werden, erreichbar, wenn die
  Hauptwebsite und das Netzwerk ausgefallen sind.
- **Verfahren**: Schritt für Schritt, getestet, eines je Szenario.
- **Kommunikation**: was der Öffentlichkeit mitgeteilt wird und wo, solange die Website ausgefallen ist (eine
  Statusseite auf einer anderen Plattform, soziale Kanäle, eine Telefonleitung).
- **Abhängigkeiten**: DNS, Zertifikate, Mail-Relay, zentraler Proxy, Identitätsanbieter.

## Eine Gliederung zum Übernehmen

1. Zweck und Geltungsbereich: welche Websites, welche Installationen.
2. Ziele je Website: Wiederherstellungspunkt, Wiederherstellungszeit, Priorität.
3. Rollen und Kontakte (Verweis auf die Kontaktliste).
4. Bestandsaufnahme: Hosting-Entscheidungsprotokoll, Verzeichnis der Datenspeicherorte, Speicherorte der
   Datensicherungen ([Ein Hosting-Entscheidungsprotokoll](../hosting/hosting-entscheidungsprotokoll.md),
   [Speicherort der Daten und was wo gespeichert wird](../hosting/datenstandort-und-was-wo-gespeichert-wird.md)).
5. Szenarien und ihre Verfahren (Tabelle oben).
6. Vorlagen für die Kommunikation.
7. Rückkehr zum Normalbetrieb: Prüfungen, Abschluss des Vorfalls, Nachbetrachtung.
8. Übungsplan und Übungsverlauf.

## Die technische Wiederherstellung

Unabhängig vom Szenario folgt die Seite von Exponential demselben Kern:

```bash
php bin/php/maintenance.php on --message="Restoring the service" --until=2h --allow-root-user
php bin/php/console exp:velocity stop --allow-root-user
# restore the database with the engine's tool, unpack the files and key files
php bin/php/ezcache.php --clear-all --allow-root-user
php bin/php/ezpgenerateautoloads.php -e
php bin/php/console exp:velocity start --allow-root-user
php bin/php/console exp:velocity cache clear --allow-root-user
./console exp:audit verify --allow-root-user
php bin/php/maintenance.php off --allow-root-user
```

Aus [Aus einer Datensicherung wiederherstellen](../../../hosting/runbooks/aus-einem-backup-wiederherstellen.md). Bewahren Sie nach
einem Angriff vor dem Überschreiben eine Kopie des beschädigten Zustands für die Untersuchung auf. Bauen Sie nach der
Wiederherstellung den Suchindex neu auf, wenn er unvollständig wirkt (`php bin/php/updatesearchindex.php`), und
veröffentlichen Sie erneut oder geben Sie neu ein, was seit der Datensicherung verloren gegangen ist.

## Den Plan üben

Ein Plan, der nie geübt wurde, ist ein Entwurf. Üben Sie ihn in einem Rhythmus, den Sie wählen: ein Planspiel am Tisch
mit den Rollen und eine technische Wiederherstellung an einem zweiten Standort. Halten Sie die gemessene Zeit und jede
gefundene Lücke fest und aktualisieren Sie den Plan. Die Berichte der Wiederherstellungstests aus
[Wiederherstellung testen](wiederherstellungstests.md) sind Teil der Nachweise.

## Checkliste

- [ ] Jede Website hat Ziele, eine Priorität und eine Szenariotabelle.
- [ ] Datensicherungen, Schlüssel und der Plan sind erreichbar, wenn die Hauptwebsite und das Netzwerk ausgefallen
  sind.
- [ ] Die Kommunikation mit der Öffentlichkeit während eines Ausfalls ist vorbereitet.
- [ ] Der Plan wurde geübt, und die gemessenen Zeiten stehen darin.
- [ ] Er wird nach jedem Vorfall und jeder größeren Änderung überprüft.

## Verwandte Seiten

- [Datensicherung und Wiederherstellung](README.md): Übersicht dieses Ordners
- [Überblick über Hochverfügbarkeit](../verfuegbarkeit/hochverfuegbarkeit-im-ueberblick.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
