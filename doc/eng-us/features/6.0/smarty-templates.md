# Smarty templates (.stpl) next to .tpl

This page is for site builders and template developers who know Smarty, or who want to port a design to it. A design
can now hold Smarty 5 templates (`*.stpl`) next to the usual Exponential templates (`*.tpl`). A `.stpl` is found,
overridden, included and cached like any template, sees the same variables, and can use every Exponential template
operator, every fetch function and the `*_view_gui` functions. A converter turns a whole design of `.tpl` files into
`.stpl`. The feature is part of the kernel library: the code is in `lib/smarty/`, the settings are in
`settings/smarty.ini`, and nothing has to be activated: `.stpl` templates work in every siteaccess. Twig 3 templates
(`.twig`) are the third engine ([Twig templates](twig-templates.md)); choosing between them:
[The three template engines compared](../../guides/template-engines.md). Added in
October 2026. How fast it is and why: [Smarty templates: performance (Speed)](../../guides/smarty-templates-performance.md). Coming from `.tpl`: [Moving from .tpl to .stpl](../../help/template-language/moving-from-tpl-to-stpl.md).

The guides go further:

| Guide | For |
|---|---|
| [A first .stpl, step by step](../../guides/smarty-templates-tutorial.md) | your first Smarty template: a siteaccess, an override rule, variables, operators, a fetch with paging, a loop, includes |
| [Writing and running .stpl templates](../../guides/smarty-templates.md) | how it works: the stub, setup, variables and write-back, objects, includes, loops, functions, blocks, caching, debugging, Velocity, limits |
| [Operators and fetch functions](../../guides/smarty-templates-operators.md) | the two call forms, how an operator gets its input, the `exp_` prefix, `fetch()`, the helpers |
| [Operator reference](../../guides/smarty-templates-operator-reference.md) | every operator of the installation and its extensions, with its `.stpl` form, mode and an example |
| [Template functions and blocks](../../guides/smarty-templates-functions.md) | every bridged function and block, the `exp*` tags and blocks, Smarty's own tags |
| [Caching and performance](../../guides/smarty-templates-caching.md) | the cache layers, `{expcacheblock}`, a page block with per-visitor placeholders, keys, expiry, measuring |
| [Performance (Speed)](../../guides/smarty-templates-performance.md) | the whole story of making `.stpl` fast: what was slow, every fix and why, the numbers, the page cache pattern, measuring, Velocity against PHP-FPM, a checklist |
| [Moving from .tpl to .stpl](../../help/template-language/moving-from-tpl-to-stpl.md) | the template-language manual's chapter: when to choose `.stpl`, what changes, the converters in both directions, side by side |
| [Converting templates between template engines](../../guides/template-conversion.md) | the `exp:template:convert` commands: every option, the output and exit codes, the marker, the override settings, the same from PHP |
| [The converter and porting a design](../../guides/smarty-templates-converter.md) | `exp:template:convert:tpl-to-stpl` and `exp:template:convert:stpl-to-tpl`, everything they write, their notes, a siteaccess for a converted design |
| [Migrating a design](../../guides/smarty-templates-migrating.md) | planning, converting, page-by-page parity, fixing differences, keeping `.tpl` and `.stpl` in step |
| [Troubleshooting and FAQ](../../guides/smarty-templates-troubleshooting.md) | every error message with its cause and fix, and the common questions |
| [Writing operators and functions](../../guides/smarty-templates-plugins.md) | your own operators and functions that work in both engines |
| [Security and escaping](../../guides/smarty-templates-security.md) | `wash`, `escape` and `raw`, visitors' values, Smarty's reach, the form token, cache blocks |

## What it does

- **A `.stpl` is a template like any other.** The design fallback finds it, an `override.ini` rule can point its
  `MatchFile` at it, `{include uri='design:parts/x.stpl'}` includes it, and `node_view_gui` or
  `attribute_view_gui` can end in it. A `.stpl` can include `.tpl` templates and the other way round.
