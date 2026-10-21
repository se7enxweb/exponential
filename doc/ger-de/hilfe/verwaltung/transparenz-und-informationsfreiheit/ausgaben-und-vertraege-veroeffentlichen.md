---
title: Ausgaben und Verträge veröffentlichen
audience: government, editor, website-creator
category: public-sector
order: 110
summary: Wie Sie mit Exponential Ausgabenberichte, Verträge und Vergabebekanntmachungen veröffentlichen - als Dokumente in einer datierten Struktur, als Register in Form von Inhalten oder beides, mit Exporten als offene Daten und Prüfungen auf Geschäftsgeheimnisse und personenbezogene Daten.
keywords: Ausgaben, Aufwendungen, Verträge, Vertragsregister, Vergabebekanntmachungen, Transparenz, Haushalt, Zahlungen, CSV, Register, öffentliche Ausgaben, spending, expenditure, contracts, contract register, procurement notices, transparency, budget, payments, public spending
type: help
---
# Ausgaben und Verträge veröffentlichen

Diese Seite richtet sich an Teams aus Finanzen, Beschaffung und Web, die veröffentlichen, was eine öffentliche Stelle
ausgibt und welche Verträge sie schließt. Ob, wie oft und wie detailliert Sie veröffentlichen müssen, hängt von Ihrer
Rechtsordnung und Ihrer eigenen Richtlinie ab. In Exponential können Sie die Informationen als Dokumente, als Register
aus Inhaltsobjekten oder beides veröffentlichen.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Zwei Wege zu veröffentlichen](#zwei-wege-zu-veröffentlichen)
2. [Dokumente in einer datierten Struktur](#dokumente-in-einer-datierten-struktur)
3. [Ein Vertragsregister als Inhalt](#ein-vertragsregister-als-inhalt)
4. [Maschinenlesbare Kopien](#maschinenlesbare-kopien)
5. [Was Sie vor dem Veröffentlichen prüfen sollten](#was-sie-vor-dem-veröffentlichen-prüfen-sollten)

## Zwei Wege zu veröffentlichen

| | Dokumente | Register als Inhalt |
|---|---|---|
| Was | der Bericht oder Vertrag als Datei (PDF, CSV) | ein Objekt pro Vertrag oder pro Gruppe von Zahlungszeilen, mit Feldern |
| Aufwand | gering | eine Klasse und Dateneingabe oder Import |
| Suchen und filtern | nach Dokumenttitel und Text | nach jedem Feld |
| Offene Daten | die CSV-Datei selbst | das Register als CSV, JSON oder XML exportieren |
| Gut für | regelmäßige Ausgabenberichte | Vertragsregister, in denen Menschen stöbern |

## Dokumente in einer datierten Struktur

1. Ein Ordner „Ausgaben“ mit einem Ordner pro Jahr, und pro Monat oder Quartal, wenn Sie so oft veröffentlichen.
2. Jeder Bericht als Dateiobjekt mit einem klaren Namen („Ausgaben über dem Schwellenwert, März 2026“) sowohl im
   Dokumentformat (PDF) als auch in einem maschinenlesbaren Format (CSV).
3. Eine Einleitung, die Schwellenwerte erklärt, was ausgenommen ist und warum.
4. Ein RSS-Export des Ordners „Ausgaben“ mit Unterknoten ([RSS-Feeds](../../../anleitungen/rss-feeds.md)).

## Ein Vertragsregister als Inhalt

Eine Klasse `contract` ([Inhaltsklassen für Websites](../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md)):

| Attribut | Datentyp |
|---|---|
| Titel (Gegenstand des Vertrags) | Textzeile |
| Aktenzeichen | Textzeile |
| Auftragnehmer (Name der Organisation) | Textzeile |
| Wert | Fließkommawert oder Preis, mit Angabe der Währung |
| Beginn, Ende | Datum |
| Verfahren | Auswahl |
| Abteilung | Auswahl oder Tags |
| Vertragsdokument (geschwärzt) | Datei |

Datensätze für das Register: Exportieren Sie es mit `xrowextract` (siehe „Inhalte als CSV, JSON oder XML exportieren“
im Bereich offene Daten). Der Audit-Trail zeichnet Änderungen an jedem Eintrag auf.

Masseneingabe: `xrowextract` kann CSV-, JSON- oder XML-Dateien mit einer Spaltenzuordnung und einem Probelauf in
Inhaltsobjekte importieren ([xrowextract](../../../../eng-us/features/6.0/extensions/xrowextract.md)). Probieren Sie einen Import
zuerst auf einer Testinstallation aus.

## Maschinenlesbare Kopien

Veröffentlichen Sie Ausgabendaten neben jedem PDF als CSV, in UTF-8 mit einer Kopfzeile, einer Zeile pro Zahlung oder
Vertrag und in jedem Zeitraum mit denselben Spaltennamen. Das macht die Veröffentlichung zugleich zu offenen Daten
(siehe den Bereich offene Daten).

## Was Sie vor dem Veröffentlichen prüfen sollten

- [ ] Zahlungen an Einzelpersonen (zum Beispiel Einzelunternehmer, Entschädigungen, Leistungen) werden nach Ihren
  Datenschutzregeln behandelt; Namen von Privatpersonen werden meist entfernt oder zusammengefasst.
- [ ] Geschäftsgeheimnisse in Verträgen sind mit dokumentierten Gründen geschwärzt (siehe „Schwärzen ist
  Handarbeit“).
- [ ] Die Summen stimmen mit der amtlichen Rechnungslegung überein; die Einleitung sagt, wie sie ermittelt wurden.
- [ ] Dateinamen enthalten keine personenbezogenen Daten.

## Verwandte Seiten

- [Proaktive Veröffentlichung](proaktive-veroeffentlichung.md)
- [xrowextract](../../../../eng-us/features/6.0/extensions/xrowextract.md)
- [Produkte, Preise und Mehrwertsteuer](../../../../eng-us/help/creators/website-creators/products-prices-and-vat.md): der Datentyp Preis im Shop-Kontext
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
