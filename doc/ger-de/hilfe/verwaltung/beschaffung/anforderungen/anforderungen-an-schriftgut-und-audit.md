---
title: Anforderungen an Schriftgut und Audit
audience: government, project-manager, admin
category: public-sector
order: 110
summary: Formulierungen für Schriftgutverwaltung und Rechenschaft in einem öffentlichen CMS - Versionsgeschichte, ein manipulationssicheres Audit-Protokoll, Aufbewahrung, Export für Archive und der Nachweis, wer was veröffentlicht hat - mit dem, was Exponential dokumentiert, und wo weiterhin ein externes Schriftgutsystem nötig ist.
keywords: Schriftgutverwaltung, Audit-Protokoll, Rechenschaft, Versionsgeschichte, Aufbewahrung, Archivierung, ISO 15489, OAIS, manipulationssicher, Hash-Kette, Nachweis, Export, Ausschreibung, Anforderungen, records management, audit trail, accountability, version history, retention, archiving, tamper-evident, hash chain, evidence, tender, requirements
type: help
---
# Anforderungen an Schriftgut und Audit

Diese Seite richtet sich an Verantwortliche für Schriftgut, prüfende Stellen und Projektleitungen, die sicherstellen
müssen, dass sich später nachvollziehen lässt, was eine öffentliche Stelle veröffentlicht: wer es veröffentlicht hat,
wann, was darin stand und dass der Nachweis nicht verändert wurde. Sie gibt Formulierungen für Anforderungen und ordnet
sie den dokumentierten Funktionen von Exponential zu. Ein CMS ist kein Schriftgutverwaltungssystem; die Anforderungen
unten decken ab, was das CMS an eines übergeben muss.

