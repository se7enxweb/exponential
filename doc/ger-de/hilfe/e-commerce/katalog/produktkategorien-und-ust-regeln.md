---
title: Produktkategorien und USt-Regeln
audience: ecommerce, website-creator, businesses
category: e-commerce
order: 24
summary: Wie die USt. nach Land des Kunden und Produktkategorie in Exponential wirklich funktioniert - der USt-Handler, der eingeschaltet sein muss, Produktkategorien als Steuerklassen, wie der Standard-Handler USt-Regeln bewertet, das Land des Kunden und wie Sie das Ergebnis testen.
keywords: USt-Regeln, Umsatzsteuer, Produktkategorie, ezproductcategory, USt-Handler, eZDefaultVATHandler, VATSettings, Handler=ezdefault, dynamische USt., durch USt-Abrechnungsregeln bestimmt, UserCountryAttribute, ProductCategoryAttribute, RequireUserCountry, Priorität der Regeln, VAT rules, product category, VAT handler, dynamic VAT, rule priority
type: help
---
# Produktkategorien und USt-Regeln

Diese Seite ist für Entwickler und Händler, die die USt. nach dem Land des Kunden und der Steuerklasse des Produkts
berechnen. Die geschäftliche Sicht steht unter [USt-Regeln und USt-Typen](../../unternehmen/shops/ust-regeln-und-ust-typen.md)
und die Prüfungen für Inhaber in der [USt-Checkliste](../../kunden/handel/mehrwertsteuer-checkliste.md); diese Seite erklärt
den Mechanismus, einschließlich einer Einstellung, die die anderen Seiten nicht nennen: USt-Regeln funktionieren nur,
wenn ein USt-Handler eingeschaltet ist.

## Inhalt