- **Rendered by Smarty on every request.** The template system never parses the Smarty text. For a `.stpl` it parses a
  one-line stub, `{exp_smarty_render( '<file>' )}`, and compiles and caches that stub as it would any template. The
  stub runs on every request, from the compiled cache as well, and renders the file with Smarty and the variables of
  the calling template. Nothing a request rendered is ever stored in a template cache.
- **The same variables, both ways.** A `.stpl` sees what a `.tpl` in its place sees (`$node`, `$view_parameters`,
  `$module_result` and `$site` in a pagelayout, the parameters of an include). What it assigns goes back to the
  calling template, as `{def}`, `{set}` and `{undef}` do.
- **Operators as modifiers and functions.** `{$node.name|wash}`, `{'Read more'|i18n:'design/standard/node'}`,
  `{concat('a', $b)}`, `{fetch('content', 'node', ['node_id' => 2])}`. Where Smarty has a modifier of the same name,
  the Exponential operator is called with the prefix `exp_` (`{$list|exp_count}`).
- **Template functions as tags.** `{attribute_view_gui attribute=$node.data_map.body}`,
  `{node_view_gui view='line' content_node=$child}`, and functions with a body as blocks
  (`{explblock name='content'}...{/explblock}`). The Exponential tags that are template structure have Smarty
  equivalents: `{expinclude}`, `{expset}`, `{expundefall}`, `{expsetblock}`, `{expappendblock}`, `{expcacheblock}`,
  `{exprunonce}`, `{expfetch}`.
- **Converters, both ways.** `./console exp:template:convert:tpl-to-stpl` converts every `.tpl` of a design to a
  `.stpl` that renders the same output, reports what it could not convert exactly, and can write the `override.ini`
  that makes a siteaccess use the converted templates; `./console exp:template:convert:stpl-to-tpl` converts back.
  Both belong to the general command `exp:template:convert` ([Converting templates between template engines](../../guides/template-conversion.md)).

## How to use it

