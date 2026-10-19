---
title: The maintenance life cycle
audience: developer, designer, app-creator
category: extension-development
order: 130
summary: An extension's life from the first release to the last - supported versions, following PHP and kernel releases, deprecating before removing, branches for old versions, and ending maintenance honestly.
keywords: maintenance, life cycle, support, PHP versions, kernel releases, deprecation, branches, end of life, archive, hand-over
type: help
---
# The maintenance life cycle

Part 7, chapter 13. A release is a moment; an extension lives for years. This chapter looks at the whole span: what
"supported" means, how to keep up with PHP and the kernel, how to change things without surprising users, and how to
end maintenance well.

## The stages

| Stage | What happens | Typical releases |
|---|---|---|
| Young (0.x or 1.0) | the design still moves; users expect changes | frequent, small |
| Stable (1.x and later) | users depend on it; changes are careful | patch releases, occasional minor ones |
| Mature | it does what it should; work is mostly keeping up | patch releases when PHP or the kernel move |
| Ending | the maintainer stops, or the kernel now does the job | a last release, then a clear notice |

## Say what you support

Write it in the README, as a small table:

| | Supported |
|---|---|
| Exponential | 6.0.14 and later |
| PHP | 8.0 to 8.5 |
| Databases | SQLite, MySQL, MariaDB, PostgreSQL |

and keep `composer.json` in step ([Composer and Packagist](composer-and-packagist.md)). Users can then decide
before they install, instead of finding out.

## Keeping up

| Event | What to do |
|---|---|
| A new PHP minor version (yearly, usually late in the year) | add it to the CI matrix; fix new deprecations; release |
| A PHP version reaches its end of life | decide whether to keep supporting it; if you drop it, that is at least a minor release, announced |
| A kernel release | read its behavior changes and changelog; test; release if needed |
| A library you bundle releases a fix | update it; release |

The kernel's own PHP history is a good calendar of what to expect: [PHP 8 support](../../../bc/6.0/php8.md).

## Changing things without surprises

1. **Deprecate before you remove.** Keep the old setting, template or operator working for at least one minor
   release, with a note in the changelog and, where possible, a message in the debug output.
2. **Remove only in a major release**, listed in the upgrade notes.
3. **Rename by adding**: the new name first, the old name as an alias
   ([Naming and branding during an upgrade](../part-4-upgrading/naming-and-branding-during-an-upgrade.md)).

## Old versions

Most extensions maintain only their newest version. If users need fixes for an older major version (because the new
one needs a newer kernel), keep a branch such as `1.x` and release patch versions from it. Announce how long you will
do that.

## Ending well

When you stop maintaining an extension:

- Say so at the top of the README, with the date and, if there is one, the replacement.
- Offer it for adoption ([Adopting an abandoned extension](../part-5-extend-fork-contribute/adopting-an-abandoned-extension.md)
  describes the other side). A new maintainer is better than an archive.
- If nobody takes it, archive the repository on GitHub: it stays readable and installable, and everyone can see it
  is no longer maintained. Do not delete it: sites depend on its tags.
- Mark the Packagist package as abandoned, with the replacement if there is one.

## Related pages

- Previous: [Announcing a release](announcing-a-release.md)
- Next: [The release checklist](the-release-checklist.md)
- [The maintainer path](../part-5-extend-fork-contribute/the-maintainer-path.md)
- [Supported PHP versions](https://www.php.net/supported-versions.php) on php.net
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
