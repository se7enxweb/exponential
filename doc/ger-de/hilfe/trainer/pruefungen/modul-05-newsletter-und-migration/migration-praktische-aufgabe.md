---
title: "Lektion 5.4: Migration zu Exponential, praktische Aufgabe"
audience: trainer, admin, developer
category: training
order: 54
summary: Eine praktische Planungsaufgabe von 90 Minuten für das Ende des Migrationskurses, aus einer kleinen Beispiel-Website des Trainers eine Bestandsaufnahme, eine Abbildungstabelle für das Inhaltsmodell, eine Weiterleitungstabelle und einen Plan für Generalprobe und Umstellung mit Zeitangaben und Auslösern für den Rückfall erstellen und drei Seiten mit Remote-IDs in eine Schulungsinstallation importieren, bewertet mit einem Bewertungsraster aus sechs Kriterien. 115 Minuten mit Bewertung.
keywords: prüfungen und praktische übungen, migration, praktische aufgabe, bewertungsraster, bestandsaufnahme, abbildung des inhaltsmodells, remote-id, weiterleitungstabelle, url-alias, url-wildcard, generalprobe, umstellung, rückfallplan, statisches html, assessments and practical exercises, practical task, rubric, inventory, content model mapping, remote id, redirect table, URL alias, URL wildcard, rehearsal, cut-over, rollback, static HTML
type: help
---
# Lektion 5.4: Migration zu Exponential, praktische Aufgabe

Eine Migration ist vor allem Planung. In dieser Aufgabe erhalten alle Teilnehmenden dieselbe kleine Beispiel-Website
und erstellen die Dokumente, die ein echtes Projekt braucht, bevor Import-Code geschrieben wird. Dann weisen sie die
wichtigste Idee, die Remote-ID, nach, indem sie drei Seiten zweimal importieren, ohne Duplikate zu erzeugen.

> **Hinweis**
> Dies ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung. Die Beispiel-Website wird vom Trainer für
> den Kurs erstellt und enthält keine personenbezogenen Daten. Importe gehen ausschließlich in die eigene
> Schulungsinstallation der Teilnehmenden.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 5: Die Kurse Newsletter und Migration prüfen](README.md) |
| Dauer | 115 Minuten (5 Einweisung, 90 Aufgabe, 20 Bewertung) |
| Niveau | Experten |
| Zielgruppe | Teilnehmende am Ende des Kurses Migration zu Exponential |
| Voraussetzungen | [Lektion 5.3](migration-schriftlicher-test.md); eine Schulungsinstallation und eine Shell pro Teilnehmer |

## Lernziele

Wer diese Aufgabe besteht, hat gezeigt, dass er Folgendes kann:

- eine Bestandsaufnahme einer Quell-Website machen, einschließlich ihrer Adressen;
- Quelltypen und -felder auf Klassen, Attribute, Datentypen und Orte abbilden;
- eine Weiterleitungstabelle mit einer Entscheidung pro Adresse erstellen;
- Generalproben, Umstellung und Rückfallplan mit Auslösern und Verantwortlichen planen;
- Inhalte wiederholbar über die Remote-ID importieren.

## Hinweise für Trainer

### So führen Sie sie durch

1. **Vorher**: Bereiten Sie eine Beispiel-Website aus 10 bis 20 statischen HTML-Seiten vor: eine Startseite,
   Nachrichten mit Datum, einige „Über uns“-Seiten, zwei Bilder pro Nachricht, eine Seite mit einer Query-String-Adresse
   in ihren Links (zum Beispiel `news.php?id=3`), eine Seite, auf die nichts verlinkt. Geben Sie allen Teilnehmenden
   eine Kopie als Ordner. Installieren Sie auf den Schulungsinstallationen das Import-Werkzeug, das Ihr Kurs behandelt
   hat ([Die Import- und Exportwerkzeuge von Exponential](../../../../installation/migration/exponential-tools.md)).
2. **Einweisung**: Teilen Sie die Schritte und das Bewertungsraster aus; 90 Minuten. Die Migrationskapitel dürfen
   geöffnet sein.
