---
title: Member site checklist
audience: businesses, project-manager, website-creator
category: business-solutions
order: 60
summary: A checklist for launching and running a member site on Exponential - basics, registration, members-only content, communications, events, privacy, operations, the first members and the routine afterwards.
keywords: member site checklist, launch, go-live, registration test, members area test, newsletter rehearsal, events, privacy, backups, cronjobs, member list hygiene, routine
type: help
---
# Member site checklist

This page is for whoever launches a member site, often a small team or volunteers. Each block lists what to check and
links to the page that explains it. Assign a name to each line; tick it only when it has been tried, not when it has
been configured.

## Contents

1. [Basics](#basics)
2. [Registration](#registration)
3. [Members-only content](#members-only-content)
4. [Communications](#communications)
5. [Events](#events)
6. [Privacy](#privacy)
7. [Operations](#operations)
8. [The first members](#the-first-members)
9. [The routine afterwards](#the-routine-afterwards)
10. [Related pages](#related-pages)

## Basics

- [ ] Purpose, levels and owner decided ([Member site planning](member-site-planning.md)).
- [ ] The site answers with HTTPS on its address
  ([Going live: hosts, HTTPS and siteaccesses](../../creators/website-creators/going-live-hosts-https.md)).
- [ ] The default administrator password changed
  ([Default administrator password](../../default-administrator-password.md)).
- [ ] The public siteaccess has `RequireUserLogin=false`
  ([What visitors may see, Common mistakes](../../creators/website-creators/visitor-permissions.md#common-mistakes)).
- [ ] Accessibility checked on the main pages
  ([Testing accessibility](../../creators/website-creators/testing-accessibility.md)).

## Registration

- [ ] Registration open or closed; `user/register` given to Anonymous only if open
  ([Member registration](member-registration.md#opening-registration)).
- [ ] `DefaultUserPlacement` points to the Members (or Applicants) group in the public siteaccess.
- [ ] The Member role has only what members need.
- [ ] A test registration: form, activation mail, activation link, sign-in.
- [ ] If the organization approves members: the "New user registered" mail arrives and its links work
  ([Registration and account approval](../extranets/registration-and-account-approval.md)).
- [ ] CAPTCHA on the user class ([Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md)).
- [ ] Forgotten password tested ([Reset a user's password](../../../features/6.0/reset-user-password.md)).

## Members-only content

- [ ] Members section assigned to the members folder ([Members-only content](members-only-content.md)).
- [ ] Logged out: members pages ask for sign-in; member documents refused; search finds no member text.
- [ ] Signed in as a member: pages and documents open; higher levels refused.
- [ ] Public teasers for key member content.
- [ ] Forum moderators named, if forums are offered.

## Communications

- [ ] Newsletter lists per audience ([Member communications](member-communications.md)).
- [ ] A full newsletter rehearsal with file transport done
  ([cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md#rehearse-the-whole-thing-without-sending-a-single-mail)).
- [ ] Newsletter cron parts scheduled.
- [ ] E-mail preferences footer: organization name and postal address filled in.
- [ ] Sender domain records checked by the host ([Mail delivery](../../hosting/operations/mail-delivery.md)).
- [ ] `notification/use` in the Member role if notifications are offered.

## Events

- [ ] Event calendars in the right sections ([Events and calendars](events-and-calendars.md)).
- [ ] Sign-up form tested end to end, including the mail to the organizer.
- [ ] Upcoming events visible on the front page.

## Privacy

- [ ] Registration and sign-up forms say what is collected and why ([Consent and forms](../security/consent-and-forms.md)).
- [ ] Only needed fields on the user class.
- [ ] Search statistics left off, or switched on with a reason
  ([Site search, Search statistics](../../creators/website-creators/site-search.md#search-statistics)).
- [ ] Retention for unconfirmed and inactive accounts agreed
  ([Data retention and deletion](../security/data-retention-and-deletion.md)).
- [ ] Privacy notice published ([GDPR basics](../security/gdpr-basics.md)).

## Operations

- [ ] Cronjobs scheduled, including the `frequent` group ([Cronjobs](../../../guides/cronjobs.md)).
- [ ] Backups scheduled and one restore tried ([Backups that restore](../reliability/backups-that-restore.md)).
- [ ] Update plan and a contact for problems ([Upgrades without surprises](../reliability/upgrades-without-surprises.md)).
- [ ] Two-step sign-in for administrators and editors ([Two-step sign-in](../../../guides/two-factor-login.md)).
- [ ] `MaxNumberOfFailedLogin` set ([Two-factor and sign-in](../intranets/two-factor-and-sign-in.md#locking-an-account-after-failed-sign-ins)).

## The first members

- [ ] The board or a small group registers first and reports problems.
- [ ] An announcement to all members with the address and how to join.
- [ ] Someone answers questions in the first weeks.

## The routine afterwards

| Rhythm | Task | Page |
|---|---|---|
| Weekly | approve or clean up registrations | [Roles and policies, 7](../../../guides/roles-and-policies.md#7-clean-up-unactivated-users) |
| Monthly | newsletter; check bounces and the blacklist | [Newsletter lists and subscriptions](../../creators/website-creators/newsletter-lists-and-subscriptions.md) |
| Quarterly | check inactive and duplicate accounts | [Check user accounts](../../../features/6.0/user-hygiene.md) |
| Yearly | review roles, sections and the privacy notice | [Access reviews](../security/access-reviews.md) |

Adapt the rhythm to the size of your organization.

## Related pages

- [Member sites](README.md)
- [Small group checklist](../associations/small-group-checklist.md)
- [Launch checklist for website creators](../../creators/website-creators/launch-checklist.md)
- [Go-live checklist template](../templates/go-live-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
