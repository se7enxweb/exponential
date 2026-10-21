---
title: "Lektion 3.3: Schaltflächen, Bilder und der Tab Design"
audience: trainer, editor, website-creator, designer
category: training
order: 33
summary: Die Teilnehmenden fügen einen Button-/Link- und einen Image-Block hinzu, legen ein Linkziel und einen Alternativtext fest und verwenden Ansichtstyp, Name und CSS-Klasse im Tab Design, um das Aussehen eines Blocks ohne Template zu ändern. 45 Minuten.
keywords: exponential layouts, schaltflächenblock, link, ziel, bildblock, alternativtext, design-tab, ansichtstyp, blockname, css-klasse, css-id, button block, target, image block, alt text, design tab, view type, block name, css class, css id
type: help
---
# Lektion 3.3: Schaltflächen, Bilder und der Tab Design

Handlungsaufforderungen und Bilder machen eine Seite wirksam. Diese Lektion fügt eine Schaltfläche und ein Bild hinzu
und zeigt dann den Tab **Design**: den Ort, an dem Form und Stil eines Blocks ohne Entwickler gewählt werden.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Layouts](../README.md) |
| Modul | [Modul 3: Blöcke](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Redakteure, Website-Ersteller und Designer |
| Voraussetzungen | [Lektion 3.2](textbloecke.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- einen **Button / Link**-Block mit Text, Linkadresse und Ziel hinzufügen;
- einen **Image**-Block mit einem Alternativtext hinzufügen;
- den **Ansichtstyp** eines Blocks wählen, wo er mehrere hat;
- einem Block einen **Namen** und eine **CSS-Klasse** aus dem Theme der Website geben.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Exponential Layouts: Blockparameter](../../../../../eng-us/bc/6.0/LAYOUTS.md#block-parameters) und
[Layout-Typen, Zonen und Blöcke](../../../../../eng-us/help/creators/website-creators/layout-types-zones-and-blocks.md#view-types-and-parameters).

1. **Button / Link** (3 Minuten). Zu seinen Einstellungen gehören der Schaltflächentext, die Linkadresse, ein Ziel
   (im selben oder in einem neuen Fenster öffnen) und ein Stil oder eine CSS-Klasse. Die genauen Feldnamen hängen vom
   Block-Handler Ihrer Installation ab; zeigen Sie sie am Beamer.
2. **Image** (3 Minuten). Ein Bild mit seiner Adresse und einem **Alternativtext** sowie einem optionalen Link.
   Schreiben Sie den Alternativtext wie für jedes Bild (Redakteure 201, Lektion 8.2).
3. **Ansichtstyp** (3 Minuten). Der Ansichtstyp eines Blocks ist die Form, in der er dargestellt wird. Die meisten
   einfachen Blöcke haben einen (`default`); Listenblöcke haben mehrere (Modul 4).
4. **Name, CSS-ID, CSS-Klasse** (4 Minuten). Jeder Block hat einen **Namen** (eine Beschriftung für Redakteure, um
   ähnliche Blöcke auseinanderzuhalten), eine **CSS-ID** und eine **CSS-Klasse**, die in den äußeren Container des
   Blocks geschrieben werden. Die CSS-Klasse ist die Nahtstelle zwischen Redakteur und Designer: Das Theme liefert
   Klassen (einen Hintergrund, einen Abstand), der Redakteur wendet sie pro Block an, niemand bearbeitet ein Template.
5. **Den Designer fragen** (2 Minuten). Die Teilnehmenden brauchen eine Liste der Klassen, die ihr Theme anbietet.
   Teilen Sie eine aus oder verwenden Sie die Klassen, die der Trainer vorbereitet hat.

### Häufige Fragen

**Wo finde ich die CSS-Klassen, die ich verwenden darf?** Beim Designer Ihrer Website oder in der Dokumentation des
Themes. Eine Klasse, die das Theme nicht definiert, ändert nichts.

**Sollte das Bild ein Block oder ein Inhaltselement sein?** Für ein Bild, das zur Anordnung dieser Seite gehört, ist
ein Block in Ordnung; Bilder, die auf mehreren Seiten verwendet werden, gehören in die Medienbibliothek.

**Warum öffnet meine Schaltfläche den Link in einem neuen Fenster?** Das Ziel ist auf ein neues Fenster gesetzt;
setzen Sie es auf dasselbe Fenster zurück, sofern es keinen Grund dafür gibt.

### Stolperfallen

- **Leere Alternativtexte.** Der Bildblock hat ein Feld dafür; füllen Sie es.
- **CSS-Klassen erfinden.** Nur Klassen des Themes wirken.
- **Leer gelassene Namen.** Zehn Blöcke namens „Rich text“ lassen sich kaum unterscheiden. Benennen Sie sie.

## Praktische Übung

Verwenden Sie die Schulungsinstallation und Ihr Kurs-Layout. Der Trainer hat zwei CSS-Klassen des Themes an die
Tafel geschrieben.

1. Öffnen Sie Ihr Kurs-Layout mit **Öffnen** (setzen Sie Ihren Entwurf fort, wenn Sie gefragt werden).
2. Fügen Sie in *MAIN* unter dem Markdown-Block einen **Button / Link**-Block hinzu.
3. Setzen Sie unter **Inhalt** den Schaltflächentext auf `Contact us`, die Linkadresse auf die Adresse, die der
   Trainer nennt, und das Ziel auf dasselbe Fenster.
4. Fügen Sie in *POST_HEADER* unter dem Titel einen **Image**-Block hinzu. Legen Sie seine Bildadresse fest (der
   Trainer nennt eine aus der Medienbibliothek der Website) und einen Alternativtext, der das Bild beschreibt.
5. Wählen Sie den **Rich-text**-Block aus. Geben Sie ihm unter **Design** den **Namen** `Intro text` und eine der
   CSS-Klassen des Themes von der Tafel. Beobachten Sie, wie sich der Block in der Mitte ändert, wenn die Klasse die
   Editoransicht beeinflusst.
6. Geben Sie dem **Button / Link**-Block den **Namen** `Main call to action`.
7. Sehen Sie sich den Tab **Design** jedes Blocks an: Welche Blöcke bieten mehr als einen Ansichtstyp?
8. Wählen Sie **Entwurf speichern und schließen**.

## Erwartetes Ergebnis

- Der Entwurf hat eine Schaltfläche mit Text, Adresse und Ziel und ein Bild mit Alternativtext.
- Zwei Blöcke haben klare Namen; der Rich-text-Block hat eine Theme-Klasse.
- Die Teilnehmenden können sagen, dass einfache Blöcke einen Ansichtstyp haben.
- Der Trainer prüft den Entwurf einer Person und seine Tabs **Design** am Beamer.

## Quiz

1. Welche drei Einstellungen braucht eine Schaltfläche mindestens?
2. Wozu dient der **Name** eines Blocks?
3. Was erlaubt das Feld **CSS-Klasse** den Redakteuren?
   A. Neues CSS schreiben B. Klassen anwenden, die das Theme definiert C. Das Template ändern D. Den Layout-Typ ändern
4. Richtig oder falsch: Jeder Block hat mehrere Ansichtstypen.
5. Warum sollte ein Bildblock einen Alternativtext haben?

<details>
<summary>Antworten</summary>

1. Den Text, die Linkadresse und das Ziel.
2. Eine Beschriftung für Redakteure, um ähnliche Blöcke auseinanderzuhalten ([Exponential Layouts](../../../../../eng-us/bc/6.0/LAYOUTS.md#block-parameters)).
3. **B.**
4. **Falsch.** Die meisten einfachen Blöcke haben einen; Listenblöcke haben mehrere.
5. Für Menschen, die das Bild nicht sehen können, und für den Fall, dass es nicht lädt.

</details>

## Weiterführende Informationen

- [Exponential Layouts: Blockparameter](../../../../../eng-us/bc/6.0/LAYOUTS.md#block-parameters): die gemeinsamen Felder.
- [Ihre Website gestalten](../../../../../eng-us/help/creators/website-creators/branding-your-site.md): woher das Aussehen einer Website und ihre Klassen kommen.

## Verwandte Seiten

- [Modul 3: Blöcke](README.md)
- [Exponential Layouts: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
