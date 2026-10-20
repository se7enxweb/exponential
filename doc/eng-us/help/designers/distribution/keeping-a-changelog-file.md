---
title: "Keeping a CHANGELOG.md"
audience: designer
category: theme-distribution
order: 180
summary: Maintain a theme's CHANGELOG.md release by release - collect entries from the git log since the last tag, sort them by prefix, add the heading in the release commit, and keep the file and the GitHub releases consistent.
keywords: CHANGELOG.md, changelog file, git log, since last tag, release history, maintain changelog
type: help
---
# Keeping a CHANGELOG.md

This page is for designers who release regularly. The changelog file travels with the theme into every installation;
a GitHub release does not. Keep both.

## Step by step for each release

1. **Collect the changes since the last release**:

   ```bash
   git log --oneline "$(git describe --tags --abbrev=0)"..HEAD
   ```

   Every commit since the newest tag, one per line.
2. **Rewrite them** as changelog entries with prefixes ([Changelog entries](changelog-prefixes.md)): merge commits
   that belong together, drop internal ones (a typo fix in a comment), and write each from the user's view.
3. **Add a heading** at the top of `CHANGELOG.md` with the new version (without `v`), then the entries:

   ```markdown
   # Changelog

   ## 1.0.25

   - Added: Dark mode for the event pages.
   - Fixed: The search field keeps its focus ring at 200% zoom.
   ```

4. **Commit it with the version bump** ([Bumping the version before the tag](bumping-versions-before-the-tag.md)).
5. **Reuse the entries** for the GitHub release notes.

## Good practice

- Newest release at the top.
- Never rewrite the entries of a published release, except to correct a factual mistake, and then say so.
- If you skipped the changelog for older releases, start now; a short note "Releases before 1.0.20: see the GitHub
  releases" is better than nothing.

## Related pages

- [INSTALL.md and CHANGELOG.md](../extensions/install-and-changelog-files.md)
- [Writing release notes](writing-release-notes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
