---
title: "FAQ: Schriftgut und Audit"
audience: government, admin, project-manager
category: public-sector
order: 130
summary: Antworten auf die Fragen öffentlicher Stellen zu Schriftgut und Audit in Exponential - Versionsgeschichte, der Papierkorb, der Audit-Trail und seine Unversehrtheit, Aufbewahrung, Exporte für Archive und welche Funktionen der Schriftgutverwaltung es nicht hat.
keywords: FAQ Schriftgut, FAQ Audit, Versionsgeschichte, Papierkorb, Audit-Trail, manipulationssicher, Hash-Kette, Aufbewahrung, Archiv, Schriftgutverwaltung, Export, öffentlicher Sektor, records FAQ, audit FAQ, version history, trash, audit trail, tamper-evident, hash chain, retention, archive, records management, export, public sector
type: help
---
# FAQ: Schriftgut und Audit

Diese Seite beantwortet die Fragen öffentlicher Stellen zur Aufbewahrung von Schriftgut und zum Audit in Exponential. Die
Antworten stammen aus [Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md),
[Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md) und [Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Archiv- und Schriftgutrecht mit Ihrer Rechtsberatung.

## Inhalt

1. [Führt Exponential eine Geschichte der Änderungen?](#führt-exponential-eine-geschichte-der-änderungen)
2. [Sind gelöschte Inhalte weg?](#sind-gelöschte-inhalte-weg)
3. [Was zeichnet der Audit-Trail auf?](#was-zeichnet-der-audit-trail-auf)
4. [Kann der Audit-Trail manipuliert werden?](#kann-der-audit-trail-manipuliert-werden)
5. [Wie lange werden Audit-Einträge aufbewahrt?](#wie-lange-werden-audit-einträge-aufbewahrt)
6. [Ist Exponential ein Schriftgutverwaltungssystem?](#ist-exponential-ein-schriftgutverwaltungssystem)
7. [Können wir Schriftgut für ein Archiv exportieren?](#können-wir-schriftgut-für-ein-archiv-exportieren)

## Führt Exponential eine Geschichte der Änderungen?

Ja. Jedes Inhaltsobjekt behält seine Versionen; die Versionsseite vergleicht sie, stellt sie wieder her und räumt sie
auf ([Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md)).

## Sind gelöschte Inhalte weg?

Nicht sofort. Entfernte Objekte kommen in den Papierkorb, der zeigt, wer sie von wo entfernt hat, bis sie endgültig
gelöscht werden ([Papierkorb](../../../../eng-us/features/6.0/trash-who-and-where.md)). Benutzerobjekte kommen nie in den
Papierkorb ([Datenschutzstandards](../../../../eng-us/standards/privacy.md#known-gaps)).

## Was zeichnet der Audit-Trail auf?

Sicherheitsrelevante Ereignisse in fünf Kanälen (content, access, system, commerce, read): Anmeldungen,
Inhaltsänderungen, Schreiben von Einstellungen, Leeren von Caches, Umschalten der Wartung und mehr
([Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md), [das Glossar von Exponential](../../../../eng-us/glossary.md#a)). Er ist
standardmäßig eingeschaltet.

## Kann der Audit-Trail manipuliert werden?

Er ist manipulationssicher: Die Einträge sind mit einer Hash-Kette und signierten Manifesten verbunden, und
`./console exp:audit verify` prüft die Kette ([Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)). Eine
Alarmregel schlägt an, wenn die Kette bricht oder das Audit ausgeschaltet wird. Bewahren Sie die Signaturschlüssel sicher
und gesichert auf
([Datensicherungen: Schlüssel und Geheimnisse](../../hosting/betrieb/backups-was-gesichert-werden-muss.md#schlüssel-und-geheimnisse-die-sich-nicht-neu-erzeugen-lassen)).

## Wie lange werden Audit-Einträge aufbewahrt?

Standardmäßig pro Kanal 90 Tage aktiv und zwei Jahre archiviert, dann werden sie automatisch entfernt; Felder werden
nach einer festgelegten Anzahl von Tagen pseudonymisiert ([Datenschutzstandards](../../../../eng-us/standards/privacy.md#gdpr),
[Audit-Leitfaden 4.3](../../../../eng-us/bc/6.0/audit.md#43-retention)). Legen Sie die Fristen fest, die Ihre Regeln verlangen.

## Ist Exponential ein Schriftgutverwaltungssystem?

Nein. Es behält Versionen, einen Papierkorb und einen Audit-Trail, aber die Dokumentation beschreibt keine
Aufbewahrungspläne nach Klasse, keine Aufbewahrungssperren und keine Abgabepakete für Archive. Verwenden Sie daneben
feste Abläufe oder ein Schriftgutsystem ([Checkliste Schriftgut](../checklisten/checkliste-schriftgut.md)).

## Können wir Schriftgut für ein Archiv exportieren?

Ja: xrowextract exportiert jede Klasse als CSV, XML oder JSON mit einem Manifest; das Speicherverzeichnis enthält die
angehängten Dateien; Audit-Einträge lassen sich mit `./console exp:audit export` exportieren
([Von Exponential wegmigrieren](../../../installation/migration/exponential-verlassen.md#1-was-sie-exportieren-sollten)).

## Verwandte Seiten

- [FAQ für Verwaltungen](README.md)
- [Checkliste Schriftgut](../checklisten/checkliste-schriftgut.md)
- [Bereitschaft für Informationsfreiheit](../checklisten/bereitschaft-fuer-informationsfreiheit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
