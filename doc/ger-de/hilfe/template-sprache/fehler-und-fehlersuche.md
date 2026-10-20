---
title: Fehler und Fehlersuche
audience: developer, designer, app-creator
category: development
order: 430
summary: Die Meldungen, die die Template-Engine protokolliert - unbekannte Variable, unbekanntes Attribut, unbekannter Operator oder unbekannte Funktion, Parserfehler mit Zeile und Spalte, ein fehlendes Template, Fehlgebrauch von def/set/undef - jede auf alpha erzeugt, was die Engine in jedem Fall ausgibt, die Leerraumregel, die alle überrascht, und die Debug-Einstellungen (Debug, ShowXHTMLCode, ShowUsedTemplates, ShowTemplatePathComments, ShowMethodDebug, DisplayWarnings).
keywords: Template-Fehler, Template-Warnungen, unbekannte Template-Variable, nicht registriert, Parserfehler, nicht abgeschlossenes Tag, kein Template geladen, bereits definiert, Fehlersuche, Debug, ShowXHTMLCode, ShowUsedTemplates, ShowTemplatePathComments, DisplayWarnings, Leerraum, template errors, template warnings, unknown template variable, not registered, parser error, unterminated tag, whitespace
type: help
---
# Fehler und Fehlersuche

Die Template-Engine hält eine Seite nie wegen eines Fehlers im Template an. Sie protokolliert einen Fehler oder eine Warnung, gibt für den
fehlerhaften Teil nichts (oder den Rest des Texts) aus und macht weiter. Diese Seite zeigt die Meldungen, die sie erzeugt, jede auf alpha von
einem kleinen Template ausgelöst, damit Sie sie in der Debug-Ausgabe wiedererkennen, und die Einstellungen, die helfen, das Template hinter
einem Stück HTML zu finden.

## Inhalt

