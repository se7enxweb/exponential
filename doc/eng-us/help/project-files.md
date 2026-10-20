---
title: "Project files: README, security policy and contributing in the help"
audience: admin, developer
category: project-files
order: 60
summary: The README.md, SECURITY.md, CONTRIBUTING.md, COPYRIGHT.md and LICENSE.md of the repository, and the README files of its directories, are pages of the help. Where to find them, how links between them and the documentation work, and what is never shown.
keywords: README, SECURITY, CONTRIBUTING, COPYRIGHT, LICENSE, project files, repository, root, Markdown, git
type: help
---
# Project files: README, security policy and contributing in the help

The repository of Exponential has a few files outside the documentation directory that every administrator and
developer should know: the [README](../../../README.md) that introduces the project, the
[security policy](../../../SECURITY.md) that says how to report a vulnerability, the
[contributors' guidelines](../../../CONTRIBUTING.md), the [copyright notice](../../../COPYRIGHT.md) and the
[license](../../../LICENSE.md). The help shows them as pages of their own, so you can read them in the
administration without going to GitHub.

## Where to find them

- **All help pages** (the contents page) and **the page tree** next to every help page: each of them is a page at
  the top of the tree, next to the main folders, under its own title (the README's is the name of the project).
  They are not in a folder, and the breadcrumb above one reads Help > its title.
- **The dashboard**: the category **Project files**.
- **The search**: they are found like every page; choose the category "Project files" or the type "Project file"
  to see only them.

Their addresses start with `project/`, followed by the path of the file in the repository:
`/exphelp/viewer/project/README.md`, `/exphelp/viewer/project/SECURITY.md`. The `README.md` files of the command line
scripts (`project/bin/...`), the designs and the kernel are shown too, when those directories have one.

The documentation of the extensions is not among them: it has its own category, **Extensions**. Tests, the files
of hidden directories such as `.github/` (the pull request template) and anything else that is not meant to be read
as documentation are never shown here.

## Links between them and the documentation

The files link to each other and to the documentation with relative links, as on GitHub, and the help follows them:
a link from the README to a guide opens the guide in the help, and a link from a help page to the README opens the
README here. Each page also has "Edit on GitHub", which opens the same file in the repository.

## In other languages

These files are written in English and are not translated, like the release notes. If you read the help in another
language, they are shown in English with a note that says so.

## License

The project files are part of the software, not of the documentation: they are distributed with Exponential under
its license, the GNU General Public License, version 2 or any later version. The note under each of them says so and
links the license. The documentation pages themselves are free documentation (see
[Free documentation](free-documentation.md)).

## What is never shown

Only files that are committed to the repository are shown. A file someone put in the installation directory without
committing it (a private note, a local copy of a page) is never shown, never found by the search and never linked,
whatever the settings say. Files in places git ignores, files outside the installation directory, hidden files and
directories (`.github/`), tests, anything below `extension/`, and anything that is not Markdown are never shown
either.

For administrators who want to change which files are shown, and for the full list of rules, see
[The help system](../features/6.0/help-system.md#project-files) (`exphelp.ini [ProjectFiles]`).

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](partners/partners-start-here.md) and [How to apply](partners/how-to-apply.md).
<!-- exp-partner-block:end -->
