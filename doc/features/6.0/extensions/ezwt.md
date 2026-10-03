# ezwt: the website toolbar

`ezwt` ("eZ Website Toolbar LS") is the editing toolbar a logged-in editor sees on the public site:
edit, move, remove, create here, sort sub items, change the state, upload or export a document. It
requires `ezjscore`. [ezwebin](ezwebin.md), [ezflow](ezflow.md), [ezdemo](ezdemo.md) and the
[simple theme](sevenx_themes_simple.md) all build on it.

## Put it in your design

In `pagelayout.tpl`, wrap the toolbar in a cache block keyed by the user's roles so it adds no SQL:

```
{def $user_hash = concat( $current_user.role_id_list|implode( '_' ), '_', $current_user.limited_assignment_value_list|implode( '_' ) )}
{cache-block keys=array( $uri_string, $user_hash )}
    {include uri='design:parts/website_toolbar.tpl' current_node_id=$module_result.node_id}
{/cache-block}
```

Load `stylesheets/websitetoolbar.css` with `ezdesign`. In `content/edit.tpl` include
`design:parts/website_toolbar_edit.tpl`, and in the version view template the matching part. The extension
README has the full text.

## Settings (`websitetoolbar.ini`)

| Block | Key | Default | Meaning |
|---|---|---|---|
| WebsiteToolbarSettings | `ODFDisplayClasses[]` | documentation_page, blog, blog_post, folder, article, article_mainpage, article_subpage, event | Classes for which the OpenDocument import and export buttons are shown |
| WebsiteToolbarSettings | `HideODFContainerClasses[]` | article | |
| WebsiteToolbarSettings | `HiddenContentClasses[]` | banner, common_ini_settings | Classes the toolbar does not offer to create |
| CustomTemplateSettings | `CustomTemplateList[]`, `IncludeInView[<name>]` | object_states, link (full view) | Extra toolbar templates |

## What changed in the Exponential 6 releases

* 6.0.3: HTML5 cleanup of the toolbar templates (void tags) and the stylesheet (vendor prefixes for
  `box-shadow`, `border-radius` and `transition` removed).
* 6.0.5: every visible text is a translation string, with German.
* 6.0.7: the date and date/time fields of the demo design use Exponential UI's calendar,
  `exp::datepicker`; the extension requires `se7enxweb/expui ^1.0.0.1`.
* 6.0.8: **YUI removed.** The sort page (`websitetoolbar/sort`), drag and drop of sub items, runs on jQuery
  with native drag events: the same priorities, the same automatic update through `ezwt::updatepriority`
  (`ezjsc::jqueryio`). See [YUI removal](../../../bc/6.0/yui-removal.md).
* 6.0.9: command line scripts, cronjob parts and module views are classes the files call; copyright
  notices name 1998 - 2026 7x & Exponential Foundation first
  ([CLI, cronjob and view abstractions](../../../bc/6.0/cli_cronjob_view_abstractions.md)).

## Related

* [ezwebin](ezwebin.md), [ezflow](ezflow.md)
* [Chronicle](../../../history/extensions/ezwt.md) and [release notes](../../../changelogs/extensions/ezwt.md)
