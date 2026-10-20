---
title: Funktionsreferenz
audience: designer, developer
category: templates
order: 330
summary: Jede Template-Funktion (die Tags in geschweiften Klammern, die Templates steuern) der Template-Bibliothek und des Kernels, mit ihrer Syntax in einer Zeile und der Seite, die sie erklärt - def, set, if, foreach, for, while, do, switch, include, cache-block, set-block, die gui-Funktionen und mehr.
keywords: Funktionsreferenz, Template-Funktionen, def, undef, set, let, default, if, foreach, for, while, do, switch, section, include, cache-block, set-block, append-block, run-once, ldelim, rdelim, sequence, attribute_view_gui, node_view_gui, content_view_gui, Debug-Funktionen, function reference, template functions, debug functions
type: help
---
# Funktionsreferenz

Template-**Funktionen** sind die Tags in geschweiften Klammern, die ein Template steuern, im Gegensatz zu Operatoren, die
Werte berechnen. Diese Seite listet sie danach auf, wo sie registriert sind.

## Inhalt

1. [Die Template-Bibliothek](#die-template-bibliothek)
2. [Der Kernel: gui-Funktionen](#der-kernel-gui-funktionen)
3. [Erweiterungen](#erweiterungen)

## Die Template-Bibliothek

Registriert in `lib/eztemplate/classes/eztemplateautoload.php`:

| Funktion | Syntax | Seite |
|---|---|---|
| `def`, `undef` | `{def $a=1 $b=2}`, `{undef $a}` | [Variablen und def](../../../../eng-us/help/designers/templates/variables-and-def.md) |
| `set`, `let`, `default` | `{set $a=2}`, `{default name=value}` | [Variablen und def](../../../../eng-us/help/designers/templates/variables-and-def.md) |
| `if` (`elseif`, `else`) | `{if cond}...{elseif cond}...{else}...{/if}` | [If und Bedingungen](if-und-bedingungen.md) |
| `foreach` | `{foreach $list as $key => $item offset 1 max 3 reverse}...{/foreach}` | [Foreach-Schleifen](foreach-schleifen.md) |
| `for` | `{for 1 to 5 as $i}...{/for}` | [For-Schleifen](for-schleifen.md) |
| `while` | `{while cond}...{/while}` | [While- und do-Schleifen](../../../../eng-us/help/designers/templates/while-and-do-loops.md) |
| `do` | `{do}...{/do while cond}` | [While- und do-Schleifen](../../../../eng-us/help/designers/templates/while-and-do-loops.md) |
| `delimiter`, `break`, `continue`, `skip` | innerhalb von Schleifen | [Foreach-Schleifen](foreach-schleifen.md) |
| `switch` (`case`) | `{switch match=$x}{case match=1}...{/case}{case}...{/case}{/switch}` | [Switch und case](../../../../eng-us/help/designers/templates/switch-case.md) |
| `section` (`section-else`, `section-include`, `section-exclude`) | `{section loop=$list}...{/section}` | [Section (veraltet)](../../../../eng-us/help/designers/templates/section-legacy.md) |
| `include` | `{include uri='design:path.tpl' name=value}` | [Include](include.md) |
| `cache-block` | `{cache-block keys=... expiry=...}...{/cache-block}` | [Cache-Block](cache-block.md) |
| `set-block`, `append-block` | `{set-block variable=$x}...{/set-block}` | [Set-block und append-block](../../../../eng-us/help/designers/templates/set-block-and-append-block.md) |
| `run-once` | `{run-once}...{/run-once}` | [Run-once](../../../../eng-us/help/designers/templates/run-once.md) |
| `ldelim`, `rdelim` | `{ldelim}` gibt `{` aus | [Literal und Begrenzer](literal-und-begrenzer.md) |
| `literal` | `{literal}...{/literal}` | [Literal und Begrenzer](literal-und-begrenzer.md) |
| `sequence` | `{sequence name=seq loop=array( ... )}` | ältere wechselnde Werte; ziehen Sie `foreach ... sequence` vor |
| `debug-timing-point`, `debug-accumulator`, `debug-log`, `debug-trace` | `{debug-log msg='...'}` | [Template-Debugging](../../../../eng-us/help/designers/templates/template-debugging.md) |
| `tool_bar` | `{tool_bar name=... view=...}` | Werkzeugleisten aus `toolbar.ini` |
| `menu` | `{menu name=...}` | Menüs aus `menu.ini` |

## Der Kernel: gui-Funktionen

Registriert in `kernel/common/eztemplateautoload.php` (`eZObjectForwardInit`); jede leitet an ein Template weiter, das nach
dem erhaltenen Objekt gewählt wird:

| Funktion | Zeichnet | Template-Ordner |
|---|---|---|
| `attribute_view_gui` | ein Feld zum Ansehen | `content/datatype/view/` (`collect/` für Information Collector) ([attribute_view_gui](attribute-view-gui.md)) |
| `attribute_edit_gui` | das Bearbeitungsformular eines Felds | `content/datatype/edit/` |
| `attribute_result_gui`, `attribute_pdf_gui`, `attribute_diff_gui` | gesammelte Ergebnisse, PDF, Versionsunterschiede | `content/datatype/result/`, `pdf/`, `diff/` |
| `node_view_gui` | einen Knoten in einem Ansichtsmodus | `node/view/<view>.tpl` ([node_view_gui](../../../../eng-us/help/designers/templates/node-view-gui.md)) |
| `content_view_gui` | ein Objekt in einem Ansichtsmodus | `content/view/<view>.tpl` ([content_view_gui](content-view-gui.md)) |
| `related_view_gui`, `content_pdf_gui`, `content_version_view_gui` | verwandte Objekte, PDF, Versionen | |
| `class_attribute_view_gui`, `class_attribute_edit_gui` | Klassendefinitionen (Administration) | |
| `event_view_gui`, `event_edit_gui` | Workflow-Ereignisse (Administration) | |
| `collaboration_*`, `shop_account_view_gui` | Teile für Zusammenarbeit und Shop-Konto | |

## Erweiterungen

- `explblock` (explayouts) zeichnet einen Layout-Block.
- `powercontent_create_gui`, `powercontent_attribute_create_gui` (nxc_powercontent, wo aktiv).

## Verwandte Seiten

- [Operatorreferenz](../../../../eng-us/help/designers/templates/operator-reference.md)
- [TPL-Syntax: Grundlagen](../../../../eng-us/help/designers/templates/tpl-syntax-basics.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
