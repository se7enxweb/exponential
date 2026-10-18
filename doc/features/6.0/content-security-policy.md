# Content Security Policy

A Content Security Policy tells the browser which scripts, stylesheets, images and connections a page may use. A
strict policy (`script-src 'self'`, no `'unsafe-inline'`) means that a script injected into a page through a stored
or reflected value does not run, even where a template forgets to escape it.

Exponential sends a policy from `site.ini [ContentSecurityPolicy]`, beside the security headers of
`[HTTPHeaderSettings] SecurityHeaders[]` (see [Security hardening](../../install/13-security-hardening.md), 13.9).
By default it is **report-only on the administration** and off on the sites.

## Settings

```ini
[ContentSecurityPolicy]
# disabled | report-only | enforce | auto
Mode=auto
Directives[]
Directives[default-src]='self'
Directives[script-src]='self'
Directives[style-src]='self'
Directives[img-src]='self' data: blob:
Directives[font-src]='self' data:
Directives[media-src]='self' blob:
Directives[connect-src]='self'
Directives[frame-src]='self'
Directives[object-src]='none'
Directives[base-uri]='self'
Directives[form-action]='self'
Directives[frame-ancestors]='self'
ReportURI=
```

- **Mode**
  - `disabled`: no policy.
  - `report-only`: the header `Content-Security-Policy-Report-Only`. The browser reports what the policy would block
    (in its developer console, and to `ReportURI` when set) and blocks nothing.
  - `enforce`: the header `Content-Security-Policy`. The browser blocks what the policy does not allow. It is sent as a
    second policy after `SecurityHeaders[Content-Security-Policy]` (`frame-ancestors 'self'` by default); the browser
    applies both.
  - `auto` (the default): `report-only` on administration siteaccesses, those whose `[DesignSettings] SiteDesign`
    begins with `admin` (admin, admin3, admin4, admin4l, adminui) or whose `AdditionalSiteDesignList[]` has such a
    design (an editor siteaccess on admin4l), `disabled` on all others.
- **Directives[]**: the policy, one directive per entry. Set an entry empty in an override to leave the directive out;
  an override that starts with `Directives[]` alone replaces the whole list. An entry whose value holds `;` or `,`, or
  whose name is not a directive name, is ignored, so a setting cannot split the header or add a directive of its own.
  `frame-ancestors` and `sandbox` are sent only when the policy is enforced: browsers ignore them in a report-only
  policy (and say so in the console). Framing stays limited by `SecurityHeaders[]` (`X-Frame-Options`,
  `frame-ancestors 'self'`) in every mode.
- **ReportURI**: where the browser posts its reports (`report-uri`), for example a collector of your own. Empty: the
  developer console only.

Per path, `[HTTPHeaderSettings] HeaderList` still overrides any header, the policy included. Pages answered from the
kernel's HTTP cache carry the same headers.

## Turning it on

1. Leave the administration in `report-only` and work with it for a while: open the developer console (or collect the
   reports with `ReportURI`) and note what the policy would block. Extensions and site designs of your own are the
   usual source.
2. For a site, set `Mode=report-only` in its siteaccess and do the same.
3. When nothing is reported for the pages you use, set `Mode=enforce`.

```ini
# settings/siteaccess/<admin siteaccess>/site.ini.append.php
[ContentSecurityPolicy]
Mode=enforce
```

### Switching to enforce, step by step

The shipped default changes nothing for a visitor: `Mode=auto` sends a report-only policy on administration
siteaccesses and none on the sites. Enforcing is a decision per siteaccess, made after an audit:

