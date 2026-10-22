---
title: How do I turn an extension on or off?
audience: admin
category: manual
order: 645
summary: Under Setup > Extensions, click Activate or Deactivate on the extension's card, read the risks under Review changes and click Apply changes; the page writes ActiveExtensions, clears the caches and regenerates the autoloads.
keywords: extension, activate, deactivate, turn on, turn off, plugin, add-on, Setup Extensions, ActiveExtensions, apply changes, autoload, loading order
context: setup/extensions
type: help
---
# How do I turn an extension on or off?

Extensions add features to Exponential without changing its own files. An extension that is on the server does
nothing until it is active. **Setup** > **Extensions** switches extensions on and off for the whole installation.

## Who can do this

Users whose role has the policy `setup/setup`, usually administrators only. Getting the extension's files onto the
server is a separate job for a developer or the host.

## Steps

1. Click **Setup** in the top menu and choose **Extensions**.
   The list shows the active extensions first, in their loading order, and the inactive ones after them.
2. Find the extension with the search, or choose **Inactive** or **Active** in the filter.
3. Click **Activate** on its card to turn it on, or **Deactivate** to turn it off.
   Nothing is written yet; a bar says how many changes are waiting.
4. Click **Review changes**.
   The page lists the changes and the risks, for example an extension that another active extension needs.
5. If a risk is marked red and you still want the change, tick the confirmation box under it.
6. Click **Apply changes**.
   The page writes the list, clears the caches and says what changed.
7. Open the pages of the extension and check that it works.

## What can go wrong

- **The extension is not in the list.** Its folder is not under `extension/` on the server. Ask the person who
  installs software on the server.
- **The site still behaves as before.** When the site is also served by Exponential Velocity, its workers keep the
  old list until it is restarted (`./console exp:velocity restart`); the page says whether Velocity is running.
- **Something else stopped working after a deactivation.** Another extension needed it, or it provided the design of a
  siteaccess. Activate it again; the review step names these cases before you apply.
- **The extension needs more than activation.** Many extensions need database tables, settings or policies of their
  own. Read the extension's own documentation in the help center first.
- **The change was made by someone else at the same time.** The page notices it and starts again from the saved
  list instead of overwriting it.
- **Attention:** Deactivating `ezformtoken` removes the protection of every form against forged requests, and
  deactivating `ezjscore` breaks the scripts of the administration. Leave both active.

## Related pages

- [The Extensions page](../../guides/extensions-page.md): the cards, the loading order and the review in detail
- [How do I see the active extensions and the system information?](how-do-i-see-active-extensions-and-system-information.md)
- [Activating an extension](../extension-development/part-3-extension-developer/activating-an-extension.md)
- [Extensions](../../guides/extensions.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
