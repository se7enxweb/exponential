---
title: "Delivering updates to paying customers"
audience: designer, developer
category: theme-sales
order: 140
summary: Ways to get theme releases to customers - public Packagist releases, a private Git repository with Composer, packages sent as files, or installing for them - with how each works and what access ends when.
keywords: updates, delivery, private repository, composer, private packagist, satis, deploy keys, access, customers
type: help
---
# Delivering updates to paying customers

This page is for designers who sell updates or maintenance. Choose one delivery way per offer and document it.

## The options

| Way | How the customer updates | Notes |
|---|---|---|
| Public release on Packagist | `composer update yourvendor/mytheme` | Simplest; the customer pays for support, not access |
| Private Git repository | Composer with a `vcs` repository and an access token or deploy key | Access can end with the subscription; the copies they have stay theirs under the GPL |
| A private Composer repository | Composer with your repository URL and credentials | For many packages; you run or rent the repository |
| `.ezpkg` or archive files | Import or copy by hand | For customers without Composer |
| You install it | Nothing; you deploy | Needs access to their server; agree on it in writing |

## Step by step: a private repository with Composer

1. Create a private repository for the customer's version (or give them read access to yours).
2. Give the customer a read-only credential: a deploy key or a token limited to that repository.
3. Tell them to add it to their project:

   ```bash
   composer config repositories.mytheme vcs https://github.com/yourvendor/mytheme-pro
   composer require yourvendor/mytheme-pro:~1.0.0
   ```

   with the credential stored in Composer's `auth.json` on their side, never committed.
4. Tag every release there as you do publicly ([Bumping the version before the tag](../distribution/bumping-versions-before-the-tag.md)).
5. When the subscription ends, remove the credential. Tell the customer beforehand.

## Rules

- Credentials go into `auth.json` or the server's environment, never into a repository or an email thread that others
  read.
- Releases are permanent here too: never move a tag in a private repository either.

## Related pages

- [Maintenance subscriptions](maintenance-subscriptions.md)
- [What buyers receive](what-buyers-receive.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
