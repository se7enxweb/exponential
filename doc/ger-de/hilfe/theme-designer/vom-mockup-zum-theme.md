---
title: "Vom Mockup zum Theme"
audience: theme-designer
category: theme-design
order: 5
summary: Der Ablauf von einem Design im Designtool bis zu einem funktionierenden Exponential-Theme - sieben Etappen, wer in jeder was tut, was jede an die nächste übergibt und wo die technischen Schritte dokumentiert sind.
keywords: Theme-Designer, Ablauf, Workflow, Mockup, Prototyp, Übergabe, Designprozess, Etappen, statisches HTML, portieren, Theme-Erweiterung, Review, theme designer, workflow, mockup, prototype, handoff, design process, stages, static html, port, theme extension, review
type: help
---
# Vom Mockup zum Theme

Diese Seite ist für Theme-Designer, die ein Projekt von der ersten Skizze bis zum veröffentlichten Theme planen. Sie
beschreibt sieben Etappen, was jede hervorbringt und wer sie meist verantwortet. Die technischen Schritte (Templates
schreiben, die Erweiterung verpacken) sind anderswo dokumentiert; diese Seite sagt Ihnen, wann sie stattfinden, und
verlinkt darauf.

## Inhalt

1. [Die sieben Etappen](#die-sieben-etappen)
2. [Etappe 1: Bestandsaufnahme](#etappe-1-bestandsaufnahme)
3. [Etappe 2: Grundlagen](#etappe-2-grundlagen)
4. [Etappe 3: Komponenten](#etappe-3-komponenten)
5. [Etappe 4: maßgebliche Vorlagen](#etappe-4-maßgebliche-vorlagen)
6. [Etappe 5: statischer Prototyp](#etappe-5-statischer-prototyp)
7. [Etappe 6: die Theme-Erweiterung](#etappe-6-die-theme-erweiterung)
8. [Etappe 7: Review und Start](#etappe-7-review-und-start)
9. [Wenn Etappen sich überschneiden](#wenn-etappen-sich-überschneiden)

## Die sieben Etappen

| Etappe | Bringt hervor | Verantwortlich | Technische Seite |
|---|---|---|---|
| 1. Bestandsaufnahme | Liste der Klassen, Ansichten, besonderen Seiten, Blöcke | Designer mit Entwickler | [Ein Design auf Inhaltsklassen abbilden](ein-design-auf-inhaltsklassen-abbilden.md) |
| 2. Grundlagen | Tokens: Farbe, Schrift, Abstand, Radius | Designer | [Design-Tokens](../designer/theme-design/design-tokens.md) |
| 3. Komponenten | Komponenteninventar mit Zuständen | Designer | [Komponenteninventar](komponenteninventar.md) |
| 4. Maßgebliche Vorlagen | Rahmen + ein Mockup pro Klasse und Ansicht | Designer | [Ansichtsmodi gestalten](ansichtsmodi-gestalten.md) |
| 5. Statischer Prototyp | HTML und CSS, kein CMS | Designer oder Frontend-Entwickler | [Tutorial: ein statisches HTML-Design portieren](../designer/theme-design/tutorial-ein-statisches-html-design-portieren.md) |
| 6. Theme-Erweiterung | Design-Erweiterung mit Templates und Stylesheets | Frontend-Entwickler | [Tutorial: eine Theme-Erweiterung anlegen](../designer/theme-design/tutorial-eine-theme-erweiterung-anlegen.md) |
| 7. Review und Start | Geprüftes und veröffentlichtes Theme | beide | [Checkliste: Start eines Themes](../designer/theme-design/checkliste-theme-start.md) |

## Etappe 1: Bestandsaufnahme

Listen Sie vor dem Zeichnen auf, was existiert: Inhaltsklassen und ihre Felder, die Ansichtsmodi, in denen jede Klasse
erscheint, die besonderen Seiten (Suche, Anmeldung, Fehler, Formulare) und, wenn die Website Layouts verwendet, die
Blocktypen und Ansichtstypen. Bei einer bestehenden Website bringt der [Plan für den ersten Tag](ihr-erster-tag-als-theme-designer.md)
diese Liste hervor. Bei einer neuen Website kommt sie aus dem Inhaltsmodell ([Ein Inhaltsmodell entwerfen](../../../eng-us/help/cms-developers/designing-a-content-model.md));
setzen Sie sich also mit dem Entwickler zusammen, während die Klassen festgelegt werden: Ein Feld, das nicht in der
Klasse steht, kann nicht in Ihrem Design erscheinen.

**Übergabe:** die Bestandsaufnahme, schriftlich vereinbart.

## Etappe 2: Grundlagen

Legen Sie die kleine Menge von Werten fest, die alles andere verwendet: eine typografische Skala, eine Abstandsskala,
eine Farbpalette mit Rollen, einen Radius, einen Akzent. Geben Sie jedem Wert jetzt einen Rollennamen, denn jeder wird
zu einer CSS Custom Property ([Typografische Skala](typografische-skala.md), [Abstände und Raster](abstaende-und-raster.md),
[Farbpaletten und Farbabstufungen](farbpaletten-und-farbabstufungen.md)). Zeichnen Sie die Werte für den Dunkelmodus gleich
mit; sie später zu ergänzen kostet doppelt so viel.

**Übergabe:** eine Token-Tabelle (Name, heller Wert, dunkler Wert, Verwendung), siehe
[Von Designtool-Variablen zu CSS Custom Properties](von-designtool-variablen-zu-css-custom-properties.md).

## Etappe 3: Komponenten

Zeichnen Sie die wiederverwendbaren Teile (Schaltflächen, Karten, Tags, Formularfelder, Seitennavigation, Hinweise) mit
allen ihren Zuständen: Hover, Fokus, aktiv, deaktiviert, aktuell, Fehler ([Interaktive Zustände gestalten](interaktive-zustaende-gestalten.md)).
Jede Komponente verwendet nur Tokens.

**Übergabe:** das [Komponenteninventar](komponenteninventar.md).

## Etappe 4: maßgebliche Vorlagen

Zeichnen Sie jetzt Seiten, aber denken Sie in Templates: den Rahmen (Kopfbereich, Navigation, Fußbereich) einmal, dann
ein Mockup pro Klasse und Ansichtsmodus, dann die besonderen Seiten. Verwenden Sie echte Inhalte in ihrer kürzesten und
längsten Form ([Mit echten Inhalten gestalten](mit-echten-inhalten-gestalten.md)). Kennzeichnen Sie, welche Teile
optionale Felder sind, damit der Entwickler weiß, was zu verbergen ist, wenn ein Feld leer ist
([Für Redakteure gestalten](fuer-redakteure-gestalten.md)).

**Übergabe:** Rahmen, Ansichten pro Klasse, besondere Seiten, jeweils in drei Breiten, hell und dunkel.

## Etappe 5: statischer Prototyp

Ein statischer Prototyp in HTML und CSS fängt die meisten Layoutprobleme ab, bevor es ein Template gibt. Halten Sie ihn
ehrlich:

- ein Stylesheet für Tokens, eines für Komponenten, keine Inline-Styles;
- Klassennamen mit dem Präfix Ihres Themes, zum Beispiel `mytheme-card`;
- keine Schriften oder Skripte von Hosts Dritter ([Schriften in einem Design](../designer/theme-design/schriften-in-einem-design.md)).

Wer später die Templates schreibt, kann Markup und Stylesheets dann fast unverändert übernehmen, wie das
[Tutorial zum Portieren](../designer/theme-design/tutorial-ein-statisches-html-design-portieren.md) Schritt für Schritt zeigt.

**Übergabe:** der Prototyp im Git-Repository des Themes, siehe [Mit Entwicklern arbeiten](mit-entwicklern-arbeiten.md).

## Etappe 6: die Theme-Erweiterung

Der Prototyp wird zu einer Design-Erweiterung: Der Rahmen wird zu `pagelayout.tpl`, jedes Mockup zu einem
Ansichts-Template, die Stylesheets werden in den Einstellungen des Designs aufgeführt und gepackt. Die Tutorials
behandeln das: [Eine Theme-Erweiterung anlegen](../designer/theme-design/tutorial-eine-theme-erweiterung-anlegen.md) und
[Eine Vollansicht überschreiben](../designer/theme-design/tutorial-eine-vollansicht-ueberschreiben.md). Designer, die keine
Templates schreiben, prüfen in dieser Etappe jedes Template gegen sein Mockup.

## Etappe 7: Review und Start

Prüfen Sie auf einem Vorschau-Siteaccess mit echten Inhalten ([Mit Kunden und Reviews arbeiten](mit-kunden-und-reviews-arbeiten.md)),
gehen Sie die [Checkliste zur Design-Qualitätssicherung](checkliste-design-qualitaetssicherung.md) durch, dann die Checklisten des
Projekts zu Start, Barrierefreiheit, Dunkelmodus und Performance. Ein Theme, das veröffentlicht wird, geht weiter mit
[Ein Theme verteilen: hier beginnen](../designer/veroeffentlichung/ein-theme-veroeffentlichen-hier-beginnen.md).

## Wenn Etappen sich überschneiden

Die Etappen 2 bis 5 überschneiden sich oft, und das ist in Ordnung. Zwei Regeln halten die Überschneidung sicher:

- Ein Token oder eine Komponente ändert sich zuerst im Inventar, dann in Mockups und Code;
- nichts erreicht Etappe 6, das nie mit echten Inhalten gesehen wurde.

## Verwandte Seiten

- [Checkliste zur Übergabe](checkliste-zur-uebergabe.md)
- [Checkliste für das Theme-Design-Review](checkliste-theme-design-review.md)
- [Ein Ausgangs-Theme wählen](../designer/theme-design/ein-ausgangs-theme-waehlen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
