---
title: "Häufige Fragen von Theme-Designern"
audience: theme-designer
category: theme-design
order: 42
summary: Kurze Antworten auf die Fragen, die Theme-Designer am häufigsten zu Exponential stellen - muss ich Templates schreiben, warum keine Inline-Stile, wie der Dunkelmodus funktioniert, warum meine Änderung nicht erscheint, wie Redakteure Seiten ändern, Schriften, Bilder, Multisite, die DXP und der Verkauf von Themes - jeweils mit Link auf die ausführliche Seite.
keywords: Theme-Designer, FAQ, häufige Fragen, Antworten, Templates, Inline-Stile, Dunkelmodus, Cache, Schriften, Bilder, Layouts, Multisite, DXP, Themes verkaufen, theme designer, faq, questions, answers, inline styles, dark mode, fonts, images, selling themes
type: help
---
# Häufige Fragen von Theme-Designern

Diese Seite ist für Theme-Designer, die eine schnelle Frage haben. Jede Antwort ist kurz und verlinkt auf die Seite mit
den Einzelheiten.

## Inhalt

1. [Der Einstieg](#der-einstieg)
2. [Regeln und ihre Gründe](#regeln-und-ihre-gründe)
3. [Wenn etwas nicht erscheint](#wenn-etwas-nicht-erscheint)
4. [Inhalte, Redakteure und Layouts](#inhalte-redakteure-und-layouts)
5. [Bilder, Schriften und Sprachen](#bilder-schriften-und-sprachen)
6. [Andere Produkte und Geschäftliches](#andere-produkte-und-geschäftliches)

## Der Einstieg

**Muss ich Templates schreiben, um für Exponential zu gestalten?**
Nein. Viele Designer übergeben Designs und prüfen die gebauten Seiten; andere schreiben Stylesheets und manche auch
Templates. Vereinbaren Sie, wer wofür verantwortlich ist ([Mit Entwicklern arbeiten](mit-entwicklern-arbeiten.md#wer-wofür-verantwortlich-ist)).
Wenn Sie wissen, wie Templates und Ansichtsmodi funktionieren, lassen sich Ihre Designs leichter bauen
([Wie sich Exponential-Themes unterscheiden](wie-sich-exponential-themes-unterscheiden.md)).

**Wo fange ich bei einer Website an, die es schon gibt?**
Mit dem [Plan für den ersten Tag](ihr-erster-tag-als-theme-designer.md), dann mit
[Eine bestehende Website neu gestalten](eine-bestehende-website-neu-gestalten.md).

**Soll ich mit einem mitgelieferten Theme beginnen oder bei null?**
Das hängt vom Projekt ab; die Hilfe für Designer hat eine Entscheidungstabelle ([Ein Ausgangs-Theme wählen](../designer/theme-design/ein-ausgangs-theme-waehlen.md)).

## Regeln und ihre Gründe

**Warum sind Inline-Stile nicht erlaubt?**
Damit eine strenge Content Security Policy möglich ist, Stile an einer Stelle bleiben und Tokens und Dunkelmodus überall
funktionieren. Die einzige dokumentierte Ausnahme sind Skins für Newsletter-E-Mails, weil E-Mail-Programme Inline-Stile
brauchen ([Kein Inline-CSS](../designer/theme-design/kein-inline-css.md), [Newsletter-E-Mails gestalten](newsletter-e-mails-gestalten.md#e-mail-ist-nicht-das-web)).

**Warum braucht jede Farbe einen dunklen Wert?**
Weil ein Token, der ohne dunklen Wert hinzugefügt wird, den Dunkelmodus an dem Tag zerbricht, an dem er verwendet wird,
meist als weißer Kasten auf einer dunklen Seite. Es ist eine Projektregel
([Design-Tokens](../designer/theme-design/design-tokens.md#regeln-die-token-nützlich-halten)).

**Warum kein `?v=` an Stylesheet-Links?**
Der Packer schreibt die Änderungszeit bereits in den Namen der gepackten Datei, sodass Browser die neue Datei bekommen
([Keine Versionsabfragen an Asset-Links](../designer/theme-design/keine-versionsparameter-an-asset-links.md)).

**Welche Breiten teste ich?**
1440 px, 960 px bei 200 % Zoom und 375 px, hell und dunkel, mit der Tastatur
([Checkliste zur Design-Qualitätssicherung](checkliste-design-qualitaetssicherung.md)).

## Wenn etwas nicht erscheint

**Ich habe ein Stylesheet geändert, und nichts hat sich geändert.**
Gepackte Dateien und Caches müssen nach einer Änderung erneuert werden; bitten Sie den Entwickler, sie zu deployen, und
laden Sie dann hart neu ([Der Packer-Cache und Deployments](../designer/theme-design/packer-cache-und-deployment.md)).

**Mein neues Template wird ignoriert.**
Neue Template-Dateien brauchen einen geleerten Cache der Template-Overrides, und die Datei muss in einem Design dieses
Siteaccess liegen ([Ein Design debuggen](../designer/theme-design/ein-design-debuggen.md#mein-neues-template-oder-mein-override-wird-ignoriert)).

**Welches Template hat diesen Teil der Seite gezeichnet?**
Schalten Sie auf einem Entwicklungs-Siteaccess die Kommentare mit Template-Pfaden ein
([Werkzeuge für Theme-Designer](werkzeuge-fuer-theme-designer.md#sehen-welches-template-was-gezeichnet-hat)).

## Inhalte, Redakteure und Layouts

**Können Redakteure mein Design kaputt machen?**
Nur wenn es davon abhängt, dass die Inhalte genau passen. Gestalten Sie für die Extreme und für leere Felder
([Für Redakteure gestalten](fuer-redakteure-gestalten.md)).

**Können Redakteure Seiten selbst bauen?**
Ja, mit dem Layout-Editor, aus den Blöcken und Ansichtstypen, die Ihr Design bereitstellt
([Layoutblöcke entwerfen](layoutbloecke-gestalten.md)).

**Wie gebe ich einer Seite ein besonderes Aussehen?**
Ein Override für diese Seite, ein Layout oder eine eigene Klasse; jede Möglichkeit hat ihren Preis
([Einen einzelnen Knoten überschreiben](../designer/vorlagen/einen-einzelnen-knoten-ueberschreiben.md#alternativen),
[Die Startseite gestalten](die-startseite-gestalten.md)).

**Ein Feld, das ich brauche, gibt es nicht.**
Fragen Sie den Entwickler, mit Begründung und Einzelheiten; über das Inhaltsmodell wird gemeinsam entschieden
([Ein Design auf Inhaltsklassen abbilden](ein-design-auf-inhaltsklassen-abbilden.md#wenn-das-design-ein-feld-braucht-das-es-nicht-gibt)).

## Bilder, Schriften und Sprachen

**Lassen sich Bilder automatisch um das Motiv herum zuschneiden?**
Nicht mit den mitgelieferten Filtern: Sie skalieren und schneiden an festen Positionen. Verwenden Sie CSS-Zuschnitte,
oder Redakteure schneiden mit dem Bildeditor zu, wo er installiert ist
([Bildzuschnitte und Art Direction gestalten](bildzuschnitte-und-art-direction-gestalten.md)).

**Kann ich eine Schrift von einem Schriftendienst verwenden?**
Schriften werden aus Gründen des Datenschutzes und der Geschwindigkeit im Design selbst gehostet, und nur mit einer
Lizenz, die das erlaubt ([Schriften in einem Design](../designer/theme-design/schriften-in-einem-design.md)).

**Wie gestalte ich für Arabisch oder Hebräisch?**
Verwenden Sie logische CSS-Eigenschaften und spiegeln Sie die wichtigen Bildschirme
([Für viele Sprachen gestalten](fuer-viele-sprachen-gestalten.md#von-rechts-nach-links)).

**Mehrere Marken auf einer Installation?**
Ein gemeinsames Design plus ein kleines Marken-Design pro Website, vor allem Tokens und Logos
([Für mehrere Marken im Multisite-Betrieb gestalten](fuer-mehrere-marken-in-multisite-gestalten.md)).

## Andere Produkte und Geschäftliches

**Der Kunde betreibt Exponential Platform DXP. Gilt dieser Bereich?**
Der Designablauf gilt; die Template-Sprache und die Dateiablage unterscheiden sich
([Themes für Exponential Platform](themes-fuer-exponential-platform.md)).

**Darf ich Themes verkaufen, die ich mache?**
Ja, im Rahmen der GPL; der Vertriebsbereich der Hilfe für Designer erklärt Modelle und Bedingungen
([Themes und Dienstleistungen verkaufen: hier beginnen](../designer/verkauf/verkaufen-hier-beginnen.md)).

**Gibt es eine offizielle Design-Zertifizierung für Exponential?**
Das Partnerprogramm nennt eine Design-Zertifizierung, aber ihr Lehrplan und ihre Prüfungen sind nicht veröffentlicht
([Zertifizierungen](../partner/zertifizierungen.md)).

**Hat Exponential mit eZ Systems oder Ibexa zu tun?**
Nein. Exponential setzt die Codebasis von eZ Publish fort, einem Produkt, das sein Hersteller nach fast 18 Jahren
eingestellt hat, und wird von 7x mit der Exponential Foundation geführt ([Wer ist 7x?](../wer-ist-7x.md)).

## Verwandte Seiten

- [Theme-Designer: hier beginnen](theme-designer-hier-beginnen.md)
- [Glossar für Theme-Designer](glossar-fuer-theme-designer.md)
- [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
