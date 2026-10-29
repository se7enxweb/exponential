# Smarty templates: a first .stpl, step by step

This tutorial is for template developers who have never written a `.stpl`. You start from a siteaccess of your own and
end with a folder page rendered by Smarty: an override rule that selects the template, a variable, operators, a fetch
of the folder's children, a loop over them with paging, an include of a `.stpl` part and of a `.tpl` part, and a
value handed back to the pagelayout. Every step ends with something to check in the browser. It takes about forty
minutes.

You need a running installation with content (the shipped demo content or your own) and a folder that has a few
children. Every command runs from the installation root; add `--allow-root-user` when you are logged in as `root`
(the examples write it out). Smarty 5 comes with the installation: `ls vendor/smarty/smarty/src/Smarty.php` prints the
path. If it does not, read [Check that Smarty is available](smarty-templates.md#2-check-that-smarty-is-available)
first.

The pages that go deeper:

- [Smarty templates: writing and running .stpl templates](smarty-templates.md), the guide;
- [Operator reference](smarty-templates-operator-reference.md) and [Template functions and blocks](smarty-templates-functions.md);
- [Troubleshooting and FAQ](smarty-templates-troubleshooting.md) when something does not appear.

## 1. A siteaccess of your own

Work in a siteaccess of your own, so the public site is never touched. Here it is called `stpltest`, a copy of the
public siteaccess `site`:

```bash
cp -r settings/siteaccess/site settings/siteaccess/stpltest
```

Find out which designs `site` uses:

```bash
./console exp:ini where site/DesignSettings/SiteDesign site --allow-root-user
./console exp:ini where site/DesignSettings/AdditionalSiteDesignList site --allow-root-user
```

Give the new siteaccess a design of its own in front of them. In `settings/siteaccess/stpltest/site.ini.append.php`,
replace the `[DesignSettings]` block (here `site` used the design `media`; put the list you found, with the old
`SiteDesign` first):

```ini
[DesignSettings]
SiteDesign=tutorial
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=media
AdditionalSiteDesignList[]=ezwebin
AdditionalSiteDesignList[]=standard
AdditionalSiteDesignList[]=base
```

Create the design directory. A design in the installation's `design/` directory is found by its name:

```bash
mkdir -p design/tutorial/override/templates/full design/tutorial/templates/parts
```

Make the siteaccess known in `settings/override/site.ini.append.php`, next to the entries already there:

```ini
[SiteSettings]
SiteList[]=stpltest

[SiteAccessSettings]
AvailableSiteAccessList[]=stpltest
```

Let visitors in: in the administration, **Users > Roles and policies**, edit the **Anonymous** role and add
`stpltest` to the SiteAccess limitation of its `user/login` policy. Then clear the INI cache:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

**Check.** With URI matching (`[SiteAccessSettings] MatchOrder=uri`, the default of many installations), open
`https://<your host>/stpltest/`. The site looks like `site`: nothing is overridden yet. With host matching, add a
`HostMatchMapItems[]` line for a host of the new siteaccess instead.

## 2. An override rule that selects a .stpl

Tell the siteaccess to render the full view of folders with a template of the tutorial design. Create
`settings/siteaccess/stpltest/override.ini.append.php` (if the copy already has one, add the block at the top: the
first matching rule wins):

```ini
<?php /* #?ini charset="utf-8"?

[folder_full_stpl]
Source=node/view/full.tpl
MatchFile=full/folder.stpl
Subdir=templates
Match[class_identifier]=folder

*/ ?>
```

`MatchFile` is looked up below `override/templates/` of every design of the siteaccess, so this rule uses
`design/tutorial/override/templates/full/folder.stpl`. The rule is the same as for a `.tpl`; only the suffix of
`MatchFile` decides that Smarty renders it.

Write a first version of `design/tutorial/override/templates/full/folder.stpl`:

```smarty
<div class="content-view-full class-folder">
    <h1>{$node.name|wash}</h1>
    <p>Rendered by Smarty {$smarty.version}.</p>
</div>
```

A new template file and a new override rule need the template caches and the rendered views cleared:

```bash
php bin/php/ezcache.php --clear-tag=template --allow-root-user
php bin/php/ezcache.php --clear-id=content --allow-root-user
```

**Check.** Open a folder on `stpltest`. The page shows the folder's name in the heading and the line "Rendered by
Smarty 5.x." inside the usual pagelayout. Open the same folder on `site`: unchanged.

From now on, a change to `folder.stpl` is seen on the next request (Smarty recompiles a changed file while
`[SmartySettings] CompileCheck=enabled`, the default). If the content view cache serves the page, clear the content
cache, or turn the view cache off in the siteaccess while you work
(`[ContentSettings] ViewCaching=disabled` in `site.ini.append.php` of `stpltest`; turn it on again when you are done).

## 3. Variables and attributes

A full view gets the same variables as its `.tpl` would: `$node` (the node shown), `$view_parameters` (the
`/(offset)/20` parts of the address) and the others of the content view. Exponential objects are read with Smarty's
dot syntax, as in a `.tpl`. Add below the heading:

```smarty
    <p class="byline">
        Published {$node.object.published|l10n:'shortdatetime'}
        by {$node.object.owner.name|wash},
        {$node.children_count} items below.
    </p>
    {if isset($node.data_map.short_description) && $node.data_map.short_description.has_content}
        <div class="attribute-short">{attribute_view_gui attribute=$node.data_map.short_description}</div>
    {/if}
```

What this shows:

- `$node.object.owner.name` walks attributes through objects: each step is the attribute of the object before it.
- `isset($node.data_map.short_description)` is Smarty's `isset`; it is true when the attribute exists and is not
  `null`. The `.tpl` form `is_set(...)` works too.
- `{attribute_view_gui attribute=...}` is the template function of a `.tpl`, written as a Smarty tag with the same
  named parameters. It renders the attribute with its datatype's view template (a `.tpl` or a `.stpl`).

**Check.** The byline shows the date in the siteaccess's locale, the owner and the number of children. If the
folder class has no `short_description`, nothing more appears; that is the `isset`.

## 4. Operators

Every template operator is available, as a Smarty modifier (`$value|operator:param1:param2`) or as a function
(`operator(param1, param2)`). Change the paragraph under the heading and add a link back to the parent:

```smarty
    <p>{'Rendered by Smarty %version.'|i18n:'design/tutorial':expvoid():(['%version' => $smarty.version])}</p>
    <p><a href={$node.parent.url_alias|ezurl}>{'Up one level'|i18n:'design/tutorial'}</a></p>
```

- `i18n` translates its input in the context given as the first parameter; the second parameter is a comment, left
  empty with `expvoid()`; the third gives the values of `%version`. Without a translation the text is shown as it is.
- `ezurl` turns a URL alias into a link for the current siteaccess, with the quotes around it (`href=` takes no quotes
  of its own here). `ezurl:'no'` gives the address without quotes, `ezurl:'no':'full'` with the host.
- Modifier parameters follow colons. `|i18n('design/tutorial')`, the `.tpl` spelling, is a Smarty syntax error.

Where Smarty has a modifier of the same name as an operator (`count`, `implode`, `explode` ...), the operator is
called with the prefix `exp_`. Add the class identifiers of the children, comma separated:

```smarty
    {$classes = $node.children|expcolumn:'class_identifier'}
    <p>Classes below: {$classes|exp_implode:', '|wash}</p>
```

`expcolumn` takes one attribute of every item of an array. `exp_implode` is the Exponential operator `implode`;
plain `implode` would be Smarty's own, deprecated in Smarty 5. The [operator reference](smarty-templates-operator-reference.md)
lists every operator with the form it needs.

**Check.** The link goes to the parent folder; the class list shows identifiers such as `article, folder`.

## 5. A fetch with paging

`fetch()` calls any module fetch function, with its parameters as an array literal (the `.tpl` `hash(...)`). Fetch
the children ten at a time, starting at the offset in the address, sorted as the folder says:

```smarty
    {$page_limit = 10}
    {$children = fetch('content', 'list', ['parent_node_id' => $node.node_id,
                                           'offset' => $view_parameters.offset,
                                           'limit' => $page_limit,
                                           'sort_by' => $node.sort_array])}
    {$children_count = fetch('content', 'list_count', ['parent_node_id' => $node.node_id])}
```

`$view_parameters.offset` is 0 on the first page and the number in `/(offset)/10` on the next ones. `$node.sort_array`
is the sorting the editor chose for the folder.

## 6. A loop

Loop over the children with Smarty's `{foreach}`, and render each one with its line view:

```smarty
    <ul class="children">
    {foreach $children as $child}
        <li class="{if $child@iteration is odd}odd{else}even{/if}">
            {node_view_gui view='line' content_node=$child}
        </li>
    {foreachelse}
        <li>{'This folder is empty.'|i18n:'design/tutorial'}</li>
    {/foreach}
    </ul>
```

- `{foreachelse}` is shown when the array is empty.
- `$child@iteration` counts from 1, `$child@index` from 0, `$child@first` and `$child@last` are true on the first and
  the last item.
- `{node_view_gui view='line' content_node=$child}` renders the child with the line view of its class, which may be a
  `.tpl` or a `.stpl` chosen by override rules.
- `{foreach}` iterates a variable. A computed source (a fetch, a value with modifiers) must be assigned to a variable
  first, as `$children` above; `{foreach fetch(...) as $child}` is refused with "missing 'from' attribute".

**Check.** The children appear, alternately with the classes `odd` and `even`; an empty folder shows the message.

## 7. Includes: a .stpl part and a .tpl part

Put the paging navigation in a part of its own. Create `design/tutorial/templates/parts/paging.stpl`:

```smarty
{if $children_count > $page_limit}
    {expinclude uri='design:navigator/google.tpl'
                name='navigator'
                page_uri=$node.url_alias
                item_count=$children_count
                view_parameters=$view_parameters
                item_limit=$page_limit}
{/if}
```

and include it from `folder.stpl`, after the list:

```smarty
    {expinclude uri='design:parts/paging.stpl'}
```

- `{expinclude}` is the template system's `{include}`: the URI goes through the design resource and the override rules,
  and the included template may be a `.stpl` or a `.tpl`.
- `parts/paging.stpl` is included without `name=`, so it shares the variables of `folder.stpl` (`$children_count`,
  `$page_limit`, `$node`, `$view_parameters`) and runs as a Smarty subtemplate, without the detour through the template
  system.
- `navigator/google.tpl` is the standard design's `.tpl` navigator. It is included with `name='navigator'`, so it runs
  in a namespace of its own, as the classic `{include name=navigator uri=...}` does; the named parameters become its
  variables.

**Check.** On a folder with more than ten children, the page numbers appear below the list; page 2 has
`/(offset)/10` in the address and shows the next children.

## 8. Handing a value back to the pagelayout

A `.stpl` writes the variables it assigns back to the template that rendered it, as `{def}` and `{set}` do. To give
the pagelayout a value it reads from a global namespace, use `{expset}` with `scope='global'`. Many designs read the
`persistent_variable` this way (the `.tpl` form is `{set scope=global persistent_variable=...}`):

```smarty
{expset name='persistent_variable' value=['extra_menu' => false, 'left_menu' => false] scope='global'}
```

Whether the pagelayout reads it depends on your design; the media design does not, the `ezwebin` design does.

## 9. The whole template

`design/tutorial/override/templates/full/folder.stpl` now reads:

```smarty
<div class="content-view-full class-folder">
    <h1>{$node.name|wash}</h1>
    <p>{'Rendered by Smarty %version.'|i18n:'design/tutorial':expvoid():(['%version' => $smarty.version])}</p>
    <p><a href={$node.parent.url_alias|ezurl}>{'Up one level'|i18n:'design/tutorial'}</a></p>
    <p class="byline">
        Published {$node.object.published|l10n:'shortdatetime'}
        by {$node.object.owner.name|wash},
        {$node.children_count} items below.
    </p>
    {if isset($node.data_map.short_description) && $node.data_map.short_description.has_content}
        <div class="attribute-short">{attribute_view_gui attribute=$node.data_map.short_description}</div>
    {/if}
    {$classes = $node.children|expcolumn:'class_identifier'}
    <p>Classes below: {$classes|exp_implode:', '|wash}</p>

    {$page_limit = 10}
    {$children = fetch('content', 'list', ['parent_node_id' => $node.node_id,
                                           'offset' => $view_parameters.offset,
                                           'limit' => $page_limit,
                                           'sort_by' => $node.sort_array])}
    {$children_count = fetch('content', 'list_count', ['parent_node_id' => $node.node_id])}
    <ul class="children">
    {foreach $children as $child}
        <li class="{if $child@iteration is odd}odd{else}even{/if}">
            {node_view_gui view='line' content_node=$child}
        </li>
    {foreachelse}
        <li>{'This folder is empty.'|i18n:'design/tutorial'}</li>
    {/foreach}
    </ul>
    {expinclude uri='design:parts/paging.stpl'}
</div>
```

Note the design rule of the project for real designs: no `style=""` and no `<style>` in templates; classes such as
`odd` and `even` get their look from the design's stylesheets.

## 10. When something goes wrong

Turn the debug output on for your own address (see [the Exp Debug bar](../features/6.0/exp-debug-bar.md)). A `.stpl`
that fails is replaced by nothing, and the reason is in the debug output with the label `smarty`, and on the page as
an HTML comment `<!-- smarty: ... -->` where the template would have been. Make a mistake on purpose: change
`{/foreach}` into `{/forech}` and reload. The comment reads `<!-- smarty: rendering .../folder.stpl failed: Syntax
error in template ... -->`. Every message and its fix: [Troubleshooting and FAQ](smarty-templates-troubleshooting.md).

## 11. Clean up

When you are done, remove the siteaccess from `SiteList[]` and `AvailableSiteAccessList[]`, remove `stpltest` from the
Anonymous role's limitation, and delete `settings/siteaccess/stpltest/` and `design/tutorial/` (or keep the design and
use it). Clear the INI and template caches.

## Where to go next

- See how fast it is, and why: [Smarty templates: performance (Speed)](smarty-templates-performance.md).
- Port a whole design instead of writing it: [Migrating a design from .tpl to .stpl](smarty-templates-migrating.md).
- Keep pages fast: [Caching and performance](smarty-templates-caching.md).
- Output that is safe: [Security and escaping](smarty-templates-security.md).

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../help/partners/partners-start-here.md) and [How to apply](../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
