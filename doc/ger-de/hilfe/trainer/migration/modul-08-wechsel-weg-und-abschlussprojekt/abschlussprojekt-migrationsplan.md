---
title: "Lektion 8.2: Abschlussprojekt: ein vollständiger Migrationsplan"
audience: trainer, admin, developer, project-manager
category: training
order: 82
summary: Teams schreiben und präsentieren für ein gegebenes Szenario einen vollständigen Migrationsplan - Weg, Bestandsaufnahme, Umfang, Zuordnung, Werkzeuge, URLs, Probeläufe, Umstellung und Rückweg - und belegen einen Teil davon auf ihrer Schulungsinstallation; der Trainer bewertet ihn mit einem Bewertungsraster. 90 Minuten.
keywords: migration zu exponential, abschlussprojekt, migrationsplan, teamübung, präsentation, bewertungsraster, bewertung, szenario, machbarkeitsnachweis, kursabschluss, migrating to exponential, capstone, migration plan, team exercise, presentation, rubric, review, scenario, proof of concept, course wrap-up
type: help
---
# Lektion 8.2: Abschlussprojekt: ein vollständiger Migrationsplan

Das Abschlussprojekt führt den ganzen Kurs zusammen. Jedes Team erhält ein Szenario, schreibt den Migrationsplan, mit
dem ein echtes Projekt beginnen würde, belegt einen Teil davon auf einer Schulungsinstallation und präsentiert ihn.
Der Trainer bewertet ihn mit dem unten stehenden Bewertungsraster. Das Bewertungsraster ist das eigene Kursmaterial
des Trainers, keine offizielle Zertifizierung.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 8: Exponential verlassen und das Abschlussprojekt](README.md) |
| Dauer | 90 Minuten (10 Präsentation, 55 Teamarbeit, 25 Präsentationen und Bewertung) |
| Niveau | Experten |
| Zielgruppe | Gemischte Teams: ein Administrator, ein Entwickler und, wenn möglich, ein Projektleiter |
| Voraussetzungen | Module 1 bis 7, oder mindestens die Module 1 bis 3 und das Modul zum Szenario des Teams |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- für eine realistische Website einen Migrationsweg wählen und begründen;
- einen Plan schreiben, der jeden Abschnitt der gemeinsamen Checkliste abdeckt;
- einen riskanten Teil des Plans auf einer Schulungsinstallation belegen;
- den Plan präsentieren und Fragen zu Rückweg und Adressen beantworten.

## Hinweise für Trainer

### So präsentieren Sie es

1. **Auftrag** (5 Minuten). Teilen Sie die Szenarien (unten) aus, eines pro Team. Jedes Team schreibt seinen Plan in
   einem gemeinsamen Dokument mit den Überschriften aus der Übung.
2. **Regeln** (5 Minuten). Jede Aussage im Plan verweist auf eine Seite der Dokumentation. Wo die Dokumentation etwas
   nicht sagt (eine Dauer, ein Detail eines Herstellers), schreibt der Plan „zu messen“ oder „zu prüfen“, nie eine
   Vermutung. Der Nachweis läuft nur auf der Schulungsinstallation des Teams.
3. **Teamarbeit** (55 Minuten). Gehen Sie zwischen den Teams umher. Stellen Sie jedem Team die beiden wichtigsten
   Fragen: „Was tun Sie, wenn der letzte Import scheitert?“ und „Wohin führt `/old/address` am ersten Tag?“
4. **Präsentationen** (20 Minuten). Fünf Minuten pro Team; die anderen Teams stellen je eine Frage.
5. **Bewertung** (5 Minuten). Bewerten Sie mit dem Bewertungsraster; geben Sie jedem Team eine Stärke und einen
   Verbesserungspunkt.

### Szenarien

1. **Das alte Intranet.** Eine 4.7-Website des Legacy-Kernels unter PHP 5 und MySQL mit `latin1`-Tabellen, zwei
   Siteaccesses, drei eigenen Erweiterungen, 9 000 Objekten in einer Sprache, dem Datenbank-Cluster-Handler, 150
   Benutzern (einige mit `mysql`-Hashes). Ziel: Exponential 6.
