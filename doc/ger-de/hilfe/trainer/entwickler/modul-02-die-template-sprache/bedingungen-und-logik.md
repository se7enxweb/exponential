---
title: "Lektion 2.2: Bedingungen und Logik"
audience: trainer, developer
category: training
order: 22
summary: Die Teilnehmenden schreiben Blöcke mit if, elseif und else, vergleichen Werte mit eq, ne, gt, lt, ge und le, verknüpfen Prüfungen mit and, or und not und verwenden cond, first_set, is_set und switch. 45 Minuten.
keywords: entwickler, template-sprache, bedingung, vergleich, logik, if, elseif, else, eq, ne, gt, lt, and, or, not, cond, first_set, is_set, is_unset, switch, case, contains, developers course, template language, conditions
type: help
---
# Lektion 2.2: Bedingungen und Logik

Templates entscheiden: das Bild zeigen, wenn es eines gibt, eine andere Überschrift für die Startseite, eine Meldung,
wenn eine Liste leer ist. In dieser Lektion schreiben die Teilnehmenden Bedingungen, lernen die Vergleichs- und
Logikoperatoren kennen und die zwei Schreibweisen eines Operators: als Pipe nach einem Wert oder als Funktion.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 2: Die Template-Sprache](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Entwickler und Template-Bauer |
| Voraussetzungen | [Lektion 2.1](variablen-und-ausgabe.md): die Template-Spielwiese |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- Blöcke mit `{if}`, `{elseif}` und `{else}` schreiben;
- Werte mit `eq`, `ne`, `gt`, `lt`, `ge` und `le` in Pipe- und Funktionsform vergleichen;
- Prüfungen mit `and()`, `or()` und `not()` verknüpfen;
- mit `cond()` und `first_set()` einen Wert direkt im Ausdruck wählen und mit `is_set()` prüfen, ob eine Variable
  existiert;
- mit `{switch}` und `{case}` nach einem Wert verzweigen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Templates und Design, Teil 6](../../../../anleitungen/templates-und-design.md#6-die-template-sprache-in-zehn-minuten)
und die Operatorlisten in `lib/eztemplate/classes/eztemplateautoload.php`.

1. **Zwei Formen eines Operators** (3 Minuten). `{$node.depth|gt( 2 )}` und `{gt( $node.depth, 2 )}` sind dieselbe
   Prüfung. Die Pipe übergibt den Wert links von ihr als erste Eingabe.
2. **Vergleiche** (3 Minuten). `eq`, `ne`, `lt`, `gt`, `le`, `ge`. `eq` nimmt mehrere Werte an:
   `{$a|eq( 'x', 'y' )}` ist nur wahr, wenn alle gleich sind.
3. **Logik** (3 Minuten). `and( ... )`, `or( ... )`, `not( ... )` und die Konstanten `true()` und `false()`.
4. **Wahrheitswerte** (2 Minuten). Ein nicht leerer String ist wahr; ein leeres Array ist falsch. `{if $node.name}`
   funktioniert, `{if array()}` geht zu `{else}`.
5. **Direkt im Ausdruck und mit Ersatzwert** (2 Minuten). `cond( $test, 'yes', 'no' )` ist die Bedingung im Ausdruck;
   `first_set( $a, $b, 'default' )` gibt den ersten vorhandenen Wert zurück.
6. **`switch`** (2 Minuten). `{switch match=$value}{case match=3}...{/case}{case}...{/case}{/switch}`: Das `case`
   ohne `match` ist der Standardfall.

### Häufige Fragen

**Wie prüfe ich, ob ein Array einen Wert enthält?** `{if array( 'folder', 'article' )|contains( $node.class_identifier )}`.
`contains` funktioniert auch bei Strings: `{'folder'|contains( 'old' )}` ist wahr.

**Gibt es ein `!=`?** Keine Symbole: Verwenden Sie `ne` oder `not( eq( ... ) )`.

**Was liefert `is_set` für ein Attribut, das es nicht gibt?** `is_set( $node.missing )` ist falsch. Verwenden Sie es,
wenn ein Template mit oder ohne einen Parameter eingebunden werden kann.

### Stolperfallen

- **`eq` mit mehreren Werten.** `{$x|eq( 'a', 'b' )}` bedeutet nicht „a oder b“. Verwenden Sie dafür `contains` auf
  einem Array.
- **Falsches schließendes Tag.** Jedes `{if}` braucht ein `{/if}`. Ein falsches schließendes Tag meldet
  `eztemplatecheck.php` mit Template und Zeile („Unterminated tag“); ein fehlendes `{/if}` ganz am Ende einer Datei
  kann die Prüfung bestehen, zählen Sie also in langen Templates die öffnenden und schließenden Tags.
- **Strings und Zahlen.** `$node.depth|eq( '1' )` und `|eq( 1 )` vergleichen wie PHP; verwenden Sie für Zahlen besser
  Zahlen.

## Praktische Übung

Verwenden Sie die Spielwiese aus Lektion 2.1. Ersetzen Sie bei jedem Schritt den Inhalt von
`extension/mysite_hello/scripts/play.tpl` oder ergänzen Sie ihn darunter, und führen Sie nach jedem Schritt
`php bin/php/ezexec.php extension/mysite_hello/scripts/play.php` aus.

1. Schreiben Sie eine Bedingung über die Tiefe des Knotens:

   ```
   {if $node.depth|eq( 1 )}top level{elseif $node.depth|lt( 4 )}near the top{else}deep{/if}
   ```

2. Schreiben Sie dieselbe Prüfung in Funktionsform: `{if eq( $node.depth, 1 )}top level{/if}`.
3. Verknüpfen Sie zwei Prüfungen:

   ```
   {if and( $node.depth|eq( 1 ), $node.children_count|gt( 0 ) )}top level with children{/if}
   ```

4. Prüfen Sie die Klasse mit einem Array:

   ```
   {if array( 'folder', 'article' )|contains( $node.class_identifier )}a folder or an article{/if}
   ```

5. Geben Sie mit `cond()` `visible` oder `hidden` aus: `{cond( $node.is_hidden, 'hidden', 'visible' )}`.
6. Verwenden Sie `first_set()` für eine Variable, die nie deklariert wurde: `{first_set( $subtitle, 'No subtitle' )}`.
7. Schreiben Sie ein `switch` über die Zahl der Kinder:

   ```
   {switch match=$node.children_count}
   {case match=0}no children{/case}
   {case}some children{/case}
   {/switch}
   ```

8. Ändern Sie das `{/if}` aus Schritt 1 in `{/foreach}` und führen Sie `php bin/php/eztemplatecheck.php extension/mysite_hello/scripts/play.tpl` aus.
   Lesen Sie die Meldung und setzen Sie dann `{/if}` wieder ein.
9. Committen Sie das Template.

## Erwartetes Ergebnis

- Jeder Schritt gibt eine kurze Wendung aus; ist Knoten 2 ein sichtbarer Ordner an der Spitze des Baums, gibt Schritt 1
  `top level` aus, Schritt 4 `a folder or an article`, Schritt 5 `visible`, Schritt 6 `No subtitle`.
- Schritt 8: Die Prüfung gibt `parser error` mit Datei, Zeile und Spalte aus, `Unterminated tag "if" does not match
  tag "foreach"`, und `Template file invalid` statt `Template file valid`.
- Der Trainer prüft das, indem er zwei Teilnehmende bittet, ihre Ausgabe aus Schritt 3 zu erklären.

## Quiz

1. Schreiben Sie die Prüfung „Tiefe größer als 2“ in Pipe-Form.
2. Was gibt `{cond( false(), 'a', 'b' )}` aus?
   A. `a` B. `b` C. nichts D. `false`
3. Wie prüfen Sie, ob `$node.class_identifier` `folder` oder `article` ist?
4. Was gibt `first_set( $x, 'fallback' )` zurück, wenn `$x` nie deklariert wurde?
5. Richtig oder falsch: `{if array()}` geht in den Zweig `{else}`.

<details>
<summary>Antworten</summary>

1. `{$node.depth|gt( 2 )}`.
2. **B.** `cond` gibt den Wert nach der ersten wahren Bedingung zurück, hier den letzten Wert.
3. `{array( 'folder', 'article' )|contains( $node.class_identifier )}`; `eq` mit zwei Werten würde verlangen, dass
   alle drei gleich sind.
4. `'fallback'`.
5. **Richtig.** Ein leeres Array ist falsch.

</details>

## Weiterführende Literatur

- [Templates und Design](../../../../anleitungen/templates-und-design.md): die Sprache in zehn Minuten und die Fehlersuche.
- [Template-Operatoren für Rollen und Richtlinien](../../../../funktionen/6.0/template-operatoren-fuer-rollen-und-richtlinien.md):
  Bedingungen darüber, was der aktuelle Benutzer darf.

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
