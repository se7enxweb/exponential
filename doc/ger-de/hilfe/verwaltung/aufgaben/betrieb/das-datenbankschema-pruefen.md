---
title: Das Datenbankschema prüfen
audience: government, admin, developer
category: public-sector
order: 220
summary: Schritt für Schritt - die Prüfung der Datenbankkonsistenz unter Setup > Upgrade-Prüfung ausführen, fehlende, geänderte und zusätzliche Tabellen lesen, das SQL herunterladen und nur anwenden, was Sie verstehen, nach einer Sicherung.
keywords: Datenbankkonsistenz, Schemaprüfung, Überprüfe Datenbankkonsistenz, fehlende Tabelle, geänderte Tabelle, SQL herunterladen, db_schema.dba, Upgrade, MongoDB-Collections, database consistency, schema check, Check database consistency, missing table, changed table, Download SQL, upgrade, MongoDB collections
type: help
---
# Das Datenbankschema prüfen

Diese Aufgabe ist für Administratoren und Entwickler nach einem Upgrade, einem Update einer Erweiterung oder einem
Umzug der Datenbank. Am Ende wissen Sie, ob die Datenbank dem entspricht, was Exponential und seine aktiven
Erweiterungen erwarten.
Referenz: [Die Upgrade-Prüfung, Abschnitt 6](../../../../anleitungen/upgrade-pruefung.md#6-die-prüfung-der-datenbankkonsistenz-ausführen).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis lesen](#das-ergebnis-lesen)
4. [SQL sicher anwenden](#sql-sicher-anwenden)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ein Administratorkonto.
- Eine aktuelle Sicherung ([Vor einer Änderung eine Sicherung erstellen](vor-einer-aenderung-eine-sicherung-erstellen.md)).

## Schritte

1. **Setup > Upgrade-Prüfung** (`/setup/systemupgrade`).
2. **Überprüfe Datenbankkonsistenz**. Sie vergleicht die Datenbank mit `share/db_schema.dba` und den Schemadateien
   aktiver Erweiterungen.
3. Lesen Sie die Zahlen und die Karte je Tabelle; **SQL herunterladen** für die Akten. Die Seite führt niemals SQL aus.

## Das Ergebnis lesen

| Art | Bedeutung | Tun |
|---|---|---|
| Fehlende Tabelle | mit Exponential oder einer aktiven Erweiterung ausgeliefert, nicht in der Datenbank; die Funktion schlägt fehl | ihre CREATE-Anweisung nach einer Sicherung ausführen |
| Geänderte Tabelle | Felder oder Indizes weichen ab | jede Anweisung lesen; die mit **Entfernt etwas** markierten können Daten entfernen |
| Tabelle nicht im Schema | die Tabelle einer eigenen Erweiterung oder einer entfernten Erweiterung | stehen lassen, sofern Sie nicht wissen, dass die Daten nicht gebraucht werden |
| Tabelle einer nicht aktiven Erweiterung | gehört zu einer installierten, inaktiven Erweiterung | für sie wird kein SQL geschrieben; aktivieren oder stehen lassen |
| Hinweise zur Engine | Unterschiede, für die der Schemahandler der Engine kein SQL schreibt | nichts |

Auf MongoDB vergleicht die Prüfung Collections und bietet für fehlende den Befehl `mongosh` an.

## SQL sicher anwenden

1. Zuerst auf Staging.
2. Eine Anweisung nach der anderen, mit dem Client des Herstellers.
3. Führen Sie die Prüfung erneut aus: Die Tabelle verschwindet aus den Befunden.
4. Notieren Sie die Anweisungen im Änderungsnachweis.

## Verwandte Seiten
- [Die Konsistenz nach einem Upgrade prüfen](die-konsistenz-nach-einem-upgrade-pruefen.md)
- [Überblick über Upgrades für den öffentlichen Sektor](../../betrieb/aktualisierungen-und-aenderungen/aktualisierungen-im-oeffentlichen-sektor-im-ueberblick.md)
- [Upgrades von PHP und Datenbank](../../betrieb/aktualisierungen-und-aenderungen/php-und-datenbank-aktualisierungen.md)
- [Datenbankunterstützung](../../../datenbankunterstuetzung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
