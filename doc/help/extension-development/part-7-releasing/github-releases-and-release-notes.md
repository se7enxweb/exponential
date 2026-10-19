---
title: GitHub releases and release notes
audience: developer, designer, app-creator
category: extension-development
order: 80
summary: Turn a pushed tag into a GitHub release - title, notes from the changelog, what to attach and what not - with the gh command and the rule that a published release is never edited to change what it delivers.
keywords: GitHub release, release notes, gh release create, tag, changelog, notes, assets, archive, draft, publish
type: help
---
# GitHub releases and release notes

Part 7, chapter 8. A tag is for Git and Composer. A GitHub release is for people: a page with a title and notes,
listed on the repository's front page, that users and watchers are notified about. This chapter creates one.

## What a release is

A GitHub release belongs to one tag. GitHub adds the source archives (zip and tar.gz) of that tag automatically. You
add a title and notes, and optionally files ("assets").

## Create it

After the tag is pushed ([Tags and releases are permanent](tags-are-permanent.md)), with the GitHub command line tool:

```bash
gh release create v1.2.4 --title "1.2.4" --notes-file var/release-notes-1.2.4.md
```

or on the website: **Releases > Draft a new release**, choose the existing tag, write title and notes, **Publish**.

Use the version as the title, or the version and a short theme ("1.2.4: Content Security Policy").

## The notes

The notes are the changelog entry of this version ([Changelogs](changelogs.md)), plus two things that fit a release
page:

```markdown
Upgrade notes
- Clear the template override cache after updating: two templates were added.

Fixed
- The parameter file shown after a conversion is escaped.

Updated
- The context menu entries carry no inline handlers, so they work under a Content Security Policy.

Requires: Exponential 6.0.15 or later, PHP 8.0 or later.
Install or update: composer require your-account/myext:^1.2
```

- **Requirements** in one line.
- **The install command**, so a reader can act without opening the README.

Do not put private paths, host names or the names of internal tools into notes; they are public.

## Attachments

Most extensions need none: Composer installs from the tag, and GitHub's own archives contain the source. Attach a
file only when users need something the source does not give them, for example an `.ezpkg` package
([Packages (.ezpkg) and the package creator](packages-ezpkg.md)) or a built JavaScript bundle that is not committed.

## Never change what a release delivers

Editing the text of a published release (a typo in the notes) is harmless. Replacing or adding an attachment with
different content is not: it gives two different artifacts the same version, exactly like moving a tag. If an
attachment was wrong, publish the next version.

## Drafts

`gh release create ... --draft`, or **Save draft** on the website, keeps the release invisible until you publish it.
Use it to prepare the notes while the last checks run. The tag must still be right before it is pushed: a draft does
not protect a tag.

## Related pages

- Previous: [Changelogs](changelogs.md)
- Next: [filelist.md5 and the upgrade check](filelist-md5-and-the-upgrade-check.md)
- [About releases](https://docs.github.com/en/repositories/releasing-projects-on-github/about-releases) on GitHub Docs
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