1. **Audit the templates.** `AdminContentSecurityPolicyTest` checks the core designs and the extensions this
   repository ships. Run it once with every extension of the installation, as the administration renders them
   (the chains of `uiswitch.ini DesignChain[]`, each administration siteaccess's `AdditionalSiteDesignList[]` and the
   active extensions of `settings/override`):

   ```bash
   CSP_ALL_EXTENSIONS=1 php vendor/bin/phpunit tests/tests/design/AdminContentSecurityPolicyTest.php
   ```

   Every line of the failure is a template with inline code that an enforced policy would break, by extension. A
   template of an extension is fixed in that extension's repository (scripts into files loaded with `ezscript`,
   values into `data-*` attributes, handlers into listeners or the `data-a4-*` actions, menu items into `data-menu-*`).
2. **Collect the reports of real use.** Set `ReportURI` to a collector of your own, or watch the developer console
   (Chrome and Firefox log "[Report Only] Refused to ..."), and use the administration for a few days: the pages of
   every extension, the editors, the dialogs. Handler attributes are reported only when they run, so open the context
   menus and dialogs too.
3. **Enforce one siteaccess first**, best a second administration siteaccess that only you use, with the same
   designs and extensions as the main one. Keep the report-only one for everyone else.
4. **Loosen only what you must**, per siteaccess and per directive, for example for TinyMCE 3:

   ```ini
   [ContentSecurityPolicy]
   Mode=enforce
   Directives[script-src]='self' 'unsafe-inline'
   Directives[style-src]='self' 'unsafe-inline'
   ```

5. **Clear the INI cache** (`php bin/php/ezcache.php --clear-tag=ini`) and reload PHP (and restart Velocity) so every
   process sends the new header. **To go back**, set `Mode=report-only` (or `disabled`) and clear the INI cache again;
   nothing else changes.

Sites (non-administration siteaccesses) get no policy with `auto`. Their designs and the rich text they render
(`style=""` from content, embedded players) are not checked by the test: start them with `Mode=report-only`.

## The directives shipped

- `script-src 'self'` allows script files of this site only: no inline `<script>`, no `on...=` attribute, no
  `javascript:` link, no `eval()`.
- `style-src 'self'` allows stylesheet files only: no `<style>` element and no `style=""` attribute. A script that sets
  `element.style` is not affected.
- `img-src` and `media-src` add `data:` and `blob:` (previews of files picked for an upload, icons embedded in
  stylesheets); `font-src` adds `data:`.
- `object-src 'none'`, `base-uri 'self'`, `form-action 'self'`, `frame-ancestors 'self'` close the plugin, base URL,
  form target and framing paths.

Before you set `Mode=enforce`, mind what `object-src 'none'` blocks: the `<object>`/`<embed>` players of the legacy
media previews (`admin_preview/flash.tpl`, `windows_media.tpl`, `real_video.tpl`, `quicktime.tpl` and the `ezmedia`
view templates; browsers no longer run these plugins anyway) and a PDF shown inline through `<object>` or `<embed>`.
Allow it where you need it, e.g. `Directives[object-src]='self'`.

A page that needs more (a map service, a font host, an analytics script) adds the host to the directive in its
siteaccess, for example `Directives[script-src]='self' https://maps.example.com`.

## The administration without inline code

admin4 and admin4l, and the templates of admin3, admin2, admin and standard that they render, carry no inline
`<script>`, no `on...=` attribute, no `javascript:` link, no `style=""` attribute and no `<style>` element, so the
administration runs under the shipped policy. `tests/tests/design/AdminContentSecurityPolicyTest.php` holds this for
every template of the two design chains and for the scripts of admin4; the exceptions it lists are below.

The test reads the chain the way the kernel builds it: the designs of `uiswitch.ini [UISwitch] DesignChain[]` (or
those of an administration siteaccess: `SiteDesign`, `AdditionalSiteDesignList[]`, `StandardDesign`) and, for each
design, first the design directories of the active extensions (`design.ini [ExtensionSettings] DesignExtensions[]`),
then the core directory. An extension's template therefore counts where it replaces a core template, and so do the
extension templates nothing in the core has, such as the edit templates of the online editor. Active are the
extensions of `site.ini [ExtensionSettings] ActiveExtensions[]` (with `settings/override`) and the
`ActiveAccessExtensions[]` of every siteaccess whose `SiteDesign` is admin4 or admin4l; a checkout without
`settings/override` counts every extension it carries. What an extension still has inline is listed per extension in
`EXTENSION_ALLOWLIST`, with the reason; like the list of core templates it only shrinks: the test fails when a listed
file has less inline code than listed, or is no longer rendered, until the entry is lowered or removed.

