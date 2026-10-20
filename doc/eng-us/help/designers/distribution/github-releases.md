---
title: "GitHub releases"
audience: designer, developer
category: theme-distribution
order: 140
summary: Create a GitHub release for a pushed tag with gh release create - title, notes from the changelog, latest flag - check it, and know what a release adds to a tag.
keywords: GitHub release, gh release create, release notes, latest, release title, notes-file, verify-tag
type: help
---
# GitHub releases

This page is for designers who have pushed a release tag. A GitHub release adds a title, notes and (optionally) files
to a tag, and makes the version visible on the repository's front page.

## Step by step

1. Write the notes into a file in your scratch folder, for example `var/tmp/notes-1.0.25.md`
   ([Writing release notes](writing-release-notes.md)).
2. Create the release for the pushed tag:

   ```bash
   gh release create v1.0.25 --verify-tag \
       --title "v1.0.25: the event pages in dark mode" \
       --notes-file var/tmp/notes-1.0.25.md
   ```

   `--verify-tag` refuses to create a release if the tag is not on the remote, so `gh` never creates a tag for you
   from whatever commit is current.
3. Check it:

   ```bash
   gh release view v1.0.25
   gh release list --limit 3
   ```

   Expected: your title and notes, and the new release marked **Latest**.

## Titles the published themes use

`gh release list -R se7enxweb/cjw_themes_jumper` shows titles such as "v0.1.6: the starter design without inline
scripts" and "v0.1.4: The map popup escapes the address and title": the version, a colon, and what the release is
about. Use the same text as the tag message.

## Files on a release

A theme installed with Composer needs no files on the release: Composer downloads the tag's archive. Attach files only
when users need something else (an `.ezpkg`, a screenshot set). An attached file is part of the release; never
replace it later ([A pushed tag is permanent](a-pushed-tag-is-permanent.md)).

## Related pages

- [Writing release notes](writing-release-notes.md)
- [Keeping Packagist up to date automatically](packagist-auto-update.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
