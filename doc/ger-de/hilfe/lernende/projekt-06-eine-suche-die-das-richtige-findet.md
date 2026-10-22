---
title: "Projekt 6: eine Suche, die das Richtige findet"
audience: student
category: learning
order: 30
summary: Lernprojekt 6 - lernen, wie die eingebaute Suchmaschine Ihre Inhalte indiziert, planen, welche Attribute Ihrer Klassen durchsuchbar sind, eine Markierung ändern, den Index mit updatesearchindex.php neu aufbauen, Suchergebnisse vorher und nachher vergleichen und einen Suchtestplan mit Nachweisen abgeben, bewertet mit einem Bewertungsraster.
keywords: lernprojekt, suche, eZSearchEngine, durchsuchbar, suchindex, updatesearchindex, --clean, erweiterte suche, klassenattribut, Solr, ezfind, lernende, study project, search, searchable, search index, advanced search, class attribute, learner
type: help
---
# Projekt 6: eine Suche, die das Richtige findet

Besucher beurteilen eine Website danach, ob ihre Suche findet, wonach sie suchen. In diesem Projekt lernen Sie, wie
die eingebaute Suchmaschine Inhalte indiziert, entscheiden, welche Felder gefunden werden sollen, ändern eines, bauen
den Index neu auf und weisen den Unterschied nach.

## Inhalt

