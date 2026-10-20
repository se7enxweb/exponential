---
title: "Lesson 3.4: A theme ready for a Content Security Policy"
audience: trainer, designer, developer
category: training
order: 34
summary: Learners explain what the shipped Content Security Policy allows, run their siteaccess in report-only mode, read a violation caused by an inline style, and replace inline code with classes, data attributes and script files. 45 minutes.
keywords: theme design, Content Security Policy, CSP, report-only, enforce, auto, script-src, style-src, inline script, inline style, onclick, data attribute, AdditionalSources, ContentSecurityPolicy Mode
type: help
---
# Lesson 3.4: A theme ready for a Content Security Policy

A Content Security Policy tells the browser which scripts and styles a page may use. The policy Exponential ships
allows only files of the site itself: no `<script>` with code, no `onclick=`, no `<style>` block, no `style=""`. A
theme written that way from the start can be put under the policy at any time; a theme full of inline code cannot.
This lesson makes learners write the first kind.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 3: Stylesheets and scripts through the packer](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Designers and front-end developers |
| Prerequisites | [Lesson 3.1](listing-files-in-design-ini.md); a browser with developer tools |

## Learning objectives

After this lesson, learners can:

- explain the four values of `[ContentSecurityPolicy] Mode` and what `auto` does;
- name what `script-src 'self'` and `style-src 'self'` forbid in a template;
- run their siteaccess in report-only mode and read a violation in the browser console;
- replace inline styles and scripts with classes, `data-*` attributes and script files.

## Trainer's notes

### How to present it

Sources: [Content Security Policy](../../../../features/6.0/content-security-policy.md) and
[Stylesheets and scripts for your site, CSP](../../../creators/website-creators/stylesheets-and-scripts.md#scripts-that-work-under-a-content-security-policy).

1. **The modes** (4 minutes). `site.ini [ContentSecurityPolicy] Mode`:

   | Value | Effect |
   |---|---|
   | `disabled` | no policy |
   | `report-only` | the browser reports what the policy would block and blocks nothing |
   | `enforce` | the browser blocks it |
   | `auto` (default) | report-only on administration siteaccesses, disabled on the others |

2. **What the policy says** (4 minutes). `Directives[script-src]='self'` allows script files of the site only: no
   inline `<script>`, no `on...=` attributes, no `javascript:` links, no `eval()`. `Directives[style-src]='self'`
   allows stylesheet files only: no `<style>` elements and no `style=""`. Setting `element.style` from a script file
   stays allowed.
3. **The replacements** (5 minutes):

   | Instead of | Write |
   |---|---|
   | `<a onclick="openGallery()">` | `<a class="js-gallery">` and `addEventListener` in the theme's script file |
   | `<script>var root = "...";</script>` | a `data-*` attribute the script file reads, or a `<script type="application/json">` data block |
   | `<div style="width: 40%">` | a CSS class, or a `data-*` attribute the script reads |
   | a script from another host | a copy in the design, or `AdditionalSources[]=script-src https://<host>` |

4. **The path to enforce** (2 minutes). Report-only first, work with it, fix what is reported, then `enforce`. Who
   switches a live site is the site owner's decision, not the theme's.

### Common questions

**Is a `<script type="application/json">` block not inline code?** It is data; the browser does not run it, so the
policy does not block it.

**Can editors' content break the policy?** Yes, a `style=""` in content would be reported. That is content, not the
theme; start such sites with report-only.

**Does the administration follow these rules?** Yes; admin4 runs without inline code apart from documented exceptions
(module 4).

### Pitfalls

- **The console is closed.** Report-only violations only show in the developer console (or at `ReportURI`). Open it
  before loading the page.
- **Leaving report-only on.** It costs nothing visible, but end the exercise by removing the setting so the lab is
  back to its default.
- **Third-party widgets.** Maps, video players and tag managers usually need hosts in `AdditionalSources[]`; check
  before promising a customer an enforced policy.

## Hands-on exercise

On your training installation, with the browser's developer console open. `D` is your design folder.

1. Switch the policy to report-only for your siteaccess:

   ```bash
   php bin/php/console exp:ini set site.ini/ContentSecurityPolicy/Mode report-only siteaccess:<site>
   php bin/php/ezcache.php --clear-tag=ini
   ```

2. Reload a page of `<site>`. In the network panel, find the response header `Content-Security-Policy-Report-Only`
   of the page and read its `script-src` and `style-src`.
3. Read the console: note any reported violation.
4. In `D/templates/page_footer.tpl`, add `style="color: red"` to the `<footer>` element. Clear `--clear-tag=template`
   and reload. Read the new console message.
5. Remove the attribute again. Instead, add to `training.css` the rule `.training_theme-footer { color: #555; }`
   (the class the wizard gave the footer). Run the routine of lesson 3.3 and reload: no new message.
6. Pass the site's root address to a script without inline code: in `D/templates/pagelayout.tpl`, add to the
   `<body>` element the attribute `data-root={'/'|ezurl}`.
7. In `D/javascript/site.js`, add:

   ```js
   document.addEventListener( 'DOMContentLoaded', function () {
       var root = document.body.getAttribute( 'data-root' );
       document.querySelectorAll( '.training_theme-logo' ).forEach( function ( a ) {
           a.setAttribute( 'title', 'Home: ' + root );
       } );
   } );
   ```

8. Run the routine of lesson 3.3, reload, and hover over the site name in the header: the title shows the root.
9. Check the console once more: no violation from your theme.
10. Remove the policy setting again:

    ```bash
    php bin/php/console exp:ini rem site.ini/ContentSecurityPolicy/Mode siteaccess:<site>
    php bin/php/ezcache.php --clear-tag=ini
    ```

## Expected result

- Step 2: the page sends the report-only header with `script-src 'self'` and `style-src 'self'`.
- Step 4: the console reports that an inline style violates `style-src`; the page still shows red, because nothing is
  blocked in report-only mode.
- Steps 5 to 9: the footer color comes from the stylesheet, the script reads the data attribute, and the console
  shows no violation from the theme.
- Step 10: the page no longer sends the report-only header.
- The trainer checks one learner's console on the projector after step 9.

## Quiz

1. What does `Mode=auto` do on a public siteaccess?
2. Which of these does `script-src 'self'` block?
   A. `<script src="...">` from the site B. an `onclick=` attribute C. a script file in the design D. `ezjsc::jquery`
3. How do you pass a value from a template to a script without inline code?
4. In report-only mode, an inline style violates the policy. Does the page still show it?
5. A customer wants a map from another host on an enforced site. Which setting allows it?

<details>
<summary>Answers</summary>

1. Nothing: `auto` is disabled on public siteaccesses and report-only on administrations
   ([Content Security Policy](../../../../features/6.0/content-security-policy.md)).
2. **B.**
3. In a `data-*` attribute (or a JSON data block) that a script file reads.
4. Yes; report-only reports and blocks nothing.
5. `[ContentSecurityPolicy] AdditionalSources[]`, for example `script-src https://<host>`.

</details>

## Further reading

- [Content Security Policy](../../../../features/6.0/content-security-policy.md): directives, reports and the steps to
  enforce.
- [Content Security Policy for your code](../../../creators/app-creators/content-security-policy.md): the developer's
  view.
- [Removing inline scripts and styles](../../../designers/lifecycle/removing-inline-scripts-and-styles.md): cleaning up
  an older theme.
- [CSP audit per release](../../../designers/lifecycle/csp-audit-per-release.md): keeping a theme clean over time.

## Related pages

- [Module 3: Stylesheets and scripts through the packer](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
