---
title: Medien im formatierten Text
audience: podcaster
category: podcasting-streaming
order: 26
summary: Wie Sie eine Episode, ein Video oder einen Audioclip mit Objekt einbetten des Online-Editors in den Text eines Artikels setzen, was bestimmt, wie es aussieht, warum formatierter Text keine Player-iframes aufnehmen kann und welche alten Videooptionen Sie meiden sollten.
keywords: formatierter Text, Online-Editor, ezoe, TinyMCE, Objekt einbetten, Einbettung, Audio im Artikel, Video im Artikel, iframe, Custom-Tag, Video-Tag, Flash, ezxmltext, rich text, online editor, embed object, embed, audio in article, video in article, custom tag, video tag
type: help
---
# Medien im formatierten Text

Diese Seite ist für Redakteure, die einen Player in einem Artikel möchten: einen Clip in einer Nachricht, die Episode in
einem Blogbeitrag, ein Video in einer Anleitung. Der Editor für formatierten Text kann ein Objekt der Website
einbetten, und das ist der Weg dafür.

## Inhalt

1. [Ein Objekt einbetten](#ein-objekt-einbetten)
2. [Wie die Einbettung aussieht](#wie-die-einbettung-aussieht)
3. [Warum es keine Schaltfläche für iframes gibt](#warum-es-keine-schaltfläche-für-iframes-gibt)
4. [Optionen, die Sie meiden sollten](#optionen-die-sie-meiden-sollten)
5. [Alternativen](#alternativen)

## Ein Objekt einbetten

Die Werkzeugleiste des Online-Editors hat eine Gruppe zum Einbetten mit den Schaltflächen **image** und **object**
(`extension/ezoe/settings/ezoe.ini [EditorLayout] Buttons[embed]=image,object`); die Hilfe nennt sie **Objekt
einbetten** ([Inhalte erstellen und bearbeiten](../inhalte-bearbeiten.md#rich-text)).

1. Laden Sie zuerst das Audio- oder Videoobjekt hoch oder legen Sie es an (eine Episode, ein Objekt *Video*, ein Objekt
   *File*).
2. Bearbeiten Sie den Artikel und setzen Sie den Cursor dorthin, wo der Player hingehört.
3. Drücken Sie **Objekt einbetten**, suchen Sie das Objekt, wählen Sie es und fügen Sie es ein.
4. Veröffentlichen Sie den Artikel.

Die Einbettung ist ein Verweis, keine Kopie. Wird das Audio der Episode später ersetzt, zeigt der Artikel die neue
Datei.

## Wie die Einbettung aussieht

Was im Artikel erscheint, ist die **Einbettungsansicht** des Objekts, ein Template des Designs der Website. Ob diese
Ansicht einen Player, einen Link oder einen Anrisstext zeigt, hängt vom Design ab:

- Auf Installationen mit dem Media-Theme haben die Klassen *Audio* und *NG Video* eigene Einbettungs-Templates
  (`extension/sevenx_themes_media/design/media/override/templates/node/view/embed/embed_ng_audio.tpl` und die
  passenden Videodateien).
- Bei anderen Klassen zeigt das Design vielleicht nur den Namen und einen Link. Prüfen Sie eine Einbettung in der
  Vorschau, bevor Sie sich darauf verlassen, und bitten Sie Ihren Designer bei Bedarf um eine Einbettungsansicht mit
  Player ([Template-Overrides](../ersteller/website-ersteller/template-overrides.md)).

## Warum es keine Schaltfläche für iframes gibt

Formatierter Text wird als XML gespeichert (`ezxmltext`), nicht als freies HTML. Der Editor wandelt das, was Sie
schreiben, in dieses XML um, und darin gibt es kein Element für ein `<iframe>`: Eingefügter Einbettungscode eines
Videodienstes bleibt nicht als Player erhalten. Das TinyMCE-Plugin `media` wird in
`extension/ezoe/design/standard/javascript/tinymce8/plugins/` mitgeliefert, ist aber in `ezoe.ini [EditorSettings]
Plugins[]` nicht aktiviert, und es zu aktivieren würde nichts daran ändern, was das XML speichern kann.

Das ist Absicht: So bleiben Skripte und Rahmen von Drittanbietern aus Texten heraus, die viele Redakteure ändern können.

## Optionen, die Sie meiden sollten

| Option | Warum Sie sie meiden sollten |
|---|---|
| Das Custom-Tag `video` des Designs ezwebin | sein Template schreibt ein `<object>`/`<embed>` mit `application/x-shockwave-flash` (`extension/ezwebin/design/ezwebin/templates/content/datatype/view/ezxmltags/video.tpl` auf Installationen mit ezwebin): Flash, das aktuelle Browser nicht ausführen |
| Medienfelder mit dem Playertyp Flash, QuickTime, RealPlayer, Silverlight oder Windows Media Player | Plugin-Markup für Player, die es nicht mehr gibt; eine durchgesetzte Content Security Policy blockiert `<object>` (`object-src 'none'`) |

## Alternativen

| Bedarf | Verwenden Sie |
|---|---|
| Einen Player von YouTube, Vimeo, Dailymotion oder einem Streamingdienst auf einer Seite | den Block **External video** des Layout-Editors ([Ein Video oder einen Stream einbetten](ein-video-oder-einen-stream-einbetten.md)) |
| Einen Player auf der Episodenseite selbst | das Medienfeld der Episode ([Audio hochladen](audio-hochladen.md)) |
| Eine Liste der neuesten Episoden neben dem Text | einen Listenblock des Layout-Editors oder die verwandten Inhalte des Designs |
| Einen Link zur Episode | einen normalen Link auf das Episodenobjekt, der ihm folgt, wenn es verschoben wird |

## Verwandte Seiten

- [Ein Video oder einen Stream einbetten](ein-video-oder-einen-stream-einbetten.md)
- [Verknüpfungen und Wiederverwendung](../ersteller/website-ersteller/relationen-und-wiederverwendung.md)
- [Inhalte erstellen und bearbeiten](../inhalte-bearbeiten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
