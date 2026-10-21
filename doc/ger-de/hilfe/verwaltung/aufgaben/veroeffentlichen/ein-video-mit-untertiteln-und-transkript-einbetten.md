---
title: Ein Video mit Untertiteln und Transkript einbetten
audience: government, editor
category: public-sector
order: 320
summary: Für Redakteure - ein Video so veröffentlichen, dass alle es nutzen können - Untertitel, ein Transkript auf der Seite, eine datenschutzbewusste Wahl zwischen dem Hosten der Datei und dem Einbetten des Players einer Plattform - mit der mitgelieferten Klasse Video oder einem Link.
keywords: Video, Untertitel, Transkript, Audiodeskription, Klasse Video, ezmedia, einbetten, YouTube, Datenschutz, Drittanbieter, Barrierefreiheit, WCAG 1.2, video, captions, subtitles, transcript, audio description, Video class, embed, privacy, third party, accessibility
type: help
---
# Ein Video mit Untertiteln und Transkript einbetten

Diese Seite ist für Redakteure, die Videos veröffentlichen: eine aufgezeichnete Sitzung, ein Erklärvideo, eine
Botschaft der Leitung. Videos brauchen Untertitel und ein Transkript, um barrierefrei zu sein
([WCAG 2.2](https://www.w3.org/TR/WCAG22/), Richtlinie 1.2). Das Standardpaket liefert eine Klasse **Video** (`video`,
mit einem Mediendateifeld `ezmedia`) in der Gruppe Media mit
([sevenx_classes](../../../seitenpakete/sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md)); viele Sites betten stattdessen eine
Videoplattform ein.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Die Art der Veröffentlichung wählen](#die-art-der-veröffentlichung-wählen)
3. [Schritte](#schritte)
4. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Eine Untertiteldatei (zum Beispiel WebVTT oder SRT) für das gesprochene Wort, von einem Menschen geprüft.
- Ein Transkript als Text: das Gesprochene, dazu wichtige Geräusche und das Gezeigte, wo es darauf ankommt.
- Wenn Informationen nur visuell sind, eine Audiodeskription oder eine Beschreibung im Text.

## Die Art der Veröffentlichung wählen

| Weg | Für | Vorsicht |
|---|---|---|
| Die Datei in ein Video-Objekt hochladen | kurze Videos, kein Drittanbieter | große Dateien und Bandbreite; der Player Ihres Templates muss Untertitel unterstützen |
| Den Player einer Plattform einbetten | lange Videos, Streaming | die Plattform erhält Besucherdaten, sobald der Player lädt; eine Einwilligung kann nötig sein ([Datenschutzstandards](../../../../standards/datenschutz.md)); eine Content Security Policy muss es erlauben |
| Ein Link zur Plattform | am einfachsten, kein Tracking bis zum Klick | die Besucher verlassen die Seite |

## Schritte

1. Legen Sie die Seite an, die das Video enthält (einen Artikel oder die Seite der Veranstaltung oder Sitzung).
2. Schreiben Sie eine Einleitung: was das Video ist, wie lang es ist, dass Untertitel und ein Transkript verfügbar sind.
3. Fügen Sie das Video hinzu:
   - **Upload**: Legen Sie ein Objekt **Video** an (in der Medienbibliothek oder neben der Seite), laden Sie die Datei
     hoch und verknüpfen Sie es mit der Seite oder betten Sie es ein;
   - **Plattform**: Fügen Sie die Einbettung der Plattform über den Weg ein, den Ihre Site erlaubt (ein Custom Tag oder
     ein Block), oder einen Link.
4. Fügen Sie das Transkript unter dem Video ein, unter einer Überschrift „Transkript“, oder als verknüpfte Seite, wenn
   es lang ist.
5. **Vorschau**, dann **Zur Veröffentlichung schicken**.

## Das Ergebnis prüfen

- [ ] Die Untertitel lassen sich im Player einschalten.
- [ ] Das Transkript steht als Text auf der Seite oder ist einen Klick entfernt.
- [ ] Der Player funktioniert mit der Tastatur.
- [ ] Abgemeldet lädt die Seite ohne Fehler; bei einer eingebetteten Plattform prüfen Sie, was lädt, bevor der
  Besucher auf Wiedergabe drückt.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Automatische Untertitel ungeprüft veröffentlicht | korrigieren Sie zuerst Namen und Fachbegriffe |
| Ein Video als einzige Quelle wichtiger Informationen | der Text sagt es auch |
| Automatische Wiedergabe mit Ton | nicht verwenden; sie stört Nutzer von Screenreadern |
| Rohen Einbettungscode in den Rich Text einfügen | der Editor lehnt ihn womöglich ab; verwenden Sie den Weg, den Ihre Site vorsieht |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Barrierefreie Informationen zu Veranstaltungen](../../aufbauen/veranstaltungen/barrierefreie-informationen-zu-veranstaltungen.md)
- [SVG und Bildformate](../../../ersteller/website-ersteller/svg-und-bildformate.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
