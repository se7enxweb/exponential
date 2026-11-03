# Smarty-Templates: Operatoren und Funktionen für .stpl schreiben

Diese Seite ist für PHP-Entwickler, die Templates etwas Neues geben wollen: einen Wert, der berechnet wird, ein Stück
Markup, das gerendert wird. In Exponential tun Sie das mit einem Template-Operator oder einer Template-Funktion, die
Ihre Extension registriert. Einmal geschrieben, funktioniert er in `.tpl`- und in `.stpl`-Templates: Die Brücke findet
jeden Operator und jede Funktion, die das Template-System kennt. Diese Seite erklärt, wie die Brücke sie findet, welche
Entscheidungen bestimmen, wie sie aus Smarty aufgerufen werden, und geht einen Operator jeder Art und eine Funktion
durch, mit der Registrierung und einem Test.

## Wie die Brücke Operatoren und Funktionen findet

`expSmartyExtension` (`lib/smarty/classes/expsmartyextension.php`) ist eine Smarty-5-Extension. Smarty fragt sie nach
einem Modifikator, einem Funktions-Tag oder einem Block-Tag, sobald ein kompiliertes Template eines braucht, das Smarty
selbst nicht hat:

- **Modifikatoren** (`$x|name:a`, und `name(a, b)` in einem Ausdruck, das Smarty als `a|name:b` kompiliert):
  1. zuerst Smartys eigene Modifikatoren;
  2. die Helfer der Brücke: `fetch`, `expattr`, `expvar`, `expvoid`, `expsequence`, `expslice`, `expsectionloop`,
     `exprange`, `expcolumn`;
  3. `exp_<name>`, wenn es keinen Operator mit diesem vollen Namen gibt: der Operator `<name>`;
  4. jeder im Template-System des Siteaccess registrierte Operator (`eZTemplate::factory()->Operators`), über seinen
     Namen. Jeder Aufruf geht an `expSmartyRenderer::callOperator()`, das den Operator über
     `eZTemplate::processOperator()` ausführt, genau wie ein `.tpl`.
- **Funktions-Tags** (`{name a=... b=...}`): die Tags der Brücke, die Namen in `[SmartySettings] FunctionList[]`, dann
  jede registrierte Template-Funktion ohne Rumpf (`expSmartyFunction`, das sie über ein erzeugtes `.tpl` ausführt).
- **Block-Tags** (`{name}...{/name}`): die Blöcke der Brücke, dann jede registrierte Template-Funktion
  (`expSmartyBlockFunction`, das der Funktion den gerenderten Rumpf als ihre Kinder übergibt).

Die Antworten werden pro PHP-Prozess gemerkt. Es gibt keine Einstellung, um eigene Smarty-Plugins zu registrieren (die
Smarty-Instanz wird von `expSmartyRenderer::smarty()` nur mit der Extension der Brücke erzeugt); ein Operator oder eine
Funktion ist der unterstützte Weg, und er hat den Vorteil, in beiden Engines zu funktionieren.

## Den Namen wählen

- **Stellen Sie Ihre Extension voran**: `myext_initials`, `myext_badge`. Namen gelten global im Siteaccess; eine später
  registrierte Extension ersetzt einen Operator gleichen Namens (das `image` des Media-Themes ersetzt das des Kernels).
