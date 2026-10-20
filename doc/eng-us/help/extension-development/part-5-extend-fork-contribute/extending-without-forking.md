---
title: Extending without forking
audience: developer, designer, app-creator
category: extension-development
order: 10
summary: Change how the kernel or someone else's extension behaves from your own extension - settings, template overrides, design order, handlers, events, module replacement - so their updates keep reaching you.
keywords: extend, without forking, override, template override, settings append, design extension, handler, event, filter, module override, extension load order
type: help
---
# Extending without forking

Part 5, chapter 1. Before you copy someone else's extension and change it, look for a way to change its behavior
from your own extension. Exponential was built for this: almost everything the kernel and well-written extensions do
can be adjusted from outside. This chapter lists the ways, from the lightest to the heaviest, so you can pick the
lightest one that works.

## Why it matters

When you change someone else's files, you own those changes forever. Every update of the original has to be merged
with your changes by hand. When you change behavior from your own extension, the original stays untouched: you
update it with `composer update`, and your extension keeps doing its job.

## The ways, from light to heavy

| Way | What it changes | Chapter |
|---|---|---|
| 1. A setting in your extension's `settings/*.ini.append.php` | anything the original reads from its INI file | [Settings in an extension](../part-3-extension-developer/settings-in-an-extension.md) |
| 2. A template in your design | how a page or a part looks | [The design chain](../part-2-builder/the-design-chain.md) |
| 3. A template override rule (`override.ini`) | one template for one class, node, section or view mode | [Template overrides](../part-2-builder/template-overrides-basics.md) |
| 4. A template operator or fetch function of your own | what templates can compute or load | [Template operators](../part-3-extension-developer/template-operators.md) |
| 5. An event listener or filter | what happens before or after kernel actions | [Events, listeners and filters](../part-3-extension-developer/events-and-filters.md) |
| 6. A handler named by a setting | a whole service (mail transport, image handler, search) | [Handlers named by settings](../part-3-extension-developer/handlers-named-by-settings.md) |
| 7. A subclass of the original class, registered where the original is named | one class's behavior | below |
| 8. A module of the same name in your extension | a whole module and its views | [Extension module override](../../../features/6.0/extension-module-override.md) |
| 9. A kernel override | a kernel class | [Kernel overrides: the last resort](../part-3-extension-developer/kernel-overrides-the-last-resort.md) |

Each step down the list ties you closer to the original's internals, so an update of the original is more likely to
need a change on your side.

## Who wins: the load order

Most of these ways work because the kernel reads the active extensions in an order, and the last setting or the first
template found wins. Your extension must therefore load in the right place relative to the one it changes. Name the
original in your `extension.xml`:

```xml
<dependencies>
    <requires>
        <extension name="theoriginal" />
    </requires>
</dependencies>
```

The details, including how to check the order you got, are in
[Extension load order](../part-3-extension-developer/extension-load-order.md).

## Example: a different template for one block

The original extension renders a block with `design/standard/templates/theoriginal/block.tpl`. You want another
markup. In your extension:

```text
extension/mysite/design/mysite/templates/theoriginal/block.tpl
```

with your design (`mysite`) placed before `standard` in the siteaccess's design list. The original's file stays as it
is; yours is found first. If you only need it for one content class, use an override rule instead, so the original's
template still serves the others.

## Example: one setting changed

The original reads `theoriginal.ini [ListSettings] Limit=10`. In your extension:

```ini
<?php /* #?ini charset="utf-8"?

[ListSettings]
Limit=25

*/ ?>
```

saved as `extension/mysite/settings/theoriginal.ini.append.php`. With your extension after the original in the load
order, `Limit` is 25.

## Example: a subclass

When the original names its classes in settings (many do: handlers, block types, converters), register a subclass
of your own there:

```php
<?php
class mySiteListHandler extends theOriginalListHandler
{
    public function title()
    {
        return 'Latest from ' . parent::title();
    }
}
```

Override only the methods you need and call `parent::` for the rest. Regenerate the autoloads after adding the class
(`php bin/php/ezpgenerateautoloads.php -e`). If the original creates the class with `new` directly and names it
nowhere, a subclass cannot replace it, and you have found a good reason to ask the author for a setting
([Writing good issues](writing-good-issues.md)).

## When none of this works

Then you need to change the original's code. There are still two ways that do not mean a fork:

1. **Ask for an extension point.** Open an issue that explains what you want to change and proposes a setting, an
   event or a template variable. Better: send it as a pull request
   ([Contributing back with pull requests](contributing-back-with-pull-requests.md)). Once it is merged, you extend
   without forking.
2. **Wait for it in a branch of your fork** while the pull request is open, and go back to the original once it is
   released ([When to fork, and when not to](when-to-fork.md)).

## Related pages

- Previous: [Part 5: Extending, forking ethically and contributing back](README.md)
- Next: [When to fork, and when not to](when-to-fork.md)
- [RAD extension points](../../../bc/6.0/rad-extension-points.md)
- [Extension load order](../part-3-extension-developer/extension-load-order.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
