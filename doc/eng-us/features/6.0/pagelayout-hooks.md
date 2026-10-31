# Pagelayout hooks: one place at the end of every page for extensions

Since 6.0.15 every pagelayout ends with one line that includes `design:pagelayout/hooks/body_end.tpl`, and that
template includes each template an extension lists in `site.ini [PageLayoutHooks] BodyEnd[]`. An extension that needs
something on every page of every design (the website sidebar expwt is the first) adds one setting instead of asking
every design to change its pagelayout.

## The line in a pagelayout

One line per template engine, just before `</body>`:

| Engine | Line |
|---|---|
| `.tpl` | `{include uri='design:pagelayout/hooks/body_end.tpl'}` |
| `.stpl` (Smarty) | `{expinclude uri='design:pagelayout/hooks/body_end.tpl'}` |
| `.twig` (Twig) | `{% expinclude uri='design:pagelayout/hooks/body_end.tpl' %}` |

The kernel's designs carry it: `standard`, `base`, `plain`, `mysite`, `admin`, `admin3`, `admin4` and `admin4l`. A
design of an extension adds the line to its own pagelayouts. Without an entry in `BodyEnd[]` the hook prints nothing,
so the line costs nothing on a site that does not use it.

## Adding a hook

In the extension's `settings/site.ini.append.php`:

```ini
[PageLayoutHooks]
BodyEnd[]=myext/body_end.tpl
```

`myext/body_end.tpl` is a template of the extension's design (`design/standard/templates/myext/body_end.tpl`), found
through the design chain like any other.

## What a hook may print

A pagelayout that keeps the whole page in one cache block (the media designs do) renders the hook inside it, and the
full-page cache keeps the page for everybody with the same roles. So a hook prints only what those keys cover: the
siteaccess, the node, whether the visitor is signed in, and the roles. Whatever belongs to the user themselves (a
name, a token, a preference) is fetched by the hook's script after the page has loaded. A hook that has nothing to
show prints nothing: not even a comment.

expwt, for example, prints one custom element and one deferred script for a user with the policy `expwt/use`, and
nothing for a visitor who is not signed in while its `ShowToAnonymous` is off ([the website sidebar](website-sidebar.md)).
