---
title: Who does what
audience: customer, partner, hosting
category: customer-success
order: 2
summary: A responsibility table for an Exponential site shared between the owner's team, the host, the partner and the project - updates, backups, security, content, custom code, monitoring and incidents - to agree once and put into every contract.
keywords: responsibilities, RACI, roles, host, partner, owner, project, hosting support, application support, line of responsibility
type: help
---
# Who does what

"Most friction in hosting support comes from an unclear line" ([Support tiers](../../hosting/business/support-tiers.md)).
This page gives you a table to agree once with your host and partner, and to put into each contract. Change it to
fit your arrangement; what matters is that every line has one name.

## The four parties

| Party | Typically |
|---|---|
| Your team | Owns the site, the content and the decisions; manages editors and accounts |
| The host | Runs the server, PHP, the engine, the database server, backups and certificates |
| The partner or developer | Builds and changes templates, designs, extensions and the content model |
| The project and 7x | Make Exponential itself, fix bugs in the software, publish releases ([Working with 7x and the project](working-with-7x-and-the-project.md)) |

## The line between hosting and application

From [Support tiers](../../hosting/business/support-tiers.md):

| Hosting support | Application support |
|---|---|
| The server, PHP, the engine, the database server | Content classes, templates, design |
| Backups and restores | The site's own extensions and custom code |
| TLS certificates | Editorial questions ("how do I publish?") |
| Mail delivery from the server | Data fixes inside the content |
| Applying Exponential updates, where agreed | Upgrades that need code changes |

Some hosts offer both under one contract; many offer only the left column.

## A responsibility table to fill in

| Task | Your team | Host | Partner | Notes |
|---|---|---|---|---|
| Deciding when to upgrade | ✓ | | | |
| Applying Exponential updates | | | | Host or partner, as agreed |
| PHP and operating system updates | | ✓ | | |
| Database and file backups | | ✓ | | |
| Restore tests and their record | | ✓ | | Result reported to you ([Restore test](../healthy/restore-test.md)) |
| Monitoring and alerts | | ✓ | | |
| TLS certificates and DNS | | | | Whoever holds the domain |
| Security settings of the site | | | ✓ | |
| Administrator accounts and roles | ✓ | | | |
| Templates, designs, extensions | | | ✓ | |
| Content, translations, media | ✓ | | | |
| Reporting bugs in Exponential upstream | | | ✓ | ([GitHub issues](../healthy/github-issues.md)) |
| Private security reports | | | | Whoever finds it ([Private security reports](../healthy/private-security-reports.md)) |
| Incident lead | ✓ | | | Decides; providers fix |
| Training editors | | | ✓ | |

The ticks are an example of a common split: replace them with your own. If one company is both host and partner,
merge the two columns but keep the lines.

## Project managers' version

The project managers' section has a RACI matrix (responsible, accountable, consulted, informed) for a whole project
([Project templates](../../leadership/project-templates.md)). Use it when a build or migration is running; the table
above is for the years of running the site afterwards.

## Keep it current

- [ ] The table is attached to each contract.
- [ ] Every line has one name, not "both".
- [ ] It is reviewed when a provider changes and once a year
      ([Reviewing your provider each year](reviewing-your-provider-each-year.md)).
- [ ] Your [escalation contacts](escalation-contacts-template.md) match it.

## Related pages

- [When to call whom](when-to-call-whom.md)
- [Contracts and SLAs](contracts-and-slas.md)
- [Security basics](../healthy/security-basics.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
