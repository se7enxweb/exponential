---
title: "Publishing a security release"
audience: designer, developer
category: theme-lifecycle
order: 200
summary: Release a security fix for a theme - fix, version, tag and release first, then describe the problem plainly with affected versions and the action users must take, and tell the channels where your users are.
keywords: security release, fix release, advisory, affected versions, upgrade notice, changelog Fixed, disclosure
type: help
---
# Publishing a security release

This page is for designers who have a security fix ready ([Handling security reports](handling-security-reports.md)).

## Step by step

1. **Version**: the next number in your maintained line ([Choosing a version number](../distribution/choosing-a-version-number.md)).
   Only the fix goes in, if possible, so users can update without risk.
2. **Bump, commit, tag, push** ([Bumping the version before the tag](../distribution/bumping-versions-before-the-tag.md)).
3. **Release notes** that say plainly:
   - what was wrong, in terms a site owner understands ("markup in an address or a title ran in the map popup");
   - which versions are affected;
   - what users must do (update; run an upgrade script; clear caches);
   - who reported it, as they wish to be credited.
4. **Changelog entry** with the prefix `Fixed:`.
5. **Tell your users**: the release, the project news on projects.exponential.earth, your announcement thread, and
   paying customers directly.
6. **Optionally** publish a GitHub security advisory for the repository, so tools that read advisories warn users.

## What not to publish

- An exploit ready to use.
- Details before the fix is available, unless the problem is already public.

## Example

`cjw_themes_jumper` 0.1.4 (17 October 2026) was released with the title "The map popup escapes the address and title"
and a changelog entry that explains the cause, the fix, the credit (a pull request by a contributor) and a related step
users of another extension should take.

## Related pages

- [When a release goes wrong](../distribution/when-a-release-goes-wrong.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
