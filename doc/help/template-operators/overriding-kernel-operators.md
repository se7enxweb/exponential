---
title: Overriding kernel operators
audience: developer, app-creator
category: development
order: 510
summary: Changing what a kernel or template-library operator does from an extension, the way extension ezurlmultisite does for ezurl - subclass the class that owns the name, register the subclass for all of its names, drop the compile-time code for what you change - with each mistake and the working pattern rendered on alpha in interpreted and compiled mode.
keywords: override operator, kernel operator, extend operator, eZURLOperator, ezurlmultisite, eZTemplateStringOperator, subclass, operatorTemplateHints, element-transformation, ExtensionAutoloadPath, ActiveExtensions
type: help
---
# Overriding kernel operators

Sometimes the kernel's operator is almost right: `ezurl` should know about other sites, `upcase` should follow a
house rule. Editing the kernel is not an option (an upgrade replaces the file), so an extension replaces the
operator. This page shows the one pattern that holds up, why the simpler attempts fail, and the risks that remain.
Read [Registration order and overrides](registration-order-and-overrides.md) first for the rules behind it.

## Contents

1. [The pattern](#the-pattern)
2. [A real case: ezurlmultisite](#a-real-case-ezurlmultisite)
3. [Step by step](#step-by-step)
4. [Why registering one name fails](#why-registering-one-name-fails)
5. [Drop the compile-time code you change](#drop-the-compile-time-code-you-change)
6. [Order, siteaccesses and Velocity](#order-siteaccesses-and-velocity)
7. [Risks and how to keep them small](#risks-and-how-to-keep-them-small)

## The pattern

1. Find the class that owns the name and **all** the names it registers (the `operator_names` of its entry in
   the `eztemplateautoload.php` that registers it, or its `operatorList()`).
2. Write a subclass in your extension. Override `modify()` for the names you change and call `parent::modify()`
   for everything else.
3. If you change what an operator does, remove that operator's compile-time code in `operatorTemplateHints()`.
4. Register the subclass in your extension's `autoloads/eztemplateautoload.php` for **every** name of the parent
   class.
5. Make sure your extension's file is read after the one that registered the parent (normally it is: the
   kernel and the template library are read before every extension).

Because your class answers every name, the parent class is never loaded on that siteaccess, so it can never take
a name back.

## A real case: ezurlmultisite

Extension ezurlmultisite (active on alpha) makes `ezurl` produce links to the right site when content belongs to
another participating siteaccess. Its `autoloads/eztemplateautoload.php`:

```php
$eZTemplateOperatorArray[] = array(
    'script' => 'extension/ezurlmultisite/classes/ezurlmultisiteurloperator.php',
    'class' => 'eZURLMultisiteURLOperator',
    'operator_names' => array( 'ezurl', 'ezroot', 'ezdesign', 'ezimage', 'exturl',
                               'ezsys', 'ezhttp', 'ezhttp_hasvariable', 'ezini', 'ezini_hasvariable' )
);
```

These are exactly the ten names `kernel/common/eztemplateautoload.php` registers for `eZURLOperator`. The class,
in `extension/ezurlmultisite/classes/ezurlmultisiteurloperator.php`, follows the pattern (shortened):

```php
class eZURLMultisiteURLOperator extends eZURLOperator
{
    public function operatorTemplateHints()
    {
        $hints = parent::operatorTemplateHints();
        // ezurl is decided at run time: which siteaccess a link belongs to
        // depends on the siteaccess the compiled template is shown in.
        if ( isset( $hints[$this->URLName] ) )
        {
            $hints[$this->URLName]['element-transformation'] = false;
            unset( $hints[$this->URLName]['element-transformation-func'] );
        }
        return $hints;
    }

    public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement )
    {
        if ( $operatorName === $this->URLName )
        {
            // ... multisite handling, then return
        }
        parent::modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, $operatorValue, $namedParameters, $placement );
    }
}
```

Its doc comment states the reason for registering all ten names: "so the template engine never creates the kernel
object next to this one (it registers an object for all the names of its operatorList() the first time one of
them runs). No kernel override and no EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE are needed." It also adds a parameter to
`ezurl` by extending `namedParameterList()` from the parent's list.

## Step by step

The examples below override `upcase`, one of the fifteen names of `eZTemplateStringOperator`
(`lib/eztemplate/classes/eztemplatestringoperator.php`: `upcase`, `downcase`, `count_words`, `count_chars`, `trim`,
`break`, `wrap`, `shorten`, `pad`, `upfirst`, `upword`, `simplify`, `wash`, `chr`, `ord`). The override adds a
visible `[override]` marker so the output shows which class answered.

The subclass, `extension/myextension/autoloads/c11stringoperatoroverride.php`:

```php
class C11StringOperatorOverride extends eZTemplateStringOperator
{
    function operatorTemplateHints()
    {
        $hints = parent::operatorTemplateHints();
        unset( $hints['upcase'] ); // no inline PHP for upcase: compiled templates call modify() below
        return $hints;
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace,
                     &$operatorValue, $namedParameters, $placement = false )
    {
        if ( $operatorName === 'upcase' )
        {
            $operatorValue = '[override] ' . mb_strtoupper( (string)$operatorValue );
            return;
        }
        parent::modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace,
                        $operatorValue, $namedParameters, $placement );
    }
}
```

The registration, `extension/myextension/autoloads/eztemplateautoload.php`:

```php
<?php
$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array(
    'script' => 'extension/myextension/autoloads/c11stringoperatoroverride.php',
    'class' => 'C11StringOperatorOverride',
    'operator_names' => array( 'upcase', 'downcase', 'count_words', 'count_chars', 'trim', 'break', 'wrap', 'shorten',
                               'pad', 'upfirst', 'upword', 'simplify', 'wash', 'chr', 'ord' ) );
```

and `extension/myextension/settings/site.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[TemplateSettings]
ExtensionAutoloadPath[]=myextension

*/ ?>
```

Not run on alpha as an installed extension: the outputs below were rendered by a script that registered the same
entry on the template engine in memory, for that process only, with the class in a scratch folder (the scratch copy
has one extra static switch that keeps the hints, used in the section on compile-time code). The template
uses `upcase`, then `wash`, then `upcase` again:

```tpl
{'fit & healthy'|upcase}
{'Fit & Healthy'|wash}
{'fit & healthy'|upcase}
```

Output on alpha:

```text
[override] FIT & HEALTHY
Fit &amp; Healthy
[override] FIT & HEALTHY
```

Output on alpha with the template compiler (compiled into a scratch folder):

```text
[override] FIT & HEALTHY
Fit &amp; Healthy
[override] FIT & HEALTHY
```

Both `upcase` calls are answered by the override, in the interpreted and in the compiled template, and `wash`
still works: the subclass hands it to the parent's `modify()`.

## Why registering one name fails

Registering the subclass (or any class) only for `upcase` looks enough, and works in a quick test:

```tpl
{'fit & healthy'|upcase}
```

Output on alpha:

```text
[override] FIT & HEALTHY
```

It stops working as soon as the request uses any other operator of `eZTemplateStringOperator`. Loading that class
registers it for all fifteen names again, `upcase` included:

```tpl
{'fit & healthy'|upcase}
{'Fit & Healthy'|wash}
{'fit & healthy'|upcase}
```

Output on alpha:

```text
[override] FIT & HEALTHY
Fit &amp; Healthy
FIT & HEALTHY
```

Output on alpha with the template compiler (compiled into a scratch folder):

```text
FIT & HEALTHY
Fit &amp; Healthy
FIT & HEALTHY
```

In the compiled template not even the first line is overridden: the compiler loaded the library class for `wash`
while compiling and wrote the library's inline code for both `upcase` calls.

## Drop the compile-time code you change

Many library operators declare `element-transformation` hints: when a template is compiled, the operator class
writes PHP for the call straight into the compiled template (for `upcase`, a call of `mb_strtoupper()`), and
`modify()` is never called for it again. A subclass inherits those hints. If it changes `modify()` but keeps the
hints, the override works in interpreted templates and is silently ignored in compiled ones. The same subclass with
the library's hints kept, registered for all fifteen names:

```tpl
{'fit & healthy'|upcase}
{'Fit & Healthy'|wash}
{'fit & healthy'|upcase}
```

Output on alpha:

```text
[override] FIT & HEALTHY
Fit &amp; Healthy
[override] FIT & HEALTHY
```

Output on alpha with the template compiler (compiled into a scratch folder):

```text
FIT & HEALTHY
Fit &amp; Healthy
FIT & HEALTHY
```

Alpha, like every default installation, compiles templates (`[TemplateSettings] TemplateCompile=enabled` in
`settings/site.ini`), so the compiled result is what visitors get. Remove the hint entry of each operator you change
(`unset( $hints['upcase'] )`), or set `element-transformation` to `false` as ezurlmultisite does. Keep the hints of
the names you only pass through to the parent.

## Order, siteaccesses and Velocity

- Extensions are read after the kernel and the template library, so an extension's registration of a kernel
  operator name is always the later one. Between two extensions that register the same names, the one whose
  `site.ini.append.php` is merged later wins: that follows `[ExtensionSettings] ActiveExtensions[]` and the
  siteaccess's `ActiveAccessExtensions[]`. Avoid needing that.
- An override registered by an extension that is active on one siteaccess only (an access extension) applies only
  there. Check every siteaccess that renders the templates, including the admin when it previews content.
- Clear the INI and template caches after adding the registration (compiled templates keep the old inline code
  until they are compiled again): `php bin/php/ezcache.php --clear-tag=ini,template --allow-root-user`. Under
  Velocity the workers keep the operator registrations of their warm-up: on alpha run
  `./console exp:velocity deploy --allow-root-user`, which clears these caches and restarts Velocity.

## Risks and how to keep them small

- **Upgrades**: your subclass depends on the parent's internals (`$this->URLName`, the hint array, the
  `modify()` signature). After a kernel upgrade, read the parent class's changes and run your templates in both
  interpreted and compiled mode.
- **New names in the parent**: if a kernel release adds an operator to the parent class, your registration does
  not list it, and the parent class gets loaded for that name and takes all names back. Compare your
  `operator_names` with the parent's `operatorList()` in a test (`array_diff()` of the two must be empty).
- **Two extensions overriding the same class**: only one registration can win. Merge them into one subclass.
- **Hidden changes**: an override changes what every template on the siteaccess gets, including templates of other
  extensions. Prefer a new operator with its own name ([Creating an operator, step by step](creating-an-operator-step-by-step.md))
  when only your own templates need the new behavior.
- **No kernel class override**: you do not need `EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE` or a copy of the kernel class
  for this; the operator registration is the supported hook.

## Related pages

- [Registration order and overrides](registration-order-and-overrides.md)
- [The operator class interface](operator-class-reference.md)
- [Registering operators](registering-operators.md)
- [URL operators](url-operators.md): `ezurl` and its siblings as ezurlmultisite serves them
- [The template compiler](../template-language/template-compiler.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
