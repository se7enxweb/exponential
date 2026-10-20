---
title: Registration order and overrides
audience: developer, designer, app-creator
category: development
order: 500
summary: How the template engine decides which class serves an operator name - the order it reads the eztemplateautoload.php files, "last registration wins", the 21 names two classes register on alpha, and the lazy loading rule that lets a class take a name back in the middle of a request - proven with examples rendered on alpha.
keywords: operator registration, override, eztemplateautoload.php, AutoloadPathList, ExtensionAutoloadPath, ActiveExtensions, last wins, lazy loading, operator clash, swark, ezurlmultisite, substr
type: help
---
# Registration order and overrides

Operator names are global: one name, one class, for every template of a siteaccess. When two autoload files
register the same name, the template engine has rules for which one serves it, and one of those rules is
surprising. This page explains them from `lib/eztemplate/classes/eztemplate.php` and shows them on alpha. For the
practical "how do I replace a kernel operator", see [Overriding kernel operators](overriding-kernel-operators.md).

## Contents

1. [The order the files are read](#the-order-the-files-are-read)
2. [The last registration wins](#the-last-registration-wins)
3. [The names registered twice on alpha](#the-names-registered-twice-on-alpha)
4. [A loaded class takes its names back](#a-loaded-class-takes-its-names-back)
5. [Compiled templates](#compiled-templates)
6. [Functions and fetch functions](#functions-and-fetch-functions)
7. [Rules](#rules)

## The order the files are read

`eZTemplate::factory()` builds the list of folders to read from two `site.ini` settings and then calls
`autoload()`, which includes `eztemplateautoload.php` from each folder in that order:

1. `[TemplateSettings] AutoloadPathList[]`: in `settings/site.ini` the template library
   (`lib/eztemplate/classes/`), the kernel (`kernel/common/`), `lib/ezpdf/classes/` and
   `kernel/private/eztemplate/`;
2. `[TemplateSettings] ExtensionAutoloadPath[]`: one entry per extension, expanded to
   `extension/<name>/autoloads/`. Each extension adds its own line in its `settings/site.ini.append.php`, so the
   order is the order in which the INI system merges those files, which follows the order of
   `[ExtensionSettings] ActiveExtensions[]` (and of the siteaccess's `ActiveAccessExtensions[]`).

On alpha's `site` siteaccess the engine reads 23 files: the four above, then
`bcwebsitestatistics, ezwt, ezwebin, ezoe, eztags, ezstarrating, ezurlmultisite, sevenx_themes_media, powercontent,
recaptcha, explayouts_ui, explayouts, enhancedezbinaryfile, swark, owsimpleoperator, cjw_newsletter, xrowmetadata,
ezjscore, expui` (bcwebsitestatistics adds its folder to `AutoloadPathList[]` itself, the others use
`ExtensionAutoloadPath[]`). The `admin` siteaccess reads more (extension expai adds its operators there), so the same name
can be served by different classes on different siteaccesses.

## The last registration wins

Each file sets `$eZTemplateOperatorArray`, a list of entries like

```php
$eZTemplateOperatorArray[] = array( 'script' => 'extension/myext/autoloads/myoperators.php',
                                    'class' => 'myOperators',
                                    'operator_names' => array( 'my_one', 'my_two' ) );
```

and `registerAutoloadOperators()` stores the entry under each name: `$this->Operators[$operatorName] = $operatorDefinition;`.
A later file that names the same operator simply overwrites the entry. Nothing warns about it.

At this point nothing is loaded: the engine only knows which class to load for a name. The class file is included
and the object created the first time a template uses one of its names (`loadAndRegisterOperators()`).

## The names registered twice on alpha

This short script lists every name that more than one file registers, with the classes in reading order (save it
anywhere and run it with `php bin/php/ezexec.php -s site <file>`):

```php
<?php
// Which eztemplateautoload.php registers each operator name, in the order the engine reads them.
$owners = array();
foreach ( eZTemplate::factory()->autoloadPathList() as $path )
{
    $eZTemplateOperatorArray = array();
    if ( !file_exists( $path . 'eztemplateautoload.php' ) )
        continue;
    include $path . 'eztemplateautoload.php';
    foreach ( $eZTemplateOperatorArray as $definition )
    {
        $names = isset( $definition['operator_names'] ) ? $definition['operator_names']
                                                        : call_user_func( $definition['operator_names_function'] );
        foreach ( $names as $name )
            $owners[$name][] = $definition['class'] ?? $definition['function'];
    }
}
foreach ( $owners as $name => $classes )
    if ( count( $classes ) > 1 )
        echo str_pad( $name, 20 ), implode( ' -> ', $classes ), "\n";
```

Its output on alpha's `site` siteaccess:

```text
nl2br               eZTemplateNl2BrOperator -> eZPHPOperatorInit
image               eZTemplateImageOperator -> sevenxThemesMediaOperators
trim                eZTemplateStringOperator -> eZTemplateStringOperator
ltrim               eZTemplateStringsOperator -> SwarkLTrimOperator
rtrim               eZTemplateStringsOperator -> SwarkRTrimOperator
strip_tags          eZTemplateStringsOperator -> eZPHPOperatorInit
strpos              eZTemplateStringsOperator -> SwarkStrPosOperator
strrpos             eZTemplateStringsOperator -> SwarkStrRPosOperator
substr              eZTemplateStringsOperator -> SwarkSubStrOperator
ezurl               eZURLOperator -> eZURLMultisiteURLOperator
ezroot              eZURLOperator -> eZURLMultisiteURLOperator
ezdesign            eZURLOperator -> eZURLMultisiteURLOperator
ezimage             eZURLOperator -> eZURLMultisiteURLOperator
exturl              eZURLOperator -> eZURLMultisiteURLOperator
ezsys               eZURLOperator -> eZURLMultisiteURLOperator
ezhttp              eZURLOperator -> eZURLMultisiteURLOperator
ezhttp_hasvariable  eZURLOperator -> eZURLMultisiteURLOperator
ezini               eZURLOperator -> eZURLMultisiteURLOperator
ezini_hasvariable   eZURLOperator -> eZURLMultisiteURLOperator
json_encode         SwarkJSONEncodeOperator -> ezjscEncodingTemplateFunctions
str_replace         SwarkStrReplaceOperator -> PHPFunctionOperator
```

Three kinds of overlap show up:

- **Deliberate, complete overrides**: extension ezurlmultisite replaces all ten names of the kernel's
  `eZURLOperator` with a subclass. This is the safe pattern, explained in
  [Overriding kernel operators](overriding-kernel-operators.md).
- **Accidental clashes between extensions and the library**: swark registers its own `ltrim`, `rtrim`, `strpos`,
  `strrpos`, `substr`, `json_encode` and `str_replace`, the template library and ezjscore register the same names,
  and the media theme registers its own `image`. These are the dangerous ones, as the next section shows.
- **Settings-driven names**: `nl2br` (from cjw_newsletter's `template.ini.append.php`) and `strip_tags` (from
  ezmultiupload's) are also listed in `template.ini` `[PHP] PHPOperatorList[]`,
  which the kernel turns into operators served by `eZTemplatePHPOperator` (created by the function `eZPHPOperatorInit`).
  (`trim` appears twice in the library's own list; that is the same class.)

## A loaded class takes its names back

When the engine loads an operator class, `registerOperatorsInternal()` registers the object under **every** name
its `operatorList()` returns:

```php
foreach( $operatorObject->operatorList() as $operatorName )
{
    $this->Operators[$operatorName] = $operatorObject;
}
```

So if a class with many names is loaded after another class took one of those names, it takes the name back,
whatever the autoload order said. Which class serves a clashing name therefore depends on which operators the
request used before. With `substr`: swark's version treats a length of `0` as "to the end", the library's
`eZTemplateStringsOperator` passes `0` to PHP, which returns an empty string. Rendered on alpha with a fresh engine:

```tpl
[{'abcdef'|substr( 2, 0 )}]
```

Output on alpha:

```text
[cdef]
```

The same expression in one template, before and after a use of `strlen` (an operator of `eZTemplateStringsOperator`):

```tpl
[{'abcdef'|substr( 2, 0 )}]
{'abc'|strlen}
[{'abcdef'|substr( 2, 0 )}]
```

Output on alpha:

```text
[cdef]
3
[]
```

The first `substr` was served by `SwarkSubStrOperator` (the last registration); `strlen` loaded
`eZTemplateStringsOperator`, which registered all its sixty names, `substr` among them, and the second `substr`
was served by the library. In a real page the "before" can be any template that ran earlier in the request: the
pagelayout, an included menu, another block. The result of a template can change because a different part of the
page changed.

The same holds for a single overriding name from an extension. An extension that registers only `upcase` (one of
the fifteen names of `eZTemplateStringOperator`) wins in the first line and loses after `wash`, an operator of the
same library class, has run:

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

(Rendered with the override registered on the engine in memory; the `[override]` marker only shows which class
answered.) Almost every page uses `wash`, so such an override would work in a test template and fail on real pages.

## Compiled templates

Alpha runs with `[TemplateSettings] TemplateCompile=enabled`, the default. When the compiler meets an operator it
loads the operator's class at compile time to ask for its hints, and with hints it may write the operator's work
straight into the generated PHP (see [The operator class interface](operator-class-reference.md#operatortemplatehints)).
Two consequences:

- the class that is loaded when the template is **compiled** can decide the result, not the one that would serve
  the name at run time. The `upcase` example above, rendered by the compiler into a scratch folder, gives
  `FIT & HEALTHY` in all three lines: the compiler had loaded `eZTemplateStringOperator` for `wash` and used its
  inline code for `upcase`;
- an override that inherits the library's hints keeps the library's inline code in compiled templates, see
  [Overriding kernel operators](overriding-kernel-operators.md#drop-the-compile-time-code-you-change).

## Functions and fetch functions

Template functions (`$eZTemplateFunctionArray`) are registered the same way, last registration wins, and a loaded
function class registers all its `functionList()` names. On alpha no function name is registered twice.

Fetch functions follow a different rule: `fetch( 'content', ... )` uses the first `content/function_definition.php`
found in the module repositories, and extension repositories are searched before the kernel. See
[Fetch functions: how they work](fetch-functions-how-they-work.md).

## Rules

- Give every operator a name no other operator has: a prefix of your extension (`exp_`, `myext_`) is the habit.
- Before choosing a name, check the [operator index](operator-index.md) and run the script above on every
  siteaccess that will use it.
- Override a kernel or library operator only by replacing **all** names of its class with a subclass, as
  [Overriding kernel operators](overriding-kernel-operators.md) explains.
- Do not rely on `ActiveExtensions[]` order to settle a clash between two extensions: it only decides the first
  registration, and lazy loading can undo it.

## Related pages

- [Overriding kernel operators](overriding-kernel-operators.md)
- [Registering operators](registering-operators.md)
- [The operator class interface](operator-class-reference.md)
- [Operator index](operator-index.md)
- [OW Simple Operator: limitations and risks](owsimpleoperator-limitations.md#name-clashes)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
