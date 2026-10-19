---
title: Rebranding ethically
audience: developer, designer, app-creator
category: extension-development
order: 50
summary: What you may and may not do when you put your own name on a fork - new names, kept credit, honest descriptions, no borrowed trademarks - with a checklist and examples from extensions this project forked.
keywords: rebranding, rename, fork, credit, ethics, trademark, attribution, honesty, original author, description
type: help
---
# Rebranding ethically

Part 5, chapter 5. Free software licenses let you copy, change and republish code under your own name. They do not
let you pretend you wrote it, and they do not give you anyone's trademark. Between those two lines there is a lot of
room, and this chapter shows how to use it honestly.

## What the license allows

The GPL, MIT and BSD licenses all allow you to:

- publish a changed copy under a new name;
- sell it, or services around it;
- maintain it separately from the original forever.

## What it does not allow, or requires

| Rule | Where it comes from |
|---|---|
| Keep the existing copyright notices | every common free license requires it |
| Keep the license (GPL: the same license for the whole) | the license itself |
| Mark that you changed the files (GPL 2: "prominent notices stating that you changed the files and the date") | GPL 2, section 2a |
| Do not use the original's trademark or logo in a way that suggests it is the original | trademark law, not the license |

## What fairness asks beyond that

The license is the minimum. Fairness asks a bit more:

- **Say what it is.** "A fork of X" in the description and the README. se7enxweb/expchangeclass's `composer.json`
  description ends with "A hardened fork of eZChangeclass", and its README opens its explanation with "This is a fork
  of eZChangeclass" and a link.
- **Say why.** A short section on why the fork exists and what it changes. Users deserve to choose.
- **Name the people.** The original authors stay in `composer.json` `authors` (expchangeclass gives the original author
  the role "Original author of eZChangeclass") and in an `Includes` entry of `ezinfo.php`.
- **Do not hide the history.** Fork with Git so the original commits and their authors stay in the log.
- **Do not imply endorsement.** "Works with X" is fine; "the official X" is not, unless the owners of X say so.

## Names in practice

| Situation | A good name |
|---|---|
| You maintain a PHP 8 version of an abandoned extension | a new package name under your account; the display name may say "for Exponential 6" |
| Your fork goes a different direction | a new name of its own, with "based on X" in the description |
| A temporary fork for a pull request | no rebranding at all: keep everything as it is |

## Product names inside the code

Renaming the product name in interface texts and documentation is part of an upgrade
([Naming and branding during an upgrade](../part-4-upgrading/naming-and-branding-during-an-upgrade.md)). Renaming the
original author's class names, operators or settings is not rebranding: it breaks every site that uses them, and it
erases a visible trace of where the code came from. Keep them.

## Logos and designs

A theme or an extension may carry the original company's logo in images. Replace logos that identify a company or a
product you are not; keep the license and copyright of every image you keep. A logo is usually not covered by the
code's license at all.

## Checklist

- [ ] The package name and display name are new and do not suggest you are the original.
- [ ] The description and README say "fork of" or "based on", with a link.
- [ ] Every copyright line is kept; yours is added beside them.
- [ ] The license is unchanged (or changed only as the license allows, for example GPL-2.0-or-later to GPL-3.0).
- [ ] The original authors are in `composer.json` and `ezinfo.php`.
- [ ] No logo or trademark of the original is used as yours.
- [ ] The Git history is kept.

## Related pages

- Previous: [Keeping a fork in sync with its source](keeping-a-fork-in-sync.md)
- Next: [Licenses and how they combine](licenses-and-compatibility.md)
- [Attribution and copyright notices](attribution-and-copyright-notices.md)
- [The GNU GPL version 2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html) on gnu.org
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
