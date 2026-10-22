---
title: Rechte und Lizenzen dessen, was Sie veröffentlichen
audience: podcaster
category: podcasting-streaming
order: 35
summary: Wie Sie mit den Rechten an Episoden umgehen, die Sie auf einer Exponential-Website veröffentlichen - Ihre eigene Lizenz, Musik und Clips, Einverständniserklärungen von Gästen, Cover und Fotos, eingebettete Player von Drittanbietern -, welche Felder Exponential dafür hat und welche Sie ergänzen, und wie Sie reagieren, wenn ein Rechteproblem auftaucht.
keywords: Rechte, Lizenz, Lizenzierung, Urheberrecht, Creative Commons, Musikrechte, Einverständnis von Gästen, Einwilligung, Rechte am Cover, Fotonachweis, Inhalte Dritter, Entfernung, GPL, Eigentum, rights, license, licensing, copyright, music clearance, guest release, consent, artwork rights, photo credit, third-party content, takedown, ownership
type: help
---
# Rechte und Lizenzen dessen, was Sie veröffentlichen

Diese Seite ist für Produzenten, die Episoden veröffentlichen und klar wissen möchten, wem was gehört, was Hörer damit
tun dürfen und was geklärt wurde. Sie erklärt, was Exponential festhält und was Sie ergänzen. Sie ist keine
Rechtsberatung; fragen Sie bei Verträgen und Streitfällen einen Anwalt.

## Inhalt

