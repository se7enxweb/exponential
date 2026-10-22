---
title: "Grundlagen der Template-Syntax: Kommentare, Begrenzer, Variablen, Ablauf"
audience: builder, designer, developer
category: books
order: 410
summary: Die Grundlagen der Template-Sprache, wie das Buch sie lehrt (Kommentare, geschweifte Klammern in JavaScript, Variablen und ihr Gültigkeitsbereich, section und if), mit dem, was Sie heute schreiben (foreach, def und set, kein Inline-Skript).
keywords: Template-Syntax, Kommentar, Variable, Namensraum, Kontrollfluss, Buch Learning, template syntax, comment, ldelim, rdelim, literal, variable, def, set, let, namespace, section, foreach, if, switch, Learning book
type: help
---
# Grundlagen der Template-Syntax: Kommentare, Begrenzer, Variablen, Ablauf

Die Template-Sprache ist eine eigene: Tags in geschweiften Klammern, Variablen mit `$`, Operatoren nach einem senkrechten
Strich. Diese Seite ist die Einführung des Buchs, auf den aktuellen Stand gebracht. Die Sprache ist vollständig in
[Was die Template-Sprache ist](../template-sprache/was-die-template-sprache-ist.md) beschrieben; dort finden Sie die
Einzelheiten.

## Woher die Daten in einem Template kommen

1. **Navigation** (Menüs, Banner): aus dem Baum abgerufen, nicht eingetippt ([Inhalte abrufen](inhalte-in-templates-abrufen.md)).
2. **Applets** (Umfragen, Nachrichtenkästen, Feeds): kleine Templates, die mit Parametern aufgerufen werden, meist mit
   einem Knoten.
3. **`$module_result.content`**: das Hauptergebnis, von der Inhaltsansicht gezeichnet.
4. **Was die Ansicht selbst abruft**: das Objekt mit seinen Attributen, eine reichhaltige Struktur, die Sie verwenden,
   wo sie passt.

## Kommentare

```
{* This is a comment. It is dropped when the template is compiled. *}
```

Ein Kommentar kann sich über mehrere Zeilen erstrecken und Template-Code enthalten, der nicht laufen soll. Schreiben Sie
Kommentare: Beschreiben Sie, wofür ein Template da ist und welche Variablen es erwartet. HTML-Kommentare (`<!-- -->`)
werden an den Browser gesendet und auch von der Template-Engine ausgewertet, sodass sie mit der Template-Syntax
kollidieren können; bevorzugen Sie `{* *}`.

## Geschweifte Klammern: `ldelim`, `rdelim`, `literal`

`{` und `}` beginnen und beenden ein Template-Tag. Um eine davon auszugeben, verwenden Sie `{ldelim}` und `{rdelim}`. Um
einen längeren Block mit geschweiften Klammern (einen JavaScript- oder CSS-Block) unverändert durchzureichen, schließen
Sie ihn in `{literal}` ein:

```
{literal}
function f( p ) { return p; }
{/literal}
```

In Exponential gehört Skript in eine Datei, die aus der Liste der Skriptdateien des Designs geladen wird, nicht in das
Template: Die Content Security Policy blockiert Inline-Skripte ([Content Security Policy für Erweiterungsautoren](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/content-security-policy-fuer-erweiterungen.md)).

## Variablen

Eine Variable enthält einen Wert, der von einer Funktion, einem Operator, einem Modul oder vom Template selbst gesetzt
wird. Eine Variable, die Sie in einem Template anlegen, ist lokal in dieser Template-Datei. Legen Sie sie mit `def` (für
den Rest der Datei deklariert) oder `let` (für einen Block) an und ändern Sie sie mit `set`:

```
{def $title='Hello'}
{set $title=concat( $title, ' world' )}
{$title|wash}
```

Die ältere Form `{let fruit=array(apple,orange)}...{/let}` funktioniert weiterhin. Variablen aus `{section}` und
`{foreach}` liegen im eigenen Namensraum der Schleife (`$:item`, mit führendem Doppelpunkt, in `{section}`; ein
schlichtes `$item` in `{foreach}`). Variablen, die in einem eingebundenen Template gesetzt werden, kommen nicht in das
einbindende zurück, es sei denn, Sie übergeben sie als Parameter.
Siehe [Variablen im Detail](../template-sprache/variablen-im-detail.md) und [Namensräume](../template-sprache/namensraeume.md).

## Kontrollfluss

Das Buch lehrt `{section}` für Schleifen, Bedingungen und Fallunterscheidungen. Das funktioniert weiterhin, und viele
vorhandene Templates verwenden es. Für neue Templates schreiben Sie:

```
{foreach $children as $child}
    <li>{$child.name|wash}</li>
{/foreach}

{if $node.depth|gt( 2 )}...{else}...{/if}

{switch match=$node.class_identifier}
    {case match='article'}...{/case}
    {case}...{/case}
{/switch}
```

[Sections oder foreach](../template-sprache/sections-und-foreach.md) und die
[Referenz der Kontrollstrukturen](../template-sprache/referenz-der-kontrollstrukturen.md) nennen jeden Parameter.

Weiter: [Inhalte in Templates abrufen](inhalte-in-templates-abrufen.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 3, „Displaying Content with eZ publish Templates“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: umgestellt und gekürzt; Syntax, Operatoren, Einstellungen und Beispiele auf Exponential 6 aktualisiert (foreach, aktuelle Einstellungen, kein Inline-Stil und kein Inline-Skript).
