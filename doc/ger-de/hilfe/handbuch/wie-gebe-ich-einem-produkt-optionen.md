---
title: Wie gebe ich einem Produkt Optionen wie Größe oder Farbe?
audience: editor
category: manual
order: 170
summary: Fügen Sie im Optionsfeld des Produkts für jede Auswahl eine Mehrfachauswahl hinzu (zum Beispiel Größe), fügen Sie ihre Optionen mit einem eventuellen Aufpreis hinzu, markieren Sie die Standardoption und veröffentlichen Sie; eine Klasse mit dem neueren Mehrfachoptionsfeld bietet außerdem Gruppen, Unterebenen, Bilder pro Option, deaktivierte Optionen und Regeln.
keywords: Produktoptionen, Varianten, Mehrfachauswahl, Optionsgruppe, Aufpreis, zusätzlicher Preis, Standardoption, Option hinzufügen, Größe, Farbe, Bereichsoption, product options, variants, multioption, option set, additional price, default option, add option, add multioption, multioption2, rules, size, color, range option
type: help
---
# Wie gebe ich einem Produkt Optionen wie Größe oder Farbe?

Mit Optionen wählt ein Kunde eine Variante, wenn er ein Produkt in den Warenkorb legt, zum Beispiel eine Größe oder
eine Farbe, jeweils mit einem möglichen Aufpreis. Die Produktklasse bestimmt, welche Art von Optionsfeld sie hat; in
einer neuen Installation hat die Klasse **Produkt** das Feld **Additional options**.

## Wer das tun kann

Redakteure, die das Produkt bearbeiten dürfen.

## Schritte

1. Bearbeiten Sie das Produkt und gehen Sie zu seinem Optionsfeld (**Additional options** beim Standardprodukt).
2. Geben Sie einen **Name der Gruppe von Optionen** ein, wenn das Feld danach fragt.
3. Klicken Sie für jede Auswahl, die der Kunde trifft, auf **Neue Mehrfachauswahl hinzufügen** und geben Sie ihr einen
   **Name**, zum Beispiel *Größe*.
4. Klicken Sie darunter für jeden Wert auf **Option hinzufügen**, zum Beispiel *S*, *M* und *L*.
5. Geben Sie für jede Option den **Zusätzlicher Preis** ein (lassen Sie 0 stehen, wenn es keinen Aufpreis gibt) und,
   wo angezeigt, eine **Priorität**, um die Optionen zu ordnen.
6. Wählen Sie die Option **Standard** mit ihrem Optionsfeld.
7. Um etwas herauszunehmen, haken Sie es an und klicken Sie auf **Ausgewählte entfernen**.
8. Klicken Sie auf **Zur Veröffentlichung schicken**, öffnen Sie dann das Produkt auf der Website und probieren Sie die
   Auswahl aus.

### Das neuere Mehrfachoptionsfeld

Manche Klassen verwenden stattdessen das neuere Mehrfachoptionsfeld. Es bietet zusätzlich:

- **Gruppe hinzufügen** und **Eine Unterebene der Mehrfachauswahl hinzufügen**, für Auswahlen, die von einer
  früheren abhängen (zum Beispiel ein Modell, dann die Farben dieses Modells);
- ein **Bild** pro Option, das dem Kunden gezeigt wird;
- **Deakt.**, um eine Option anzubieten, die sich nicht wählen lässt, zum Beispiel um eine echte Auswahl statt einer
  Standardoption zu erzwingen;
- **Regeln**, ein Raster, welche Optionen sich kombinieren lassen; **Regeln zurücksetzen** erlaubt wieder alles.

## Was schiefgehen kann

- **Die Optionen erscheinen nicht auf der Website.** Das Template der Produktseite zeigt dieses Feld nicht. Fragen Sie
  die Person, die sich um das Design kümmert.
- **Der Warenkorb zeigt einen unerwarteten Preis.** Der zusätzliche Preis jeder gewählten Option wird zum Preis des
  Produkts addiert. Prüfen Sie beide Felder und den USt-Typ des Preises: siehe
  [Wie richte ich die USt für meine Produkte ein?](wie-richte-ich-die-ust-ein.md)
- **Die Klasse hat kein Optionsfeld.** Bitten Sie einen Administrator, der Klasse eines hinzuzufügen (**Setup** >
  **Klassen**).

## Verwandte Seiten

- [Optionen und Varianten](../../../eng-us/help/e-commerce/catalog/options-and-variants.md)
- [Ein Produkt hinzufügen](../kunden/handel/ein-produkt-anlegen.md)
- [Produkte und Katalog](../kunden/handel/produkte-und-katalog.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
