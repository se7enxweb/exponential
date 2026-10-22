---
title: Alternativtext für Bilder
audience: ai-agents, editor, admin
category: ai-agents
order: 28
summary: Wie Exponential Intelligence den Alternativtext von Bildern mit einem Vision-Modell vorschlägt - die Schaltflächen Write alt text und Decorative im Bearbeitungsformular, welches Modell das Bild ansieht und wie die Datenrichtlinie Bilder von einem Anbieter fernhalten kann, der Bericht Missing alt text für einen Teilbaum und warum die Wahl "dekorativ" immer beim Redakteur bleibt.
keywords: Alternativtext, Alt-Text, Barrierefreiheit, Bilder, Vision-Modell, Write alt text, dekoratives Bild, Bericht Missing alt text, AltTextSettings, Datenrichtlinie für Bilder, WCAG, alt text, alternative text, accessibility, images, vision model, decorative image, data policy for images
type: help
---
# Alternativtext für Bilder

Diese Seite richtet sich an Redakteure, die jedem Bild einen nützlichen Alternativtext geben müssen, und an Administratoren, die
entscheiden, ob Bilder an einen KI-Anbieter gehen dürfen. Exponential Intelligence kann den Alternativtext vorschlagen; der Redakteur
entscheidet.

## Inhalt

1. [Im Bearbeitungsformular](#im-bearbeitungsformular)
2. [Welches Modell das Bild ansieht](#welches-modell-das-bild-ansieht)
3. [Bilder und die Datenrichtlinie](#bilder-und-die-datenrichtlinie)
4. [Viele Bilder auf einmal](#viele-bilder-auf-einmal)
5. [Gute Praxis](#gute-praxis)

## Im Bearbeitungsformular

Jedes Bildfeld erhält zwei Schaltflächen über seinem Feld "Alternativer Bildtext"
([Dokumentation der Erweiterung: Alternativtext](https://github.com/se7enxweb/expai/blob/main/doc/alt-text.md)):

| Schaltfläche | Was sie tut |
|---|---|
| **Write alt text** | Sendet das gespeicherte Bild dieses Entwurfs, den Seitentitel, den Text der anderen Felder und die Sprache an ein Vision-Modell. Die Antwort kommt in das Feld für den Alternativtext; **Undo** stellt wieder her, was dort stand. Ein Hinweis nennt das Modell, die Größe des gesendeten Bildes und die Kosten |
| **Decorative (empty alt text)** | Ihre ausdrückliche Wahl für ein Bild, das keine Information trägt: Das Feld wird geleert, Screenreader überspringen das Bild. Es wird keine Anfrage gestellt |

- **Das Modell entscheidet nie, dass ein Bild dekorativ ist.** Wenn es das meint, schreibt es trotzdem eine Beschreibung und sagt
  das im Hinweis. Sie wählen.
- Bei einem Feld, dessen Klasse einen Alternativtext verlangt, ist die Schaltfläche Decorative mit Begründung deaktiviert.
- Das Bild muss zuerst **im Entwurf gespeichert** sein: Eine neu gewählte Datei wird erst nach "Entwurf speichern" verwendet.
- Werte gehen nur in das Formular; der Entwurf wird wie üblich gespeichert oder veröffentlicht und als mit Exponential Intelligence
  geschrieben gekennzeichnet.

## Welches Modell das Bild ansieht

Nur Modelle mit der Fähigkeit `vision`. Das erste, das passt, in dieser Reihenfolge: das vom Administrator gesetzte Modell für
Alternativtext, Ihre eigene Modellwahl, der Standard der Website, dann jedes andere Vision-Modell. Ein Modell wird übersprungen,
wenn Sie es nicht verwenden dürfen, sein Server angehalten ist, sein Anbieter keinen Schlüssel hat oder die Datenrichtlinie das Bild
von ihm fernhält. Bleibt keines übrig, ist die Schaltfläche deaktiviert und sagt, warum.

## Bilder und die Datenrichtlinie

Das Bild ist Inhalt seines Objekts: Die Regeln der Datenrichtlinie für Sektion und Klasse gelten dafür, und
`[AltTextSettings] ImageRegions[]` und `ImageZeroRetention` können Bilder strengeren Regeln als Text unterwerfen. Das Bild reist
in der Anfrage, nie als Link, den der Anbieter später abrufen könnte, und das Anfrageprotokoll behält die Pixel nie
([Datenschutz und Daten, die die Website verlassen](../steuerung/datenschutz-und-daten-die-die-website-verlassen.md)).

## Viele Bilder auf einmal

Der Bericht **Missing alt text** (`expai/alttext`, mit `expai/write`) listet die Bilder eines Teilbaums ohne Alternativtext und
reiht über die Job-Warteschlange Vorschläge dafür als **Entwürfe** ein. Jeder Job prüft die Datenrichtlinie erneut, wenn er läuft.
Nichts wird veröffentlicht; jeder Vorschlag wartet auf Prüfung.

## Gute Praxis

- Lesen Sie jeden Vorschlag gegen das Bild und die Seite: Alternativtext beschreibt, was das Bild hier bedeutet, nicht nur, was es
  zeigt.
- Entfernen Sie Vermutungen: Namen von Personen, Orten oder Produkten, die das Modell nicht wissen konnte.
- Halten Sie ihn kurz; lange Beschreibungen gehören in den Seitentext.
- Verwenden Sie Decorative nur für Bilder, die wirklich keine Information tragen.

Die Seiten der Hilfe zur Barrierefreiheit gehen weiter: [Für Barrierefreiheit eintreten](../../fuersprecher/fuer-barrierefreiheit-eintreten.md).

## Verwandte Seiten

- [Was Exponential Intelligence tut](was-exponential-intelligence-tut.md)
- [KI-Entwürfe prüfen](../checklisten/ki-entwuerfe-pruefen.md)
- [Medien und Uploads](../../medien-und-uploads.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
