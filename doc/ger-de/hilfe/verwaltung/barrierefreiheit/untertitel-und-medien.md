---
title: Untertitel und Medien
audience: government, editor, designer, developer
category: public-sector
order: 240
summary: Für öffentliche Stellen, die Video und Audio veröffentlichen - was der Medien-Datentyp von Exponential ausgibt (HTML5-Video und -Audio neben älteren Plugin-Typen, mit Optionen für Auto-Wiedergabe, Schleife und Bedienelemente, aber ohne Untertitelspur), und wie Sie trotzdem Untertitel, Transkripte und Audiodeskription bereitstellen.
keywords: Video, Audio, Medien, Untertitel, Transkript, Audiodeskription, HTML5-Video, Spur, Auto-Wiedergabe, Bedienelemente, eingebettete Player, öffentliche Sitzungen, video, audio, media, captions, subtitles, transcript, audio description, ezmedia, HTML5 video, track, autoplay, controls, WCAG 1.2.1, 1.2.2, 1.2.5, 1.4.2, embedded players, public meetings
type: help
---
# Untertitel und Medien

Diese Seite richtet sich an die Redaktion, die Aufzeichnungen veröffentlicht (Ratssitzungen, Erklärvideos,
Podcasts), und an Design und Entwicklung, die sie ausgeben. WCAG 2.2 Konformitätsstufe AA verlangt Untertitel für
aufgezeichnete und Live-Videos mit Ton, ein Transkript oder eine Audiodeskription und eine Möglichkeit, Audio
anzuhalten, das von selbst abgespielt wird (1.2.1 bis 1.2.5, 1.4.2; <https://www.w3.org/TR/WCAG22/>).

## Inhalt

1. [Was das Medienfeld ausgibt](#was-das-medienfeld-ausgibt)
2. [Untertitel bereitstellen](#untertitel-bereitstellen)
3. [Transkripte und Audiodeskription](#transkripte-und-audiodeskription)
4. [Eingebettete Player anderer Dienste](#eingebettete-player-anderer-dienste)
5. [Eine Checkliste](#eine-checkliste)
6. [Verwandte Seiten](#verwandte-seiten)

## Was das Medienfeld ausgibt

Der Medien-Datentyp (`ezmedia`) wird von `design/standard/templates/content/datatype/view/ezmedia.tpl` ausgegeben. Er
kennt mehrere Player-Typen; die aktuellen sind **HTML5-Video** und **HTML5-Audio**, die die Elemente `<video>` und
`<audio>` mit der Datei aus `content/download` schreiben. Die Optionen des Felds werden zu Attributen:

| Option des Medienfelds | HTML-Attribut |
|---|---|
| Auto-Wiedergabe | `autoplay` |
| Schleife | `loop` |
| Controller | `controls` |

Die anderen Typen (Flash, QuickTime, Windows Media Player, Real Player, Silverlight) schreiben Plugin-Markup, das
aktuelle Browser nicht mehr unterstützen; verwenden Sie sie nicht für neue Inhalte.

**Das Standard-Template schreibt kein Element `<track>`**, sodass sich im Auslieferungszustand über das Medienfeld keine
Untertiteldatei anhängen lässt.

## Untertitel bereitstellen

Wählen Sie eine dieser Möglichkeiten und schreiben Sie sie in Ihren Redaktionsleitfaden:

1. **Offene Untertitel**: Brennen Sie die Untertitel vor dem Hochladen in das Video ein. Funktioniert überall, lässt sich
   nicht abschalten.
2. **Eine Untertitelspur in Ihrem Design**: Eine Entwicklerin oder ein Entwickler überschreibt das Medien-Template für
   Ihr Design und fügt ein `<track
   kind="captions">` hinzu, das auf eine WebVTT-Datei zeigt, zum Beispiel aus einem zweiten Dateifeld in einer eigenen
   Videoklasse. Das ist Template-Arbeit in Ihrem Design, keine eingebaute Funktion.
3. **Eine externe Videoplattform**, deren Player Untertitel unterstützt, eingebettet in die Seite (siehe unten).

Immer: **Controller** einschalten und **Auto-Wiedergabe** aus (WCAG 1.4.2 und schlichte Höflichkeit).

## Transkripte und Audiodeskription

- Veröffentlichen Sie ein **Transkript** als Text auf derselben Seite, unter dem Player, oder als verlinkte Seite. Für
  reine Audioinhalte (Podcasts) ist das Transkript die barrierefreie Alternative.
- Für Videos, deren Bild Informationen enthält, die nicht ausgesprochen werden (eine Folie mit Zahlen), stellen Sie eine
  **Audiodeskription** oder eine Textbeschreibung des visuellen Inhalts bereit.
- Bei Aufzeichnungen von Sitzungen ist das Protokoll kein Transkript; veröffentlichen Sie beides, wenn beides existiert.

## Eingebettete Player anderer Dienste

Das Einbetten des Players einer Videoplattform bringt dessen Barrierefreiheit (und seine Folgen für Datenschutz und
Content Security Policy) auf Ihre Seite. Prüfen Sie, dass der Player mit der Tastatur bedienbar ist, dass sich
Untertitel einschalten lassen und dass der Fokus ihn wieder verlassen kann. Nennen Sie den Dritten in Ihrer
Datenschutzerklärung und erlauben Sie seinen Host in Ihrer Content Security Policy, wenn Sie eine durchsetzen
([Content Security Policy](../../../funktionen/6.0/content-security-policy.md)).

## Eine Checkliste

- [ ] Jedes Video mit Sprache hat Untertitel.
- [ ] Jedes Audio und Video hat ein Transkript auf der Seite oder einen Klick entfernt.
- [ ] Keine Medien starten von selbst; Bedienelemente werden angezeigt.
- [ ] Rein visuelle Informationen werden beschrieben.
- [ ] Der Player funktioniert mit der Tastatur, und der Fokus kann ihn verlassen.

## Verwandte Seiten

- [Barrierefreie Inhalte für die Redaktion](barrierefreie-inhalte-fuer-die-redaktion.md)
- [Bilder, Medien und Uploads](../../medien-und-uploads.md)
- [Template-Overrides](../../ersteller/website-ersteller/template-overrides.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
