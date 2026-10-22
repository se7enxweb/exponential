---
title: Budgetposten für Hosting und Wartung
audience: non-profit, businesses, project-manager, hosting
category: non-profits
order: 40
summary: Die Budgetposten, die eine Non-Profit-Organisation für eine Exponential-Website einplanen sollte, während einer Förderung und in den Jahren danach - was null ist (Lizenz), was nie null ist (Hosting, Domain, Updates, Sicherungen, die Zeit von Menschen), was optional ist (Unterstützung durch Partner, Übersetzung, E-Mail-Zustellung, Zahlungsgebühren) - ohne erfundene Preise und mit Links zu den Kostenseiten für die Berechnung.
keywords: Budget, Budgetposten, Hostingkosten, Wartungskosten, Förderbudget, laufende Kosten, Domain, Sicherungen, Updates, Unterstützung durch Partner, Übersetzung, Zeit der Ehrenamtlichen, Sachleistung, Schatzmeister, budget lines, hosting cost, maintenance cost, grant budget, running costs, backups, partner support, volunteer time, in-kind, treasurer
type: help
---
# Budgetposten für Hosting und Wartung

Diese Seite richtet sich an Schatzmeister und Antragsschreiber. Sie listet die Posten auf, die ein Website-Budget
braucht, sagt, welche davon null sein können, und verweist auf die Seiten, die die Berechnung übernehmen. Sie nennt
**keine Preise**: Holen Sie Angebote ein und füllen Sie das [Arbeitsblatt Förderbudget](../vorlagen/arbeitsblatt-foerderbudget.md) aus.

## Inhalt

