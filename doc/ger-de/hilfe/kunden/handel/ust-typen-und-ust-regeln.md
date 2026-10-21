---
title: USt-Typen und USt-Regeln
audience: customer, admin, businesses
category: customer-success
order: 50
summary: Die Mehrwertsteuer in einem laufenden Exponential-Shop pflegen - wer für die Mehrwertsteuer-Konfiguration zuständig ist, was bei einer Satzänderung zu tun ist, eine Regel für ein neues Land oder eine neue Produktgruppe hinzufügen, einen USt-Typ sicher entfernen und testen. Keine Steuerberatung.
keywords: Mehrwertsteuer, USt, USt-Typen, USt-Regeln, Satzänderung, Umsatzsteuer, Produktkategorie, Land des Kunden, shop/vattype, shop/vatrules, Steuerberater, keine Steuerberatung, VAT, VAT types, VAT rules, rate change, sales tax, product category, customer country, tax adviser
type: help
---
# USt-Typen und USt-Regeln

Diese Seite ist für den Eigentümer und die Person, die die Buchhaltung macht. Es geht darum, die
Mehrwertsteuer-Konfiguration eines laufenden Shops richtig zu halten: was zu tun ist, wenn sich ein Satz ändert,
wenn Sie in ein neues Land verkaufen oder wenn eine neue Produktgruppe einen anderen Satz hat. Wie der Shop die
Mehrwertsteuer berechnet und wie er eine Regel auswählt, ist unter
[USt-Regeln und USt-Typen für Unternehmen](../../unternehmen/shops/ust-regeln-und-ust-typen.md) erklärt.

> **Warnung**
> Diese Seite ist keine Steuer- oder Rechtsberatung. Mehrwertsteuer- und Umsatzsteuerrecht unterscheiden sich von Land
> zu Land und ändern sich mit der Zeit. Der Shop berechnet, was Sie konfigurieren; ob das für Ihr Unternehmen richtig
> ist, bestätigt Ihr Steuerberater.

## Die zwei Werkzeuge, in einem Absatz

Ein **USt-Typ** ist ein benannter Prozentsatz, verwaltet unter **Shop > USt-Typen** (`shop/vattype`). Eine
**USt-Regel** legt fest, welcher USt-Typ für Kunden eines Landes und Produkte einer Kategorie gilt, verwaltet unter
**Shop > USt-Regeln** (`shop/vatrules`), mit den Produktkategorien in `shop/productcategories`. Alle drei erfordern die
Richtlinie `shop/setup` (`kernel/shop/module.php`). Ein Preis verwendet entweder einen festen USt-Typ oder den
dynamischen Typ, der in `settings/shop.ini [VATSettings] DynamicVatTypeName` benannt ist (ausgeliefert: „Determined
by VAT charging rules“) und die Wahl den Regeln überlässt.

## Wer für die Mehrwertsteuer zuständig ist

| Aufgabe | Zuständig |
|---|---|
| Sätze festlegen und wo sie gelten | Eigentümer, mit dem Steuerberater |
| USt-Typen und USt-Regeln eingeben | Eigentümer, oder eine vertrauenswürdige Person mit `shop/setup` |
| Den USt-Typ oder die Kategorie eines Produkts setzen | Produktredaktion, nach einer schriftlichen Liste |
| Testbestellungen nach einer Änderung prüfen | Shop-Leitung |
| Festhalten, was sich wann geändert hat | Eigentümer; das Prüfprotokoll hält Mehrwertsteuer-Änderungen in seinem Kanal `commerce` fest ([Das Prüfprotokoll](../../../funktionen/6.0/pruefprotokoll.md)) |

Geben Sie `shop/setup` so wenigen Personen wie möglich. Ein einziger falscher Prozentsatz ändert den Preis jedes
Produkts, das diesen Typ verwendet.

## Wenn sich ein Satz ändert

1. Ihr Steuerberater bestätigt den neuen Satz und das Datum, ab dem er gilt.
2. Notieren Sie, welcher USt-Typ den Satz trägt. Benennen Sie Typen nach ihrer Bedeutung und ihrem Prozentsatz, dann
   ist das offensichtlich.
3. Ändern Sie am Stichtag den Prozentsatz dieses USt-Typs. Jeder Preis, der den Typ verwendet, folgt; bestehende
   Bestellungen behalten die Mehrwertsteuer, mit der sie aufgegeben wurden, weil jede Bestellposition ihren eigenen
   Preis, Mehrwertsteuerwert und Rabatt speichert (Tabelle `ezproductcollection_item` in `kernel/sql/mysql/kernel_schema.sql`).