1. Smarty 5 comes with the installation: the root `composer.json` requires `smarty/smarty ^5`. Without Composer,
   point `[SmartySettings] LibraryPath` at a Smarty 5 checkout (see [Settings](#settings)).
2. Put a `.stpl` in a design and use it through an override rule, for example in
   `settings/siteaccess/<siteaccess>/override.ini.append.php`:

   ```ini
   [folder_smarty]
   Source=node/view/full.tpl
   MatchFile=full/folder.stpl
   Subdir=templates
   Match[class_identifier]=folder
   ```

   `MatchFile` is looked up below `override/templates/` of each design, so this rule uses
   `design/<design>/override/templates/full/folder.stpl`.

3. Write the template in Smarty syntax:

   ```smarty
   <div class="content-view-full">
       <h1>{$node.name|wash}</h1>
       {if isset($node.data_map.short_description) && $node.data_map.short_description.has_content}
           <div class="attribute-short">{attribute_view_gui attribute=$node.data_map.short_description}</div>
       {/if}
       {$children = fetch('content', 'list', ['parent_node_id' => $node.node_id, 'limit' => 5, 'sort_by' => ['priority', true]])}
       <ul>
       {foreach $children as $child}
           <li>{node_view_gui view='line' content_node=$child}</li>
       {foreachelse}
           <li>{'No items.'|i18n:'design/standard/node'}</li>
       {/foreach}
       </ul>
   </div>
   ```

4. Clear the caches once, because a new template file was added
   (`php bin/php/ezcache.php --clear-tag=template --allow-root-user`, and the content cache if the view cache is on).
   After that, a change to the `.stpl` is seen on the next request while `CompileCheck` is enabled.

The kernel ships a working demo pair in `design/standard/templates/smarty/`: `demo_full.stpl` (a full view with a
fetch and `attribute_view_gui`) and `demo_children.stpl` (included with Smarty's own `{include file=...}`). Because
they are in `templates/`, not `override/templates/`, an override rule reaches them with
`MatchFile=../../templates/smarty/demo_full.stpl`.

## Why use it

Smarty is an optional engine. Exponential's own template language stays the default, and nothing changes for a
site that never adds a `.stpl`. What a `.stpl` gets is not a reduced subset behind a stub but the whole system the
templates of a site rely on:

- **Every template operator** of the kernel and of the active extensions, as a Smarty modifier or function, called
  the way the compiled template system calls it (`i18n`, `ezurl`, `ezimage`, `wash`, `ezini`, `cond`, the string
  operators, the operators of Exponential Layouts ...).
- **Every fetch function** of every module, with the same parameters and permission checks.
- **Every template function**: `attribute_view_gui`, `node_view_gui`, `content_view_gui` and the rest as tags, block
  functions such as Layouts' `{explblock}` as blocks.
- **Override rules and the design fallback**: a `.stpl` is chosen by `override.ini` like a `.tpl`, and a port can fall
  back on the original design for anything it does not have.
- **Includes both ways**, `.tpl` from `.stpl` and `.stpl` from `.tpl`, sharing their variables as `{include}` does.
- **Translations**: `i18n` with contexts, comments and arguments, from the same translation files.
- **Caching**: the template caches, the content view cache, `{expcacheblock}` (the template-block cache with its
  expiry rules), Velocity's response cache; Smarty's compiled files are cleared with the template caches.
- **Velocity and PHP-FPM alike**, with persistent workers: nothing of one request is kept for the next.
- **A converter** that ports a whole design and the override rules that select it.

So a team that knows Smarty can write templates in it from the first day, and a design can move to Smarty one
template at a time, next to the `.tpl` files it still uses.

### Speed

A `.stpl` page is faster than the same page in `.tpl`. Measured on a development installation on 28 October 2026, the script time the debug
toolbar shows, the median of 10 requests of the same page, with the content view cache warm, the debug toolbar on for
both, and the `site` (`.tpl`) and `smarty` (`.stpl`) siteaccesses showing byte for byte the same HTML:

| Page | `.tpl`, Apache + PHP-FPM | `.stpl`, Apache + PHP-FPM | `.tpl`, Velocity | `.stpl`, Velocity |
|---|---|---|---|---|
| A category page (`/fitness`) | 0.119 s | 0.097 s (18 % faster) | 0.102 s | 0.069 s (32 % faster) |
| A recipe list (`/recipes`) | 0.102 s | 0.077 s (25 % faster) | 0.093 s | 0.037 s (60 % faster) |
| The front page | — | 0.104 s | — | 0.036 s |

The front page of the `.tpl` site was answered from the full-page cache in these runs, so it has no script time of its
own to compare. In template processing alone, the debug toolbar showed 74 ms for the `.stpl` category page against
90 ms for the `.tpl` one, with 14 MB of memory against 18 MB.

Where the speed comes from:

- **A `.stpl` included by a `.stpl` runs as a Smarty subtemplate**, without the detour through the template system,
  its stub and a second render: the front page renders 37 `.stpl` templates through the bridge, not 266.
- **Values are converted once per page**: a variable the next template sees unchanged (`$node`, `$module_result` ...)
  is found with one comparison, and the modifiers, functions and blocks are looked up once per process.
- **The media port keeps its whole page in one `{expcacheblock}`**, per node, page content, the visitor's roles and
  whether the visitor is signed in. Only what is the visitor's own, the user menu with the basket, is rendered on every
  request and put into the cached page. A publish of content, of a layout or of a layout rule expires it.
- **Velocity keeps the kernel, Smarty and the compiled templates in its workers**, so a request does not start them
  again: that is why the `.stpl` pages there are mostly at 0.03 to 0.07 s.

The numbers depend on the server's load. To measure your own pages, compare the debug toolbar's script time, or
`curl -w '%{time_total}'`, for the same URL on both siteaccesses. A page the full-page cache answers (anonymous visitors, no debug report) is served without rendering,
in either engine.

The full story, with every change that was made, the reason for it, the costs that are left and how to measure:
[Smarty templates: performance (Speed)](../../guides/smarty-templates-performance.md).

## Enable the converted media design in a siteaccess

The media design was ported to Smarty: 516 `.stpl` templates in `extension/sevenx_themes_media/design/smarty_media/`,
converted from `design/media/`. The worked example serves it from the siteaccess `smarty`
(`https://www.example.com/smarty/`), a copy of the media site's public siteaccess `site`. To set up the same in your
installation (here the new siteaccess is called `smarty`, copied from `site`):

1. Copy `settings/siteaccess/site/` to `settings/siteaccess/smarty/` and set the design list in
   `settings/siteaccess/smarty/site.ini.append.php`; the `.stpl` port comes first, the media design and its parents
   are the fallback for stylesheets, images and any template not ported:

   ```ini
   [DesignSettings]
   SiteDesign=smarty_media
   AdditionalSiteDesignList[]
   AdditionalSiteDesignList[]=media
   AdditionalSiteDesignList[]=ezwebin
   AdditionalSiteDesignList[]=standard
   AdditionalSiteDesignList[]=base
   ```

2. Put the override rules that map the templates to `.stpl` in the siteaccess. The extension ships the file of its
   port; `./console exp:template:convert:tpl-to-stpl --override-ini=...` writes the same for a design of your own:

   ```bash
   cp extension/sevenx_themes_media/doc/smarty/override.ini.append.php settings/siteaccess/smarty/override.ini.append.php
   ```

3. Make the siteaccess known in `settings/override/site.ini.append.php`, next to the entries already there:

   ```ini
   [SiteSettings]
   SiteList[]=smarty

   [SiteAccessSettings]
   AvailableSiteAccessList[]=smarty
   ```

   With `MatchOrder=uri` (or `uri;host`) the siteaccess is reached at `/smarty/`; for host matching add a
   `HostMatchMapItems[]` line.

4. Let visitors in: in the administration, **Users > Roles and policies**, edit the **Anonymous** role and add
   `smarty` to the SiteAccess limitation of its `user/login` policy. Without it, every page asks for a sign-in.

5. Clear the caches:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   php bin/php/ezcache.php --clear-tag=template --allow-root-user
   php bin/php/ezcache.php --clear-id=content --allow-root-user
   ```

   or run `./console exp:velocity deploy --allow-root-user`, which also reloads PHP-FPM and restarts Velocity.

Nothing has to be activated: the kernel renders `.stpl` templates for every siteaccess. The details, the converter
and the checks after converting are in [the converter guide](../../guides/smarty-templates-converter.md).

## Settings

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `settings/smarty.ini` | `SmartySettings` | `LibraryPath` | empty | Smarty 5 when it is not in Composer's autoload: the directory that holds `src/Smarty.php`, relative to the installation root or absolute |
| `settings/smarty.ini` | `SmartySettings` | `CompileCheck` | `enabled` | Smarty recompiles a `.stpl` that changed; `disabled` never checks (production; clear the template cache after a change) |
| `settings/smarty.ini` | `SmartySettings` | `Debugging` | `disabled` | `enabled` opens Smarty's debug console |
| `settings/smarty.ini` | `SmartySettings` | `FunctionList[]` | the `*_view_gui` family | Exponential template functions that are always callable as Smarty tags |
| `settings/site.ini` | `Cache_smarty` | `path`, `tags[]` | `smarty`, `template` | Smarty's compiled templates in `<cache directory>/smarty/`, cleared with the template caches |
| `settings/site.ini` | `TemplateSettings` | `AutoloadPathList[]` | includes `lib/smarty/classes/` | registers the `exp_smarty_render` operator the stub calls |

Override them in `settings/override/smarty.ini.append.php` or per siteaccess, as any INI file.

## Commands

| Command | Does |
|---|---|
| `./console exp:template:convert:tpl-to-stpl` (`php bin/php/exptemplateconverttpltostpl.php`) | converts the `.tpl` templates of a design, or one with `--file`, to `.stpl`; `--override-ini` writes the override rules; `--dry-run`, `--force` |
| `./console exp:template:convert:stpl-to-tpl` (`php bin/php/exptemplateconvertstpltotpl.php`) | the reverse: `.stpl` to `.tpl` |
| `./console exp:template:convert --from-engine=tpl --to-engine=stpl` (`php bin/php/exptemplateconvert.php`) | the general command; the same work with the engines as options |
| `php bin/php/ezcache.php --clear-id=smarty` | clears Smarty's compiled templates and the generated function calls (also cleared with `--clear-tag=template`) |

Every option, output line and exit code (0 `PASS`, 1 `FAIL` with errors, 2 wrong options, 3 a direction that is not
available): [Converting templates between template engines](../../guides/template-conversion.md). The command
reference: [exp:template:convert](../../examples/commands/exptemplateconvert.md),
[exp:template:convert:tpl-to-stpl](../../examples/commands/exptemplateconverttpltostpl.md) and
[exp:template:convert:stpl-to-tpl](../../examples/commands/exptemplateconvertstpltotpl.md).

## Inside

The Smarty code is in `lib/smarty/classes/`, the conversion of whole designs in `lib/templatebridge/classes/`; the
template system itself has two small hooks, in one class.

| Part | Role |
|---|---|
| `eZTemplateFileResource::handleResourceData()`, `cacheKey()` | the kernel's two hooks: a `.stpl` is handed to the template system as the stub `{exp_smarty_render( '<file>' )}`, compiled under a key of its own |
| `expSmartyOperator` | the operator `exp_smarty_render` the stub calls, registered through `[TemplateSettings] AutoloadPathList[]=lib/smarty/classes/` |
| `expSmartyRenderer` | renders a `.stpl` with Smarty: the Smarty instance per process and set of designs, the variables in and back out, a `.stpl` included by a `.stpl` as a Smarty subtemplate, operator calls with their input mode, function calls, error reporting |
| `expSmartyExtension` | the Smarty 5 extension that makes operators modifiers and template functions tags or blocks, with the `exp_` prefix and the `exp*` helpers; answers remembered per process |
| `expSmartyObjectProxy` | reads Exponential objects with Smarty's dot syntax, read-only |
| `expSmartyFunction`, `expSmartyBlockFunction` | template functions as tags and as blocks |
| `expSmartyIncludeFunction`, `expSmartySetFunction`, `expSmartyUndefAllFunction`, `expSmartyFetchFunction` | `{expinclude}`, `{expset}`, `{expundefall}`, `{expfetch}` |
| `expSmartySetBlock`, `expSmartyCacheBlock`, `expSmartyRunOnceBlock` | `{expsetblock}` and `{expappendblock}`, `{expcacheblock}`, `{exprunonce}` |
| `expSmartyVoid` | the value of `expvoid()`, an empty operator parameter |
| `expSmartyConverter`, `expSmartyReverseConverter` | the converters of one template text, tpl to stpl and back, registered in `site.ini [TemplateBridgeSettings] ConverterList` |
| `expTemplateDesignConverter` (`lib/templatebridge/classes/`) | the work of the `exp:template:convert` commands: walks a design, keeps files changed by hand (the marker), writes the override settings |

Tests, without a database: `tests/tests/lib/smarty/expSmartyTemplateRenderTest.php` (rendering, write-back, the stub
and its key, the converter on the constructs of the media port), `expSmartyOperatorParityTest.php` (every engine and
operator case of the template engine's own tests gives the same output as a `.stpl`),
`expSmartyReverseConverterTest.php` (the way back) and
`tests/tests/kernel/private/classes/commands/expTemplateConvertCommandTest.php` (the commands, both directions on a
design, the round trip of a whole design). Without Smarty 5 installed, the rendering cases are skipped.

## Who may use it

There is no policy of its own: a `.stpl` is a design file, and whoever may change the design files may write one. A
`.stpl` can call any fetch function and template function a `.tpl` can, with the same permission checks, since they
run through the same template system. Smarty adds what a `.tpl` cannot do (reading files with `{fetch file=...}`,
the request through `$smarty.get` and `$smarty.server`): no Smarty security policy is set, so treat `.stpl` files as
code and give the right to edit templates in the administration only to people you would let change PHP. See
[Security and escaping](../../guides/smarty-templates-security.md).

## Related pages

- [Templates and design](../../guides/templates-and-design.md): how a template is found and overridden
- [Template path comments](template-path-comments.md) and [the Exp Debug bar](exp-debug-bar.md)
- [Caches](../../guides/caches.md): the template caches the Smarty cache is cleared with
- [Velocity: running Exponential in a persistent-worker web server](velocity-persistent-worker-server.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
