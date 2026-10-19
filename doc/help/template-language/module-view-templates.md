---
title: Module view templates
audience: developer, designer, app-creator
category: development
order: 300
summary: How a module view renders its templates - the PHP side (eZTemplate::factory(), setVariable(), fetch( 'design:...' )), the $Result array a view returns, and how the kernel hands it to the pagelayout as $module_result together with the other pagelayout variables - proven by running content/view on alpha read-only.
keywords: module view, $Result, $module_result, pagelayout, eZTemplate::factory, setVariable, fetch design, ezpKernelWeb, view template, module.php, kernel
type: help
---
# Module view templates

Every page of an Exponential site is the result of a **module view**: `content/view/full/96` runs the view `view` of
the module `content` with the parameters `full` and `96`. The view is PHP; the HTML comes from templates. This page
follows a request from the view's PHP to its template and on to the pagelayout, with the kernel code that does each
step.

## Contents

1. [The two templates of a page](#the-two-templates-of-a-page)
2. [The PHP side of a view](#the-php-side-of-a-view)
3. [What content/view returns on alpha](#what-contentview-returns-on-alpha)
4. [From $Result to $module_result](#from-result-to-module_result)
5. [Choosing the pagelayout](#choosing-the-pagelayout)
6. [Variables a node view template gets](#variables-a-node-view-template-gets)
7. [Related pages](#related-pages)

## The two templates of a page

1. The view renders **its own template** (for `content/view` that is `node/view/<view mode>.tpl`, or the override
   that matches) and puts the HTML into `$Result['content']`.
2. The kernel then renders the **pagelayout** (`pagelayout.tpl` of the siteaccess design), which places
   `{$module_result.content}` inside the page frame: head, menus, footer.

The view template knows the content; the pagelayout knows the page. They talk through `$module_result` (from the
view's `$Result`) and the [persistent variable](persistent-variable-internals.md).

## The PHP side of a view

A module's `module.php` declares its views; `kernel/section/module.php` for example:

```php
$ViewList['view'] = array(
    'functions' => array( 'view or assign' ),
    'script' => 'view.php',
    'ui_context' => 'view',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array( 'SectionID' ),
    'unordered_params' => array( 'offset' => 'Offset' ) );
```

The view script (`kernel/section/view.php`, whose code lives in
`kernel/private/classes/views/section/view.php`) does what every view does: gets the template engine, sets
variables, fetches a template through the `design:` resource and returns `$Result`:

```php
$tpl = \eZTemplate::factory();
$tpl->setVariable( "view_parameters", $viewParameters );
$tpl->setVariable( "section", $section );

$Result = array();
$Result['content'] = $tpl->fetch( "design:section/view.tpl" );
$Result['path'] = array( array( 'url' => 'section/list',
                                'text' => \ezpI18n::tr( 'kernel/section', 'Sections' ) ),
                         array( 'url' => false,
                                'text' => $section->attribute('name') ) );
```

- `eZTemplate::factory()` returns the one shared engine of the request, with all operators and functions loaded
  from the autoload lists and the `design:` resource registered (`lib/eztemplate/classes/eztemplate.php`).
- `setVariable( $name, $value )` puts a variable into the root namespace; in `section/view.tpl` it is `$section`.
- `fetch( 'design:section/view.tpl' )` finds the file through the design resource (overrides first, then the design
  bases; see [Template resources and overrides](resources-and-overrides.md)) and returns the rendered text.
- `$Result['path']` becomes the breadcrumb; each entry has `text` and `url` (false for the current page).

A view of your own in an extension follows the same pattern; the walk-through is in
[Custom modules and views](../creators/app-creators/custom-modules-and-views.md).

## What content/view returns on alpha

To see a real `$Result`, a read-only CLI script on alpha ran the view the way the kernel does, with the content view
cache and template compiling switched off in memory for that process (no cache file was written), and printed the
structure of the returned array:

```php
$ini = eZINI::instance();
$ini->setVariable( 'ContentSettings', 'ViewCaching', 'disabled' );   // in memory only
$module = eZModule::findModule( 'content' );
$result = $module->run( 'view', array( 'full', 96 ), false, array() );
```

Output on alpha (the `path` and `title_path` lists are shortened to their first and last entry here):

```text
exit status: 1 (STATUS_OK)
$Result: array(11)
  content: string (7085 bytes)
  view_parameters: array(5)
    offset: false
    year: false
    month: false
    day: false
    namefilter: false
  path: array(4)
    0: array(4)
      text: 'Websites'
      url: '/content/view/full/2'
      url_alias: ''
      node_id: 2
    3: array(4)
      text: 'Why Should You Choose Strength Training?'
      url: false
      url_alias: false
      node_id: 96
  title_path: array(4)
    0: array(4)
      text: 'Websites'
      url: '/content/view/full/2'
      url_alias: ''
      node_id: 2
    3: array(3)
      text: 'Why Should You Choose Strength Training?'
      url: false
      url_alias: false
  section_id: 1
  node_id: 96
  navigation_part: 'ezcontentnavigationpart'
  content_info: array(25)
    object_id: 94
    node_id: 96
    parent_node_id: 92
    class_id: 44
    class_identifier: 'ng_article'
    remote_id: 'media-o-958'
    node_remote_id: 'media-n-729'
    offset: false
    viewmode: 'full'
    navigation_part_identifier: 'ezcontentnavigationpart'
    node_depth: 4
    url_alias: 'fitness/why-should-you-choose-strength-training'
    current_language: 'eng-US'
    language_mask: 2
    main_node_id: 96
    main_node_url_alias: false
    persistent_variable: false
    class_group: false
    state: array(2)
      2: 1
      47: 80
    state_identifier: array(2)
    section_identifier: 'standard'
    parent_class_id: 45
    parent_class_identifier: 'ng_landing_page'
    parent_node_remote_id: 'media-n-721'
    parent_object_remote_id: 'media-o-950'
  template_list: array(11)
    0: string (96 bytes)
    1: string (81 bytes)
    2: string (77 bytes)
    3: string (75 bytes)
    4: string (88 bytes)
    5: string (98 bytes)
    6: string (68 bytes)
    7: string (98 bytes)
    8: string (93 bytes)
    9: string (66 bytes)
    10: string (75 bytes)
  cache_ttl: -1
  is_default_navigation_part: false
```

The keys are explained one by one in the [Module result reference](module-result-reference.md). Two details:
`is_default_navigation_part` is added by `eZModule::run()` (`lib/ezutils/classes/ezmodule.php`): `false` when the
view set `navigation_part` itself, as `content/view` does, `true` when `run()` filled it in from the view's
`default_navigation_part` (as in the error result below); and `template_list` names the
templates used, which the content view cache stores to know when a template change makes the cache stale.

A node that does not exist returns the error view's result instead (`kernel/private/classes/views/error/view.php`):

```text
content/view/full/99999999
exit status: 2 (other)
$Result: array(9)
  content: string (1391 bytes)
  path: array(2)
    0: array(2)
      text: 'Error'
      url: false
    1: array(2)
      text: 'kernel (3)'
      url: false
  errorCode: '404'
  errorMessage: 'Not Found'
  errorType: 'kernel'
  errorNumber: 3
  responseHeaders: array(2)
    0: 'HTTP/1.1 404 Not Found'
    1: 'Status: 404 Not Found'
  is_default_navigation_part: true
  navigation_part: 'ezcontentnavigationpart'
```

The exit status is `eZModule::STATUS_FAILED` (2); `STATUS_OK` is 1.

## From $Result to $module_result

The web kernel (`ezpKernelWeb`, `kernel/private/classes/ezpkernelweb.php`) runs the view in its dispatch loop and
then, before rendering the pagelayout:

1. sets `ui_context` (when the view did not), `ui_component`, `is_mobile_device` and `mobile_device_alias` on the
   result, `path` to `false` when the view gave none, and `uri` to the request URI;
2. calls `$tpl->setVariable( "module_result", $moduleResult )`;
3. sets the other pagelayout variables:

| Variable | Value |
|---|---|
| `$module_result` | the view's `$Result`, with the keys above added |
| `$site` | `uri`, `redirect`, `meta` (`[SiteSettings] MetaDataArray`, with a description made from the path when none is set), `version` (the Exponential version), `page_title` (the module's title) |
| `$current_user` | the current `eZUser` (false when the siteaccess needs no user object) |
| `$anonymous_user_id` | `[UserSettings] AnonymousUserID` |
| `$access_type` | the current siteaccess (name, type) |
| `$warning_list` | warnings collected for the page, or false |
| `$navigation_part` | the `eZNavigationPart` of `$module_result.navigation_part` (default `ezcontentnavigationpart`) |
| `$uri_string`, `$requested_uri_string` | the URI that ran and the one the visitor asked for |
| `$ui_context`, `$ui_component` | copies of the result's keys |

4. fetches the pagelayout and outputs it. Before that it removes any variable `$node` left in the engine, so a
   pagelayout never sees the `$node` of the view template by accident.

## Choosing the pagelayout

- By default the kernel fetches `design:pagelayout.tpl`.
- A view may set `$Result['pagelayout']`: a template name (the kernel adds `design:` unless the name already has a
  resource with `:`), or `false` for no pagelayout at all, in which case `$Result['content']` is the whole response.
  The user views `login`, `register`, `activate`, `success` and `forgotpassword` use `loginpagelayout.tpl` this way,
  and `content/treemenu` sets `false`.
- The `layout/set/<style>` view sets `$Result['pagelayout']` from the `PageLayout` of that style in `layout.ini`
  and adds the override key `layout`, so URLs such as `/layout/set/print/content/view/full/96` render with another
  pagelayout.

## Variables a node view template gets

`eZNodeviewfunctions::generateNodeViewData()` (`kernel/classes/eznodeviewfunctions.php`) sets these before it
fetches `design:node/view/<view mode>.tpl`:

| Variable | Value |
|---|---|
| `$node` | the node (`eZContentObjectTreeNode`) |
| `$viewmode` | the view mode, such as `full` |
| `$language_code` | the language asked for in the URL, or false |
| `$view_parameters` | the [view parameters](view-parameters.md) |
| `$collection_attributes`, `$validation` | for information collection forms |
| `$persistent_variable` | `false`; a template may set it (see [The persistent variable, inside](persistent-variable-internals.md)) |
| `$node_path` | the same list as `$Result['path']` |

User parameters given in the URL are also set as variables of their own (the `_custom` part of the view parameters),
so `/(color)/red` makes a `$color` variable in the node template.

## Related pages

- [Module result reference](module-result-reference.md)
- [View parameters](view-parameters.md)
- [The persistent variable, inside](persistent-variable-internals.md)
- [Module result and content info](../designers/templates/module-result-and-content-info.md)
- [Custom modules and views](../creators/app-creators/custom-modules-and-views.md)
- [View results and the pagelayout](../creators/app-creators/view-results-and-the-pagelayout.md)
- [Module definition reference](../creators/app-creators/module-definition-reference.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
