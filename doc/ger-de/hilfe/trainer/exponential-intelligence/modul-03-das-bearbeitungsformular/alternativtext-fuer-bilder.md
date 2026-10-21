---
title: "Lektion 3.3: Alternativtext für Bilder"
audience: trainer, editor
category: training
order: 33
summary: Die Teilnehmenden lassen mit Write alt text einen Alternativtext vorschlagen, wählen Decorative (empty alt text), wo es richtig ist, prüfen den Vorschlag anhand der Regeln für Barrierefreiheit und finden mit dem Bericht Missing alt text Bilder ohne Alternativtext. 45 Minuten.
keywords: exponential intelligence, alternativtext, alt-text, write alt text, dekorativ, bildmodell, missing alt text, bericht, queue suggestions as drafts, barrierefreiheit, bilder, alt text, alternative text, decorative, vision model, report, accessibility, images
type: help
---
# Lektion 3.3: Alternativtext für Bilder

Gute Alternativtexte zu schreiben ist mühsame Arbeit, und viele Websites haben Hunderte Bilder ohne einen. Der Assistent
kann ein Bild ansehen und einen Text vorschlagen; der Redakteur prüft und entscheidet. Diese Lektion behandelt die
Schaltfläche im Bearbeitungsformular und den Bericht, der Bilder ohne Alternativtext findet.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Intelligence](../README.md) |
| Modul | [Modul 3: Das Bearbeitungsformular](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Redakteure |
| Voraussetzungen | [Lektion 3.2](felder-ausfuellen-vorlagen-und-verbessern.md); Redakteure 201, Lektion 8.2 (barrierefreie Inhalte) hilft |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- mit **Write alt text** einen Alternativtext vorschlagen lassen und ihn rückgängig machen;
- **Decorative (empty alt text)** für ein Bild wählen, das keine Information trägt, und wissen, wann es nicht angeboten
  wird;
- einen Vorschlag anhand der Regeln für Barrierefreiheit prüfen;
- **Missing alt text** öffnen, die Bilder eines Teilbaums anzeigen und wissen, was **Queue suggestions as drafts** tut.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: die Seite zum Alternativtext unter **EI: Help** (`extension/expai/doc/alt-text.md`).

1. **Die Schaltflächen** (3 Minuten). Jedes Bildfeld bekommt über seinem **Alternativer Bildtext** zwei
   Schaltflächen: **Write alt text** (schickt das gespeicherte Bild, den Seitentitel, den Text der anderen Felder und
   die Sprache an ein Modell, das Bilder ansehen kann) und **Decorative (empty alt text)** (leert das Feld; keine
   Anfrage). **Undo** stellt den Text wieder her. Das Bild muss zuerst im Entwurf gespeichert sein.
2. **Die Regeln** (4 Minuten). Das Modell wird gebeten um das, was für die Seite wichtig ist, ein knapper Satz oder
   Ausdruck, kein „Bild von“, sichtbaren Text, wenn er die Botschaft trägt, keine wiederholte Bildunterschrift, keine
   geratenen Namen, kein Urteil über Menschen nach ihrem Aussehen, in der Sprache des Inhalts. Das Modell entscheidet
   nie, dass ein Bild dekorativ ist; das tut der Redakteur.
3. **Erforderlicher Alternativtext** (2 Minuten). Wo ein Feld einen Alternativtext verlangt, ist **Decorative**
   deaktiviert.
4. **Datenschutz** (2 Minuten). Das Bild geht in der Anfrage an den Anbieter; die Datenrichtlinie der Website gilt dafür
   und kann für Bilder strenger sein. Verwenden Sie das nie bei Fotos von Menschen ohne deren Einwilligung.
5. **Der Bericht** (3 Minuten). **Missing alt text** (im linken Menü des Reiters Design): **Node ID of the subtree** und
   **Show** listen die Bilder auf (**Content**, **Class**, **Field**, **Language**, **Size**); **Queue suggestions as
   drafts** legt, mit einem optionalen **Budget of this job group (optional, USD)**, über die Auftragswarteschlange
   Entwürfe mit Vorschlägen an. Jeder Entwurf braucht weiterhin die Prüfung durch einen Redakteur.

### Häufige Fragen

**Warum ist Write alt text deaktiviert?** Ihnen steht kein Modell zur Verfügung, das Bilder ansehen kann, die
Datenrichtlinie hält das Bild von jedem solchen Modell fern, oder das Bild ist noch nicht im Entwurf gespeichert. Die
Schaltfläche sagt, warum.

**Ist der Vorschlag immer richtig?** Nein. Er kann nicht so gut wie Sie wissen, warum das Bild auf der Seite ist. Lesen
Sie ihn.

**Ändert der Bericht veröffentlichte Bilder?** Nein. Er stellt Entwürfe in die Warteschlange; nichts wird
veröffentlicht.

### Stolperfallen

- **Fotos von Menschen.** Schicken Sie sie nicht ohne Einwilligung ab; beschreiben Sie sie selbst.
- **Standardmäßig dekorativ.** Die meisten Bilder auf einer Inhaltsseite tragen Informationen.
- **Eine ganze Website in die Warteschlange stellen.** Das legt viele Entwürfe an und kostet; beginnen Sie mit einem
  Ordner und einem Budget.

## Praktische Übung

Verwenden Sie die Schulungsinstallation und die Beispielbilder aus Ihrem Kursordner (keine Fotos von Menschen).

1. Öffnen Sie eines Ihrer Bilder mit **Bearbeiten**. Suchen Sie **Alternativer Bildtext** und die beiden
   Schaltflächen darüber.
2. Klicken Sie auf **Write alt text**. Lesen Sie den Vorschlag und den Hinweis (Modell, Größe des geschickten Bildes,
   geschätzte Kosten).
3. Vergleichen Sie den Vorschlag mit den Regeln, die der Trainer am Beamer zeigt. Verbessern Sie ihn bei Bedarf von
   Hand.
4. Klicken Sie auf **Undo** und noch einmal auf **Write alt text**; behalten Sie die Fassung, die Ihnen lieber ist.
   Speichern Sie den Entwurf und veröffentlichen Sie ihn.
5. Klicken Sie bei einem rein dekorativen Bild (einem Muster oder einer Trennlinie, die der Trainer bereitgestellt hat)
   auf **Decorative (empty alt text)** und veröffentlichen Sie.
6. Öffnen Sie **Missing alt text** im linken Menü des Reiters Design (wenn Ihre Rolle es hat). Geben Sie die
   Knotennummer Ihres Kursordners in **Node ID of the subtree** ein und klicken Sie auf **Show**.
7. Lesen Sie die Liste. Klicken Sie nicht auf **Queue suggestions as drafts**, außer der Trainer bittet darum (es legt
   Entwürfe an und kostet).

## Erwartetes Ergebnis

- Ein Bild hat einen geprüften Alternativtext, ein dekoratives Bild einen leeren.
- Der Bericht hat die Bilder des Ordners der Person aufgelistet, denen noch ein Alternativtext fehlt (möglicherweise
  keine).
- Der Trainer prüft das, indem er zwei Alternativtexte vorliest.

## Quiz

1. Was schickt **Write alt text** an das Modell?
2. Wer entscheidet, dass ein Bild dekorativ ist?
   A. Das Modell B. Der Redakteur C. Die Website D. Niemand
3. Warum könnte **Decorative (empty alt text)** deaktiviert sein?
4. Nennen Sie zwei Regeln für einen guten Alternativtext.
5. Was legt **Queue suggestions as drafts** an?

<details>
<summary>Antworten</summary>

1. Das gespeicherte Bild, den Seitentitel, den Text der anderen Felder und die Sprache.
2. **B.**
3. Das Feld verlangt einen Alternativtext.
4. Zwei beliebige aus: was für die Seite wichtig ist; ein knapper Satz oder Ausdruck; kein „Bild von“; der sichtbare
   Text, wenn er die Botschaft trägt; keine wiederholte Bildunterschrift; keine geratenen Namen; die Sprache des
   Inhalts.
5. Entwürfe mit vorgeschlagenen Alternativtexten, über die Auftragswarteschlange, zur Prüfung durch Redakteure.

</details>

## Weiterführende Informationen

- Die Seite zum Alternativtext unter **EI: Help** (`extension/expai/doc/alt-text.md`).
- [Standards für Barrierefreiheit](../../../../standards/barrierefreiheit.md): was von den Redakteuren abhängt.
- [Bilder, Medien und Uploads](../../../medien-und-uploads.md#bewährte-vorgehensweise)

## Verwandte Seiten

- [Modul 3: Das Bearbeitungsformular](README.md)
- [Exponential Intelligence: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
