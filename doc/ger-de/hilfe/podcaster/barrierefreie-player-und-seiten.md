---
title: Barrierefreie Player und Seiten
audience: podcaster
category: podcasting-streaming
order: 34
summary: Wie Sie Episodenseiten und Player für alle nutzbar machen - Bedienelemente an und Auto-Wiedergabe aus im Medienfeld, was Ihnen das Template des HTML5-Players gibt, eingebettete Player, Überschriften, Linktexte mit Format und Größe, Bilder und Transkripte - mit einer Checkliste für jede Episode.
keywords: Barrierefreiheit, barrierefreier Player, Bedienelemente, Auto-Wiedergabe, Tastatur, Screenreader, WCAG, 1.4.2, Audio-Steuerelement, Linktext, Dateigröße, Überschriften, Alternativtext, eingebetteter Player, accessibility, accessible player, controls, autoplay, keyboard, screen reader, audio control, link text, file size, headings, alternative text, embedded player
type: help
---
# Barrierefreie Player und Seiten

Diese Seite ist für Produzenten und Redakteure, die möchten, dass jeder Hörer und Zuschauer eine Episodenseite nutzen
kann: Menschen mit Tastatur oder Screenreader, Menschen, die nicht hören oder sehen können, Menschen mit langsamen
Verbindungen. Vieles davon liegt als Redakteur in Ihrer Hand; manches hängt vom Design ab.

## Inhalt

1. [Der Player](#der-player)
2. [Eingebettete Player](#eingebettete-player)
3. [Die Seite um den Player](#die-seite-um-den-player)
4. [Downloads](#downloads)
5. [Checkliste pro Episode](#checkliste-pro-episode)

## Der Player

Mit dem Playertyp **Html5 Audio** oder **Html5 Video** wird das Medienfeld zum eigenen `<audio>`- oder
`<video>`-Element des Browsers (`design/standard/templates/content/datatype/view/ezmedia.tpl`). Browser machen ihre
eigenen Bedienelemente mit der Tastatur nutzbar und kündigen sie Screenreadern an. Ihr Teil sind die Optionen jedes
Objekts:

| Option | Einstellen auf | Warum |
|---|---|---|
| **Controller** | an | ohne `controls` gibt es keine Möglichkeit, abzuspielen, anzuhalten oder die Lautstärke zu ändern |
| **Auto-Wiedergabe** | aus | Ton, der von selbst startet, stört Screenreader; das WCAG-2.2-Erfolgskriterium 1.4.2 verlangt eine Möglichkeit, ihn zu stoppen |
| **Schleife** | aus | eine Episode, die nie endet, verwirrt |

Das Template schreibt `preload="none"`: Nichts wird heruntergeladen, bevor der Besucher auf Abspielen drückt, was
Menschen mit langsamen oder volumenbegrenzten Verbindungen hilft.

Die älteren Playertypen (Flash, QuickTime, RealPlayer, Silverlight, Windows Media Player) erzeugen Plugin-Markup, das
aktuelle Browser und assistive Technologien nicht nutzen können; wählen Sie sie nie für neue Inhalte.

## Eingebettete Player

Ein Player eines Video- oder Streamingdienstes bringt seine eigene Barrierefreiheit mit. Bevor Sie sich darauf
verlassen:

- Prüfen Sie, ob er sich mit der Tastatur bedienen lässt und ob der Fokus ihn wieder verlassen kann;
- prüfen Sie, ob sich Untertitel einschalten lassen;
- geben Sie dem Block eine **Caption**, die sagt, was das Video ist; der Block External video verwendet sie als Titel
  des Players.

Auf Installationen mit Exponential Layouts zeigt der Block External video zuerst eine Schaltfläche **Load video**; die
Schaltfläche ist eine echte Schaltfläche, sodass Tastaturnutzer sie erreichen
([Ein Video oder einen Stream einbetten](ein-video-oder-einen-stream-einbetten.md)).

## Die Seite um den Player

- **Ein klarer Titel** mit Episodennummer und Thema.
- **Überschriften** aus den Absatzformaten des Editors für die Teile der Shownotes, damit Screenreader-Nutzer springen
  können.
- **Linktexte**, die für sich allein Sinn ergeben: "Das Transkript lesen", nicht "hier klicken".
- **Bilder** mit einem Alternativtext, der sagt, was sie zeigen ([Cover und Episodenbilder](cover-und-episodenbilder.md)).
- **Das Transkript** auf der Seite oder einen Klick entfernt ([Transkripte und Untertitel](transkripte-und-untertitel.md)).
- **Sprache**: Eine Episode in einer anderen Sprache als die Website ist eine Übersetzung oder ein Objekt in dieser
  Sprache, damit die Seite die richtige Sprache trägt ([Mehrsprachige Sendungen](mehrsprachige-sendungen.md)).

## Downloads

Nennen Sie bei einem Download-Link Format und Größe, bevor jemand klickt. Die Ansicht des Dateifelds zeigt Name und
Größe, und das Beispiel-Template unter
[Dateien und Downloads](../ersteller/website-ersteller/dateien-und-downloads.md#download-links-in-templates) ergänzt den
MIME-Typ; bei Audio in einem Medienfeld schreiben Sie beides selbst in den Linktext, zum Beispiel "Die Episode
herunterladen (MP3, 48 MB)".

## Checkliste pro Episode

- [ ] **Controller** an, **Auto-Wiedergabe** und **Schleife** aus.
- [ ] Der Player funktioniert allein mit der Tastatur: mit Tab hin, abspielen, anhalten, springen.
- [ ] Ein Transkript oder Untertitel sind vorhanden.
- [ ] Überschriften gliedern die Notizen; Links haben aussagekräftigen Text.
- [ ] Jedes Bild hat einen Alternativtext.
- [ ] Download-Links nennen Format und Größe.

Für eine gründlichere Prüfung der Website siehe [Barrierefreiheit testen](../ersteller/website-ersteller/barrierefreiheit-testen.md)
und die [Checkliste zur Barrierefreiheit für Redakteure](../kunden/alltag/checkliste-barrierefreiheit-fuer-redakteure.md).

## Verwandte Seiten

- [Transkripte und Untertitel](transkripte-und-untertitel.md)
- [Ihre Berichterstattung barrierefrei machen](../presse/barrierefreie-berichterstattung.md)
- [Barrierefreiheit für Website-Ersteller](../ersteller/website-ersteller/barrierefreiheit-fuer-website-ersteller.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
