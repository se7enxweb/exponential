---
title: "Lektion 2.1: Klassen, Datentypen und der Inhaltsbaum"
audience: trainer, admin, developer
category: training
order: 21
summary: Die Teilnehmenden ordnen die Inhaltstypen eines beliebigen Quellsystems Exponential-Klassen, Attributen und Datentypen zu, platzieren Elemente mit Remote-IDs im Inhaltsbaum und schreiben die Zuordnungstabelle vor jedem Importcode. 60 Minuten.
keywords: migration zu exponential, inhaltsmodell zuordnen, klasse, attribut, datentyp, ort, hauptort, remote-id, taxonomie, übersetzungen, zuordnungstabelle, migrating to exponential, content model mapping, class, attribute, datatype, ezstring, ezxmltext, location, main location, remote id, taxonomy, eztags, translations, mapping table
type: help
---
# Lektion 2.1: Klassen, Datentypen und der Inhaltsbaum

Ganz gleich, welche Quelle: Eine Migration endet in denselben Bausteinen, nämlich Klassen mit Attributen, Objekten mit
Versionen und Übersetzungen sowie Orten in einem Baum. Diese Lektion vermittelt die Zuordnungstabelle, die die
gemeinsame Checkliste verlangt, bevor irgendein Importcode geschrieben wird.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 2: Inhalte auf Exponential abbilden](README.md) |
| Dauer | 60 Minuten (25 Präsentation, 35 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler und Administratoren, die den Import entwerfen |
| Voraussetzungen | [Modul 1](../modul-01-eine-migration-planen/README.md); ein Administratorkonto auf einer Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Begriffe anderer Systeme (Inhaltstyp, Feld, Eintrag, Kategorie) in Exponential-Begriffe übersetzen;
- anhand der Zuordnungstabelle für jedes Quellfeld einen Datentyp wählen;
- für die Taxonomie einer Quelle zwischen Orten, Schlüsselwörtern und Tags entscheiden;
- erklären, warum jedes importierte Objekt eine Remote-ID erhält, die seine Quelle benennt;
- eine Zuordnungstabelle mit den Spalten schreiben, die die Checkliste verlangt.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Inhalte auf Exponential abbilden](../../../../installation/migration/inhaltsmodell-zuordnen.md), Abschnitte 1 bis 4
und 8.

1. **Das Vokabular** (5 Minuten). Zeigen Sie die Tabelle „Die Bausteine auf einen Blick“: Aus dem Inhaltstyp wird die
   **Inhaltsklasse**, aus dem Feld ein **Attribut** eines **Datentyps**, aus dem Eintrag ein **Inhaltsobjekt**, aus
   dem Kategoriebaum werden **Orte** und so weiter.
2. **Datentypen** (7 Minuten). Zeigen Sie die Tabelle aus Abschnitt 2. Betonen Sie drei Zeilen: Rich Text
   (`ezxmltext`, HTML wird umgewandelt; Skripte, Styles und Inline-Event-Handler bleiben absichtlich nicht erhalten),
   Verweise (`ezobjectrelationlist`, über die Remote-ID aufgelöst, nachdem alle Elemente existieren) und sich
   wiederholende Gruppen (kein verschachtelter Repeater-Datentyp: Kindobjekte oder `ezmatrix`).
3. **Die Klasse in der Administration** (4 Minuten). Öffnen Sie am Beamer **Setup > Klassen**, die Gruppe
   **Content** und dann **Article**. Zeigen Sie **Bezeichner**, **Objekt Namensschema**, **URL Alias Namensschema**
   und die Liste der **Attribute** mit ihren Bezeichnern und Datentypen (zum Beispiel **Textzeile**).
4. **Objekte und der Baum** (5 Minuten). Ein Quellelement wird zu einem Objekt mit einer Remote-ID (`wp-post-1234`);
   Übersetzungen sind Versionen desselben Objekts; jedes Objekt hat einen Hauptort, weitere Kategorien können
   zusätzliche Orte sein. Die Sortierreihenfolge pro Elternknoten bildet die Menüreihenfolge der Quelle ab.
5. **Taxonomie** (4 Minuten). Abschnitt 8: Ein Kategoriebaum, der zugleich die Navigation ist, wird zu Ordnern; flache
   Tags zu `ezkeyword` oder `eztags`; kontrollierte Vokabulare zu `eztags`.

### Häufige Fragen

**Eine Klasse pro Quelltyp oder eine Klasse für alles?** Die Checkliste sagt: pro Typ entscheiden. Eine Klasse pro
Quelltyp mit eigenen Feldern ist die Standardempfehlung der Zuordnungsseite.

**Sollen wir alte Revisionen importieren?** Die Zuordnungsseite sagt, dass sich das selten lohnt; archivieren Sie sie
stattdessen.

**Was bedeutet „Always available“?** Ein Objekt kann in Sprachen angezeigt werden, in die es nicht übersetzt ist;
entscheiden Sie das pro Klasse (Zuordnungsseite, Abschnitt 3).

**Warum kurze Bezeichner?** Templates, Einstellungen und Importe verweisen darauf; sie sollten kurz und stabil sein.

### Stolperfallen

- **Zuerst im Code zuordnen.** Schreiben Sie zuerst die Tabelle; Code, der vorher geschrieben wird, wird später neu
  geschrieben.
- **Beziehungen im ersten Durchlauf.** Verknüpfte Elemente existieren vielleicht noch nicht. Importieren Sie in der
  Reihenfolge der Abhängigkeiten und lösen Sie Beziehungen in einem zweiten Durchlauf über die Remote-ID auf.
