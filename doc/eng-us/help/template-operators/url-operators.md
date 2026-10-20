---
title: URL, design, INI and HTTP operators
audience: developer, designer, app-creator
category: development
order: 300
summary: The ten operators of the kernel's eZURLOperator (ezurl, ezroot, ezdesign, ezimage, exturl, ezsys, ezhttp, ezhttp_hasvariable, ezini, ezini_hasvariable), how the ezurlmultisite extension takes them over on alpha, and its own ezurl_path, ezurl_siteaccess and ezurl_roots, each with proven examples.
keywords: ezurl, ezroot, ezdesign, ezimage, exturl, ezsys, ezhttp, ezhttp_hasvariable, ezini, ezini_hasvariable, ezurl_path, ezurl_siteaccess, ezurl_roots, eZURLOperator, ezurlmultisite, links, url, quote
type: help
---
# URL, design, INI and HTTP operators

The kernel registers ten operators with one class, `eZURLOperator` (`kernel/common/ezurloperator.php`), in
`kernel/common/eztemplateautoload.php`. They build links (`ezurl`, `ezroot`), find design files (`ezdesign`, `ezimage`),
register external links (`exturl`), and read the environment (`ezsys`), the request (`ezhttp`, `ezhttp_hasvariable`)
and the settings (`ezini`, `ezini_hasvariable`).

On alpha all ten names are registered by the extension `ezurlmultisite` instead (class `eZURLMultisiteURLOperator`,
`extension/ezurlmultisite/classes/ezurlmultisiteurloperator.php`), which extends the kernel class and changes only
`ezurl`. The same extension adds three operators of its own. Every example on this page was rendered on alpha with
the `site` siteaccess from the command line, so links are made without a web request (no `index.php`, scheme `http`).

## Contents

