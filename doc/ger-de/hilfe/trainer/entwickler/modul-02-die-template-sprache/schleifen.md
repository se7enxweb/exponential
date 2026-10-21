---
title: "Lektion 2.3: Schleifen mit foreach"
audience: trainer, developer
category: training
order: 23
summary: Die Teilnehmenden durchlaufen mit foreach Arrays, Hashes und die Kinder eines Knotens, verwenden Schlüssel, Trennzeichen, Sequenzen für abwechselnde Klassen, offset, max und reverse und verlassen eine Schleife vorzeitig mit break, continue und skip. 45 Minuten.
keywords: entwickler, template-sprache, foreach, schleife, trennzeichen, delimiter, sequence, offset, max, reverse, break, continue, skip, kinder, children, array-operatoren, count, developers course, template language, loop, array operators
type: help
---
# Lektion 2.3: Schleifen mit foreach

Die meisten Seiten zeigen Listen: die Artikel eines Ordners, die Einträge eines Menüs, die Bilder einer Galerie.
`{foreach}` ist die Schleife der Template-Sprache. In dieser Lektion durchlaufen die Teilnehmenden Arrays, Hashes und
die Kinder eines Knotens und verwenden die Teile von `foreach`, die zusätzlichen Code sparen: Trennzeichen, Sequenzen,
Offset und Maximum.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 2: Die Template-Sprache](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Entwickler und Template-Bauer |
| Voraussetzungen | [Lektion 2.2](bedingungen-und-logik.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- mit `{foreach $list as $item}` und `{foreach $hash as $key => $value}` ein Array oder einen Hash durchlaufen;
- mit `{delimiter}` Trennzeichen zwischen die Elemente setzen;
- mit `sequence ... as $class` CSS-Klassen abwechseln;
- eine Schleife mit `offset`, `max` und `reverse` begrenzen und umordnen;
- mit `{break}`, `{continue}` und `{skip}` eine Schleife verlassen oder Elemente überspringen und mit `|count` zählen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: der Syntaxkommentar in `lib/eztemplate/classes/eztemplateforeachfunction.php`:

```
{foreach <array> as [$keyVar =>] $itemVar
         [sequence <array> as $sequenceVar]
         [offset <offset>]
         [max <max>]
         [reverse]}
    [{delimiter}...{/delimiter}]
    [{break}] [{continue}] [{skip}]
{/foreach}
```

1. **Die einfache Schleife** (3 Minuten). `{foreach $list as $item}{$item}{/foreach}`. Zeigen Sie sie über ein Array
   und über `$node.children`, ein Attribut des Knotens, das seine Kindknoten zurückgibt.
2. **Schlüssel** (2 Minuten). `{foreach $hash as $key => $value}` liefert auch den Schlüssel; bei einem Array ist der
   Schlüssel der Index, gezählt ab 0.
3. **Trennzeichen** (2 Minuten). `{delimiter}, {/delimiter}` wird zwischen den Elementen ausgegeben, nie nach dem
   letzten.
4. **Sequenz** (3 Minuten). `sequence array( 'odd', 'even' ) as $class` durchläuft die Werte im Kreis, für Zeilen im
   Zebramuster.
5. **Offset, max, reverse** (3 Minuten). Die ersten Elemente überspringen, nach einer Anzahl aufhören, rückwärts
   gehen.
6. **Vorzeitig verlassen** (2 Minuten). `{break}` beendet die Schleife, `{continue}` und `{skip}` gehen zum nächsten
   Element.

### Häufige Fragen

**Sollte ich bei einem großen Ordner über `$node.children` laufen?** Nein. Das lädt jedes Kind. Für echte Listen
verwenden Sie `fetch( 'content', 'list', ... )` mit einem `limit` (Modul 5); `$node.children` ist für wenige Elemente
in Ordnung.

**Gibt es einen Zähler?** Verwenden Sie den Schlüssel einer Array-Schleife oder `{set $i = $i|inc}` in der Schleife mit
einer vorher deklarierten Variablen.

**Was ist mit `{section}`?** Das ist die ältere Schleife, die noch in alten Templates zu finden ist. Schreiben Sie neue
Templates mit `foreach`.

### Stolperfallen

- **wash in Schleifen.** Jeder ausgegebene Name braucht weiterhin `|wash`.
- **`def` in einer Schleife.** Ein `{def}` in der Schleife läuft bei jedem Element und warnt ab dem zweiten Mal.
  Deklarieren Sie vor der Schleife, verwenden Sie `set` darin.
- **Große Schleifen in der Spielwiese.** Halten Sie bei einem großen Ordner `max` klein, damit die Ausgabe lesbar
  bleibt.

## Praktische Übung

Verwenden Sie die Spielwiese aus Lektion 2.1 (`extension/mysite_hello/scripts/play.tpl`) und führen Sie nach jedem
Schritt `php bin/php/ezexec.php extension/mysite_hello/scripts/play.php` aus.

1. Durchlaufen Sie ein Array mit seinen Schlüsseln und einem Trennzeichen:

   ```
   {def $colors = array( 'red', 'green', 'blue' )}
   {foreach $colors as $i => $color}{$i}:{$color}{delimiter}, {/delimiter}{/foreach}
   ```

2. Fügen Sie abwechselnde Klassen hinzu:

   ```
   <ul>
   {foreach $colors as $color sequence array( 'odd', 'even' ) as $class}
       <li class="{$class}">{$color|wash}</li>
   {/foreach}
   </ul>
   ```

3. Zeigen Sie nur das zweite und dritte Element: `{foreach $colors as $c max 2 offset 1}{$c} {/foreach}`.
4. Zeigen Sie sie rückwärts: `{foreach $colors as $c reverse}{$c} {/foreach}`.
5. Zählen Sie sie: `{$colors|count} colors`.
6. Durchlaufen Sie die Kinder des Knotens, höchstens fünf:

   ```
   <ul>
   {foreach $node.children as $child max 5}
       <li>{$child.name|wash} ({$child.class_identifier})</li>
   {/foreach}
   </ul>
   ```

7. Überspringen Sie versteckte Kinder: Fügen Sie `{if $child.is_hidden}{continue}{/if}` als erste Zeile in der
   Schleife hinzu.
8. Durchlaufen Sie einen Hash: `{foreach hash( 'a', 1, 'b', 2 ) as $key => $value}{$key}={$value} {/foreach}`.
9. Geben Sie die Variablen am Ende mit `{undef $colors}` frei und committen Sie das Template.

## Erwartetes Ergebnis

- Schritt 1 gibt `0:red, 1:green, 2:blue` aus.
- Schritt 2 gibt drei Elemente `<li>` mit den Klassen `odd`, `even`, `odd` aus.
- Schritt 3 gibt `green blue` aus; Schritt 4 `blue green red`; Schritt 5 `3 colors`.
- Schritt 6 gibt die Namen und Klassenbezeichner von bis zu fünf Kindern von Knoten 2 aus.
- Schritt 8 gibt `a=1 b=2` aus.
- Der Trainer vergleicht die Ausgabe von Schritt 6 mit dem Inhaltsbaum in der Administration.

## Quiz

1. Wie geben Sie `, ` zwischen den Elementen aus, aber nicht nach dem letzten?
2. Welcher Teil von `foreach` wechselt CSS-Klassen ab?
   A. `delimiter` B. `sequence` C. `offset` D. `reverse`
3. Was gibt `{foreach $list as $c max 2 offset 1}` für `array( 'a', 'b', 'c', 'd' )` aus?
4. Warum ist `$node.children` für einen Ordner mit Tausenden von Elementen eine schlechte Wahl?
5. Was ist der Unterschied zwischen `{break}` und `{continue}`?

<details>
<summary>Antworten</summary>

1. Mit `{delimiter}, {/delimiter}` in der Schleife.
2. **B.** `sequence array( 'odd', 'even' ) as $class`.
3. `b` und `c`: eines überspringen, dann zwei nehmen.
4. Es lädt jedes Kind; ein `fetch` mit einem `limit` lädt eine Seite (Modul 5).
5. `{break}` beendet die Schleife; `{continue}` macht mit dem nächsten Element weiter.

</details>

## Weiterführende Literatur

- [Templates und Design, Teil 6](../../../../anleitungen/templates-und-design.md#6-die-template-sprache-in-zehn-minuten):
  die Sprache in zehn Minuten, mit einem vollständigen Listen-Template.
- [String-Template-Operatoren](../../../../funktionen/6.0/template-operatoren-fuer-zeichenketten.md): Operatoren für die Elemente.

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
