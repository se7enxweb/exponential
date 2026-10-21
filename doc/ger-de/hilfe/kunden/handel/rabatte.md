---
title: Rabatte
audience: customer, admin, businesses
category: customer-success
order: 70
summary: Rabatte in einem Exponential-Shop durchführen - Gruppenrabatte für Mitglieder, Wiederverkäufer und Mitarbeitende, eine Aktion von Hand durchführen, einen Rabatt testen und Buch führen - mit dem, was der Shop nicht kann, etwa Gutscheincodes.
keywords: Rabatte, Ermäßigungen, Ermäßigungsgruppen, Ermäßigungsregeln, Aktion, Mitgliederpreise, Händlerpreise, Mitarbeiterrabatt, Werbeaktion, Gutschein, discounts, discount groups, discount rules, sale, member prices, reseller prices, staff discount, coupon, voucher, shop/discountgroup
type: help
---
# Rabatte

Diese Seite ist für Eigentümer und Marketing-Verantwortliche, die Aktionen im Shop durchführen. Sie gibt die Abläufe für
die Rabatte vor, die der Shop hat, und eine ehrliche Liste dessen, wofür eine Erweiterung nötig ist. Wie
Ermäßigungsgruppen und -regeln funktionieren und welcher Rabatt gewinnt, ist unter
[Rabatte und Wunschzettel für Unternehmen](../../unternehmen/shops/rabatte-und-wunschlisten.md) erklärt.

## Was der Shop bietet

Eine **Ermäßigungsgruppe** hat Mitglieder (einzelne Kunden oder ganze Benutzergruppen) und eine oder mehrere
**Ermäßigungsregeln**, jede ein Prozentsatz, wahlweise eingeschränkt auf Inhaltsklasse, Sektion oder bestimmte
Produkte. Ermäßigungsgruppen werden unter **Shop > Ermäßigungen** verwaltet (`shop/discountgroup`,
`shop/discountruleedit`), wofür `shop/setup` nötig ist (`kernel/shop/module.php`). Für jedes Produkt erhält der Kunde
den **höchsten** passenden Prozentsatz; Rabatte addieren sich nicht (`kernel/classes/ezdiscount.php`).

Da Rabatte dem Konto und den Benutzergruppen des Kunden folgen, erreichen sie nur angemeldete Kunden.

## Typische Anwendungen, und wer sie durchführt

| Aktion | Wie | Üblicherweise durchgeführt von |
|---|---|---|
| Mitgliederpreise | Eine Ermäßigungsgruppe, deren Mitglied die Benutzergruppe der Mitglieder ist | Eigentümer |
| Händlerpreise | Eine Benutzergruppe für Wiederverkäufer mit ihrem Rabatt | Eigentümer, mit dem Vertrieb |
| Mitarbeiterrabatt | Die Benutzergruppe der Mitarbeitenden in einer Ermäßigungsgruppe | Eigentümer |
| Eine Aktion auf einige Produkte | Eine Regel, eingeschränkt auf diese Produkte oder auf eine Sektion „Sale“, für eine Gruppe, der alle Angemeldeten angehören | Marketing, mit dem Eigentümer |
| Eine persönliche Vereinbarung | Ein Kunde als einzelnes Mitglied | Eigentümer |

Ziehen Sie Benutzergruppen einzelnen Kunden vor: Wer später der Gruppe beitritt, erhält den Rabatt, ohne dass jemand
daran denken muss, ihn hinzuzufügen.

## Eine Aktion von Hand durchführen

Der Shop hat keine Rabatte, die von selbst beginnen und enden. Eine Aktion wird von Hand durchgeführt:

1. **Vorbereiten**: Legen Sie die Regel und ihre Einschränkung vorab an, mit 0 Prozent oder ohne Mitglieder, damit sie bereitsteht.
2. **Starten**: Setzen Sie den Prozentsatz oder fügen Sie die Mitglieder zum vereinbarten Zeitpunkt hinzu.
3. **Prüfen** (siehe unten), direkt nach dem Start.
4. **Beenden**: Entfernen Sie die Mitglieder oder die Regel zum vereinbarten Endzeitpunkt.
5. **Festhalten**: Notieren Sie in Ihrem Aktionsprotokoll, was von wann bis wann lief.

Das Verschieben vieler Produkte in eine Sektion „Sale“ oder aus ihr heraus kann im Hintergrund als Inhaltsauftrag
laufen ([Inhaltsaufträge](../../../funktionen/6.0/inhaltsauftraege.md)).

> **Tipp**
> Tragen Sie Start und Ende mit je einer namentlich genannten Person in den Teamkalender ein. Eine Aktion, die niemand
> beendet, ist eine Preissenkung.

## Einen Rabatt prüfen

- [ ] Melden Sie sich als Testkunde an, der in der Gruppe ist: Der Warenkorb zeigt den reduzierten Preis.
- [ ] Melden Sie sich als Testkunde an, der nicht in der Gruppe ist: Der Warenkorb zeigt den normalen Preis.
- [ ] Ein Produkt außerhalb der Einschränkung der Regel ist nicht reduziert.
- [ ] Ist der Kunde in zwei Gruppen, gilt der höhere Prozentsatz, nicht die Summe.
- [ ] Die Bestellansicht zeigt den Rabatt auf der Bestellposition (jede Bestellposition speichert ihren Rabatt,
  Tabelle `ezproductcollection_item` in `kernel/sql/mysql/kernel_schema.sql`).

## Buch führen

Änderungen an Rabatten werden im Kanal `commerce` des Prüfprotokolls festgehalten ([Das Prüfprotokoll](../../../funktionen/6.0/pruefprotokoll.md)).
Führen Sie zusätzlich ein eigenes kurzes Protokoll, denn der Grund für eine Aktion steht nicht im Prüfprotokoll:

```text
PROMOTION LOG
Name | Discount group / rule | Percentage | Who gets it | Start | End | Started by | Ended by | Notes
-----|-----------------------|------------|-------------|-------|-----|------------|----------|------
```

## Was der Shop ohne Erweiterung nicht kann

Aus dem Kernel-Code, wie unter [Rabatte und Wunschzettel für Unternehmen](../../unternehmen/shops/rabatte-und-wunschlisten.md) zusammengefasst:

- Gutschein- oder Rabattcodes, die an der Kasse eingegeben werden;
- Geschenkkarten;
- Mengenregeln wie „zwei kaufen, eins gratis“;
- Rabatte, die von selbst beginnen und enden;
- Rabatte für nicht angemeldete Gäste (außer man gibt allen einen).

Wenn Sie etwas davon brauchen, bitten Sie Ihren Partner um ein Angebot. Ein gängiger Ansatz ist ein Workflow-Ereignis,
das vor der Bestätigungsseite eine negative Bestellposition hinzufügt ([Workflows](../../../anleitungen/workflows.md)).

## Verwandte Seiten

- [Rabatte und Wunschzettel für Unternehmen](../../unternehmen/shops/rabatte-und-wunschlisten.md)
- [Ihren Shop bewerben](../../unternehmen/shops/ihren-shop-bewerben.md)
- [Kundenkonten](kundenkonten.md)
- [Wunschzettel und Warenkörbe](wunschzettel-und-warenkoerbe.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
