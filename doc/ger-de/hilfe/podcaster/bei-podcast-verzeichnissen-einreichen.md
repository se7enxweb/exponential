---
title: Bei Podcast-Verzeichnissen einreichen
audience: podcaster
category: podcasting-streaming
order: 20
summary: Was Exponential bereitstellt, wenn eine Sendung bei Apple Podcasts und anderen Verzeichnissen eingereicht wird - eine stabile Feed-Adresse mit den Elementen, die Apple verlangt -, was außerhalb von Exponential geschieht und welche Prüfungen vor und nach dem Einreichen nötig sind.
keywords: Podcast einreichen, Apple Podcasts, Verzeichnis, Podcast-App, Feed-Adresse, Validierung, Einreichung, Eigentümerschaft, E-Mail des Eigentümers, Eintrag, nach dem Einreichen, submit podcast, directory, podcast app, feed address, validation, submission, ownership, owner email, listing, after submission
type: help
---
# Bei Podcast-Verzeichnissen einreichen

Diese Seite ist für Produzenten, die eine Sendung in Podcast-Verzeichnissen eintragen. Der Teil von Exponential ist der
Feed: eine Adresse, die mit RSS 2.0 und den iTunes-Elementen antwortet. Die Einreichung selbst geschieht in den
eigenen Werkzeugen jedes Verzeichnisses, mit Ihrem Konto dort; nichts in Exponential verbindet sich mit einem
Verzeichnis.

## Inhalt

1. [Was Exponential Ihnen gibt](#was-exponential-ihnen-gibt)
2. [Bevor Sie einreichen](#bevor-sie-einreichen)
3. [Einreichen](#einreichen)
4. [Nachdem Sie eingereicht haben](#nachdem-sie-eingereicht-haben)
5. [Was diese Seite Ihnen nicht sagen kann](#was-diese-seite-ihnen-nicht-sagen-kann)

## Was Exponential Ihnen gibt

- Eine Feed-Adresse: `https://<your site>/rss/feed/<access URL>`.
- Die Kanalelemente, die Apple verlangt, soweit Sie sie ausgefüllt haben: Titel, Beschreibung, Sprache, Cover,
  Kategorie, Kennzeichnung expliziter Inhalte, Eigentümer ([Referenz der Kanalfelder](referenz-der-kanalfelder.md)).
- Episoden mit einem `enclosure`, das die echte Länge in Byte und den MIME-Typ trägt, eine `guid`, die sich nicht
  ändert, und ein `pubDate` ([Referenz der Episodenzuordnung](referenz-der-episodenzuordnung.md)).
- Werte, die beim Speichern des Exports auf das begrenzt werden, was das Format erlaubt, "rather than stored as typed
  and rejected later by Apple, where the reason would not be visible", also statt so gespeichert zu werden, wie sie
  eingetippt wurden, und später von Apple abgelehnt zu werden, wo der Grund nicht sichtbar wäre (Kommentar zu
  `setPodcastHead()` in `kernel/classes/ezrssexport.php`).

Der Feed ist gewöhnliches RSS, daher können ihn auch andere Verzeichnisse und Apps als die von Apple lesen.

## Bevor Sie einreichen

- [ ] Mindestens eine Episode ist veröffentlicht und im Feed. Ein Feed ohne Elemente gibt einem Verzeichnis nichts zu
      prüfen.
- [ ] Name des Eigentümers, E-Mail des Eigentümers, Adresse des Titelbilds und Kategorie sind ausgefüllt.
- [ ] Die Adresse des Titelbilds öffnet das Bild im Browser, abgemeldet.
- [ ] Jedes Enclosure öffnet sich und spielt abgemeldet ab; sein `type` ist ein Audio- oder Videotyp, nicht
      `application/octet-stream`.
- [ ] Die Feed-Adresse verwendet `https://` und den öffentlichen Hostnamen, nicht den Host der Administration
      ([Ihren Feed prüfen](ihren-feed-pruefen.md)).
- [ ] Die Zugriffs-URL ist die, die Sie behalten möchten. Verzeichnisse speichern sie.
- [ ] Die Testepisoden, die Sie beim Einrichten angelegt haben, sind entfernt.

## Einreichen

1. Melden Sie sich in den Werkzeugen des Verzeichnisses für Podcaster mit dem Konto an, dem der Eintrag gehören soll.
2. Geben Sie die Feed-Adresse ein.
3. Folgen Sie der Validierung des Verzeichnisses. Meldet sie ein Problem, beheben Sie es im Export oder in der
   Episode, warten Sie den Feed-Cache ab (im Auslieferungszustand 1200 Sekunden, oder bitten Sie Ihren Administrator,
   den RSS-Cache zu leeren) und validieren Sie erneut.

Die E-Mail-Adresse des Eigentümers ist im Feed öffentlich und die Stelle, an der ein Verzeichnis die Sendung erreichen
kann. Verwenden Sie eine Funktionsadresse der Sendung, die jemand liest.

## Nachdem Sie eingereicht haben

- **Ändern Sie die Zugriffs-URL nicht.** Wenn Sie den Feed umziehen müssen, verwenden Sie **Umgezogen nach** und lassen
  Sie die alte Adresse noch eine Weile antworten ([Eine Sendung umziehen, pausieren oder beenden](eine-sendung-umziehen-pausieren-oder-beenden.md)).
- **Legen Sie Episoden nicht neu an.** Ein neu angelegtes Objekt erhält eine neue Remote-ID und damit eine neue `guid`,
  und Apps zeigen es als neue Episode.
- **Behalten Sie die Dateien alter Episoden.** Ein Objekt zu entfernen entfernt sein Enclosure, und Abonnenten, die die
  Liste geladen haben, erhalten dafür Fehler.
- **Halten Sie den Feed aktiv.** Ein inaktiver Export antwortet nicht mehr ([RSS-Feeds](../../anleitungen/rss-feeds.md#5-anlegen-und-entfernen)).

## Was diese Seite Ihnen nicht sagen kann

Die aktuellen Regeln jedes Verzeichnisses (Größe des Covers, Prüfzeiten, Inhaltsrichtlinien, welche Elemente sie lesen)
legen diese Verzeichnisse fest, und sie ändern sich. Die einzigen verzeichnisspezifischen Fakten im Code von
Exponential sind die Kategorienliste von Apple aus dem Jahr 2019 und die in den Code-Kommentaren genannten
erforderlichen Kanalelemente. Lesen Sie vor dem Einreichen die eigene Dokumentation des Verzeichnisses, und zitieren
Sie diese Seite nicht als Regeln eines Verzeichnisses.

## Verwandte Seiten

- [Einen Podcast-Feed aufbauen](einen-podcast-feed-aufbauen.md)
- [Ihren Feed prüfen](ihren-feed-pruefen.md)
- [Checklisten für Podcaster](checklisten-fuer-podcaster.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
