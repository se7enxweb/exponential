---
title: "Lektion 6.5: Abschlussprojekt, ein Migrationsprojekt"
audience: trainer, admin, developer, project-manager
category: training
order: 65
summary: Ein zweitägiges Abschlussprojekt für Migrationsteams, die Beispiel-Website einer erfundenen Organisation in eine Schulungsinstallation migrieren, von Bestandsaufnahme und Abbildung über zwei Generalproben mit Zeitmessung und Zahlen, automatisch getestete Weiterleitungen und einen Plan für Umstellung und Rückfall bis zu einer Go/No-Go-Empfehlung, bewertet mit einem Bewertungsraster aus sieben Kriterien. Zwei Tage plus Präsentationen.
keywords: prüfungen und praktische übungen, abschlussprojekt, migrationsprojekt, bestandsaufnahme, abbildung des inhaltsmodells, remote-id, generalprobe, zahlen, weiterleitungen, umstellung, rückfallplan, go/no-go, bewertungsraster, assessments and practical exercises, capstone, migration project, inventory, content model mapping, remote id, rehearsal, counts, redirects, cut-over, rollback, go no-go, rubric
type: help
---
# Lektion 6.5: Abschlussprojekt, ein Migrationsprojekt

Ein Team migriert die Beispiel-Website einer erfundenen Organisation in eine Schulungsinstallation, als wäre es ein
echtes Projekt: planen, importieren, zweimal proben, die Weiterleitungen testen und mit Nachweisen Go oder No-Go
empfehlen. Es erweitert die Planungsaufgabe aus
[Lektion 5.4](../modul-05-newsletter-und-migration/migration-praktische-aufgabe.md) zu einem vollständigen Projekt.

> **Hinweis**
> Dies ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung. Die Beispiel-Website wird vom Trainer für
> den Kurs erstellt und enthält keine personenbezogenen Daten; Benutzerkonten darin verwenden Adressen unter
> `example.invalid`.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 6: Abschlussprojekte](README.md) |
| Dauer | Zwei Tage: 15 Minuten Einweisung, etwa 10 Stunden Arbeit mit drei Zwischenständen, 15 Minuten Präsentation pro Team, 1 Stunde Bewertung |
| Niveau | Experten |
| Zielgruppe | Teams aus zwei bis vier Personen am Ende von Migration zu Exponential, mit Website-Administration oder Entwickler |
| Voraussetzungen | Diese Kurse; pro Team eine Schulungsinstallation und eine Shell; das Import-Werkzeug des Kurses installiert |

## Lernziele

Teams, die dieses Abschlussprojekt bestehen, haben gezeigt, dass sie Folgendes können:

- eine Migration von der Bestandsaufnahme bis zur Go/No-Go-Entscheidung durchführen;
- einen wiederholbaren Import bauen, der auf Remote-IDs beruht;
- mit Zahlen und Zeitmessung proben und sich zwischen den Generalproben verbessern;
- jede Weiterleitung automatisch testen;
- Umstellung und Rückfall mit Auslösern und einem Verantwortlichen planen.

## Hinweise für Trainer

### So führen Sie es durch

1. **Vorher**: eine Beispiel-Website, die größer ist als in Lektion 5.4 (40 bis 80 Seiten, zwei Inhaltstypen mit
   Datum und Autor, Bilder, einige Dokumente, eine Seite in einer zweiten Sprache, Adressen mit Query-Strings, zwei
   verwaiste Seiten), als statische Dateien oder als Export des Quellsystems, das Ihr Kurs behandelt hat
   ([WordPress](../../../../installation/migration/wordpress.md), [Drupal](../../../../installation/migration/drupal.md),
   [Joomla](../../../../installation/migration/joomla.md) oder
   [statisches HTML](../../../../installation/migration/statisches-html.md)).
2. **Zwischenstände**: Mittag von Tag 1 (Bestandsaufnahme und Abbildung), Ende von Tag 1 (erste Generalprobe mit
   Zahlen), Mittag von Tag 2 (zweite Generalprobe, Test der Weiterleitungen).
3. **Eine späte Änderung**: Ändern Sie am Vormittag von Tag 2 zwei Quellseiten und fügen Sie eine hinzu; das Team muss
   das mit einem Delta-Lauf erfassen.
4. **Präsentation**: zuerst die Empfehlung, dann die Nachweise.

### Stolperfallen

- **Keine Zahlen.** Eine Generalprobe ohne Zahlen beweist nichts; bestehen Sie auf dem Vergleich mit der
  Bestandsaufnahme.
- **Weiterleitungen stichprobenartig getestet.** Die Checkliste verlangt jede Zeile, automatisch.

## Praktische Übung

Die Aufgabenstellung, wie sie an die Teams ausgeteilt wird.

*Die Northpoint Historical Society (ein erfundener Geschichtsverein) zieht mit ihrer Website zu Exponential um. Sie
sind das Migrationsteam.*

1. Bestandsaufnahme: jede Adresse (per Crawl oder Dateiliste, nicht nur über das Menü), jeder Inhaltstyp mit Zahlen
   pro Sprache, Medien mit Größe, Benutzer, Integrationen, alles, was nicht umziehen soll.
2. Umfang und Verantwortliche: was umzieht, was nicht und wer in einem echten Projekt über jeden Teil entscheiden
   würde.
3. Abbildung: die Tabelle pro Typ und Feld, Platz im Baum, Bilder und Dokumente als Objekte, Übersetzungen als
   Übersetzungen eines Objekts, Muster der Remote-ID.
