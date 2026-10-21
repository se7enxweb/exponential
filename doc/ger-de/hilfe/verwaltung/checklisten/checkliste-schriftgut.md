---
title: Checkliste Schriftgut
audience: government, admin, project-manager
category: public-sector
order: 60
summary: Eine Checkliste für öffentliche Stellen, die Inhalte der Website als Schriftgut aufbewahren müssen - was Exponential aufbewahrt (Versionen, Papierkorb, Prüfprotokoll), was es nicht tut (Aufbewahrungspläne, Vernichtung, Abgabe an ein Archiv) und die Abläufe, die die Lücke schließen.
keywords: Schriftgutverwaltung, Schriftgut, Aufbewahrungsplan, Versionen, Geschichte, Prüfprotokoll, Archiv, Vernichtung, Kassation, ISO 15489, OAIS, Export, öffentlicher Sektor, records management, records, retention schedule, versions, history, audit trail, archive, disposal, export, public sector
type: help
---
# Checkliste Schriftgut

Diese Checkliste richtet sich an die Schriftgutverwaltung und die Website-Administration einer öffentlichen Stelle,
deren Website-Inhalte als amtliches Schriftgut zählen können. Sie listet auf, was Exponential von selbst aufbewahrt und
was Sie darum herum organisieren müssen.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung. Die internationale Norm für die Schriftgutverwaltung ist
> ISO 15489-1 ([iso.org](https://www.iso.org/standard/62542.html)); für digitale Langzeitarchive ISO 14721 (OAIS,
> [iso.org](https://www.iso.org/standard/57284.html)).

## Inhalt

1. [Entscheiden, was Schriftgut ist](#entscheiden-was-schriftgut-ist)
2. [Was Exponential aufbewahrt](#was-exponential-aufbewahrt)
3. [Was Exponential nicht tut](#was-exponential-nicht-tut)
4. [Abläufe](#abläufe)
5. [Abgabe und Vernichtung](#abgabe-und-vernichtung)

## Entscheiden, was Schriftgut ist

- [ ] Ihr Aufbewahrungsplan nennt, welche Website-Inhalte Schriftgut sind (zum Beispiel Beschlüsse, Bekanntmachungen, Unterlagen zu Beteiligungsverfahren).
- [ ] Jede solche Art von Inhalt hat eine eigene Inhaltsklasse, damit sie nach Klasse gefunden und exportiert werden kann ([Ihr Inhaltsmodell entwerfen](../../ersteller/website-ersteller/ihr-inhaltsmodell-entwerfen.md)).
- [ ] Die Aufbewahrungsfrist jeder Klasse ist schriftlich festgehalten.

## Was Exponential aufbewahrt

- [ ] **Versionen.** Jedes Objekt behält seine Versionen; sie können auf der Versionsseite verglichen und wiederhergestellt werden ([Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md)). Prüfen Sie, wie viele Versionen Ihre Einstellungen aufbewahren, bevor ältere entfernt werden.
- [ ] **Papierkorb.** Entfernte Objekte warten im Papierkorb, mit Angabe, wer sie entfernt hat und von wo, bis sie endgültig gelöscht werden ([Papierkorb](../../../funktionen/6.0/papierkorb-wer-und-wo.md)).
- [ ] **Prüfprotokoll.** Wer hat wann was geändert, mit Hash-Kette; standardmäßig 90 Tage aktiv und 2 Jahre archiviert pro Kanal ([Prüfprotokoll](../../../funktionen/6.0/pruefprotokoll.md), [Datenschutzstandards](../../../standards/datenschutz.md#dsgvo)).
- [ ] **Objektzustände.** Kennzeichnen Sie Inhalte zum Beispiel als „Schriftgut“ oder „archiviert“ und beschränken Sie, wer sie ändern darf ([Objektzustände](../../../anleitungen/objektzustaende.md)).

## Was Exponential nicht tut

Kennen Sie diese Lücken; nichts in der Dokumentation beschreibt Folgendes als eingebaut:

- [ ] Aufbewahrungspläne, die Inhalte nach Klasse automatisch entfernen oder archivieren.
- [ ] Aufbewahrungssperren (Legal Holds), die das Löschen verhindern.
- [ ] Abgabe an ein Archiv in einem Format für Archivpakete.
- [ ] Metadaten für Schriftgut nach einer Norm für die Schriftgutverwaltung (über die Felder hinaus, die Ihre Inhaltsklassen festlegen).

Planen Sie für jede Lücke, die Sie betrifft, Abläufe oder Werkzeuge ein.

## Abläufe

- [ ] Beschränken Sie das Entfernen von Schriftgutklassen auf wenige Rollen ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).
- [ ] Leeren Sie den Papierkorb für Schriftgutklassen nicht ohne Zustimmung der Schriftgutverwaltung.
- [ ] Exportieren Sie Schriftgutklassen nach Zeitplan mit xrowextract (CSV, XML oder JSON pro Klasse, mit einem Manifest) ([Von Exponential weg migrieren](../../../installation/migration/exponential-verlassen.md#1-was-sie-exportieren-sollten)).
- [ ] Bewahren Sie die Speicherdateien (angehängte Dokumente) mit jedem Export auf.
- [ ] Sichern Sie das Audit-Verzeichnis und seine Signaturschlüssel ([Datensicherungen: was gesichert werden muss](../../hosting/betrieb/backups-was-gesichert-werden-muss.md)).
- [ ] Prüfen Sie die Audit-Kette regelmäßig: `./console exp:audit verify` ([Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)).

## Abgabe und Vernichtung

- [ ] Abgabe an das Archiv: exportieren, den Export mit den Anzahlen in der Administration abgleichen, mit einem Manifest übergeben.
- [ ] Vernichtung: die Objekte entfernen, sie aus dem Papierkorb endgültig löschen und festhalten, was wann vernichtet wurde.
- [ ] Denken Sie an die Datensicherungen: Vernichtete Inhalte bleiben in älteren Datensicherungen, bis diese ablaufen ([Sicherungspläne und Aufbewahrung](../../hosting/betrieb/backup-zeitplaene-und-aufbewahrung.md)).
- [ ] Ein statischer Schnappschuss der öffentlichen Website, wenn Ihre Regeln einen verlangen: `exp:makestaticcache` schreibt statische Seiten ([Von Exponential weg migrieren](../../../installation/migration/exponential-verlassen.md)).

## Verwandte Seiten

- [Checklisten für Verwaltungen](README.md)
- [Bereitschaft für Informationsfreiheit](bereitschaft-fuer-informationsfreiheit.md)
- Der Bereich Schriftgut und Archivierung dieser Hilfe für Verwaltungen (von einem anderen Autor geschrieben)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
