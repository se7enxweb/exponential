---
title: Eigene Template-Funktionen
audience: developer, app-creator
category: development
order: 500
summary: Eine Template-Funktion (ein Tag in geschweiften Klammern mit benannten Parametern und wahlweise einem Rumpf) in einer Erweiterung schreiben - functionList, hasChildren, attributeList, process, Parameter auswerten, den Rumpf rendern, Registrierung in eztemplateautoload.php, Hinweise zur Kompilierzeit - und die Funktionen des Kernels erweitern oder überschreiben, mit einer Beispielfunktion auf alpha gerendert.
keywords: Template-Funktion, eigene Funktion, functionList, hasChildren, attributeList, process, processNode, elementValue, eZTemplateFunctionArray, function_names, function_attributes, functionTemplateHints, Funktion überschreiben, template function, custom function, override function
type: help
---
# Eigene Template-Funktionen

Eine Template-Funktion ist ein Tag wie `{include ...}`, `{cache-block}...{/cache-block}` oder `{def ...}`: Sie nimmt benannte Parameter, kann
einen Rumpf haben und schreibt Ausgabe oder ändert Variablen. Operatoren dagegen nehmen einen Wert und geben einen zurück
([Funktionen und Operatoren](funktionen-und-operatoren.md)). Diese Seite zeigt, wie eine Erweiterung eine Funktion hinzufügt und was die Engine
erwartet, aus `lib/eztemplate/classes/eztemplate.php` und `lib/eztemplate/classes/eztemplatemultipassparser.php`.

## Inhalt

