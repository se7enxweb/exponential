---
title: "Lektion 3.3: Ein eigenes Importskript"
audience: trainer, developer
category: training
order: 33
summary: Entwickler lesen das Gerüst eines eigenen Importskripts aus der Dokumentation, führen es mit dem Exponential-Bootstrap auf einer Schulungsinstallation aus und weisen nach, dass Remote-IDs es wiederholbar machen; sie lernen die fromString()-Formate und die Reihenfolge der Abhängigkeiten. 60 Minuten.
keywords: migration zu exponential, importskript, remote-id, reihenfolge der abhängigkeiten, migrating to exponential, import script, ezexec, eZContentFunctions, createAndPublishObject, updateAndPublishObject, fetchByRemoteID, fromString, eZSimplifiedXMLInputParser, storePath, remote id, dependency order
type: help
---
# Lektion 3.3: Ein eigenes Importskript

Wenn kein Werkzeug die Quelle liest, schreibt ein Entwickler ein PHP-Skript, das den Export der Quelle liest und über
die API des Kernels Objekte anlegt. Die Dokumentation enthält ein Gerüst, das nur Kernel-Funktionen verwendet, die es
in 6.0.15 gibt. Diese Lektion geht es durch, und die Entwickler führen eine minimale Fassung auf ihrer
Schulungsinstallation aus.

