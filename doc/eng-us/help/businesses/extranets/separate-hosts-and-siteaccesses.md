---
title: Separate hosts and siteaccesses
audience: businesses, admin, hosting
category: business-solutions
order: 50
summary: How a partner portal gets its own address next to the public website in one Exponential installation, or runs in an installation of its own - siteaccesses, host matching, sign-in only on the portal, its own design, HTTPS, and the trade-offs of each shape.
keywords: siteaccess, separate host, partners host name, host matching, HostMatchMapItems, HostMatchMethod, RequireUserLogin, user/login SiteAccess limitation, RootNode, SiteDesign, multisite, separate installation, extranet address, HTTPS
type: help
---
# Separate hosts and siteaccesses

This page is for managers and administrators who decide where the partner portal lives: at its own address such as
`partners.example.com`, beside the public website in the same installation, or in an installation of its own. It
explains the Exponential concept that makes this possible, the siteaccess, and the trade-offs.

## Contents

1. [The siteaccess in one paragraph](#the-siteaccess-in-one-paragraph)
2. [Three shapes](#three-shapes)
3. [A portal beside the public site](#a-portal-beside-the-public-site)
4. [Sign-in only on the portal](#sign-in-only-on-the-portal)
5. [Its own look](#its-own-look)
6. [Its own host name and HTTPS](#its-own-host-name-and-https)
7. [Test and staging hosts](#test-and-staging-hosts)
8. [Trade-offs](#trade-offs)
9. [Checklist](#checklist)
10. [Related pages](#related-pages)

## The siteaccess in one paragraph

A **siteaccess** is one way into an installation: a name, its settings in `settings/siteaccess/<name>/`, and a rule
for which requests it answers, for example by host name
([10.2 Siteaccesses and site addresses](../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).
The public website, the administration and a partner portal can be three siteaccesses of one installation, each with
its own address, design, start page and sign-in rules.

## Three shapes

| Shape | What it means | Fits when |
|---|---|---|
| Portal siteaccess in the same installation | one installation, a `partners` siteaccess on its own host | content is partly shared with the public site; one team runs both |
| Portal folder of the public site | a members area under the public site's address | small portals, little separation needed ([Member registration](../members/member-registration.md)) |
| Separate installation | its own code, database and users | contracts require strict separation, or a different update rhythm |

## A portal beside the public site

The steps for a second site are in
[Setting up several sites in one installation](../../creators/website-creators/multisite-setup.md#step-by-step-a-second-site).
For a portal they come down to:

1. A folder *Partner portal* in the content tree; its node becomes the portal's root (`content.ini [NodeSettings]
   RootNode`) and start page (`IndexPage`).
2. A siteaccess `partners` with `SiteURL=partners.example.com`.
3. Host matching in `settings/override/site.ini.append.php`: `MatchOrder=host;uri`, `HostMatchType=map`,
   `HostMatchMapItems[]=partners.example.com;partners`.
4. The host name in DNS, the web server and the certificate.

Shared items, such as product news, can be shown on both sites: an object can have locations in both trees, or be
related from a page in the portal ([Relations and reuse](../../creators/website-creators/relations-and-reuse.md)).

## Sign-in only on the portal

Two settings make the portal private while the public site stays open:

- In the portal siteaccess, `[SiteAccessSettings] RequireUserLogin=true`: every visitor who is not signed in goes to
  the login page; `AnonymousAccessList[]` keeps registration, activation and password reset reachable
  ([What visitors may see, Siteaccess settings](../../creators/website-creators/visitor-permissions.md#siteaccess-settings)).
  The public siteaccess sets `false`.
- The policy `user/login` can be limited by **SiteAccess** (`kernel/user/module.php`). Give partner roles `user/login`
  for `partners`, staff roles for the administration, and members of the public site only the public siteaccess. A
  role page then reads, for example, *May log in, on the siteaccesses partners*
  ([Roles and policies, 2](../../../guides/roles-and-policies.md#2-read-one-role)).

> **Warning**
> Portal content must sit in sections that the public Anonymous role does not read. The default Anonymous role reads
> Section Standard only ([Sections, 2](../../../guides/sections.md#2-the-sections-of-a-new-installation)); keep it
> that way and test logged out on the public host.

## Its own look

Each siteaccess has its own `SiteDesign` and can fall back to other designs
([Designs and the fallback chain](../../creators/website-creators/designs-and-the-fallback-chain.md)). The portal can
look like the brand but plainer, with a menu of the partner areas and a sign-out link. Templates can also differ by
section, for example a different look for the Gold tier
([Sections, 11.1](../../../guides/sections.md#111-override-a-template-for-one-section)).

## Its own host name and HTTPS

The portal's host name goes into DNS, the web server or Velocity, and the certificate; `SiteURL` must be the address
people use, because mail links are built from it ([Going live: hosts, HTTPS and siteaccesses](../../creators/website-creators/going-live-hosts-https.md)).
Registration mails point into the administration; if its address is not reachable from the mail, set
`RegistrationAdminBaseURL` ([Registration and account approval](registration-and-account-approval.md#the-administrators-mail-with-signed-links)).

The administration itself can have its own host name and network limits
([Restricting the administration](../../hosting/security/restricting-the-administration.md#its-own-host-name)).

## Test and staging hosts

`site.ini [SiteAccessSettings] HostMatchMethod` decides how the host map compares hosts: `strict` (default),
`start`, `end` or `part`. With `start`, `partners.example.com.test.local` reaches the portal's siteaccess without
entries of its own. `start` and `part` also accept hosts you did not mean, so use them only where the web server
answers for your own hosts ([Siteaccess by host prefix](../../../features/6.0/siteaccess-host-and-language-matching.md#the-host-map-by-the-start-of-a-host)).

## Trade-offs

| Same installation | Separate installation |
|---|---|
| one kernel and one set of extensions to update and secure | two to update and secure |
| shared content, media and search | content copied or synchronized |
| one user base: staff accounts work in both | separate users: nothing leaks across |
| one backup restores both | independent restores |
| a mistake in roles can expose content | separation by construction |

The same table, for brands, is in [Setting up several sites, Trade-offs](../../creators/website-creators/multisite-setup.md#trade-offs).
Logs and caches can be kept per site ([Logs, INI cache and expiry timestamps per site](../../../features/6.0/multi-site-log-and-cache-paths.md)).

## Checklist

- [ ] Shape chosen and written into the plan.
- [ ] Portal siteaccess with `RequireUserLogin=true`; public siteaccess with `false`.
- [ ] `user/login` limited by siteaccess in every role.
- [ ] Portal sections absent from the Anonymous role; tested logged out on the public host.
- [ ] Host name in DNS, web server and certificate; `SiteURL` correct.
- [ ] Registration mail links tested.

## Related pages

- [Setting up several sites in one installation](../../creators/website-creators/multisite-setup.md)
- [Siteaccess by host prefix, and the redirect to the language](../../../features/6.0/siteaccess-host-and-language-matching.md)
- [One installation, many sites](../multisite/one-installation-many-sites.md)
- [Customer and partner areas](customer-and-partner-areas.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