![Der Zustand des Audit-Protokolls: jede Hash-Kette als intakt verifiziert](../../../../../eng-us/help/images/government/gov-audit-req.webp)

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Hintergrund](#hintergrund)
2. [Formulierung der Anforderungen](#formulierung-der-anforderungen)
3. [Was Exponential dokumentiert](#was-exponential-dokumentiert)
4. [Wo Sie weiterhin ein Schriftgutsystem brauchen](#wo-sie-weiterhin-ein-schriftgutsystem-brauchen)
5. [Fallstricke](#fallstricke)

## Hintergrund

- Die Grundsätze der Schriftgutverwaltung legt [ISO 15489-1:2016](https://www.iso.org/standard/62542.html) fest; die
  langfristige digitale Erhaltung das OAIS-Referenzmodell, [ISO 14721:2012](https://www.iso.org/standard/57284.html).
- Viele Rechtsordnungen haben Archivgesetze, die festlegen, welche Webinhalte Schriftgut sind und wie lange sie
  aufbewahrt werden. Ihre Schriftgutverwaltung oder Archivbehörde legt den Aufbewahrungsplan fest; das CMS muss ihn
  unterstützen.

## Formulierung der Anforderungen

| Id | Anforderung |
|---|---|
| REC-01 | Jede veröffentlichte Änderung an einem Inhaltselement wird als Version mit Autor und Datum aufbewahrt; Versionen lassen sich ansehen und vergleichen. |
| REC-02 | Für Sicherheit und Rechenschaft relevante Aktionen (veröffentlichen, entfernen, verschieben, Änderungen an Berechtigungen, Änderungen an Einstellungen, Anmeldungen) werden mit handelnder Person und Zeit protokolliert. |
| REC-03 | Das Protokoll ist manipulationssicher; der Lieferant beschreibt, wie die Integrität gezeigt und wie sie verifiziert wird. |
| REC-04 | Das Protokoll lässt sich für einen Zeitraum, eine handelnde Person oder einen Gegenstand in einem dokumentierten Format exportieren. |
| REC-05 | Die Aufbewahrung von Protokollen und Versionen ist einstellbar und folgt dem Aufbewahrungsplan <Verweis>. |
| REC-06 | Veröffentlichte Inhalte mit Metadaten und Dateien lassen sich zur Übergabe an <Schriftgutsystem / Archiv> in einem dokumentierten Format exportieren. |
| REC-07 | Für die Archivierung lässt sich ein statischer Schnappschuss der öffentlichen Website erzeugen. |
| REC-08 | Das Entfernen von Inhalten wird festgehalten, und entfernte Inhalte bleiben <Zeitraum> wiederherstellbar, bevor sie endgültig gelöscht werden. |

## Was Exponential dokumentiert

| Id | Nachweis |
|---|---|
| REC-01 | [Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md), [Veröffentlichen und Versionen](../../../veroeffentlichen-und-versionen.md) |
| REC-02 | Das Audit-Protokoll, standardmäßig an, mit den Kanälen `content`, `access`, `system`, `commerce` und optional `read` ([Das Audit-Protokoll](../../../../funktionen/6.0/pruefprotokoll.md)) |
| REC-03 | Jeder Eintrag trägt den Hash des vorherigen; tägliche Dateien werden mit signierten Manifesten archiviert; `./console exp:audit verify` endet mit 1, wenn eine Kette gebrochen ist |
| REC-04 | `./console exp:audit export`, einschließlich `--subject-user=<id>`; der Tab **Audit** in der Administration (`/audit/dashboard`) ([Audit-Leitfaden](../../../../bc/6.0/audit.md)) |
| REC-05 | Aufbewahrung des Audits standardmäßig je Kanal 90 Tage live und 2 Jahre archiviert; Pseudonymisierung nach einer festgelegten Zeit ([Datenschutzstandards](../../../../standards/datenschutz.md)). Alte Versionen lassen sich auf der Versionsseite bereinigen |
| REC-06 | Website-Archiv von xrowextract (CSV je Klasse mit `manifest.json`), Pakete, Datenbank-Dumps ([Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md)) |
| REC-07 | `exp:makestaticcache` schreibt statische Seiten ([Generator für den statischen Cache](../../../../funktionen/6.0/generator-fuer-den-statischen-cache.md)) |
| REC-08 | Der Papierkorb hält fest, wer ein Element in den Papierkorb gelegt hat und wo es war ([Die Ansicht des Papierkorbs](../../../../bc/6.0/papierkorb.md)) |

Die Signaturschlüssel des Audits liegen in `settings/override/audit.ini.append.php`; ihr Verlust bedeutet, dass sich
bestehende Einträge nicht mehr verifizieren lassen ([Sicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md)).
Nehmen Sie ihre Sicherung in die Anforderungen auf.

## Wo Sie weiterhin ein Schriftgutsystem brauchen

- **Aufbewahrungspläne nach Schriftgutklasse** und rechtliche Sperren sind keine Funktionen des CMS.
- **Formate für die Langzeiterhaltung** (zum Beispiel Archiv-PDF): Die PDF-Ausgabe von Exponential ist nicht getaggt und
  deklariert PDF 1.3 ([Standards zu Dokumentation und Veröffentlichung](../../../../standards/dokumentation-und-veroeffentlichung.md)).
- **Genehmigungen zur Vernichtung** und deren Nachweise.

Spezifizieren Sie die Übergabe (REC-06, REC-07) und überlassen Sie den Rest dem Schriftgutsystem.

## Fallstricke

- Annehmen, die Standardaufbewahrung des Audits passe zu Ihrem Plan. Schreiben Sie Ihre Fristen in die Anforderung.
- Formulareingaben und Newsletter vergessen: Auch sie können Schriftgut sein
  ([Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md)).
- Personenbezogene Daten in Archiven länger aufbewahren, als der Zweck erlaubt; beziehen Sie die Datenschutzbeauftragten
  ein.

## Verwandte Seiten
- [Anforderungen an ein CMS für den öffentlichen Sektor](README.md)
- [Anforderungen an den Datenschutz](anforderungen-an-datenschutz.md)
- [Leitfaden zu Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
