---
title: "Template operators in a theme"
audience: designer, developer
category: theme-extensions
order: 200
summary: Add a template operator to a theme - the operator class, autoloads/eztemplateautoload.php, ExtensionAutoloadPath[] and the autoload array - and avoid the one file the design wizard does not write.
keywords: template operator, autoloads, eztemplateautoload.php, ExtensionAutoloadPath, TemplateSettings, operator class, RAD, template operator wizard
type: help
---
# Template operators in a theme

This page is for designers who need a small piece of PHP in their templates: formatting a date range, picking a
color from a setting, shortening a text. A template operator is the clean way; logic copied into many templates is
not.

## The three pieces

| Piece | File | Read by |
|---|---|---|
| The operator class | `autoloads/<name>operators.php` (any class name of yours) | The autoload array |
| The registration | `autoloads/eztemplateautoload.php` | The template engine, which includes this exact file from every path it is given |
| The path | `site.ini [TemplateSettings] ExtensionAutoloadPath[]=<extension>` in `settings/site.ini.append.php` | The template engine (`eZTemplate::factory()`) |

## The quick way: the template operator wizard

**Setup > RAD > Template extension** (`/setup/templateoperator`) writes all three pieces, including
`autoloads/eztemplateautoload.php` ("which is how operators and functions are really registered"). Use it when the
theme needs more than one operator.

## Watch out with the design wizard

The [design extension wizard](design-extension-wizard.md) has a part **Template operators** that writes the operator
class (`autoloads/<name>operators.php`, with an example operator `<name>_excerpt`) and the `ExtensionAutoloadPath[]`
line, but **not** `autoloads/eztemplateautoload.php`. Without that file the engine never registers the operator, and
a template that uses it reports an unknown operator. Add the file as in step 2 below.

## Step by step by hand

1. Write the class `extension/mytheme/autoloads/mythemeoperators.php` with `operatorList()`,
   `namedParameterPerOperator()`, `namedParameterList()` and `modify()` (the design wizard's example is a good start).
2. Write `extension/mytheme/autoloads/eztemplateautoload.php`:

   ```php
   <?php
   $eZTemplateOperatorArray = array();
   $eZTemplateOperatorArray[] = array(
       'script'         => 'extension/mytheme/autoloads/mythemeoperators.php',
       'class'          => 'mythemeOperators',
       'operator_names' => array( 'mytheme_excerpt' ),
   );
   ```

3. Register the path in `extension/mytheme/settings/site.ini.append.php`:

   ```php
   <?php /* #?ini charset="utf-8"?

   [TemplateSettings]
   ExtensionAutoloadPath[]=mytheme

   */ ?>
   ```

4. Check the PHP files and regenerate the autoloads:

   ```bash
   php -l extension/mytheme/autoloads/mythemeoperators.php
   php -l extension/mytheme/autoloads/eztemplateautoload.php
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   php bin/php/ezcache.php --clear-id=template --allow-root-user
   ```

5. Use it in a template: `{$node.name|mytheme_excerpt( 20 )}`. Reload the PHP service that serves the site.

## Good practice

- Prefix operator names with your theme's name; a short generic name can collide with another extension's operator.
- Escape output in the template (`|wash`), not in the operator, unless the operator returns HTML on purpose.
- Keep operators free of database writes; they run while a page is drawn.

## Related pages

- Operators overview
- [RAD tools](../../../features/6.0/rad-tools.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
