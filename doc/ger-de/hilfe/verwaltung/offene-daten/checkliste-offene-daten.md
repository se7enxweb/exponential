---
title: Checkliste offene Daten
audience: government, project-manager, editor, admin
category: public-sector
order: 180
summary: Eine Checkliste für den Start und den Betrieb eines Open-Data-Katalogs mit Exponential, von den ersten Entscheidungen bis zur jährlichen Überprüfung, mit einem Link zu der Seite, die jeden Punkt erklärt.
keywords: Checkliste, offene Daten, Start, Katalog, Datensatz, Lizenz, Datenschutz, stabile URL, Feed, API, Überprüfung, Qualität, checklist, open data, launch, catalog, dataset, license, privacy, stable URL, review, quality
type: help
---
# Checkliste offene Daten

Diese Checkliste richtet sich an Projektleitungen und Datenteams. Haken Sie jeden Punkt vor dem Start ab und überprüfen
Sie sie einmal im Jahr. Jeder Punkt nennt die Seite dieses Bereichs, die ihn erklärt.

## Inhalt

1. [Entscheidungen](#entscheidungen)
2. [Aufbau](#aufbau)
3. [Jeder Datensatz](#jeder-datensatz)
4. [Schnittstellen](#schnittstellen)
5. [Betrieb](#betrieb)

## Entscheidungen

- [ ] Die Stelle weiß, welche Regeln für offene Daten für sie gelten (siehe „Was offene Daten für eine öffentliche
  Stelle bedeuten“; Rechtsberatung).
- [ ] Eine Bestandsaufnahme der Datensätze liegt vor, mit einer verantwortlichen Stelle pro Datensatz.
- [ ] Die genehmigten Lizenzen sind aufgelistet (siehe „Datensätze lizenzieren“).
- [ ] Es ist entschieden, ob ein nationales Portal den Katalog einsammeln muss, und wie (siehe „DCAT und nationale
  Portale“).
- [ ] Der Ablauf der Datenschutzprüfung ist mit den Datenschutzbeauftragten abgestimmt (siehe „Datenschutzprüfung vor
  dem Veröffentlichen von Daten“).

## Aufbau

- [ ] Die Klassen Datensatz und Distribution existieren, mit Pflichtfeldern für Lizenz, Herausgeber und Kontakt (siehe
  „Eine Datensatzklasse entwerfen“).
- [ ] Ein Katalogordner in einer eigenen Sektion, für Anonymous lesbar (siehe „Wer Datensätze veröffentlichen darf“).
- [ ] Eine Rolle für die Datenveröffentlichung und ein Freigabe-Workflow; die Cronjob-Gruppe `frequent` läuft.
- [ ] Eine Zeilenansicht für Datensätze; die Katalogseite zeigt Lizenz, Formate und Daten (siehe „Eine
  Datenkatalogseite“).
- [ ] Themen als Tags oder Auswahl (siehe „Schlagworte und Themen für Datensätze“).

## Jeder Datensatz

- [ ] Titel, Beschreibung, Herausgeber, Kontaktpostfach des Teams, Lizenz, Daten und Aktualisierungshäufigkeit
  ausgefüllt.
- [ ] Dateien in offenen, maschinenlesbaren Formaten, UTF-8, mit stabilen Namen.
- [ ] Datenschutzprüfung durchgeführt und festgehalten; Dateieigenschaften entfernt.
- [ ] Download abgemeldet getestet.
- [ ] Ein kurzer, dauerhafter Alias („Zitieren als“) hinzugefügt (siehe „Stabile URLs für Datensätze“).

## Schnittstellen

- [ ] Ein RSS- oder Atom-Feed neuer Datensätze, vom Katalog aus verlinkt (siehe „Feeds für neue und geänderte
  Datensätze“).
- [ ] REST-API: authentifiziert oder anonym entschieden und dokumentiert (siehe „Die REST-API als
  Datenschnittstelle“).
- [ ] Massenexporte von als Inhalt geführten Registern sind wiederholbar (Voreinstellung oder Befehl) (siehe „Inhalte
  als CSV, JSON oder XML exportieren“).

## Betrieb

- [ ] Aktualisierungen folgen dem vereinbarten Muster (eine aktuelle Datei oder eine Datei pro Zeitraum) (siehe
  „Datensätze versionieren und aktualisieren“).
- [ ] Zurückgezogene Datensätze behalten ihre Seite mit einem Hinweis; nichts wird entfernt.
- [ ] Die Grenzen der Versionsgeschichte passen zu den Anforderungen an die Aufbewahrung.
- [ ] **Setup > URL-Verwaltung** und **Setup > Suchstatistiken** werden auf defekte Links und erfolglose Suchen
  überprüft.
- [ ] Rollen und die Gruppe Datenteam werden einmal im Jahr überprüft.

## Verwandte Seiten

- [Was offene Daten für eine öffentliche Stelle bedeuten](was-offene-daten-bedeuten.md)
- [Offene Daten in Exponential auf einen Blick](offene-daten-in-exponential-auf-einen-blick.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