1. [Feste und dynamische USt.](#feste-und-dynamische-ust)
2. [Dynamische USt. einschalten](#dynamische-ust-einschalten)
3. [Produktkategorien](#produktkategorien)
4. [Wie der Standard-Handler eine Regel auswählt](#wie-der-standard-handler-eine-regel-auswählt)
5. [Das Land des Kunden](#das-land-des-kunden)
6. [Ein durchgerechneter Regelsatz](#ein-durchgerechneter-regelsatz)
7. [Die USt. testen](#die-ust-testen)

## Feste und dynamische USt.

| Modus | Der USt-Typ des Produkts | Angewandte USt. |
|---|---|---|
| Fest | Ein benannter USt-Typ, zum Beispiel „Regulär 20 %“ | Immer dieser Prozentsatz |
| Dynamisch | „Durch USt-Abrechnungsregeln bestimmt“ (`shop.ini [VATSettings] DynamicVatTypeName`) | Zum Zeitpunkt des Kaufs vom USt-Handler aus den USt-Regeln gewählt |

Intern hat der dynamische USt-Typ die ID `-1` (`eZVatType::dynamicVatType()` in `kernel/classes/ezvattype.php`). Wenn
der Handler keinen Prozentsatz bestimmen kann, wird die USt. als unbekannt markiert.

## Dynamische USt. einschalten

`eZVATManager` (`kernel/classes/ezvatmanager.php`) verwendet USt-Regeln nur, wenn `shop.ini [VATSettings] Handler`
gesetzt ist. In der ausgelieferten `settings/shop.ini` ist die Zeile auskommentiert (`#Handler=ezdefault`); die
dynamische USt. ist also aus, und die Auswahl „durch USt-Abrechnungsregeln bestimmt“ wird erst angeboten, wenn ein
Handler konfiguriert ist. Um sie einzuschalten, in `settings/override/shop.ini.append.php`:

```ini
[VATSettings]
Handler=ezdefault
ProductCategoryAttribute=tax_class
UserCountryAttribute=country
RequireUserCountry=true
```

| Einstellung | Bedeutung |
|---|---|
| `Handler` | Der USt-Handler; `ezdefault` ist `eZDefaultVATHandler` in `kernel/classes/vathandlers/` |
| `ProductCategoryAttribute` | Bezeichner des Felds `ezproductcategory` an Produktklassen |
| `UserCountryAttribute` | Bezeichner des Länderfelds an der Benutzerklasse |
| `RequireUserCountry` | `true`: Ein fehlendes Land wird gemeldet; setzen Sie `false` nur mit einem eigenen Handler, der es nicht braucht |

Leeren Sie danach den INI-Cache. Der Abschnitt „So funktioniert Ihr Shop“ des Shop-Dashboards zeigt dann die USt-Regeln und
die Länder, aus denen Bestellungen kamen ([Shop-Dashboard und Bestellstatus](../../../funktionen/6.0/shop-dashboard.md)).

## Produktkategorien

Eine Produktkategorie ist eine Steuerklasse, kein Katalogordner. Legen Sie sie unter **Web-Shop > Produkt Kategorien**
an (`shop/productcategories`, Richtlinie `shop/setup`), fügen Sie jeder Produktklasse ein Feld `ezproductcategory` hinzu
und setzen Sie es an jedem Produkt. Typische Kategorien: „Regulär“, „Ermäßigt (Lebensmittel, Bücher)“, „Nullsatz“,
„Digitale Dienstleistungen“.

## Wie der Standard-Handler eine Regel auswählt

Jede USt-Regel hat ein Land (oder `*` für jedes Land), eine Liste von Produktkategorien (oder keine, was jede Kategorie
bedeutet) und einen USt-Typ. Für ein Produkt und ein Land bewertet `eZDefaultVATHandler::chooseVatType()` jede Regel:

| Regel passt | Punkte |
|---|---|
| Genaues Land und genaue Kategorie | 4 |
| Genaues Land, Regel ohne Kategorien | 3 |
| Jedes Land (`*`) und genaue Kategorie | 2 |
| Jedes Land, keine Kategorien | 1 |
| Land oder Kategorie passt nicht | 0 (nicht verwendet) |

Die höchste Punktzahl gewinnt; bei Gleichstand gewinnt die zuerst gefundene Regel. Erreicht keine Regel mehr als 0,
wird ein Fehler protokolliert und der USt-Typ „None“ mit 0 Prozent verwendet. Ein Produkt ohne Kategorie oder ein
Kunde ohne Land passt nur zu Regeln mit „beliebig“ auf dieser Seite. Zwei praktische Folgen:

- Legen Sie immer eine Auffangregel an (`*`, keine Kategorien), damit nichts versehentlich auf 0 Prozent fällt.
- Legen Sie nicht zwei Regeln mit demselben Land und derselben Kategorie an; das Ergebnis hängt dann von der
  Reihenfolge ab, in der die Regeln geladen werden.

## Das Land des Kunden

`eZVATManager::getUserCountry()` nimmt zuerst das Land, das ein Besucher mit `shop/setusercountry` gewählt hat (das
bevorzugte Land), und liest andernfalls das Feld `UserCountryAttribute` des Benutzers. Der Bestätigungsschritt führt die
Methode `handle-user-country` der Operation `confirmorder` aus, bevor der Versand berechnet wird. Gäste und neue Kunden
brauchen daher im Kassenformular ein Länderfeld, das Ihr Konto-Handler speichert
([shopaccount.ini durchgegangen](../settings/shopaccount-ini-durchgegangen.md)). Die Länderliste selbst, mit den am
häufigsten verwendeten Ländern zuerst, erklärt [Ländergruppen und Regionen](../../unternehmen/shops/laendergruppen-und-regionen.md).

## Ein durchgerechneter Regelsatz

Ein Shop in Land A verkauft nach A, nach B und in den Rest der Welt; Bücher haben in A einen ermäßigten Satz:

| Land | Kategorien | USt-Typ |
|---|---|---|
| A | Bücher | Ermäßigt A |
| A | (beliebig) | Regulär A |
| B | (beliebig) | Regulär B |
| `*` | (beliebig) | Null |

Ein aus A gekauftes Buch erhält bei der ersten Regel 4 Punkte: ermäßigt. Ein Hemd aus A erhält bei der zweiten 3:
regulär. Alles aus B erhält bei der dritten 3. Alles von anderswo erhält bei der letzten 1. Ob das die richtigen Sätze
sind, klärt Ihre Steuerberatung.

## Die USt. testen

- [ ] Für jede Regel eine Testbestellung mit einem Produkt dieser Kategorie und einem Kunden dieses Landes.
- [ ] Ein Produkt ohne Kategorie und ein Kunde ohne Land: Die Auffangregel greift.
- [ ] Bestätigungsseite, Bestellansicht und Bestellmail zeigen dieselbe USt.
- [ ] Das Dashboard zeigt keine unerwartete Warnung zu einem USt-Typ mit 0 Prozent.

## Verwandte Seiten

- [Katalogmodellierung](README.md)
- [Eigener USt-Handler](../erweitern/eigener-ust-handler.md)
- [USt-Typen und -Regeln (Ihren Shop betreiben)](../../kunden/handel/ust-typen-und-ust-regeln.md)
- [shop.ini durchgegangen](../settings/shop-ini-durchgegangen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
