---
title: "Projekt 5.3: Eine mehrsprachige Veranstaltungswebsite"
audience: trainer, developer
category: training
order: 53
summary: Eine Aufgabenstellung für ein Abschlussprojekt für Entwickler, eine zweisprachige Veranstaltungswebsite mit einer Klasse Event, eigenen Voll- und Zeilenansichten in einer Erweiterung, einer Listenseite und übersetzten Inhalten, in sechs bis acht Stunden auf einer Schulungsinstallation gebaut, mit einem Bewertungsraster.
keywords: abschlussprojekt, projektaufgabe, entwickler, mehrsprachig, veranstaltungswebsite, inhaltsklasse, templates, overrides, erweiterung, übersetzungen, bewertungsraster, capstone, project brief, developers, multilingual, event site, content class, extension, translations, rubric
type: help
---
# Projekt 5.3: Eine mehrsprachige Veranstaltungswebsite

Entwickler beweisen ihr Können, indem sie eine kleine Funktion von Anfang bis Ende liefern. Diese Aufgabe passt an das
Ende des [Entwickler-Bootcamps](../modul-03-dreitaegige-bootcamps/entwickler-bootcamp.md) oder der
[Full-Stack-Entwicklerwoche](../modul-04-fuenftaegige-bootcamps/full-stack-entwicklerwoche.md).

## Auf einen Blick

| | |
|---|---|
| Kurs | [Workshops und Bootcamps](../README.md) |
| Modul | [Modul 5: Abschlussprojekte](README.md) |
| Dauer | 6 bis 8 Stunden |
| Niveau | Fortgeschrittene bis Experten |
| Zielgruppe | Entwickler, allein oder zu zweit |
| Voraussetzungen | Inhaltsklassen, Templates und Overrides, Erweiterungen ([Templates und Design](../../../../anleitungen/templates-und-design.md), [Erweiterungen](../../../../anleitungen/extensions.md)) |

## Lernziele

Die Teilnehmenden zeigen, dass sie:

- eine Inhaltsklasse modellieren und erstellen können;
- Templates für Voll- und Zeilenansicht schreiben und sie pro Klasse per Override ersetzen können;
- die Arbeit als Erweiterung paketieren und aktivieren können;
- zwei Inhaltssprachen unterstützen können.

## Die Aufgabenstellung

> Die erfundene „Lakeside Music Week“ braucht eine Veranstaltungswebsite auf Englisch und in einer weiteren Sprache.
> Jedes Konzert hat einen Titel, Datum und Uhrzeit, einen Veranstaltungsort, eine Beschreibung und ein Bild. Die Seite
> Concerts listet alle Konzerte nach Datum. Jedes Konzert hat eine eigene Seite. Alle Templates und Einstellungen
> liegen in einer Erweiterung `lakesidemusic`.

| Teil | Anforderung |
|---|---|
| Klasse | „Concert“: Titel, Datum und Uhrzeit, Veranstaltungsort, Beschreibung, Bild |
| Ansichten | Eine Vollansicht für Konzerte; eine Zeilenansicht, die in der Liste Concerts verwendet wird |
| Liste | Der Ordner Concerts listet seine Konzerte nach Datum sortiert |
| Erweiterung | `lakesidemusic` enthält die Overrides, Templates und Einstellungen; aktiviert, Autoloads neu erzeugt |
| Sprachen | Eine zweite Inhaltssprache; mindestens zwei Konzerte übersetzt |

## Hinweise für Trainer

- **Begutachten Sie den Code** anhand der [Programmierrichtlinien](../../../../../eng-us/standards/coding-standards.md) und der
  Template-Regeln: Ausgabe mit `|wash` maskiert, keine zwischen Templates kopierte Logik.
- **Prüfen Sie die Grenze der Erweiterung**: Außerhalb von `extension/lakesidemusic` wurde nichts geändert, abgesehen
  von der Aktivierung.
- **Sprache**: Eine zweite Sprache muss der Website hinzugefügt werden, bevor übersetzt werden kann
  ([Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md)).

## Praktische Übung

1. Erstellen Sie das Grundgerüst der Erweiterung (von Hand oder mit einem RAD-Assistenten) und aktivieren Sie sie.
2. Erstellen Sie die Klasse „Concert“ und vier Konzerte.
3. Schreiben Sie die Templates für Zeilen- und Vollansicht und die Overrides in der Erweiterung.
4. Lassen Sie den Ordner Concerts die Konzerte nach Datum sortiert auflisten.
5. Fügen Sie die zweite Inhaltssprache hinzu und übersetzen Sie zwei Konzerte.
6. Erzeugen Sie die Autoloads neu, wenn die Erweiterung Klassen enthält, leeren Sie die Caches und testen Sie jede
   Seite in beiden Sprachen.
7. Präsentieren Sie das Ergebnis und die Dateiliste der Erweiterung.

## Erwartetes Ergebnis

Bewertungsraster (jedes Kriterium 0, 1 oder 2 Punkte):

| Kriterium | 0 | 1 | 2 |
|---|---|---|---|
| Klasse | Felder fehlen | Vollständig | Vollständig, sinnvolle Bezeichner und Pflichtfelder |
| Ansichten | Nur Standardansichten | Eine eigene Ansicht | Voll- und Zeilenansicht, maskierte Ausgabe |
| Liste | Unsortiert oder fehlt | Sortiert, Standardansicht | Nach Datum sortiert mit der Zeilenansicht |
| Grenze der Erweiterung | Änderungen außerhalb der Erweiterung | Größtenteils innerhalb | Alles innerhalb; sauber aktiviert |
| Sprachen | Eine Sprache | Zweite Sprache hinzugefügt | Zwei Konzerte übersetzt und korrekt angezeigt |
| Präsentation | Fehlt | Funktioniert | Funktioniert, erklärt, Dateiliste gezeigt |

Bestanden ist mit 8 von 12 Punkten; das Bewertungsraster ist das eigene des Trainers und beansprucht keinen
offiziellen Status.

## Quiz

1. Warum muss die Ausgabe eines Templates mit `|wash` maskiert werden?
2. Wo liegen die Overrides dieses Projekts?
3. Was muss existieren, bevor Inhalte in eine neue Sprache übersetzt werden können?

<details>
<summary>Antworten</summary>

1. Um Werte sicher in HTML auszugeben, damit Inhalte kein Markup und keine Skripte einschleusen können.
2. In der Erweiterung `lakesidemusic` (ihren Einstellungen und ihrem Design), nicht in `settings/siteaccess/`.
3. Die Sprache muss der Website hinzugefügt sein ([Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md)).

</details>

## Weiterführende Informationen

- [Entwicklung von Erweiterungen](../../../../../eng-us/help/extension-development/README.md)
- [Templates und Design](../../../../anleitungen/templates-und-design.md)
- [Übersetzungen und Sprachen](../../../../../eng-us/features/6.0/translations-and-languages.md)

## Verwandte Seiten

- [Modul 5: Abschlussprojekte](README.md)
- [Workshops und Bootcamps: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
