# Twig-Templates: Operatoren und Funktionen schreiben

Diese Anleitung ist für PHP-Entwickler, die Template-Operatoren oder -Funktionen hinzufügen und sie in `.twig` (und
`.stpl`) ebenso haben möchten wie in `.tpl`. Die kurze Antwort: Schreiben Sie sie, wie Sie es immer getan haben, für das
Template-System. Jeder Operator und jede Template-Funktion, die das Template-System registriert hat, ist ein Twig-Filter,
eine Twig-Funktion oder ein Tag, ohne eigenen Twig-Code.

## Ein Operator

Ein Template-Operator ist eine Klasse mit `operatorList()`, `namedParameterPerOperator()`, `namedParameterList()` und
`modify()`, registriert in der `autoloads/eztemplateautoload.php` der Extension:

```php
<?php
// extension/<ext>/autoloads/eztemplateautoload.php
$eZTemplateOperatorArray[] = array( 'class' => 'myShoutOperator', 'operator_names' => array( 'shout' ) );
```

```php
<?php
// extension/<ext>/classes/myshoutoperator.php
class myShoutOperator
{
    public function operatorList()
    {
        return array( 'shout' );
    }

    public function namedParameterPerOperator()
    {
        return true;
    }

    public function namedParameterList()
    {
        return array( 'shout' => array( 'marks' => array( 'type' => 'integer', 'required' => false, 'default' => 1 ) ) );
    }

    public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace,
                            &$operatorValue, $namedParameters, $placement )
    {
        $operatorValue = strtoupper( (string)$operatorValue ) . str_repeat( '!', (int)$namedParameters['marks'] );
    }
}
```

Mit aktiver Extension und neu erzeugten Autoloads haben ihn alle drei Engines:

| `.tpl` | `.stpl` | `.twig` |
|---|---|---|
| `{$name\|shout( 3 )}` | `{$name\|shout:3}` | `{{ name\|shout(3) }}` |

Mit `name` = `'Ada'` druckt jede `ADA!!!`.

Worauf Sie achten sollten:

- **Die Eingabe.** `modify()` bekommt den Wert, den das Template hineinleitet, als `$operatorValue`. Ein Operator, der
  nur seine Parameter liest (keine Eingabe), sollte das in `operatorTemplateHints()` sagen (`'input' => false`) oder in
  `site.ini [TemplateBridgeSettings] ParameterOperatorList[]` stehen, damit `{{ op(a, b) }}` und `{{ a|op(b) }}` so
  gelesen werden, wie das `.tpl` sie liest.
- **Werte, keine Proxys.** Die Brücke übergibt `modify()` die eigenen Werte des Template-Systems: Arrays, Skalare und
  die Objekte selbst, nie Twigs Hüllen. Was `modify()` zurückgibt, wird für Twig wieder eingehüllt.
- **Der Name.** Hat Twig einen Filter oder eine Funktion gleichen Namens (`round`, `replace`, `merge` ...), rufen
  Templates Ihren als `exp_<name>` auf; ein eigener Name vermeidet das.
- **Keine Abhängigkeit von Twig.** Nichts in der Klasse bezieht sich auf Twig; der Operator funktioniert, wenn Twig
  nicht installiert ist.

## Eine Template-Funktion

Eine Template-Funktion (eine Klasse mit `functionList()` und `process()`, registriert in `$eZTemplateFunctionArray`) ist
eine Twig-Funktion, die einen Hash nimmt, wenn sie keinen Rumpf hat (`{{ my_box({title: 'x'}) }}`), und ein Tag mit
End-Tag, wenn sie einen hat (`{% my_box title='x' %}...{% endmy_box %}`). `[TwigSettings] FunctionList[]` wird nur für
eine Funktion gebraucht, die das Template-System nicht über die Autoload-Arrays registriert.

## Prüfen

```bash
./console exp:template:check --level=strict extension/<ext>/design/<design>/templates
```

meldet Templates, die einen Operatornamen aufrufen, den keine Extension registriert, in allen drei Engines.

## Siehe auch

- [Operatoren und Fetch-Funktionen](twig-templates-operatoren.md)
- [Template-Funktionen und Tags](twig-templates-funktionen.md)
- [Smarty-Templates: Plugins](smarty-templates-plugins.md)
