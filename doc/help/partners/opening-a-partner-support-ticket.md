---
title: Opening a partner support ticket
audience: partner
category: partnership
order: 330
summary: How partners open support tickets that get solved quickly - what to check first, what information to include, how to describe the environment safely without secrets or customer data, a ticket template and what to do after the answer.
keywords: support ticket, GitHub issue, ticket, template, environment, logs, reproduce, debug, partner support
type: help
---
# Opening a partner support ticket

This page is for developers and administrators in partner companies. Partner support runs on "GitHub Issue Tickets"
counted per week or month by level (<https://exponential.earth/partner/benefits>). Which repository to use for
partner tickets, and whether it is private, is told to you at activation; if not, ask the partner team.

## Before you open a ticket

- [ ] Search the help (the `/` key in the help focuses the search) and the forums.
- [ ] Search open and closed issues of <https://github.com/se7enxweb/exponential/issues>.
- [ ] Reproduce the problem on a clean, current installation if you can.
- [ ] Read [When something goes wrong](../when-something-goes-wrong.md); it explains logs and debug output.
- [ ] Decide: is it a bug in Exponential (public issue), a question about your project (partner ticket), or a
      security problem (private e-mail to security@se7enx.com, never a ticket)?

## What to include

| Item | Example | Why |
|---|---|---|
| Exponential version | 6.0.14, or `main` at commit `abc1234` | Most answers depend on it |
| PHP version and engine | PHP 8.3, Velocity / PHP-FPM / FrankenPHP | Behavior differs |
| Database | SQLite, PostgreSQL, MariaDB ... | Drivers differ |
| Extensions involved | Names and versions | Many problems are in extensions |
| What you did | Exact steps | So it can be reproduced |
| What you expected | One sentence | Defines success |
| What happened | Error text, log lines | The evidence |
| What you tried | Cache cleared, autoloads regenerated ... | Avoids repeated advice |
| Urgency | Production down / degraded / question | Prioritization |

## Keep secrets and customer data out

Before you paste anything, remove passwords, keys, database host names, customer names, e-mail addresses and
private URLs. Replace them with placeholders such as `<customer-site>`. The help's bug-report helper builds a safe
environment summary for exactly this reason ([When something goes wrong](../when-something-goes-wrong.md)).

## Template

```text
Title: <short description> on Exponential <version>

Partner: <company> (<level>)          Urgency: <down / degraded / question>
Environment: Exponential <version>, PHP <x.y> (<engine>), <database>, extensions: <list>

Steps to reproduce:
1. ...
2. ...
Expected: ...
Actual: ...  (error text / log lines, secrets removed)
Tried so far: ...
```

## After the answer

- Confirm when it is solved and how, so the ticket helps others.
- If the cause was a bug in Exponential, make sure a public issue or pull request exists
  ([Filing bugs well](filing-bugs-as-a-partner.md)).
- Log solved bug reports as activity ([Logging your partner activity](logging-partner-activity.md)).

## Related pages

- [Support levels and response times](support-levels-and-response-times.md)
- [Escalations and security issues](escalations-and-security-issues.md)
- [When something goes wrong](../when-something-goes-wrong.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