> **Hinweis**
> Führen Sie Importskripte nur auf einer Schulungsinstallation oder einer Probelauf-Kopie aus. Das Gerüst ist ein
> Ausgangspunkt, kein fertiger Importer: Das Lesen der Quelle, die Zuordnung und die Fehlerbehandlung sind Ihre
> Aufgabe.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 3: Import- und Exportwerkzeuge](README.md) |
| Dauer | 60 Minuten (20 Präsentation, 40 Übung) |
| Niveau | Experten |
| Zielgruppe | PHP-Entwickler |
| Voraussetzungen | [Lektion 3.2](importieren-mit-xrowextract.md); PHP-Kenntnisse; Shell-Zugang zu einer Schulungsinstallation und die Knoten-ID eines Kursordners |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- jeden der drei Teile des Gerüsts erklären: anlegen oder über die Remote-ID finden, Rich Text, alte Adresse;
- ein Skript mit dem Exponential-Bootstrap ausführen;
- zeigen, dass ein zweiter Lauf des Skripts aktualisiert, statt zu duplizieren;
- das `fromString()`-Format von fünf gängigen Datentypen angeben;
- die Reihenfolge der Abhängigkeiten eines Imports nennen und sagen, was darauf folgt (Suchindex, Caches).

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Die Import- und Exportwerkzeuge von Exponential, Abschnitt 4](../../../../installation/migration/exponential-tools.md#4-ein-eigenes-importskript-das-grundgerüst).

1. **Die Schleife** (6 Minuten). Zeigen Sie das Gerüst am Beamer. Für jedes Element: eine aus der Quell-ID gebildete
   Remote-ID, `eZContentObject::fetchByRemoteID()`, dann `eZContentFunctions::createAndPublishObject()`, falls es
   noch nicht existiert, mit `false` für keine Benachrichtigungen bei importierten Inhalten.
2. **Rich Text** (4 Minuten). HTML wird auf die vereinfachte Eingabe reduziert (`p`, `strong`, `em`, `a href`,
   Überschriften, Listen, Tabellen) und mit `eZSimplifiedXMLInputParser` in XmlText umgewandelt, wie es der RSS-Import
   tut.
3. **Die alte Adresse** (3 Minuten). `eZURLAliasML::storePath()` speichert einen weiterleitenden Alias für den Knoten.
4. **`fromString()`-Formate** (4 Minuten). Zeigen Sie die Tabelle: Datumsangaben als Unix-Zeitstempel, `ezimage` als
   `path|alternative text`, `ezkeyword` durch Kommas getrennt, `ezobjectrelationlist` IDs durch `-` getrennt.
5. **Bewährte Vorgehensweise** (3 Minuten). Reihenfolge der Abhängigkeiten (Medien, Inhalte, Beziehungen in einem
   zweiten Durchlauf), ein eigener Import-Benutzer, den Suchindex neu aufbauen, den Inhalts-Cache leeren, in eine
   Datei protokollieren, jeden Lauf wiederholbar machen.

### Häufige Fragen

**Wie führe ich das Skript aus?** Die Werkzeugseite verwendet `./console exp:ezexec path/to/your_import.php`; in einer
Installation ohne den Wrapper `./console` führt `php bin/php/ezexec.php path/to/your_import.php` denselben Bootstrap
aus.

**Warum `false` als zweites Argument?** Keine Benachrichtigungen für importierte Inhalte; sonst bekäme jeder Abonnent
pro importiertem Element eine Mail.

**Kann ich das ursprüngliche Veröffentlichungsdatum setzen?** Die Zuordnungsseite, Abschnitt 12: `published` und
`modified` des Objekts können vom Importskript gesetzt und gespeichert werden; prüfen Sie das Ergebnis in der
Administration.

### Stolperfallen

- **Keine Remote-ID.** Ohne sie dupliziert der zweite Lauf alles. Die Übung beweist es von der anderen Seite her.
- **Falscher Sprachcode.** Das Gerüst verwendet `eng-GB`; die Schulungsinstallation verwendet vielleicht einen
  anderen. Prüfen Sie zuerst **Setup > Sprachen**.
- **Beziehungen im ersten Durchlauf.** Das Ziel existiert vielleicht noch nicht; lösen Sie sie in einem zweiten
  Durchlauf über die Remote-ID auf.

## Praktische Übung

Für Entwickler, auf ihrer eigenen Schulungsinstallation. Das Skript legt drei Ordner unterhalb Ihres Kursordners an.

1. Öffnen Sie in der Administration **Setup > Sprachen** und notieren Sie den Sprachcode der Hauptsprache (zum
   Beispiel `eng-US`).
2. Notieren Sie die Knoten-ID Ihres Kursordners; im Folgenden heißt sie `<folder>`.
3. Legen Sie `var/tmp/import/import_training.php` mit dem Gerüst aus
   [Abschnitt 4 der Werkzeugseite](../../../../installation/migration/exponential-tools.md#4-ein-eigenes-importskript-das-grundgerüst)
   an.
4. Ändern Sie `$parentNodeId` in `<folder>`, `$classIdentifier` in `folder` und `$language` in Ihren Sprachcode.
5. Ersetzen Sie das leere `$items` durch drei fest eingetragene Elemente, jedes mit einer `source_id` (`1`, `2`, `3`),
   einem `title` und einem `html` aus einem Absatz; lassen Sie `old_path` leer.
6. Das Namensattribut eines Ordners heißt `name`, nicht `title`: Ändern Sie das Attribut-Array in
   `array( 'name' => $item['title'] )` und das Rich-Text-Attribut von `body` in `short_description` (prüfen Sie beide
   Bezeichner auf der Seite der Klasse **Folder** unter **Setup > Klassen**).
7. Führen Sie es mit dem Exponential-Bootstrap aus:

   ```bash
   php bin/php/ezexec.php var/tmp/import/import_training.php
   ```

8. Öffnen Sie Ihren Kursordner in der Administration und zählen Sie die neuen Ordner.
9. Führen Sie das Skript unverändert ein zweites Mal aus. Zählen Sie erneut.
10. Ändern Sie den Titel von Element 2 im Skript, führen Sie es ein drittes Mal aus und sehen Sie sich die Versionen
    dieses Ordners an.

## Erwartetes Ergebnis

- Nach Schritt 7 hat das Skript `ok src-1`, `ok src-2`, `ok src-3` ausgegeben, und unterhalb des Kursordners
  erscheinen drei Ordner.
- Nach Schritt 9 sind es immer noch drei Ordner: Das Skript hat jeden über seine Remote-ID gefunden und aktualisiert.
- Nach Schritt 10 hat Ordner 2 den neuen Namen und eine neuere Version.
- Der Trainer prüft eine Installation in der Administration und bittet die Person zu erklären, warum Schritt 9 keine
  Duplikate erzeugt hat.

## Quiz

1. Welcher Kernel-Aufruf findet ein Objekt, das ein früherer Lauf bereits angelegt hat?
2. In welchem Format liest `ezdatetime` einen Wert über `fromString()`?
   A. ISO-8601-Text B. Ein Unix-Zeitstempel C. Tag/Monat/Jahr D. Ein PHP-`DateTime`-Objekt
3. Warum ist das zweite Argument von `createAndPublishObject()` im Gerüst auf `false` gesetzt?
4. Nennen Sie die Importreihenfolge, die die Werkzeugseite empfiehlt.
5. Welche zwei Dinge tun Sie, nachdem der Import abgeschlossen ist?

<details>
<summary>Antworten</summary>

1. `eZContentObject::fetchByRemoteID()`
   ([Werkzeugseite, Abschnitt 4](../../../../installation/migration/exponential-tools.md#4-ein-eigenes-importskript-das-grundgerüst)).
2. **B** (die `fromString()`-Tabelle auf der Werkzeugseite, Abschnitt 4).
3. Keine Benachrichtigungen für importierte Inhalte (der Kommentar im Gerüst).
4. Zuerst Medien, dann Inhalte, die sich darauf beziehen, dann Inhalte, die sich auf Inhalte beziehen; Beziehungen
   werden in einem zweiten Durchlauf über die Remote-ID aufgelöst (Werkzeugseite, Abschnitt 4, bewährte
   Vorgehensweise).
5. Den Suchindex neu aufbauen und den Inhalts-Cache leeren (Werkzeugseite, Abschnitt 4, bewährte Vorgehensweise).

</details>

## Weiterführende Informationen

- [Die Import- und Exportwerkzeuge von Exponential, Abschnitt 4](../../../../installation/migration/exponential-tools.md#4-ein-eigenes-importskript-das-grundgerüst):
  das Gerüst und die `fromString()`-Tabelle.
- [Inhalte auf Exponential abbilden](../../../../installation/migration/inhaltsmodell-zuordnen.md): worauf das Skript
  abbildet.
- [Kapitel 17.6: PHP-API](../../../../installation/17-migrationsreferenz.md#176-php-api-legacy-klassen-und-dienste-der-public-api):
  die Legacy-Klassen neben ihren Entsprechungen in der Plattform.

## Verwandte Seiten

- [Modul 3: Import- und Exportwerkzeuge](README.md)
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
