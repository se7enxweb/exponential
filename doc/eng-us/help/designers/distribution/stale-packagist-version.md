---
title: "Packagist shows an old version"
audience: designer, developer
category: theme-distribution
order: 210
summary: Diagnose why Packagist or composer require does not offer the newest release of a theme - a missing or failing hook, a tag Packagist could not read, a composer.json error, or a cached client - and fix each.
keywords: stale Packagist, old version, not updated, missing webhook, composer cache, packagist update, version missing
type: help
---
# Packagist shows an old version

This page is for designers whose new release does not appear on Packagist, or whose users still get an older
version from `composer require`.

## Diagnose in order

1. **Is the tag on the remote?** `git ls-remote --tags origin | grep v1.0.25`. No output: push the tag.
2. **Does Packagist know it?**

   ```bash
   curl -s https://repo.packagist.org/p2/yourvendor/mytheme.json | grep -o '"version":"[^"]*"' | head -3
   ```

   The new version missing: go to step 3. Present: go to step 5.
3. **Is there a hook?** `gh api repos/yourvendor/mytheme/hooks --jq '.[].config.url'` lists a `packagist.org` URL.
   No hook is the most common cause: a package can stay on an old version for months without one. Set it up
   ([Keeping Packagist up to date automatically](packagist-auto-update.md)) and press **Update** on the package page.
4. **Could Packagist read the tag?** The package page shows errors from its last read. A broken `composer.json` in the
   tagged commit, or a `version` field that disagrees with the tag, makes Packagist skip that version. Fix it in the
   next release; never move the tag.
5. **Is the user's Composer cached or constrained?**

   | Check | Command |
   |---|---|
   | What the user's constraint allows | `composer why-not yourvendor/mytheme 1.0.25` |
   | Clear Composer's cache | `composer clear-cache` |
   | Which version is installed | `composer show yourvendor/mytheme` |

## A table of causes

| Symptom | Cause | Fix |
|---|---|---|
| Packagist lists only `dev-main` | No tag yet | Release a tag |
| Packagist months behind | No update hook | Add the hook, press Update |
| One version missing between others | `composer.json` invalid in that tag | Release the next version with it fixed |
| Packagist fine, user still old | Constraint or cache | `composer why-not`, `composer clear-cache` |

## Related pages

- [Version constraints your users write](version-constraints-for-users.md)
- [Checking what is already published](checking-what-is-already-published.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
