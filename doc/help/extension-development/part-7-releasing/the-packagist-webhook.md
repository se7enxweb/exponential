---
title: The Packagist webhook
audience: developer, app-creator
category: extension-development
order: 60
summary: When Packagist shows an old newest version although the tag is on GitHub, the repository is missing its Packagist hook. How to see that, how to set it up, and how to bring the package up to date once.
keywords: Packagist, webhook, hook, GitHub integration, stale version, auto-update, gh api hooks, update package
type: help
---
# The Packagist webhook

Part 7, chapter 6. You pushed tag `v1.2.4`, the GitHub release is there, but Packagist still offers `1.2.3` and
`composer require` installs the old version. The usual cause: GitHub never tells Packagist that the repository
changed, because the repository has no Packagist hook. This chapter shows how to check and fix that.

## How Packagist learns about new tags

Packagist does not watch repositories on its own schedule for each push. GitHub notifies it through a webhook (or
the Packagist GitHub integration) on every push. Without that notification the package page stays at whatever
Packagist read last.

This project found exactly that on one of its extensions: its Packagist page stayed at an old version for months,
because the repository had no hook. The tags were all there; only the notification was missing.

## Check

On GitHub: the repository's **Settings > Webhooks** lists the hooks. One should point at `packagist.org`.

With the GitHub command line tool (you need admin rights on the repository):

```bash
gh api repos/your-account/myext/hooks --jq '.[].config.url'
```

No line with `packagist.org` means no hook.

On Packagist, the package page shows when it was last updated, and warns maintainers when the package is not
auto-updated.

## Fix

Packagist's own instructions are on its page [About: how to update packages](https://packagist.org/about#how-to-update-packages).
In short, one of:

1. **The GitHub integration**: sign in to Packagist with GitHub and grant it access to the organization; Packagist then
   sets up hooks for packages it can see.
2. **A webhook by hand**: in the repository's **Settings > Webhooks > Add webhook**, the payload URL and the secret
   that Packagist shows on your profile page, content type `application/json`, the push event.

The secret is your Packagist API token: keep it out of issues, commit messages and scripts in the repository.

## Bring it up to date once

After the hook is set, the next push updates Packagist. To catch up now, use the **Update** button on the package
page (as a maintainer), or push anything.

Then check: the package page lists the new version, and

```bash
composer show your-account/myext --all | grep versions
```

shows it too.

## Related pages

- Previous: [Composer and Packagist](composer-and-packagist.md)
- Next: [Changelogs](changelogs.md)
- [Creating webhooks](https://docs.github.com/en/webhooks/using-webhooks/creating-webhooks) on GitHub Docs
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
