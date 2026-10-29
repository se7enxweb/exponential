---
title: Non-Profit-Organisationen: hier beginnen
audience: non-profit, businesses, government
category: non-profits
order: 1
summary: Die Einstiegsseite für gemeinnützige Organisationen, Stiftungen, Gemeinschaftsorganisationen und andere Non-Profit-Organisationen, die ihre Website mit Exponential betreiben - was es für Fördermittel, Spenden, Ehrenamtliche, Barrierefreiheit, Transparenz, Newsletter, Veranstaltungen und Sprachen gibt, was es nicht gibt und einen externen Dienst braucht, wie Sie die Kosten niedrig halten und wo jede Seite dieses Bereichs zu finden ist.
keywords: Non-Profit-Organisation, gemeinnützig, Verein, Stiftung, NGO, Gemeinschaftsorganisation, hier beginnen, Fördermittel, Spenden, Ehrenamtliche, Barrierefreiheit, Transparenz, Jahresbericht, Vorstandsprotokolle, Newsletter, Veranstaltungen, mehrsprachig, kleines Budget, SQLite, Velocity, Datenschutz, Partner, non-profit, nonprofit, charity, foundation, community organization, start here, grants, donations, volunteers, accessibility, transparency, annual report, board minutes, events, multilingual, low budget, data protection
featured: true
type: help
---
# Non-Profit-Organisationen: hier beginnen

Dieser Bereich richtet sich an Menschen, die die Website einer gemeinnützigen Organisation, einer Stiftung, einer
Gemeinschaftsorganisation, eines Vereins mit öffentlichem Auftrag oder einer anderen Non-Profit-Organisation betreuen:
Geschäftsführungen, Fundraiser, Koordinatoren von Ehrenamtlichen, Kommunikationsmitarbeitende, Schatzmeister sowie die
Ehrenamtlichen oder Partner, die sich um die Technik kümmern. Er erklärt in einfacher Sprache, was Exponential einer
Non-Profit-Organisation bietet, was es nicht bietet, was der Betrieb der Website kosten wird und wie Sie sie mit einem
kleinen Team gesund halten.

> **Hinweis**
> Jede in diesem Bereich genannte Funktion wurde am Repository von Exponential (`composer.json`, das Verzeichnis
> `extension/`, die Einstellungen) oder an einer vorhandenen Hilfeseite geprüft, und diese Seite ist verlinkt. Wo
> etwas nicht existiert, sagt dieser Bereich das klar. Es werden keine Kunden, Statistiken oder Preise erfunden; wo
> eine Seite Zahlen braucht, tragen Sie Ihre eigenen ein.

## Inhalt

