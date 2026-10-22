---
title: Eine Episodenklasse aufbauen
audience: podcaster
category: podcasting-streaming
order: 8
summary: Ein Vorschlag für eine Inhaltsklasse für Podcast- oder Videoepisoden, nur aus Datentypen gebaut, die Exponential mitliefert - Titel, Shownotes, Audio oder Video, Cover, Gäste, Veröffentlichungsdatum, Transkript, Lizenz - mit den Einstellungen, durch die sie mit Uploads, Zeitplanung und dem Podcast-Feed zusammenarbeitet.
keywords: Episodenklasse, Inhaltsklasse, Klasseneditor, Datentypen, ezmedia, ezxmltext, ezobjectrelationlist, ezdatetime, ezbinaryfile, ezauthor, ezkeyword, upload.ini, ClassSettings, maximale Dateigröße, Administrator, episode class, content class, class editor, datatypes, max file size, administrator
type: help
---
# Eine Episodenklasse aufbauen

Diese Seite ist für Administratoren und Website-Ersteller, die eine Episodenklasse für eine Sendung anlegen, und für
Produzenten, die ihnen sagen möchten, was die Sendung braucht. Klassen werden unter **Setup > Klassen** von einem
Benutzer mit den Richtlinien für Klassen bearbeitet
([Inhaltsklassen für Websites](../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md)). Jeder unten genannte
Datentyp liegt in `kernel/classes/datatypes/`, sofern nicht anders angegeben.

## Inhalt