- **Während der Übung eine Klasse auf einer Live-Website ändern.** Die Teilnehmenden sehen sich in dieser Lektion
  Klassen nur an; sie klicken nicht auf **Bearbeiten**.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation und ein Administratorkonto. Sie sehen sich nur um; ändern Sie keine
Klasse.

1. Öffnen Sie in der Administration **Setup > Klassen**.
2. Öffnen Sie die Klassengruppe **Content** und dann die Klasse **Article** (bei einem Standard-Seitenpaket; nehmen
   Sie eine ähnliche Klasse, wenn Ihre Installation keine Article-Klasse hat).
3. Notieren Sie den **Bezeichner**, das **Objekt Namensschema** und das **URL Alias Namensschema**.
4. Schreiben Sie jedes Attribut auf: seinen Namen, seinen Bezeichner und seinen Datentyp, wie die Seite ihn anzeigt.
5. Übernehmen Sie die Zuordnungstabelle aus [der Checkliste, Abschnitt 3](../../../../installation/migration/checkliste.md#3-das-inhaltsmodell-abbilden)
   in Ihre Notizen, mit den Spalten **Quelltyp**, **Quellfeld**, **Exponential-Klasse**, **Attribut
   (Bezeichner)**, **Datentyp**, **Umwandlung**, **Anmerkungen**.
6. Füllen Sie sie für einen WordPress-Beitrag mit diesen Feldern aus: `post_title`, `post_content`, `post_excerpt`,
   Beitragsbild, Kategorien, Tags, Autor, SEO-Titel und -Beschreibung. Verwenden Sie die
   [WordPress-Zuordnungstabelle](../../../../installation/migration/wordpress.md#2-wordpress-auf-exponential-abbilden) und
   die Datentyptabelle der [Zuordnungsseite](../../../../installation/migration/inhaltsmodell-zuordnen.md#2-inhaltsklassen-und-datentypen).
7. Markieren Sie jedes Quellfeld, für das es in der Article-Klasse Ihrer Installation kein Attribut gibt. Entscheiden
   Sie jeweils: einer neuen Klasse ein Attribut hinzufügen oder das Feld bewusst weglassen.
8. Schreiben Sie das Muster für die Remote-IDs auf, das Sie für Beiträge und für Anhänge verwenden werden.

## Erwartetes Ergebnis

- Eine Liste der Article-Attribute mit Bezeichnern (zum Beispiel `title` als **Textzeile**).
- Eine Zuordnungstabelle, in der jedes WordPress-Feld zugeordnet oder als weggelassen markiert ist, mit einer
  vermerkten Umwandlung für `post_content` (HTML zu XmlText) und Kategorien, die Orten oder Tags zugeordnet sind.
- Muster für Remote-IDs wie `wp-post-<id>` und `wp-attachment-<id>`.
- Der Trainer prüft zwei Tabellen: Ist jedes Feld abgedeckt, und sind die Beziehungen für einen zweiten Durchlauf
  geplant?

## Quiz

1. Wie heißt ein „Feld“ eines anderen Systems in Exponential?
   A. Ein Ort B. Ein Attribut eines Datentyps C. Eine Sektion D. Ein Objektzustand
2. Welcher Datentyp nimmt einen HTML-Text mit Formatierung auf, und was geschieht mit Inline-Skripten?
3. Ein Artikel gehört zu drei Kategorien. Wie kann der Baum das ausdrücken?
4. Warum erhält jedes importierte Objekt eine Remote-ID, die aus der Quell-ID gebildet wird? Nennen Sie zwei Gründe.
5. Richtig oder falsch: Exponential hat einen verschachtelten „Repeater“-Datentyp für sich wiederholende Feldgruppen.

<details>
<summary>Antworten</summary>

1. **B** ([Zuordnungsseite, Abschnitt 1](../../../../installation/migration/inhaltsmodell-zuordnen.md#1-die-bausteine-im-überblick)).
2. `ezxmltext`; Skripte, Styles und Inline-Event-Handler bleiben absichtlich nicht erhalten
   ([Zuordnungsseite, Abschnitt 2](../../../../installation/migration/inhaltsmodell-zuordnen.md#2-inhaltsklassen-und-datentypen)).
3. Ein Hauptort und zwei zusätzliche Orte, oder Schlüsselwörter oder Tags
   ([Zuordnungsseite, Abschnitt 4](../../../../installation/migration/inhaltsmodell-zuordnen.md#4-orte-der-inhaltsbaum)).
4. Ein erneuter Importlauf aktualisiert, statt zu duplizieren, und Beziehungen und Weiterleitungen lassen sich darüber
   auflösen
   ([Zuordnungsseite, Abschnitt 3](../../../../installation/migration/inhaltsmodell-zuordnen.md#3-objekte-versionen-und-übersetzungen)).
5. **Falsch.** Verwenden Sie Kindobjekte einer kleinen Klasse oder `ezmatrix` (Zuordnungsseite, Abschnitt 2).

</details>

## Weiterführende Informationen

- [Inhalte auf Exponential abbilden](../../../../installation/migration/inhaltsmodell-zuordnen.md): jeder Baustein aus Sicht
  einer Migration.
- [Die gemeinsame Migrations-Checkliste, Abschnitt 3](../../../../installation/migration/checkliste.md#3-das-inhaltsmodell-abbilden):
  die Zuordnungstabelle.
- [Inhaltsmodell und Bearbeitung](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md): Klassen und Objekte für
  Administratoren.

## Verwandte Seiten

- [Modul 2: Inhalte auf Exponential abbilden](README.md)
- [Migration zu Exponential: Kursübersicht](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
