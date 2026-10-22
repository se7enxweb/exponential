---
title: Optionen und Varianten
audience: ecommerce, website-creator, businesses
category: e-commerce
order: 23
summary: Kunden in einem Exponential-Shop Größe, Farbe oder Extras wählen lassen - die vier Options-Datentypen, wie eine Auswahl mit dem Warenkorbposten und der Bestellung gespeichert wird, das Formular „Zum Warenkorb hinzufügen“ und wann eigene Produkte das bessere Modell sind.
keywords: Optionen, Varianten, ezoption, ezmultioption, ezmultioption2, ezrangeoption, Produktoptionen, Aufpreis, Option eines Warenkorbpostens, Größe, Farbe, konfigurierbares Produkt, options, variants, product options, additional price, size, color, configurable product
type: help
---
# Optionen und Varianten

Diese Seite ist für Entwickler und Händler, die Produkte mit Auswahlmöglichkeiten verkaufen. Exponential hat vier
Options-Datentypen; die Auswahl eines Kunden wird mit dem Formular „Zum Warenkorb hinzufügen“ gesendet, mit dem
Warenkorbposten gespeichert und in die Bestellung übernommen. Diese Seite erklärt, welcher Datentyp zu welcher Art von
Auswahl passt und wann eine „Variante“ ein eigenes Produkt sein sollte.

## Inhalt

1. [Die vier Options-Datentypen](#die-vier-options-datentypen)
2. [Der Weg einer Auswahl](#der-weg-einer-auswahl)
3. [Optionen oder eigene Produkte](#optionen-oder-eigene-produkte)
4. [Arbeit an den Templates](#arbeit-an-den-templates)
5. [Checkliste](#checkliste)

## Die vier Options-Datentypen

| Datentyp | Verwenden für | Beispiel |
|---|---|---|
| `ezoption` | Eine Liste von Auswahlen, jede mit optionalem Aufpreis | Geschenkverpackung: keine, Standard (+2,00), Premium (+5,00) |
| `ezmultioption` | Mehrere unabhängige Listen an einem Produkt | Größe und Farbe |
| `ezmultioption2` | Gruppen von Optionslisten, die verschachtelt werden können, mit Regeln zwischen Auswahlen, für konfigurierbare Produkte | Ein Computer mit Basismodell, Arbeitsspeicher und Extras |
| `ezrangeoption` | Ein Zahlenbereich mit Start-, End- und Schrittwert | Eine Länge von 1 bis 10 Metern in Schritten von 1 |

Alle vier liegen in `kernel/classes/datatypes/`. Ihre Bearbeitung behandelt
[Datentypen für Ihre Felder wählen](../../ersteller/website-ersteller/datentypen-waehlen.md).

## Der Weg einer Auswahl

1. Das Template der Vollansicht des Produkts sendet das Formular „Zum Warenkorb hinzufügen“ mit den gewählten
   Optionswerten ([Warenkorb, Kasse und Bestellungen](../../ersteller/website-ersteller/warenkorb-kasse-und-bestellungen.md#zum-warenkorb-hinzufügen)).
2. Die Operation `addtobasket` speichert den Posten in der Produktsammlung des Warenkorbs und jede gewählte Option als
   Postenoption mit Name, Wert und Preis (Tabelle `ezproductcollection_item_opt`).
3. Die Postenpreise werden einschließlich der Optionspreise berechnet; Warenkorb und Bestätigung zeigen sie an.
4. An der Kasse wird die Produktsammlung zu der der Bestellung, sodass die Auswahl in der Bestellansicht und in der
   Bestellmail sichtbar bleibt, auch wenn sich das Produkt später ändert.

Der Workflow-Trigger `shop_addtobasket` erhält die `option_list`, sodass ein Ereignis an diesem Trigger eine Auswahl
prüfen kann, bevor der Posten hinzugefügt wird ([Workflows an Shop-Triggern](../kasse/workflows-an-shop-triggern.md)).

## Optionen oder eigene Produkte

| Optionen sind besser, wenn | Eigene Produkte sind besser, wenn |
|---|---|
| Die Auswahl den Preis wenig oder gar nicht ändert | Jede Variante eine eigene Artikelnummer für Lager oder Buchhaltung hat |
| Alle Varianten Bilder und Beschreibung teilen | Varianten eigene Bilder oder Beschreibungen haben |
| Sie keinen Lagerbestand pro Variante führen | Sie später ein Lagersystem anbinden werden |
| Kunden eine Produktseite sehen | Kunden jede Variante über die Suche finden sollen |

Eigene Produkte können einen gemeinsamen übergeordneten Ordner oder Relationen haben, sodass die Seite trotzdem
„andere Farben“ zeigt. Eine Versand- oder Lagerintegration braucht meist eine Kennung pro verkaufbarer Einheit, was für
eigene Produkte spricht.

## Arbeit an den Templates

Die Produkt-Templates der ausgelieferten Themes senden das Formular „Zum Warenkorb hinzufügen“; die Optionsfelder müssen
innerhalb dieses Formulars ausgegeben werden, damit ihre Werte mitgesendet werden. Das Standarddesign zeigt gewählte
Optionen in den Warenkorb- und Bestell-Templates unter `design/standard/templates/shop/`. Prüfen Sie jedes Template,
das Posten auflistet: Warenkorb, Bestätigung, Bestellansicht, Bestellbeleg und Bestellmail.

## Checkliste

- [ ] Jede Optionsliste hat eine klare Beschriftung für Screenreader ([Barrierefreiheit im Shop](../betrieb/barrierefreiheit-im-shop.md)).
- [ ] Aufpreise enthalten die USt. oder nicht, genauso wie der Preis des Produkts.
- [ ] Eine Testbestellung mit Optionen zeigt jede Auswahl im Warenkorb, in der Bestellansicht und in der Mail.
- [ ] Eine Änderung des Preises einer Option am Produkt ändert keine bestehenden Bestellungen.

## Verwandte Seiten

- [Katalogmodellierung](README.md)
- [Katalogmodellierung im Detail](katalogmodellierung-im-detail.md)
- [Produktklassen und Katalog](../../unternehmen/shops/produktklassen-und-katalog.md)
- [Wunschlisten und Warenkörbe](../../kunden/handel/wunschzettel-und-warenkoerbe.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