- **Vermeiden Sie die Namen von Smartys Modifikatoren** (`count`, `escape`, `replace`, `truncate`, ... die vollständige
  Liste steht in [der Anleitung zu den Operatoren](smarty-templates-operatoren.md#das-präfix-exp)): Ein solcher Operator
  funktioniert, aber Autoren von `.stpl` müssen ihn mit `exp_` davor schreiben.
- **Ein Name, der mit `exp_` beginnt, wird mit diesem Namen aufgerufen** (`exp_path`, `exp_environment`). Verwenden Sie
  ihn nur, wenn Ihr Operator wirklich `exp_...` heißen soll; nennen Sie nie einen `exp_<ein Smarty-Modifikator>`.
- **Kein Bindestrich im Namen einer Funktion**: `{myext-badge}` kann kein Smarty-Tag sein. Buchstaben, Ziffern und
  Unterstriche.

## Wählen, wie der Operator seine Eingabe nimmt

Das ist die eine Entscheidung, die für `.stpl` zählt. Ein Smarty-Modifikator und ein Smarty-Funktionsaufruf sind
derselbe Aufruf (`op(a, b)` ist `a|op:b`), also muss die Brücke wissen, ob der erste Wert die Eingabe des Operators ist
oder sein erster Parameter. Sie liest die `operatorTemplateHints()` des Operators
(`expSmartyRenderer::operatorInputMode()`):

| Ihre Hinweise für den Operator | Modus | In einem `.stpl` |
|---|---|---|
| keine, oder `'input' => true` ohne `input-as-parameter`, oder `'input-as-parameter' => 'always'` | **input** | `$x\|op:a` übergibt `$x` als Operatorwert; ohne Eingabe schreiben Sie `op(null, a)` |
| `'input' => false` | **param** | der erste Wert wird zum ersten Parameter: `op(a, b)` = `a\|op:b` |
| `'input-as-parameter' => true` | **param** | ebenso; Ihr `modify()` muss den ersten Parameter lesen, wenn der Operatorwert `null` ist |
| Klasse erweitert `eZTemplateControlOperator` | **param** | wie `cond` und `first_set` |
| keine, und der Name steht in `[SmartySettings] ParameterOperatorList[]` von `smarty.ini` | **param** | `op(a, b)` wie im `.tpl`; siehe unten |

Wählen Sie **input**, wenn der Operator einen Wert umformt (`$name|myext_initials`), **param**, wenn er aus mehreren
Werten rechnet, von denen keiner besonders ist (`myext_clamp($value, 1, 10)`). Deklarieren Sie
`'input-as-parameter' => true` nur, wenn `modify()` seine Eingabe zur Laufzeit wirklich aus dem ersten Parameter liest:
`nl2br` und `indent` des Kernels deklarieren es, lesen aber den Operatorwert, und die Brücke muss sie als Ausnahmen
führen (`expSmartyRenderer::INPUT_AT_RUN_TIME`). Ihr Operator sollte diese Liste nicht brauchen.

### Operatoren ohne Hinweise, die ihre Parameter lesen

Ein Operator ohne Template-Hinweise ist für die Brücke ein **input**-Operator, da ihr nichts anderes gesagt wird. Das
ist falsch für einen Operator, dessen `modify()` den Operatorwert nie liest und allein aus seinen Parametern arbeitet,
etwa eine Abfrage `myext_setting('Group', 'Name')`: In einem `.stpl` würde `{myext_setting('Group', 'Name')}` `'Group'`
als Eingabe und `'Name'` als ersten Parameter übergeben. Zwei Wege, das zu beheben:

- geben Sie dem Operator Hinweise: `'input' => false` (die bessere Lösung, die auch der Template-Compiler verwendet);
- oder führen Sie einen Operator, den Sie nicht ändern können, in `settings/smarty.ini` (oder in der
  `settings/smarty.ini.append.php` einer Extension) auf:

  ```ini
  [SmartySettings]
  ParameterOperatorList[]=myext_setting
  ```

Ein aufgeführter Operator ist ein **param**-Operator: `{myext_setting('Group', 'Name')}` und
`{'Group'|myext_setting:'Name'}` sind der Aufruf des `.tpl`-`myext_setting( 'Group', 'Name' )`. Ein `null` davor,
`myext_setting(null, 'Group', 'Name')` (die Form, die der Konverter für einen input-Operator schreibt), wird
weggelassen, ebenso ein per Pipe übergebenes `null`, sodass Templates, die konvertiert wurden, bevor der Operator
aufgeführt war, weiter funktionieren; `myext_setting()` hat keine Parameter. Der Konverter lässt einen Wert weg, der in
einem `.tpl` per Pipe an einen aufgeführten Operator übergeben wird (`$x|myext_setting('Group', 'Name')`), da das
Template-System ihn ebenfalls ignoriert.

Der Kernel führt seine eigenen Operatoren dieser Art in `expSmartyRenderer::PARAMETER_OPERATORS` auf: `ezhttp`,
`ezhttp_hasvariable`, `icon_info`, `alphabet`, `content_structure_tree`, `csp_nonce`, `exp_demo_mode`,
`exp_environment`, `exp_environment_badge`, `ezmodule`, `eztoc`, die Rollen-Operatoren (`has_role` ...),
`installation_name`, `is_production_system`, `language_switcher`, `module_params`, `pdf`, `topmenu`, die
Transfer-Operatoren (`transfer_tools` ...), `treemenu` und `exp_smarty_render`. Die Einstellung ergänzt diese Liste.

Die Liste wird einmal pro Prozess gelesen: Nach einer Änderung deployen Sie (was die PHP-Worker neu startet). Führen
Sie nur Operatoren auf, deren `modify()` `$operatorValue` nie liest; ein Operator, der ihn in einem Fall liest und in
einem anderen nicht, ist ein input-Operator.

Werte erreichen `modify()` so, wie das Template-System sie hat: Smartys Proxys werden durch die echten Objekte ersetzt,
Arrays werden ausgepackt, und `expvoid()` kommt als leerer Parameter an (der Standardwert des Parameters gilt). Was
`modify()` zurückgibt, wird für Smarty wieder verpackt: Ein Objekt mit `attribute()` und `hasAttribute()` wird mit
Punkten lesbar (`$result.name`).

## Beispiel 1: ein input-Operator

`extension/myext/classes/myextinitialsoperator.php`:

```php
<?php
/**
 * {$name|myext_initials}, {$name|myext_initials:'.'}: die Initialen eines Namens.
 */
class myExtInitialsOperator
{
    function operatorList()
    {
        return array( 'myext_initials' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'myext_initials' => array(
            'separator' => array( 'type' => 'string', 'required' => false, 'default' => '' ),
        ) );
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace,
                     &$operatorValue, $namedParameters, $placement )
    {
        $initials = array();
        foreach ( preg_split( '/\s+/u', trim( (string)$operatorValue ), -1, PREG_SPLIT_NO_EMPTY ) as $word )
            $initials[] = mb_strtoupper( mb_substr( $word, 0, 1 ) );
        $operatorValue = implode( $namedParameters['separator'], $initials );
    }
}
```

Keine Template-Hinweise: ein **input**-Operator. In einem `.tpl` und einem `.stpl`:

```
{$node.object.owner.name|myext_initials}          {* .tpl: JD *}
{$node.object.owner.name|myext_initials('.')}     {* .tpl: J.D *}
```

```smarty
{$node.object.owner.name|myext_initials}          {* .stpl: JD *}
{$node.object.owner.name|myext_initials:'.'}      {* .stpl: J.D *}
{myext_initials(null, 'Jane Doe')}                {* .stpl, ohne Eingabe: ein leeres Ergebnis, wie das .tpl myext_initials('Jane Doe') *}
```

## Beispiel 2: ein param-Operator

```php
<?php
/**
 * myext_clamp( value, min, max ): der Wert, zwischen min und max gehalten.
 */
class myExtClampOperator
{
    function operatorList()
    {
        return array( 'myext_clamp' );
    }

    function operatorTemplateHints()
    {
        return array( 'myext_clamp' => array( 'input' => false, 'output' => true, 'parameters' => 3 ) );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'myext_clamp' => array(
            'value' => array( 'type' => 'integer', 'required' => true, 'default' => 0 ),
            'min' => array( 'type' => 'integer', 'required' => true, 'default' => 0 ),
            'max' => array( 'type' => 'integer', 'required' => true, 'default' => 0 ),
        ) );
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace,
                     &$operatorValue, $namedParameters, $placement )
    {
        $operatorValue = max( (int)$namedParameters['min'], min( (int)$namedParameters['max'], (int)$namedParameters['value'] ) );
    }
}
```

`'input' => false`: ein **param**-Operator. `myext_clamp($page, 1, $page_count)` funktioniert in beiden Engines gleich.
In einem `.stpl` ist `$page|myext_clamp:1:$page_count` derselbe Aufruf; in einem `.tpl` würde das Pipe-Zeichen
ignoriert, also schreiben Sie in Templates, die für beide gedacht sind, die Funktionsform.

## Sie registrieren

`extension/myext/autoloads/eztemplateautoload.php`:

```php
<?php

$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array( 'script' => 'extension/myext/classes/myextinitialsoperator.php',
                                    'class' => 'myExtInitialsOperator',
                                    'operator_names' => array( 'myext_initials' ) );
$eZTemplateOperatorArray[] = array( 'script' => 'extension/myext/classes/myextclampoperator.php',
                                    'class' => 'myExtClampOperator',
                                    'operator_names' => array( 'myext_clamp' ) );
```

`extension/myext/settings/site.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[TemplateSettings]
ExtensionAutoloadPath[]=myext

*/ ?>
```

Führen Sie jeden Namen, den die Klasse behandelt, in `operator_names` auf: Die Registrierung erfolgt erst bei Bedarf,
und ein Name, der dort fehlt, wird nur auf Seiten aufgelöst, auf denen zuvor ein anderer Name der Klasse verwendet
wurde. Aktivieren Sie dann die Extension, erzeugen Sie die Autoloads neu, leeren Sie die Caches und laden Sie die Server
neu:

```bash
php bin/php/ezpgenerateautoloads.php -e
./console exp:velocity deploy --allow-root-user
```

Die Brücke merkt sich pro Prozess, welche Namen es gibt, also kennt ein Worker, der gestartet wurde, bevor die
Extension aktiv war, den neuen Operator erst nach einem Neustart; das Deploy erledigt das.

## Beispiel 3: eine Template-Funktion und ein Block

Eine Funktion rendert Markup mit benannten Parametern. `extension/myext/classes/myextbadgefunction.php`:

```php
<?php
/**
 * {myext_badge text='New'}: ein Badge.
 */
class myExtBadgeFunction
{
    function functionList()
    {
        return array( 'myext_badge' );
    }

    function hasChildren()
    {
        return false;
    }

    function process( $tpl, &$textElements, $functionName, $functionChildren, $functionParameters,
                      $functionPlacement, $rootNamespace, $currentNamespace )
    {
        $text = isset( $functionParameters['text'] )
            ? (string)$tpl->elementValue( $functionParameters['text'], $rootNamespace, $currentNamespace, $functionPlacement )
            : '';
        $textElements[] = '<span class="myext-badge">' . htmlspecialchars( $text, ENT_QUOTES ) . '</span>';
    }
}
```

Registriert mit `$eZTemplateFunctionArray[] = array( 'script' => ..., 'class' => 'myExtBadgeFunction',
'function_names' => array( 'myext_badge' ) );` in derselben Autoload-Datei. In beiden Engines ist es
`{myext_badge text='New'}`; aus einem `.stpl` läuft es über ein erzeugtes `.tpl` in einem Namensraum, der nur seine
Parameter enthält, also muss es alles, was es braucht, als Parameter nehmen.

Gibt `hasChildren()` `true` zurück, hat die Funktion einen Rumpf, `{myext_box}...{/myext_box}`, und die Brücke macht
daraus einen Smarty-Block: Der Rumpf wird zuerst gerendert, und `process()` bekommt ihn als einen Textknoten in
`$functionChildren` (`array( array( eZTemplate::NODE_TEXT, false, '<the rendered body>' ) )`). Eine Funktion, die ihre
Kinder mit `$tpl->processNode()` verarbeitet oder deren Text liest, funktioniert; eine, die deren Struktur untersucht
(nach einer bestimmten Kind-Funktion sucht), sieht in einem `.stpl` nur den Text. `ExplBlockFunction` aus
`extension/explayouts` ist eine echte Block-Funktion, die in beiden funktioniert.

## In beiden Engines testen

Rendern Sie denselben Fall als `.tpl` und als `.stpl` über das Template-System, so wie
`tests/tests/lib/smarty/expSmartyTemplateRenderTest.php` es tut: Schreiben Sie jeden in eine eigene Datei unterhalb von
`var/tmp/`, rendern Sie ihn mit `eZTemplate::factory()->fetch( $file )` und vergleichen Sie.
`expSmartyOperatorParityTest` führt jeden Operator-Fall aus den eigenen Tests der Template-Engine durch beide; fügen Sie
die Fälle Ihres Operators auf dieselbe Weise einem Test Ihrer Extension hinzu. Ohne installiertes Smarty 5 überspringen
diese Tests die Smarty-Hälfte.

## Verwandte Seiten

- [Operator-Referenz](smarty-templates-operator-referenz.md): die Modi jedes ausgelieferten Operators
- [Template-Funktionen und Blöcke](smarty-templates-funktionen.md)
- [Extensions](extensions.md): eine Extension bauen und veröffentlichen

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
