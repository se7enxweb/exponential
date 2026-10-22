---
title: "Für Performance-Budgets gestalten"
audience: theme-designer
category: theme-design
order: 31
summary: Die Designentscheidungen, die bestimmen, wie schnell ein Exponential-Theme ist - ein Budget vor dem Zeichnen vereinbaren, der erste Bildschirm, Schriften, Bilder, Einbettungen Dritter, Seitengewicht pro Template und wie ein Designer eine Seite gegen das Budget prüft.
keywords: Theme-Designer, Performance, Performance-Budget, Seitengewicht, Schriften, Bilder, LCP, CLS, erster Bildschirm, Drittanbieter, Einbettungen, Designentscheidungen, Geschwindigkeit, theme designer, performance budget, page weight, fonts, images, first screen, third parties, embeds, design decisions, speed
type: help
---
# Für Performance-Budgets gestalten

Diese Seite ist für Theme-Designer. Der größte Teil der Geschwindigkeit einer Seite wird entschieden, bevor eine Zeile
Code geschrieben ist: davon, wie viele Schriften das Design verwendet, wie groß das erste Bild ist, ob im Kopfbereich
ein Video läuft und wie viele Widgets Dritter die Seite trägt. Die technischen Hebel stehen in
[Performance von Themes](../designer/theme-design/theme-leistung.md) und
[Checkliste: Performance](../designer/theme-design/checkliste-leistung.md); diese Seite handelt von den
Designentscheidungen dahinter.

## Inhalt