1. [Ein Vorschlag für die Klasse](#ein-vorschlag-für-die-klasse)
2. [Entscheidungen, die später zählen](#entscheidungen-die-später-zählen)
3. [Uploads in die Klasse](#uploads-in-die-klasse)
4. [Die Anbindung an den Rest](#die-anbindung-an-den-rest)
5. [Ein Test vor der ersten Episode](#ein-test-vor-der-ersten-episode)

## Ein Vorschlag für die Klasse

Klassenname **Episode**, Bezeichner `episode`, Muster für den Objektnamen `<title>`, kein Container.

| Attribut | Bezeichner | Datentyp | Pflicht | Warum |
|---|---|---|---|---|
| Title | `title` | Textzeile (`ezstring`) | ja | der Titel der Episode, auch im Feed |
| Episode number | `episode_number` | Ganzzahl (`ezinteger`) | nein | für Listen und Sortierung auf der Website; der Feed hat kein Feld dafür |
| Short description | `teaser` | Textblock (`eztext`) | nein | ein oder zwei Sätze für Listen und die Beschreibung im Feed |
| Show notes | `show_notes` | Formatierter Text (`ezxmltext`) | nein | Links, Kapitel, Mitwirkende, Hinweis auf Sponsoren |
| Audio | `audio` | Medien (`ezmedia`), Playertyp **Html5 Audio** | ja | die Episodendatei und das Enclosure des Feeds |
| Artwork | `image` | Bild (`ezimage`) | nein | auf der Episodenseite angezeigt |
| Guests | `guests` | Objektbeziehungen (`ezobjectrelationlist`) | nein | Verknüpfungen zu Personen-Objekten |
| Hosts and credits | `credits` | Autoren (`ezauthor`) | nein | Namen und E-Mail-Adressen der Mitwirkenden |
| Release date | `release_date` | Datum und Uhrzeit (`ezdatetime`) | nein | für das Ereignis *Bis Datum warten* |
| Transcript | `transcript` | Formatierter Text (`ezxmltext`) | nein | der barrierefreie Text der Episode |
| Transcript file | `transcript_file` | Datei (`ezbinaryfile`) | nein | ein herunterladbares Transkript, falls Sie eines haben |
| Keywords | `keywords` | Schlüsselwörter (`ezkeyword`) | nein | Themen; oder eztags auf Installationen mit dieser Erweiterung |
| License | `license` | Auswahl (`ezselection`) | nein | die Lizenz, unter der Sie veröffentlichen ([Rechte und Lizenzen](rechte-und-lizenzen.md)) |

Verwenden Sie für eine Videosendung Medien mit dem Playertyp **Html5 Video** statt Html5 Audio.

## Entscheidungen, die später zählen

- **Die Höchstgröße des Medienfelds** wird am Klassenattribut in MB festgelegt
  (`ContentClass_ezmedia_max_filesize_*`, bis zu fünf Ziffern). Lassen Sie sie auf 0, um sich nur auf die Grenzen
  von PHP zu verlassen ([Upload-Grenzen und große Dateien](upload-grenzen-und-grosse-dateien.md)).
- **Übersetzbare Felder.** Jedes Attribut kann übersetzbar sein. Ein übersetzbares Medienfeld bedeutet, dass jede
  Sprache ihre eigene Audiodatei haben kann; machen Sie es nicht übersetzbar, wenn alle Sprachen eine Datei teilen
  ([Mehrsprachige Sendungen](mehrsprachige-sendungen.md)).
- **Durchsuchbar.** Shownotes und Transkript sollten durchsuchbar sein, damit die Suche der Website Episoden nach dem
  findet, was gesagt wurde. Medien- und Dateifelder werden nicht nach ihrem Inhalt durchsucht.
- **Bezeichner sind praktisch dauerhaft.** Feeds, Templates und die Upload-Zuordnung nennen sie; einen später
  umzubenennen bedeutet, all das zu ändern.

## Uploads in die Klasse

Wenn Episoden durch Hochladen von Dateien auf der Upload-Seite entstehen sollen, ordnet der Administrator den Typ in
einem Override von `settings/upload.ini` der Klasse zu:

```ini
[CreateSettings]
MimeClassMap[audio/mpeg]=episode

[episode_ClassSettings]
FileAttribute=audio
NameAttribute=title
NamePattern=<original_filename_base>
```

Die mitgelieferten Blöcke `[file_ClassSettings]` und `[video_ClassSettings]` folgen demselben Muster. Ohne die
Zuordnung wird ein hochgeladenes MP3 zu einem Objekt *File* ([Eine Klasse für Episoden wählen](eine-klasse-fuer-episoden-waehlen.md)).

## Die Anbindung an den Rest

| Teil | Was einzustellen ist | Seite |
|---|---|---|
| Der Feed | Eine Quelle pro Sendung mit der Klasse **Episode**; Titel auf `title`, Beschreibung auf `teaser`, Anhang auf `audio` zuordnen | [Einen Podcast-Feed aufbauen](einen-podcast-feed-aufbauen.md) |
| Zeitplanung | Das Ereignis *Bis Datum warten* mit **Release date** als Attribut | [Eine Veröffentlichung planen](eine-veroeffentlichung-planen.md) |
| Templates | Eine Vollansicht für die Klasse in Ihrem Design, wenn die Standardansicht nicht genügt | [Template-Overrides](../ersteller/website-ersteller/template-overrides.md) |
| Rechte | Redakteure dürfen *Episode* unterhalb der Ordner der Sendung anlegen | [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md) |

## Ein Test vor der ersten Episode

1. Legen Sie eine Testepisode mit einem kurzen MP3 an und veröffentlichen Sie sie.
2. Öffnen Sie die Episodenseite abgemeldet: Der Player erscheint und spielt ab.
3. Öffnen Sie den Feed: Das Element ist vorhanden, mit einem `enclosure`, dessen `type` `audio/mpeg` und dessen
   `length` die Dateigröße ist.
4. Entfernen Sie die Testepisode (in den Papierkorb, dann den Papierkorb leeren), bevor Sie den Feed irgendwo
   einreichen, damit keine App sie je sieht.

## Verwandte Seiten

- [Eine Klasse für Episoden wählen](eine-klasse-fuer-episoden-waehlen.md)
- [Ihr Inhaltsmodell entwerfen](../ersteller/website-ersteller/ihr-inhaltsmodell-entwerfen.md)
- [Datentypen wählen](../ersteller/website-ersteller/datentypen-waehlen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