1. [Wann Sie eine Funktion schreiben](#wann-sie-eine-funktion-schreiben)
2. [Die Beispielfunktion](#die-beispielfunktion)
3. [Die Methoden](#die-methoden)
4. [Sie registrieren](#sie-registrieren)
5. [Sie nutzen](#sie-nutzen)
6. [Hinweise zur Kompilierzeit](#hinweise-zur-kompilierzeit)
7. [Funktionen des Kernels erweitern und überschreiben](#funktionen-des-kernels-erweitern-und-überschreiben)

## Wann Sie eine Funktion schreiben

Schreiben Sie eine Funktion, wenn das Tag einen Rumpf braucht (das Markup zwischen öffnendem und End-Tag umschließen, wiederholen oder
auffangen), Variablen im Namensraum des Aufrufers setzen muss oder steuert, was gerendert wird. Für Wert hinein, Wert heraus schreiben Sie einen
[Operator](../template-operatoren/einen-operator-schritt-fuer-schritt-anlegen.md); zum Lesen von Daten eine
[Fetch-Funktion](../template-operatoren/wie-fetch-funktionen-arbeiten.md). Für wiederverwendbares Markup ohne PHP ist ein `{include}` eines
Template-Teils einfacher als beides.

## Die Beispielfunktion

`{c11_box title=... class=...}body{/c11_box}` umschließt ihren Rumpf mit einem `<section>` samt maskierter Überschrift. Die Klasse, wie eine
Erweiterung sie in `extension/myextension/autoloads/c11examplefunction.php` ausliefern würde:

```php
class C11ExampleFunction
{
    /** The function names this class answers */
    function functionList()
    {
        return array( 'c11_box' );
    }

    /** true: the function has a body and an end tag {/c11_box} */
    function hasChildren()
    {
        return true;
    }

    function process( $tpl, &$textElements, $functionName, $functionChildren, $functionParameters,
                      $functionPlacement, $rootNamespace, $currentNamespace )
    {
        if ( !isset( $functionParameters['title'] ) )
        {
            $tpl->warning( $functionName, 'Missing parameter: title', $functionPlacement );
            return;
        }
        // parameters arrive unevaluated: evaluate each one
        $title = $tpl->elementValue( $functionParameters['title'], $rootNamespace, $currentNamespace, $functionPlacement );
        $class = isset( $functionParameters['class'] )
               ? $tpl->elementValue( $functionParameters['class'], $rootNamespace, $currentNamespace, $functionPlacement )
               : 'box';

        // render the body in the caller's namespaces
        $body = array();
        foreach ( (array)$functionChildren as $child )
            $tpl->processNode( $child, $body, $rootNamespace, $currentNamespace );

        $textElements[] = '<section class="' . htmlspecialchars( $class ) . '"><h2>' . htmlspecialchars( $title ) . '</h2>'
                        . implode( '', $body ) . '</section>';
    }
}
```

## Die Methoden

| Methode | Pflicht | Was die Engine damit tut |
|---|---|---|
| `functionList()` | ja | registriert das Objekt beim Laden der Klasse unter jedem zurückgegebenen Namen (und nimmt diese Namen jeder anderen Klasse wieder ab, wie bei Operatoren) |
| `hasChildren()` | ja | `true`: Das Tag hat einen Rumpf und ein End-Tag; `false`: ein Einzel-Tag. Darf für Klassen mit mehreren Funktionen ein Array `name => bool` zurückgeben. Der Parser ruft sie auf, ohne zu prüfen, ob sie existiert (`eZTemplate::hasChildren()`), eine Klasse ohne sie endet also in einem PHP-Fehler |
| `attributeList()` | nein | Namen der im Rumpf erlaubten Unter-Tags, `name => hasChildren`, etwa `elseif`/`else` von `if`, `delimiter`/`skip`/`break` von `foreach`, `case` von `switch` |
| `process()` | ja | rendert die Funktion, siehe unten |
| `functionTemplateHints()` | nein | sagt dem Compiler, ob die Funktion in kompilierten Code umgewandelt werden kann, siehe unten |

`process( $tpl, &$textElements, $functionName, $functionChildren, $functionParameters, $functionPlacement, $rootNamespace, $currentNamespace )`:

- `$textElements`: Hängen Sie Ausgabezeichenketten an dieses Array an (nicht mit echo ausgeben);
- `$functionChildren`: der gelesene Rumpf, eine Liste von Knoten; rendern Sie sie mit
  `$tpl->processNode( $child, $textElements, $rootNamespace, $currentNamespace )`, in Ihr eigenes Array, wenn Sie den Rumpf nachbearbeiten
  wollen, wie das Beispiel;
- `$functionParameters`: die benannten Parameter **unausgewertet**, nach Namen geordnet; werten Sie sie mit
  `$tpl->elementValue( $functionParameters['name'], $rootNamespace, $currentNamespace, $functionPlacement )` aus. Ein Parameter, den das Template
  nicht geschrieben hat, ist nicht gesetzt;
- Variablen: `$tpl->setVariable( $name, $value, $currentNamespace )` und `$tpl->variable( $name, $currentNamespace )` lesen und schreiben die
  Variablen des Aufrufers (`eZTemplateBlockFunction` nutzt sie für `set-block`);
- `$functionPlacement`: wo das Tag steht, für `$tpl->warning()` und `$tpl->error()`.

## Sie registrieren

In `extension/myextension/autoloads/eztemplateautoload.php`, neben etwaigen Operatoren:

```php
<?php
$eZTemplateFunctionArray = array();
$eZTemplateFunctionArray[] = array( 'script' => 'extension/myextension/autoloads/c11examplefunction.php',
                                    'class' => 'C11ExampleFunction',
                                    'function_names' => array( 'c11_box' ) );
```

Schlüssel: `class` oder `function` (eine PHP-Funktion, die das Objekt zurückgibt), `script`, `function_names` oder `function_names_function`
und `function_attributes` (die Namen der Unter-Tags, damit der Parser sie kennt, bevor die Klasse geladen ist; die Bibliothek registriert
`elseif` und `else` von `if` auf diese Weise). Die Erweiterung braucht außerdem `[TemplateSettings] ExtensionAutoloadPath[]=myextension` in
ihrer `settings/site.ini.append.php`; siehe [Operatoren registrieren](../template-operatoren/operatoren-registrieren.md), das beide Arrays
behandelt.

## Sie nutzen

Nicht als installierte Erweiterung auf alpha ausgeführt: Das Skript, das diese Beispiele renderte, registrierte den Eintrag oben im Speicher an
der Template-Engine, mit der Klasse in einem Arbeitsordner. Interpretierte und kompilierte Ergebnisse waren gleich.

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )}
{c11_box title=$node.name class='teaser'}<p>{$node.children_count} children</p>{/c11_box}
{c11_box title='Plain'}<p>Default class</p>{/c11_box}
```

Ausgabe auf alpha:

```text
<section class="teaser"><h2>Fit &amp; Healthy</h2><p>16 children</p></section><section class="box"><h2>Plain</h2><p>Default class</p></section>
```

Ein fehlender Parameter, von der eigenen Prüfung der Funktion behandelt:

```tpl
{c11_box}<p>no title</p>{/c11_box}
```

Ausgabe auf alpha:

```text
(no output)
```

Von der Template-Engine protokolliert:

```text
WARNING c11_box: Missing parameter: title
```

Ein Funktionsname, den niemand registriert hat: Der Parser behandelt ein unbekanntes Tag als Block (sein Standard, wenn keine Klasse auf den
Namen antwortet), also wird auch der Rumpf verschluckt, und die Engine protokolliert eine Warnung:

```tpl
{c11_unknown title='x'}<p>body</p>{/c11_unknown}
```

Ausgabe auf alpha:

```text
(no output)
```

Von der Template-Engine protokolliert:

```text
WARNING : Function "c11_unknown" is not registered
```

## Hinweise zur Kompilierzeit

`functionTemplateHints()` gibt pro Funktionsname Schlüssel zurück wie `parameters`, `static`, `tree-transformation` (die Funktion schreibt ihren
eigenen Knoten zur Kompilierzeit über `templateNodeTransformation()` um, wie `eZTemplateDelimitFunction` es für `ldelim`/`rdelim` tut) und die
zugehörigen Einstellungen zur Transformation, die die Funktionen der Bibliothek nutzen. Eine Funktion ohne Hinweise, wie das Beispiel, wird zu
einem Laufzeitaufruf ihres `process()` kompiliert, was dasselbe Ergebnis ergibt und die sichere Wahl ist. Fügen Sie Hinweise nur mit einem Test
hinzu, der interpretierte und kompilierte Ausgabe vergleicht; die Gründe sind dieselben wie bei Operatoren
([operatorTemplateHints](../template-operatoren/referenz-der-operatorklassen.md#operatortemplatehints)).

## Funktionen des Kernels erweitern und überschreiben

Funktionsnamen folgen genau den Regeln für Operatoren: Der letzte Eintrag in einer `eztemplateautoload.php` für einen Namen gewinnt, und eine
Klasse registriert beim Laden alle Namen ihres `functionList()`
([Reihenfolge der Registrierung und Overrides](../template-operatoren/registrierungsreihenfolge-und-ueberschreibungen.md)). Auf alpha ist kein Funktionsname
doppelt registriert. Um eine Funktion des Kernels oder der Bibliothek aus einer Erweiterung zu ändern:

- leiten Sie von der Klasse ab, der der Name gehört (zum Beispiel `eZTemplateBlockFunction`, der `set-block`, `append-block` und `run-once`
  gehören), überschreiben Sie `process()` für den Namen, den Sie ändern, und rufen Sie für die anderen `parent::process()` auf;
- registrieren Sie die Unterklasse für **alle** Namen der Elternklasse, und halten Sie `function_attributes` und `hasChildren()` mit der
  Elternklasse übereinstimmend;
- entfernen Sie den Eintrag in `functionTemplateHints()` der Elternklasse für den Namen, den Sie ändern, sonst behalten kompilierte Templates
  den Code der Elternklasse ([der Fall bei Operatoren, auf alpha gezeigt](../template-operatoren/kernel-operatoren-ueberschreiben.md#den-code-zur-übersetzungszeit-entfernen-den-sie-ändern)).

Nicht mit einer Funktion auf alpha vorgeführt: Das Überschreiben eines Funktionsnamens nutzt denselben Codepfad wie bei Operatoren
(`registerAutoloadFunctions()`, `registerFunctionsInternal()`), den [Operatoren des Kernels überschreiben](../template-operatoren/kernel-operatoren-ueberschreiben.md)
gerendert zeigt. Das Überschreiben der eigenen Kontrollfunktionen der Sprache (`if`, `foreach`, `def`, `include`) ändert jedes Template auf dem
Siteaccess; ziehen Sie einen neuen Funktionsnamen vor.

## Verwandte Seiten

- [Funktionen und Operatoren](funktionen-und-operatoren.md)
- [Referenz der Funktionen](../designer/vorlagen/funktionsreferenz.md): jede Funktion der Bibliothek und des Kernels
- [Operatoren registrieren](../template-operatoren/operatoren-registrieren.md)
- [Die Schnittstelle der Operatorklasse](../template-operatoren/referenz-der-operatorklassen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
