---
title: Pagelayout variables
audience: designer
category: theme-design
order: 70
summary: Every variable the kernel gives the pagelayout ($module_result, $site, $current_user, $access_type, $ui_context and more), the keys of $module_result.content_info for content pages, and examples of using them.
keywords: pagelayout variables, module_result, content_info, site, current_user, access_type, ui_context, navigation_part, uri_string, requested_uri_string, path, title_path, view_parameters, persistent_variable
type: help
---
# Pagelayout variables

This page is for designers who write or read a pagelayout. It lists the variables the kernel sets before it renders
`pagelayout.tpl`, as set in `kernel/private/classes/ezpkernelweb.php`, and the keys a content view adds in
`kernel/classes/eznodeviewfunctions.php`.

## Contents

1. [The variables](#the-variables)
2. [$module_result](#module_result)
3. [$module_result.content_info on content pages](#module_resultcontent_info-on-content-pages)
4. [$site](#site)
5. [Examples](#examples)
6. [Variables you should not expect](#variables-you-should-not-expect)

## The variables

| Variable | What it holds |
|---|---|
| `$module_result` | what the module view produced (a hash, below) |
| `$site` | the site's title, design, HTTP header values, URI, meta data, version, text direction |
| `$current_user` | the signed-in user (an `eZUser`), the anonymous user for visitors; `false` on the few pages that run without a user object |
| `$anonymous_user_id` | `site.ini [UserSettings] AnonymousUserID` |
| `$access_type` | the matched siteaccess (`name`, the match type ...) |
| `$navigation_part` | the navigation part of the view (the admin uses it for the active tab) |
| `$uri_string` | the URI of the request as the kernel resolved it |
| `$requested_uri_string` | the URI as requested (before a URL alias was translated) |
| `$ui_context` | the context of the view as its module declares it, for example `view`, `edit`, `browse`, `administration`, `authentication` (the `ui_context` entries of `kernel/*/module.php`) |
| `$ui_component` | the module name of the view |
| `$warning_list` | kernel warnings to show, or `false` |

## $module_result

| Key | Content |
|---|---|
| `content` | the HTML of the view: print it once with `{$module_result.content}` |
| `path` | the breadcrumb path: a list of hashes with `text`, `url`, `url_alias`, `node_id`; `false` when the view sets none |
| `title_path` | like `path`, for the `<title>` |
| `uri` | the request URI |
| `content_info` | for content views: facts about the node shown (below) |
| `view_parameters` | the view parameters of the URL, e.g. `(offset)/20` gives `offset` 20 |
| `node_id`, `section_id` | for content views |
| `navigation_part` | identifier of the navigation part |
| `ui_context`, `ui_component` | as above |
| `is_mobile_device`, `mobile_device_alias` | from the kernel's mobile device detection |
| `errorType`, `errorNumber` | on error pages (the media design's title template reads them) |

## $module_result.content_info on content pages

A content view (`content/view`) fills `content_info` with these keys (from `eZNodeviewfunctions`):

| Key | Example use |
|---|---|
| `node_id`, `object_id`, `parent_node_id` | fetch the node; mark the current menu item |
| `class_id`, `class_identifier` | a class-specific body class or header |
| `viewmode` | `full`, or a special mode such as the media design's `payload` |
| `node_depth` | how deep the page is in the tree |
| `url_alias`, `main_node_url_alias` | canonical links |
| `section_id`, `section_identifier`, `navigation_part_identifier` | section-based styling |
| `remote_id`, `node_remote_id`, `parent_node_remote_id`, `parent_object_remote_id` | stable identifiers that do not change between installations |
| `parent_class_id`, `parent_class_identifier` | styling by the parent's class |
| `class_group`, `state`, `state_identifier` | class groups and object states |
| `current_language`, `language_mask` | the language shown |
| `offset` | the paging offset |
| `persistent_variable` | what view templates set for the pagelayout (The persistent variable) |

`content_info` is not an array on pages of other modules (search, user, error). Always test before you read it:

```tpl
{if and( is_array( $module_result.content_info ), is_set( $module_result.content_info.class_identifier ) )}
    ...
{/if}
```

## $site

| Key | Content |
|---|---|
| `title` | `site.ini [SiteSettings] SiteName` |
| `design` | `site.ini [DesignSettings] SiteDesign` |
| `http_equiv.Content-Type` | `text/html; charset=...` |
| `http_equiv.Content-language` | the language of the page, for `<html lang="...">` |
| `text_direction` | `rtl` for a right-to-left interface language, else `ltr` |
| `uri` | the original URI object |
| `meta` | `site.ini [SiteSettings] MetaDataArray`, with a `description` built from the path when none is set |
| `version` | the Exponential version |
| `page_title` | the module's title |

## Examples

A body class per content class, so one stylesheet can style article pages differently from folders:

```tpl
{def $body_class = 'page'}
{if and( is_array( $module_result.content_info ), is_set( $module_result.content_info.class_identifier ) )}
    {set $body_class = concat( 'page page-', $module_result.content_info.class_identifier )}
{/if}
<body class="{$body_class|wash}">
```

A signed-in user's name in the header:

```tpl
{if $current_user.is_logged_in}
    <span class="user-name">{$current_user.contentobject.name|wash}</span>
    <a href={'/user/logout'|ezurl}>{'Sign out'|i18n( 'design/mytheme' )}</a>
{else}
    <a href={'/user/login'|ezurl}>{'Sign in'|i18n( 'design/mytheme' )}</a>
{/if}
```

The language attribute and direction:

```tpl
<html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
```

## Variables you should not expect

- **`$node`**: not set in the pagelayout (the kernel unsets it); fetch the node from `content_info.node_id`
  (since 3.9, see `doc/features/3.9/no_more_node_in_pagelayout.txt`).
- **Variables of view templates**: a `{def}` in `node/view/full.tpl` does not reach the pagelayout. Use the
  persistent variable to hand values over.
- **Anything user-specific inside a cache-block** without the user in the keys: the cached copy would be shown to
  everybody.

## Related pages

- [The pagelayout](the-pagelayout.md)
- Module result and content info
- Breadcrumbs
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
