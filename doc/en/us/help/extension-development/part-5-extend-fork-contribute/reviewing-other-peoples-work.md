---
title: Reviewing other people's work
audience: developer, designer, app-creator
category: extension-development
order: 120
summary: How to review a pull request to an Exponential extension usefully - what to check in order (purpose, safety, behavior, tests, style), how to try it locally, and how to write comments that help.
keywords: code review, review, pull request, checklist, security, CSP, tests, comments, feedback, maintainer
type: help
---
# Reviewing other people's work

Part 5, chapter 12. Reviewing is a contribution of its own, and one every project is short of. You do not have to be
a maintainer to review: a careful comment from any user who tried the change helps the maintainer decide. This
chapter gives an order to review in, a checklist for Exponential extensions, and a way to write comments.

## Review in this order

Start with the questions whose answer can make the rest unnecessary.

1. **Purpose.** Does the description say what problem this solves? Is it a problem the project wants solved?
2. **Safety.** Could it damage content, leak data, or let someone do what they should not?
3. **Behavior.** Does it do what it says, in the cases that matter?
4. **Tests.** Is there a test that fails without the change?
5. **Fit.** Does it follow the project's conventions?
6. **Details.** Names, comments, typos.

Comments about step 6 on a change that fails step 2 waste everybody's time.

## A checklist for Exponential extensions

| Area | Check |
|---|---|
| Input | values from the request go through `eZHTTPTool`; integers are cast; strings in SQL go through `escapeString()` ([Security in extensions](../part-3-extension-developer/security-in-extensions.md)) |
| Output | template values are washed (`\|wash`), attributes too |
| Access | new views declare policy functions; menu entries check `has_access_to` |
| Forms | state-changing views require POST and the form token |
| Content | writes that belong together run in a transaction |
| Browser | no inline script, no `on...=` attribute, no `style=""` in templates |
| Admin pages | shared `.a4-*` components, tokens only, works in dark mode |
| PHP | runs on the oldest PHP the extension supports; no new deprecations |
| Settings | new settings have defaults and a comment |
| Metadata | version bumps only in release commits, in all files |
| Texts | interface texts are translation strings with the extension's context |
| Commits | the subject format, one subject per commit ([Commit messages](commit-messages.md)) |

The kernel has its own code review checklist for administration templates in
[Styling the administration](../../../bc/6.0/admin4-styling.md).

## Try it

Reading a diff finds some problems; running it finds others. To try a pull request locally:

```bash
git fetch origin pull/123/head:pr-123
git switch pr-123
```

(`123` is the pull request's number; on GitHub, `origin` here is the original repository.) Install it in a
development site, clear the caches, and use the feature, including the error cases: wrong input, a user without the
policy, a missing setting.

## Writing comments

- **Say what and why.** "This value is printed without `wash`, so a title with `<` breaks the page" helps. "Escape
  this" makes the author guess.
- **Separate must from may.** Mark optional suggestions as such ("Optional: ..."). The author then knows what blocks
  the merge.
- **Suggest, do not command.** GitHub's "suggestion" blocks let the author accept a small change with one click.
- **Praise what is good.** A specific "the test for the empty case is a good idea" teaches as much as a criticism.
- **Ask when you do not understand.** "Why is this needed?" is a fine review comment; the answer often belongs in a
  code comment.
- **Keep it about the code.** Never about the person.

## Approving

Approve when you would be comfortable maintaining the code yourself. If you only reviewed part of it, say which part.
If you are not a maintainer, a comment "Tried it on PHP 8.0 and 8.5 with the steps in the description: works" is a
valuable review without any approval.

## Related pages

- Previous: [Commit messages the project can build a changelog from](commit-messages.md)
- Next: [Adopting an abandoned extension](adopting-an-abandoned-extension.md)
- [Pull request etiquette](pull-request-etiquette.md)
- [About pull request reviews](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/reviewing-changes-in-pull-requests/about-pull-request-reviews) on GitHub Docs
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
