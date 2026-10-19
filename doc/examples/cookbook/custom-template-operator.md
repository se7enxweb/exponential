---
title: A custom template operator
audience: developer
category: examples
order: 40
summary: Add two template operators, exp_initials and exp_reading_time with a named parameter, through an extension's autoloads/eztemplateautoload.php and an operator class with operatorList, namedParameterList and modify.
keywords: template operator, custom operator, eztemplateautoload.php, eZTemplateOperatorArray, operatorList, namedParameterPerOperator, namedParameterList, modify, ExtensionAutoloadPath, TemplateSettings
type: example
---
# A custom template operator

A template operator is a function a template calls with a pipe: `{$node.name|exp_initials}`. You add two to the
example extension:

- `exp_initials`: *Ada Lovelace* becomes *AL*;
- `exp_reading_time( words_per_minute )`: the minutes a text takes to read, at least 1; the parameter is optional
  and defaults to 200.

Prerequisites: the extension of [An extension skeleton](extension-skeleton.md), switched on.

## 1. Register the operators

The template engine reads `autoloads/eztemplateautoload.php` of every extension named in
`site.ini [TemplateSettings] ExtensionAutoloadPath[]`. Add that line to the extension's
`settings/site.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[TemplateSettings]
# Look for autoloads/eztemplateautoload.php in this extension (template operators)
ExtensionAutoloadPath[]=expexample

*/ ?>
```

`extension/expexample/autoloads/eztemplateautoload.php` lists which class answers which operator names:

```php
<?php
/**
 * The template operators of the expexample extension. Read because site.ini [TemplateSettings]
 * ExtensionAutoloadPath[] names expexample.
 *
 * @copyright Copyright (C) 2026 Example Company
 * @license GNU General Public License v2.0 (or any later version)
 * @package expexample
 */

$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array( 'script' => 'extension/expexample/autoloads/expexampletemplateoperators.php',
                                    'class' => 'expExampleTemplateOperators',
                                    'operator_names' => array( 'exp_initials', 'exp_reading_time' ) );
```

The class is loaded only when a template first uses one of the names. Operator names share one namespace with every
operator of the kernel and of other extensions: a prefix of your own (`exp_`, or your extension's) avoids clashes.
The [list of template operators](../template-operators/README.md) shows which names are taken.

## 2. The operator class

`extension/expexample/autoloads/expexampletemplateoperators.php`:

```php
<?php
/**
 * Two template operators:
 *   {$node.name|exp_initials}                          "Ada Lovelace" -> "AL"
 *   {$text|exp_reading_time( 200 )}                    minutes to read at 200 words a minute (at least 1)
 *
 * @copyright Copyright (C) 2026 Example Company
 * @license GNU General Public License v2.0 (or any later version)
 * @package expexample
 */

class expExampleTemplateOperators
{
    /** The operator names this class answers, as listed in eztemplateautoload.php */
    public function operatorList()
    {
        return array( 'exp_initials', 'exp_reading_time' );
    }

    /** true: namedParameterList() is keyed by operator name */
    public function namedParameterPerOperator()
    {
        return true;
    }

    /** The parameters of each operator, in the order they are written in the template */
    public function namedParameterList()
    {
        return array(
            'exp_initials' => array(),
            'exp_reading_time' => array(
                'words_per_minute' => array( 'type' => 'integer',
                                             'required' => false,
                                             'default' => 200 ) )
        );
    }

    /**
     * @param eZTemplate $tpl
     * @param string $operatorName
     * @param array $operatorParameters
     * @param string $rootNamespace
     * @param string $currentNamespace
     * @param mixed $operatorValue the value the operator is applied to; replace it with the result
     * @param array $namedParameters
     */
    public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace,
                            &$operatorValue, $namedParameters )
    {
        switch ( $operatorName )
        {
            case 'exp_initials':
                $initials = '';
                foreach ( preg_split( '/\s+/u', trim( (string)$operatorValue ), -1, PREG_SPLIT_NO_EMPTY ) as $word )
                    $initials .= mb_strtoupper( mb_substr( $word, 0, 1 ) );
                $operatorValue = $initials;
                break;

            case 'exp_reading_time':
                $perMinute = max( 1, (int)$namedParameters['words_per_minute'] );
                $words = str_word_count( strip_tags( (string)$operatorValue ) );
                $operatorValue = max( 1, (int)ceil( $words / $perMinute ) );
                break;
        }
    }
}
```

The four methods are what the engine asks an operator object (`lib/eztemplate/classes/eztemplate.php`):

| Method | Returns |
|---|---|
| `operatorList()` | the names the object answers |
| `namedParameterPerOperator()` | `true` when `namedParameterList()` is keyed by operator name |
| `namedParameterList()` | per operator: parameter name => `type`, `required`, `default`; the template passes them by position |
| `modify( ... &$operatorValue, $namedParameters )` | writes the result into `$operatorValue` |

`$operatorValue` is the value in front of the pipe (`null` when the operator is called as a function,
`{exp_initials()}`). Return text unescaped: the template decides with `|wash` whether it is HTML-escaped.

## 3. Use it

```tpl
<p class="byline">
    <span class="initials">{$node.object.owner.name|exp_initials}</span>
    {'%minutes min read'|i18n( 'extension/expexample', '', hash( '%minutes', $node.data_map.body.content|exp_reading_time ) )}
</p>
<p>{$node.data_map.body.content|exp_reading_time( 120 )} min at a slow pace</p>
```

## 4. Make it known and check it

```bash
php -l extension/expexample/autoloads/eztemplateautoload.php
php -l extension/expexample/autoloads/expexampletemplateoperators.php
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezcache.php --clear-tag=template --allow-root-user
```

The operator class is not in the autoload array; it is included from the `script` path. Under Velocity, restart it
(`./console exp:velocity restart --allow-root-user`, or the whole [deploy](velocity-deploy.md)): its workers read the
template settings and the operator list when they start.

Reload a page that uses the operator. An unknown operator is reported in the debug output as
*Operator 'exp_initials' is not registered*: the setting is not read (extension not active, INI cache) or the name in
`eztemplateautoload.php` differs from `operatorList()`.

Checked for this page (2026-10-18): `php -l` on both PHP files; the class has the same methods, with the same
signatures, as `extension/ezjscore/autoloads/ezjscaccesstemplatefunctions.php`.

## Common mistakes

| Symptom | Cause |
|---|---|
| *Operator ... is not registered* | `ExtensionAutoloadPath[]` missing, extension inactive, or the INI cache |
| The parameter is always the default | `namedParameterPerOperator()` returns `false` while the list is keyed by operator, or the parameter name differs |
| HTML appears as text, or text as HTML | escape in the template with `\|wash`, not in the operator |
| A heavy operator slows every page | cache its result (`cache-block`, [Caching](caching.md)) or compute it once in the view |

## Derived from

- `extension/ezjscore/autoloads/eztemplateautoload.php` and `ezjscaccesstemplatefunctions.php`
- `lib/eztemplate/classes/eztemplate.php` (`ExtensionAutoloadPath`, `AutoloadPathList`, operator loading)
- `extension/ezjscore/settings/site.ini.append.php`

## Related pages

- [Cookbook](README.md)
- [Template operators](../template-operators/README.md): every operator with its parameters
- [String template operators](../../features/6.0/string-template-operators.md)
- [Templates and design](../../guides/templates-and-design.md), [A template override](template-override.md)
- [RAD tools](../../features/6.0/rad-tools.md): `/setup/templateoperator` writes an operator skeleton
- The coding rules and the pattern catalogue: [the standards](../../standards/README.md)
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