1. [Exponential für eine Non-Profit-Organisation in einer Minute](#exponential-für-eine-non-profit-organisation-in-einer-minute)
2. [Was es gibt und was nicht](#was-es-gibt-und-was-nicht)
3. [Wie dieser Bereich aufgebaut ist](#wie-dieser-bereich-aufgebaut-ist)
4. [Lesepfade nach Rolle](#lesepfade-nach-rolle)
5. [Ihr erster Monat](#ihr-erster-monat)
6. [Benachbarte Bereiche](#benachbarte-bereiche)

## Exponential für eine Non-Profit-Organisation in einer Minute

- **Keine Lizenzgebühr.** „Using Exponential under the terms of the GNU GPL is free of charge“ (`README.md`,
  „License“): Die Nutzung unter der GNU GPL ist kostenlos. Das Geld fließt in das Hosting und in die Arbeit, die Sie
  einkaufen möchten, nicht in Lizenzen ([Lizenzierung für Käufer](../geschaeft/lizenzen-fuer-kaeufer.md)).
- **Kleines Hosting ist möglich.** SQLite 3 ist die voreingestellte Datenbank, sodass eine Website ohne
  Datenbankserver laufen kann ([SQLite-Datenbankunterstützung](../../funktionen/6.0/sqlite-datenbank.md)), und der
  mitgelieferte Server Exponential Velocity „needs nothing installed beyond PHP“ (`README.md`, „Requirements“), braucht
  also außer PHP nichts Installiertes.
- **Formulare, Newsletter und Landingpages gehören zum Produkt.** Der Informationssammler für Formulare,
  `cjw_newsletter` für E-Mails an Unterstützer und Exponential Layouts für Kampagnenseiten werden über `composer.json`
  installiert.
- **Rechenschaft ist eingebaut.** Jede veröffentlichte Änderung behält ihre früheren Versionen, und ein
  manipulationssicheres Audit-Protokoll zeichnet auf, wer was getan hat ([Das Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md)).
- **Das Projekt selbst hat ein gemeinnütziges Zuhause**: Es wird von der Exponential Foundation betrieben
  ([Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md)).

## Was es gibt und was nicht

| Bedarf | Was es gibt | Lesen |
|---|---|---|
| Spenden annehmen | **Kein eigenes Spendenmodul.** Verlinken Sie auf eine externe Spendenplattform, oder verkaufen Sie feste Spendenartikel im eingebauten Shop mit `ezpaypal` oder `ezauthorize` | [Spenden](spenden/README.md) |
| Regelmäßige Spenden | Für den Shop nicht dokumentiert; nutzen Sie die externe Plattform | [Spendenwege im Überblick](spenden/spendenwege-im-ueberblick.md) |
| Anmeldung von Ehrenamtlichen | Formulare mit dem Informationssammler, CAPTCHA (`hcaptcha`, `recaptcha`), Datei-Uploads (`enhancedezbinaryfile`) | [Ehrenamtliche](ehrenamtliche/README.md) |
| Ehrenamtliche, die die Website bearbeiten | Rollen, Bereiche, Freigabe-Workflow, Versionen, Zwei-Faktor-Anmeldung | [Ehrenamtliche als Redakteure](ehrenamtliche/ehrenamtliche-als-redakteure.md) |
| Berichte für Fördermittel | Kein Fördermodul; die Website liefert die Nachweise (Seiten, Statistiken, Audit-Protokoll) | [Fördermittel](foerdermittel/README.md) |
| Jahresberichte, Vorstandsprotokolle | Dateien als Downloads, Ordner nach Jahren, Versionen | [Transparenz](transparenz/README.md) |
| Newsletter für Unterstützer | `cjw_newsletter` mit Double-Opt-in | [Unterstützer-Newsletter](kommunikation/unterstuetzer-newsletter-mit-cjw-newsletter.md) |
| Veranstaltungen | Die Klassen `event`, `event_calendar` und `multicalendar` aus `ezwebin`; kein Ticketverkauf, kein iCal-Export | [Veranstaltungen und Kalender](kommunikation/veranstaltungen-und-kalender-fuer-non-profit-organisationen.md) |
| Mehrere Sprachen | Bis zu 62 Inhaltssprachen pro Installation | [Mehrsprachige Websites](kommunikation/mehrsprachige-non-profit-websites.md) |
| Barrierefreiheit | Hinweise für Templates, verpflichtende Alternativtexte, ein barrierefreies Administrationsdesign | [Barrierefreiheit](barrierefreiheit/README.md) |
| Niedrige Betriebskosten | SQLite, Velocity, keine Lizenzgebühr | [Hosting mit kleinem Budget](betrieb/hosting-mit-kleinem-budget.md) |
| Non-Profit-Preise im Partnerprogramm | **Keine veröffentlicht.** Die Partnerpläne haben eine einzige Preisliste | [Mit einem Partner arbeiten](betrieb/mit-einem-partner-arbeiten.md) |

Die vorhandenen Seiten zu [Non-Profit-Organisationen](../unternehmen/gemeinnuetzige/README.md) im Bereich für Unternehmen
behandeln Website-Planung, Spenden, Ehrenamtliche, Kampagnen, Barrierefreiheit und einen Betrieb mit kleinem Budget in
kompakter Form. Dieser Bereich geht weiter und ergänzt Fördermittel, Transparenz, Betrieb, Checklisten und
Arbeitsblätter; er verlinkt auf jene Seiten, statt sie zu wiederholen.

## Wie dieser Bereich aufgebaut ist

| Ordner | Was er behandelt |
|---|---|
| [Fördermittel](foerdermittel/README.md) | Geförderte Website-Projekte, den technischen Teil eines Antrags, Budgetposten, Ergebnisberichte |
| [Spenden](spenden/README.md) | Die beiden praktischen Wege Schritt für Schritt, die Spendenseite und das Vertrauen der Spender |
| [Ehrenamtliche](ehrenamtliche/README.md) | Anmeldeformulare, Spam, Bearbeitung von Bewerbungen, Mitgliederbereiche, Ehrenamtliche, die Inhalte bearbeiten |
| [Barrierefreiheit](barrierefreiheit/README.md) | Die Grundlagen, barrierefreie Kampagnen und Dokumente, eine Erklärung zur Barrierefreiheit |
| [Transparenz](transparenz/README.md) | Jahresberichte, Finanzdokumente, Seiten des Vorstands, Versionen und Audit, offene Daten |
| [Kommunikation](kommunikation/README.md) | Newsletter, Veranstaltungen, Sprachen, Kampagnen und Spendenaufrufe |
| [Betrieb](betrieb/README.md) | Hosting mit kleinem Budget, SQLite und Velocity, Datenschutz, Sicherungen, Abschiede, Partner |
| [Checklisten](checklisten/README.md) | Checklisten zum Start, monatlich, jährlich und zum Datenschutz |
| [Vorlagen](vorlagen/README.md) | Arbeitsblätter zum Ausfüllen: Förderbudget, Ehrenamtsformular, Rollen, Seite für den Jahresbericht |

Kurze Antworten finden Sie in der [FAQ](faq-fuer-non-profit-organisationen.md), die Begriffe im [Glossar](glossar-fuer-non-profit-organisationen.md) und
die vollständige Lesereihenfolge im [Lesepfad](lesepfad-fuer-non-profit-organisationen.md).

## Lesepfade nach Rolle

| Sie sind | Lesen Sie zuerst |
|---|---|
| Geschäftsführung oder Vorstandsmitglied | diese Seite, [FAQ](faq-fuer-non-profit-organisationen.md), [Transparenz](transparenz/README.md), [Mit einem Partner arbeiten](betrieb/mit-einem-partner-arbeiten.md) |
| Fundraiser | [Spenden](spenden/README.md), [Kampagnen und Spendenaufrufe](kommunikation/kampagnen-und-spendenaufrufe.md), [Fördermittel](foerdermittel/README.md) |
| Verfasser von Förderanträgen | [Projektplan für eine geförderte Website](foerdermittel/projektplan-fuer-eine-gefoerderte-website.md), [Budgetposten](foerdermittel/budgetposten-fuer-hosting-und-wartung.md) |
| Koordinator von Ehrenamtlichen | [Ehrenamtliche](ehrenamtliche/README.md), [Datenschutz für Spender und Ehrenamtliche](betrieb/datenschutz-fuer-spender-und-ehrenamtliche.md) |
| Kommunikationsmitarbeitende | [Kommunikation](kommunikation/README.md), [Barrierefreiheit](barrierefreiheit/README.md) |
| Schatzmeister | [Budgetposten](foerdermittel/budgetposten-fuer-hosting-und-wartung.md), [Finanzdokumente](transparenz/finanzdokumente.md), [Hosting mit kleinem Budget](betrieb/hosting-mit-kleinem-budget.md) |
| Technisch versierte Ehrenamtliche oder Partner | [SQLite und Velocity für kleine Teams](betrieb/sqlite-und-velocity-fuer-kleine-teams.md), [Routine für Sicherungen und Updates](betrieb/routine-fuer-sicherungen-und-updates.md) |
| Neu im Thema Websites | [Nicht-Techniker: hier beginnen](../nicht-technische-menschen/nicht-technische-menschen-hier-beginnen.md) |

## Ihr erster Monat

| Woche | Tun | Lesen |
|---|---|---|
| 1 | Schreiben Sie auf, was die Website für Ihren Auftrag leisten muss und wer sie pflegt | [Projektplan für eine geförderte Website](foerdermittel/projektplan-fuer-eine-gefoerderte-website.md) |
| 2 | Entscheiden Sie, wie Spenden und Anmeldungen von Ehrenamtlichen funktionieren | [Spendenwege](spenden/spendenwege-im-ueberblick.md), [Ehrenamtsformular](ehrenamtliche/anmeldeformular-fuer-ehrenamtliche-schritt-fuer-schritt.md) |
| 3 | Geben Sie jedem Redakteur ein Konto und eine Rolle; schalten Sie für Administratoren die Zwei-Faktor-Anmeldung ein | [Arbeitsblatt Rollen und Berechtigungen](vorlagen/arbeitsblatt-rollen-und-berechtigungen.md) |
| 4 | Richten Sie Sicherungen und eine monatliche Routine ein und testen Sie eine Wiederherstellung | [Routine für Sicherungen und Updates](betrieb/routine-fuer-sicherungen-und-updates.md), [Monatliche Checkliste](checklisten/monatliche-checkliste.md) |

![Das Dashboard der Administrationsoberfläche von Exponential](../bilder/admin-dashboard.webp)

## Benachbarte Bereiche

- [Unternehmen: hier beginnen](../unternehmen/unternehmen-hier-beginnen.md) und die dortigen Seiten zu
  [Non-Profit-Organisationen](../unternehmen/gemeinnuetzige/README.md) und zu [kleinen Gruppen und Vereinen](../unternehmen/vereine/README.md).
- [Behörden: hier beginnen](../verwaltung/verwaltungen-hier-beginnen.md): Öffentliche Einrichtungen teilen viele
  Anforderungen mit Non-Profit-Organisationen (Barrierefreiheit, Transparenz, Aktenführung).
- [Kunden: hier beginnen](../kunden/kunden-hier-beginnen.md): eine Website besitzen und im Alltag betreiben.
- [Lokale Unternehmen: hier beginnen](../lokale-unternehmen/lokale-unternehmen-hier-beginnen.md) und
  [Privatpersonen: hier beginnen](../privatpersonen/privatpersonen-hier-beginnen.md).

## Verwandte Seiten

- [FAQ für Non-Profit-Organisationen](faq-fuer-non-profit-organisationen.md)
- [Lesepfad für Non-Profit-Organisationen](lesepfad-fuer-non-profit-organisationen.md)
- [Glossar für Non-Profit-Organisationen](glossar-fuer-non-profit-organisationen.md)
- [Non-Profit-Organisationen im Bereich für Unternehmen](../unternehmen/gemeinnuetzige/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
