---
title: Reporting problems
audience: customer, editor, admin
category: customer-success
order: 17
summary: How a site owner's team reports a problem with an Exponential site so it gets fixed - one contact, the facts to collect, where each kind of problem goes (your administrator, host, partner, the public trackers or the private security address), and what never to send.
keywords: report a problem, bug, support, error reference, severity, host, partner, issue tracker, security, what to include
type: help
---
# Reporting problems

A problem reported well is half solved. This page sets up how your team reports problems: who collects them, what
facts to send, and where each kind of problem goes. The editor's step-by-step version is
[When something goes wrong](../../when-something-goes-wrong.md).

## First: is it an error?

Some pages only say that something is not allowed or not there, for example "Your account does not have the proper
privileges", or a grayed-out button. Those are questions of access, not bugs: ask your site administrator
([When something goes wrong](../../when-something-goes-wrong.md)).

## One contact for the team

Agree one person (and a deputy) who collects problems from editors and customers and passes them on. It avoids the
same problem being reported three times, and it gives your provider one person to answer.

## The facts to collect

| Fact | Example |
|---|---|
| The page address | Copied from the browser's address bar |
| The time, with time zone | "14:05 CET" |
| The error reference, if shown | `ERR-0123456789` |
| What was done, step by step | "Opened the article, pressed Edit, uploaded a ZIP file" |
| What was expected, and what happened | The exact message |
| Everyone, or one account? | "Only my account", "every editor" |
| Recent changes | An update, a new extension, a settings change |

This is the list a host's support staff are told to collect ([Support tiers](../../hosting/business/support-tiers.md));
sending it straight away saves a round trip. A screenshot helps, "as long as it shows no passwords or other people's
personal data" ([When something goes wrong](../../when-something-goes-wrong.md)).

## How urgent is it?

A common severity scale from the hosting section ([Support tiers](../../hosting/business/support-tiers.md)):

| Severity | Definition | Example |
|---|---|---|
| 1 Critical | The site is down or data is being lost or exposed | Every page returns an error; a suspected compromise |
| 2 High | An important function fails, no workaround | Editors cannot publish; sign-in fails |
| 3 Normal | A function fails, a workaround exists | One view renders wrongly |
| 4 Low | Questions, wishes | "Can we add a language?" |

Use the scale your contract defines, if it has one; otherwise agree this one.

## Where it goes

| Kind of problem | Goes to |
|---|---|
| Access, roles, "how do I" | Your site administrator, and the help in the administration |
| The server, the site is down, mail, certificates | Your host ([When to call whom](../partners-and-hosts/when-to-call-whom.md)) |
| Your templates, designs, custom extensions | Your partner or developer |
| A reproducible bug in Exponential itself | The public issue tracker, usually filed by your administrator or partner ([GitHub issues](github-issues.md), [The help report page](the-help-report-page.md)) |
| A security problem in the software | Privately to security@se7enx.com, never in public ([Private security reports](private-security-reports.md)) |
| Questions about the project | The community places ([Getting support](../../project/getting-support.md)) |
| Something that works as designed, just not as your team wants | Possibly a deliberate behavior other sites rely on ([Negative features](../../project/getting-support.md#negative-features)); a change needs a clear business case and several rounds of review, and an option is easier to win than a new default |

## Never send

"Never put passwords, keys, session cookies, database addresses, or the personal data of customers or users into a
public issue" ([When something goes wrong](../../when-something-goes-wrong.md)). The same rule is wise for tickets and
e-mail to your providers: a provider should never ask for a password; "Use a throwaway test account if you must
reproduce something signed in" ([Support tiers](../../hosting/business/support-tiers.md)).

## A report to copy

```text
Subject: [Severity 1-4] <short description>
Page:            <address>
Time:            <date, time, time zone>
Error reference: <ERR-... or none>
Who is affected: <everyone / one account / editors only>
Steps:           1. ...  2. ...  3. ...
Expected:        <what should happen>
Happened:        <what happened, exact message>
Recent changes:  <update, new extension, settings, none known>
Reported by:     <name, role, how to reach you>
```

## Related pages

- [When the site is down](when-the-site-is-down.md)
- [Logs for owners](logs-for-owners.md)
- [Logs and error references (for businesses)](../../businesses/reliability/logs-and-error-references.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