1. [Auf einen Blick](#auf-einen-blick)
2. [Ziel](#ziel)
3. [Bevor Sie beginnen](#bevor-sie-beginnen)
4. [Aufgaben](#aufgaben)
5. [Abgabe](#abgabe)
6. [Bewertungsraster](#bewertungsraster)
7. [Zusatzziele](#zusatzziele)

## Auf einen Blick

| | |
|---|---|
| Stufe | Fortgeschritten |
| Zeit | Etwa 2 Stunden |
| Pfade | Administrator, Entwickler |
| Voraussetzungen | [Projekt 1](projekt-01-eine-eigene-inhaltsklasse.md); ein Terminal; eine Lernwebsite, die nur Sie nutzen, denn Klassen werden von allen auf einer Installation gemeinsam genutzt |

## Ziel

Ein Suchtestplan mit zehn Suchanfragen und erwarteten Ergebnissen, eine geänderte Markierung **Durchsuchbar**, ein
neu aufgebauter Index und ein Vergleich vorher und nachher, der Ihren Erwartungen entspricht.

## Bevor Sie beginnen

- Lesen Sie [Suchen](../suchen.md) für die Sicht der Redakteure.
- Die Suchmaschine der Installation wird in `settings/site.ini [SearchSettings] SearchEngine` festgelegt; der
  ausgelieferte Wert ist `eZSearchEngine`, die eingebaute Suchmaschine. Sie braucht keinen zusätzlichen Server: Ihr
  Index liegt in zwei Tabellen der eigenen Datenbank der Website
  ([Lektion 1.2: Die eingebaute Suchmaschine](../trainer/suche/modul-01-wie-die-suche-funktioniert/die-eingebaute-suchmaschine.md)).
- Die Suchmaschine indiziert nur Attribute, die in ihrer Klasse als **Durchsuchbar** markiert sind; eine geänderte
  Markierung gilt für neue Indizierungen, sodass vorhandene Objekte ihre alten Einträge behalten, bis sie erneut
  veröffentlicht werden oder der Index neu aufgebaut wird
  ([Lektion 2.4: Durchsuchbare Attribute wählen](../trainer/suche/modul-02-indizierung/durchsuchbare-attribute-waehlen.md)).

Prüfen Sie die Suchmaschine auf Ihrer Website:

```bash
php bin/php/console exp:ini where site/SearchSettings/SearchEngine site
```

## Aufgaben

1. **Schreiben Sie den Testplan.** Zehn Suchanfragen für Ihre Vereinswebsite: Wörter aus Titeln, aus
   Zusammenfassungen, aus Rich Text, eine Wortgruppe in doppelten Anführungszeichen, ein Wort mit Akzent und ein Wort,
   das **nicht** gefunden werden soll (zum Beispiel aus einem internen Feld). Schreiben Sie zu jeder die Elemente auf,
   die Sie erwarten.
2. **Führen Sie ihn vorher aus.** Verwenden Sie die Schnellsuche und die erweiterte Suche (Klasse und
   Klassenattribut). Halten Sie die Ergebnisse fest.
3. **Lesen Sie die Markierungen.** Öffnen Sie unter **Setup > Klassen** Ihre Klasse aus Projekt 1. Notieren Sie,
   bei welchen Attributen *Ist durchsuchbar* steht.
4. **Ändern Sie eine Markierung.** Fügen Sie ein internes Attribut wie „Notizen für das Organisationsteam“
   (Textabschnitt) hinzu, falls Sie keines haben, füllen Sie es bei einer Veranstaltung mit einem eindeutigen Wort
   und machen Sie es **nicht** durchsuchbar. Drücken Sie am Ende **OK**, statt die Klasse im Bearbeitungsmodus zu
   lassen.
5. **Bauen Sie den Index neu auf.** Wie die Lektion der Trainer zeigt:

   ```bash
   php bin/php/updatesearchindex.php --clean -s <siteaccess> --allow-root-user
   ```

   `--clean` leert zuerst den gesamten Index, sodass die Suche immer weniger findet, bis der Lauf abgeschlossen ist;
   ohne diese Option bleiben die alten Einträge auffindbar, während jedes Objekt ersetzt wird
   ([Lektion 2.3: Den Suchindex neu aufbauen](../trainer/suche/modul-02-indizierung/den-suchindex-neu-aufbauen.md)).
   Lassen Sie `--allow-root-user` weg, wenn Sie nicht `root` sind.
6. **Führen Sie ihn nachher aus.** Wiederholen Sie die zehn Suchanfragen und halten Sie die Ergebnisse fest.
7. **Erklären Sie die Unterschiede.** Schreiben Sie zu jeder Suchanfrage, deren Ergebnis sich geändert hat oder nicht
   Ihrer Erwartung entsprach, warum.

## Abgabe

- Der Testplan mit erwarteten und tatsächlichen Ergebnissen, vorher und nachher.
- Die Ausgabezeilen des Neuaufbaus (die Anzahl der zu indizierenden Objekte).
- Ein Screenshot der Klasse mit der geänderten Markierung.
- Ein kurzer Bericht: welche Attribute auf Ihrer Website durchsuchbar sein sollten und warum.

## Bewertungsraster

Bewerten Sie die vier gemeinsamen Kriterien aus [Die eigene Arbeit bewerten](die-eigene-arbeit-bewerten.md) und diese
Prüfungen (7):

1. Zehn Suchanfragen mit erwarteten Ergebnissen wurden vor dem Testen aufgeschrieben.
2. Die verwendete Suchmaschine wurde mit `exp:ini where` gezeigt.
3. Eine Markierung wurde an einer Klasse geändert, die der lernenden Person gehört, auf ihrer eigenen Installation.
4. Der Index wurde neu aufgebaut, und der Bericht sagt, was `--clean` bewirkt.
5. Das versteckte Wort wird nach dem Neuaufbau nicht mehr gefunden.
6. Der Bericht erklärt eine Suche nach einer Wortgruppe und eine Suche mit Akzent.
7. Jedes unerwartete Ergebnis hat eine Erklärung oder eine offene Frage.

## Zusatzziele

- Lesen Sie über verzögerte Indizierung und den Cronjob `indexcontent`
  ([Lektion 2.2](../trainer/suche/modul-02-indizierung/verzoegerte-indizierung-und-der-indexcontent-cronjob.md)).
- Vergleichen Sie die eingebaute Suchmaschine auf einen Blick mit Solr und der Erweiterung `ezfind`
  ([Lektion: Solr und ezfind auf einen Blick](../trainer/suche/modul-01-wie-die-suche-funktioniert/solr-und-ezfind-auf-einen-blick.md)).
  Solr ist ein eigener Suchserver; für dieses Projekt wird er nicht gebraucht.
- Durchsuchen Sie die Hilfe selbst und vergleichen Sie, wie sie Seiten einordnet ([Suchen](../suchen.md#die-hilfeseiten)).

## Verwandte Seiten

- [Lernprojekte: Überblick](lernprojekte-ueberblick.md)
- [Suchen](../suchen.md)
- Der Kurs [Suche](../trainer/suche/README.md) der Trainer
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
