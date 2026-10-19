---
title: Finding ideas on GitHub
audience: user, developer, designer, app-creator
category: extension-development
order: 90
summary: Search GitHub for extensions and working code: the se7enxweb organization, code search for kernel class names, history and issues, and whether a project is alive.
keywords: github, search, code search, se7enxweb, repositories, forks, archived, issues, pull requests, history, ideas, examples, gh cli
type: help
---
# Finding ideas on GitHub

Part 1, chapter 9. Almost every extension ever written for this code base that still exists is on GitHub, and the
se7enxweb organization keeps a large share of them, original and forked. This chapter shows how to search there for
ideas and working code, and how to judge what you find.

## The se7enxweb organization

[github.com/se7enxweb](https://github.com/se7enxweb) holds the kernel, every extension the kernel requires, the
documentation sites, the free books and a large number of forks of historical extensions kept so they do not
disappear. When this page was written (October 2026) the organization listed more than a thousand repositories;
fewer than a hundred were original repositories of the organization, the rest forks.

| Kind | How to recognize it | Use it for |
|---|---|---|
| Maintained extension | not archived, recent commits, a release tag, a Packagist package | install and build on it |
| Kept fork | marked as a fork, often archived | read it for ideas; check its license; bring it up to date before use (Part 4) |
| Documentation and books | names like `doc.exponential.earth`, `Book-...` | learn |
| Distributions and skeletons | names like `exponential-platform-...` | see how complete installations are put together |

On a repository page, **Archived** means read-only: nobody will merge your fix there. A fork shows "forked from" and
the original owner; the original is where the history started.

## Search in the browser

GitHub's search understands qualifiers. Some useful searches (type them into the search field on github.com):

| You look for | Search |
|---|---|
| Repositories of the organization about tags | `org:se7enxweb tags` |
| Datatypes written for this code base | `"extends eZDataType" language:PHP` |
| Template operators | `"eZTemplateOperatorArray" language:PHP` |
| Workflow event types | `"extends eZWorkflowEventType" language:PHP` |
| Cronjob parts | `"[CronjobPart-" path:settings` |
| ezjscore ajax functions | `"extends ezjscServerFunctions"` |
| Uses of one kernel method | `"eZContentFunctions::createAndPublishObject"` |

Code search needs you to be signed in. A search for a kernel class name is the fastest way to find real code that uses
it, which is often clearer than any description.

## Search from the terminal

The GitHub command line tool `gh` does the same from a terminal:

```bash
gh search repos --owner se7enxweb tags --limit 20
gh search code "extends eZDataType" --language php --limit 20
gh repo view se7enxweb/eztags
gh api repos/se7enxweb/eztags/releases --jq '.[0].tag_name'
```

`gh repo view` prints the description and the README; the last command prints the newest release tag.

## Read before you take

When you find something promising, read these before you copy a single line:

1. **The license.** The repository page shows the detected license in its sidebar; the `LICENSE` file is what
   counts. No license means no permission to copy. See
   [Reading licenses before you reuse anything](reading-licenses-before-reusing.md).
2. **The history.** `Commits` on the repository page, or after a clone:

   ```bash
   git clone https://github.com/se7enxweb/<repo>.git
   git -C <repo> log --format='%ad %s' --date=short | head -20
   ```

   A long, steady history with clear messages is a good sign. A single "initial import" commit means the history
   was lost; the code may still be fine, but you know less about it.
3. **The issues and pull requests.** Open issues show known problems; merged pull requests show that someone
   reviews contributions.
4. **What version it was written for.** `composer.json` `require.php`, the README, `extension.xml`. Code written
   for PHP 5 needs the changes in Part 4 before it runs on Exponential 6.

## Is it alive?

| Sign | Alive | Needs adoption |
|---|---|---|
| Last commit | this year or last | many years ago |
| Archived | no | yes |
| Release tags | yes, following a pattern | none |
| Packagist package | yes, versions match the tags | none, or stuck on an old version |
| Issues | answered | unanswered for years |
| `composer.json` PHP requirement | 8.x | 5.x or missing |

An extension that "needs adoption" is not useless; it is an opportunity.
Adopting an abandoned extension explains how
to take one over respectfully.

## Fork, or just read?

For ideas, reading is enough. When you want to change the code and share the change, fork it on GitHub and work on a
branch; Forking on GitHub, step by step shows how, and
Contributing back with pull requests how
to offer the change to its author.

## Exercise

1. Search `org:se7enxweb datatype` and open three results.
2. For each, note: license, date of the last commit, archived or not, Packagist package or not.
3. Pick the one that looks most alive and read its datatype class. You will meet the same methods in
   [Datatypes](../part-3-extension-developer/datatypes.md).

## Related pages

- Previous: [The se7enxweb extension catalogue](the-se7enxweb-extension-catalogue.md)
- Next: [Finding ideas on Packagist](finding-ideas-on-packagist.md)
- [Develop with Git](../../develop-with-git.md)
- [GitHub Docs: searching code](https://docs.github.com/en/search-github/searching-on-github/searching-code)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
