---
title: Einen Podcast-Feed aufbauen
audience: podcaster
category: podcasting-streaming
order: 17
summary: Schritt für Schritt - einen RSS-Export im Format Apple Podcasts (RSS 2.0 + iTunes) anlegen, die Kanalfelder ausfüllen, den Ordner der Sendung mit der Episodenklasse als Quelle hinzufügen, Titel, Beschreibung und Enclosure zuordnen, den Export aktivieren und die Feed-Adresse öffnen.
keywords: Podcast-Feed aufbauen, RSS-Export anlegen, Apple Podcasts, iTunes, Setup RSS, Exportformular, Quelle, Zuordnung des Enclosures, Anhang, Zugriffs-URL, aktiv, Feed-Adresse, rss/feed, build podcast feed, create RSS export, export form, source, enclosure mapping, access URL, active, feed address
type: help
---
# Einen Podcast-Feed aufbauen

Diese Seite ist für Produzenten und Administratoren, die den Feed anlegen, mit dem eine Sendung veröffentlicht wird.
Der Feed ist ein **RSS-Export** unter **Setup > RSS** (`/rss/list`); Sie brauchen die Richtlinie `rss/edit`, und der
Siteaccess für Redakteure bietet das Modul nicht an ([RSS-Feeds](../../anleitungen/rss-feeds.md)). Was der fertige Feed
enthält, erklärt [Der Podcast-Feed erklärt](der-podcast-feed-erklaert.md).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Den Export anlegen](#den-export-anlegen)
3. [Der Feed-Teil](#der-feed-teil)
4. [Was der Feed auflistet](#was-der-feed-auflistet)
5. [Der Teil Apple Podcasts](#der-teil-apple-podcasts)
6. [Speichern und öffnen](#speichern-und-öffnen)

## Bevor Sie beginnen

Sie brauchen:

- den Ordner der Sendung mit mindestens einer veröffentlichten Episode, die eine Audiodatei hat
  ([Eine Episode veröffentlichen](eine-episode-veroeffentlichen.md));
- Name, Beschreibung, Autor, Name des Eigentümers und E-Mail-Adresse des Eigentümers der Sendung;
- die vollständige Adresse des Covers ([Cover und Episodenbilder](cover-und-episodenbilder.md));
- die Kategorie aus der Liste von Apple ([Referenz der Kanalfelder](referenz-der-kanalfelder.md)).

## Den Export anlegen

1. Öffnen Sie **Setup > RSS**.
2. Drücken Sie **Neuer Export**. Das Formular öffnet sich als Entwurf; Leser sehen nichts, bevor Sie **OK** drücken.

## Der Feed-Teil

| Feld | Eintrag |
|---|---|
| **Name** | Der Name der Sendung. Feeds tragen ihn auch als Titel |
| **Beschreibung** | Ein Absatz über die Sendung; geschrieben als `description` des Kanals |
| **Feed-Format** | **Apple Podcasts (RSS 2.0 + iTunes)**. Der Abschnitt **Apple Podcasts** erscheint |
| **URL des erzeugten RSS-Feeds** (Zugriffs-URL) | Der letzte Teil der Feed-Adresse, zum Beispiel `example-show`; der Feed antwortet unter `/rss/feed/example-show` |
| **Aktiv** | Setzen Sie das Häkchen, wenn der Feed antworten soll |
| **URL der Website** | Meist leer: Dann wird die öffentliche Adresse des Siteaccess des Exports verwendet. Füllen Sie sie aus, wenn der Feed auf einen bestimmten Host zeigen muss |

Das **Bild** dieses Teils wird nur von RSS-2.0-Exporten verwendet, nicht vom Podcast-Format; das Cover des Podcasts
gehört unten in **Adresse des Titelbilds**.

## Was der Feed auflistet

1. **Anzahl der Objekte**: wie viele Episoden der Feed enthält, neueste zuerst. Wählen Sie genug für Ihr Archiv (bis
   zu 1000).
2. **Nur Hauptknoten**: Setzen Sie das Häkchen, wenn Episoden mehr als einen Ort haben, damit keine doppelt
   aufgeführt wird.
3. Drücken Sie in der Karte der Quelle **Durchsuchen** und wählen Sie den **Ordner der Sendung**.
4. Setzen Sie das Häkchen **Unterknoten**, wenn Episoden in Staffelordnern darunter liegen.
5. Wählen Sie die **Klasse** der Episoden und drücken Sie **Setzen**, um ihre Attribute zu laden.
6. Ordnen Sie die Attribute zu:

| Zuordnung | Auswahl |
|---|---|
| **Titel** | das Titelfeld |
| **Beschreibung** | die kurze Beschreibung oder die Shownotes ([Shownotes schreiben](shownotes-schreiben.md)) |
| **Kategorie** | optional: ein Schlüsselwort- oder Textfeld |
| **Anhang (Medien)** | das Medien- oder Dateifeld mit dem Audio. Erforderlich: Eine Episode ohne es wird weggelassen |

Für mehrere Sendungen in einem Feed oder Episoden aus zwei Klassen drücken Sie für jede **Quelle hinzufügen**.

## Der Teil Apple Podcasts

Füllen Sie mindestens **Name des Eigentümers**, **E-Mail des Eigentümers**, **Adresse des Titelbilds** und
**Kategorie** aus, die die Funktionsbeschreibung als erforderlich aufführt
([RSS: Feeds für Apple Podcasts](../../funktionen/6.0/rss-podcast-und-feed-liste.md#felder-des-kanals)). Dann **Autor**,
**Sprache**, **Untertitel**, **Zusammenfassung**, **Copyright**, **Art der Sendung** und **Enthält explizite Inhalte**.
Lassen Sie **Die Sendung ist abgeschlossen**, **Die Sendung aus dem Apple-Podcasts-Verzeichnis heraushalten** und
**Umgezogen nach** bei einer neuen Sendung leer. Alle Felder: [Referenz der Kanalfelder](referenz-der-kanalfelder.md).

## Speichern und öffnen

1. Drücken Sie **OK**. Wenn etwas das Speichern des Exports verhindert (eine Klasse ohne das gewählte Attribut, ein
   aktiver Export ohne Adresse), listet das Formular es oben auf.
2. In der RSS-Liste zeigt die Karte des Exports die Feed-Adresse und **Feed öffnen**.
3. Öffnen Sie den Feed. Ihre Episoden sollten dort stehen, neueste zuerst, jede mit einem `enclosure`.
4. Führen Sie die Prüfungen unter [Ihren Feed prüfen](ihren-feed-pruefen.md) durch, bevor Sie die Adresse irgendwo
   einreichen.

Die Adresse lautet `https://<your site>/rss/feed/<access URL>`. Wählen Sie die Zugriffs-URL sorgfältig: Sie ist das,
was Verzeichnisse und Abonnenten speichern. Sie später zu ändern erfordert die Schritte unter
[Eine Sendung umziehen, pausieren oder beenden](eine-sendung-umziehen-pausieren-oder-beenden.md).

## Verwandte Seiten

- [Referenz der Episodenzuordnung](referenz-der-episodenzuordnung.md)
- [Bei Podcast-Verzeichnissen einreichen](bei-podcast-verzeichnissen-einreichen.md)
- [RSS- und Podcast-Feeds](../kunden/wachstum/rss-und-podcast-feeds.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