1. [Posten, die null sind](#posten-die-null-sind)
2. [Posten, die nie null sind](#posten-die-nie-null-sind)
3. [Posten, die von Ihren Entscheidungen abhängen](#posten-die-von-ihren-entscheidungen-abhängen)
4. [Einmalig und wiederkehrend](#einmalig-und-wiederkehrend)
5. [Die Zeit der Ehrenamtlichen zählen](#die-zeit-der-ehrenamtlichen-zählen)
6. [Das Jahr nach der Förderung](#das-jahr-nach-der-förderung)
7. [Checkliste für das Budget](#checkliste-für-das-budget)

## Posten, die null sind

| Posten | Warum er null ist | Quelle |
|---|---|---|
| Softwarelizenz | GPL, „free of charge“ (kostenlos) | `README.md`, „License“ |
| Gebühren pro Redakteur, pro Site, pro Sprache | Es gibt keine Lizenz, gegen die gezählt würde | [Lizenzierung für Käufer](../../geschaeft/lizenzen-fuer-kaeufer.md) |
| Newsletter, Formulare, Baukasten für Landingpages | Mit dem Produkt installiert (`cjw_newsletter`, Information Collector, Exponential Layouts) | `composer.json` |
| Lizenz für einen Datenbankserver | SQLite braucht keinen Server | [Unterstützung der SQLite-Datenbank](../../../funktionen/6.0/sqlite-datenbank.md) |

## Posten, die nie null sind

| Posten | Was er abdeckt | Lesen |
|---|---|---|
| Hosting | Ein Shared-Hosting-Konto, ein kleiner virtueller Server oder Managed Hosting | [Hosting mit kleinem Budget](../betrieb/hosting-mit-kleinem-budget.md), [Kostentreiber beim Hosting](../../unternehmen/kosten/kostentreiber-beim-hosting.md) |
| Domainname | Jährliche Registrierung | Ihr Registrar |
| Updates | Updates von Exponential, Erweiterungen und PHP einspielen, an einer Kopie getestet | [Updates und Upgrades](../../ersteller/website-ersteller/updates-und-aktualisierungen.md) |
| Sicherungen und Wiederherstellungstests | Speicher für Sicherungen und die Zeit, eine Wiederherstellung zu testen | [Routine für Sicherungen und Updates](../betrieb/routine-fuer-sicherungen-und-updates.md) |
| Eine verantwortliche Person | Jemand, der die Site jeden Monat prüft | [Monatliche Checkliste](../checklisten/monatliche-checkliste.md) |

Auch wenn eine ehrenamtliche Person die Arbeit erledigt, ist die Zeit real. Planen Sie sie ein oder weisen Sie sie als
Sachleistung aus, wenn Ihr Geldgeber das akzeptiert.

## Posten, die von Ihren Entscheidungen abhängen

| Posten | Nötig, wenn | Lesen |
|---|---|---|
| Design und Umsetzung | Sie keine fachkundige ehrenamtliche Person haben | [Einen Partner auswählen](../../unternehmen/auswahl/einen-partner-auswaehlen.md) |
| Support-Vereinbarung mit einem Partner | Sie garantierte Reaktionszeiten möchten | [Mit einem Partner arbeiten](../betrieb/mit-einem-partner-arbeiten.md), [Vorlage für eine Support-Vereinbarung](../../kunden/partner-und-hoster/vorlage-support-vereinbarung.md) |
| E-Mail-Zustelldienst | Newsletter an viele Abonnenten | [Zustellung von Newsletter-E-Mails](../../unternehmen/newsletters/zustellung-der-newsletter-mails.md) |
| Gebühren des Zahlungsanbieters | Spenden über den Shop oder eine Plattform | [Spendenwege im Überblick](../spenden/spendenwege-im-ueberblick.md) |
| Übersetzung | Mehr als eine Sprache | [Mehrsprachige Sites](../kommunikation/mehrsprachige-non-profit-websites.md) |
| Analysedienst | Besucherzahlen über das hinaus, was Exponential zählt | [Wirkung mit Website-Daten messen](wirkung-mit-website-daten-messen.md) |
| Prüfung der Barrierefreiheit | Eine externe Prüfung vor dem Start | [Ihr öffentliches Design prüfen](../../verwaltung/barrierefreiheit/ihr-oeffentliches-design-pruefen.md) |
| Anbieter des Schreibassistenten | Exponential Intelligence mit einem kostenpflichtigen Anbieter | [Exponential Intelligence](../../kunden/wachstum/exponential-intelligence.md) |
| Schulung | Redakteure, die das System noch nicht kennen | [Ehrenamtliche Redakteure schulen](../ehrenamtliche/ehrenamtliche-redakteure-schulen.md) |

## Einmalig und wiederkehrend

| Einmalig (Projekt) | Wiederkehrend (jedes Jahr) |
|---|---|
| Analyse und Planung | Hosting |
| Design und Umsetzung | Domain |
| Texte schreiben und übersetzen | Updates und die jährliche Upgrade-Prüfung |
| Tests der Barrierefreiheit | Sicherungen und Wiederherstellungstests |
| Schulung | Die Zeit einer verantwortlichen Person |
| Start | Unterstützung durch einen Partner, falls vorhanden |

Geldgeber finanzieren oft die linke Spalte; Ihr eigenes Budget muss die rechte tragen. Sagen Sie im Antrag, was wozu
gehört.

## Die Zeit der Ehrenamtlichen zählen

Das durchgerechnete Beispiel für eine Non-Profit-Organisation zeigt, wie Sie ehrenamtliche Stunden ehrlich zählen, mit
Beispielannahmen ([Durchgerechnetes Beispiel: eine Non-Profit-Organisation](../../unternehmen/kosten/durchgerechnetes-beispiel-gemeinnuetzige-organisation.md)).
Kurz gesagt: Listen Sie jede wiederkehrende Aufgabe auf, schätzen Sie die Stunden pro Monat und multiplizieren Sie sie
mit einem Satz, den Ihr Geldgeber für Sachleistungen akzeptiert.

## Das Jahr nach der Förderung

Planen Sie mindestens die wiederkehrende Spalte für das erste Jahr nach der Förderung ein. Ohne sie ist der
wahrscheinlichste Fehler keine kaputte Site am ersten Tag, sondern versäumte Updates und ungetestete Sicherungen in den
folgenden Monaten; siehe [Budget für Upgrades und Wartung](../../unternehmen/kosten/budget-fuer-aktualisierungen-und-wartung.md).

## Checkliste für das Budget

- [ ] Der Lizenzposten ist mit null und der Begründung ausgewiesen.
- [ ] Hosting, Domain, Updates, Sicherungen und eine verantwortliche Person für jedes Jahr eingeplant.
- [ ] Optionale Posten einzeln entschieden, nicht vergessen.
- [ ] Die Zeit der Ehrenamtlichen gezählt oder als Sachleistung ausgewiesen.
- [ ] Das erste Jahr nach der Förderung abgedeckt.
- [ ] Für jeden Posten mit Preis liegen Angebote bei.

## Verwandte Seiten

- [Fördermittel und die Website](README.md)
- [Arbeitsblatt Förderbudget](../vorlagen/arbeitsblatt-foerderbudget.md)
- [Gesamtbetriebskosten](../../unternehmen/kosten/gesamtbetriebskosten.md)
- [Wofür Sie nicht bezahlen](../../unternehmen/kosten/wofuer-sie-nicht-bezahlen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
