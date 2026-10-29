# Smarty templates: troubleshooting and FAQ

This page is for template developers and operators whose `.stpl` page does not look as expected. It lists the error
messages the bridge and Smarty produce, each with its cause and its fix, the problems that give no message, and a FAQ.
Every message here was met while porting the media design or while building the bridge.

## First: see the message

A `.stpl` that fails does not break the page. Its place stays empty, and:

- the debug output gets an error with the label `smarty` (and the error log, `var/log/error.log`, a line);
- with debug output on, the page gets an HTML comment `<!-- smarty: <message> -->` where the template would have been.

So: turn the debug output on for your own address ([the Exp Debug bar](../features/6.0/exp-debug-bar.md)), reload,
and look at the bar's Messages tab or search the page source for `<!-- smarty:`. Without debug output, look in the
error log:

```bash
grep -h 'smarty\|rendering .* failed' var/log/error.log | tail -20
```

(`var/<site>/log/` in a multi-site installation that keeps its logs apart.)

## Messages of the bridge

| Message | Cause | Fix |
|---|---|---|
| `rendering <file> failed: <reason>` | Smarty could not compile or run the template; the reason is Smarty's message or one of the messages below | the reason tells; see the tables below |
| `template not found: <file>` | the stub names a file that is gone: the `.stpl` was renamed, moved or deleted, and a compiled stub still points at it | clear the template caches (`php bin/php/ezcache.php --clear-tag=template --allow-root-user`) |
| `the Smarty library is not available (install smarty/smarty with Composer, or see [SmartySettings] LibraryPath in smarty.ini)` | Smarty 5 is not in Composer's autoload and `LibraryPath` names no checkout | install it, or set `LibraryPath` to the directory that holds `src/Smarty.php`; also logged once as `Smarty 5 not found: ...` |
| `not a Smarty template: <file>` | `exp_smarty_render` was called with a file that does not end in `.stpl` | call it with a `.stpl` |
| `invalid parameter name for <function>: <name>` | a template function was called with a parameter name that is not letters, digits and underscores starting with a letter | rename the parameter |
| `expinclude: missing parameter 'uri'` | `{expinclude}` without `uri` (or an empty one: a `concat()` that gave `''`) | give the URI; check the value you build it from |
| `expfetch: missing parameter 'assign'`, `'module'`, `'function'` | `{expfetch}` without one of them | add it |
| `expset: missing parameter 'name'` | `{expset}` without `name` | add it |
| `expsetblock: missing parameter 'variable'` | `{expsetblock}` or `{expappendblock}` without `variable` | add it |
| `<!-- smarty: unsupported characters in the template file name -->` | the `.stpl` file's name or path holds `'`, `\`, `{`, `}` or a line break | rename the file |

## Messages of Smarty

Smarty's compile errors read:

```
Syntax error in template "file:/<path>/full/folder.stpl"  on line 12 "{foreach ($validation.attributes) as $item}" missing 'from' attribute
```

The file, the line and the source line are given; the reason is at the end.

| Reason | Cause | Fix |
|---|---|---|
| `missing 'from' attribute` | a `{foreach}` over an expression (`{foreach fetch(...) as $x}`, `{foreach ($list\|append:'x') as $x}`): Smarty reads a parenthesised source as the attribute form `{foreach from=...}` | assign it first: `{$__loop1 = fetch(...)}{foreach $__loop1 as $x}`. The converter does this for you |
| `unexpected closing tag` | a closing tag with no opening tag: a `.tpl` tag left in a hand port (`{/section}`, `{/let}`, `{/default}`, `{/cache-block}`, `{/set-block}`), a closing tag whose opening tag Smarty read as text (a brace followed by a space, `{ if ...}`), or an opening tag that was removed | find the opening tag; convert the `.tpl` tag to its Smarty form ([the mapping](smarty-templates-converter.md#2-what-it-writes)) |
| `unclosed {if} tag`, `unclosed '{if}' tag` | an opening tag never closed, or tags nested the wrong way round (`{if}{foreach}{/if}{/foreach}`) | close them in the right order |
| `unknown modifier 'x'` | no Smarty modifier and no operator `x` in this siteaccess: the extension that registers it is not active here, or a typing error | activate the extension in the siteaccess, or fix the name; after activating, reload PHP-FPM and restart Velocity (names are looked up once per process) |
| `unknown tag 'x'` | no template function `x` in this siteaccess, or a `.tpl` tag left in the file (`{def}`, `{set}`, `{undef}`, `{let}`, `{set-block}`, `{cache-block}`, `{include uri=...}`) | use the Smarty form (`{$x = ...}`, `{capture}`, `{expcacheblock}`, `{expinclude}`) |
| ` - Unexpected "("` and similar | Smarty syntax: often a `.tpl` habit, modifier parameters in parentheses (`$x\|i18n('ctx')`) | `$x\|i18n:'ctx'` |
| ` - Unexpected ...` inside CSS or JavaScript | a `{` followed by anything but white space starts a tag: `body {margin:0}`, `function(){return 1;}` | `{ldelim}`, a space after the brace, or `{literal}...{/literal}` around the code |
| `Unable to load 'file:<name>'` | Smarty's own `{include file=...}` did not find the file in the designs' `override/templates/` and `templates/` | check the path; for a `.tpl`, or to apply override rules, use `{expinclude uri='design:...'}` |

## PHP errors inside a render

These come from the code an operator or function runs; the message is `rendering <file> failed:` followed by PHP's.

| Message | Cause | Fix |
|---|---|---|
| `in_array(): Argument #2 ($haystack) must be of type array, null given` | `contains` (or another array operator) ran with a `null` input and an array parameter: `$list\|contains:$x` where `$list` is not set, or `contains(null, [...])`. It was also the symptom of a bridge before October 2026 that passed the input of `contains`, `implode`, `i18n` ... as a parameter. | make sure the input is an array (`{$list = []}` before the loop that fills it); with the current bridge, convert the template again if it still has the old `op(null, ...)` forms for these |
| `Array to string conversion` (a warning, the page shows `Array`) | an array printed with `{$x}` or passed where text is expected. Usually a Smarty modifier ran instead of the operator of the same name: Smarty's `explode` takes the separator first, `implode` is deprecated, `count` counts a string as 1 | use `exp_explode`, `exp_implode`, `exp_count`; print an element (`$x.0`) or `exp_implode` the array |
| `Call to undefined method ...` or `Undefined ... setKeyValue` on every page, `.tpl` pages included | a copy of a kernel class in an extension directory took the kernel's place (see "kernel class overrides" below) | remove the copy, regenerate the autoloads |
| `Operator '<name>' is not registered` (a template warning, not a render failure) | the operator exists in the siteaccess the template was compiled for but not in this one | activate the extension here, or keep the template out of this siteaccess |

## Problems without a message

**The whole page is empty or white.** The pagelayout itself failed. With debug output off it prints nothing at all.
Turn the debug output on, or read the error log. Common causes: the pagelayout's `.stpl` has a syntax error after a
hand edit; Smarty is not installed; the compile directory is not writable (next item).

**A page works on Apache and fails on Velocity, or the other way round.** Velocity runs as `root`, PHP-FPM as the
site's user, and both write `<cache directory>/smarty/compile/` and `<cache directory>/smarty/functions/`. A directory
created by one may not be writable by the other: the render fails with a message about a file that cannot be written.
Clear the Smarty cache as the site's user (`php bin/php/ezcache.php --clear-id=smarty`) or give the directories to
that user.

**A `.stpl` page is slow.** Measure first, under identical conditions (the same caches, the same debug state, a
server that is not overloaded), with the debug toolbar's script time and the median of ten requests. Then check: is
`{expinclude}` used with `name=` where it is not needed (the include then goes through the template system instead of
running as a subtemplate); is the pagelayout cached (`{expcacheblock}`, `[TemplateSettings] TemplateCache=enabled`);
is the site served by PHP-FPM with a cold opcode cache. Everything about it:
[Smarty templates: performance (Speed)](smarty-templates-performance.md).

**A change to a `.stpl` does not show.** In order: the content view cache serves the page (clear `--clear-id=content`,
or turn `ViewCaching` off while you work); an `{expcacheblock}` around it is cached (clear `--clear-tag=template`);
the full-page HTTP cache answers anonymous visitors (`X-Exp-Cache: HIT`; look with debug output on, which bypasses
it); `[SmartySettings] CompileCheck=disabled` (clear `--clear-id=smarty`); the override rule does not select your file
(the debug bar's template tree names the file used).

**A new `.stpl` is not used.** A new template file or rule needs the template caches cleared: the override cache keeps
the list of files. If the rule points at a file in `templates/` rather than `override/templates/`, `MatchFile` needs
`../../templates/` in front (it is looked up below `override/templates/`).

**A variable is empty inside `node_view_gui`, `attribute_view_gui` ...** A template function runs in a namespace of
its own that holds only the parameters you pass. Pass the value as a parameter: `{node_view_gui view='line'
content_node=$child highlight=$search_text}` makes `$highlight` a variable of the line template.

**A variable set in an included `.stpl` is not visible afterwards.** It is visible after `{expinclude}` without
`name=`. It is not after `{expinclude ... name='x'}` (a namespace of its own) or after Smarty's `{include file=...}`
(Smarty keeps the included file's variables to itself).

**A value is always the default.** A converted `.tpl` `$x|first_set('d')` is `first_set('d')`, because the `.tpl`
ignored `$x`; see [what to check after converting](smarty-templates-converter.md#3-its-notes-and-what-to-check-after-converting).

**The text `null` appears.** A bare word in a `.tpl` is a string, and the converter keeps it (`'null'`).

**A cache block shows the content of another page.** It lacks the keys that make it differ, or two `{expcacheblock}`
in one file have the same `id`. See [Caching and performance](smarty-templates-caching.md#expcacheblock-in-detail).

**A siteaccess asks every visitor to sign in.** The Anonymous role has no `user/login` policy for it: add the
siteaccess to the policy's SiteAccess limitation.

**Kernel class overrides from inactive extensions.** The autoload generator (`php bin/php/ezpgenerateautoloads.php`)
looks at every directory below `extension/`, active or not, and any file there that declares a kernel class becomes an
override of that class in `var/autoload/ezp_override.php`, for every siteaccess. An extension that carries a copy of,
say, `eZTemplateDesignResource` replaces the kernel's class even when no siteaccess activates it, and an outdated copy
can take the whole site down on the next deploy. Smarty support needs no such copy: it is in the kernel. If an
extension directory holds kernel class copies you do not want, remove it (or list it in `.autoloadignore`), regenerate
the autoloads, and check that `var/autoload/ezp_override.php` lists only the overrides you intend.

## FAQ

**Do I have to activate an extension?** No. `.stpl` support is in the kernel (`lib/smarty`) and works in every
siteaccess.

**Which Smarty version?** Smarty 5, on PHP 8. Smarty 3 and 4 do not work: the bridge is a Smarty 5 extension.

**Can a `.tpl` include a `.stpl`, and the other way round?** Yes, both ways: `{include uri='design:x.stpl'}` in a
`.tpl`, `{expinclude uri='design:x.tpl'}` in a `.stpl`. Variables flow as with any include.

**Does it work in the administration, in mail templates, in PDF exports?** Anywhere the template system loads a
template: a `.stpl` chosen by an override rule or named in an include. Mail templates work because the variables a
`.stpl` assigns (`$subject`, `$email_receiver` ...) are written back for the module to read.

**Can I use Smarty's template inheritance (`{extends}`, `{block}`)?** Smarty finds other `.stpl` files in the designs'
`override/templates/` and `templates/` directories, so it can in principle; it was not used in the media port and is
not covered by the tests. The design fallback and override rules are the Exponential way to vary a template.

**Is the output escaped automatically?** No, as in a `.tpl`: escape with `|wash`. See
[Security and escaping](smarty-templates-security.md).

**Can I register my own Smarty plugins?** Not through a setting. Write a template operator or function instead: it
works in `.tpl` and `.stpl` alike. See [Writing operators and functions for .stpl](smarty-templates-plugins.md).

**Do I need to restart anything after editing a `.stpl`?** No, with `CompileCheck=enabled`. After changing an INI
setting, an operator class or the list of active extensions: clear the INI cache, reload PHP-FPM and restart Velocity
(`./console exp:velocity deploy --allow-root-user` does all of it).

**Is `CompileCheck=disabled` worth it?** It saves Smarty a file check per template per request. Use it on production
when templates change only with a deploy, and clear `--clear-id=smarty` after every template change.

**Where are the compiled files?** `<cache directory>/smarty/compile/` (Smarty's) and `<cache directory>/smarty/functions/`
(the generated calls of template functions), for example `var/site/cache/smarty/`. They are cleared with the template
caches.

**Why is `exp_path` not the operator `path`?** An operator whose own name starts with `exp_` is called by that name.
Only a name that is not registered has its `exp_` removed.

**Does the converter change my `.tpl` files?** No. It writes `.stpl` files below `--to` (by default next to the
`.tpl`) and never touches a source. A target it did not write itself, or that was edited after it wrote it, is skipped
unless `--force` is given.

**Can I go back from `.stpl` to `.tpl`?** Yes: `./console exp:template:convert:stpl-to-tpl`
([part 6 of the converter guide](smarty-templates-converter.md#6-converting-smarty-templates-back-to-tpl)).

**The conversion ends with `FAIL`. What does the exit code say?** The commands of `exp:template:convert` end with
`PASS` and exit code 0 when every template was converted, left unchanged or skipped (`SKIP` lines and `NOTE` lines do
not fail a run). Otherwise:

| Exit code | Last line, for example | Cause and fix |
|---|---|---|
| 1 | `FAIL tpl->stpl 516 templates: ... 1 errors; ...`, after an `ERROR <template>: ...` line | a template could not be read or converted, or the result does not compile with Smarty (tpl to stpl) or does not parse (stpl to tpl); nothing was written for it. Fix the source the `ERROR` line names and convert again |
| 2 | `FAIL --from must be a design directory: extension/no-such-design (or give --file)`, `FAIL --from-engine is needed: tpl, stpl` | wrong options: check the paths and, for `exp:template:convert`, give `--from-engine` and `--to-engine` |
| 3 | `FAIL <from>-><to>: converting ... is not available yet` | the installation has no converter for that direction (`site.ini [TemplateBridgeSettings] ConverterList`) |

Every line and option is explained in [Converting templates between template engines](template-conversion.md#3-output-and-exit-codes).

## Related pages

- [Smarty templates: writing and running .stpl templates](smarty-templates.md#11-debugging)
- [Operator reference](smarty-templates-operator-reference.md), [Template functions and blocks](smarty-templates-functions.md)
- [Migrating a design](smarty-templates-migrating.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../help/partners/partners-start-here.md) and [How to apply](../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
