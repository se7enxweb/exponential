---
title: Admin pages in the admin4 style
audience: developer, app-creator, designer
category: extension-development
order: 330
summary: Extension admin pages that look like the rest of the admin: shared .a4- components, --a4- tokens with dark values, your own stylesheet, no inline CSS, menu links, light and dark checks.
keywords: admin4, administration design, components.css, a4-card, a4-btn, a4-table, a4-toolbar, tokens, --a4, dark mode, BackendCSSFileList, admin stylesheet, menu.ini, left menu, accessibility
type: help
---
# Admin pages in the admin4 style

Part 3, chapter 33. Most extensions add a page to the administration. It should look like the rest of it, work in
dark mode and at high zoom, and cost the administration no extra CSS that drifts. The rules and the full component and
token API are in [Styling the administration](../../../bc/6.0/admin4-styling.md); this chapter is the extension
author's summary.

## The rules

1. **No `<style>` and no `style=""` in templates.** CSS lives in your design's `stylesheets/` and is loaded through
   `design.ini`.
2. **Use the shared components first**: `.a4-page`, `.a4-card`, `.a4-table`, `.a4-btn`, `.a4-toolbar`, `.a4-filterbar`,
   `.a4-badge`, `.a4-notice`, `.a4-empty` and the others of `stylesheets/components.css`.
3. **Colors, radius, spacing and fonts only from `--a4-*` tokens**: they switch for dark mode by themselves.
4. **Your own classes take your own prefix** and the same rules; your stylesheet holds only what the components cannot
   do.
5. **Check light and dark**, at 1440 px and at 960 px with a 200% zoom.

## Your stylesheet

`design/admin/stylesheets/mysite_hello.css` (or `design/admin4/...` if the page exists only in admin4), registered as the
`expqueue` extension does:

```ini
[StylesheetSettings]
BackendCSSFileList[]=mysite_hello.css
```

Extensions add to `BackendCSSFileList[]`, never to `AdminComponentCSSFileList[]` (that list is the administration's own).
`expqueue`'s stylesheet opens with a comment worth copying: the pages "are built from the administration's shared
components ... and take every colour, radius, size and space from the --a4-* tokens of components.css, which have a
light and a dark value. This file holds only what the components cannot do, as .a4-queue-* classes."

```css
/* mysite_hello: the Hello admin page. Built from the shared components; only what they cannot do is here. */
.a4-hello-list {
    display: grid;
    gap: var(--a4-space-3);
    grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr));
}
.a4-hello-name {
    color: var(--a4-accent-text);
    font-size: var(--a4-text-l);
}
```

## A page template

`design/admin4/templates/hello/admin.tpl`:

```text
<div class="a4-page">
    <div class="a4-pagehead">
        <div class="a4-pagehead-main">
            <h1 class="a4-pagehead-title">{'Greetings'|i18n( 'extension/mysite_hello' )}</h1>
        </div>
    </div>
    {if $greetings|count}
    <div class="a4-table-wrap">
        <table class="a4-table">
            <thead><tr><th scope="col">{'Name'|i18n( 'extension/mysite_hello' )}</th><th scope="col">{'When'|i18n( 'extension/mysite_hello' )}</th></tr></thead>
            <tbody>
            {foreach $greetings as $greeting}
                <tr><td>{$greeting.name|wash}</td><td>{$greeting.created|l10n( 'shortdatetime' )}</td></tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    {else}
    <p class="a4-empty">{'No greetings yet.'|i18n( 'extension/mysite_hello' )}</p>
    {/if}
    <form method="post" action={'/hello/admin'|ezurl} class="a4-toolbar">
        <button type="submit" name="ClearButton" class="a4-btn a4-btn-danger">{'Clear all'|i18n( 'extension/mysite_hello' )}</button>
    </form>
</div>
```

An empty list says so in `.a4-empty`, data tables use `.a4-table` (striped by the administration), forms post with the
form token added automatically.

## Classic admin and other designs

The classic `admin` design has an identical copy of `components.css`, so the same classes work there; put the template
in `design/admin/templates/` too when your page must work in both. Designs without the component classes still get
readable HTML.

## The left menu

Add a link to an administration menu in your extension's `settings/menu.ini.append.php`. `expqueue` adds its
"Job queue" to the Setup menu like this, with the policy a user needs to see the link:

```ini
[Leftmenu_setup]
Links[expqueue]=expqueue/dashboard
LinkNames[expqueue]=Job queue
PolicyList_expqueue[]=expqueue/view
```

A menu that is divided into groups places a link with `GroupLinks_<group>[]`; a link in no group shows under "More".

## Accessibility

- One `h1` (the page title), then `h2`/`h3` in order.
- Every form field has a label; every icon-only button has text for screen readers (`.a4-sr-only`) and a `title`.
- Notices that report a result have `role="status"` (or `role="alert"` for errors).
- Text contrast at least 4.5:1, which the tokens give you if you use them.

## Check

Open the page in light and dark mode, at 1440 px and at 960 px with the browser at 200%: no white card on the dark page,
no horizontal scroll bar, no console errors (including Content Security Policy reports).

## Related pages

- Previous: [Content Security Policy for extension authors](content-security-policy-for-extensions.md)
- Next: [Tests for extensions](tests-for-extensions.md)
- [Styling the administration](../../../bc/6.0/admin4-styling.md)
- admin4 components and tokens
- admin4 styling for extension pages
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
