---
title: Extranet FAQ
audience: businesses, project-manager, partner
category: business-solutions
order: 90
summary: Short, sourced answers to the questions managers ask about partner, customer, dealer and supplier portals on Exponential - separation between partners, registration and approval, own addresses, files, ordering, sign-in and reviews.
keywords: extranet FAQ, partner portal questions, customer portal, dealer portal, supplier portal, separation, approval, registration, separate host, files, discount groups, two-factor, single sign-on, permissions review
type: help
---
# Extranet FAQ

This page is for managers and project leads who need quick answers about portals for people outside the company. Each
answer is short and links to the page with the details.

## Contents

1. [Separation and access](#separation-and-access)
2. [Accounts](#accounts)
3. [Address and design](#address-and-design)
4. [Files and ordering](#files-and-ordering)
5. [Security and records](#security-and-records)
6. [Related pages](#related-pages)

## Separation and access

**Can one partner see another partner's documents?**
Not when each organization's folder is readable only through a role assigned to that organization's group with a
Subtree limitation, and tested ([Customer and partner areas](customer-and-partner-areas.md)).

**Do we need a separate role for every partner?**
No. One role, assigned to each organization's group with its own Subtree limitation, serves every organization
([Roles and policies, 5](../../../guides/roles-and-policies.md#5-assign-the-role)).

**Can Gold partners see more than Silver partners?**
Yes, with a section per tier and a reading role per tier
([Customer and partner areas, Tiers with sections](customer-and-partner-areas.md#tiers-with-sections)).

**Can an item move from internal to partners to public without being moved?**
Yes, with an object state group used in the reading policies
([Customer and partner areas, Audiences that change](customer-and-partner-areas.md#audiences-that-change-with-object-states)).

**Does search show partners things they may not read?**
No. Search results follow permissions ([Intranet search](../intranets/intranet-search.md)).

## Accounts

**Can partners register themselves?**
Yes, if the Anonymous role has `user/register`. New installations do not give it by default
([Roles and policies, 8](../../../guides/roles-and-policies.md#8-decide-who-may-register-and-who-may-read-node-data)).

**Can we approve each registration before it works?**
Yes. The administrator's mail carries signed **Approve** and **Reject** links; with an Approve workflow the account
waits until someone decides ([Registration and account approval](registration-and-account-approval.md)).

**Are the approve links in the mail safe if a mail scanner opens them?**
Yes. Opening a link changes nothing; the approver must be signed in with the right policy and confirm by pressing a
button. Links are signed, expire after 7 days by default and approve or reject only once
([Signed links, Security](../../../features/6.0/registration-admin-links.md#security)).

**How do we close accounts when a contract ends?**
Remove the organization's assignments, then disable or deactivate and move its accounts
([Extranet permissions review, When a partnership ends](extranet-permissions-review.md#when-a-partnership-ends)).

**Can partners sign in with their own company accounts?**
Single sign-on with a partner's identity provider is not built in. The kernel has LDAP and text file login handlers and
an extension point for single sign-on handlers ([Directory sign-in and LDAP](../intranets/directory-sign-in-and-ldap.md)).
The `sevenx_authentication_2fa` extension offers social login with providers such as Google, off by default
([Two-factor and sign-in, Social login](../intranets/two-factor-and-sign-in.md#social-login)).

## Address and design

**Can the portal have its own address?**
Yes. A siteaccess matched by host, such as `partners.example.com`, in the same installation as the public site
([Separate hosts and siteaccesses](separate-hosts-and-siteaccesses.md)).

**Can partners use the portal but not sign in on the public site, or the other way round?**
Yes. `user/login` can be limited by siteaccess in each role (`kernel/user/module.php`).

**Can partners land on their own page after signing in?**
Yes, with `LoginRedirectionUriAttribute[group]`, a text attribute of the user group that holds the address
([Customer and partner areas](customer-and-partner-areas.md#sending-partners-to-their-area-after-sign-in)).

**Can the portal look different from the public site?**
Yes. Each siteaccess has its own design, and templates can differ by section
([Separate hosts and siteaccesses, Its own look](separate-hosts-and-siteaccesses.md#its-own-look)).

## Files and ordering

**Is a forwarded download link a risk?**
No. Every download checks that the person may read the file
([Sharing files with partners](sharing-files-with-partners.md)).

**Can partners upload files?**
Yes, into their own folder, with a role limited to the File class and their subtree
([Sharing files with partners, Letting partners upload](sharing-files-with-partners.md#letting-partners-upload)).

**Can dealers order at their own prices?**
They can order through the built-in shop, and discount groups give user groups a percentage off products or classes.
Fixed price lists per dealer are not a documented feature ([Dealer and supplier portals](dealer-and-supplier-portals.md)).

**Does the shop manage stock or invoices?**
No. Stock, accounting invoices, shipping labels and marketplaces are extensions or integrations
([Running a shop, What the shop does not do](../../creators/website-creators/shop-overview.md#what-the-shop-does-not-do)).

## Security and records

**Can we require two-step sign-in for partners?**
Yes, with `sevenx_authentication_2fa` and `Enforce2FA=enabled`
([Two-step sign-in](../../../guides/two-factor-login.md)).

**Can we prove who had access and what changed?**
The audit trail records sign-ins, refusals, role assignments and content changes in a tamper-evident log
([The audit trail](../../../features/6.0/audit-trail.md)).

**How often should we review access?**
At a fixed rhythm you agree with your security officer; the review routine is in
[Extranet permissions review](extranet-permissions-review.md).

## Related pages

- [Extranets and partner portals](README.md)
- [Intranet FAQ](../intranets/intranet-faq.md)
- [Member site FAQ](../members/member-site-faq.md)
- Businesses FAQ
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