2. **Das 5.x-Portal.** Eine Community-2014.11-Website mit dem Stack aus zwei Kerneln: Twig-Frontend, ein REST-Bundle
   für eine App, Bildvarianten in YAML, zwei Siteaccesses, die über den Host zugeordnet werden. Ziel: Exponential 6.
3. **Die Ibexa-Kampagnen-Website.** Ibexa OSS 4.6 mit Netgen Layouts, RichText, 20 Landing Pages aus dem Page
   Builder und einem Team, das Twig kennt. Ziel: zwischen Nexus und Exponential 6 entscheiden, dann planen.
4. **Das WordPress-Magazin.** 3 000 Beiträge in zwei Sprachen (ein Übersetzungs-Plugin), ein Page-Builder-Plugin auf
   40 Seiten, ein SEO-Plugin, 12 000 Anhänge, Permalinks `/%category%/%postname%/`, 30 Redakteure. Ziel:
   Exponential 6.

### Häufige Fragen

**Können wir ein anderes Szenario wählen?** Ja, wenn es eine reale Art von Website ist und das Team dafür die
Dokumentation zitieren kann; zum Beispiel die Anleitung für statisches HTML oder eine andere Systemanleitung des
Migrations-Hubs.

**Wie detailliert muss der Plan sein?** Jede Überschrift ausgefüllt; die Tiefe gehört in den riskantesten Teil.

### Stolperfallen

- **Pläne ohne Rückweg.** Das Bewertungsraster gibt keine Punkte für einen Plan, der nicht zurück kann.
- **Nachweise auf alpha oder einer anderen gemeinsam genutzten Website.** Nachweise laufen nur auf der eigenen
  Schulungsinstallation des Teams.
- **Erfundene Zahlen.** „Der Import dauert drei Stunden“ ohne Probelauf ist eine Vermutung; schreiben Sie „im
  Probelauf 1 zu messen“.

## Praktische Übung

In Teams, mit geöffnetem Migrations-Hub und dem Kapitel zu Ihrem Szenario.

