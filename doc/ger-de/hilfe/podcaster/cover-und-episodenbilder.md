---
title: Cover und Episodenbilder
audience: podcaster
category: podcasting-streaming
order: 16
summary: Wohin das Cover der Sendung und die Episodenbilder in Exponential gehören - die Adresse des Titelbilds im Feed, geschrieben als itunes:image, das RSS-2.0-Bild anderer Exporte, Bildfelder von Episodenklassen und ihre Varianten auf der Website - und was Podcast-Apps erreicht und was nicht.
keywords: Cover, Titelbild, Adresse des Titelbilds, itunes:image, Episodenbild, Bildfeld, Bildvarianten, Alternativtext, Poster, Podcast-Cover, Bild der Sendung, artwork, cover art, Artwork address, episode image, image field, image variations, alternative text, poster, podcast cover, show image
type: help
---
# Cover und Episodenbilder

Diese Seite ist für Produzenten und Designer, die das Cover der Sendung und die Bilder der Episoden vorbereiten.
Exponential behandelt hier zwei verschiedene Dinge: die **Adresse des Titelbilds**, die Podcast-Apps aus dem Feed
lesen, und **Bilder auf der Website**, die Apps nie sehen.

## Inhalt

1. [Das Cover der Sendung im Feed](#das-cover-der-sendung-im-feed)
2. [Die Cover-Datei bereitstellen](#die-cover-datei-bereitstellen)
3. [Episodenbilder](#episodenbilder)
4. [Bilder auf der Seite der Sendung](#bilder-auf-der-seite-der-sendung)
5. [Alternativtext](#alternativtext)

## Das Cover der Sendung im Feed

Der Podcast-Export hat in seinem Abschnitt **Apple Podcasts** ein Feld **Adresse des Titelbilds**. Es ist eine
Adresse, kein Upload. `generateITunes()` schreibt sie als `<itunes:image href="..."/>` in den Kanal; ein
Code-Kommentar hält fest, dass dieses Element die Adresse im Attribut `href` tragen muss und dass es als Text zu
schreiben "is the single most common reason a feed is rejected for having no artwork", also der häufigste Grund ist,
warum ein Feed wegen fehlenden Covers abgelehnt wird (`kernel/classes/ezrssexport.php`). Die Adresse wird beim
Speichern des Exports geprüft (`eZRSSExportOPMLItem::safeURL()` in `kernel/classes/ezrssexportopmlitem.php`):
Leerraum wird entfernt, eine Adresse mit einem anderen Schema als `http` oder `https` wird verworfen, und sie wird auf
255 Zeichen begrenzt. Eine relative Adresse besteht die Prüfung, aber Apps holen das Bild von anderswo ab; geben Sie
deshalb immer eine vollständige `https://`-Adresse ein.

Podcast-Verzeichnisse legen ihre eigenen Regeln für Größe, Format und Farbraum des Covers fest. Exponential prüft das
Bild hinter der Adresse nicht; prüfen Sie die aktuellen Anforderungen jedes Verzeichnisses, bei dem Sie einreichen.

## Die Cover-Datei bereitstellen

Der einfachste Ort für das Cover ist die Website selbst:

1. Laden Sie das Cover-Bild als Objekt *Bild* in die Medienbibliothek hoch ([Bilder, Medien und Uploads](../medien-und-uploads.md)).
2. Öffnen Sie das Bildobjekt auf der Website und kopieren Sie die Adresse der Originalbilddatei.
3. Fügen Sie diese Adresse in das Feld **Adresse des Titelbilds** des Exports ein.

> **Hinweis**
> Exponential ändert die Adresse eines Bildes, wenn das Bild ersetzt wird (die Version ist Teil der URL, wie die
> Seite [CDN](../hosting/leistung/cdn.md) erklärt). Wenn Sie das Cover ersetzen, kopieren Sie die neue Adresse
> erneut in den Export; sonst verweist der Feed weiter auf die alte Datei.

Die anderen Feed-Formate (RSS 2.0) haben im Exportformular ein eigenes **Bild**, ausgewählt aus den Bildern der
Website; das Podcast-Format verwendet es nicht.

## Episodenbilder

Der Podcast-Feed schreibt **kein Bild pro Episode**: Apps zeigen für jede Episode das Cover der Sendung
([Was nicht eingebaut ist](was-nicht-eingebaut-ist.md)). Episodenbilder sind für die Website:

| Klasse | Bildfeld |
|---|---|
| Audio (`ng_audio`, Installationen mit Media Site) | **Image** |
| NG Video (Installationen mit Media Site) | **Poster** |
| Video | keines |
| Ihre eigene Episodenklasse | wie Sie es festlegen ([Eine Episodenklasse aufbauen](eine-episodenklasse-aufbauen.md)) |

Auf der Website werden Bildfelder in Größen angezeigt, die **Varianten** (Aliase) heißen und vom Bildsystem aus dem
Original erzeugt werden (`settings/image.ini`); das Design entscheidet, welche Größe jede Ansicht verwendet
([Bilder und Bildvarianten](../ersteller/website-ersteller/bilder-und-bildvarianten.md)).

## Bilder auf der Seite der Sendung

Der Ordner der Sendung kann ein Bild für die Seite der Sendung tragen, und soziale Netzwerke lesen die Metadaten der
Seite, wenn jemand einen Link teilt ([Metadaten und Teilen in sozialen Netzwerken](../ersteller/website-ersteller/metadaten-und-teilen-in-sozialen-medien.md)).
Das ist vom Cover im Feed getrennt; halten Sie die beiden Bilder einheitlich.

## Alternativtext

Jedes Bild auf der Website braucht einen Alternativtext, der sagt, was es zeigt. Nennen Sie beim Cover die Sendung und
was auf dem Bild ist: "Cover von The Example Show: zwei Mikrofone vor blauem Hintergrund." Wo die Website es
verlangt, fragen die Upload-Dialoge danach ([Bilder, Medien und Uploads](../medien-und-uploads.md#bewährte-vorgehensweise)).

## Verwandte Seiten

- [Referenz der Kanalfelder](referenz-der-kanalfelder.md)
- [SVG und Bildformate](../ersteller/website-ersteller/svg-und-bildformate.md)
- [Titel, Vorschaubilder und Beschreibungen](../presse/titel-vorschaubilder-und-beschreibungen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
