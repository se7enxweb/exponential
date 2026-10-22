---
title: Verträge mit Kunden
audience: reseller, partner
category: reselling
order: 350
summary: Was der Vertrag eines Wiederverkäufers mit einem Endkunden für ein Angebot auf Basis von Exponential enthalten sollte - Vertragsparteien und Subunternehmer, Leistungsumfang, Service-Levels, Lizenzerklärung, Eigentum, Auftragsverarbeitung, Sicherheit, Änderungen, Laufzeit und Verlängerung, Ausstieg und Übergabe -, mit einer Checkliste der Klauseln. Keine Rechtsberatung.
keywords: Vertrag, Kundenvertrag, Vereinbarung, Bedingungen, Leistungsumfang, Service-Levels, Lizenz, Eigentum, Auftragsverarbeitung, AVV, Subunternehmer, Gewährleistung, Haftung, Laufzeit, Verlängerung, Kündigung, Ausstieg, Übergabe, contract, customer contract, agreement, terms, scope, service levels, license, ownership, data processing, DPA, subcontractors, warranty, liability, term, renewal, termination, exit, handover
type: help
---
# Verträge mit Kunden

Diese Seite ist für Inhaber und Kundenbetreuer, die den Vertrag zwischen einem Wiederverkäufer und einem Endkunden
schreiben oder prüfen. Sie listet auf, was ein solcher Vertrag abdecken sollte, wenn das Angebot auf Exponential
aufbaut. Sie ist **keine Rechtsberatung**: Lassen Sie Ihre Vertragsvorlage von einem Anwalt in Ihrer
Rechtsordnung prüfen.

## Inhalt

