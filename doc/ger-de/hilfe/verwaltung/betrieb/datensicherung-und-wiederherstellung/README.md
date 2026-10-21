---
title: Datensicherung und Wiederherstellung für Websites des öffentlichen Sektors
audience: government, hosting, admin, project-manager
category: public-sector
order: 10
summary: Übersicht der Seiten zu Datensicherung und Wiederherstellung für öffentliche Stellen: was zu sichern ist, Datenbanksicherungen je Engine, Dateien und Schlüssel, Sicherungspläne und Aufbewahrung, Datensicherungen aus der Administration, Wiederherstellungstests, Notfallwiederherstellung, RPO und RTO, Archive des Prüfprotokolls sowie verschlüsselte Kopien außer Haus.
keywords: Datensicherung, Wiederherstellung, Wiederherstellungstest, Notfallwiederherstellung, RPO, RTO, Aufbewahrung, Schlüssel, Audit-Archive, außer Haus, Verschlüsselung, git_manager, Kontinuität, backup, recovery, restore, restore test, disaster recovery, retention, keys, audit archives, offsite, encryption, continuity
type: help
---
# Datensicherung und Wiederherstellung für Websites des öffentlichen Sektors

Dieser Ordner richtet sich an Betriebsteams, Kontinuitätsplanende, Verantwortliche für das Schriftgut und
Sicherheitsbeauftragte in öffentlichen Stellen, die sicherstellen müssen, dass eine Exponential-Website nach einem
Ausfall, einem Angriff oder einem Fehler wiederhergestellt werden kann. Er behandelt, was zu sichern ist, wie das je
Datenbank-Engine geht, die Dateien und Schlüssel, Sicherungspläne und Aufbewahrung, Datensicherungen aus der
Administration, Wiederherstellungstests, die Planung der Notfallwiederherstellung, Wiederherstellungsziele, die
Archive des Prüfprotokolls und verschlüsselte Kopien außer Haus. Die Befehle sind dieselben wie in den
[Betriebsseiten](../../../hosting/betrieb/README.md) des Hosting-Bereichs; diese Seiten ergänzen die Pflichten des
öffentlichen Sektors: Kontinuitätspläne, Schriftgutverwaltung, Nachweise und Speicherort der Daten.

## Seiten

| Seite | Beantwortet |
|---|---|
| [Was zu sichern ist](was-gesichert-werden-muss.md) | Die Datenbank und die Dateien, die eine Datensicherung braucht, die Schlüsseldateien und was Sie weglassen. |
| [Datensicherung je Datenbank-Engine](datensicherungen-je-datenbank-engine.md) | Der Sicherungs- und Wiederherstellungsbefehl jeder unterstützten Engine und die Einbindung in Ihr Sicherungssystem. |
| [Sicherung von Dateien und Einstellungen](sicherung-von-dateien-und-einstellungen.md) | Die einzuschließenden und auszuschließenden Verzeichnisse, Multi-Site- und Cluster-Aufbauten, Eigentümer bei der Wiederherstellung. |
| [Sicherungspläne und Aufbewahrung](sicherungsplaene-und-aufbewahrung.md) | Die Entscheidungen einer Sicherungsrichtlinie und wie die Aufbewahrung Schriftgut- und Datenschutzpflichten erfüllt. |
| [Schlüssel und Geheimnisse in Datensicherungen](schluessel-und-geheimnisse-in-datensicherungen.md) | Welche Dateien Schlüssel enthalten, die sich nicht neu erzeugen lassen, und wie Sie sie sicher sichern. |
| [Datensicherung aus der Administration mit git_manager](datensicherungen-aus-der-administration-mit-git-manager.md) | Eine Datensicherung unter Setup > Backup ohne Shell erstellen, und ihre Grenzen. |
| [Wiederherstellung testen](wiederherstellungstests.md) | Wie oft Sie testen, was Sie prüfen und wie Sie Nachweise festhalten. |
| [Wiederherstellung auf einem Testserver](wiederherstellung-auf-einem-testserver.md) | Das Wiederherstellungsverfahren auf einer separaten Maschine, mit stillgelegtem Mailversand und stillgelegten Cronjobs. |
| [Ein Notfallwiederherstellungsplan](notfallwiederherstellungsplan.md) | Szenarien, die Teile eines Plans, eine Gliederung und die technische Wiederherstellung. |
| [RPO und RTO als Entscheidungen](rpo-und-rto-als-entscheidungen.md) | Wer Wiederherstellungsziele festlegt und was welchen Wert erfüllt. |
| [Archive des Prüfprotokolls und Datensicherung](audit-trail-archive-und-datensicherungen.md) | Audit-Nachweise jahrelang aufbewahren, prüfen und alte Tage lesen. |
| [Kopien außer Haus und verschlüsselte Kopien](externe-und-verschluesselte-kopien.md) | Wohin Kopien gehen dürfen, wie Sie sie verschlüsseln und die Schlüssel wiederherstellbar halten. |

## Lesereihenfolge

1. [Was zu sichern ist](was-gesichert-werden-muss.md), dann [je Engine](datensicherungen-je-datenbank-engine.md) und
   [Dateien und Einstellungen](sicherung-von-dateien-und-einstellungen.md).
2. [Schlüssel und Geheimnisse in Datensicherungen](schluessel-und-geheimnisse-in-datensicherungen.md) und [Kopien außer Haus und verschlüsselte Kopien](externe-und-verschluesselte-kopien.md).
3. Die Richtlinie: [RPO und RTO als Entscheidungen](rpo-und-rto-als-entscheidungen.md), dann [Sicherungspläne und Aufbewahrung](sicherungsplaene-und-aufbewahrung.md)
   und [Archive des Prüfprotokolls und Datensicherung](audit-trail-archive-und-datensicherungen.md).
4. Der Nachweis: [Wiederherstellung testen](wiederherstellungstests.md) und [Wiederherstellung auf einem Testserver](wiederherstellung-auf-einem-testserver.md).
5. Der Plan: [Ein Notfallwiederherstellungsplan](notfallwiederherstellungsplan.md).
6. Optional: [git_manager](datensicherungen-aus-der-administration-mit-git-manager.md) für Administratorinnen und Administratoren ohne Shell.

Siehe auch [Hosting](../hosting/README.md) und [Verfügbarkeit](../verfuegbarkeit/README.md).

## Verwandte Seiten

- Nach oben: [Betrieb](../README.md)
- Anfang des Bereichs: [Behörden: hier beginnen](../../verwaltungen-hier-beginnen.md)
- Runbook für einen Vorfall: [Aus einer Datensicherung wiederherstellen](../../../hosting/runbooks/aus-einem-backup-wiederherstellen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
