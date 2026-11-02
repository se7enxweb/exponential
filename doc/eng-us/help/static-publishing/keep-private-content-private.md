---
title: "Keep private content private"
audience: admin, website-creator, editor, hosting, government
category: administration
order: 17
summary: How static publishing makes sure only public content reaches a public repository - builds as the anonymous user, the clean-up and refuse checks, leaving pages and sections out, and reading the diff before every public push.
keywords: static publishing, privacy, private content, anonymous user, exclude, ExcludePaths, ExcludeSections, refuse, session cookie, form token, debug output, public repository, diff, review, data protection, GDPR, GitHub Pages
featured: true
type: help
---
# Keep private content private

A public repository is public in full, including its history: a page that was pushed once can be found again even
after the next publish removes it. Static publishing is built so that this does not happen by accident, and this page
shows the safeguards and what is left to you.

## What you'll have at the end

A target that publishes only what any visitor may already see, and a habit of checking each publish.

## What Exponential does for you

1. **Builds as a visitor who is not signed in.** The build sends no cookie, so every page is what the anonymous user
   sees. Content behind a login, members' pages and drafts are never fetched.
2. **Cleans every page.** Debug output, form tokens, session ids in addresses and security nonces are taken out.
3. **Refuses what it cannot clean.** A page that still carries one of them, or whose response started a session,
   stops the build. Nothing is written and nothing is published.
4. **Copies only public files.** Storage images only of the published version of content the anonymous user may
   read; never draft images or uploaded originals that a page does not offer to every visitor.
5. **Keeps secrets out.** Tokens and keys are names in the settings; the values never reach a page, a command line or
   a log.

## What you decide

- **Who may read what.** The anonymous user's role decides what the copy holds. A section that should stay internal
  must not be readable by Anonymous, or must be left out.
- **Leave out** pages by path (`/members/*`) or by section, in step 7 of the wizard. Links to them go to your live
  site.
- **Read the changes** before every publish: Preview lists every page and file that is added, changed or removed.
  Publish asks you to confirm that you checked them.
- **Who may publish**: the policy `staticpublish/publish`, and **Verify it's you** on every publish
  ([Who may publish](who-may-publish-roles-and-policies.md)).
- **Public or private repository.** Free GitHub plans serve only public repositories. Content that must stay
  confidential belongs on a paid plan's private repository or on another host, not on a free public site.

## If something private was published

Publish again without it, or roll back ([History and rollback](history-and-rollback.md)). The page then disappears
from the site, but it stays in the repository's history. To remove it from the history as well you have to rewrite
the repository on GitHub, or delete the repository and publish to a new one; ask GitHub's support to clear cached
views if needed. Change any password or personal data that was exposed.

## Next step

[Build, preview and publish](build-preview-and-publish.md#check-the-build).
