---
title: Handing over to a customer or a new provider
audience: reseller, partner, hosting
category: reselling
order: 370
summary: How a reseller hands a site over at the end of a project or a contract - a complete export (database, var/storage, settings), accounts, domain, custom code with the GPL duties, documentation, the order of steps, deletion afterwards - and why a clean exit is part of a good offer.
keywords: handover, hand over, exit, offboarding, export, database dump, var/storage, settings/override, settings/siteaccess, domain, accounts, custom code, GPL, deletion, new provider
type: help
---
# Handing over to a customer or a new provider

This page is for technical leads and account managers at resellers. Handovers happen at the end of a build, when a
customer takes the site in-house, or when they move to another provider. Doing them well is part of your reputation,
and the GPL means a customer can always leave; a clean exit is what lets them trust you enough to stay.

## Contents

1. [When a handover happens](#when-a-handover-happens)
2. [What a complete handover contains](#what-a-complete-handover-contains)
3. [The order of steps](#the-order-of-steps)
4. [Custom code and the GPL](#custom-code-and-the-gpl)
5. [After the handover](#after-the-handover)
6. [Handover checklist](#handover-checklist)

## When a handover happens

| Situation | Who receives |
|---|---|
| End of a build project | The customer, or the provider that will run the site |
| The customer takes the site in-house | The customer's own team |
| The customer changes provider | The new provider |
| You stop offering a service | The customer and a provider you recommend |

## What a complete handover contains

A complete site is the database and the files:

| Item | Detail | Source |
|---|---|---|
| Database | A dump made with the engine's own tool | [Database backups per engine](../../hosting/operations/database-backups-per-engine.md) |
| Files | `var/storage` (each site's storage, where separate) | [What to back up](../../hosting/operations/backups-what-to-back-up.md) |
| Settings | `settings/override` and `settings/siteaccess`, transferred privately | same |
| Code | The Exponential version and extensions with versions; custom extensions and designs as agreed | [Keeping customers current](keeping-customers-current.md) |
| Accounts | The list of accounts your staff held, so they can be disabled | [Customer offboarding](../../hosting/checklists/customer-offboarding.md) |
| Domain and DNS | In the customer's name, or transferred on the agreed date | |
| Documentation | Site inventory, runbooks you wrote, the customer manual | [Documentation you can give your customers](documentation-for-your-customers.md) |

## The order of steps

1. Agree the date, who runs the site until then, and how secrets are transferred (never by plain e-mail).
2. Announce a short content freeze, or use maintenance mode for the final export
   ([Maintenance mode](../../../guides/maintenance-mode.md)).
3. Make the final export and check it restores on a test machine.
4. Transfer it privately; the receiver confirms the site works at the new place.
5. Have the receiver change the database password and every secret in `settings/override`.
6. Disable your staff accounts in the customer's administration, and confirm in writing.
7. Close or transfer hosting and panel accounts on the agreed date.

## Custom code and the GPL

- Code you hand over is distributed: the receiver gets the GPL rights for the GPL parts, with the license, the
  copyright notices and the source ([Distribution obligations checklist](../responsibilities/distribution-obligations-checklist.md)).
- What else you hand over (your own extensions, design files) follows your contract
  ([Contracts with customers](contracts-with-customers.md#exit-and-handover)).
- Check the licenses of fonts and stock images: they often cannot be transferred.

## After the handover

- Delete the customer's data from your servers and backups on the agreed schedule, and confirm it in writing
  ([Data protection for resellers](../responsibilities/data-protection.md)).
- Keep only what you must keep by law (invoices, for example).
- Ask for feedback: why did the customer leave? Record it ([Renewals and churn](renewals-and-churn.md)).
- Stay polite: customers come back, and they talk.

## Handover checklist

- [ ] Date and plan agreed in writing.
- [ ] Final export: database dump, `var/storage`, `settings/override`, `settings/siteaccess`.
- [ ] Restore of the export tested.
- [ ] Code and versions listed; custom code handed over as agreed, with notices and license.
- [ ] Secrets transferred privately; receiver changed passwords.
- [ ] Domain and DNS in the customer's name.
- [ ] Your accounts disabled and confirmed.
- [ ] Documentation handed over.
- [ ] Data deleted on schedule; deletion confirmed.

## Related pages

- [Handover checklist (for site owners)](../../customers/partners-and-hosts/handover-checklist.md)
- [Customer offboarding](../../hosting/checklists/customer-offboarding.md)
- [Moving a site between hosts](../../hosting/migrations/moving-a-site-between-hosts.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
