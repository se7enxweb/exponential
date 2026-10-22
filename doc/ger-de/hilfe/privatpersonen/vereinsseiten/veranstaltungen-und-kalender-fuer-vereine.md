---
title: Veranstaltungen und Kalender für Vereine
audience: individual, website-creator, customer
category: personal-sites
order: 1714
summary: Wie eine ehrenamtliche Person Treffen, Spiele, Konzerte und Ausflüge auf einer Exponential-Vereinsseite mit den mitgelieferten Klassen Event, Event calendar und Multicalendar veröffentlicht - Kalender- oder Programmansicht, öffentliche Veranstaltungen und solche nur für Mitglieder, Anmeldungen, Karten, Ankündigungen und was nicht eingebaut ist (Tickets, Kalenderexport).
keywords: Veranstaltungen, Kalender, Klasse Event, Event calendar, Multicalendar, Programmansicht, Treffen, Spiele, Konzerte, Veranstaltungen nur für Mitglieder, Anmeldung, ezgmaplocation, Karte, ankündigen, Ticketverkauf, iCal, Verein, events, calendar, sign-up, map, ticketing, club
type: help
---
# Veranstaltungen und Kalender für Vereine

Diese Seite ist für Privatpersonen, die den Kalender eines Vereins auf dessen Website pflegen. Die vollständige Seite
für Inhaber von Mitgliederorganisationen ist [Veranstaltungen und Kalender](../../unternehmen/mitglieder/veranstaltungen-und-kalender.md);
diese hier ist die Kurzfassung für eine ehrenamtliche Person, die auch noch alles andere erledigen muss.

## Inhalt

1. [Die Klassen](#die-klassen)
2. [Den Kalender einrichten](#den-kalender-einrichten)
3. [Eine Veranstaltung hinzufügen](#eine-veranstaltung-hinzufügen)
4. [Öffentlich und nur für Mitglieder](#öffentlich-und-nur-für-mitglieder)
5. [Anmeldungen](#anmeldungen)
6. [Wo es stattfindet: Karten](#wo-es-stattfindet-karten)
7. [Davon erzählen](#davon-erzählen)
8. [Nicht eingebaut](#nicht-eingebaut)
9. [Checkliste](#checkliste)

## Die Klassen

| Klasse | Felder | Verwendung |
|---|---|---|
| *Event* | Titel, Kurztitel, Text, Kategorie, Beginn, Ende, Metadaten | Ein Treffen, Spiel oder Konzert |
| *Event calendar* | Titel, Kurztitel, **View** (Ansicht) | Enthält Veranstaltungen; zeigt einen Monatskalender oder eine Programmliste |
| *Multicalendar* | Name, Beschreibung, Kalender | Eine Seite, die mehrere Kalender zusammenführt |

Quellen: `class-event.xml` und `class-event_calendar.xml` im Paket `sevenx_classes`; die `ezwebin`-Pakete für alle
drei ([Veranstaltungen und Kalender, Die Veranstaltungsklassen](../../unternehmen/mitglieder/veranstaltungen-und-kalender.md#die-veranstaltungsklassen)).

## Den Kalender einrichten

1. Legen Sie unter der Startseite einen *Event calendar* namens „Kalender" an.
2. Wählen Sie seine **View**: Ein Monatskalender passt zu Vereinen mit häufigen Treffen; eine Programmliste passt zu
   einer Saison mit Konzerten oder Spielen.
3. Veröffentlichen. Ein im Baum platzierter Kalender erscheint im Menü des Designs `ezwebin`.

Die Kalenderseite wird einige Minuten zwischengespeichert; eine neue Veranstaltung kann daher einen Moment brauchen,
bis sie dort erscheint.

## Eine Veranstaltung hinzufügen

1. **Hier erstellen** > *Event* innerhalb des Kalenders.
2. **Kurztitel** (Pflichtfeld) für das Kalenderraster, **vollständiger Titel** für die Veranstaltungsseite.
3. **Beginn** (Pflichtfeld) und **Ende**.
4. **Kategorie**: Schlüsselwörter wie „Training", „Spiel", „Geselliges"; verwenden Sie jedes Mal dieselben Wörter.
5. **Text**: Ort, was mitzubringen ist, Kosten, Kontakt.
6. Veröffentlichen.

Wiederkehrende Termine (jeden Dienstag) werden einzeln eingetragen; es gibt keine Wiederholungsregel. Kopieren Sie
eine bestehende Veranstaltung, um sich Tipparbeit zu sparen.

## Öffentlich und nur für Mitglieder

Ein Veranstaltungskalender ist Inhalt, also entscheidet seine Sektion, wer ihn sieht. Führen Sie zwei Kalender, wenn
Sie beides brauchen: einen öffentlichen und einen im Mitgliederbereich
([Private Bereiche mit Rollen und Sektionen](../familienseiten/private-bereiche-mit-rollen-und-sektionen.md)). Ein
*Multicalendar* kann angemeldeten Mitgliedern beide auf einer Seite zeigen.

## Anmeldungen

Es gibt kein Modul für Veranstaltungsanmeldungen. Was funktioniert:

- ein Anmeldeformular neben der Veranstaltung, gebaut mit dem Informationssammler
  ([Formulare, Umfragen und Anmeldungen](formulare-umfragen-und-anmeldungen.md));
- eine Newsletter-Liste für Menschen, die Einladungen erhalten möchten ([Vereinsnewsletter](vereinsnewsletter.md)).

## Wo es stattfindet: Karten

`ezgmaplocation` (in der `composer.json` im Wurzelverzeichnis) fügt einen Datentyp für einen Kartenort hinzu, den Sie
an die Klasse *Event* oder an einen Artikel „Veranstaltungsort" setzen können
([ezgmaplocation](../../../funktionen/6.0/erweiterungen/ezgmaplocation.md)). Eine angezeigte Karte schickt die Browser der
Besucher zum Kartenanbieter; erwähnen Sie das in Ihrem Datenschutztext. Eine einfache Adresse und ein Link zu einem
Kartendienst funktionieren auch.

## Davon erzählen

- Eine Liste „Nächste Termine" auf der Startseite.
- Die nächste Newsletter-Ausgabe.
- Ein RSS-Export des öffentlichen Kalenders ([RSS-Feeds für Ihr Blog](../blogs/rss-feeds-fuer-ihr-blog.md)).
- Nach der Veranstaltung: Ergebnisse, Fotos (mit Einwilligung) in einer *Gallery*.

## Nicht eingebaut

- **Ticketverkauf** und kostenpflichtige Anmeldung.
- **Export in Smartphone-Kalender** (iCal-Dateien) von Haus aus.
- **Wiederholungsregeln** für wiederkehrende Termine.
- **Automatische Erinnerungen** an Angemeldete.

## Checkliste

- [ ] Ein öffentlicher Kalender, bei Bedarf ein Kalender nur für Mitglieder.
- [ ] Kategoriewörter abgesprochen.
- [ ] Jede Veranstaltung hat Kurztitel, Beginn, Ort und Kontakt.
- [ ] Anmeldeformular, wo nötig, gegen Spam geschützt.
- [ ] Vergangene Veranstaltungen bewusst behalten oder archiviert.

## Verwandte Seiten

- [Vereinsseiten](README.md)
- [Veranstaltungen und Kalender](../../unternehmen/mitglieder/veranstaltungen-und-kalender.md)
- [Veranstaltungen und Neuigkeiten für Gruppen](../../unternehmen/vereine/veranstaltungen-und-neuigkeiten-fuer-gruppen.md)
- [Geburtstage und Familientermine](../familienseiten/geburtstage-und-familientermine.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