3. **Bewertung**: Lesen Sie die Dokumente; zählen Sie in der Schulungsinstallation die importierten Elemente und
   prüfen Sie ihre Remote-IDs.

### Häufige Fragen

**Dürfen die Teilnehmenden ihr eigenes Importskript schreiben?** Ja, wenn es über die Remote-ID abgleicht. Die
Checkliste sagt, zuerst vorhandene Werkzeuge zu verwenden und ein Skript nur für das zu schreiben, was diese nicht
können.

**Wie ausführlich muss der Plan sein?** Je eine Seite für Bestandsaufnahme, Abbildung, Weiterleitungen und Plan.
Echte Projekte haben mehr; das Bewertungsraster sucht nach den Entscheidungen, nicht nach der Länge.

### Stolperfallen

- **Die verwaiste Seite übersehen.** Eine Sitemap oder das Menü verfehlt sie; ein Crawl oder die Dateiliste findet
  sie. Sie ist absichtlich in der Beispiel-Website.
- **Query-String-Adressen.** Sie können keine URL-Aliase sein; wer sie auf Aliase abbildet, verliert einen Punkt.

## Praktische Übung

Die Aufgabe, wie sie an die Teilnehmenden ausgeteilt wird. Verwenden Sie den Ordner der Beispiel-Website und Ihre
eigene Schulungsinstallation.

1. **Bestandsaufnahme.** Listen Sie jede Seite der Beispiel-Website mit Adresse, Typ (Nachricht, Über uns,
   Startseite), Sprache und den verwendeten Bildern auf. Nehmen Sie auch Seiten auf, auf die kein Menü verlinkt.
   Zählen Sie die Seiten pro Typ.
2. **Umfang.** Entscheiden Sie, was umzieht und was nicht, und schreiben Sie wie in der Checkliste auf, wer in einem
   echten Projekt für welchen Teil verantwortlich wäre (Inhalte, Design, Adressen, Benutzer).
3. **Abbildung.** Schreiben Sie die Abbildungstabelle: Quelltyp, Quellfeld, Exponential-Klasse, Attributbezeichner,
   Datentyp, Umwandlung, Bemerkungen. Entscheiden Sie, wo jeder Typ im Inhaltsbaum landet und wie Bilder zu Objekten
   werden.
4. **Remote-IDs.** Legen Sie für jeden Typ das Muster der Remote-ID fest, zum Beispiel `static-news-<file name>`.
5. **Weiterleitungen.** Schreiben Sie die Weiterleitungstabelle: alte Adresse, Entscheidung (gleiche Adresse, 301 auf
   die neue Adresse oder 410/404), neue Adresse und welcher der drei Wege von Exponential (oder der Webserver) sie
   behandelt.
6. **Plan.** Schreiben Sie den Plan für Generalprobe, Umstellung und Rückfall: wie viele Generalproben, was nach jeder
   gezählt wird, der Änderungsstopp, der DNS-Schritt, die Auslöser für den Rückfall und wer entscheidet.
7. **Import.** Importieren Sie drei Nachrichtenseiten mit ihren Remote-IDs in Ihre Schulungsinstallation, mit dem
   Werkzeug aus dem Kurs, unterhalb eines Ordners `Migration test <your name>`.
8. **Wiederholen.** Ändern Sie ein Wort in einer Quellseite und führen Sie denselben Import erneut aus. Prüfen Sie,
   dass es weiterhin genau drei Elemente gibt und dass das geänderte eine neue Version hat.
9. **Zählen.** Notieren Sie die Zahlen aus Schritt 1 und aus Ihrem Import und was eine vollständige Generalprobe
   vergleichen würde.

## Erwartetes Ergebnis

- Vier kurze Dokumente: Bestandsaufnahme (mit der verwaisten Seite), Abbildungstabelle, Weiterleitungstabelle (die
  Query-String-Adresse im Webserver behandelt), Plan mit Auslösern für den Rückfall und Verantwortlichen.
- In der Schulungsinstallation: drei Nachrichten mit Remote-IDs nach dem Muster; nach Schritt 8 weiterhin drei, eine
  davon mit zwei Versionen.
