---
title: Die Sendungsstruktur einrichten
audience: podcaster
category: podcasting-streaming
order: 9
summary: Schritt für Schritt - die Ordner für eine Sendung, ihre Staffeln und ihre Gäste im Inhaltsbaum anlegen, die Sendung in ihrem Ordner beschreiben, entscheiden, wo die Audiodateien liegen, und prüfen, ob die Struktur zu dem Feed passt, den Sie aufbauen werden.
keywords: Sendung einrichten, Ordner, Staffelordner, Inhaltsbaum, Ordner für Gäste, Medienbibliothek, Struktur, Ordner anlegen, Seite der Sendung, Podcast einrichten, set up show, folder, season folder, content tree, guests folder, media library, structure, create folder, show page, podcast setup
type: help
---
# Die Sendungsstruktur einrichten

Diese Seite ist für Produzenten und Redakteure, die eine neue Sendung auf der Website einrichten. Sie setzt voraus,
dass die Episodenklasse existiert ([Eine Klasse für Episoden wählen](eine-klasse-fuer-episoden-waehlen.md)). Den
Hintergrund erklärt [Wie eine Sendung zu Inhalten wird](wie-eine-sendung-zu-inhalten-wird.md). Sie brauchen die Berechtigung,
Ordner dort anzulegen, wo die Sendung liegen wird; wird Ihnen ein Schritt nicht angeboten, fragen Sie Ihren
Administrator.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Den Ordner der Sendung anlegen](#den-ordner-der-sendung-anlegen)
3. [Staffelordner anlegen](#staffelordner-anlegen)
4. [Einen Ordner für Gäste anlegen](#einen-ordner-für-gäste-anlegen)
5. [Entscheiden, wo Dateien liegen](#entscheiden-wo-dateien-liegen)
6. [Das Ergebnis prüfen](#das-ergebnis-prüfen)

## Bevor Sie beginnen

Notieren Sie auf Papier oder in Ihrem Planungsdokument:

- den Namen der Sendung, wie er auf der Website und in Apps erscheint;
- einen Absatz, der die Sendung beschreibt;
- ob und wie Sie Staffeln nummerieren;
- welche Klasse die Episoden verwenden;
- wohin die Sendung auf der Website gehört (zum Beispiel unter einen Ordner "Podcasts" im Hauptmenü).

## Den Ordner der Sendung anlegen

1. Öffnen Sie in der Administration die **Inhaltsstruktur** und gehen Sie an die Stelle, an die die Sendung gehört.
2. Drücken Sie unter **Unterelemente** auf **Neu erstellen** (oder wählen Sie **Hier erstellen** im Menü des Objekts im
   Baum) und wählen Sie **Ordner**.
3. Tragen Sie den Namen der Sendung und eine kurze Beschreibung ein. Fügen Sie ein Bild hinzu, wenn Ihre Ordnerklasse
   ein Bildfeld hat.
4. Drücken Sie **Zur Veröffentlichung schicken**.

Die allgemeinen Schritte zum Anlegen von Objekten stehen unter
[Inhalte erstellen und bearbeiten](../inhalte-bearbeiten.md#ein-neues-objekt-erstellen).

> **Hinweis**
> Die Beschreibung des Ordners lesen Besucher auf der Seite der Sendung. Die Podcast-Apps lesen stattdessen die
> **Beschreibung** und die **Zusammenfassung** des Exports; diese füllen Sie aus, wenn Sie den Feed aufbauen.

## Staffelordner anlegen

Überspringen Sie diesen Schritt bei einer Sendung ohne Staffeln.

1. Öffnen Sie den Ordner der Sendung.
2. Legen Sie darin für jede Staffel einen **Ordner** an: "Staffel 1", "Staffel 2" und so weiter.
3. Ordnen Sie die Ordner so, wie Sie sie auf der Website haben möchten. Die Reihenfolge des Feeds hängt nicht davon
   ab: Der Feed sortiert Episoden nach ihrem Datum der Veröffentlichung.

Wenn Sie später den Feed aufbauen, setzen Sie bei der Quelle das Häkchen **Unterknoten**, damit Episoden in allen
Staffelordnern aufgelistet werden.

## Einen Ordner für Gäste anlegen

Wenn Gäste eigene Seiten bekommen, legen Sie einen Ordner für sie an, zum Beispiel im Ordner der Sendung oder einmal
für alle Sendungen. Dort liegen die Personen-Objekte; Episoden werden mit ihnen verknüpft
([Gäste und Mitwirkende](gaeste-und-mitwirkende.md)). Auf Installationen mit dem Paket der Media Site gibt es dafür die
Klasse *Person* (`ng_person`).

## Entscheiden, wo Dateien liegen

Zwei Wege funktionieren; wählen Sie einen und schreiben Sie ihn in die Richtlinien Ihres Teams:

| Weg | Wie er funktioniert | Wann er passt |
|---|---|---|
| Die Datei in der Episode | Das Medienfeld der Episode enthält die Datei | die meisten Sendungen: ein Objekt pro Episode, einfache Zuordnung im Feed |
| Die Datei in der Medienbibliothek | Dateien sind eigene Objekte unter **Medien**; die Episode ist mit ihnen verknüpft | wenn Dateien wiederverwendet oder von einem anderen Team verwaltet werden |

Der Podcast-Feed braucht die Datei in einem Medien- oder Dateifeld **des Episodenobjekts selbst**: Die Zuordnung des
Enclosures nennt ein Feld der Klasse, die im Feed aufgeführt wird, und einem verknüpften Objekt wird nicht gefolgt
(`podcastItemList()` in `kernel/classes/ezrssexport.php`). Für einen Feed ist der erste Weg derjenige, der ohne
Änderungen an Templates oder Code funktioniert.

## Das Ergebnis prüfen

- [ ] Der Ordner der Sendung erscheint auf der Website dort, wo Sie es erwartet haben, mit seiner Beschreibung.
- [ ] Staffelordner, falls vorhanden, liegen im Ordner der Sendung.
- [ ] Ihre Redakteure können Objekte der Episodenklasse in den Ordnern der Sendung (oder der Staffeln) anlegen.
- [ ] Der Ordner für Gäste existiert, wenn Sie einen brauchen.
- [ ] Alle wissen, wohin die Audiodateien gehören.

Als Nächstes: [Audio hochladen](audio-hochladen.md) oder [Eine Episode veröffentlichen](eine-episode-veroeffentlichen.md),
dann [Einen Podcast-Feed aufbauen](einen-podcast-feed-aufbauen.md).

## Verwandte Seiten

- [Wie eine Sendung zu Inhalten wird](wie-eine-sendung-zu-inhalten-wird.md)
- [Die Medienbibliothek ordnen](../ersteller/website-ersteller/die-medienbibliothek-organisieren.md)
- [Menüs und Navigation](../ersteller/website-ersteller/menues-und-navigation.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
