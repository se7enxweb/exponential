---
title: Writing good issues
audience: developer, designer, app-creator, user
category: extension-development
order: 100
summary: Report a bug or ask for a feature in a way a maintainer can act on - versions, steps to reproduce, expected and actual result, logs without secrets - with a template and what to leave out.
keywords: issue, bug report, feature request, GitHub issues, reproduce, versions, logs, template, security, secrets
type: help
---
# Writing good issues

Part 5, chapter 10. Not every contribution is code. A clear bug report is often worth more than a quick patch,
because it lets the maintainer fix the cause instead of a symptom. This chapter shows what a maintainer needs from an
issue, and what must never be in one.

## Before you write

1. **Search** the repository's issues, open and closed, for the error message or the feature.
2. **Update.** Check that the problem is still there with the latest release of the extension and of Exponential.
3. **Make it small.** Find the shortest way to make it happen. A problem you can show in four steps gets fixed.
4. **Is it a security problem?** Then do not open a public issue. Follow the project's security policy (the kernel's
   is in `SECURITY.md` at the root of its repository) and report it privately.
5. **Is it on purpose?** Read what the documentation actually promises. Behavior that is deliberate or open to
   interpretation is a [negative feature](../../project/getting-support.md#negative-features) other sites may depend
   on: say why it does not work for you, ask for an option rather than a new default, and expect a longer discussion.

## A bug report

```text
Title: Change content class entry does nothing under an enforced Content Security Policy

Versions
- Exponential 6.0.15, PHP 8.3, Apache with PHP-FPM, SQLite
- expchangeclass 1.0.8
- Design: admin4

Steps
1. Set site.ini [ContentSecurityPolicy] Mode=enforce for the admin siteaccess.
2. Right-click a node in the content tree.
3. Click "Change content class".

Expected
The class selection page opens.

Actual
Nothing happens. The browser console shows:
"Refused to execute inline event handler because it violates the following Content Security Policy directive ..."

Notes
Works with Mode=report-only. The entry has an onclick attribute in popupmenu/classcontextmenu.tpl.
```

Every part has a purpose:

| Part | Why |
|---|---|
| A title that names the symptom and the condition | the maintainer can tell it apart from twenty others |
| Versions | half of all bugs depend on one |
| Steps from a known state | the maintainer can see it with their own eyes |
| Expected and actual | sometimes the "bug" is intended, and this shows the disagreement |
| Error messages as text, copied | they can be searched; a screenshot cannot |
| What you already found | saves time; it is fine to be unsure |

## A feature request

Describe the problem before the solution:

```text
Title: A setting for the number of items on the overview page

Problem
Our editors manage 400 items. The overview shows 10 per page, so finding one takes many clicks.

Proposal
A setting myoldext.ini [OverviewSettings] Limit, default 10, read in modules/myoldext/overview.php.

Alternatives considered
Overriding the template does not help: the limit is in the PHP view.

I can send a pull request if this is welcome.
```

"I can send a pull request" is the most welcome sentence in a feature request.

## What never goes into an issue

Issues are public, often forever, and copied by search engines and archives:

- **No passwords, keys or tokens**, not even "old" ones. If you pasted one by mistake, change it at once; deleting the
  comment does not remove copies.
- **No personal data of your users or customers**: names, email addresses, IP addresses, content of their accounts.
  Replace them with made-up values (`user@example.invalid`).
- **No internal host names or addresses** of your servers.
- **No full configuration files.** Quote the few settings that matter.

Logs need the same care: cut them down to the lines about the problem, and replace anything personal.

## After you open it

- Answer questions. An issue whose reporter does not answer is usually closed after a while.
- If you find a workaround or the cause, add it.
- If it turns out to be your mistake, say so and close it. That is useful too: the next person who searches finds the
  answer.

## Related pages

- Previous: [Pull request etiquette](pull-request-etiquette.md)
- Next: [Commit messages the project can build a changelog from](commit-messages.md)
- [When something goes wrong](../../when-something-goes-wrong.md)
- [About issues](https://docs.github.com/en/issues/tracking-your-work-with-issues/about-issues) on GitHub Docs
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