- Der Trainer vergleicht die Bestandsaufnahme mit der Dateiliste der Beispiel-Website und prüft die Remote-IDs der
  importierten Elemente.

## Bewertungsraster

| Kriterium | 0: Nicht gezeigt | 1: Teilweise | 2: Größtenteils | 3: Vollständig | Nachweis |
|---|---|---|---|---|---|
| Bestandsaufnahme | Fehlt | Nur Menüseiten | Alle Seiten, Bilder oder Zahlen fehlen | Alle Seiten einschließlich der verwaisten, Bilder, Zahlen pro Typ | Die Bestandsaufnahme |
| Abbildung | Fehlt | Nur Typen | Felder abgebildet, Umwandlungen oder Bilder nicht entschieden | Jedes Feld abgebildet oder bewusst weggelassen, Bilder als Objekte, Platz im Baum | Die Abbildungstabelle |
| Weiterleitungen | Fehlen | Einige Adressen | Alle, Query-String auf einen Alias abgebildet | Alle, jeweils eine Entscheidung, Query-String im Webserver | Die Weiterleitungstabelle |
| Plan und Rückfall | Fehlt | Schritte ohne Zeitangaben | Generalproben und Umstellung, kein Auslöser für den Rückfall | Zwei oder mehr Generalproben mit Zeitmessung, Zahlen, Änderungsstopp, Auslöser für den Rückfall und Verantwortlicher | Der Plan |
| Wiederholbarer Import (Pflicht) | Kein Import | Ohne Remote-IDs importiert | Remote-IDs, erneuter Lauf hat eines dupliziert | Erneuter Lauf hat an Ort und Stelle aktualisiert, drei Elemente, eines mit neuer Version | Die Schulungsinstallation |
| Umfang und Umgang mit Daten | Nicht behandelt | Nur Umfang | Verantwortliche genannt, personenbezogene Daten nicht bedacht | Umfang, Verantwortliche und „nur umziehen, was Sie brauchen“ festgehalten | Die Notiz zum Umfang |

**Bestanden**: 12 von 18 Punkten oder mehr und **Wiederholbarer Import** mit 2 oder 3.

## Quiz

Fragen für die Nachbesprechung nach der Bewertung.

1. Warum war die verwaiste Seite wichtig?
2. Was wäre in Schritt 8 ohne Remote-IDs geschehen?
3. Warum bleiben Weiterleitungen mindestens ein Jahr lang bestehen?
4. Welches einzelne Dokument würden Sie einem Kunden zuerst zeigen, und warum?

<details>
<summary>Antworten</summary>

1. Eine Sitemap oder ein Menü allein verfehlt verwaiste Seiten; ihre Adressen können trotzdem noch Links und Besucher
   haben ([Checkliste](../../../../installation/migration/checkliste.md#1-bestandsaufnahme-wissen-was-sie-haben)).
2. Duplikate: Der Import hätte neue Elemente angelegt, statt zu aktualisieren
   ([Checkliste](../../../../installation/migration/checkliste.md#11-wiederkehrende-risiken)).
3. Suchmaschinen und alte Links brauchen sie so lange
   ([Checkliste](../../../../installation/migration/checkliste.md#4-urls-und-weiterleitungen)).
4. Eine offene Frage; eine gute Antwort ist der Umfang mit den Verantwortlichen, weil er alles andere bestimmt
   ([Checkliste](../../../../installation/migration/checkliste.md#2-umfang-und-verantwortliche-für-jeden-teil-festlegen)).

</details>

## Weiterführende Literatur

- [Statisches HTML migrieren](../../../../installation/migration/statisches-html.md): der Leitfaden für die Beispiel-Website.
- [Die Import- und Exportwerkzeuge von Exponential](../../../../installation/migration/exponential-tools.md): welches
  Werkzeug für welche Aufgabe.
- [Die Migrations-Checkliste](../../../../installation/migration/checkliste.md): jeder Schritt des Plans.

## Verwandte Seiten

- [Lektion 5.3: Migration zu Exponential, schriftlicher Test](migration-schriftlicher-test.md)
- [Modul 5: Die Kurse Newsletter und Migration prüfen](README.md)
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