1. [Ihre Inhalte gehören Ihnen](#ihre-inhalte-gehören-ihnen)
2. [Wohin Angaben zu Rechten gehören](#wohin-angaben-zu-rechten-gehören)
3. [Musik, Clips und Geräuscheffekte](#musik-clips-und-geräuscheffekte)
4. [Gäste und Mitwirkende](#gäste-und-mitwirkende)
5. [Bilder und Cover](#bilder-und-cover)
6. [Eingebettete Player](#eingebettete-player)
7. [Wenn ein Rechteproblem auftaucht](#wenn-ein-rechteproblem-auftaucht)

## Ihre Inhalte gehören Ihnen

Exponential ist freie Software unter der GNU General Public License, Version 2 oder später. Diese Lizenz gilt für die
Software, nicht für das, was Sie damit veröffentlichen: Ihre Episoden, Notizen und Cover gehören Ihnen, zu den
Bedingungen, die Sie wählen ([Lizenzen erklärt für Reporter](../presse/lizenzen-fuer-reporter-erklaert.md)). Diese
Hilfe steht unter der GNU FDL 1.3 ([Freie Dokumentation](../freie-dokumentation.md)); auch das sagt nichts über Ihre
Sendung.

## Wohin Angaben zu Rechten gehören

Exponential hat kein eigenes Lizenzfeld. Was es gibt:

| Ort | Was er enthält | Wo er erscheint |
|---|---|---|
| Das Kanalfeld **Copyright** des Feeds | eine Zeile für die ganze Sendung | Element `copyright` des Podcast-Feeds |
| Ein Feld, das Sie der Episodenklasse hinzufügen | die Lizenz jeder Episode, zum Beispiel eine **Auswahl** (`ezselection`) mit Ihren Lizenzen oder eine Textzeile | die Episodenseite, wenn das Design es zeigt |
| Das Feld **Copyright** der Klasse *Person* (Installationen mit Media Site) | der Nachweis für das Foto einer Person | die Personenseite |
| Die Shownotes | Nachweise und Lizenzen der Musik, Quellen | die Episodenseite und die Beschreibung im Feed |
| Ein Feld **Tags** (eztags, wo installiert) | eine Lizenz als Tag, damit sich Episoden nach Lizenz auflisten lassen | Listen und Suche |

Vorgeschlagene Felder für Episoden: **License** (Auswahl), **Music credits** (Textblock), **Rights notes** (ein interner
Textblock, den Ihr Design nicht zeigt). Siehe [Eine Episodenklasse aufbauen](eine-episodenklasse-aufbauen.md).

Auch ein internes Feld wird in der Datenbank gespeichert und ist für alle sichtbar, die das Objekt in der
Administration bearbeiten oder lesen dürfen. Bewahren Sie Verträge und Einverständniserklärungen in Ihren eigenen
Unterlagen auf, nicht in öffentlichen Inhalten.

## Musik, Clips und Geräuscheffekte

- Verwenden Sie nur Material, das Sie veröffentlichen dürfen: eigenes, für Podcasts und das Web lizenziertes oder unter
  einer Lizenz, die es erlaubt.
- Beachten Sie die Lizenzbedingungen: Manche Lizenzen verbieten kommerzielle Nutzung oder verlangen eine Nennung in
  einer festen Form.
- Nennen Sie das Material in den Shownotes und, wo die Lizenz es verlangt, im Audio.
- Führen Sie Buch darüber, was in welcher Episode verwendet wurde, mit der Lizenz und ihrem Datum; Sie brauchen das,
  wenn Jahre später ein Anspruch erhoben wird. [Musik, Schriften und Material von Dritten](../presse/musik-schriften-und-material-von-dritten.md#nachweise-aufbewahren)
  im Pressebereich zeigt ein Muster.

## Gäste und Mitwirkende

- Vereinbaren Sie vorab, wo die Aufnahme veröffentlicht wird (Website, Feed, Verzeichnisse, Clips), für wie lange und
  ob sie geschnitten werden darf.
- Vereinbaren Sie, wie der Gast genannt wird und welches Foto und welche Links verwendet werden
  ([Gäste und Mitwirkende](gaeste-und-mitwirkende.md)).
- Mitwirkende, die keine Angestellten sind (freie Cutter, Komponisten), sollten dieselbe Klarheit schriftlich haben.

## Bilder und Cover

- Das Cover der Sendung und die Episodenbilder brauchen dieselbe Klärung wie Audio.
- Fotos von Gästen: Verwenden Sie Fotos, die der Gast geliefert hat oder die Sie mit seiner Einwilligung aufgenommen
  haben, und nennen Sie den Fotografen.
- Bildobjekte in der Medienbibliothek haben eine Bildunterschrift oder Beschreibung, wo Ihre Klassen eine haben; setzen
  Sie den Nachweis dorthin.

## Eingebettete Player

Ein von einem Dienst eingebettetes Video bleibt Inhalt des Dienstes und dessen, der es hochgeladen hat. Betten Sie nur
Videos ein, deren Eigentümer das Einbetten erlaubt; der Dienst setzt das meist selbst durch. Einbetten gibt Ihnen nicht
das Recht, das Video herunterzuladen und erneut hochzuladen ([Ein Video oder einen Stream einbetten](ein-video-oder-einen-stream-einbetten.md)).

## Wenn ein Rechteproblem auftaucht

1. **Handeln Sie schnell auf der Website**: Verstecken Sie die Episode, was sie von der Website und bei der nächsten
   Aktualisierung des Caches aus dem Feed nimmt ([Episoden verbergen und zurückziehen](episoden-verbergen-und-zurueckziehen.md)).
2. **Prüfen Sie jede Stelle**, an der sie erscheint: andere Seiten, Einbettungen, Layouts, den Newsletter, Feeds von
   Partnern.
3. **Entscheiden Sie**: Bearbeiten (Musik ersetzen, einen Abschnitt herausschneiden) und dasselbe Objekt erneut
   veröffentlichen, damit seine `guid` bleibt, oder es entfernen.
4. **Halten Sie fest**, was geschehen ist und was Sie getan haben.

Kopien, die die Apps der Hörer bereits heruntergeladen haben, liegen außerhalb Ihrer Kontrolle.

## Verwandte Seiten

- [Musik, Schriften und Material von Dritten](../presse/musik-schriften-und-material-von-dritten.md)
- [Gäste und Mitwirkende](gaeste-und-mitwirkende.md)
- [Episoden nur für Mitglieder](episoden-nur-fuer-mitglieder.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
