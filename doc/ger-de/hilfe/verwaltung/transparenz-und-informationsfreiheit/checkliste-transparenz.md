---
title: Checkliste Transparenz
audience: government, project-manager, admin, editor
category: public-sector
order: 210
summary: Eine Checkliste für den Betrieb von Transparenz- und Informationsfreiheitsveröffentlichungen mit Exponential, von Struktur und Rollen über die Bearbeitung von Anträgen und Einstellungen zur Rechenschaft bis zur jährlichen Überprüfung, mit Verweis auf die Seite, die jeden Punkt erklärt.
keywords: Checkliste, Transparenz, Informationsfreiheit, Offenlegungsprotokoll, Veröffentlichungsplan, Audit, Versionen, Überprüfung, Antragsformular, checklist, transparency, freedom of information, FOI, disclosure log, publication scheme, review, request form
type: help
---
# Checkliste Transparenz

Diese Checkliste richtet sich an die Menschen, die Transparenzveröffentlichungen betreiben. Gehen Sie sie beim Start
und einmal im Jahr durch. Jeder Punkt nennt die Seite dieses Bereichs, die ihn erklärt.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Struktur und Rollen](#struktur-und-rollen)
2. [Anträge](#anträge)
3. [Veröffentlichen](#veröffentlichen)
4. [Einstellungen zur Rechenschaft](#einstellungen-zur-rechenschaft)
5. [Jährliche Überprüfung](#jährliche-überprüfung)

## Struktur und Rollen

- [ ] Kategorien der proaktiven Veröffentlichung, Verantwortliche und Häufigkeiten festgelegt (siehe „Proaktive
  Veröffentlichung“).
- [ ] Veröffentlichungsplan veröffentlicht und aktuell (siehe „Veröffentlichungspläne“).
- [ ] Eine Sektion pro verantwortlichem Team; Rollen auf ihre Sektionen beschränkt.
- [ ] Freigabe-Workflow auf aktenrelevanten Sektionen; Workflow-Cronjob eingeplant (siehe „Freigabe vor dem
  Veröffentlichen“).

## Anträge

- [ ] Ein Antragsformular mit minimalen Feldern, einem Datenschutztext und Spamschutz (siehe „Ein Antragsformular mit
  dem Informationssammler“).
- [ ] `infocollector/read` nur für das Antragsteam (siehe „Anträge aus gesammelten Informationen bearbeiten“).
- [ ] Einsendungen in den Vorgangsablauf überführt und nach der Aufbewahrungsregel von der Website entfernt.
- [ ] Fristen werden außerhalb von Exponential verfolgt, das keine Vorgangsverwaltung hat.

## Veröffentlichen

- [ ] Offenlegungsprotokoll ohne Daten der Antragsteller (siehe „Offenlegungsprotokolle“).
- [ ] Schwärzung außerhalb von Exponential vorgenommen und von einer zweiten Person geprüft (siehe „Schwärzen ist
  Handarbeit“).
- [ ] Sitzungen, Beschlüsse, Ausgaben und Verträge in vereinbarten Strukturen veröffentlicht (siehe ihre Seiten).
- [ ] Kurze Inhalte als Seitentext; Tabellen auch als CSV (siehe „PDF und Dokumentformate für Offenlegungen“).
- [ ] Veröffentlichungszeitpunkte mit *Bis Datum warten* gesteuert; nie der Cronjob unpublish für Schriftgut (siehe
  „Zeitgesteuertes Veröffentlichen und Sperrfristen“).

## Einstellungen zur Rechenschaft

- [ ] Versionsgrenzen für aktenrelevante Klassen angehoben (siehe „Versionen länger aufbewahren“).
- [ ] `content/versionremove` und `content/remove` für aktenrelevante Sektionen beschränkt.
- [ ] Prüfprotokoll eingeschaltet, Aufbewahrung an den Aufbewahrungsplan angepasst, `exp:audit verify` regelmäßig
  ausgeführt (siehe „Das Prüfprotokoll für ‚wer hat was geändert‘“).
- [ ] Audit-Schlüssel in `settings/override/audit.ini.append.php` gesichert.

## Jährliche Überprüfung

- [ ] Suchstatistiken auf ungedeckten Bedarf geprüft (siehe „Suche und Auffindbarkeit von Offenlegungen“).
- [ ] URL-Verwaltung auf defekte Links geprüft.
- [ ] Rollen und Gruppenmitgliedschaften überprüft; Ausgeschiedene entfernt.
- [ ] Berichtigungen und Rücknahmen auf sichtbare Hinweise überprüft (siehe „Veröffentlichte Informationen
  zurücknehmen und berichtigen“).

## Verwandte Seiten

- [Transparenz in Exponential auf einen Blick](transparenz-in-exponential-auf-einen-blick.md)
- [Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