1. [Shared parameters: quote_val and server_url](#shared-parameters-quote_val-and-server_url)
2. [The operators](#the-operators): [ezurl](#ezurl), [ezroot](#ezroot), [ezdesign](#ezdesign), [ezimage](#ezimage), [exturl](#exturl), [ezsys](#ezsys), [ezhttp](#ezhttp), [ezhttp_hasvariable](#ezhttp_hasvariable), [ezini](#ezini), [ezini_hasvariable](#ezini_hasvariable)
3. [How ezurlmultisite takes the operators over](#how-ezurlmultisite-takes-the-operators-over)
4. [The operators of ezurlmultisite](#the-operators-of-ezurlmultisite): [ezurl_path](#ezurl_path), [ezurl_siteaccess](#ezurl_siteaccess), [ezurl_roots](#ezurl_roots)
5. [Compiled templates](#compiled-templates)

## Shared parameters: quote_val and server_url

`eZURLOperator::namedParameterList()` returns one list for all ten operators (the class has no
`namedParameterPerOperator()`):

| Parameter | Type | Required | Default |
|---|---|---|---|
| `quote_val` | string | no | `double` |
| `server_url` | string | no | `relative` |

`quote_val` is used by `ezurl`, `ezroot`, `ezdesign`, `ezimage` and `exturl` at the end of `modify()`: `double`
wraps the result in `"`, `single` in `'`, `no` leaves it bare. Any other value gives double quotes. `server_url`
(`relative` or `full`) is passed to `eZURI::transformURI()` by `ezurl` and `ezroot`; `full` prefixes the scheme and
host. The extension adds a third parameter, `parameters` (array, default empty), which only `ezurl` uses.

When the HTTP tool has `UseFullUrl` set, a result that starts with `/` is turned into
a full address by `eZHTTPTool::createRedirectUrl()` for every one of these five operators.

## The operators

### ezurl

Generated page: [ezurl](../../examples/template-operators/ezurl.md).

- **Input:** a path, a URL alias or a full address (the value before `|`). With the extension also a node or a
  content object.
- **Parameters:** `quote_val`, `server_url`, and with the extension `parameters` (a hash of options, see below).
- **Returns:** the address, quoted unless `quote_val` is `no`.

The kernel code calls `eZURI::transformURI( $operatorValue, false, $server_url )`, which adds the siteaccess path and
`index.php` where the request needs them. A value with a scheme (`https:`, `mailto:`) or `//host` is left alone and,
when quoted, escaped for an HTML attribute by `eZURLOperator::escapeAbsoluteURL()` (that is why `&` became `&amp;` in
line 6 below). Escaping does not make a link safe to follow: a `javascript:` value stays one.

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 90 ) )}
1 {'content/view/full/90'|ezurl}
2 {$node.url_alias|ezurl}
3 {$node.url_alias|ezurl( 'single' )}
4 {$node.url_alias|ezurl( 'no' )}
5 {$node.url_alias|ezurl( 'no', 'full' )}
6 {'https://example.com/a?b=1&c=2'|ezurl}
```

Output on alpha:

```text
1 "/content/view/full/90"
2 "/privacy-policy"
3 '/privacy-policy'
4 /privacy-policy
5 http://alpha.se7enx.com/privacy-policy
6 "https://example.com/a?b=1&amp;c=2"
```

### ezroot

Generated page: [ezroot](../../examples/template-operators/ezroot.md).

- **Input:** a path. **Parameters:** `quote_val`, `server_url`. **Returns:** the quoted address.

The same as `ezurl` without `index.php` and without the siteaccess part: the kernel adds a leading `/` when missing
and calls `eZURI::transformURI( $value, true, $server_url )`. Use it for files served from the web root.

### ezdesign

Generated page: [ezdesign](../../examples/template-operators/ezdesign.md).

- **Input:** a path inside a design (`stylesheets/core.css`). **Parameters:** `quote_val`. **Returns:** the quoted
  path of the first match.

`eZURLOperator::eZDesign()` looks the file up in every design base (`eZTemplateDesignResource::allDesignBases()`:
the siteaccess design, the additional designs, the extension designs, `standard`) and returns the www directory plus
the path of the first file found, HTML-escaped. When nothing matches it logs two warnings ("Design element ... does
not exist in any design" and the files it tried) and returns `design/<SiteDesign>/<path>`.

### ezimage

Generated page: [ezimage](../../examples/template-operators/ezimage.md).

- **Input:** an image name. **Parameters:** `quote_val`; a second positional value `true()` asks for the path without
  the leading `/` when the installation runs in the web root (www directory empty). **Returns:** the quoted path.

Like `ezdesign`, but it searches the `images` folder of each design base. Missing images log the same two warnings.

```tpl
1 {'/robots.txt'|ezroot}
2 {'favicon.ico'|ezroot( 'no' )}
3 {'stylesheets/core.css'|ezdesign( 'no' )}
4 {'1x1.gif'|ezimage( 'no' )}
5 {'1x1.gif'|ezimage( 'no', true() )}
```

Output on alpha:

```text
1 "/robots.txt"
2 /favicon.ico
3 /extension/ezwebin/design/ezwebin/stylesheets/core.css
4 /design/standard/images/1x1.gif
5 design/standard/images/1x1.gif
```

Line 3 shows the design fallback: the first design base in the list that has `stylesheets/core.css` answers, here
the ezwebin design of an extension.

### exturl

Generated page: [exturl](../../examples/template-operators/exturl.md).

- **Input:** an external address. **Parameters:** `quote_val`. **Returns:** the quoted address as stored in the
  link table.

`exturl` looks the address up by its MD5 in the `ezurl` table (`eZURL::urlByMD5()`); when it is not there it
**registers it** with `eZURL::registerURL()`, so the link checker and the URL management in the administration
interface know it.

Not run on alpha: `exturl` writes a row into the `ezurl` table for every address it has not seen, and these examples
change nothing. Its use is `{'https://example.com/'|exturl}`, which gives `"https://example.com/"`.

### ezsys

Generated page: [ezsys](../../examples/template-operators/ezsys.md).

- **Input:** none (it is called as a function). **Parameter:** the attribute name, positional. **Returns:** the
  value of `eZSys::attribute()`.

The attributes are `wwwdir`, `sitedir`, `indexfile`, `indexdir`, `querystring` and `hostname`
(`eZSys::attributes()` in `lib/ezutils/classes/ezsys.php`). Without a name the operator logs the warning "Requires
attributename"; an unknown name logs "No such attribute". The values describe the current request, so on the command
line `wwwdir`, `indexfile` and `querystring` are empty.

### ezhttp

Generated page: [ezhttp](../../examples/template-operators/ezhttp.md).

| Position | Meaning | Default |
|---|---|---|
| 1 | variable name | (none: `ezhttp()` returns the `eZHTTPTool` object itself) |
| 2 | `post`, `get`, `session` or `cookie` | `post` |
| 3 | `true()` or `'hasVariable'`: return whether it exists instead of its value | |

- **Input:** none (called as a function).
- **Returns:** the variable's value; with the existence check `true` or `false`.
- An unknown type logs the warning "Unknown http type" and falls back to `post`.
- A missing variable without the existence check is an **error** ("Unknown get variable 'q'"), so test first.
- For `session`, `null` comes back when no session has been started yet (lazy sessions).

The value is the raw request value: wash it before output (`|wash`).

### ezhttp_hasvariable

Generated page: [ezhttp_hasvariable](../../examples/template-operators/ezhttp_hasvariable.md).

The same code as `ezhttp` with the existence check always on: parameters 1 (name) and 2 (type, default `post`),
no input, returns `true` or `false` (or `null` for a session that has not started). Lines 1 to 3 below use both
forms; lines 4 to 6 show `ezhttp()` without a name and `ezsys`.

```tpl
1 {ezhttp_hasvariable( 'q', 'get' )|choose( 'no', 'yes' )}
2 {ezhttp( 'q', 'get', 'hasVariable' )|choose( 'no', 'yes' )}
3 {ezhttp_hasvariable( 'theme', 'cookie' )|choose( 'no', 'yes' )}
4 {ezhttp()|get_class}
5 wwwdir=[{ezsys( 'wwwdir' )}] indexfile=[{ezsys( 'indexfile' )}]
6 indexdir=[{ezsys( 'indexdir' )}] hostname=[{ezsys( 'hostname' )}] querystring=[{ezsys( 'querystring' )}]
```

Output on alpha:

```text
1 no
2 no
3 no
4 ezhttptool
5 wwwdir=[] indexfile=[]
6 indexdir=[] hostname=[alpha.se7enx.com] querystring=[]
```

### ezini

Generated page: [ezini](../../examples/template-operators/ezini.md).

| Position | Meaning | Default |
|---|---|---|
| 1 | group (block) | required: without it the error "Missing group name parameter" |
| 2 | setting | required: without it the error "Missing variable name parameter" |
| 3 | INI file | `site.ini` |
| 4 | directory to read the file from directly | (the normal override chain) |
| 5 | compiled templates: `true()` reads the value at run time instead of compile time | `[eZINISettings] DynamicTemplateMode` |
| 6 (`ezini` only) | `true()` or `'hasVariable'`: return whether it exists | |

- **Returns:** the setting (a string, or an array for `Name[]` settings); with the existence check `true`/`false`.
- A missing setting is an error ("No such variable ... for site.ini") unless parameter 4 is given, in which case the
  result is an empty string.
- **Input:** none (called as a function).
- The values come from the resolved settings of the current siteaccess, with all overrides and extensions applied.

### ezini_hasvariable

Generated page: [ezini_hasvariable](../../examples/template-operators/ezini_hasvariable.md).

`ezini` with the existence check always on: parameters 1 to 5 as above, returns `true` or `false`, never an
error. Lines 6 and 7 below; line 5 is the same check through parameter 6 of `ezini`.

```tpl
1 {ezini( 'SiteSettings', 'DefaultAccess' )}
2 {ezini( 'DesignSettings', 'SiteDesign' )}
3 {ezini( 'TemplateSettings', 'TemplateCompile' )}
4 {ezini( 'MultisiteSettings', 'ResolveUnprefixedPaths', 'ezurlmultisite.ini' )}
5 {ezini( 'SiteSettings', 'NoSuchSetting', 'site.ini', false(), false(), true() )|choose( 'missing', 'present' )}
6 {ezini_hasvariable( 'SiteSettings', 'DefaultAccess' )|choose( 'no', 'yes' )}
7 {ezini_hasvariable( 'SiteSettings', 'NoSuchSetting' )|choose( 'no', 'yes' )}
8 {ezini( 'SiteAccessSettings', 'AvailableSiteAccessList' )|count} siteaccesses
```

Output on alpha:

```text
1 site
2 media
3 enabled
4 disabled
5 missing
6 yes
7 no
8 18 siteaccesses
```

## How ezurlmultisite takes the operators over

The extension switches itself on in `extension/ezurlmultisite/settings/site.ini.append.php`:

```ini
[TemplateSettings]
ExtensionAutoloadPath[]=ezurlmultisite
```

That makes the template engine read `extension/ezurlmultisite/autoloads/eztemplateautoload.php` after the kernel's
autoload file. It registers `eZURLMultisiteURLOperator` for all ten names. Because a later registration of a name
replaces the earlier one in `eZTemplate::$Operators`, the kernel class is never created. The comment in that file
says why **all ten** names must be listed: the engine registers an object for every name of its `operatorList()` the
first time one of them runs, so a single missing name would bring the kernel object back for the rest of the
request.

The subclass changes `ezurl` only:

- With a non-empty `parameters` hash, or with `[OperatorSettings] ForceAbsoluteURL=enabled` in
  `ezurlmultisite.ini`, it calls `eZURLMultisite::buildURL()`, which knows these keys: `absolute`, `siteaccess`,
  `hash`, `query`, `user_parameters`, `no_self_link`, `node_id`, `link_type`, `protocol`, `debug`
  (`eZURLMultisite::extractOptions()`).
- Otherwise it asks `eZURLMultisite::crossSiteURL()` whether the path, node or object belongs to another
  participating siteaccess. If it does, the link is that siteaccess' address; if not, the kernel code runs unchanged.

The participating siteaccesses are `[MultisiteSettings] SiteAccessList[]`, or, when that is empty, every siteaccess
with `RequireUserLogin=false` and a path prefix. Below, node 74 belongs to the Bold Agency site, so a template on the
`site` siteaccess links to it under `/bold/` without being told to:

```tpl
{def $bold = fetch( 'content', 'node', hash( 'node_id', 74 ) )}
1 {$bold.url_alias|ezurl( 'no' )}
2 {$bold|ezurl( 'no' )}
3 {$bold.url_alias|ezurl_path}
4 {$bold.url_alias|ezurl_siteaccess}
5 {'privacy-policy'|ezurl_siteaccess}
6 {'privacy-policy'|ezurl( 'no', 'relative', hash( 'hash', 'top', 'query', 'a=1' ) )}
7 {'privacy-policy'|ezurl( 'no', 'relative', hash( 'absolute', true() ) )}

{foreach ezurl_roots() as $name => $r}
{$name}: host={$r.host} prefix={$r.prefix} root={$r.root_node_id}

{/foreach}
```

Output on alpha:

```text
1 /bold/privacy-policy
2 /bold/privacy-policy
3 privacy-policy
4 bold
5 site
6 /privacy-policy?a=1#top
7 http://alpha.se7enx.com/privacy-policy
site: host=alpha.se7enx.com prefix=fit-healthy root=89
bold: host=alpha.se7enx.com/bold prefix=bold-agency root=73
bold_ger: host=alpha.se7enx.com/bold_ger prefix=bold-agency root=73
starter: host=alpha.se7enx.com/starter prefix=starter root=31927
starter_ger: host=alpha.se7enx.com/starter_ger prefix=starter root=31927
```

## The operators of ezurlmultisite

Registered by `eZURLMultisiteOperator` (`extension/ezurlmultisite/classes/ezurlmultisiteoperator.php`), only while
the extension is active. One parameter list for all three:

| Parameter | Type | Required | Default |
|---|---|---|---|
| `parameters` | array | no | empty |
| `quote_val` | string | no | `double` |
| `server_url` | string | no | `relative` |

They have no generated pages; the example in the previous section shows all three.

### ezurl_path

- **Input:** a path, a node or an object. **Returns:** the path without the path prefix of the siteaccess it belongs
  to (`eZURLMultisite::buildPath()`), unquoted; `parameters.siteaccess` chooses the siteaccess. Line 3 of the
  example above.

### ezurl_siteaccess

- **Input:** the same. **Returns:** the name of the siteaccess that should show it
  (`eZURLMultisite::buildSiteAccessName()`): `parameters.siteaccess` when given, an empty string for an absolute
  address, and the current siteaccess for content that belongs to no other site. Lines 4 and 5 above.

### ezurl_roots

- **Input:** none (call it as `ezurl_roots()`). **Returns:** a hash per participating siteaccess with `siteaccess`,
  `host`, `prefix` and `root_node_id` (`eZURLMultisite::buildRoots()`). The loop at the end of the example above.

## Compiled templates

The kernel's hints mark all ten operators for element transformation: when the input and parameters are constants,
the compiler can build the result at compile time. `ezini` with constant group and name is read **when the template
is compiled** and stored in the compiled file (`eZURLOperator::iniTrans()`), unless parameter 5 is `true()` or
`[eZINISettings] DynamicTemplateMode=enabled`: then the compiled code reads the INI on every run. After changing a
setting that a template reads with `ezini`, clear the compiled templates as well as the INI cache.

`eZURLMultisiteURLOperator::operatorTemplateHints()` switches the transformation off for `ezurl`, because which
siteaccess a link belongs to depends on the siteaccess that shows the compiled template.

## Related pages

- [Registration order and overrides](registration-order-and-overrides.md): the general rules behind the ezurlmultisite takeover
- [Overriding kernel operators](overriding-kernel-operators.md)
- [URL operators for designers](../designers/templates/url-operators.md)
- [The ezini operator](../designers/templates/ezini-operator.md)
- [The ezhttp operator](../designers/templates/ezhttp-operator.md)
- [Operator reference](../designers/templates/operator-reference.md)
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
