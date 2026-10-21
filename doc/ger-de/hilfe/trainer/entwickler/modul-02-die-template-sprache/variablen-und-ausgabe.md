---
title: "Lektion 2.1: Variablen und Ausgabe"
audience: trainer, developer
category: training
order: 21
summary: Die Teilnehmenden richten eine Template-Spielwiese ein, geben Variablen und ihre Attribute aus, deklarieren, ändern und geben Variablen mit def, set und undef frei, bauen Arrays und Hashes und maskieren Ausgaben mit wash. 50 Minuten.
keywords: entwickler, template-sprache, tpl, def, set, undef, variable, attribut, array, hash, wash, kommentar, template-spielwiese, developers course, template language, attribute, comment, template playground
type: help
---
# Lektion 2.1: Variablen und Ausgabe

Seiten bestehen aus Templates: HTML mit Ausdrücken in geschweiften Klammern. Diese Lektion beginnt die Template-Sprache
mit dem, was jedes Template zuerst tut: einen Wert ausgeben, Variablen deklarieren und maskieren, was Benutzer
eingegeben haben. Die Teilnehmenden bauen eine kleine Spielwiese, die ein Template auf der Kommandozeile rendert, damit
sie jeden Ausdruck dieses Moduls ausprobieren können, ohne das Design einer Website anzufassen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 2: Die Template-Sprache](README.md) |
| Dauer | 50 Minuten (15 Präsentation, 35 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Entwickler und Template-Bauer |
| Voraussetzungen | [Lektion 1.4](../modul-01-die-entwicklungsinstallation/ein-skript-mit-geladenem-kernel.md): `extension/mysite_hello` ist vorhanden |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine Template-Datei mit `eZTemplate::factory()` und `ezexec.php` auf der Kommandozeile rendern;
- eine Variable und eines ihrer Attribute mit `{$node.name}` ausgeben;
- Variablen mit `{def}`, `{set}` und `{undef}` deklarieren, ändern und freigeben;
- Listen mit `array()` und benannte Listen mit `hash()` bauen;
- jeden Wert, den ein Benutzer eingegeben hat, mit `|wash` maskieren.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Templates und Design, Teil 6](../../../../anleitungen/templates-und-design.md#6-die-template-sprache-in-zehn-minuten).

1. **Was ein Template ist** (3 Minuten). Eine Datei `.tpl` ist HTML mit Ausdrücken `{ ... }`; `{* ... *}` ist ein
   Kommentar. Die Templates des Kernels liegen in `design/standard/templates/`; öffnen Sie `node/view/full.tpl` am
   Projektor.
2. **Die Spielwiese** (3 Minuten). Zeigen Sie die beiden Dateien der Übung. `eZTemplate::factory()` liefert die
   Template-Engine, `setVariable()` übergibt ihr einen Wert, `fetch()` rendert eine Datei und gibt den Text zurück.
3. **Variablen und Attribute** (4 Minuten). `{$node.name}` liest das Attribut `name` des Knotens. Derselbe Punkt liest
   einen Schlüssel eines Hashs (`{$person.name}`) oder einen Index (`{$list.0}`); `{$person['role']}` funktioniert
   ebenfalls.
4. **`def`, `set`, `undef`** (3 Minuten). `{def}` deklariert einmal pro Template; ein zweites `{def}` mit demselben
   Namen erzeugt eine Warnung. `{set}` ändert eine deklarierte Variable. `{undef}` gibt sie am Ende frei, eine
   Gewohnheit, die Überraschungen in eingebundenen Templates vermeidet.
5. **`wash`** (2 Minuten). `{$value|wash}` macht aus `<` ein `&lt;`. Verwenden Sie es für alles, was von Benutzern
   kommt: Namen, Titel, Suchbegriffe. Rich-Text-Attribute werden stattdessen mit `attribute_view_gui` angezeigt
   (Lektion 2.6).

### Häufige Fragen

**Warum `def` und nicht einfach `set`?** `def` legt die Variable im aktuellen Template an; `set` ändert eine
vorhandene. Ein zweites `def` mit demselben Namen in einem Template schreibt die Warnung „Variable '...' is already
defined“, die Fehler beim Kopieren und Einfügen auffängt.

**Brauche ich `undef`?** Variablen leben bis zum Ende des Templates. Sie freizugeben ist gute Praxis in Templates, die
oft eingebunden werden, damit nichts zwischen den Aufrufen durchsickert.

**Was ist mit den alten `let` und `section`?** Ältere Templates verwenden `{let}` und `{section}`. Sie funktionieren
noch; neue Templates verwenden `def` und `foreach`.

### Stolperfallen

- **Leerraum.** Ein Tag am Zeilenende schluckt den Zeilenumbruch danach; die Ausgabe der Spielwiese wirkt
  zusammengedrückt. Das ist normal.
- **Kein `wash` bei einem Titel.** Ein Titel mit `&` oder `<` zerstört die Seite oder öffnet eine Lücke für
  Cross-Site-Scripting. Machen Sie `|wash` zum Reflex.
- **Fehlendes Attribut.** `{$node.nonexistent}` gibt nichts aus, oft ohne jede Meldung. Ist ein Wert unerwartet leer,
  listen Sie die Attribute der Variablen mit `{$node|attribute( 'show' )}` auf (Lektion 2.4).

## Praktische Übung

Auf der Entwicklungsinstallation, aus ihrem Wurzelverzeichnis.

1. Legen Sie `extension/mysite_hello/scripts/play.php` an:

   ```php
   <?php
   // Template playground: php bin/php/ezexec.php extension/mysite_hello/scripts/play.php
   $tpl = eZTemplate::factory();
   $tpl->setVariable( 'node', eZContentObjectTreeNode::fetch( 2 ) );
   eZCLI::instance()->output( $tpl->fetch( 'extension/mysite_hello/scripts/play.tpl' ) );
   ```

2. Legen Sie `extension/mysite_hello/scripts/play.tpl` an:

   ```
   {* My first template *}
   {def $title = $node.name
        $count = 3}
   <h1>{$title|wash}</h1>
   {set $count = $count|inc}
   <p>Count: {$count}</p>
   {undef $title $count}
   ```

3. Prüfen Sie beide: `php -l extension/mysite_hello/scripts/play.php` und
   `php bin/php/eztemplatecheck.php extension/mysite_hello/scripts/play.tpl`.
4. Führen Sie die Spielwiese aus: `php bin/php/ezexec.php extension/mysite_hello/scripts/play.php`.
5. Fügen Sie am Ende von `play.tpl` ein Array und einen Hash hinzu und geben Sie Teile davon aus:

   ```
   {def $colors = array( 'red', 'green', 'blue' )
        $person = hash( 'name', 'Ada', 'role', 'editor' )}
   {$colors.1} {$person.name} {$person['role']}
   {undef $colors $person}
   ```

6. Führen Sie sie erneut aus.
7. Geben Sie einen Wert mit HTML darin aus, einmal ohne und einmal mit `wash`:
   `{'<b>bold</b>'}` und `{'<b>bold</b>'|wash}`. Führen Sie es aus und vergleichen Sie.
8. Geben Sie drei Attribute des Knotens aus: `{$node.node_id}`, `{$node.class_identifier}`, `{$node.children_count}`.
9. Fügen Sie `{def $count = 1}` zweimal am Ende der Datei hinzu und führen Sie die Spielwiese mit angezeigten Warnungen
   aus: `php bin/php/ezexec.php --debug=warning extension/mysite_hello/scripts/play.php`. Lesen Sie die Warnung und
   entfernen Sie dann die beiden Zeilen.
10. Committen Sie beide Dateien im Repository der Erweiterung.

## Erwartetes Ergebnis

- Schritt 4 gibt `<h1>` mit dem Namen von Knoten 2 und `Count: 4` aus.
- Schritt 6 fügt `green Ada editor` hinzu.
- Schritt 7: Die erste Ausgabe zeigt `<b>bold</b>`, die maskierte `&lt;b&gt;bold&lt;/b&gt;`.
- Schritt 8 gibt die ID 2, den Klassenbezeichner (zum Beispiel `folder`) und eine Zahl aus.
- Schritt 9: Der Debug-Abschnitt am Ende nennt `play.tpl`, Zeile und Spalte, mit „Variable 'count' is already
  defined.“
- Der Trainer prüft die Ausgabe der Spielwiese aller Teilnehmenden am Bildschirm.

## Quiz

1. Wie schreiben Sie einen Kommentar in einem Template?
2. Welche Funktion ändert den Wert einer bereits deklarierten Variablen?
   A. `def` B. `set` C. `undef` D. `let`
3. Was liest `{$person.name}`, wenn `$person` ein Hash ist?
4. Was macht `|wash` mit `<`?
5. Richtig oder falsch: Ein Rich-Text-Attribut sollte mit `{$attribute.content|wash}` ausgegeben werden.

<details>
<summary>Antworten</summary>

1. `{* ... *}`.
2. **B.** `set`; `def` deklariert, `undef` gibt frei.
3. Den Wert unter dem Schlüssel `name`; der Punkt liest Attribute von Objekten und Schlüssel von Hashes gleichermaßen.
4. Es macht daraus `&lt;`, sodass der Browser das Zeichen anzeigt, statt ein Tag zu lesen.
5. **Falsch.** Verwenden Sie `attribute_view_gui`, das den Rich Text mit dem Template seines Datentyps rendert
   (Lektion 2.6).

</details>

## Weiterführende Literatur

- [Templates und Design](../../../../anleitungen/templates-und-design.md): wie Templates gefunden werden, Overrides,
  Fehlersuche.
- [String-Template-Operatoren](../../../../../eng-us/features/6.0/string-template-operators.md): die String-Operatoren von
  Exponential 6.

## Verwandte Seiten

- [Modul 2: Die Template-Sprache](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
