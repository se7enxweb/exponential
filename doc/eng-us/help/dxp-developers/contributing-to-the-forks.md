---
title: Contributing to the forks
audience: dxp-developer
category: dxp-development
order: 46
summary: How a DXP developer gives back - where to report a bug (skeleton, metapackage or a fork), how to send a pull request the way the skeleton's README describes, how to test a fork change in your own project, and how security reports are made.
keywords: contribute, pull request, fork, issue, bug report, se7enxweb, GitHub, phpunit, path repository, security report, community, discussions
type: help
---
# Contributing to the forks

This page is for developers who found a bug or wrote an improvement while working on a DXP project. The project
grows through contributions; the se7enxweb repositories accept pull requests.

## Contents

1. [Where a change belongs](#where-a-change-belongs)
2. [Reporting a bug](#reporting-a-bug)
3. [A pull request, step by step](#a-pull-request-step-by-step)
4. [Testing a fork change in your project](#testing-a-fork-change-in-your-project)
5. [Security reports](#security-reports)
6. [Places to talk](#places-to-talk)

## Where a change belongs

| The problem is in | Repository |
|---|---|
| The install guide, the project template | `se7enxweb/exponential-platform-dxp-skeleton` |
| Which packages the DXP installs | `se7enxweb/exponential-platform-dxp` |
| The content kernel and its commands | `se7enxweb/exponential-platform-dxp-core` |
| The administration interface | `se7enxweb/admin-ui` (assets: `se7enxweb/admin-ui-assets`) |
| Rich text | `se7enxweb/fieldtype-richtext` |
| An upstream `ibexa/*` package the DXP requires unchanged | the upstream project; if it is a blocker for the DXP, open an issue on the metapackage too |

`composer show -s` and `composer why <package>` tell you which package a class comes from.

## Reporting a bug

Use the issue tracker of the repository. Include the PHP version, `composer show "se7enxweb/*"`, the exact error from
`var/log/`, and steps to reproduce on a fresh skeleton install (SQLite makes that quick,
[SQLite for development](sqlite-for-development.md)). [Writing good issues](../extension-development/part-5-extend-fork-contribute/writing-good-issues.md)
applies here as well.

## A pull request, step by step

The skeleton's README describes the path:

1. Fork the repository on GitHub.
2. Clone your fork and create a branch: `git checkout -b feature/my-improvement`.
3. Install the full development stack (`APP_ENV=dev`).
4. Make the change, following the repository's coding standards.
5. Test with `php bin/phpunit` and check for regressions.
6. Push the branch and open a pull request against the default branch (`master` for the skeleton).
7. Answer the review.

Write the commit message as the project does: what changed and why, with a prefix such as `Added:` or `Updated:`
([Commit messages](../extension-development/part-5-extend-fork-contribute/commit-messages.md)).

## Testing a fork change in your project

Point Composer at your clone with a path repository, in a branch of your project, never in production:

```json
{
    "repositories": [
        { "type": "path", "url": "../admin-ui", "options": { "symlink": true } }
    ]
}
```

```bash
composer require "se7enxweb/admin-ui:dev-my-branch as 5.0.5.99"
```

Remove the repository entry again before you merge your project's branch.

## Security reports

Do not open a public issue for a vulnerability. The skeleton's README gives a security mail address for responsible
reports; use it, and give the maintainers time to publish a fix.

## Places to talk

The skeleton's README lists the community places: GitHub Discussions on the metapackage's repository, the forums at
share.exponential.earth, and the chat channels. See also
[Who is the Exponential Foundation?, Community places](../who-is-the-exponential-foundation.md#community-places).

## Related pages

- [Contributing back with pull requests](../extension-development/part-5-extend-fork-contribute/contributing-back-with-pull-requests.md)
- [The se7enxweb forks and `replace`](the-se7enxweb-forks-and-replace.md)
- [Develop with Git](../develop-with-git.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