4. Import: ein wiederholbarer Import über die Remote-ID in eine Staging-Installation (Ihre Schulungsinstallation).
5. Generalprobe 1: Führen Sie ihn aus, messen Sie die Zeit, zählen Sie pro Typ und Sprache, vergleichen Sie mit der
   Bestandsaufnahme und notieren Sie jede Lücke.
6. Korrektur und Generalprobe 2: Verbessern Sie, führen Sie ihn erneut auf einem sauberen Ziel aus, messen Sie die
   Zeit und zählen Sie erneut.
7. Späte Änderung: Erfassen Sie die Änderungen des Trainers an Tag 2 mit einem Delta-Lauf ohne Duplikate.
8. Weiterleitungen: Erzeugen Sie die Weiterleitungstabelle aus dem Import; testen Sie jede Zeile automatisch (Status
   und Ziel) auf der Schulungsinstallation; behandeln Sie Query-String-Adressen in einer Webserver-Regel, die Sie
   aufschreiben.
9. Plan für Umstellung und Rückfall: Änderungsstopp, letzter Lauf, DNS-Schritt, Cache-Aufwärmen, Beobachten der Logs,
   Auslöser für den Rückfall, wer entscheidet, wie lange die alte Website schreibgeschützt bleibt.
10. Empfehlung: Go oder No-Go, mit den Nachweisen und den verbleibenden Risiken.

## Erwartetes Ergebnis

- Die Dokumente der Schritte 1 bis 3 und 9, zwei Protokolle der Generalproben mit Zeiten und Zahlen, ein Protokoll des
  Delta-Laufs, die Ausgabe des Weiterleitungstests und eine einseitige Empfehlung.
- In der Schulungsinstallation: die importierten Inhalte, keine Duplikate nach dem Delta-Lauf.

## Bewertungsraster

| Kriterium | 0: Nicht gezeigt | 1: Teilweise | 2: Größtenteils | 3: Vollständig | Nachweis |
|---|---|---|---|---|---|
| Bestandsaufnahme und Umfang | Fehlen | Nur Menüseiten | Vollständig, Verantwortliche fehlen | Vollständig einschließlich verwaister Seiten, Umfang mit Verantwortlichen | Dokumente |
| Abbildung | Fehlt | Nur Typen | Felder abgebildet, Medien oder Sprachen nicht entschieden | Vollständig, Medien als Objekte, Übersetzungen, Remote-IDs | Abbildungstabelle |
| Wiederholbarer Import (Pflicht) | Kein Import | Importiert, Duplikate beim erneuten Lauf | Keine Duplikate, Delta-Änderung übersehen | Erneute Läufe aktualisieren an Ort und Stelle, Delta erfasst | Installation, Protokolle |
| Generalproben | Keine | Eine, ohne Zahlen | Zwei, Zahlen ohne Vergleich | Zwei, mit Zeitmessung, gezählt, verglichen, verbessert | Protokolle der Generalproben |
| Weiterleitungen | Keine | Nur Tabelle | Stichprobenartig getestet | Jede Zeile automatisch getestet, Query-Strings behandelt | Testausgabe |
| Umstellung und Rückfall | Nichts | Nur Schritte | Rückfall ohne Auslöser oder Verantwortlichen | Auslöser, Verantwortlicher, schreibgeschützte alte Website, Zeiten aus den Generalproben | Plan |
| Empfehlung | Keine | Nur eine Meinung | Nachweise, Risiken fehlen | Go oder No-Go mit Nachweisen und verbleibenden Risiken | Empfehlung |

**Bestanden**: 14 von 21 Punkten oder mehr und **Wiederholbarer Import** mit 2 oder 3.

## Quiz

Fragen für die Nachbesprechung nach den Präsentationen.

1. Was hat sich zwischen Generalprobe 1 und 2 geändert, und wie haben die Zahlen es gezeigt?
2. Wie hat Ihr Import im Delta-Lauf Duplikate vermieden?
3. Welchen Auslöser für den Rückfall würden Sie vor der Umstellung mit dem Verein vereinbaren?
4. Was würde Sie zu einem No-Go bewegen?

<details>
<summary>Antworten</summary>

1. Eine offene Frage; gute Antworten vergleichen die Zahlen mit der Bestandsaufnahme
   ([Checkliste](../../../../installation/migration/checkliste.md#6-den-import-bauen-und-proben)).
2. Durch Abgleich über die Remote-IDs und Aktualisieren des Vorhandenen
   ([Checkliste](../../../../installation/migration/checkliste.md#3-das-inhaltsmodell-abbilden)).
3. Zum Beispiel ein festgelegter Anteil defekter Schlüsselseiten oder eine nicht funktionierende Anmeldung
   ([Checkliste](../../../../installation/migration/checkliste.md#9-rückweg)).
4. Eine offene Frage; unerklärte Abweichungen bei den Zahlen, fehlschlagende Weiterleitungen oder ein nicht getesteter
   Rückfall sind starke Gründe.

</details>

## Weiterführende Literatur

- [Die Migrations-Checkliste](../../../../installation/migration/checkliste.md)
- [Inhalte auf Exponential abbilden](../../../../installation/migration/inhaltsmodell-zuordnen.md)
- [Die Import- und Exportwerkzeuge von Exponential](../../../../installation/migration/exponential-tools.md)

## Verwandte Seiten

- [Modul 6: Abschlussprojekte](README.md)
- [Prüfungen und praktische Übungen: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
