---
title: Rabatte und Wunschlisten
audience: businesses, admin, sales-marketing
category: business-solutions
order: 160
summary: Die zwei Werkzeuge des eingebauten Shops zur Kundenbindung - prozentuale Rabattgruppen für Kunden und Kundengruppen mit Regeln nach Klasse, Sektion oder Produkt, und die Wunschliste für angemeldete Kunden - sowie das, was nicht enthalten ist, etwa Gutscheincodes.
keywords: Rabatte, Rabattgruppen, Ermäßigungsgruppen, Rabattregeln, Kundengruppen, Großhandelspreise, Mitgliederpreise, Wunschliste, Wunschzettel, Gutschein, Aktion, Kundenbindung, discounts, discount groups, discount rules, customer groups, wholesale prices, member prices, wishlist, coupon, voucher, promotion, loyalty
type: help
---
# Rabatte und Wunschlisten

Diese Seite richtet sich an Geschäftsinhaber und Marketingverantwortliche, die Kunden belohnen möchten. Sie erklärt die
Rabattgruppen des Shops, wie der Shop entscheidet, welcher Rabatt gilt, die Wunschliste und was nicht enthalten ist,
etwa Gutscheincodes. Die Seite der Einstellungen steht unter
[Produkte, Preise und USt](../../../../eng-us/help/creators/website-creators/products-prices-and-vat.md#discounts).

## Inhalt

1. [Rabattgruppen in Kürze](#rabattgruppen-in-kürze)
2. [Wer einen Rabatt bekommt](#wer-einen-rabatt-bekommt)
3. [Worauf der Rabatt angewendet wird](#worauf-der-rabatt-angewendet-wird)
4. [Welcher Rabatt gewinnt](#welcher-rabatt-gewinnt)
5. [Einsatzmöglichkeiten für ein kleines Unternehmen](#einsatzmöglichkeiten-für-ein-kleines-unternehmen)
6. [Die Wunschliste](#die-wunschliste)
7. [Was nicht enthalten ist](#was-nicht-enthalten-ist)
8. [Checkliste](#checkliste)

## Rabattgruppen in Kürze

Eine **Rabattgruppe** (in der Administration „Ermäßigungsgruppe“, `shop/discountgroup`, Richtlinie `shop/setup`) hat
Mitglieder und eine oder mehrere **Rabattregeln**. Eine Regel ist ein Prozentsatz, auf Wunsch beschränkt auf bestimmte
Produkte (`shop/discountruleedit`). Der Warenkorb wendet den Prozentsatz auf den Preis der Position mit und ohne USt an
(`kernel/classes/ezbasket.php`).

## Wer einen Rabatt bekommt

Mitglieder einer Rabattgruppe werden aus der Benutzerliste hinzugefügt (`shop/discountgroupview`). Der Shop sucht dann
Rabattregeln für den Kunden **und für jede Benutzergruppe, zu der der Kunde gehört** (`eZDiscount::discountPercent()`
in `kernel/classes/ezdiscount.php`). In der Praxis:

- fügen Sie einen einzelnen Kunden hinzu, für eine persönliche Vereinbarung;
- fügen Sie eine Benutzergruppe hinzu, etwa „Mitglieder“ oder „Wiederverkäufer“, damit jeder darin den Rabatt
  bekommt, auch Personen, die später dazukommen.

Rabatte brauchen daher angemeldete Kunden. Ein Gast ist der anonyme Benutzer und bekommt keinen Gruppenrabatt, es sei
denn, Sie geben allen einen.

## Worauf der Rabatt angewendet wird

Eine Regel lässt sich durch jede Kombination aus Folgendem beschränken (dieselbe Datei):

| Beschränkung | Beispiel |
|---|---|
| Inhaltsklasse | 10 Prozent auf alles der Klasse „Book“ |
| Sektion | 15 Prozent auf Produkte in der Sektion „Outlet“ |
| Bestimmte Produkte | 20 Prozent auf drei genannte Produkte |

Eine Regel ohne Beschränkung gilt für jedes Produkt. Sind mehrere Arten der Beschränkung gesetzt, muss ein Produkt
allen entsprechen.

## Welcher Rabatt gewinnt

Rabatte addieren sich nicht. Für jedes Produkt nimmt der Shop den **höchsten** Prozentsatz unter den Regeln, die zum
Kunden und zum Produkt passen (`kernel/classes/ezdiscount.php` sortiert die Regeln nach Prozentsatz und behält den
besten Treffer). Ein Kunde in „Mitglieder“ (5 Prozent auf alles) und „Wiederverkäufer“ (20 Prozent auf Bücher)
bekommt 20 Prozent auf Bücher und 5 Prozent auf alles andere.

## Einsatzmöglichkeiten für ein kleines Unternehmen

| Ziel | Wie |
|---|---|
| Mitgliederpreise für einen Club oder Verein | Ein Gruppenrabatt für die Benutzergruppe der Mitglieder ([Mitglieder](../mitglieder/README.md)) |
| Händlerpreise für Wiederverkäufer | Eine Benutzergruppe für Wiederverkäufer mit ihrem Rabatt, dazu die Bestellung nur mit Konto ([Kundenkonten und Gastbestellung](kundenkonten-und-gastbestellung.md)) |
| Mitarbeiterrabatt | Ein Gruppenrabatt für die Benutzergruppe der Mitarbeiter |
| Ein Räumungsverkauf | Eine Regel, beschränkt auf eine Sektion „Outlet“, für alle |
| Dank an einen wichtigen Kunden | Dieser Kunde als einzelnes Mitglied |

> **Tipp**
> Zeigen Sie angemeldeten Kunden den reduzierten Preis auf der Produktseite, nicht nur im Warenkorb. Das ist
> Template-Arbeit für Ihren Designer, und es macht den Rabatt dort sichtbar, wo die Kaufentscheidung fällt.

## Die Wunschliste

Der Shop hat eine Wunschliste (`shop/wishlist`, Richtlinie `shop/buy`). Kunden fügen Produkte mit einer Schaltfläche
**„Add to wishlist“** auf der Produktseite hinzu, ändern Mengen, entfernen Artikel und legen sie später in den
Warenkorb.

- Die Wunschliste gehört zum Benutzerkonto (`eZWishList::currentWishList()` in `kernel/classes/ezwishlist.php`), und
  das Hinzufügen erfordert einen registrierten, angemeldeten Benutzer
  (`kernel/private/classes/views/content/action.php` weist anonyme Besucher ab).
- Templates werden in den Designs standard, admin und admin4 mitgeliefert (`shop/wishlist.tpl`); das Design Ihrer
  Website braucht die Schaltfläche und einen Link zur Wunschliste.

Die Wunschliste ist ein guter Grund, Kundenkonten anzubieten, wenn Ihre Produkte gut überlegte Anschaffungen sind, etwa
Möbel oder Geschenke.

## Was nicht enthalten ist

Wir haben den Kernel nach Folgendem durchsucht und es nicht gefunden:

- **Gutschein- oder Aktionscodes**, die an der Kasse eingegeben werden;
- **Geschenkkarten**;
- **„Kaufe zwei, bekomme eins gratis“** oder andere Mengenregeln;
- **zeitlich begrenzte Rabatte**, die von selbst beginnen und enden;
- **geteilte Wunschlisten** oder Wunschlisten für Gäste.

Wege dorthin: Einen zeitlich begrenzten Ausverkauf können Sie von Hand durchführen: Legen Sie die Regel zu Beginn des
Ausverkaufs an und entfernen Sie sie am Ende, oder weisen Sie die Aktionsprodukte einer Sektion „Outlet“ zu und
danach wieder zurück (eine Sektionszuweisung für viele Elemente kann als Inhaltsjob im Hintergrund laufen,
[Inhaltsjobs](../../../../eng-us/features/6.0/content-jobs.md)). Gutscheincodes, Geschenkkarten und Mengenregeln brauchen eine
Erweiterung, zum Beispiel ein Workflow-Ereignis an `before_shop_confirmorder`, das eine negative Bestellposition
hinzufügt; das ist Arbeit für einen Entwickler oder Partner ([Einen Workflow-Ereignistyp schreiben](../../../../eng-us/help/creators/app-creators/writing-a-workflow-event-type.md),
[Einen Partner auswählen](../auswahl/einen-partner-auswaehlen.md)).

## Checkliste

- [ ] Rabattgruppen sind danach benannt, für wen sie gedacht sind.
- [ ] Mitglieder sind wo immer möglich Benutzergruppen, keine einzelnen Benutzer.
- [ ] Die Beschränkung jeder Regel ist mit einem Testkunden im Warenkorb getestet.
- [ ] Wer Aktionen plant, versteht, dass der höchste Rabatt gewinnt.
- [ ] Die Schaltfläche der Wunschliste erscheint nur für angemeldete Kunden.
- [ ] Änderungen an Rabatten werden festgehalten: Der Audit-Trail protokolliert Rabatte in der Kategorie `commerce` ([Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)).

## Verwandte Seiten

- [Produkte, Preise und USt](../../../../eng-us/help/creators/website-creators/products-prices-and-vat.md)
- [Ihren Shop bewerben](ihren-shop-bewerben.md)
- [Kundenkonten und Gastbestellung](kundenkonten-und-gastbestellung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
