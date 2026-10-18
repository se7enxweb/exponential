---
title: Which tracker for which report
audience: advocate
category: advocacy
order: 74
summary: A decision table that sends every kind of report to the right place - the Exponential issue tracker, the Velocity tracker, an extension's repository, the community tracker, the private security address, the forums - with examples and the Report an error page.
keywords: tracker, issues, bug report, where to report, decision table, Exponential issues, Velocity issues, extension repository, community tracker, security, forums, Report an error
type: help
---
# Which tracker for which report

This page is for advocates who help people report things. A report in the right place gets handled; a report in the
wrong place waits or gets lost. The list of trackers comes from
[When something goes wrong](../when-something-goes-wrong.md#filing-a-bug-in-exponential).

## Contents

1. [The decision table](#the-decision-table)
2. [Examples](#examples)
3. [The Report an error page](#the-report-an-error-page)
4. [When you are not sure](#when-you-are-not-sure)

## The decision table

| It is about | Where | Public? |
|---|---|---|
| A security problem: seeing or changing what one should not | security@se7enx.com or GitHub's private vulnerability reporting ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)) | **No, private** |
| An error in the kernel, the administration, a bundled extension or the documentation | [Exponential issues](https://github.com/se7enxweb/exponential/issues) | Yes |
| An error of the Velocity server: starting, workers, its response cache, HTTPS | [Velocity issues](https://github.com/se7enxweb/exponential-velocity/issues) | Yes |
| An error in an extension with its own repository | That repository's issues; the **Report an error** page lists them | Yes |
| The project, the website, the ecosystem, anything that is not a software error | [Community issues](https://github.com/se7enxweb/exponential-community/issues) | Yes |
| A "how do I ...?" question | [Forums](https://share.exponential.earth/forums) or [Telegram](https://t.me/exponentialcms) | Yes |
| A problem of one site's setup (its own templates, server, hosting) | The site's administrator or provider first | Depends |
| A wrong or unclear help page | A pull request with **Edit on GitHub**, or an issue in Exponential | Yes |

## Examples

| Report | Goes to |
|---|---|
| "The upload page fails with a ZIP file containing a folder." | Exponential issues |
| "exp:velocity start hangs on my machine." | Velocity issues |
| "The newsletter extension sends a wrong date." | That extension's repository |
| "The project website has a broken link." | Community issues |
| "I can see other users' drafts by changing the URL." | **Private**: SECURITY.md |
| "How do I add a field to articles?" | Forums |
| "The help page on languages names a button that no longer exists." | Pull request, or Exponential issues |
| "Our site is slow since our host moved us." | The site's administrator or host |

## The Report an error page

In the administration, the help's **Report an error** page (`exphelp/report`) guides a report: it chooses the
tracker, fills in a summary of the installation and opens a new issue with a prepared title and text. The summary
leaves out passwords, keys and server paths ([When something goes wrong](../when-something-goes-wrong.md#filing-a-bug-in-exponential)).
Recommend it to everyone who can sign in.

## When you are not sure

- If it might be a security problem, treat it as one: private first.
- If you cannot tell whether it is the kernel or an extension, file it in Exponential issues and say so; maintainers
  can move it.
- If it is not a software error, the community tracker is the right place.
- Search first: add to an existing issue rather than opening a duplicate.

## Related pages

- [Reporting bugs well](reporting-bugs-well.md)
- [Security reports stay private](security-reports-stay-private.md)
- [GitHub for advocates](github-for-advocates.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
