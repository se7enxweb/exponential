---
title: "End of life"
audience: designer, project-manager
category: theme-lifecycle
order: 250
summary: End maintenance of a theme responsibly - decide, look for a successor or new maintainer first, give notice, publish a last release, keep it installable, and archive - so users are never stranded without warning.
keywords: end of life, EOL, stop maintenance, last release, successor, notice, archive, abandoned
type: help
---
# End of life

This page is for designers who can no longer maintain a theme. Stopping is fine; stopping silently is not.

## Step by step

1. **Decide** the date and the reason.
2. **Look for a new maintainer first**: a contributor, an agency using the theme, the community
   ([Handing a theme over](handover-to-a-new-maintainer.md)). Ask in the issues and on share.exponential.earth.
3. **Name a successor** if there is one: another theme, or a fork someone maintains.
4. **Announce** with notice ([Announcing end of life](announcing-end-of-life.md)).
5. **Publish a last release** with every finished fix, the README marked "no longer maintained" and the successor named.
6. **Keep it installable**: never delete the repository, tags or the Packagist package; sites depend on them.
7. **Archive** the repository ([Archiving a repository](archiving-a-repository.md)) and, on Packagist, mark the package
   as abandoned, naming the successor if any.
8. **Update listings**: the project page on projects.exponential.earth says the theme ended and links the successor.

## What you do not owe

After end of life you owe no fixes. Say clearly what users can do: switch to the successor, fork the theme (the GPL
allows it), or pay someone to maintain their copy.

## Related pages

- [The life cycle of a theme](the-life-cycle-of-a-theme.md)
- [Adopting an abandoned theme](adopting-an-abandoned-theme.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