1. [Wohin die Meldungen gehen](#wohin-die-meldungen-gehen)
2. [Variablen und Attribute](#variablen-und-attribute)
3. [Operatoren und Funktionen](#operatoren-und-funktionen)
4. [Parserfehler](#parserfehler)
5. [Fehlende Templates](#fehlende-templates)
6. [def, set und undef](#def-set-und-undef)
7. [Die Leerraumregel](#die-leerraumregel)
8. [Debug-Einstellungen](#debug-einstellungen)
9. [Verwandte Seiten](#verwandte-seiten)

## Wohin die Meldungen gehen

`eZTemplate::error()` und `eZTemplate::warning()` (`lib/eztemplate/classes/eztemplate.php`) behalten jede Meldung im Fehler- oder Warnungslog
der Engine und geben sie mit Template-Datei, Zeile und Spalte an `eZDebug` weiter. Sie sehen sie

- in der Debug-Ausgabe der Seite, wenn `[DebugSettings] DebugOutput=enabled` gesetzt ist und Ihre IP oder Ihr Benutzer zugelassen ist
  (`DebugByIP`, `DebugByUser`);
- in `var/log/error.log` und `var/log/warning.log`, wenn das Debug-Log für diese Stufe eingeschaltet ist;
- auf der Seite selbst nur mit `template.ini [ControlSettings] DisplayWarnings=enabled` (standardmäßig und auf alpha ausgeschaltet), was einen
  Warnkasten für Template-Fehler hinzufügt.

Die Beispiele unten liefen auf alpha durch die Engine; jedes zeigt die Ausgabe und dann, was die Engine protokolliert hat. Die Dateipfade in den
Parsermeldungen sind die Arbeitspfade, in denen die Beispiele lagen.

## Variablen und Attribute

Eine nicht vorhandene Variable gibt nichts aus und protokolliert einen Fehler mit dem Namensraum, in dem sie gesucht wurde:

```tpl
before [{$no_such_variable}] after
```

Ausgabe auf alpha:

```text
before [] after
```

Von der Template-Engine protokolliert:

```text
ERROR : Unknown template variable 'no_such_variable' in namespace ''
```

Ein Attribut, das ein Objekt oder Array nicht hat: Die Meldung nennt die Klasse (oder die Größe des Arrays) und führt auf, was existiert:

```tpl
{def $list = array( 'a', 'b' )}
[{$list.5}] [{$list.first}]
```

Ausgabe auf alpha:

```text
[] []
```

Von der Template-Engine protokolliert:

```text
ERROR : No such attribute for array(2): 5
Choose one of following: 0, 1
ERROR : No such attribute for array(2): first
Choose one of following: 0, 1
```

Bei einem Objekt lautet die Meldung `No such attribute for object(<class>): <name>`; siehe [Das Objektmodell in Templates](das-objektmodell-in-templates.md).
Nutzen Sie `is_set()`, um ohne Meldung zu prüfen.

## Operatoren und Funktionen

Ein auf diesem Siteaccess nicht registrierter Operator protokolliert eine Warnung und **reicht seine Eingabe unverändert durch**:

```tpl
before [{'text'|no_such_operator}] after
```

Ausgabe auf alpha:

```text
before [text] after
```

Von der Template-Engine protokolliert:

```text
WARNING : Operator 'no_such_operator' is not registered
```

Ein unbekanntes Funktions-Tag protokolliert eine Warnung, und vom Tag wird nichts ausgegeben. Beachten Sie, dass auch der Text nach dem Tag in
derselben Zeile nicht ausgegeben wurde:

```tpl
before {no_such_function param=1} after
```

Ausgabe auf alpha:

```text
before
```

Von der Template-Engine protokolliert:

```text
WARNING : Function "no_such_function" is not registered
```

Beides geschieht, wenn eine Erweiterung, die den Operator oder die Funktion registriert, auf dem Siteaccess nicht aktiv ist oder ihre
Autoload-Registrierung fehlt (siehe [Template-Operatoren schreiben](../../../eng-us/help/creators/app-creators/template-operators.md)).

## Parserfehler

Ein Tag ohne schließende Klammer: Der Parser protokolliert, wo das Tag beginnt (`file:line[column]`), und gibt den Rest des Templates als Text
aus:

```tpl
line one
{def $a = 1
line three
```

Ausgabe auf alpha:

```text
line one
{def $a = 1
line three
```

Von der Template-Engine protokolliert:

```text
ERROR : parser error @ tm-errors/04-unterminated-tag.tpl:1[0]
Unterminated tag, needs a } to end the tag.
{def $a = 1
line three
```

Überzählige Zeichen in den Parametern einer Funktion: Der Parser protokolliert, was er erwartet hat; der verstandene Teil läuft trotzdem (`$a`
wurde auf 1 gesetzt):

```tpl
{def $a = 1 2}
[{$a}]
```

Ausgabe auf alpha:

```text
[1]
```

Von der Template-Engine protokolliert:

```text
ERROR def: parser error @ tm-errors/05-extra-characters.tpl:1
($) expected at [2]
```

Ein Block, der nie geschlossen wird, wird überhaupt nicht gemeldet: Der Parser behandelt den Rest der Datei als Rumpf des Blocks.

```tpl
{if true()}
open if, never closed
```

Ausgabe auf alpha:

```text
open if, never closed
```

## Fehlende Templates

Ein `include` (oder eine Ansichtsfunktion), dessen Template in keiner Design-Basis gefunden wird, protokolliert eine Warnung und gibt nichts aus:

```tpl
before [{include uri='design:no/such/template.tpl'}] after
```

Ausgabe auf alpha:

```text
before [] after
```

Von der Template-Engine protokolliert:

```text
WARNING : No template could be loaded for "no/such/template.tpl" using resource "design"
```

Zur Suchreihenfolge hinter `could not be loaded` siehe [Template-Ressourcen und Overrides](template-ressourcen-und-overrides.md).

## def, set und undef

`def` auf eine vorhandene Variable warnt und weist trotzdem zu; `set` auf eine nicht vorhandene Variable warnt und tut nichts; `undef` auf eine
nicht mit `def` angelegte Variable warnt:

```tpl
{def $a = 1}
{def $a = 2}
{set $b = 3}
{undef $c}
[{$a}]
```

Ausgabe auf alpha:

```text
[2]
```

Von der Template-Engine protokolliert:

```text
WARNING def: Variable 'a' is already defined.
WARNING set: Variable 'b' doesn't exist, cannot set
WARNING undef: Variable 'c' is not defined with {def}.
```

Die Regeln stehen in `lib/eztemplate/classes/eztemplatedeffunction.php` und `eztemplatesetfunction.php`; die Seite für Designer ist
[Variablen und def](../../../eng-us/help/designers/templates/variables-and-def.md).

## Die Leerraumregel

Viele Fragen nach einem „fehlenden Zeilenumbruch“ kommen von einer Regel des Parsers (`eZTemplateMultiPassParser::parseWhitespaceRemoval()`,
`lib/eztemplate/classes/eztemplatemultipassparser.php`): **Ein Zeilenumbruch direkt vor und einer direkt nach jedem Funktions-Tag** (`{if}`,
`{/if}`, `{foreach}`, `{def ...}`, `{include ...}` und den übrigen) wird entfernt. Ausgabe-Tags wie `{$x}` oder `{$x|wash}` behalten die
Zeilenumbrüche um sich. Die Folgen:

- Ein Funktions-Tag allein in seiner Zeile hinterlässt keine Leerzeile, was Templates lesbar macht.
- Text zwischen zwei Funktions-Tags in getrennten Zeilen verliert beide Zeilenumbrüche. Um einen Zeilenumbruch zwischen `{/if}` und dem nächsten
  `{if}` zu behalten, lassen Sie eine Leerzeile dazwischen; zwei Leerzeilen behalten zwei.
- Eine Zeile, die mit einem Funktions-Tag endet, und die Zeile danach werden verbunden.

Die Beispiele von [Data Map und Inhalt](data-map-und-inhalt.md) nutzen genau deshalb Leerzeilen.

## Debug-Einstellungen

Alle in `site.ini [TemplateSettings]`, wenn nicht anders vermerkt; Werte auf dem Siteaccess `site` von alpha:

```tpl
Debug:                    {ezini( 'TemplateSettings', 'Debug' )}
ShowXHTMLCode:            {ezini( 'TemplateSettings', 'ShowXHTMLCode' )}
ShowUsedTemplates:        {ezini( 'TemplateSettings', 'ShowUsedTemplates' )}
ShowTemplatePathComments: {ezini( 'TemplateSettings', 'ShowTemplatePathComments' )}
DebugOutput:              {ezini( 'DebugSettings', 'DebugOutput' )}
DisplayWarnings:          {ezini( 'ControlSettings', 'DisplayWarnings', 'template.ini' )}
```

Ausgabe auf alpha:

```text
Debug:                    disabled
ShowXHTMLCode:            enabled
ShowUsedTemplates:        disabled
ShowTemplatePathComments: disabled
DebugOutput:              enabled
DisplayWarnings:          disabled
```

| Einstellung | Wirkung |
|---|---|
| `Debug` | fügt Kommentare `<!-- START: including template: <file> (<uri>) -->` und `STOP` um jedes gelesene Template ein (nicht um ein Pagelayout, das mit `<!DOCTYPE` beginnt) und protokolliert, welche Dateien geladen werden |
| `ShowXHTMLCode` | gibt bei eingeschaltetem `Debug` den Template-Pfad zusätzlich als sichtbaren Text in der Seite aus |
| `ShowUsedTemplates` | zeichnet jedes genutzte Template auf; die Debug-Ausgabe (und die Template-Liste der Debug-Leiste) zeigt sie. Braucht `DebugOutput` |
| `ShowTemplatePathComments` | schließt jedes gerenderte Template in Kommentare `<!-- START <path> -->` / `<!-- STOP <path> -->` ein, mit oder ohne Compiler; nur für die Entwicklung, nie auf Siteaccesses, die JSON, XML oder RSS ausliefern |
| `ShowMethodDebug` | protokolliert Beginn und Ende jedes Aufrufs einer Funktion oder eines Operators (nur interpretierte Templates) |
| `template.ini [ControlSettings] DisplayWarnings` | zeigt Template-Fehler als Warnkasten auf der Seite |

Kommentare von `Debug` werden beim Lesen eines Templates eingefügt; bereits vorhandene kompilierte Templates bekommen sie also erst, wenn der
Template-Cache geleert ist. `ShowTemplatePathComments` funktioniert mit kompilierten Templates so, wie sie sind.

Um auf einer laufenden Website die Datei hinter einem Stück HTML zu finden, ist `ShowTemplatePathComments` meist der schnellste Weg: Schalten
Sie es in einer Entwicklungsinstallation für einen Siteaccess ein, sehen Sie sich den Quelltext der Seite an und suchen Sie nach dem HTML. Die
Seite [Fehlersuche in Templates](../../../eng-us/help/designers/templates/template-debugging.md) geht das durch.

## Verwandte Seiten

- [Fehlersuche in Templates](../../../eng-us/help/designers/templates/template-debugging.md)
- [Template-Pfadkommentare](../../../eng-us/help/designers/templates/template-path-comments.md)
- [Häufige Fehler in Templates](../../../eng-us/help/designers/templates/common-template-mistakes.md)
- [Der Template-Compiler](der-template-compiler.md)
- [Fehlersuche und Logs](../../../eng-us/help/creators/app-creators/debugging-and-logs.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
