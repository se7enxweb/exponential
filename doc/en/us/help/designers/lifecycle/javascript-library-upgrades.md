---
title: "JavaScript library upgrades (jQuery 4, no YUI)"
audience: designer, developer
category: theme-lifecycle
order: 120
summary: Bring a theme's JavaScript up to date with the libraries Exponential ships - jQuery 4.0.0 with Migrate and jQuery UI 1.14.2 through ezjscore, YUI removed - what to search for, what to replace, and how to test.
keywords: jQuery 4, YUI removal, ezjscore, ezjsc::jquery, jQuery Migrate, jQuery UI, JavaScript upgrade, .on, .prop
type: help
---
# JavaScript library upgrades (jQuery 4, no YUI)

This page is for designers whose theme has JavaScript. On 1 and 2 October 2026 the administration moved to jQuery 4 and
YUI was removed from the admin designs, the `standard` design and the bundled ezjscore. The details are in
[jQuery 4 in the admin, and YUI gone](../../../features/6.0/jquery4-and-yui-removal.md) and
[YUI removal](../../../bc/6.0/yui-removal.md).

## What ezjscore ships

`ezjsc::jquery` and `ezjsc::jqueryUI` deliver **jQuery 4.0.0 with jQuery Migrate 4.0.2 and jQuery UI 1.14.2**. Migrate
is the quiet build by default, so older code keeps working without filling the console. The older jQuery 3.7.1,
Migrate 3.4.1 and jQuery UI 1.10.3 files were removed on 14 October 2026.

## Step by step

1. **Find YUI use**; it loads nothing any more:

   ```bash
   grep -rn "ezjsc::yui\|YUI(" extension/mytheme
   ```

   No output means nothing to port. Otherwise follow [YUI removal](../../../bc/6.0/yui-removal.md).
2. **Find old jQuery patterns**:

   ```bash
   grep -rn -E "\.(click|change|submit|bind|unbind)\(|jQuery\.trim|\.attr\(['\"](checked|disabled|selected)" extension/mytheme/design
   ```

3. **Replace** them as the admin did: event shorthands and `.bind()` with `.on()`, `.unbind()` with `.off()`, boolean
   `.attr()` with `.prop()`, `jQuery.trim()` with `String.prototype.trim()`.
4. **Load jQuery through ezjscore**, not a copy of your own, so the version matches the system.
5. **Test** every interactive part with the browser console open: no errors, no Migrate warnings with the reporting
   build switched on.

## Related pages

- [jQuery in designs](../theme-design/jquery-in-designs.md)
- [Keeping dependencies up to date](dependency-updates.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
