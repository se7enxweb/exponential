---
title: "Lesson 7.4: Loading order and dependencies"
audience: trainer, developer
category: training
order: 74
summary: Learners see why the order of active extensions decides which settings and templates win, declare extends, requires and uses in extension.xml, switch on ExtensionOrdering, and read the real loading position on the Extensions page. 40 minutes.
keywords: developers course, loading order, ActiveExtensions order, extension.xml dependencies, extends, requires, uses, ExtensionOrdering, ezpExtension getLoadingOrder, Setup Extensions, Loads as number
type: help
---
# Lesson 7.4: Loading order and dependencies

When two extensions set the same setting or provide the same template, one of them wins, and the order of the
active extensions decides which. In this lesson learners see the order at work, declare what their extension
builds on in `extension.xml`, and read on the **Extensions** page where each extension really loads.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 7: Extensions](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Advanced |
| Audience | Developers |
| Prerequisites | [Lesson 7.3](autoloads-and-activation.md) |

## Learning objectives

After this lesson, learners can:

- explain that an extension loaded earlier wins for settings and design templates;
- find which extension a setting comes from with `exp:ini where`;
- declare `<extends>`, `<requires>` and `<uses>` in `extension.xml`;
- explain what `[ExtensionSettings] ExtensionOrdering=enabled` changes;
- read the real loading position on **Setup** > **Extensions**.

## Trainer's notes

### How to present it

Sources: [Declaring dependencies in extension.xml](../../../designers/extensions/dependencies-in-extension-xml.md),
[Extension loading order](../../../../features/6.0/extension-loading-order.md) and
[The Extensions page](../../../../guides/extensions-page.md).

1. **Who wins** (4 minutes). The order of `ActiveExtensions[]` decides: an extension loaded earlier wins for
   settings and design templates. `exp:ini where` lists every file that sets a variable, in load order, and the
   value in effect.
2. **Dependencies** (5 minutes). In `extension.xml`, after `<metadata>`:

   | Element | Loads your extension | Use it for |
   |---|---|---|
   | `<extends>` | before the named one (yours wins) | Overriding templates or settings of a base extension |
   | `<requires>` | after the named one | Something yours cannot work without |
   | `<uses>` | after the named one | Something yours uses when present |

   The kernel reads them in `ezpExtension::getLoadingOrder()` when `ExtensionOrdering=enabled`. They order only
   active extensions: `<requires>` does not activate the other one.
3. **The page** (3 minutes). On **Setup** > **Extensions** each card shows where the extension really loads
   (**Loads as number n**) when dependencies move it; moves are reviewed before they are written.
4. **Composer too** (2 minutes). The same dependencies belong in `composer.json` (`require` or `suggest`), so
   Composer installs what is needed.

### Common questions

**Why did my theme's template not win over another theme's?** The other extension loads earlier, or declares that
it extends yours. Read the order and the `<extends>` blocks.

**Does a broken `extension.xml` stop the site?** It is reported in the debug output and its loading order is
ignored; check it with `xmllint`.

**Should every extension declare dependencies?** Every extension that overrides or needs another one, yes; it
makes the order explainable instead of accidental.

### Pitfalls

- **Editing the order by hand on a live site.** Use the Extensions page or `exp:ini` with a review; a wrong order
  can change templates across the site.
- **`<requires>` as an installer.** It orders; it installs and activates nothing.
- **Different order on development and live.** Settings come from different files on each machine; compare with
  `exp:ini get site.ini/ExtensionSettings/ActiveExtensions` on both.

## Hands-on exercise

On the development installation.

1. Show the order of the active extensions:
   `php bin/php/console exp:ini get site.ini/ExtensionSettings/ActiveExtensions`.
2. Check whether dependency ordering is on:
   `php bin/php/console exp:ini where site.ini/ExtensionSettings/ExtensionOrdering`.
3. Find a setting several extensions set, for example `php bin/php/console exp:ini where
   design.ini/ExtensionSettings/DesignExtensions <sa>`, and read which file comes first.
4. Read a real dependency block: `grep -A8 "<dependencies>" extension/*/extension.xml | head -40`.
5. Add a dependency to your `extension.xml`, after `</metadata>`:

   ```xml
   <dependencies>
       <uses>
           <extension name="ezjscore" />
       </uses>
   </dependencies>
   ```

6. Check it: `xmllint --noout extension/mysite_hello/extension.xml`.
7. Clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini`) and open **Setup** > **Extensions**. Find where
   `mysite_hello` loads.
8. Commit `extension.xml` with `Updated: Declare the use of ezjscore`.

## Expected result

- Step 1: the list in load order with `mysite_hello` in it.
- Step 3: several files in load order, the value in effect at the end.
- Step 6: no output.
- Step 7: the card of `mysite_hello`; with `ExtensionOrdering=enabled` it loads after `ezjscore`.
- The trainer asks each learner which element they would use for a theme that changes another theme's templates.

## Quiz

1. Two extensions provide the same template. Which one wins?
2. Your theme overrides templates of a base theme. Which element do you declare?
   A. `<requires>` B. `<uses>` C. `<extends>` D. `<metadata>`
3. Which setting makes the kernel order extensions by their declared dependencies?
4. Does `<requires>` activate the other extension?
5. Which command lists every file that sets a variable, in load order?

<details>
<summary>Answers</summary>

1. The one loaded earlier in the order of active extensions.
2. **C.** `<extends>` loads yours before the base, so yours wins.
3. `site.ini [ExtensionSettings] ExtensionOrdering=enabled`.
4. No; it only orders active extensions.
5. `exp:ini where <file>/<block>/<variable>`.

</details>

## Further reading

- [Declaring dependencies in extension.xml](../../../designers/extensions/dependencies-in-extension-xml.md).
- [Loading order and which theme wins](../../../designers/extensions/loading-order-for-themes.md).
- [Extension loading order](../../../../features/6.0/extension-loading-order.md).

## Related pages

- [Module 7: Extensions](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
