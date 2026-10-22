---
title: "Wie sich Exponential-Themes unterscheiden"
audience: theme-designer
category: theme-design
order: 4
summary: Für Theme-Designer, die Themes für andere Systeme gebaut haben. Die sechs Ideen, die ein Exponential-Theme anders machen, jeweils in wenigen Sätzen, mit der Seite, die sie ausführlich erklärt.
keywords: Theme-Designer, Unterschiede, Design-Kaskade, Overrides, Pagelayout, Ansichtsmodi, Inhaltsklassen, Packer, von anderen Systemen kommen, Denkmodell, theme designer, differences, design cascade, overrides, pagelayout, view modes, content classes, packer, coming from other systems, mental model
type: help
---
# Wie sich Exponential-Themes unterscheiden

Diese Seite ist für Theme-Designer, die Themes für ein anderes Content-Management-System oder für statische Websites
gemacht haben. Sie nennt die sechs Ideen, die in Exponential anders funktionieren, jeweils in wenigen Sätzen, und
verweist auf die Seite, die jede gründlich erklärt. Lesen Sie sie, bevor Sie Ihr Designtool öffnen, denn mehrere
dieser Ideen ändern, was Sie zeichnen sollten.

## Inhalt

1. [Ein Theme ist ein Stapel, kein einzelner Ordner](#ein-theme-ist-ein-stapel-kein-einzelner-ordner)
2. [Seiten sind Inhalte, keine Dateien](#seiten-sind-inhalte-keine-dateien)
3. [Ein Objekt, viele Ansichten](#ein-objekt-viele-ansichten)
4. [Rahmen und Inhalt sind getrennt](#rahmen-und-inhalt-sind-getrennt)
5. [Assets werden gepackt, nie inline](#assets-werden-gepackt-nie-inline)
6. [Redakteure bauen manche Seiten selbst](#redakteure-bauen-manche-seiten-selbst)
7. [Was das für Ihre Designdateien bedeutet](#was-das-für-ihre-designdateien-bedeutet)

## Ein Theme ist ein Stapel, kein einzelner Ordner

Ein Siteaccess (ein Zugang zur Installation, oft eine Website oder eine Sprache) nennt eine Liste von Designs. Für jedes
Template, jedes Stylesheet und jedes Bild durchsucht der Kernel diese Liste der Reihe nach und nimmt die erste Datei,
die er findet. Das ist die **Design-Kaskade**. Ihr Theme enthält deshalb nur, was sich von den Designs darunter
unterscheidet; alles andere fällt zu ihnen durch.

Für einen Designer heißt das: Sie können von einer funktionierenden Website ausgehen und sie Stück für Stück ändern. Sie
müssen nie "alles" gestalten, bevor irgendetwas erscheint.

Lesen Sie: [Die Design-Kaskade](../designer/theme-design/die-design-kaskade.md),
[Was ein Design ist](../designer/theme-design/was-ein-design-ist.md).

## Seiten sind Inhalte, keine Dateien

In einem Theme gibt es keine Seitendateien. Jede Seite ist ein **Knoten** eines Inhaltsobjekts einer **Inhaltsklasse**
(Artikel, Ordner, Veranstaltung, Produkt ...), und ein Template zeichnet sie. Sie gestalten ein Artikel-Template, und es
zeichnet jeden Artikel. Bestimmte Knoten, Sektionen oder übergeordnete Ordner können über **Overrides** ein eigenes
Template bekommen.

Für einen Designer heißt das: Ihre Mockups sollten pro Inhaltsklasse und pro Ansicht entstehen, nicht pro URL. Eine
"Seite" in einem Mockup ist meist eine Klasse in ihrer Vollansicht innerhalb des Rahmens.

Lesen Sie: [Ein Design auf Inhaltsklassen abbilden](ein-design-auf-inhaltsklassen-abbilden.md),
[Grundlagen der Overrides](../designer/vorlagen/override-grundlagen.md).

## Ein Objekt, viele Ansichten

Derselbe Artikel erscheint als ganze Seite, als Zeile in einer Liste, als Karte in einem Block, als Einbettung in einem
anderen Text. Jede davon ist ein **Ansichtsmodus** mit eigenem Template. Ein Design, das nur die ganze Seite zeigt, ist
ein halbes Design.

Lesen Sie: [Ansichtsmodi gestalten](ansichtsmodi-gestalten.md), [Ansichtsmodi](../designer/vorlagen/ansichtsmodi.md).

## Rahmen und Inhalt sind getrennt

Das **Pagelayout** (`pagelayout.tpl`) ist der Rahmen: Head, Kopfbereich, Navigation, Fußbereich. Das Modul
(Inhaltsansicht, Suche, Benutzeranmeldung ...) erzeugt die Mitte, die das Pagelayout ausgibt. Kopf- und Fußbereich
werden einmal gestaltet und erscheinen um alles herum, auch um Such-, Anmelde- und Fehlerseiten.

Lesen Sie: [Das Pagelayout](../designer/theme-design/das-pagelayout.md),
[Kopf- und Fußbereiche gestalten](kopf-und-fussbereiche-gestalten.md).

## Assets werden gepackt, nie inline

Stylesheets und Skripte werden in Einstellungen aufgeführt und vom **Packer** von ezjscore zu gecachten Dateien
zusammengeführt. Templates enthalten keine Blöcke `<style>`, keine Attribute `style=""` und keine Inline-Skripte, damit
eine strenge Content Security Policy verwendet werden kann. Farben, Abstände und Schriften kommen aus **Design-Tokens**
(CSS Custom Properties), jede Farbe mit einem Wert für den Dunkelmodus.

Für einen Designer heißt das: Jeder visuelle Wert muss sich als Token benennen lassen, und jede Variante muss sich als
Klasse ausdrücken lassen. Planen Sie das in Ihrem Designtool ein.

Lesen Sie: [Stylesheets laden](../designer/theme-design/stylesheets-laden.md),
[Kein Inline-CSS](../designer/theme-design/kein-inline-css.md), [Design-Tokens](../designer/theme-design/design-tokens.md),
[Von Designtool-Variablen zu CSS Custom Properties](von-designtool-variablen-zu-css-custom-properties.md).

## Redakteure bauen manche Seiten selbst

Mit Exponential Layouts setzen Redakteure **Blöcke** (Text, Liste, Galerie, Hero ...) in die **Zonen** eines Layouts
und wählen für jeden Block einen **Ansichtstyp**. Ihr Design liefert das Aussehen jedes Blocks und Ansichtstyps, aber
Sie entscheiden nicht, welche Blöcke in welcher Reihenfolge auf diesen Seiten erscheinen.

Lesen Sie: [Layoutblöcke entwerfen](layoutbloecke-gestalten.md),
[Layouts für Designer](../designer/theme-design/layouts-fuer-designer.md).

## Was das für Ihre Designdateien bedeutet

| Gewohnheit von anderswo | In Exponential |
|---|---|
| Ein Mockup pro Seite | Ein Mockup pro Inhaltsklasse und Ansichtsmodus, dazu der Rahmen |
| Fest eingetragene Farben und Größen | Benannte Tokens, mit Werten für den Dunkelmodus |
| Pixelgenauer Beispieltext | Echte Inhalte in ihrer kürzesten und längsten Form ([Mit echten Inhalten gestalten](mit-echten-inhalten-gestalten.md)) |
| Eine feste Startseite | Oft ein Layout, das Redakteure aus Blöcken bauen |
| Ein Theme ersetzt alles | Ein Design überschreibt nur, was es ändert |

## Verwandte Seiten

- [Theme-Designer: hier beginnen](theme-designer-hier-beginnen.md)
- [Designer: hier beginnen](../designer/designer-hier-beginnen.md)
- [Ein Ausgangs-Theme wählen](../designer/theme-design/ein-ausgangs-theme-waehlen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
