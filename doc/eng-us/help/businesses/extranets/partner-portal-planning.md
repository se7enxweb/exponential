---
title: Partner portal planning
audience: businesses, project-manager, partner
category: business-solutions
order: 10
summary: The decisions an organization makes before building a partner, customer, dealer or supplier portal on Exponential - audiences and tiers, what is shared, how accounts are approved, where the portal lives, and who owns it.
keywords: partner portal, extranet planning, customer portal, dealer portal, supplier portal, tiers, audiences, B2B, account approval, data separation, ownership, requirements
type: help
---
# Partner portal planning

This page is for managers who own partner or customer relationships and want a portal for them. It turns the
business questions into decisions, and shows which Exponential feature answers each decision. Work through it before
anyone builds a page; a portal whose structure follows the contracts is much easier to keep safe.

## Contents

1. [Who the portal is for](#who-the-portal-is-for)
2. [What is shared with whom](#what-is-shared-with-whom)
3. [Decisions and the features behind them](#decisions-and-the-features-behind-them)
4. [Choosing the separation model](#choosing-the-separation-model)
5. [Ownership on your side](#ownership-on-your-side)
6. [Data and contracts](#data-and-contracts)
7. [Planning worksheet](#planning-worksheet)
8. [Related pages](#related-pages)

## Who the portal is for

List the outside audiences and how many organizations each has. Typical audiences:

| Audience | Usually needs |
|---|---|
| Resellers and partners | price lists, sales material, product news, deal registration forms |
| Customers | their own documents, manuals, support articles, order history |
| Dealers | ordering with their own prices, stock information, marketing material |
| Suppliers | specifications, quality documents, a way to upload certificates |

Within one audience there are often **tiers** (Gold and Silver partners, or regions). Tiers decide much of the
structure.

## What is shared with whom

Draw a simple matrix before deciding anything technical:

| Content | All partners | Gold tier | One partner only | Public |
|---|---|---|---|---|
| Product news | yes | yes | | some |
| Price list | | yes | | |
| Contract documents | | | yes | |
| Marketing material | yes | yes | | |

Every "one partner only" column means an area per partner organization
([Customer and partner areas](customer-and-partner-areas.md)). Every "Public" column means the public site may show
it too, which Exponential handles by giving the same object to both siteaccesses
([Separate hosts and siteaccesses](separate-hosts-and-siteaccesses.md)).

## Decisions and the features behind them

| Decision | Options | Feature | Page |
|---|---|---|---|
| How accounts are created | self-registration with approval; created by your staff | `user/register`, approval mail links, Approve workflow, Unactivated users | [Registration and account approval](registration-and-account-approval.md) |
| Where the portal lives | its own host name; a path of the main site | siteaccess matching | [Separate hosts and siteaccesses](separate-hosts-and-siteaccesses.md) |
| How partners are separated | sections per tier; subtrees per partner; object states | role limitations | [Customer and partner areas](customer-and-partner-areas.md) |
| Whether partners upload | read-only; upload into their own folder | `content/create` limited by class and subtree | [Sharing files with partners](sharing-files-with-partners.md) |
| Whether partners order | no; built-in shop with group discounts | shop, discount groups | [Dealer and supplier portals](dealer-and-supplier-portals.md) |
| Sign-in strength | password; two-step sign-in | `sevenx_authentication_2fa` | [Two-factor and sign-in](../intranets/two-factor-and-sign-in.md) |
| How access is checked | quarterly review | role pages, Check user accounts, audit trail | [Extranet permissions review](extranet-permissions-review.md) |

## Choosing the separation model

| Model | Fits when | Effort as partners grow |
|---|---|---|
| One section per tier | content is the same for every partner of a tier | low: new partners join a group |
| One subtree per partner organization | each partner has its own documents | one folder and one role assignment per partner |
| Object states (for example Internal, Partners, Public) | the same items change audience over time | low, once the state group exists |
| Separate installation per partner | strict contractual separation | highest: one site to run per partner |

Most portals combine the first two: tier sections for shared material, a subtree per partner for private documents.

## Ownership on your side

- A **portal owner** who decides structure and approves new partners.
- **Relationship managers** who request and confirm accounts for their partners.
- **Editors** for each content area.
- **An approver** for registrations, with a deputy.
- **IT or the host** for sign-in, backups and updates.

Write down who approves a new account and who closes it when a contract ends. Closing accounts on time is the most
frequently forgotten task of any portal ([Extranet permissions review](extranet-permissions-review.md)).

## Data and contracts

A portal holds personal data of people at other organizations: names, e-mail addresses, sign-in records. Plan what is
collected and how long it is kept with your data protection contact ([GDPR basics](../security/gdpr-basics.md),
[Personal data in Exponential](../security/personal-data-in-exponential.md)). This page is not legal advice.

## Planning worksheet

- [ ] Audiences and the number of organizations in each.
- [ ] Tiers and what each tier receives.
- [ ] The sharing matrix.
- [ ] Separation model chosen.
- [ ] Account creation and approval process, with names.
- [ ] Host name for the portal.
- [ ] Upload and ordering needs.
- [ ] Sign-in requirements.
- [ ] Review rhythm and the person responsible.
- [ ] Data retention for accounts of ended partnerships.

## Related pages

- [Extranets and partner portals](README.md)
- [Planning an Exponential project](../../leadership/planning-an-exponential-project.md)
- [Risk management](../../leadership/risk-management.md)
- [Requirements checklist](../templates/requirements-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