4. Geben Sie eine Testbestellung auf (siehe unten) und prüfen Sie die Bestätigungsseite.
5. Aktualisieren Sie jede Seite und jeden Newsletter, die Preise inklusive Mehrwertsteuer nennen.

> **Tipp**
> Wenn Preise inklusive Mehrwertsteuer eingegeben werden, ändert eine Satzänderung auch Ihren Nettoerlös pro Produkt,
> es sei denn, Sie ändern auch die Preise. Entscheiden Sie mit Ihrer Buchhaltung, welche der beiden Größen Sie
> festhalten.

## Wenn Sie in ein neues Land verkaufen

- [ ] Ihr Steuerberater bestätigt, was Sie Kunden dort berechnen müssen.
- [ ] Wenn der Satz von Ihrer allgemeinen Regel abweicht: Fügen Sie eine USt-Regel für dieses Land hinzu, mit „jede
  Kategorie“ oder den betroffenen Kategorien. Eine Regel, die das Land genau nennt, hat Vorrang vor einer Regel für
  „jedes Land“ (`kernel/classes/vathandlers/ezdefaultvathandler.php`).
- [ ] Das Land kann in den Formularen des Shops gewählt werden; die Länderliste ist gruppiert, mit den
  meistverwendeten Ländern zuerst ([Ländergruppen und Regionen](../../unternehmen/shops/laendergruppen-und-regionen.md)).
- [ ] Eine Testbestellung aus diesem Land zeigt die erwartete Mehrwertsteuer.

## Wenn eine Produktgruppe einen eigenen Satz hat

- [ ] Für die Gruppe gibt es eine Produktkategorie (`shop/productcategories`).
- [ ] Die Produktklasse hat ein Kategoriefeld, und `shop.ini [VATSettings] ProductCategoryAttribute` benennt es; diese
  Einstellung nimmt Ihr Partner vor.
- [ ] Bei jedem Produkt der Gruppe ist die Kategorie gesetzt.
- [ ] Eine USt-Regel nennt die Kategorie mit dem richtigen USt-Typ.

## Einen USt-Typ entfernen

Wenn Sie einen USt-Typ entfernen, listet die Seite der USt-Typen zuerst die USt-Regeln und Produkte auf, die ihn
verwenden, und bittet Sie, für diese einen anderen Typ zu wählen. Sie verweigert das Entfernen eines Typs, den eine
Produktklasse als Standard verwendet, und das Entfernen des letzten verbliebenen Typs; die Meldung schlägt vor,
stattdessen einen Typ mit null Prozent zu behalten (`kernel/private/classes/views/shop/vattype.php`).

Das Shop-Dashboard warnt, wenn ein USt-Typ mit 0 Prozent in Gebrauch ist, sodass ein Nullsatz nie ein Versehen ist
([Shop-Dashboard und Bestellstatus](../../../funktionen/6.0/shop-dashboard.md)).

## Testen nach jeder Änderung an der Mehrwertsteuer

Verwenden Sie zwei Testbestellungen, idealerweise aus zwei Ländern und mit zwei Produktkategorien
([Warenkorb, Kasse und Bestellungen](../../ersteller/website-ersteller/warenkorb-kasse-und-bestellungen.md)):

- [ ] Die Bestätigungsseite zeigt auf jeder Position die erwartete Mehrwertsteuer.
- [ ] Der Versand wird so behandelt, wie Ihr Steuerberater es Ihnen gesagt hat.
- [ ] Die Bestellliste zeigt Summen mit und ohne Mehrwertsteuer, die übereinstimmen
  ([Sortierung der Bestellliste](../../../funktionen/6.0/sortierung-der-bestellliste.md)).
- [ ] Stornieren oder entfernen Sie die Testbestellungen so, wie Ihr Team es vereinbart hat, damit sie nicht als Umsatz zählen.

Die wiederkehrende Fassung dieser Prüfungen ist die [Mehrwertsteuer-Checkliste](mehrwertsteuer-checkliste.md).

## Verwandte Seiten

- [Mehrwertsteuer-Checkliste](mehrwertsteuer-checkliste.md)
- [USt-Regeln und USt-Typen für Unternehmen](../../unternehmen/shops/ust-regeln-und-ust-typen.md)
- [Produkte, Preise und Mehrwertsteuer](../../ersteller/website-ersteller/produkte-preise-und-mehrwertsteuer.md)
- [Preise und Währungen](preise-und-waehrungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
