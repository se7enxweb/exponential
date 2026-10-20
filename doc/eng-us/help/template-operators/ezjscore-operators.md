---
title: ezjscore operators in depth
audience: developer, designer, app-creator
category: development
order: 400
summary: Every operator of the ezjscore extension - the packer operators ezscript, ezscript_require, ezscript_load, ezscriptfiles, ezcss, ezcss_require, ezcss_load, ezcssfiles, the encoders json_encode, xml_encode, node_encode and has_access_to_limitation - with all parameters, the persistent-variable mechanism behind require and load, pack levels, and examples rendered on alpha.
keywords: ezjscore, ezscript, ezscript_require, ezscript_load, ezscriptfiles, ezcss, ezcss_require, ezcss_load, ezcssfiles, json_encode, xml_encode, node_encode, has_access_to_limitation, packer, pack_level, persistent_variable
type: help
---
# ezjscore operators in depth

The extension `ezjscore` (shipped in the kernel repository, `extension/ezjscore/`) registers twelve operators in
`extension/ezjscore/autoloads/eztemplateautoload.php`; its `settings/site.ini.append.php` adds the extension to
`[TemplateSettings] ExtensionAutoloadPath[]`. They exist only while ezjscore is an active extension, which every
shipped design needs. For the designer's view, read [ezjscore template operators](../designers/templates/ezjscore-template-operators.md)
and [The ezcss and ezscript operators](../designers/theme-design/ezcss-and-ezscript-operators.md) first; this page
documents every parameter.

| Class | File | Operators |
|---|---|---|
| `ezjscPackerTemplateFunctions` | `autoloads/ezjscpackertemplatefunctions.php` | the eight packer operators |
| `ezjscEncodingTemplateFunctions` | `autoloads/ezjscencodingtemplatefunctions.php` | `json_encode`, `xml_encode`, `node_encode` |
| `ezjscAccessTemplateFunctions` | `autoloads/ezjscaccesstemplatefunctions.php` | `has_access_to_limitation` |

## Contents

