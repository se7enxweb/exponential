---
title: Escalation contacts template
audience: customer, admin, project-manager
category: customer-success
order: 12
summary: A template for the contact list an owner's team of an Exponential site needs on a bad day - internal roles, the host, the partner, the project's channels - with severity, hours and the next step when nobody answers, plus rules for keeping it current and private.
keywords: escalation, contacts, template, on call, phone list, host, partner, severity, incident, security contact
type: help
---
# Escalation contacts template

**Template.** Fill it in on a quiet day, keep it outside the site (printed, and in a shared place that does not
depend on the site), and check it every quarter. On a bad day it replaces a search with a phone call.

Write roles and business contact details only. Do not write passwords or anyone's private phone number into it, and
keep it readable only by the people who need it.

## Internal roles

The incident plan for small teams names three roles: a lead who decides, a fixer, and a communicator
([Incident response for small teams](../../businesses/reliability/incident-response-for-small-teams.md)).

| Role | Name | Business phone | E-mail | Deputy |
|---|---|---|---|---|
| Owner (decides on cost, rollback, public statements) | [name] | [phone] | [e-mail] | [name] |
| Incident lead | [name] | [phone] | [e-mail] | [name] |
| Site administrator (accounts, roles, maintenance mode) | [name] | [phone] | [e-mail] | [name] |
| Communicator (customers, staff) | [name] | [phone] | [e-mail] | [name] |
| Data protection contact | [name] | [phone] | [e-mail] | [name] |

## Providers

| Provider | For | Channel and hours | Severity 1 outside hours | Account or contract number | Next step if no answer within [agreed time] |
|---|---|---|---|---|---|
| Host | Server, PHP, database, backups, certificates, mail | [channel], [hours, time zone] | [phone of the on-call role] | [number] | [manager role, phone] |
| Partner | Templates, designs, extensions, site settings | [channel], [hours, time zone] | [phone or "none"] | [number] | [manager role, phone] |
| Domain registrar and DNS | Domain, DNS records | [channel] | [phone or "none"] | [account] | [step] |
| Other: [payment provider, mail service, CDN] | [for] | [channel] | [phone] | [number] | [step] |

Fill in the hours and response times from your agreements ([Support agreement template](support-agreement-template.md)).

## The project

| For | Where | Notes |
|---|---|---|
| A security weakness in Exponential | security@se7enx.com (`SECURITY.md`) | Private; first answer "within one business day" |
| A bug in Exponential | <https://github.com/se7enxweb/exponential/issues> | Public; usually filed by your partner ([GitHub issues](../healthy/github-issues.md)) |
| A bug in Exponential Velocity | <https://github.com/se7enxweb/exponential-velocity/issues> | Public |
| The project and community | <https://github.com/se7enxweb/exponential-community/issues> | Public |
| Questions | The community forums and chats ([Getting support](../../project/getting-support.md)) | No promised answer time |
| Paid support | 7x ([Working with 7x and the project](working-with-7x-and-the-project.md)) | Under a contract |

## Facts to have at hand

| Fact | Value |
|---|---|
| Site addresses | [addresses] |
| Administration address | [address] |
| Exponential version, last checked | [version], [date] |
| Engine and database | [Velocity / Apache or nginx with PHP-FPM / other], [database] |
| Where backups are and who can restore | [place], [role] |
| Where the break-glass procedure is written | [place, never the password] |

## Keeping it current

- [ ] Checked every quarter, and whenever someone joins or leaves or a provider changes.
- [ ] Each number tested once (a short call is enough).
- [ ] Matches [Who does what](who-does-what.md).
- [ ] A copy kept where it is reachable when the site and your mail are down.

## Related pages

- [When to call whom](when-to-call-whom.md)
- [When the site is down](../healthy/when-the-site-is-down.md)
- [Incident notes template](../healthy/incident-notes-template.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
