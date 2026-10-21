---
title: Shop-Planung für kleine Unternehmen
audience: businesses, project-manager, website-creator
category: business-solutions
order: 10
summary: Die Entscheidungen, die ein kleines Unternehmen trifft, bevor ein Exponential-Shop eingerichtet wird - was verkauft wird, wohin, in welchen Währungen, wie Kunden bezahlen und die Ware erhalten und wer die Bestellungen bearbeitet.
keywords: Shop-Planung, Planung des Web-Shops, kleines Unternehmen, E-Commerce-Plan, Produkte, USt, Versand, Zahlung, Rollen, Anforderungen, store planning, webshop planning, small business, e-commerce plan, products, VAT, shipping, payment, roles, requirements
type: help
---
# Shop-Planung für kleine Unternehmen

Diese Seite richtet sich an Inhaber und Verantwortliche kleiner Unternehmen, die einen Online-Shop mit Exponential
planen. Sie listet die Entscheidungen auf, die fallen sollten, bevor jemand den Shop konfiguriert, erklärt, warum jede
davon wichtig ist, und verweist auf die Seite, die sie behandelt. Nutzen Sie sie als Tagesordnung Ihres ersten
Planungstreffens mit der Person, die den Shop baut.

## Inhalt

1. [Warum zuerst planen](#warum-zuerst-planen)
2. [Die zehn Entscheidungen](#die-zehn-entscheidungen)
3. [Personen und Rollen](#personen-und-rollen)
4. [Was der Shop Ihnen bietet und was Sie ergänzen](#was-der-shop-ihnen-bietet-und-was-sie-ergänzen)
5. [Ein Planungsarbeitsblatt](#ein-planungsarbeitsblatt)
6. [Nach der Planung](#nach-der-planung)

## Warum zuerst planen

In Exponential sind die meisten Entscheidungen für einen Shop Einstellungen, Rollen und Inhaltsklassen, kein Code. Sie
später zu ändern ist möglich, aber manche Änderungen betreffen jede Bestellung oder jedes Produkt: der Wechsel von
einer Währung zu mehreren, die Einführung von USt-Regeln nach Land oder eine Änderung, wie Kundendaten an der Kasse
erfasst werden. Wer das früh entscheidet, hält die Einrichtung kurz und die ersten Testbestellungen aussagekräftig.

## Die zehn Entscheidungen

| # | Entscheidung | Warum sie wichtig ist | Lesen Sie |
|---|---|---|---|
| 1 | Was Sie verkaufen: physische Waren, Dienstleistungen, Dateien | Bestimmt Versand, Optionen und Downloads | [Produktklassen und Katalog](produktklassen-und-katalog.md) |
| 2 | Wie viele Produkte und wie sie gruppiert sind | Bestimmt Ordner, Menüs und Suche | [Suche und Navigation im Shop](suche-und-navigation-im-shop.md) |
| 3 | Optionen pro Produkt (Größe, Farbe) | Bestimmt die Preis- und Optionsfelder | [Produktklassen und Katalog](produktklassen-und-katalog.md) |
| 4 | Eine Währung oder mehrere | Bestimmt `ezprice` oder `ezmultiprice` | [Preise und Währungen](preise-und-waehrungen.md) |
| 5 | In welche Länder Sie verkaufen | Bestimmt USt-Regeln und die Länderliste | [Ländergruppen und Regionen](laendergruppen-und-regionen.md) |
| 6 | Wie Steuern berechnet werden | Bestimmt USt-Typen und -Regeln | [USt-Regeln und USt-Typen](ust-regeln-und-ust-typen.md) |
| 7 | Wie die Ware zu den Kunden kommt | Bestimmt den Versand-Workflow oder -Handler | [Versand und Bearbeitung](versand-und-bearbeitung.md) |
| 8 | Wie Kunden bezahlen | Bestimmt den Zahlungsanbieter | [Zahlungsanbieter für Unternehmen](zahlungsanbieter-fuer-unternehmen.md) |
| 9 | Konten oder Gastbestellung | Bestimmt den Shop-Account-Handler | [Kundenkonten und Gastbestellung](kundenkonten-und-gastbestellung.md) |
| 10 | Welche Bestellstatus Sie verwenden | Bestimmt den täglichen Arbeitsablauf | [Bestellungen und Bestellstatus](bestellungen-und-bestellstatus.md) |

## Personen und Rollen

Die Berechtigungen des Shops sind in `kernel/shop/module.php` in Funktionen aufgeteilt: `buy`, `administrate`,
`setup`, `edit_status` und `setstatus` ([Einen Shop betreiben: Überblick](../../ersteller/website-ersteller/shop-ueberblick.md)).
Mit dieser Aufteilung kann ein kleines Unternehmen jeder Person nur das geben, was ihre Aufgabe braucht.

| Person | Typische Richtlinie | Tut |
|---|---|---|
| Besucher und Kunden | `shop/buy` | Warenkorb, Kasse, die eigenen Bestellungen |
| Bestellbearbeitung | `shop/administrate`, `shop/setstatus` | Bestellliste, Dashboard, Status, Kunden |
| Shop-Inhaber oder Buchhaltung | zusätzlich `shop/setup` | USt-Typen und -Regeln, Währungen, Rabattgruppen |
| Redakteure | nur Inhaltsrichtlinien | Produktseiten, Bilder, Texte |

Die Funktion `setstatus` nimmt die Beschränkungen `FromStatus` und `ToStatus` an (`kernel/shop/module.php`), sodass
einer Packhilfe zum Beispiel nur erlaubt werden kann, Bestellungen von Paid auf Packed zu setzen. Rollen werden unter
[Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md) erklärt.

> **Tipp**
> Schreiben Sie vor dem Start auf, wer an Wochenenden und Feiertagen Bestellungen bearbeitet. Das Shop-Dashboard hebt
> Bestellungen hervor, die zu lange warten ([Shop-Dashboard und Berichte](shop-uebersicht-und-berichte.md)), aber
> jemand muss hinsehen.

## Was der Shop Ihnen bietet und was Sie ergänzen

Der technische Überblick listet auf, was der Shop nicht kann und was als Erweiterung oder Integration geplant werden
sollte: Lagerverwaltung, Rechnungen in einem Buchhaltungssystem, Versandetiketten und Marktplätze
([Einen Shop betreiben: Überblick](../../ersteller/website-ersteller/shop-ueberblick.md)). Entscheiden Sie früh, ob Sie
etwas davon brauchen und ob ein Partner es bauen oder anbinden wird. Siehe
[Einen Partner auswählen](../auswahl/einen-partner-auswaehlen.md) und
[Fragen an einen Partner](../auswahl/fragen-an-einen-partner.md).

## Ein Planungsarbeitsblatt

Kopieren Sie diese Liste in Ihre Projektbeschreibung und füllen Sie sie aus.

- [ ] Produktarten und die Felder, die jede braucht (Name, Nummer, Preis, Bilder, Optionen, technische Daten)
- [ ] Anzahl der Produkte zum Start und in einem Jahr
- [ ] Shop-Währung und weitere Währungen, die Kunden angezeigt werden
- [ ] Länder, in die Sie liefern, und die Länder, die Sie in Formularen zuerst aufführen
- [ ] USt- oder Umsatzsteuersätze, mit Ihrem Steuerberater abgestimmt
- [ ] Modell der Versandkosten: ein pauschaler Betrag oder Regeln nach Gewicht, Zone oder Wert
- [ ] Zahlungsanbieter und ob Kunden zum Bezahlen auf Ihrer Website bleiben
- [ ] Gastbestellung, Kundenkonten oder beides
- [ ] Die Bestellstatus, die Sie verwenden, und wer sie ändert
- [ ] Der Text der Bestellbestätigung per Mail und des Belegs
- [ ] Wer die Kopie des Shops von jeder Bestellmail erhält (`[MailSettings] AdminEmail`)
- [ ] Erweiterungen oder Integrationen, die Sie brauchen (Lagerbestand, Buchhaltung, Etiketten)
- [ ] Starttermin und die Testphase davor

Eine Vorlage für die Projektbeschreibung steht unter [Vorlage für Projektbeschreibungen](../vorlagen/vorlage-fuer-projektbeschreibungen.md),
eine Checkliste der Anforderungen unter [Checkliste: Anforderungen](../vorlagen/checkliste-anforderungen.md).

## Nach der Planung

1. Geben Sie das Arbeitsblatt der Person, die den Shop baut, zusammen mit
   [Einen Shop betreiben: Überblick](../../ersteller/website-ersteller/shop-ueberblick.md), wo die Reihenfolge der
   Einrichtung steht.
2. Vereinbaren Sie eine Testphase mit echten Testbestellungen im Testmodus des Zahlungsanbieters.
3. Verwenden Sie vor der Eröffnung die [Checkliste für den Shop-Start](checkliste-fuer-den-shop-start.md).
4. Schätzen Sie die Kosten mit [Gesamtbetriebskosten](../kosten/gesamtbetriebskosten.md) und
   [Durchgerechnetes Beispiel: ein kleiner Shop](../kosten/durchgerechnetes-beispiel-kleiner-shop.md).

## Verwandte Seiten

- [Was der eingebaute Shop kann](was-der-eingebaute-shop-kann.md)
- [Ihre Website planen](../../ersteller/website-ersteller/ihre-website-planen.md)
- [Ihr Inhaltsmodell entwerfen](../../ersteller/website-ersteller/ihr-inhaltsmodell-entwerfen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
