---
title: Template-Funktionen
audience: developer, app-creator, designer
category: extension-development
order: 150
summary: Block-Tags wie {mysite_card}...{/mysite_card} - Registrierung, der Vertrag von process(), Auswerten von Parametern und Kindknoten, ein geprüftes Beispiel und wann ein Operator oder ein Include besser ist.
keywords: Template-Funktion, Block-Tag, eigenes Tag, Parameter, Kindknoten, kompilierte Templates, template function, eZTemplateFunctionArray, function_names, process, functionList, hasChildren, elementValue, processNode, functionTemplateHints, block tag, custom tag
type: help
---
# Template-Funktionen

Teil 3, Kapitel 15. Operatoren berechnen Werte. **Template-Funktionen** sind Tags mit eigener Syntax, oft mit einem
Rumpf: `{foreach}`, `{cache-block}` und `{debug-log}` sind Funktionen des Kernels. Eine Erweiterung kann eigene
hinzufügen. Sie sind seltener als Operatoren, und meist ist ein Operator oder ein `{include}` einfacher, aber für eine
Hülle um einen Block Template-Code ist eine Funktion das saubere Werkzeug. Das Beispiel unten wurde von der
Template-Engine gerendert.

## Registrierung

In derselben Datei `autoloads/eztemplateautoload.php`, die auch die Operatoren registriert (der Anschluss ist
`[TemplateSettings] ExtensionAutoloadPath[]`, siehe [Template-Operatoren](template-operatoren.md)):

```php
<?php
$eZTemplateFunctionArray = array();
$eZTemplateFunctionArray[] = array(
    'script' => 'extension/mysite_hello/classes/mysitehellocardfunction.php',
    'class' => 'mysiteHelloCardFunction',
    'function_names' => array( 'mysite_card' )
);
```

`eZTemplate::registerAutoloadFunctions()` registriert die Namen; die Klasse wird bei der ersten Verwendung geladen. Die
Erweiterung `explayouts` registriert ihre Funktion `explblock` genau auf diese Weise.

## Die Klasse

```php
<?php
/**
 * Template function of mysite_hello:
 *   {mysite_card title=$node.name}...content...{/mysite_card}
 * wraps its content in a card with an escaped title.
 */
class mysiteHelloCardFunction
{
    public function functionList()
    {
        return array( 'mysite_card' );
    }

    public function hasChildren()
    {
        return true;
    }

    public function process( $tpl, &$textElements, $functionName, $functionChildren, $functionParameters, $functionPlacement, $rootNamespace, $currentNamespace )
    {
        $title = '';
        if ( isset( $functionParameters['title'] ) )
        {
            $title = (string)$tpl->elementValue( $functionParameters['title'], $rootNamespace, $currentNamespace, $functionPlacement );
        }

        $children = array();
        if ( is_array( $functionChildren ) )
        {
            foreach ( $functionChildren as $child )
            {
                $tpl->processNode( $child, $children, $rootNamespace, $currentNamespace );
            }
        }

        $textElements[] = '<section class="mysite-card"><h2>' . htmlspecialchars( $title, ENT_QUOTES, 'UTF-8' ) . '</h2>'
                        . implode( '', $children ) . '</section>';
    }
}
```

| Methode | Bedeutung |
|---|---|
| `functionList()` | die Funktionsnamen, die diese Klasse verarbeitet |
| `hasChildren()` | `true`, wenn das Tag einen Rumpf und ein schließendes Tag hat |
| `process( ... )` | erzeugt die Ausgabe: hängen Sie Zeichenketten an `$textElements` an |
| `$tpl->elementValue( $functionParameters[...], ... )` | wertet einen Parameter aus (eine Variable, eine Zeichenkette, einen Ausdruck) |
| `$tpl->processNode( $child, $out, ... )` | rendert einen Kindknoten des Rumpfs nach `$out` |
| `$tpl->missingParameter( $function, $name )`, `$tpl->error( $function, $text, $placement )` | melden Probleme in der Debug-Ausgabe |

## Das Ergebnis

```text
{def $t = 'Tom & <Jerry>'}
{mysite_card title=$t}<p>{mysite_greet( 'Ada' )}</p>{/mysite_card}
```

ergibt

```text
<section class="mysite-card"><h2>Tom &amp; &lt;Jerry&gt;</h2><p>Hello, Ada!</p></section>
```

Weil die Funktion HTML erzeugt, maskiert sie den Titel selbst.

## Kompilierte Templates

Templates werden zu PHP kompiliert. Eine Funktion ohne Kompilierhinweise wird zur Laufzeit über `process()` aufgerufen
(`eZTemplateCompiler` prüft `functionTemplateHints()` und weicht darauf aus, wenn sie fehlt oder keine
`tree-transformation` hat). Das ist korrekt und meist schnell genug. Die eigenen Funktionen des Kernels und
`explblock` fügen `functionTemplateHints()` und eine `templateNodeTransformation()` hinzu, um direkt zu PHP zu
kompilieren; lesen Sie `lib/eztemplate/classes/eztemplatedelimitfunction.php` als kleines Beispiel aus dem Kernel,
bevor Sie es versuchen.

## Wann Sie keine Funktion schreiben sollten

| Sie möchten | Besser |
|---|---|
| einen Wert | einen Operator |
| wiederverwendbares HTML mit Parametern | `{include uri='design:parts/card.tpl' title=...}` |
| eine Hülle mit Logik um einen Rumpf | eine Template-Funktion |

## RAD

**Assistent für Template-Extensions** (`/setup/templateoperator`) kann das Gerüst einer Template-Funktion schreiben.

## Verwandte Seiten

- Zurück: [Fetch-Funktionen](fetch-funktionen.md)
- Weiter: [Datentypen](datentypen.md)
- [Eingebaute Template-Funktionen](../teil-8-referenz/referenz-der-template-funktionen.md)
- [eZTemplate aus PHP](../teil-8-referenz/eztemplate.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