How the pieces work, for templates of your own and for extensions:

- **Page scripts** are files: `design/admin4/javascript/page/<module>_<view>.js`, printed with `{ezscript( array(
  'page/<file>.js' ) )}` where the inline script stood, so the code runs at the same point of the page. Use `ezscript`,
  not `ezscript_load`, in a template of the page's content: `ezscript_load` there would print every file that
  `ezscript_require` collected so far at that place and end their loading in the head (a file required later, such as
  the sub-items table's script, would then come after the page script that needs it). The values a script needs from
  the template are `data-*` attributes on the element it works on, washed for HTML
  (`data-text-selected="{'%count selected.'|i18n( ... )|wash}"`); structured values are JSON:
  `data-conf="{json_encode( hash( ... ) )|wash}"`. A template that can appear more than once on a page (a datatype's
  edit template) gets its file each time: the file sets itself up once.
- **The head script** of every page (color mode, `window.a4Pref`) is `design/admin4/javascript/a4_head.js`, loaded by
  `page_head_style.tpl` (with `ezscript`) before the stylesheets, as a normal (blocking) script in `<head>`: it still runs before the
  body is parsed, so the first paint has the saved color mode, column widths and `data-a4-pref-*` values.
- **Small actions** are `data-a4-*` attributes, handled by one listener per event in
  `design/admin4/javascript/a4_actions.js` (loaded on every admin4 page): `data-a4-confirm="Question?"` (a link,
  button or form), `data-a4-discard-confirm`, `data-a4-toggle-checkboxes="Name[]"` (with `data-a4-form` for another
  form), `data-a4-check-all="#selector"`, `data-a4-dismiss`, `data-a4-submit-on-change`,
  `data-a4-navigate-on-change`, `data-a4-reset-field` and `data-a4-focus`. The header comment of the file
  describes each.
- **Context menus** (`popupmenu/*.tpl`, `ezpopupmenu.js`) are declared in `data-menu-*` attributes: on the menu
  `data-menu-depth` and `data-menu-header`; on an item `data-menu-url`, `data-menu-shown-if`,
  `data-menu-disabled-class`, `data-menu-disabled-for`, `data-menu-variable` with `data-menu-template` (the id of a
  `<template>` with the markup of one entry); what an item does: `data-menu-submenu` (with `data-menu-hide`),
  `data-menu-form` (with `data-menu-form-values`) or `data-menu-redirect`. An element that opens a menu has
  `data-menu-open`, `data-menu-values` (JSON), `data-menu-languages`, `data-menu-classes`, `data-menu-title`,
  `data-menu-disable` and `data-menu-disable-item`. The header comment of `ezpopupmenu.js` lists them.
  **Extensions keep working:** a menu item with its own `onmouseover`/`onclick` attribute is left to it, and a script
  that adds an item to a core menu (`menuArray['ContextMenu']['elements']['my-item'] = { ... }`) finds the menu
  created from its attributes. Such an extension item is inline code, though, and reported by the policy until the
  extension declares it the new way.
- **The content tree** (`contentstructuremenu/content_structure_menu_dynamic.tpl`) is a `div.content-tree-menu` with
  its data in `data-tree-root`, `data-tree-params` and `data-tree-request` (JSON);
  `contentstructuremenu_dynamic.js` draws it when the page is parsed (`window.treeMenu` is set as before), and its
  open/close buttons work through one listener instead of generated `onclick` attributes.
- **Generated script and style content** of ezjscore (server functions such as `ezjsc::jqueryio` with packing off, as
  under `DevelopmentMode`) is written to a file in the packer's cache directory, named after its content, and linked
  (`ezjscPacker::generatedContentURL()`); it was printed inline before.

The same applies to the `design/admin` copies of the context menu and tree scripts and templates, which are kept
identical to admin4's; the other templates of the classic admin design are unchanged.

### What still needs more than 'self'

- **Content output**: the width of a rich text table (`ezxmltags/table.tpl`), the alignment of its cells (`td.tpl`)
  and the border of an image (`content/datatype/view/ezimage.tpl`) are `style=""` values that come from the content.
  Under an enforced `style-src 'self'` the browser drops them (the admin preview shows the table at its natural width).
  Add `'unsafe-inline'` to `style-src` if that matters for your content.
- **The receipt** (`shop/orderreceipt_style.tpl`) keeps its `<style>` element: it is shared with the receipt as a file
  to keep (`orderreceipt_document.tpl`), which must carry its own styles.
- **Rich text editors.** The TinyMCE 8 engine of ezoe starts from a file: `ezxmltext_ezoe_tinymce8.tpl` writes the
  settings every editor shares as `<script type="application/json" id="ezoe8-settings">` and the toolbar map as
  `id="ezoe8-button-map"`, each textarea carries its attribute id, buttons, path location and rows in `data-ezoe8-*`
  attributes, and `javascript/tinymce8_ez/ezoe8_start.js` (loaded with `defer`) reads them and calls `tinymce.init`.
  A data block of your own is printed from a `set-block` with `|explode( '<' )|implode( '\u003c' )|explode( '>' )|
  implode( '\u003e' )`: `json_encode` leaves `<!--` and `<script` in a value as they are, and `<!--<script>` makes the
  browser read past the block's `</script>`, so the scripts after it do not run.
  TinyMCE 8 itself sets styles on its own elements (its documentation asks for `style-src 'unsafe-inline'`), so
  enforce with `'unsafe-inline'` in `style-src` where it is used.
  TinyMCE 3, the default engine (`ezoe.ini [EditorSettings] EditorEngine=tinymce3`), is not converted: its edit
  template `ezxmltext_ezoe.tpl` starts the editor with an inline script, and its dialogs (the 27 templates under
  `extension/ezoe/design/standard/templates/ezoe/` that carry inline code) have 23 inline scripts, 13 event handler
  attributes, 51 `javascript:` links, 52 `style=""` attributes and one `<style>` element (counted 16 October 2026).
  `tiny_mce.js` and `tiny_mce_popup.js` write `javascript:` links and `onclick` attributes into the toolbars and
  dialogs they build, so moving the templates into files alone would not make TinyMCE 3 run under the policy. A siteaccess that edits with TinyMCE 3 needs
  `Directives[script-src]='self' 'unsafe-inline'` and `Directives[style-src]='self' 'unsafe-inline'` before it
  enforces the policy, or switches to TinyMCE 8. Editors of other packages (Tiptap, exp_oe_tiptap) are checked by the
  same test when they are installed; what they still write inline is in `EXTENSION_ALLOWLIST`.
- **Extensions** that write inline scripts or handlers into admin pages (their own menu items, page scripts) are
  reported until they move to files and `data-*` attributes as described above.

### Measured on an installation with many extensions (17 October 2026)

A crawl of 45 administration pages per siteaccess (admin4l, admin4, Exponential UI's adminui), on Apache and on
Velocity, signed in as an administrator, with the report-only policy of this release:

| | before | after |
|---|---|---|
| inline scripts per page | 32-34 | 11-15 |
| event handler attributes per page | 165-167 | 36-42 |
| policy reports per page (admin4, admin4l) | 35 | 11-14 |

No page broke and no script error was added (report-only blocks nothing). Every remaining finding comes from an
installed extension or Exponential UI's own design, not from the kernel's templates; `CSP_ALL_EXTENSIONS=1` lists
them per file. On that installation they were, by number of findings in the templates admin4 and admin4l render:
cjw_newsletter (193), eztags (49), xrowextract (24, its export and import context menus), explayouts_ui_api (22),
sevenx_authentication_2fa (14), ezodf (14, the OpenOffice menu entries), explayouts (12), ezgmaplocation, expai (8
each), xrowmetadata, expchangeclass (7 each), syndication, recaptcha, bcwebsitestatistics (6 each), ezie,
explayouts_ui, enhancedselection2 (5 each), ezwt, ezownerchange, ezautosave (4 each), ezstarrating (3), and one or two
in sevenx_dse, git_manager, bccie, ezupdate, expui, expqueue, explayouts_content_browser_ui, enhancedezbinaryfile,
cjw_tmv and cjw_themes_jumper. At run time the context menu entries of ezmultiupload and ezownerchange, the debug
output and Exponential UI's head script (Ace and the jQuery UI fallback) add to them. Each of these is changed in its
own repository before a siteaccess that uses it is set to `enforce`.

### Extensions without inline scripts (17 October 2026)

The extensions listed in `tests/tests/design/ExtensionInlineScriptsTest.php` carry no inline script, no event handler
attribute and no `javascript:` link in any template from the version listed there on (composer.json requires those
versions); the test holds them to it whenever they are installed. They follow the patterns above with one
difference: an extension runs in every administration design and on earlier releases, so it does not rely on
admin4's `data-a4-*` actions or on the `data-menu-*` menus. Each ships its own script file with delegated listeners;
its context menu entries are declared in data attributes, and its script adds them to `menuArray` and calls the
same `ezpopmenu_*` functions as before.

What they still have inline is styles (`style=""`, `<style>`), which `style-src 'self'` reports; enforce with
`style-src 'self' 'unsafe-inline'` until those are moved as well.

Measured on the same installation after these releases, with the policy put on every page by the browser test and
`script-src 'self'` enforced: the administration pages of admin4l, admin4 and Exponential UI (the newsletter, tags,
export, layouts, owner change and the other extension pages among them) report no script violation and their
features work, on Apache and on Velocity. What is still inline in a script sense is the TinyMCE 3 engine (ezoe and
Exponential UI's copy of its edit template); external scripts (Google Maps, reCAPTCHA, the social buttons of ezdemo,
an ad server of ezflow) need their hosts in `script-src`.

### Before enforcing on the administration

- **Menu items of extensions.** An extension that adds context menu items with an inline script
  (`menuArray['ContextMenu']['elements'][...] = ...`) and `onmouseover`/`onclick` attributes - for example the menus of
  xrowextract, ezodf (the OpenOffice import/export entries) and ezmultiupload (the Multi Upload entry of the context menus) - works
  under report-only. Under an enforced policy the browser runs neither the script nor the handlers: the entries are
  still drawn but do nothing, without an error on the page. Convert them to `data-menu-*` (as the core menus are)
  before you set `Mode=enforce`, or keep `'unsafe-inline'` in `script-src` for that siteaccess.
- **`menuArray` assigned anew.** `ezpopupmenu.js` creates `menuArray`, and creates the entry of a menu declared with
  `data-menu-*` the first time it is asked for. A script that assigns it anew (`var menuArray = new Array();`,
  `menuArray = {};`) replaces that: the declared core menus (context menu, sub-items, class, bookmark menus and their
  submenus) then no longer open. Add to `menuArray`, never assign it.
- **Plugins and inline PDF**: see `object-src` above.

## Enforcing: nonce, more sources, a stricter companion, rollback

These settings of `site.ini [ContentSecurityPolicy]` make an enforced policy practical. None of them changes the
policy of a new installation, except that the report-only policy of the administration carries a nonce.

```ini
[ContentSecurityPolicy]
# sources added to a directive of Directives[], a list
AdditionalSources[]
# with Mode=enforce: a stricter Content-Security-Policy-Report-Only beside it
ReportOnlyDirectives[]
# designs that stay report-only under Mode=enforce
ReportOnlyDesigns[]
ReportOnlyDesigns[]=admin
ReportOnlyDesigns[]=admin2
ReportOnlyDesigns[]=admin_classic
Nonce=enabled
NonceDirectives[]
NonceDirectives[]=script-src
ReportURI=
ReportEndpoint=disabled
```

- **AdditionalSources[]** `=<directive> <sources>`: adds sources to a directive that `Directives[]` already has, for
  example `AdditionalSources[]=script-src https://maps.googleapis.com`. Because it is a list, an extension's
  `site.ini.append.php` and the siteaccess can each add their hosts without restating `'self'` or each other's
  hosts. An entry for a directive that `Directives[]` does not have is ignored: it would otherwise start a directive
  that replaces the `default-src` fallback. Duplicates are dropped.
- **ReportOnlyDirectives[]**: only with `Mode=enforce`. A second header, `Content-Security-Policy-Report-Only`, carries
  `Directives[]` with these entries in their place (and the same additional sources, nonce and report address).
  The browser enforces the first one and only reports what the second would block. Use it to measure the way to a
  stricter policy, for example `style-src 'self'` while the enforced policy still allows `'unsafe-inline'` styles:

  ```ini
  Mode=enforce
  Directives[style-src]='self' 'unsafe-inline'
  ReportOnlyDirectives[style-src]='self'
  ```
- **ReportOnlyDesigns[]**: a request whose design (`[DesignSettings] SiteDesign`, after the UI switch has applied the
  user's choice) is listed gets the report-only policy even when the siteaccess enforces. The classic administration
  (`admin`, and `admin2`, which renders its pagelayout) has inline scripts in many templates; a user who picks it with
  the UI switch keeps a working page.
- **Nonce**, **NonceDirectives[]**: with `Nonce=enabled`, every request gets a nonce (16 random bytes, base64url)
  that is added as `'nonce-...'` to each directive of `NonceDirectives[]`. A template prints it with the operator
  `csp_nonce()`:

  ```html
  <script nonce="{csp_nonce()}">...</script>
  ```

  The operator returns an empty string when the policy carries no nonce. The nonce is not added to a directive that
  allows `'unsafe-inline'`, because browsers ignore `'unsafe-inline'` next to a nonce. That keeps a siteaccess that
  still needs `'unsafe-inline'` (TinyMCE 3) working. Velocity's persistent workers get a new nonce per request (the
  kernel resets it at the start of each one). **Never print `csp_nonce()` inside a `{cache-block}`, a view-cached
  template or a static cache**: the cached copy would carry the nonce of another request and the browser would block
  the script. Prefer, in this order: a file loaded with `ezscript` (a script in the head runs before the first paint,
  so it does not flash), a hash of a static inline script (`AdditionalSources[]=script-src 'sha256-...'`), then the
  nonce, for uncached output only. The kernel's HTTP cache stores a page with its headers, so a cached page and its
  nonce stay together.
- **ReportURI**, **ReportEndpoint**: with `ReportEndpoint=enabled` and `ReportURI` empty, the reports go to the
  `csp/report` view of the siteaccess, which logs one line per violation to `var/log/csp.log` (log viewer key `csp`).
  An absolute report address is also announced as the reporting endpoint `csp-endpoint`, with the header
  `Reporting-Endpoints` and the directive `report-to`. Chrome sends to that one; Firefox to `report-uri`.
- `allowsInlineScripts()`: `expContentSecurityPolicy::allowsInlineScripts()` tells code (an editor that chooses its
  engine, for example) whether inline scripts and handler attributes run under the current siteaccess's policy.

**Rollback.** Set `Mode=report-only` in the siteaccess, clear the INI cache (`php bin/php/ezcache.php
--clear-tag=ini`), reload PHP-FPM and restart Velocity. Nothing else changes: the same policy is sent as
`Content-Security-Policy-Report-Only` and the stricter companion is no longer sent.

The shipped default stays `Mode=auto`, report-only on the administration. Enforcing is a decision per installation,
taken after the audit above.
