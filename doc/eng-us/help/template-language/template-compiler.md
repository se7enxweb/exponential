---
title: The template compiler
audience: developer, designer, app-creator
category: development
order: 400
summary: How eZTemplateCompiler turns a parsed template into PHP - the pipeline (node transformation, static optimizations, combining, PHP generation), the compiled file name and folder, the operator and function hints that let an operator become inline PHP, the processOperator fallback, DevelopmentMode, the settings - with generated code from a development installation and one real difference between compiled and interpreted output.
keywords: template compiler, eZTemplateCompiler, TemplateCompile, compiled templates, var/cache/template/compiled, operatorTemplateHints, functionTemplateHints, element-transformation, processOperator, DevelopmentMode, eztc, CompileComments, choose
type: help
---
# The template compiler

With `[TemplateSettings] TemplateCompile=enabled` (the default in `settings/site.ini`, and the value on a development installation) a
template is not interpreted on every request. The first time it is used, `eZTemplateCompiler`
(`lib/eztemplate/classes/eztemplatecompiler.php`) turns its parsed tree into a PHP file, and later requests include
that file. This page shows how, with code the compiler generated on a development installation.

## Contents

1. [When a template is compiled](#when-a-template-is-compiled)
2. [The pipeline](#the-pipeline)
3. [What the generated PHP looks like](#what-the-generated-php-looks-like)
4. [Hints: how an operator becomes PHP](#hints-how-an-operator-becomes-php)
5. [Compiled and interpreted are not always identical](#compiled-and-interpreted-are-not-always-identical)
6. [Where the files go, and when they are rebuilt](#where-the-files-go-and-when-they-are-rebuilt)
7. [Settings](#settings)
8. [Related pages](#related-pages)

## When a template is compiled

`eZTemplate::fetch()` asks the resource (`design:` or `file:`) to load the template. The file resource
(`lib/eztemplate/classes/eztemplatefileresource.php`) first asks `eZTemplateCompiler::hasCompiledTemplate()` whether
a compiled file exists that is at least as new as the template file. If so, the template is not even parsed: the
engine runs `eZTemplateCompiler::executeCompilation()`, which includes the PHP file. If not, the template is parsed
and, when compiling is allowed, compiled and then executed. If running the compiled file fails, the engine reloads
the template and, as a last resort, interprets the parsed tree (`eZTemplate::reloadAfterFailedCompiledTemplate()`).

Compiling is skipped when `TemplateCompile` is not `enabled`, when the engine's caching is switched off
(`$tpl->setIsCachingAllowed( false )`, which the example renderer of these pages uses), or when a request is marked
"no cache advised" (`$GLOBALS['eZSiteBasics']['no-cache-adviced']`).

## The pipeline

`eZTemplateCompiler::compileTemplate()` runs these steps on the parsed tree:

1. **Node transformation** (`processNodeTransformation`): every function and operator that provides hints may
   replace its node with simpler nodes or with PHP code (see below).
2. **Static optimizations** (`processStaticOptimizations`): values known at compile time are computed once.
3. **Node combining** (`processNodeCombining`): neighboring text nodes are joined.
4. Placement information (file, line, column, used in error messages) is dropped when
   `CompileNodePlacements=disabled`.
5. **PHP generation** (`generatePHPCode`), written with `eZPHPCreator`.

`CompileIncludeNodeTree[]` (`original`, `transformed`, `static`, `combined`, `final`) adds the intermediate trees to
the generated file, for work on the compiler itself.

## What the generated PHP looks like

This small template was compiled on a development installation into a scratch folder (the compiler's `compilation-directory` setting
pointed elsewhere, so the site's cache was not touched) and run both ways:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 96 ) )}
<h1>{$node.name|wash}</h1>
{if $node.children_count|gt( 0 )}{$node.children_count} children{else}no children{/if}

{'compiled'|upcase}
```

Output:

```text
<h1>Why Should You Choose Strength Training?</h1>no children
COMPILED
```

Both runs gave the same output. Excerpts of the 135 generated lines:

```php
<?php
// URI:       file:var/tmp/examples/tm-compiler/01-small.tpl
// Filename:  var/tmp/examples/tm-compiler/01-small.tpl
// Timestamp: 1792402671 (Mon Oct 19 2:37:51 PDT 2026)
$tpl->Level++;
if ( $tpl->Level > 40 )
{
$text = $tpl->MaxLevelWarning;$tpl->Level--;
return;
}

// def $node
unset( $var );
if ( class_exists( 'eZContentFunctionCollection' ) && method_exists( 'eZContentFunctionCollection', 'fetchContentNode' ) )
    $var = call_user_func_array( array( new eZContentFunctionCollection(), 'fetchContentNode' ),
      array_values( array(     'node_id' => 96,
    'node_path' => false,
    'language_code' => false,
    'remote_id' => false ) ) );
...
$var = htmlspecialchars( (string) $var1, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 );
...
$if_cond = eZTemplateLogicOperator::compareCounts( 'gt', $if_cond1, 0 );
...
$var = 'COMPILED' ;
```

What the compiler did:

- `fetch( 'content', 'node', ... )` became a direct call of the fetch function's PHP method,
  `eZContentFunctionCollection::fetchContentNode()`, with the parameters from `function_definition.php` filled in
  (the fetch operator's transformation in `eZTemplateExecuteOperator`).
- `|wash` became `htmlspecialchars()`.
- `gt( 0 )` became a call of a static helper of the logic operator.
- `'compiled'|upcase` was computed at compile time: the PHP holds the constant `'COMPILED'`.
- Attribute access (`$node.name`) goes through `compiledFetchAttribute()`, a function of the shared `common.php` in
  the same folder that calls `hasAttribute()` and `attribute()`, exactly as the interpreter does.
- The `Level` check stops runaway recursion: more than 40 nested templates (`$tpl->MaxLevel`) end with a warning.

## Hints: how an operator becomes PHP

An operator class may describe its operators to the compiler in `operatorTemplateHints()`. From
`eZTemplateStringOperator` (`lib/eztemplate/classes/eztemplatestringoperator.php`):

```php
$this->UpcaseName => array( 'parameters' => false, 'element-transformation-func' => 'phpMapTransformation' ),
$this->ShortenName => array( 'parameters' => 3, 'element-transformation-func' => 'customMapTransformation' ),
$this->WashName => array( 'parameters' => 1, 'element-transformation-func' => 'washTransformation' ),
...
$hints[$operator]['input'] = true;
$hints[$operator]['output'] = true;
$hints[$operator]['element-transformation'] = true;
$hints[$operator]['transform-parameters'] = true;
```

| Hint | Meaning |
|---|---|
| `input`, `output` | the operator takes the piped value / produces a value |
| `parameters` | how many parameters it takes (`false`: none, `true`: any) |
| `element-transformation` | the operator can turn itself into PHP code |
| `element-transformation-func` | the method that returns that code |
| `transform-parameters` | its parameters are compiled before it is |
| `input-as-parameter` | the piped value is passed as the first parameter (`always`, or true) |
| `static` | the result depends only on constant parameters, so it may be computed at compile time |

An operator **without** hints still works compiled: the compiler generates a call of
`$tpl->processOperator( ... )`, which runs the operator's `modify()` at run time, as the interpreter would. Hints
only make it faster. Template functions have the same mechanism in `functionTemplateHints()` and
`templateNodeTransformation()`; `cache-block` for example compiles into a call of `eZTemplateCacheBlock::handle()`.
Writing an operator of your own is covered in [Writing template operators](../creators/app-creators/template-operators.md).

## Compiled and interpreted are not always identical

The transformation code is a second implementation of each operator, so the two can disagree. Running every example
of this section both ways on a development installation found one case: `choose` with an empty string as the index.

```tpl
{def $empty_image = fetch( 'content', 'node', hash( 'node_id', 96 ) ).data_map.additional_image}
has_content returns {$empty_image.has_content|get_type}
choose: [{$empty_image.has_content|choose( 'no', 'yes' )}]
cond:   [{cond( $empty_image.has_content, 'yes', 'no' )}]
```

Output:

```text
has_content returns string[0]
choose: [no]
cond:   [no]
```

That is the interpreted output. Compiled, the same template gave `choose: []`. The interpreter
(`eZTemplateLogicOperator::modify()`) turns any non-numeric index into 0 or 1 by truthiness, so `''` picks `'no'`.
The compiled code is a PHP `switch ( $index ) { case 0: ...; case 1: ... }`, and PHP 8 does not consider `''` equal
to `0`, so no case matches and the result is empty. The `has_content` of an empty `ezimage` field returns `''`
(the `teaser_intro` of the same article, an `ezxmltext`, returns `false`, which both modes treat alike). Use
`cond()` or `{if}` for such tests, which agree in both modes, as the last line shows.

## Where the files go, and when they are rebuilt

- Folder: `var/<site>/cache/template/compiled/` (`eZSys::cacheDirectory()` plus `template/compiled`), or
  `[TemplateSettings] SharedCompiledTemplatesDir` when `ShareCompiledTemplates=enabled`.
- Name: `<template name>-<md5>.php`. The md5 is taken from the template key plus the internal charset, the locale,
  whether full URLs are used, the siteaccess, a custom pagelayout, the index file and, when a signed-in user has
  chosen another interface (UI switch), that choice (`eZTemplateCompiler::compilationFilename()`). One template can
  therefore have many compiled files, one per siteaccess and language. With `ShareCompiledTemplates` only the key
  and the locale count.
- Rebuilt when the template file is newer than the compiled file. Templates included from a compiled template are
  checked only for existence, unless `DevelopmentMode=enabled` adds a modification time check there too (the
  generated include code compares `filemtime()` of both files). With `DevelopmentMode=disabled`, as on a development installation, clear
  the template cache after changing an included template.
- Cleared with `php bin/php/ezcache.php --clear-id=template` or the "Template cache" button of the cache page; see
  [Template caches](template-caches.md).
- Precompiled with `php bin/php/eztc.php` (see [eztc](../../examples/commands/eztc.md)), so the first visitors do
  not pay for compiling.

## Settings

All in `site.ini [TemplateSettings]`, values on the `site` siteaccess:

```tpl
TemplateCompile:  {ezini( 'TemplateSettings', 'TemplateCompile' )}
DevelopmentMode:  {ezini( 'TemplateSettings', 'DevelopmentMode' )}
TemplateCache:    {ezini( 'TemplateSettings', 'TemplateCache' )}
NodeTreeCaching:  {ezini( 'TemplateSettings', 'NodeTreeCaching' )}
ViewCaching:      {ezini( 'ContentSettings', 'ViewCaching' )}
CachedViewModes:  {ezini( 'ContentSettings', 'CachedViewModes' )}
```

Output:

```text
TemplateCompile:  enabled
DevelopmentMode:  disabled
TemplateCache:    enabled
NodeTreeCaching:  disabled
ViewCaching:      enabled
CachedViewModes:  full;sitemap;pdf;googlesitemap
```

| Setting | Effect |
|---|---|
| `TemplateCompile` | compile templates to PHP |
| `DevelopmentMode` | extra modification time checks for included templates |
| `TemplateOptimization` | further optimizations of compiled templates |
| `CompileComments` | comments in the generated PHP |
| `CompileNodePlacements` | keep file/line/column for error messages |
| `CompileExecution` | `disabled`: generate the files but interpret anyway |
| `CompileAlwaysGenerate` | compile even when a compiled file exists |
| `CompileAccumulators`, `CompileTimingPoints` | debug accumulators and timing points in the code |
| `CompileResourceFallback` | fallback code for resource handling, for debugging the compiler |
| `ShareCompiledTemplates`, `SharedCompiledTemplatesDir` | one compiled set for all siteaccesses |
| `TemplateCompression` | store compiled templates gzip-compressed |
| `NodeTreeCaching` | cache the parsed node tree of templates |

## Related pages

- [Template caches](template-caches.md)
- [Template compilation and cache](../designers/templates/template-compilation-and-cache.md)
- [Errors and debugging](errors-and-debugging.md)
- [The object model in templates](object-model.md)
- [eztc](../../examples/commands/eztc.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
