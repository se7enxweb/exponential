---
title: The design directory layout
audience: designer
category: theme-design
order: 50
summary: Every folder a design can have, what the kernel and the web server do with each, which folders browsers may fetch directly, and how the media and starter designs of this installation organize their templates.
keywords: design folder, directory layout, templates folder, override templates, stylesheets, javascript, images, fonts, folder structure, served folders, rewrite rules, htaccess, design layout
type: help
---
# The design directory layout

This page is for designers who are about to create files in a design. It answers: which folders a design has, what
each is for, which ones a browser may load files from, and how real designs of this installation arrange their
templates.

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../../examples/web-servers/nginx.md), [LiteSpeed](../../../examples/web-servers/litespeed.md),
> [Caddy](../../../examples/web-servers/caddy.md), [IIS](../../../examples/web-servers/iis.md), [Velocity](../../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../../hosting/panels/plesk.md), [cPanel](../../hosting/panels/cpanel.md), [DirectAdmin](../../hosting/panels/directadmin.md), [ISPConfig](../../hosting/panels/ispconfig.md).

## Contents

1. [The folders](#the-folders)
2. [Folders the browser can reach](#folders-the-browser-can-reach)
3. [Inside templates/](#inside-templates)
4. [Inside override/templates/](#inside-overridetemplates)
5. [Two real examples](#two-real-examples)
6. [Naming rules](#naming-rules)

## The folders

```
design/<name>/                      or  extension/<extension>/design/<name>/
├── templates/                       TPL templates found by path (pagelayout.tpl, node/view/full.tpl ...)
├── override/templates/              templates used only when an override.ini rule matches
├── stylesheets/                     CSS, loaded through ezjscore (ezcss_load) or ezdesign
├── javascript/                      JavaScript, loaded through ezjscore (ezscript_load)
├── images/                          images of the design (ezimage)
└── fonts/                           web fonts referenced from your CSS
```

None of the folders is required. A design that only changes colors may hold a single `stylesheets/` file; a design that
changes the markup of articles may hold only `override/templates/full/article.tpl`.

## Folders the browser can reach

Templates are read by PHP and are never sent to a browser. Assets are fetched by the browser directly, so the web
server must let them through. The shipped rewrite rules (`.htaccess_root` in the installation root, and the Apache
example `doc/eng-us/examples/ezpublish.conf`) serve only these folders as static files:

| Location | Folders served directly |
|---|---|
| `design/<name>/` | `stylesheets`, `images`, `javascript`, `fonts` |
| `extension/<ext>/design/<name>/` | `stylesheets`, `flash`, `images`, `lib`, `javascript` / `javascripts`, `fonts`, `vendor`, `media` (the `.htaccess_root` list) |

Every other request goes to `index.php`. So:

- put images under `images/`, never under `templates/`;
- put fonts under `fonts/`;
- a third-party library with its own files may go under `lib/` or `vendor/` in an extension's design.

> **Note**
> Packed stylesheets and scripts are served from `var/<site>/cache/public/`, which the rules also let through, so
> in production the browser mostly loads the packer's output, not your original files. See
> [The packer](the-packer.md).

## Inside templates/

The template path is what other templates and the kernel ask for, so it is fixed by convention. The most important
paths:

| Path | What it is |
|---|---|
| `pagelayout.tpl` | the frame of every page ([The pagelayout](the-pagelayout.md)) |
| `node/view/<viewmode>.tpl` | how a node is shown in a view mode: `full.tpl`, `line.tpl`, `embed.tpl`, `embed-inline.tpl` ... |
| `content/view/<viewmode>.tpl` | how a content object (without a node) is shown |
| `content/datatype/view/<datatype>.tpl` | how one field of a datatype is shown, e.g. `ezstring.tpl`, `ezimage.tpl` |
| `content/datatype/edit/<datatype>.tpl` | the edit form of one field |
| `content/datatype/view/ezxmltags/<tag>.tpl` | one tag of rich text (`paragraph.tpl`, `link.tpl`, `factbox.tpl` ...) |
| `user/login.tpl`, `user/register.tpl` ... | the pages of the user module |
| `error/kernel/<number>.tpl` | error pages |
| `navigator/google.tpl` | page navigation (paging) |
| `parts/...`, `pagelayout/...` | free names for your own partial templates |

Every path in `design/standard/templates/` is a template the kernel or a module may ask for. Copying one into your
design at the same path replaces it for siteaccesses that use your design.

## Inside override/templates/

Templates here are not found by path. They are named by an `override.ini` rule (`MatchFile=`) and searched under
`override/templates/` in every design base. Designs usually group them by view mode:

```
override/templates/
├── full/article.tpl        MatchFile=full/article.tpl     Match[class_identifier]=article
├── line/article.tpl        MatchFile=line/article.tpl
└── embed/image.tpl         MatchFile=embed/image.tpl
```

The media design has `content`, `edit`, `embed`, `full`, `line`, `node`, `standard` and `view` here. See
[Override basics](../templates/override-ini-basics.md).

## Two real examples

**The media design** (`extension/sevenx_themes_media/design/media/templates/`) splits its frame into parts:

```
pagelayout.tpl
pagelayout/variables.tpl
pagelayout/head/...            (meta, style.tpl, script.tpl)
pagelayout/header.tpl, header/...
pagelayout/breadcrumbs.tpl
pagelayout/footer.tpl
pagelayout/accessibility_links.tpl
pagelayout/cookie_control.tpl
pagelayout/script_bottom.tpl
explayouts/...                 (its versions of the layouts block templates)
parts/...                      (Open Graph, consent, maps ...)
```

**The starter design** (`extension/cjw_themes_jumper/design/starter/`) has the same idea with `pagelayout/head/` for
`meta.tpl`, `opengraph.tpl`, `link.tpl`, `style.tpl`, `script.tpl`, and keeps its fonts in `fonts/`, its
favicons in `images/favicon/` and its scripts in `javascript/`.

Splitting the pagelayout into small included files is not required, but it makes each part easy to find, to override in
a design above, and to read in the template path comments.

## Naming rules

- File and folder names: lowercase, words joined with `_` or `-`, `.tpl` for templates.
- A template for a content class uses the class identifier: `full/ng_article.tpl` for class `ng_article`.
- Your own parts under a folder of your own (`parts/`, `pagelayout/`) cannot collide with kernel paths.
- Never put secrets, passwords or keys in a design: everything under `stylesheets/`, `javascript/`, `images/` and
  `fonts/` is public.

## Related pages

- [What a design is](what-a-design-is.md)
- [The design cascade](the-design-cascade.md)
- [Loading stylesheets](loading-stylesheets.md)
- [Fonts in a design](fonts-in-a-design.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
