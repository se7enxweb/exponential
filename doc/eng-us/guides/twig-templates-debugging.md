# Debugging Twig templates

This guide is for template developers who need to find out why a `.twig` prints nothing, prints the wrong thing or is
slow. It goes through the tools in the order you would use them: the check before the page, the message in the page,
the debug bar, the template path comments, the Twig profile, and the log. Every message and every output below was
printed on a development installation; file paths below the installation root are shortened to `<root>`.

## 1. Check before you look at a page

Most mistakes in a `.twig` are compile errors, and `exp:template:check` finds them without a web server, a database or
a visitor:

```bash
./console exp:template:check extension/<ext>/design/<design>/templates --level=strict
```

On three small files with typical mistakes:

```
ERROR  twig  var/tmp/twigdocs/check/article.twig:3
       Tag "do" is not allowed. (the sandbox would refuse the template) [TWIG-SECURITY-001]
       > 3 | {% do 1 %}
WARN   twig  var/tmp/twigdocs/check/article.twig:2
       exp_upper names no template operator: it passes its input through [TWIG-STRICT-UNKNOWN-OPERATOR]
       > 2 | {{ node.name|exp_upper }}
ERROR  twig  var/tmp/twigdocs/check/article.twig:4
       the included template design:parts/no_such_part.tpl is not found in the design chain [TWIG-STRICT-INCLUDE-MISSING]
       > 4 | {% expinclude uri='design:parts/no_such_part.tpl' %}
ERROR  twig  var/tmp/twigdocs/check/typo.twig:1
       Unknown "wahs" filter. [TWIG-SYNTAX-001]
       > 1 | {{ node.name|wahs }}
       hint: did you mean "wash"?
ERROR  twig  var/tmp/twigdocs/check/unclosed.twig:3
       Unexpected end of template. [TWIG-SYNTAX-001]
SUMMARY 3 files: 0 OK, 3 failed, 0 SKIP; findings: 4 ERROR, 1 WARN, 0 NOTE  (twig 3)  0.1 s
FAIL
```

The exit code was 1. What each rule means:

| Rule | Level | What it finds | What to do |
|---|---|---|---|
| `TWIG-SYNTAX-001` | every level | Twig cannot parse or compile the file: an unknown filter, function or tag, an unclosed `{% if %}`, a stray `}` | read the message and the line; for an unknown name the hint suggests the nearest one |
| `TWIG-COMPILE-001` | `compile`, `strict` | the compiled PHP is not valid | rare; report it with the template |
| `TWIG-SECURITY-001` | `strict`, `--security` | a tag, filter or function the sandbox refuses | use an allowed construct, or allow it in the siteaccess's `twig.ini` |
| `TWIG-STRICT-UNKNOWN-OPERATOR` | `strict` | an `exp_<name>` that is no operator; it compiles, but passes its input through | fix the name (`upcase`, not `exp_upper`), or activate the extension that has the operator |
| `TWIG-STRICT-INCLUDE-MISSING` | `strict`, `--includes` | an `expinclude` whose `design:` URI does not resolve in the design chain | fix the URI, or add the template |
| `TWIG-DEPRECATION-001` | `--deprecations` | a construct Twig deprecates | rewrite it before the next Twig version removes it |

Why the unknown operator is only a warning: `exp_<name>` is a valid Twig filter name, so the template compiles, and at
run time an unknown operator does what it does in a `.tpl`, it passes its input through. `--level=strict` is the only
way to see it before a page does. `./console exp:template:check --explain=<rule>` explains a rule;
the [rule reference](../reference/template-check-rules.md) lists them all with good and bad examples.

For an editor or CI, `--format=jsonl` prints one finding per line:

```
{"schema":"exp-template-check/1","type":"finding","rule":"TWIG-SYNTAX-001","severity":"ERROR","engine":"twig","file":"var/tmp/twigdocs/check/typo.twig","line":1,"column":0,"message":"Unknown \"wahs\" filter.","hint":"did you mean \"wash\"?","source":"{{ node.name|wahs }}","belongs":[]}
{"schema":"exp-template-check/1","type":"summary","files":1,"ok":0,"failed":1,"skipped":0,"errors":1,"warnings":0,"notes":0,"engines":{"twig":1},"unavailable":[],"seconds":0.046,"level":"compile","exit":1,"result":"FAIL"}
```

([Checking template syntax](template-syntax-check.md) has every option, the other formats and a pre-commit hook.)

## 2. The message in the page

A `.twig` that fails to compile or to run prints nothing in its place, so a visitor never sees a stack trace. With the
debug output on, it leaves an HTML comment with the reason; view the page source and search for `twig:`:

```
<!-- twig: rendering <root>/var/tmp/doc-examples/example.twig failed: Unknown &quot;wahs&quot; filter in &quot;var/tmp/doc-examples/example.twig&quot; at line 1. (example.twig:1) -->
```

How to read it:

- `rendering <root>/.../example.twig failed:` the file that failed, with its full path: the file the design resource
  or an override rule chose, which is not always the one you are editing.
- `Unknown "wahs" filter in "var/tmp/doc-examples/example.twig" at line 1.` Twig's own message, with the template name
  (its path below the installation root) and the line in the `.twig`.
- `(example.twig:1)` where the error was thrown. For a Twig error that is the template again; when the error came
  from PHP code the template called (an operator, a fetch function), it names that PHP file and line instead, which
  tells you the template was fine and the operator failed.

The same text, without the HTML escaping, goes to the debug output (label `twig`) and to `var/log/error.log`.

### The messages you will meet

| Message | Cause | Fix |
|---|---|---|
| `Unknown "x" filter.` / `Unknown "x" function.` | no operator, template function or Twig filter of that name in this siteaccess | a typing mistake, or the extension that registers it is not active here |
| `Filter "x" is not allowed` / `Function "x" is not allowed` / `Tag "x" is not allowed` | the sandbox refuses one of Twig's own constructs (`filter`, `map`, `spaceless`, `do`, `autoescape` ...) | use an allowed construct, or allow it in `[TwigSettings] SecurityAllowed...List[]` of a siteaccess you trust |
| `Calling "name" method on a "expTwigObjectProxy" object is not allowed` | a method call: `node.name()` | read the attribute: `node.name` |
| `Unexpected end of template.` | a tag opened and never closed (`{% if %}` without `{% endif %}`) | close it; the line is that of the end of the file, look for the opening tag above |
| `Unknown operator upper` (debug output only, the page prints the input) | `exp_upper`: the `exp_` prefix of a name that is no operator | the operator's real name (`upcase`); see section 1 |
| `No template could be loaded for "parts/x.tpl" using resource "design"` (a warning, the include prints nothing) | `expinclude` of a URI the design chain does not have | fix the URI; `--level=strict` reports it as `TWIG-STRICT-INCLUDE-MISSING` |
| `template not found: <file>` | the file the compiled stub names is gone (renamed or deleted) | clear the template caches: `./console exp:ezcache --clear-tag=template` |
| `the Twig library is not available (...)` / `Twig 3 not found ...` | Twig is not installed for this PHP | [Versions and compatibility](twig-templates-compatibility.md#when-twig-is-not-installed) |
| `a loop in <file> stopped after 10000 rounds ([TwigSettings] WhileLoopLimit)` | a converted `{while}` or `{do}` that does not end | fix the condition, or raise the limit |

## 3. Which template answered: path comments and the debug bar

**Template path comments.** With `[TemplateSettings] ShowTemplatePathComments=enabled` (or `Debug=enabled`) in
`site.ini.append.php` of a development siteaccess, every template is wrapped in comments that name its file and the URI
it was asked for, and `.twig` files take part like any other:

```
<!-- START: including template: <root>/var/tmp/doc-examples/example.twig (<root>/var/tmp/doc-examples/example.twig) -->
Why Track Your Workouts?07/23/2018 x "/fitness/why-track-your-workouts"

<!-- START: including template: var/site/cache/twig/functions/attribute_view_gui-d2eb444e35c0a71f0a85df8194acb5b6.tpl (file:var/site/cache/twig/functions/attribute_view_gui-d2eb444e35c0a71f0a85df8194acb5b6.tpl) -->

<!-- START: including template: design/standard/templates/content/datatype/view/ezstring.tpl (design:content/datatype/view/ezstring.tpl) -->

Why Track Your Workouts?
<!-- STOP: including template: design/standard/templates/content/datatype/view/ezstring.tpl (design:content/datatype/view/ezstring.tpl) -->

<!-- STOP: including template: var/site/cache/twig/functions/attribute_view_gui-d2eb444e35c0a71f0a85df8194acb5b6.tpl (file:var/site/cache/twig/functions/attribute_view_gui-d2eb444e35c0a71f0a85df8194acb5b6.tpl) -->

<!-- STOP: including template: <root>/var/tmp/doc-examples/example.twig (<root>/var/tmp/doc-examples/example.twig) -->
```

Two things to notice. A template function such as `attribute_view_gui` runs through a small generated `.tpl` in
`<cache directory>/twig/functions/`, which calls the template system's own function, so the datatype's template
(`ezstring.tpl` here) is chosen by the override rules exactly as from a `.tpl`. And the comments break nothing that
prints into a start tag: the kernel leaves them out of templates listed in `TemplatesWithoutDebugComments`.
Never switch these comments on for a production siteaccess: they make every page larger and show your file layout.

**The debug bar.** With the debug output on, the Exp debug bar shows:

- **Templates**: the tree of templates the page rendered, `.twig` files included with `expinclude` among them, each
  with the URI it was asked for and the file that answered.
- **Rendering**: the controller, what the content view cache did and its key, how many templates were compiled for
  this request, and the cache blocks: a `{% expcacheblock %}` appears as its template and number
  (`pagelayout.twig id=4`), with hit or miss, its keys and its cache file.

A page that "does not change" after you edited a `.twig` is nearly always served from one of those caches: the
Rendering tab says which. See [Caching](twig-templates-caching.md).

## 4. The Twig profile

With `[TwigSettings] Profile=enabled` and the debug output on, each page writes one notice labelled `twig profile`
with where the Twig part of the page spent its time. For the three-line template of section 3, rendered from the
command line on a development installation (no OPcache there, so loading compiled classes costs more than under a web
server), the second run:

```
environment built (once per process)           1 calls      8.9 ms
render() of a .twig from the template system     1 calls     65.5 ms
variables in (wrap)                            1 calls      0.5 ms
load (compiled template)                       1 calls     30.3 ms
operators (filters)                            2 calls     12.8 ms
template functions (*_view_gui ...)            1 calls     13.7 ms
variables out (write-back)                     1 calls      0.0 ms
  |ezurl                                       1 calls     11.8 ms
  |l10n                                        1 calls      0.9 ms
  {% expcacheblock %} hit twig:var/tmp/doc-examples/example.twig:0     1 calls      0.0 ms
```

How to read it:

| Row | Meaning | When it is high |
|---|---|---|
| environment built | building the Twig environment; once per process (once per Velocity worker) | under PHP-FPM it is paid on every request; normal |
| render() | all `.twig` rendered from the template system, inclusive | the total; compare with the page's script time |
| variables in / out | handing the namespace's variables to Twig and writing back what the template set | many large arrays in the namespace |
| load (compiled template) | loading the compiled classes | the first request after a change compiles; without OPcache every request reads the files |
| operators (filters), template functions | time inside the template system's operators and functions | look at the indented rows |
| `\|name` rows | each operator by name, slowest first | an operator called in a loop; `ezurl` and `i18n` with arguments are the usual ones |
| `{% expcacheblock %}` rows | each cache block with `hit` or `miss` | a block that misses on every request: its keys change too often |

The first run of the same template had `miss` for the cache block, `load` 35.8 ms (the template was compiled) and the
template function at 51.1 ms (its datatype template compiled too): the cost of a cold cache, paid once.

Leave `Profile` disabled in production; it costs a timer per call.

## 5. Twig's own debug mode

`[TwigSettings] Debug=enabled` turns on Twig's debug mode and its `dump()` function. `dump` is not in the sandbox's
allowed function list; for a development siteaccess add it:

```ini
# settings/siteaccess/<development siteaccess>/twig.ini.append.php
[TwigSettings]
Debug=enabled
SecurityAllowedFunctionList[]=dump
```

With both, `{{ dump(user_name) }}` printed `string(3) "Ada"`.

The template system's own `attribute` operator with `show` is often more useful for content objects, because it lists
the attributes the object offers, with their types and values, rather than PHP internals:
`{{ node|attribute('show', 1) }}` printed a table that starts with `node_id` (integer, 98), `parent_node_id`
(integer, 92), `main_node_id`, `contentobject_id` and goes on through every attribute of the node.

## 6. A change that does not show

Work through this list in order:

1. **A cache answered.** The debug bar's Rendering tab, or the response header `X-Exp-Cache`, says which. Clear the
   view cache (`./console exp:ezcache --clear-id=content`) or the template block cache
   (`--clear-id=template-block`).
2. **Another file answers.** The path comments or the Templates tab name the file; an override rule of a siteaccess or
   a design higher in the list may win.
3. **AutoReload is off.** With `[TwigSettings] AutoReload=disabled` a changed `.twig` is not compiled again; clear the
   compiled templates: `./console exp:ezcache --clear-id=twig`.
4. **The template failed.** Look for `<!-- twig:` in the page source, or run `exp:template:check` on the file.

Velocity's workers compile a changed `.twig` on the next request without a restart: the compiled class name holds the
file's modification time.

## Related

- [Troubleshooting and FAQ](twig-templates-troubleshooting.md)
- [Checking template syntax](template-syntax-check.md)
- [Template check rules](../reference/template-check-rules.md)
- [The Exp debug bar](../features/6.0/exp-debug-bar.md) and [template path comments](../features/6.0/template-path-comments.md)
- [Speed of the three template engines](template-engines-speed.md)