1. [Die Teile des Vertrags](#die-teile-des-vertrags)
2. [Klauseln, die Exponential betreffen](#klauseln-die-exponential-betreffen)
3. [Subunternehmer, einschließlich 7x](#subunternehmer-einschließlich-7x)
4. [Ausstieg und Übergabe](#ausstieg-und-übergabe)
5. [Checkliste der Klauseln](#checkliste-der-klauseln)

## Die Teile des Vertrags

| Teil | Inhalt | Seite |
|---|---|---|
| Vertragsparteien | Ihr Unternehmen und der Kunde; Sie sind für Ihre Subunternehmer verantwortlich | unten |
| Leistungsumfang | Websites, Siteaccesses, Sprachen, Erweiterungen, Dienstleistungen der Stufe | [Angebote in Stufen paketieren](../angebote/angebote-in-stufen-paketieren.md) |
| Service-Levels | Supportzeiten, Reaktionszeiten, Verfügbarkeit, Datensicherungen, Sicherheits-Releases | [Service-Levels gestalten](../angebote/service-levels-gestalten.md) |
| Zuständigkeiten | Wer was tut (eine Zuständigkeitsmatrix) | [Wiederverkäufer verwalteter Dienste](../modelle/wiederverkaeufer-verwalteter-dienste.md#eine-zuständigkeitsmatrix-für-den-vertrag) |
| Lizenzerklärung | Exponential steht unter der GPL v2 oder später; keine Lizenzgebühr | unten |
| Eigentum | Inhalte, Domain, eigener Code, Design-Assets, Konten | [Agenturen, die an Endkunden weiterverkaufen](../modelle/agenturen-die-an-endkunden-weiterverkaufen.md#wem-was-gehört) |
| Auftragsverarbeitung | Bedingungen der Verarbeitung, Unterauftragsverarbeiter, Speicherort der Daten, Meldung von Datenpannen | [Datenschutz für Wiederverkäufer](../verantwortlichkeiten/datenschutz.md) |
| Sicherheit | Updates, Umgang mit Vorfällen, Pflichten des Kunden (Passwörter, Konten) | [Sicherheitspflichten von Wiederverkäufern](../verantwortlichkeiten/sicherheitspflichten.md) |
| Änderungen | Wie Änderungswünsche gestellt und bepreist werden | [Änderungswünsche](../../kunden/partner-und-hoster/aenderungsanfragen.md) |
| Gebühren und Rechnungsstellung | Preise, Zahlungsbedingungen, Preisänderungen mit Ankündigungsfrist | [Rechnungsstellung und Steuern](../verantwortlichkeiten/rechnungsstellung-und-steuern.md) |
| Laufzeit und Verlängerung | Dauer, automatische Verlängerung, Kündigungsfristen | [Verlängerungen und Abwanderung](verlaengerungen-und-abwanderung.md) |
| Ausstieg | Export, Übergabe, Löschung der Daten | [Übergabe](uebergabe.md) |
| Gewährleistung und Haftung | Was Sie gewährleisten, Grenzen | Ihr Anwalt |

## Klauseln, die Exponential betreffen

```text
Software. The site runs on Exponential, free software under the GNU General Public License, version 2 or
any later version. No license fee is charged for the software. Fees under this contract are for the
services described in Schedule <n>.

Customer's rights. Nothing in this contract restricts the rights the Customer has under the GNU General
Public License for copies of the software it receives.

Releases. <Company> applies Exponential releases as described in the service levels. Fixes to Exponential
itself are made by the Exponential project and published as new releases; <Company> does not warrant
that a particular fix will be published by a particular date.

Warranty. <Company> warrants its own services as described in Schedule <n>. The GNU GPL provides the
software without warranty; any warranty is given by <Company> under this contract.
```

Die GPL erlaubt Ihnen, „offer warranty protection in exchange for a fee“ (gegen eine Gebühr eine Gewährleistung
anzubieten, Abschnitt 1); die Software selbst kommt vom Projekt ohne Gewährleistung
([Lizenzen für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md)).

## Subunternehmer, einschließlich 7x

- Benennen Sie sich selbst als gegenüber dem Kunden verantwortlich für jede Dienstleistung, auch für die, die von
  Subunternehmern erbracht werden.
- Der Partnervertrag sagt „Partners are fully responsible for their subcontractors“ (Partner sind voll für ihre
  Subunternehmer verantwortlich, <https://exponential.earth/partner/program>); dieselbe Logik gilt, wenn 7x Ihr
  Subunternehmer ist ([White-Label-Wiederverkäufer](../modelle/white-label-wiederverkaeufer.md)).
- Datenschutzrecht kann verlangen, dass Sie Unterauftragsverarbeiter auflisten; prüfen Sie Ihres.

## Ausstieg und Übergabe

Eine gute Ausstiegsklausel erleichtert den Verkauf. Legen Sie fest:

- [ ] was der Kunde beim Ausstieg erhält: einen Datenbank-Dump, `var/storage`, `settings/override` und
      `settings/siteaccess`, eigenen Code, dessen Übergabe Sie vereinbart haben, Dokumentation;
- [ ] innerhalb wie vieler Tage, in welchem Format und wie Passwörter übermittelt werden (nie per einfacher E-Mail);
- [ ] dass die Domain auf den Namen des Kunden läuft;
- [ ] wann und wie Sie die Daten des Kunden nach dem Ausstieg löschen;
- [ ] den Preis für Ausstiegsarbeiten über den enthaltenen Export hinaus, falls es ihn gibt.

Details: [Übergabe](uebergabe.md) und die eigene [Checkliste für die Übergabe](../../kunden/partner-und-hoster/checkliste-uebergabe.md) des Kunden.

## Checkliste der Klauseln

- [ ] Vertragsparteien und Verantwortung für Subunternehmer.
- [ ] Leistungsumfang mit Websites, Sprachen, Erweiterungen.
- [ ] Service-Levels, die Ihr eigenes Personal einhalten kann.
- [ ] Korrekte Lizenzerklärung; keine „Lizenzgebühr“ für die Software.
- [ ] Eigentum an Inhalten, Domain, eigenem Code, Design-Assets.
- [ ] Bedingungen zur Auftragsverarbeitung und Liste der Unterauftragsverarbeiter.
- [ ] Sicherheit: Release-Rhythmus, Meldung von Vorfällen, Pflichten des Kunden.
- [ ] Änderungswünsche und Preisgestaltung.
- [ ] Gebühren, Preisänderungen mit Ankündigungsfrist, Zahlungsbedingungen.
- [ ] Laufzeit, Verlängerung, Kündigungsfristen.
- [ ] Ausstieg, Übergabe und Löschung.
- [ ] Von einem Anwalt geprüft.

## Verwandte Seiten

- [Verträge und SLAs (für Website-Betreiber)](../../kunden/partner-und-hoster/vertraege-und-slas.md)
- [Vorlage für einen Wartungsvertrag](../../kunden/partner-und-hoster/vorlage-wartungsvereinbarung.md)
- [Die Partnerschaftsvereinbarung](../../partner/die-partnerschaftsvereinbarung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
