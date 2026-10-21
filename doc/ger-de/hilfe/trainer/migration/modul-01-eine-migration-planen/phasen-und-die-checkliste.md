---
title: "Lektion 1.3: Die Phasen einer Migration und die Checkliste"
audience: trainer, admin, developer, project-manager
category: training
order: 13
summary: Die Teilnehmenden gliedern eine Migration in die Phasen, die alle Wege gemeinsam haben (Bestandsaufnahme, Umfang, Zuordnung, URLs, Metadaten, Import, Probelauf, Test, Umstellung, Rückweg, nach dem Go-live), machen aus der gemeinsamen Checkliste einen Projektplan und benennen die wiederkehrenden Risiken. 45 Minuten.
keywords: migration zu exponential, migrationsphasen, projektplan, checkliste, risiken, tracker, probelauf, umstellung, go-live, zeitplan, migrating to exponential, migration phases, project plan, checklist, risks, rehearsal, cut-over, timeline
type: help
---
# Lektion 1.3: Die Phasen einer Migration und die Checkliste

Alle Migrationsanleitungen der Dokumentation passen in einen Rahmen: die gemeinsame Migrations-Checkliste. Diese
Lektion macht aus diesem Rahmen einen Projektplan, den die Teilnehmenden in ihren eigenen Tracker übernehmen können,
und zeigt die Risiken, die in fast jedem Projekt wiederkehren.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 1: Eine Migration planen](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Projektleiter, Administratoren und Entwickler |
| Voraussetzungen | [Lektion 1.2](bestandsaufnahme-und-umfang.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die elf Abschnitte der gemeinsamen Migrations-Checkliste in ihrer Reihenfolge benennen;
- erklären, welche Phasen parallel laufen können und welche warten müssen;
- die Checkliste in einen Plan mit Phasen, Verantwortlichen und Abnahmekriterien („erledigt, wenn“) umwandeln;
- drei wiederkehrenden Risiken ihre Gegenmaßnahmen zuordnen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Die gemeinsame Migrations-Checkliste](../../../../installation/migration/checkliste.md) und die Pläne in
[Kapitel 15.4](../../../../installation/15-migration-von-5x-legacy.md#154-planung-und-zeitplan) und
[Kapitel 16.10](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#1610-ein-plan-und-seine-phasen).

1. **Der Rahmen** (5 Minuten). Zeigen Sie das Inhaltsverzeichnis der Checkliste: Bestandsaufnahme, Umfang, Zuordnung,
   URLs und Weiterleitungen, Metadaten für Suchmaschinen, Import aufbauen und proben, Test, Umstellung, Rückweg, nach
   dem Go-live, Risiken. Jede Anleitung zu einem Weg ergänzt, was für ihre Quelle spezifisch ist.
2. **„Erledigt, wenn“** (5 Minuten). Zeigen Sie die Zeitplantabelle aus Kapitel 15.4. Jede Phase hat eine Spalte
   „Erledigt, wenn“. Ein Plan ohne diese Spalte lässt sich nicht verfolgen.
3. **Parallele Stränge** (4 Minuten). Kapitel 14.2 nennt drei Stränge, die sich erst am Ende treffen: die Datenbank,
   die Dateien und Einstellungen sowie der Code. Verschiedene Personen können gleichzeitig daran arbeiten.
4. **Wiederkehrende Risiken** (6 Minuten). Abschnitt 11 der Checkliste: verlorene Adressen, kaputter Rich Text,
   Duplikate nach wiederholten Läufen, ausgesperrte Benutzer, verlorene späte Änderungen, unterschätzte Templates,
   ohne Not umgezogene personenbezogene Daten. Fragen Sie in die Runde, welche davon die Teilnehmenden schon erlebt
   haben.

### Häufige Fragen

**Wie lange dauert eine Migration?** Die Dokumentation nennt keine feste Zahl; sie sagt, dass gemessen werden soll.
Kapitel 14.15 listet auf, womit jeder Schritt wächst, und Kapitel 16.10 nennt die drei Zahlen, die die Dauer
bestimmen. Der Probelauf liefert die tatsächliche Zeit (Lektion 1.4).

**Können die Templates warten, bis die Inhalte drin sind?** Beginnen Sie früh damit, auf Basis der Inhalte des ersten
Probelaufs (Checkliste, Abschnitt 11: „unterschätzte Templates“).

**Wo führen wir die Checkliste?** Die Checkliste sagt es selbst: Übernehmen Sie sie in den Tracker Ihres Projekts und
haken Sie sie ab.

### Stolperfallen

- **Eine riesige Phase namens „Migration“.** Zerlegen Sie sie; jede Phase braucht eine Zeile „erledigt, wenn“.
- **Nur am Ende testen.** Zählungen und Stichproben gehören zu jedem Probelauf, nicht nur zum letzten Lauf.
- **Keine Phase nach dem Go-live.** Der 404-Bericht, Ranking und Besucherzahlen sowie das Abschalten des alten Systems
  gehören zum Projekt (Checkliste, Abschnitt 10).

## Praktische Übung

Eine Planungsübung in Dreiergruppen, auf Papier oder in einem gemeinsamen Dokument. Verwenden Sie das Szenario:
*eine Website mit 2 000 Artikeln in zwei Sprachen, 300 Bildern, 40 Redakteurskonten und einem Kontaktformular, die
von einer WordPress-Installation zu Exponential 6 umzieht*.

1. Öffnen Sie [Die gemeinsame Migrations-Checkliste](../../../../installation/migration/checkliste.md).
2. Legen Sie eine Tabelle mit den Spalten **Phase**, **Was**, **Verantwortlich**, **Erledigt, wenn** an.
3. Tragen Sie der Reihe nach eine Zeile pro Abschnitt 1 bis 10 der Checkliste ein.
4. Schreiben Sie für die Phase „Import aufbauen und proben“ mindestens zwei Kriterien „erledigt, wenn“, die Sie
   Abschnitt 6 entnehmen.
5. Wählen Sie für die Phase „Test“ vier Tests aus der Tabelle in Abschnitt 7 und schreiben Sie auf, wie Sie jeden für
   dieses Szenario durchführen würden.
6. Markieren Sie, welche Zeilen parallel zur Importarbeit laufen können.
7. Wählen Sie aus Abschnitt 11 die drei Risiken, die Sie für dieses Szenario am wahrscheinlichsten halten, und
   schreiben Sie jeweils die Gegenmaßnahme auf.
8. Stellen Sie Ihren Plan einer anderen Gruppe in drei Minuten vor.

## Erwartetes Ergebnis

- Eine Tabelle mit zehn Zeilen, jede mit einem Verantwortlichen und einem messbaren Kriterium „erledigt, wenn“.
- Die Importphase nennt wiederholbare Läufe, die über die Remote-ID abgleichen, und Zählungen, die mit der
  Bestandsaufnahme verglichen werden.
- Die Testzeile nennt konkrete Prüfungen, zum Beispiel die Anzahlen pro Typ und Sprache, die 50 meistbesuchten Seiten
  im direkten Vergleich und jede Weiterleitung automatisch getestet.
- Mindestens die Risiken „ausgesperrte Benutzer“ (WordPress-Hashes) und „verlorene Adressen“ sind unter den drei
  gewählten.
- Der Trainer prüft einen Plan pro Gruppe anhand der Abschnitte der Checkliste.

## Quiz

1. Bringen Sie diese in die richtige Reihenfolge: Test, Inhaltsmodell zuordnen, Umstellung, URLs und Weiterleitungen.
2. Welches sind die drei Stränge aus Kapitel 14.2, an denen parallel gearbeitet werden kann?
3. Welchem wiederkehrenden Risiko wirken „Remote-IDs und Aktualisieren bei Übereinstimmung“ entgegen?
   A. Verlorene Adressen B. Duplikate nach wiederholten Läufen C. Ausgesperrte Benutzer D. Kaputter Rich Text
4. Nennen Sie zwei Dinge, die zur Phase nach dem Go-live gehören.
5. Richtig oder falsch: Die Dokumentation gibt an, wie viele Tage eine Migration von 2 000 Artikeln dauert.

<details>
<summary>Antworten</summary>

1. Inhaltsmodell zuordnen, URLs und Weiterleitungen, Test, Umstellung
   ([Checkliste, Inhalt](../../../../installation/migration/checkliste.md#inhalt)).
2. Die Datenbank; die Dateien und Einstellungen; der Code
   ([Kapitel 14.2](../../../../installation/14-migration-von-4x.md#142-die-migration-auf-einer-staging-kopie-planen)).
3. **B** ([Checkliste, Abschnitt 11](../../../../installation/migration/checkliste.md#11-wiederkehrende-risiken)).
4. Zwei beliebige aus: der 404-Bericht, Suchmaschinen-Ranking und Besucherzahlen, Archivieren und Abschalten des alten
   Systems, Dokumentieren der Zuordnung ([Checkliste, Abschnitt 10](../../../../installation/migration/checkliste.md#10-nach-dem-go-live)).
5. **Falsch.** Sie sagt, dass im Probelauf gemessen werden soll
   ([Kapitel 14.15](../../../../installation/14-migration-von-4x.md#1415-wie-lange-es-dauert)).

</details>

## Weiterführende Informationen

- [Die gemeinsame Migrations-Checkliste](../../../../installation/migration/checkliste.md): jede Phase als Checkliste.
- [Kapitel 15.4: Plan und Zeitplan](../../../../installation/15-migration-von-5x-legacy.md#154-planung-und-zeitplan): ein Plan
  mit Kriterien „erledigt, wenn“.
- [Kapitel 16.10: Ein Plan und seine Phasen](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#1610-ein-plan-und-seine-phasen):
  die Phasen für die beiden Plattformwege.

## Verwandte Seiten

- [Modul 1: Eine Migration planen](README.md)
- [Migration zu Exponential: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