1. [Pack levels and file names](#pack-levels-and-file-names)
2. [The packer operators](#the-packer-operators): [ezscript](#ezscript), [ezcss](#ezcss), [ezscriptfiles](#ezscriptfiles),
   [ezcssfiles](#ezcssfiles), [ezscript_require](#ezscript_require), [ezcss_require](#ezcss_require),
   [ezscript_load](#ezscript_load), [ezcss_load](#ezcss_load)
3. [The encoders](#the-encoders): [json_encode](#json_encode), [xml_encode](#xml_encode), [node_encode](#node_encode)
4. [Access](#access): [has_access_to_limitation](#has_access_to_limitation)

## Pack levels and file names

All packer operators hand their file list to `ezjscPacker` (`extension/ezjscore/classes/ezjscpacker.php`).

- A file name is looked up in the `javascript/` or `stylesheets/` folder of every design base, like `ezdesign`.
  A name such as `ezjsc::jquery` is a server function call (`ezjscServer` functions listed in `ezjscore.ini`) whose
  result is a list of files; arrays inside the list are flattened.
- `pack_level` 0 links the files as they are. Above 0 the files are joined into one file in the cache folder
  (`var/<site>/cache/public/`), and higher levels also minify. `ezjscore.ini` `[eZJSCore] Packer=disabled` forces 0, a
  number there replaces every `pack_level`; when `Packer` is not set, `[TemplateSettings] DevelopmentMode=enabled`
  forces 0. On alpha `Packer=enabled`, so the parameter decides.
- Packing writes the joined file the first time a list is used. The examples on this page use `pack_level` 0 so that
  they write nothing.
- No version query is added to the links; a changed file gets a new packed name because the name includes the file
  times.

```tpl
Packer setting: [{ezini( 'eZJSCore', 'Packer', 'ezjscore.ini' )}]
```

Output on alpha:

```text
Packer setting: [enabled]
```

## The packer operators

### ezscript

Generated page: [ezscript](../../examples/template-operators/ezscript.md).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `script_array` | array | yes | |
| `type` | string | no | `text/javascript` |
| `language` | string | no | empty (opt-in, not valid HTML5) |
| `charset` | string | no | `utf-8` |
| `pack_level` | integer | no | 2 |

- **Input:** none. **Returns:** `<script>` tags (`ezjscPacker::buildJavascriptTag()`). It does not look at what
  was required or loaded before.

### ezcss

Generated page: [ezcss](../../examples/template-operators/ezcss.md).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `css_array` | array | yes | |
| `media` | string | no | `all` |
| `type` | string | no | `text/css` |
| `rel` | string | no | `stylesheet` |
| `charset` | string | no | empty (deprecated) |
| `pack_level` | integer | no | 3 |

- **Input:** none. **Returns:** `<link>` tags (`ezjscPacker::buildStylesheetTag()`); `media` is left out when it is
  `all`.

### ezscriptfiles

Generated page: [ezscriptfiles](../../examples/template-operators/ezscriptfiles.md).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `script_array` | array | yes | |
| `pack_level` | integer | no | 2 |
| `ignore_loaded` | bool | no | `false` |

- **Returns:** the list of addresses instead of tags. Without `ignore_loaded` only the files not already required
  or loaded on this page are returned (and remembered).

### ezcssfiles

Generated page: [ezcssfiles](../../examples/template-operators/ezcssfiles.md).

The same for stylesheets: `css_array`, `pack_level` (default 3), `ignore_loaded`.

```tpl
1 {ezcssfiles( array( 'core.css', 'debug.css' ), 0, true() )|implode( ' ' )}
2 {ezscriptfiles( array( 'ezjsc::jquery' ), 0, true() )|implode( ' ' )}
3 {ezcss( array( 'core.css' ), 'screen', 'text/css', 'stylesheet', '', 0 )}
4 {ezscript( array( 'ezjsc::jquery' ), 'text/javascript', '', 'utf-8', 0 )}
```

Output on alpha:

```text
1 /extension/ezwebin/design/ezwebin/stylesheets/core.css /design/standard/stylesheets/debug.css
2  /extension/expui/design/standard/lib/jquery/jquery-4.0.0.min.js /extension/expui/design/standard/lib/jquery/jquery-migrate-4.0.2.js
3 <link rel="stylesheet" href="/extension/ezwebin/design/ezwebin/stylesheets/core.css" media="screen">

4 <script src="/extension/expui/design/standard/lib/jquery/jquery-4.0.0.min.js"></script>
<script src="/extension/expui/design/standard/lib/jquery/jquery-migrate-4.0.2.js"></script>
```

### ezscript_require

Generated page: [ezscript_require](../../examples/template-operators/ezscript_require.md).

The parameters of `ezscript`. Before `ezscript_load` has run on the page it outputs nothing and appends the files to
`js_files` in the **persistent variable**, so a view template can ask for a script that the pagelayout prints later.
After `ezscript_load` has run it prints tags for the files not yet loaded.

### ezcss_require

Generated page: [ezcss_require](../../examples/template-operators/ezcss_require.md).

The same for stylesheets (`css_files`), with the parameters of `ezcss`.

### ezscript_load

Generated page: [ezscript_load](../../examples/template-operators/ezscript_load.md).

The parameters of `ezscript`, with `script_array` optional. The first call on a page puts its own files **in front of**
the required ones, prints tags for all of them and marks scripts as loaded; later calls print only files not yet
loaded.

### ezcss_load

Generated page: [ezcss_load](../../examples/template-operators/ezcss_load.md).

The same for stylesheets, with the parameters of `ezcss` and `css_array` optional.

The remembered lists live in the persistent variable: in a view template `$persistent_variable`, in the pagelayout
`$module_result.content_info.persistent_variable` (`ezjscPackerTemplateFunctions::setPersistentArray()`), so they
survive the view cache. A persistent variable that is not a hash is reset with the error "persistent_variable was not
an array". The order below shows the whole cycle in one template:

```tpl
{def $persistent_variable = hash()}
1 require: [{ezcss_require( array( 'debug.css' ) )}]
2 load: {ezcss_load( array( 'core.css' ), 'all', 'text/css', 'stylesheet', '', 0 )}
3 remembered: {$persistent_variable.css_files|implode( ', ' )}
4 require after load: {ezcss_require( array( 'pagelayout.css' ), 'all', 'text/css', 'stylesheet', '', 0 )}
5 again: [{ezcss_require( array( 'pagelayout.css' ), 'all', 'text/css', 'stylesheet', '', 0 )}]
```

Output on alpha:

```text
1 require: []
2 load: <link rel="stylesheet" href="/extension/ezwebin/design/ezwebin/stylesheets/core.css">
<link rel="stylesheet" href="/design/standard/stylesheets/debug.css">

3 remembered: core.css, debug.css
4 require after load: <link rel="stylesheet" href="/extension/ezwebin/design/ezwebin/stylesheets/pagelayout.css">

5 again: []
```

Line 5 is empty because `pagelayout.css` was already printed by line 4.

## The encoders

### json_encode

Generated page: [json_encode](../../examples/template-operators/json_encode.md).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `hash` | hash | yes | empty array |

- **Input:** not used: pass the value as the parameter. **Returns:** PHP's `json_encode()` of it. Objects without
  public properties come out as `{}`; give it hashes and arrays.

The result is JSON, not HTML-escaped: inside a `<script type="application/json">` block that is what you want; in an
attribute, wash it.

### xml_encode

Generated page: [xml_encode](../../examples/template-operators/xml_encode.md).

The same parameter. **Returns:** an XML document with a `<root>` element and one element per key
(`ezjscAjaxContent::xmlEncode()`; list items become `<child>` elements).

### node_encode

Generated page: [node_encode](../../examples/template-operators/node_encode.md).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `node` | object | yes | |
| `params` | hash | no | empty |
| `type` | string | no | `json` |

- **Input:** none. **Returns:** a node, a content object or a list of them simplified by
  `ezjscAjaxContent::simplify()`: JSON for `type` `json`, XML for `xml`, the array itself for any other value.
- **params:** `dataMap` (attribute identifiers to include, `array( 'all' )` for every one), `dataMapType` (filter by
  datatype), `fetchPath`, `fetchSection`, `fetchChildrenCount`, `loadImages` with `imageSizes` and
  `imagePreGenerateSizes` (default `small`), `imageDataTypes`, `fetchNodeFunction`.

With `loadImages`, the listed image sizes are generated when they do not exist yet, which writes image files: ask
only for aliases the images already have.

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 90 ) )}
1 {json_encode( hash( 'title', $node.name, 'children', $node.children_count, 'tags', array( 'a', 'b' ) ) )}
2 {xml_encode( hash( 'title', $node.name, 'id', $node.node_id ) )}
{def $data = node_encode( $node, hash( 'dataMap', array( 'title' ) ), false() )}
3 node_encode as array: {foreach $data as $k => $v}{$k}{delimiter} {/delimiter}{/foreach}

4 node_encode as JSON: {node_encode( $node, hash(), 'json' )|count_chars} characters, starts {node_encode( $node, hash(), 'json' )|extract_left( 40 )}
```

Output on alpha:

```text
1 {"title":"Privacy Policy","children":0,"tags":["a","b"]}
2 <?xml version="1.0" encoding="UTF-8"?>
<root><title>Privacy Policy</title><id>90</id></root>3 node_encode as array: name id contentobject_id contentobject_remote_id contentobject_state main_node_id version modified published section_id current_language owner_id class_id class_name path_identification_string translations initial_language can_edit class_identifier is_container node_id parent_node_id node_remote_id url_alias url depth priority hidden_status_string is_hidden is_invisible path children_count image_attributes data_map
4 node_encode as JSON: 838 characters, starts {"name":"Privacy Policy","id":88,"conten
```

## Access

### has_access_to_limitation

Generated page: [has_access_to_limitation](../../examples/template-operators/has_access_to_limitation.md).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `module` | string | yes | empty |
| `function` | string | yes | empty |
| `limitations` | array (hash) | yes | empty |
| `debug` | bool | no | `false` |

- **Input:** none. **Returns:** `true` or `false` for the current user.

`hasAccessTo()` decides first: `yes` and `no` are returned as they are. For `limited` access, the values of every
policy are merged per limitation key that you passed; the call is `true` when each value you passed is among the
merged values (`Subtree` and `User_Subtree` match when your path string contains one of the allowed paths).
Limitation keys of the policies that you did not pass are ignored (noted in the debug output with `debug`). On alpha
the anonymous role reads sections 1, 3 and 67, so section 2 is refused:

```tpl
{def $access = fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'read' ) )}
anonymous content/read: {$access|choose( 'no', 'yes' )}
1 section 1: {has_access_to_limitation( 'content', 'read', hash( 'Section', 1 ) )|choose( 'no', 'yes' )}
2 section 3: {has_access_to_limitation( 'content', 'read', hash( 'Section', 3 ) )|choose( 'no', 'yes' )}
3 section 2: {has_access_to_limitation( 'content', 'read', hash( 'Section', 2 ) )|choose( 'no', 'yes' )}
4 content/edit: {has_access_to_limitation( 'content', 'edit', hash( 'Section', 1 ) )|choose( 'no', 'yes' )}
```

Output on alpha:

```text
anonymous content/read: yes
1 section 1: yes
2 section 3: yes
3 section 2: no
4 content/edit: no
```

## Related pages

- [ezjscore template operators for designers](../designers/templates/ezjscore-template-operators.md)
- [JSON endpoints with ezjscore](../creators/app-creators/json-endpoints-with-ezjscore.md)
- [The persistent variable](../designers/templates/persistent-variable.md)
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