1. [Das Budget vor dem Zeichnen vereinbaren](#das-budget-vor-dem-zeichnen-vereinbaren)
2. [Der erste Bildschirm](#der-erste-bildschirm)
3. [Schriften](#schriften)
4. [Bilder und Video](#bilder-und-video)
5. [Drittanbieter](#drittanbieter)
6. [Stabiles Layout](#stabiles-layout)
7. [Extras für einzelne Seiten](#extras-für-einzelne-seiten)
8. [Eine Seite gegen das Budget prüfen](#eine-seite-gegen-das-budget-prüfen)

## Das Budget vor dem Zeichnen vereinbaren

Die Hilfe für Designer schlägt ein einfaches Budget für eine öffentliche Inhaltsseite vor: eine Stylesheet-Anfrage und
höchstens zwei Skript-Anfragen aus dem Design, keine Anfrage an Dritte vor der Einwilligung, das größte Bild im ersten
Bildschirm in einem Alias nahe seiner angezeigten Größe und Layoutverschiebung (CLS) von höchstens 0,05
([Performance von Themes](../designer/theme-design/theme-leistung.md#ein-einfaches-budget)). Vereinbaren Sie dieses
oder ein strengeres am Anfang mit dem Kunden und schreiben Sie es ins Briefing. Ein Budget, das nach der Freigabe des
Designs vereinbart wird, ist ein Kampf; ein Budget, das vorher vereinbart wird, ist eine Vorgabe, mit der Sie gestalten.

## Der erste Bildschirm

Was ohne Scrollen erscheint, entscheidet, wie schnell sich die Seite anfühlt. Notieren Sie für jedes Template das größte
Element des ersten Bildschirms (meist die Schlagzeile oder das Hero-Bild): Das misst der Largest Contentful Paint
([CLS und LCP messen](../designer/theme-design/cls-und-lcp-messen.md)).

- Ein großes Hero-Bild auf jeder Seite ist die häufigste Ursache eines langsamen ersten Bildschirms. Entscheiden Sie pro
  Klasse, ob es gebraucht wird.
- Designs, bei denen der Text vorangeht, sind von sich aus schnell: Eine kräftige Schlagzeile in einer System- oder
  selbst gehosteten Schrift erscheint sofort.
- Hintergrundvideos und automatische Slider im ersten Bildschirm sind teuer; bieten Sie ein Standbild mit einer
  Abspielschaltfläche an.

## Schriften

Jede Schriftfamilie und jeder Schnitt ist eine Datei. Zwei Familien und insgesamt drei oder vier Schnitte sind eine
vernünftige Obergrenze. Schriften werden im Design selbst gehostet, nie von einem Schriftendienst geladen
([Schriften in einem Design](../designer/theme-design/schriften-in-einem-design.md)). Ein Stapel aus Systemschriften kostet
nichts und kann für Fließtext eine gute Designentscheidung sein. Wählen Sie variable Schriften, wo sie mehrere Schnitte
ersetzen.

## Bilder und Video

- Jeder Bildplatz erhält einen Alias nahe seiner angezeigten Größe, nicht das Original
  ([Bildzuschnitte und Art Direction gestalten](bildzuschnitte-und-art-direction-gestalten.md)).
- Dekorative Bilder sind ein Kostenfaktor: Bevorzugen Sie CSS-Formen, Verläufe aus Tokens und kleine SVG-Dateien.
- Symbole als ein kleiner Satz SVG-Dateien, nicht als Symbolschrift mit Hunderten von Glyphen
  ([Symbole in einem Design](../designer/theme-design/icons-in-einem-design.md)).
- Video: ein Vorschaubild und eine Abspielschaltfläche; der Player lädt auf Anforderung.

## Drittanbieter

Karten, Videoplayer, Feeds aus sozialen Netzwerken, Chat-Widgets, Statistikdienste und Captchas laden jeweils Skripte
und oft Schriften von anderen Hosts, was auch Datenschutz und die Content Security Policy betrifft
([Externe Ressourcen und CDNs](../designer/theme-design/externe-ressourcen-und-cdns.md)). Gestalten Sie für jede
Einbettung einen Platzhalter, der sie erst lädt, wenn der Besucher danach fragt (ein statisches Kartenbild mit "Karte
anzeigen"), und listen Sie jeden Drittanbieter im Briefing auf, damit der Kunde die Kosten sieht.

## Stabiles Layout

Seiten, die beim Laden springen, wirken langsam und verursachen Fehlklicks. Designs, die das vermeiden:

- jedes Bild und jede Einbettung hat im Design einen Kasten mit festem Seitenverhältnis;
- Banner, Cookie-Hinweise und Meldungen schieben Inhalte nicht nach unten, nachdem sie erschienen sind;
- Schriften mit ähnlichen Maßen wie ihre Ersatzschrift, damit Text nicht neu umbricht, wenn die Schrift eintrifft.

## Extras für einzelne Seiten

Stylesheets und Skripte, die nur ein Template braucht (die Lightbox einer Galerie, die Datumsauswahl eines Formulars),
lassen sich aus diesem Template anfordern, statt auf jeder Seite geladen zu werden
([Performance von Themes](../designer/theme-design/theme-leistung.md#im-browser)). Kennzeichnen Sie solche
Funktionen bei der Übergabe, damit der Entwickler weiß, dass sie seitenspezifisch sind.

## Eine Seite gegen das Budget prüfen

1. Öffnen Sie die Seite auf der Entwicklungsinstallation in einem privaten Fenster, mit den Entwicklerwerkzeugen des
   Browsers auf dem Netzwerk-Reiter und deaktiviertem Cache.
2. Zählen Sie die Anfragen nach Typ und notieren Sie jede Anfrage an einen anderen Host.
3. Führen Sie die Performance-Prüfung des Browsers für die Seite in Telefongröße aus und notieren Sie LCP und CLS.
4. Vergleichen Sie mit dem Budget; alles, was darüber liegt, kommt als Problem in die
   [Checkliste zur Design-Qualitätssicherung](checkliste-design-qualitaetssicherung.md).

## Verwandte Seiten

- [Performance von Themes](../designer/theme-design/theme-leistung.md)
- [Performance von Bildern](../designer/theme-design/bildleistung.md)
- [Eine Performance-Prüfung für jedes Release](../designer/lebenszyklus/leistungspruefung-pro-release.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
