---
title: "Häufige Aufgaben mit Templates"
audience: builder, designer
category: books
order: 450
summary: Rezepte aus dem Buch (oberes Menü, Brotkrumen, Baummenü, Bearbeitungslinks, Datumswerte, Text kürzen, Benutzerbereich, Schlüsselwörter), neu geschrieben mit foreach, aktuellen Operatoren und ohne Inline-Stil.
keywords: Menü, Brotkrumen, Baummenü, Bearbeitungslink, Datum, Text kürzen, Benutzerbereich, Schlüsselwort, Template-Rezepte, Buch Learning, menu, breadcrumb, treemenu, edit link, can_edit, datetime, currentdate, shorten, wash, autolink, keyword, user panel, template recipes, Learning book
type: help
---
# Häufige Aufgaben mit Templates

Das Buch schließt sein Template-Kapitel mit Rezepten. Hier stehen sie in heutiger Syntax: `foreach` statt `section`,
kein Inline-`style=` und die Operatoren, die es heute gibt. Alle gehören in die Templates eines Designs; wohin genau,
steht in [Grundsätze der Templates](grundsaetze-der-templates.md).

## Navigationsmenüs

**Ein Menü der obersten Ebene** (die Ordner direkt unter der Wurzel, der aktuelle markiert). `treemenu` nimmt den Pfad
der aktuellen Seite (`$module_result.path`), eine Knoten-ID (nicht mehr verwendet: übergeben Sie `false()`), den
Klassenfilter, wie viele Ebenen übersprungen und wie viele gezeigt werden ([Menü- und Baum-Operatoren](../template-operatoren/menue-und-baum-operatoren.md)):

```
{def $top=treemenu( $module_result.path, false(), array( 'folder' ), 1, 1 )}
<ul class="topmenu">
{foreach $top as $item}
    <li{if $item.is_selected} class="selected"{/if}>
        <a href={$item.url_alias|ezurl}>{$item.text|wash}</a>
    </li>
{/foreach}
</ul>
```

**Brotkrumen**: eine Schleife über den Pfad. Schließen Sie sie in einen Cache-Block mit dem Knoten als Schlüssel ein:

```
{cache-block keys=array( 'path', $module_result.node_id )}
<p class="path">
{foreach $module_result.path as $p offset 1}
    {if $p.url}<a href={$p.url_alias|ezurl}>{$p.text|shorten( 60 )|wash}</a>
    {else}{$p.text|shorten( 60 )|wash}{/if}
{delimiter} / {/delimiter}
{/foreach}
</p>
{/cache-block}
```

(Prüfen Sie `$p.url_alias`: Die Einträge des Pfads enthalten einen `url_alias`; der letzte, die Seite selbst, hat keine
`url`.)

**Ein Baummenü**: `treemenu` mit mehr Ebenen liefert eine flache Liste in Menüreihenfolge; jeder Eintrag hat `level` und
`indent`. Erledigen Sie die Einrückung mit einer CSS-Klasse, die aus der Ebene gebildet wird (`level-1`, `level-2` ...),
im Stylesheet des Designs, nie mit `style=`:

```
{foreach treemenu( $module_result.path, false(), false(), 1, 5 ) as $item}
    <li class="level-{$item.level}{if $item.is_selected} selected{/if}">
        <a href={$item.url_alias|ezurl}>{$item.text|wash}</a>
    </li>
{/foreach}
```

## Bearbeitungsfunktionen auf der Website

Lassen Sie die richtigen Benutzer von der öffentlichen Seite aus bearbeiten. Fragen Sie das Objekt, ob der aktuelle
Benutzer das darf:

```
{if $node.object.can_edit}
    <a href={concat( 'content/edit/', $node.contentobject_id )|ezurl}>Edit</a>
{/if}
```

Entfernen und Kommentieren sind Schaltflächen in einem Formular, das an `content/action` sendet (`ActionRemove`,
`NewButton`), mit einem Formular-Token. Kopieren Sie das Markup aus einem mitgelieferten Template, etwa
`design/standard/templates/node/view/full.tpl`, statt es aus dem Gedächtnis zu tippen.
[Ansichtsmodi](../erweiterungsentwicklung/teil-2-builder/view-modi-fuer-builder.md) zeigt, wo ein solches Template liegt.

## Datum und Uhrzeit

`currentdate()` ist die aktuelle Zeit als Zeitstempel; rechnen Sie mit `sum`, formatieren Sie mit `datetime`:

```
{currentdate()|sum( 86400 )|datetime( 'custom', '%j %F %Y' )}
```

für das Datum von morgen. Die Formate und die benannten Klassen (`shortdate`, `time`, ...) stehen in
[Locale-Operatoren](../template-operatoren/locale-operatoren.md).

## Text

- `{$text|shorten( 120 )}` begrenzt eine einfache Zeichenkette; fügen Sie immer `|wash` hinzu, wenn Sie Text von
  Benutzern ausgeben.
- Rich Text wird durch seine eigene Ausgabe begrenzt; der alte Rat, das XML vorher zu kürzen, wird durch ein eigenes
  kurzes Textattribut (eine *Einleitung*) in der Klasse ersetzt.
- `{$text|autolink}` verwandelt Adressen in reinem Text in Links.

## Ein eigenes Benutzererlebnis

- **Ein Platzhalterknoten** (die Klasse aus dem Planungskapitel, nur mit einem Namen) kann als Aufhänger für eine
  besondere Seite wie *My site* dienen, mit einem eigenen Override ([Templates überschreiben](templates-ueberschreiben.md)).
- **Ein Benutzerbereich**: Zeigen Sie die Gruppen und Rollen des aktuellen Benutzers mit `fetch( 'user', 'current_user' )`
  und den Rollen-Operatoren ([Rollen-Operatoren](../template-operatoren/rollen-operatoren.md)); die Funktion für
  Benutzereinstellungen steht in [Was auf meiner Kontoseite steht](../handbuch/was-steht-auf-meiner-kontoseite.md).

## Weitere Aufgaben

- **Eine Änderungshistorie**: eine Schleife über `fetch( 'content', 'version_list', ... )`, die den Ersteller und das
  Datum jeder Version zeigt.
- **Ersteller, Bearbeiter, Veröffentlichungsdatum**: `$node.object.owner.name`, `$node.object.published|datetime(...)`.
- **Schlüsselwörter und automatisch verwandte Objekte**: Ein `ezkeyword`-Attribut enthält die Schlüsselwörter
  (`$node.data_map.keywords.content.keywords`); die Objekte, die ein Schlüsselwort teilen, werden mit der Funktion
  `keyword` des Moduls `content` abgerufen (siehe [Fetch-Funktionen](../template-operatoren/wie-fetch-funktionen-arbeiten.md)).
  Die „erweiterte Schlüsselwortfunktion“ des Buchs (Auflisten nach Schlüsselwort) ist ein fetch mit den Parametern
  `alphabet` oder `classid`, wie dort beschrieben.
- **Ein Forum mit Diskussionssträngen**: Ein Forum ist ein Ordner mit Themen, jedes Thema ein Baum aus Antworten; das
  Template durchläuft die Unterelemente rekursiv mit einem Template, das sich selbst mit `include` einbindet.

Zurück zu [den Kapiteln](README.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 3, „Displaying Content with eZ publish Templates“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: umgestellt und gekürzt; Syntax, Operatoren, Einstellungen und Beispiele auf Exponential 6 aktualisiert (foreach, aktuelle Einstellungen, kein Inline-Stil und kein Inline-Skript).