1. Schreiben Sie die Überschrift **Weg** und die Anleitung, die die Entscheidungstabelle des
   [Migrations-Hubs](../../../../installation/migration/README.md#2-welcher-weg-passt) ergibt, mit Begründung.
2. Schreiben Sie **Bestandsaufnahme**: die Punkte aus [Abschnitt 1 der Checkliste](../../../../installation/migration/checkliste.md#1-bestandsaufnahme-wissen-was-sie-haben)
   und für Ihr Szenario die zusätzlichen Prüfungen seines Kapitels (14.3, 15.4, 16.4 oder die Systemanleitung).
3. Schreiben Sie **Umfang und Verantwortliche**: die Tabelle aus Abschnitt 2 der Checkliste mit einer Entscheidung und
   einem Verantwortlichen pro Zeile.
4. Schreiben Sie **Zuordnung**: eine Zuordnungstabelle für die beiden wichtigsten Inhaltstypen, dazu Benutzer und
   Passwörter.
5. Schreiben Sie **Werkzeuge**: das Werkzeug oder Skript für jeden Teil (die Tabelle aus Lektion 3.1).
6. Schreiben Sie **Adressen**: fünf Zeilen der Weiterleitungstabelle, einschließlich der schwierigsten.
7. Schreiben Sie **Probeläufe und Tests**: wie viele, was gemessen wird, welche Tests aus Abschnitt 7 der Checkliste.
8. Schreiben Sie **Umstellung und Rückweg**: das Runbook in zehn Zeilen, Auslöser, entscheidende Person,
   Entscheidungsfenster, aufbewahrte Dumps.
9. Wählen Sie den riskantesten Teil des Plans und **belegen Sie ihn** auf Ihrer Schulungsinstallation: zum Beispiel
   ein Import als Probelauf mit xrowextract, ein weiterleitender Alias oder eine Wildcard, getestet mit `curl`, die
   Vorabprüfungen oder eine INI-Übersetzung, geprüft mit `exp:ini get`. Notieren Sie die Befehle und das Ergebnis.
10. Bereiten Sie eine fünfminütige Präsentation vor: Weg, der riskanteste Teil und sein Nachweis, Rückweg.

## Erwartetes Ergebnis

- Ein Plan, in dem alle neun Überschriften ausgefüllt sind, jede Aussage eine Seite der Dokumentation zitiert und
  Unbekanntes als „zu messen“ oder „zu prüfen“ markiert ist.
- Ein Nachweis mit Befehlen und ihrer Ausgabe von der Schulungsinstallation des Teams.
- Eine Präsentation innerhalb von fünf Minuten.

Der Trainer bewertet jeden Plan mit diesem Bewertungsraster (drei Stufen pro Kriterium, höchstens 18 Punkte):

| Kriterium | 0: noch nicht | 1: teilweise | 2: vollständig |
|---|---|---|---|
| Weg | keiner oder unbegründet | genannt, schwache Begründung | genannt mit der Entscheidungstabelle und der Generation oder Quelle |
| Bestandsaufnahme und Umfang | Teile fehlen | die meisten Teile, einige Verantwortliche | jeder Punkt der Checkliste, jede Zeile mit einem Verantwortlichen |
| Zuordnung | keine Tabelle | einige Felder | beide Typen vollständig, Benutzer und Passwörter entschieden |
| Werkzeuge | keine genannt | ohne Begründung genannt | jeder Teil mit seinem Werkzeug oder Skript und Begründung |
| Adressen | keine | einige Zeilen | fünf Zeilen inkl. Query-Strings oder Mustern, Testmethode |
| Probelauf und Tests | keine | ein Lauf, wenige Tests | zwei oder mehr Läufe, Zählungen, Stichproben, Weiterleitungen |
| Umstellung und Rückweg | fehlt | Runbook oder Rückweg | beides, mit Auslösern, entscheidender Person und Dumps |
| Nachweis | keiner | versucht | ausgeführt, mit Befehlen und Ergebnis |
| Quellen | keine | einige | jede Aussage belegt; keine erfundenen Zahlen |

## Quiz

1. Was ist das Erste, was ein Team mit seinem Szenario tut?
2. Welche drei Dinge muss der Abschnitt zum Rückweg enthalten?
3. Wo darf ein Team seinen Nachweis ausführen?
   A. Auf alpha B. Auf einer beliebigen gemeinsam genutzten Website C. Nur auf seiner eigenen Schulungsinstallation
   D. Auf der Produktion
4. Wie behandelt ein Plan eine Dauer, die die Dokumentation nicht angibt?
5. Richtig oder falsch: Ein Team, das das Abschlussprojekt abschließt, besitzt eine offizielle
   Exponential-Zertifizierung.

<details>
<summary>Antworten</summary>

1. Den Weg mit der Entscheidungstabelle des
   [Migrations-Hubs](../../../../installation/migration/README.md#2-welcher-weg-passt) finden.
2. Auslöser, wer entscheidet, und das Entscheidungsfenster; dazu die aufbewahrten Dumps
   ([Checkliste, Abschnitt 9](../../../../installation/migration/checkliste.md#9-rückweg)).
3. **C.**
4. Als „zu messen“ in einem Probelauf ([Kapitel 14.15](../../../../installation/14-migration-von-4x.md#1415-wie-lange-es-dauert)).
5. **Falsch.** Das Bewertungsraster ist das eigene Kursmaterial des Trainers; das ist keine offizielle Zertifizierung; die Zertifizierungen des Partnerprogramms sind davon getrennt ([Zertifizierungen](../../../partner/zertifizierungen.md)).

</details>

## Weiterführende Informationen

- [Die gemeinsame Migrations-Checkliste](../../../../installation/migration/checkliste.md): der Rahmen jedes Plans.
- [Migration zu und von Exponential](../../../../installation/migration/README.md): der Hub und jede Anleitung.
- [Quizze und praktische Aufgaben](../../train-the-trainer/modul-06-pruefung-und-nachbereitung/quizze-und-praktische-aufgaben.md):
  wie man faire Prüfungen und Bewertungsraster erstellt.

## Verwandte Seiten

- [Modul 8: Exponential verlassen und das Abschlussprojekt](README.md)
- [Migration zu Exponential: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
