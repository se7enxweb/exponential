---
title: Improving the help as a student
audience: student
category: learning
order: 42
summary: How learners improve the Exponential help - why learners find the best documentation bugs, the quick way with Edit on GitHub, the thorough way with a clone, the front matter and style rules of help pages, what the GNU FDL means for your contribution, and what to avoid.
keywords: documentation, help, improve, Edit on GitHub, pull request, front matter, style, Markdown, GFDL, free documentation, typo, outdated step, learner
type: help
---
# Improving the help as a student

You read the help more carefully than anyone: you follow every step, and you notice at once when one does not work.
That makes learners the best testers of documentation. This page shows how to turn what you notice into an
improvement for everyone.

## Contents

1. [What learners notice](#what-learners-notice)
2. [The quick way: Edit on GitHub](#the-quick-way-edit-on-github)
3. [The thorough way: a clone](#the-thorough-way-a-clone)
4. [How a help page is written](#how-a-help-page-is-written)
5. [Your rights and the license](#your-rights-and-the-license)
6. [What to avoid](#what-to-avoid)

## What learners notice

| You notice | Contribution |
|---|---|
| A typo or a broken sentence | A one-line fix |
| A step that does not match the software | A corrected step, with the release you tested |
| A word nobody explained | A link to the glossary, or a short explanation |
| An "Expected" output that differs | The real output, with the command |
| A missing link to the next page | The link |
| A page you could not find | An issue suggesting where it should be linked |

## The quick way: Edit on GitHub

Every help page has an **Edit on GitHub** link. Open it, change the text in the browser, and propose the change as a
pull request ([Contributing documentation](../supporters/contributing-documentation.md#fix-a-page-in-two-minutes)).
Typos, outdated steps and unclear sentences are all welcome. Write a commit subject such as
`Fixed: The languages help names the Apply button`.

## The thorough way: a clone

For several pages or a new page, work in your clone of the kernel: the help is the set of Markdown files in `doc/`,
help pages are under `doc/eng-us/help/` ([Improving the help](../advocates/improving-the-help.md),
[Using Git as a student](using-git-as-a-student.md)). Make a branch, edit, look at the result in your study site's
help, commit and open a pull request.

The installation checks the links and images of the help pages with a console command:

```bash
php bin/php/console exp:help check --below=help/ --allow-root-user
```

Run it before you send a pull request that adds or moves links (`bin/php/exphelp.php`).

## How a help page is written

Every help page starts with front matter ([Contributing documentation](../supporters/contributing-documentation.md#write-a-new-help-page)):

```text
---
title: <Title>
audience: editor, admin
category: editing
order: 50
summary: <One or two sentences: who it is for and what it answers.>
keywords: <comma separated>
type: help
---
```

The audience and category values the help understands are configured in `settings/exphelp.ini`. The style rules:

- American English; "Exponential" for the product; the old name only for history.
- Every fact with its source: a file, a page, or a command with its expected output.
- Steps that work on a current installation; say which version you tested.
- No secrets, no private host names, no personal data in examples.
- Tables and checklists where they help; short sentences.

## Your rights and the license

The documentation is free documentation under the GNU Free Documentation License 1.3 or later. Your contribution is
published under the same license, so others may copy, translate and improve it
([Free documentation](../free-documentation.md)). You may also use the help in your own notes and course work, if you
keep the notices ([Free documentation](../free-documentation.md#what-you-can-do)).

## What to avoid

- Changing facts you have not checked. If you are not sure, open an issue instead of a pull request.
- Copying text from sources whose license does not allow it.
- Large rewrites as a first contribution; start small.
- Describing features that do not exist. Every claim in the help is checked against code, settings or a real
  installation.

## Related pages

- [Contributing documentation](../supporters/contributing-documentation.md)
- [Your first contribution, step by step](your-first-contribution-step-by-step.md)
- [Improving the help](../advocates/improving-the-help.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
