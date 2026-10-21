---
title: USt-Regeln und USt-Typen
audience: businesses, admin, website-creator
category: business-solutions
order: 50
summary: Wie ein Exponential-Shop Mehrwertsteuer oder Umsatzsteuer berechnet - feste USt-Typen, USt-Regeln nach Land des Kunden und Produktkategorie, wie die beste Regel gewählt wird und was ein Unternehmen mit seinem Steuerberater klären muss.
keywords: USt, Mehrwertsteuer, Umsatzsteuer, USt-Typen, USt-Regeln, Produktkategorie, Land des Kunden, Steuersatz, ermäßigter Satz, Nullsatz, DynamicVatTypeName, Steuerberater, VAT, sales tax, VAT types, VAT rules, product category, customer country, tax rate, reduced rate, zero rate, tax adviser
type: help
---
# USt-Regeln und USt-Typen

Diese Seite richtet sich an Geschäftsinhaber und Buchhalter, die wissen müssen, wie der Shop Steuern berechnet. Sie
erklärt die zwei Werkzeuge des Shops, feste USt-Typen und USt-Regeln, und wie der Shop den Satz für eine
Bestellposition wählt. Sie beschreibt, was die Software tut, nicht, was Ihr Steuerrecht verlangt.

> **Warnung**
> Diese Seite ist keine Steuerberatung. Mehrwertsteuer- und Umsatzsteuerrecht unterscheiden sich von Land zu Land und
> ändern sich mit der Zeit. Der Shop berechnet, was Sie konfigurieren; ob das für Ihr Unternehmen richtig ist, ist eine
> Frage für Ihren Steuerberater ([Produkte, Preise und USt](../../ersteller/website-ersteller/produkte-preise-und-mehrwertsteuer.md#mehrwertsteuer)).

## Inhalt

1. [Zwei Wege, USt zu berechnen](#zwei-wege-ust-zu-berechnen)
2. [USt-Typen](#ust-typen)
3. [USt-Regeln](#ust-regeln)
4. [Wie der Shop eine Regel wählt](#wie-der-shop-eine-regel-wählt)
5. [Woher das Land des Kunden kommt](#woher-das-land-des-kunden-kommt)
6. [Umsatzsteuer außerhalb von Ländern mit Mehrwertsteuer](#umsatzsteuer-außerhalb-von-ländern-mit-mehrwertsteuer)
7. [Checkliste](#checkliste)

## Zwei Wege, USt zu berechnen

| Ansatz | Wie er funktioniert | Passt zu |
|---|---|---|
| Fester USt-Typ am Preis | Jedes Preisfeld nennt einen USt-Typ, zum Beispiel „Standard“ | Shops, die in einem Land verkaufen oder überall denselben Satz berechnen |
| USt-Regeln | Der USt-Typ des Preises ist „Determined by VAT charging rules“; der Satz ergibt sich aus dem Land des Kunden und der Kategorie des Produkts | Shops, die in mehrere Länder verkaufen oder Produkte mit unterschiedlichen Sätzen haben |

Der Name des dynamischen USt-Typs wird in `settings/shop.ini`, `[VATSettings] DynamicVatTypeName` festgelegt; der
ausgelieferte Wert ist `Determined by VAT charging rules`.

## USt-Typen

Ein USt-Typ ist ein benannter Prozentsatz, etwa regulär, ermäßigt oder null. Sie werden unter **Shop > USt-Typen**
verwaltet (`shop/vattype`, das `shop/setup` braucht). Die Klasse ist `eZVatType` in `kernel/classes/ezvattype.php`.

- Benennen Sie USt-Typen danach, was sie für Ihren Buchhalter bedeuten („Standard 19“, „Ermäßigt 7“, „Null“), damit
  niemand raten muss.
- Ändern Sie einen Prozentsatz, wenn sich das Gesetz ändert; jeder Preis, der den Typ verwendet, folgt.
- Das Shop-Dashboard warnt, wenn ein USt-Typ mit 0 Prozent verwendet wird, damit ein Nullsatz nie ein Versehen ist
  ([Shop-Dashboard und Bestellstatus](../../../funktionen/6.0/shop-dashboard.md)).

## USt-Regeln

Eine USt-Regel sagt: Für Kunden in diesem Land und Produkte in diesen Kategorien verwende diesen USt-Typ. Regeln werden
unter `shop/vatrules` und `shop/editvatrule` verwaltet. Produktkategorien werden unter `shop/productcategories`
verwaltet und den Produkten über ein Feld `ezproductcategory` zugewiesen.

Regeln werden von einem USt-Handler angewendet, und die ausgelieferte `settings/shop.ini` setzt keinen: Solange
`shop.ini [VATSettings] Handler=ezdefault` nicht in einem Override (`settings/override/shop.ini.append.php`) gesetzt
ist, werden die Regeln gespeichert und nie gelesen, und jedes Produkt wird mit dem festen USt-Typ seines Preises
berechnet. Die Seite **VAT rules** weist darauf hin, wenn Regeln vorhanden sind und kein Handler sie anwendet. Der
Handler ist in der ausgelieferten Datei nicht eingeschaltet, weil das den Checkout eines laufenden Shops verändert: Der
Shop braucht dann das Land des Kunden (mit `RequireUserCountry=true` wird eine Bestellung ohne Land angehalten), und der
Typ „Determined by VAT charging rules“ berechnet 0 Prozent, wo keine Regel passt.

Die übrigen Einstellungen verbinden die Regeln mit Ihren Inhalten (`shop.ini [VATSettings]`):

| Einstellung | Bedeutung |
|---|---|
| `Handler` | `ezdefault` für den ausgelieferten Regel-Handler; ausgeliefert nicht gesetzt, also wird keine Regel angewendet |
| `ProductCategoryAttribute` | Der Bezeichner des Produktfelds, das die Kategorie enthält |
| `UserCountryAttribute` | Der Bezeichner des Benutzerfelds, das das Land des Kunden enthält |
| `RequireUserCountry` | Ausgeliefert `true`: Der Shop muss das Land des Kunden kennen, um Regeln anzuwenden |

Eine Regel kann ein bestimmtes Land nennen oder für jedes Land gelten, und sie kann Kategorien nennen oder für jede
Kategorie gelten. Ein typischer Regelsatz für einen Shop mit regulärem und ermäßigtem Satz:

| Land | Kategorien | USt-Typ |
|---|---|---|
| Jedes | Jede | Standard |
| Jedes | Bücher | Ermäßigt |
| Ein Land, in dem Sie für die USt registriert sind | Jede | Der reguläre Satz dieses Landes |

Diese Zeilen veranschaulichen den Mechanismus und sind keine Aussage über die Steuersätze irgendeines Landes.

## Wie der Shop eine Regel wählt

Der Standard-USt-Handler (`kernel/classes/vathandlers/ezdefaultvathandler.php`) bewertet jede Regel gegen die
Bestellposition und nimmt den besten Treffer:

1. Eine Regel, die genau das Land des Kunden nennt, schlägt eine Regel für jedes Land.
2. Eine Regel, die genau die Kategorie des Produkts nennt, schlägt eine Regel für jede Kategorie.
3. Eine Regel muss sowohl zum Land als auch zur Kategorie passen, um überhaupt zu zählen.
4. Wenn mehrere Regeln gleich bewertet werden, gewinnt die erste.

Einfach gesagt: Schreiben Sie eine allgemeine Regel für „jedes Land, jede Kategorie“ und fügen Sie besondere Regeln nur
dort hinzu, wo der Satz abweicht. Die besondere Regel gewinnt dort, wo sie gilt.

Ist das Land des Kunden nicht bekannt, verwendet der Handler einen Platzhalter, der nur zu den Regeln für „jedes Land“
passt (dieselbe Datei).

## Woher das Land des Kunden kommt

Das Land stammt aus dem in `UserCountryAttribute` genannten Feld des Kundenkontos oder aus dem Land, das ein Besucher
wählt (`shop/setusercountry`). Die Länderauswahl in den Shop-Formularen ist die gruppierte Liste, die unter
[Ländergruppen und Regionen](laendergruppen-und-regionen.md) beschrieben ist; in den USt-Regeln sendet sie den
Ländercode ([Die gruppierte Länderliste](../../../funktionen/6.0/laendergruppen.md)).

Eine andere USt-Logik, zum Beispiel eine, die auf der Lieferanschrift oder auf der USt-Identifikationsnummer eines
Kunden beruht, lässt sich als eigener USt-Handler schreiben; `shop.ini [VATSettings]` hat dafür
`RepositoryDirectories[]` und `ExtensionDirectories[]`. Das ist Entwicklerarbeit ([Einen Workflow-Ereignistyp schreiben](../../ersteller/anwendungsentwickler/einen-workflow-ereignistyp-schreiben.md)
zeigt das allgemeine Muster für Erweiterungen; ein Partner kann helfen, siehe [Einen Partner auswählen](../auswahl/einen-partner-auswaehlen.md)).

## Umsatzsteuer außerhalb von Ländern mit Mehrwertsteuer

Der Shop spricht von „VAT“, aber der Mechanismus ist ein Prozentsatz pro Land und Kategorie, sodass ein Shop, der eine
Umsatzsteuer (Sales Tax) berechnet, dieselben Werkzeuge verwenden kann. Eine Steuer, die von einer Region innerhalb
eines Landes abhängt oder die ein Steuerdienst an der Kasse berechnet, deckt der mitgelieferte Handler nicht ab; sie
braucht einen eigenen USt-Handler oder eine Integration.

## Checkliste

- [ ] Ihr Steuerberater hat die Sätze bestätigt und wo sie gelten.
- [ ] USt-Typen sind nach ihrer Bedeutung und ihrem Prozentsatz benannt.
- [ ] Preise verwenden entweder einen festen USt-Typ oder „Determined by VAT charging rules“, einheitlich pro Klasse.
- [ ] Mit Regeln: `Handler=ezdefault` ist in einem Override gesetzt, und die Seite **VAT rules** zeigt keinen Hinweis
  „nicht angewendet“.
- [ ] Mit Regeln: `ProductCategoryAttribute` und `UserCountryAttribute` sind gesetzt, und jedes Produkt hat eine
  Kategorie.
- [ ] Es gibt eine allgemeine Regel „jedes Land, jede Kategorie“.
- [ ] Testbestellungen aus zwei Ländern und zwei Kategorien zeigen auf der Bestätigungsseite die erwartete USt.
- [ ] Das Dashboard zeigt keinen unerwarteten USt-Typ mit 0 Prozent.

## Verwandte Seiten

- [Produkte, Preise und USt](../../ersteller/website-ersteller/produkte-preise-und-mehrwertsteuer.md)
- [Preise und Währungen](preise-und-waehrungen.md)
- [Ländergruppen und Regionen](laendergruppen-und-regionen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
