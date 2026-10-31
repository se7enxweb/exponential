# Twig templates: troubleshooting and FAQ

This page is for template developers whose `.twig` does not do what they expect. Each entry gives what you see, the
cause and the fix. Most errors are found by `./console exp:template:check <file or --design-dir=...>` before a page
shows them; with the debug output on, a template that fails leaves a comment with the message where its output would
have been, and every failure is written to the debug output and `var/log/error.log`.

## The page shows nothing where the .twig should be

A template that does not compile or fails while it runs prints nothing. Look at the debug output (or the HTML comment
`<!-- twig: rendering ... failed: ... -->`) for the message, or run the check on the file.

## Unknown "wahs" filter

```
ERROR  twig  templates/full/article.twig:1
       Unknown "wahs" filter. [TWIG-SYNTAX-001]
       > 1 | {{ node.name|wahs }}
       hint: did you mean "wash"?
```

The name is no operator of the installation and no filter of Twig's. A typing mistake, or the operator belongs to an
extension that is not active in this siteaccess. `exp_<name>` of an unknown operator compiles but passes its input
through; `--level=strict` reports it as `TWIG-STRICT-UNKNOWN-OPERATOR`.

## Tag "do" is not allowed / Calling "x" method ... is not allowed

The sandbox refused one of Twig's own tags, filters or functions, or a method call on an object. Read values through
their attributes (`node.object.name`, not `node.object.name()` or `node.attribute('name')`); allow a Twig construct in
`[TwigSettings] SecurityAllowed...List[]` of a siteaccess you trust. See [Security](twig-templates-security.md).

## An operator gives a different result than in the .tpl

Check the form: `x|op(a)` is the `.tpl`'s `$x|op( a )`, `op(x, a)` is `op( $x, a )`. For most operators these are
different calls. And check the name: where Twig has a filter of the same name (`merge`, `round`, `replace`, `trim`
...), the plain name is Twig's; the operator is `exp_merge`. See [Operators](twig-templates-operators.md).

## A variable set in an included template is not seen

`{% set %}` in a `.twig` included with `{% expinclude %}` writes back to the shared namespace when that template
ends, so the including template sees it afterwards. A `.tpl` included the same way keeps its `{def}` to itself, as it
always has. Twig's own `{% include %}` passes a copy of the variables and writes nothing back: use `expinclude` where
the included template sets something.

## A change to a .twig does not show

`[TwigSettings] AutoReload=enabled` (the default) compiles a changed file again on the next request, in Velocity's
workers too. If a change does not show: the page may come from a cache (view cache, cache block, the HTTP cache or
Velocity's response cache: clear them, or look with the debug output on), or `AutoReload` is disabled in this
siteaccess (clear the compiled templates: `./console exp:ezcache --clear-id=twig`).

## Twig 3 not found

```
Twig 3 not found: install twig/twig with Composer or set [TwigSettings] LibraryPath in twig.ini
```

The installation's Composer autoload has no Twig 3. Run `composer update` with the installation's
`composer.json`: it requires `se7enxweb/exponential-php-floor` `~1.0.3 || ~1.1.4 || ~1.2.3 || ~1.3.3`, which
installs `twig/twig` `^3.27` on PHP 8.1 and later and `se7enxweb/twig` on PHP 7.4 and 8.0 (a floor older than
x.y.3 installs no Twig). Or install Twig in a directory of its own and set `LibraryPath`
([Installing Twig](../features/6.0/twig-templates.md#installing-twig)). `./console exp:template:engines` shows whether
Twig is available.

## A loop stopped after 10000 rounds

A converted `{while}` or `{do}` stops after `[TwigSettings] WhileLoopLimit` rounds and writes a notice to the debug
output. Raise the limit for that siteaccess, or fix the loop's condition.

## FAQ

**Can a .tpl and a .twig be used in one page?** Yes: each template of a page can be of any engine. A `.tpl` includes a
`.twig` and the other way round, through the design resource.

**Do I have to convert all templates?** No. Convert or write what you want in Twig; the rest stays `.tpl`.

**Does Twig make pages slower?** No: cached pages never reach a template engine, and uncached pages render about as
fast as with `.tpl`. See [Performance](twig-templates-performance.md).

**Can I use `{% extends %}` and blocks?** In hand-written `.twig`, yes. They have no `.tpl` form, so the converter
back to `.tpl` keeps them as a note.

## Related

- [Checking template syntax](template-syntax-check.md)
- [Template check rules](../reference/template-check-rules.md)
- [Writing and running .twig templates](twig-templates.md)
