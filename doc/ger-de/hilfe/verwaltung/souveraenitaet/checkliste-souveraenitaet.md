---
title: Checkliste Souveränität
audience: government, project-manager, admin
category: public-sector
order: 230
summary: Eine Checkliste für eine öffentliche Stelle, die die Kontrolle über ihre Exponential-Plattform behalten will - Lizenz und Code, Hosting und Datenstandort, externe Dienste, Formate und Exporte, Kompetenzen und Dienstleister, Updates und Ausstieg, jeweils verlinkt mit der Seite, die es erklärt.
keywords: Checkliste, Souveränität, Unabhängigkeit, Abhängigkeit, Selbstbetrieb, Datenstandort, Drittanbieter, Ausstiegsplan, Kompetenzen, Updates, Überprüfung, checklist, sovereignty, independence, lock-in, self-hosting, data location, third parties, exit plan, skills, review
type: help
---
# Checkliste Souveränität

Diese Checkliste richtet sich an Führungskräfte und Administratoren. Verwenden Sie sie zu Beginn eines Projekts, bei
jeder Vertragsverlängerung und einmal im Jahr. Jeder Punkt nennt die Seite dieses Bereichs, die ihn erklärt.

## Inhalt

1. [Lizenz und Code](#lizenz-und-code)
2. [Hosting und Daten](#hosting-und-daten)
3. [Externe Dienste](#externe-dienste)
4. [Formate und Exporte](#formate-und-exporte)
5. [Menschen und Dienstleister](#menschen-und-dienstleister)
6. [Updates und Ausstieg](#updates-und-ausstieg)

## Lizenz und Code

- [ ] Die Lizenzlage ist für die Leitung dokumentiert (siehe „Die Open-Source-Lizenz (GPL)“).
- [ ] Sämtlicher eigens entwickelter Code liegt in einem Repository, das der Stelle gehört (siehe
  „Anbieterabhängigkeit vermeiden“).
- [ ] Keine Kernel-Patches; Anpassungen sind Erweiterungen und Overrides.
- [ ] Von jedem Paket, von dem die Installation abhängt, wird eine Kopie aufbewahrt (siehe „Composer-Pakete und
  Quellcode auf GitHub“).

## Hosting und Daten

- [ ] Das Hostingmodell und der Anbieter sind ausgewählt und dokumentiert (siehe „Verwaltungscloud und
  Hostingmodelle“).
- [ ] Die Erklärung zum Datenstandort listet jeden Ort auf, an dem Daten liegen, einschließlich der Datensicherungen
  (siehe „Datenstandort“).
- [ ] Die Wahl der Datenbank ist mit ihrem Wiederherstellungsverfahren dokumentiert (siehe „Wahl der Datenbank“).

## Externe Dienste

- [ ] Die aktiven Erweiterungen sind bekannt; jeder Dienst eines Drittanbieters wurde geprüft und genehmigt (siehe „Zu
  prüfende Dienste von Drittanbietern“).
- [ ] Öffentliche Designs laden ohne Entscheidung keine Ressourcen von anderen Hosts (siehe „Keine Pflicht zu externen
  Diensten“).

## Formate und Exporte

- [ ] Die wichtigsten Inhaltstypen lassen sich heute als CSV oder JSON exportieren (siehe „Inhalte und Pakete
  exportieren“).
- [ ] Dokumente werden in offenen Formaten veröffentlicht, Tabellen auch als CSV (siehe „Offene Formate und
  Datenportabilität“).

## Menschen und Dienstleister

- [ ] Zwei Personen pro Schlüsselrolle verfügen über die nötigen Kompetenzen (siehe „Das Wissen im Haus halten“).
- [ ] Dienstleister werden nach dokumentierten Kriterien ausgewählt; die Zuständigkeiten sind schriftlich festgehalten
  (siehe „Einen Supportanbieter wählen“).
- [ ] Verträge enthalten die Lieferung des Quellcodes und Unterstützung beim Ausstieg (siehe
  „Open-Source-Beschaffung“).
- [ ] Die Stelle hat entschieden, wie sie Beiträge zurückgibt (siehe „Beiträge als öffentliche Stelle“).

## Updates und Ausstieg

- [ ] Sicherheitsreleases werden beobachtet und innerhalb einer festgelegten Frist eingespielt (siehe
  „Sicherheitsupdates und Unabhängigkeit“).
- [ ] Ein Ausstiegsplan liegt vor, und in diesem Jahr wurde ein Wiederherstellungstest durchgeführt (siehe
  „Ausstiegsplanung“).

## Verwandte Seiten

- [Digitale Souveränität auf einen Blick](digitale-souveraenitaet-auf-einen-blick.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
