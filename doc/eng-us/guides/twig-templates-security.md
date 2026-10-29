# Twig templates: security and escaping

This guide is for site builders and template developers who decide what a `.twig` may do, and who print user input.
It explains the sandbox that is on by default, what it allows and refuses, how to change it, and how escaping works
(as in a `.tpl`: you escape with `wash`). The outputs below were printed on a development installation.

## The sandbox

`settings/twig.ini [TwigSettings] Security=enabled` (the default) runs every `.twig` in Twig's sandbox with the
policy of `expTwigSecurity`:

- **Every bridged operator, template function and `exp` tag is allowed**, with the template system's own rules (a
  `fetch()` sees what the current user may see, as in a `.tpl`).
- **Twig's own tags, filters and functions only as listed**: `SecurityAllowedTagList[]` (`if`, `for`, `set`, `apply`,
  `block`, `extends`, `include`, `embed`, `macro`, `import`, `from`, `use`, `with`, `verbatim`, `guard`, `types`),
  `SecurityAllowedFilterList[]` (`abs`, `batch`, `capitalize`, `column`, `date`, `default`, `escape`, `first`,
  `format`, `join`, `json_encode`, `keys`, `last`, `length`, `lower`, `merge`, `nl2br`, `number_format`, `raw`,
  `replace`, `reverse`, `round`, `slice`, `sort`, `split`, `striptags`, `title`, `trim`, `upper`, `url_encode`),
  `SecurityAllowedFunctionList[]` (`attribute`, `block`, `cycle`, `max`, `min`, `parent`, `random`, `range`).
- **No method call and no property of an object**: values are read through their attributes (`node.name`,
  `node.data_map.title.content`), as in a `.tpl`. `SecurityAllowedMethodList[]` (`Class::method`) and
  `SecurityAllowedPropertyList[]` (`Class::property`) open single ones.

What the sandbox refuses is found when the template is compiled. `exp:template:check --level=strict` reports it
before a page does:

```
ERROR  twig  sec.twig:1
       Tag "do" is not allowed. (the sandbox would refuse the template) [TWIG-SECURITY-001]
       > 1 | {% do 1 %}
```

A method call fails when it runs; with the debug output on, the message is left as a comment in the page:

```
<!-- twig: rendering .../example.twig failed: Calling &quot;attribute&quot; method on a &quot;expTwigObjectProxy&quot; object is not allowed ... -->
```

To allow a tag, filter or function of Twig's in a siteaccess whose templates you trust, add it to the list in that
siteaccess's `twig.ini.append.php`:

```ini
[TwigSettings]
SecurityAllowedTagList[]=do
```

`Security=disabled` turns the sandbox off (compiled templates are then kept in `<cache directory>/twig/compile`, apart
from the sandboxed ones). Only do that for templates that no editor or extension you do not control can change.

## Escaping

As in a `.tpl`, nothing is escaped unless the template says so (`[TwigSettings] Autoescape=disabled`): a converted
template prints exactly what the `.tpl` printed. Escape every value that comes from input with `wash`:

```twig
{{ html }} / {{ html|wash }}
```

```
<b>bold</b> / &lt;b&gt;bold&lt;/b&gt;
```

`wash` takes the same types as in a `.tpl`: `wash` (xhtml), `wash('email')`, `wash('javascript')`. Twig's own
`escape` (`e`) works too; `wash` is the one the rest of the design uses, so the two engines escape alike.

`Autoescape=enabled` (or a strategy name, `html`) makes Twig escape every printed value. Then values that are
already HTML (the output of `attribute_view_gui`, a rendered rich text) must be printed with `|raw`; a converted
design is written for `Autoescape=disabled`.

## What a template can reach

- The variables of its namespace and what the template system's operators and fetch functions return, with the
  current user's permissions.
- Files below the installation through `{% expinclude %}` (design resource) and Twig's `{% include %}` (below the
  design's template directories and the installation root). Neither reads outside the installation.
- No PHP: a `.twig` cannot run PHP code, call PHP functions or create objects.

## Related

- [Writing and running .twig templates](twig-templates.md)
- [Smarty templates: security](smarty-templates-security.md)
- [Checking template syntax](template-syntax-check.md)
