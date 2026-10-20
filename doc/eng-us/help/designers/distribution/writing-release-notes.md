---
title: "Writing release notes"
audience: designer, sales-marketing
category: theme-distribution
order: 150
summary: Write release notes users can act on - a headline, what changed for them, what they must do after upgrading, credits, and an honest note about any earlier mistake - with a real example from a published theme.
keywords: release notes, changelog, upgrade notes, what changed, credits, headline, GitHub release text
type: help
---
# Writing release notes

This page is for designers writing the text of a GitHub release. Users read release notes to decide whether to update
and what to do afterwards.

## The parts

1. **A headline** that says what the release is about.
2. **What changed, for the user**: one bullet per change, with its prefix (`Added:`, `Updated:`, `Fixed:` ...).
3. **What to do after updating**: caches to clear, settings to add, upgrade scripts to run. Leave it out only if
   nothing is needed, and then say "Nothing to do after updating."
4. **Compatibility**: the Exponential and PHP versions it was tested with, if they changed.
5. **Credits**: who reported or contributed, by their public handle and with their consent.
6. **A correction**, when the release repairs an earlier one: what was wrong with which version.

## A real example

The notes of `cjw_themes_jumper` v0.1.6 start like this:

```markdown
## Starter design without inline code

The pages of the starter design now work under a Content Security Policy without `'unsafe-inline'`.

- The page script of every page (...) is `javascript/cjw_jumper_page.js`, loaded last in the packed head script ...
- The speech bubble's close link and the icon that opens it again are `href="#"` links with `data-cjw-bubble`
  (no `javascript:` URLs) ...
```

A headline with the benefit, then the details a template author needs.

## Step by step

1. Copy the release's entries from `CHANGELOG.md`.
2. Add the headline and the "after updating" section.
3. Read it as a user who skipped the last three releases: is anything unclear?
4. Remove anything private: internal paths, host names, customer names, credentials.
5. Save it as the notes file for `gh release create` ([GitHub releases](github-releases.md)).

## Related pages

- [Changelog entries](changelog-prefixes.md)
- [Case studies and content marketing](../../business/case-studies-and-content-marketing.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
