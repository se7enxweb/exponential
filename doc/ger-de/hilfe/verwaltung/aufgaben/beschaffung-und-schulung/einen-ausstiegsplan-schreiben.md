---
title: Einen Ausstiegsplan schreiben
audience: government, project-manager, admin
category: public-sector
order: 190
summary: Schritt für Schritt den Ausstiegsplan für eine Exponential-Site vor der Unterzeichnung eines Vertrags schreiben - Szenarien, was in welchem Format exportiert wird, wer bei der Übergabe was tut, Schlüssel und Zugänge, Zeitplan und wie der Plan getestet wird - mit der Vorlage für den Ausstiegsplan.
keywords: Ausstiegsplan, schreiben, Vertrag, Übergabe, Export, Übergang, Schlüssel, Zugänge, Zeitplan, Anbieterbindung, Aufgabe zur Beschaffung, exit plan, write, contract, handover, export, transition, keys, access, timing, lock-in, procurement task
type: help
---
# Einen Ausstiegsplan schreiben

**Wer:** der Vertragsverantwortliche mit dem Site-Administrator. **Wann:** vor der Unterzeichnung; aktualisieren Sie
ihn jedes Jahr. Vorlage: [Vorlage für den Ausstiegsplan](../../../../../eng-us/help/government/procurement/exit-plans/exit-plan-template.md).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Lassen Sie die
> Klauseln zum Ausstieg von Ihrer Rechtsberatung prüfen.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Ergebnis](#ergebnis)
4. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Lesen Sie, warum der Ausstieg zu Beginn geplant wird
  ([Warum den Ausstieg planen](../../../../../eng-us/help/government/procurement/exit-plans/why-plan-the-exit.md)).
- Kennen Sie die Werkzeuge für den Export ([Formate für den Datenexport](../../../../../eng-us/help/government/procurement/exit-plans/data-export-formats.md),
  [Von Exponential weg migrieren](../../../../installation/migration/exponential-verlassen.md)).

## Schritte

1. **Benennen Sie die Szenarien**: Wechsel des Lieferanten, Wechsel des Hosters, Abschied von Exponential, der
   Lieferant stellt den Betrieb ein.
2. **Listen Sie die Daten und ihr Format** für jedes Szenario auf:
   - Archiv der Site je Klasse (CSV, XML oder JSON mit einem Manifest), über xrowextract;
   - das Speicherverzeichnis (Originaldateien);
   - ein Dump der Datenbank;
   - Layouts, Formulareinsendungen, Einwilligungsnachweise, Audit-Einträge, wo nötig.
3. **Listen Sie die Übergabe außer den Daten auf**: Code-Repository, Dokumentation, Einstellungen, Runbooks
   ([Übergabe der Dokumentation](../../../../../eng-us/help/government/procurement/exit-plans/documentation-handover.md)).
4. **Listen Sie Schlüssel und Zugänge auf**: Administratorkonten, Zugang zu Server und Panel, DNS, Signaturschlüssel des
   Audits, das Geheimnis der E-Mail-Einstellungen, TLS-Schlüssel
   ([Übergabe von Schlüsseln und Geheimnissen](../../../../../eng-us/help/government/procurement/exit-plans/keys-and-secrets-handover.md)).
5. **Legen Sie Rollen und Zeitplan fest**: wer exportiert, wer prüft, die Übergabezeit, die Überschneidung mit dem
   Nachfolger.
6. **Behandeln Sie personenbezogene Daten**: was übertragen wird, was gelöscht wird, Bestätigung der Löschung durch den
   Lieferanten ([Personenbezogene Daten beim Ausstieg](../../../../../eng-us/help/government/procurement/exit-plans/personal-data-at-exit.md)).
7. **Behandeln Sie Adressen**: eine Tabelle der Weiterleitungen aus URL-Aliasen und Platzhaltern
   ([URL-Weiterleitungen nach dem Ausstieg](../../../../../eng-us/help/government/procurement/exit-plans/url-redirects-after-exit.md)).
8. **Planen Sie den Test**: einmal im Jahr eine Übung des Exports ([Eine Ausstiegsübung durchführen](eine-ausstiegsuebung-durchfuehren.md)).
9. **Fügen Sie ihn** dem Vertrag als Anlage **bei**, mit der Klausel, die den Lieferanten zur Mitwirkung verpflichtet
   ([Klausel zu Ausstieg und Rückgabe der Daten](../../../../../eng-us/help/government/procurement/tender-templates/exit-and-data-return-clause.md)).

## Ergebnis

Ein kurzer, konkreter Plan als Anlage zum Vertrag, dem ein Nachfolger folgen könnte.

## Fallstricke

- Ein Plan, der sagt „der Lieferant übergibt die Daten“, ohne Formate oder eine Prüfung.
- Schlüssel vergessen, die sich nicht neu erzeugen lassen.
- Den Export nie testen.

## Verwandte Seiten

- [Aufgaben zu Beschaffung und Schulung](README.md)
- [Ausstiegspläne](../../../../../eng-us/help/government/procurement/exit-plans/README.md)
- [Bereitschaft für den Ausstieg](../../checklisten/bereitschaft-fuer-den-ausstieg.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
