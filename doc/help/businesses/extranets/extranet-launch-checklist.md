---
title: Extranet launch checklist
audience: businesses, project-manager, admin
category: business-solutions
order: 80
summary: Everything to tick before the first partner, customer, dealer or supplier is invited to an Exponential extranet - address and HTTPS, separation tests, registration and approval, sign-in, files, ordering, mail, operations and the first review date.
keywords: extranet launch, partner portal go-live, checklist, separation test, registration test, approval test, two-factor, downloads, discount groups, mail delivery, backups, review date
type: help
---
# Extranet launch checklist

This page is for the project lead who opens a portal to outside organizations. A mistake on an extranet is seen by
people outside the company, so test more than you would for an intranet. Copy the list, assign a name to each line,
and invite a first partner only when every block is ticked.

## Contents

1. [Address and basics](#address-and-basics)
2. [Separation](#separation)
3. [Accounts](#accounts)
4. [Sign-in](#sign-in)
5. [Files](#files)
6. [Ordering, if offered](#ordering-if-offered)
7. [Mail and notifications](#mail-and-notifications)
8. [Operations](#operations)
9. [Pilot partner](#pilot-partner)
10. [After launch](#after-launch)
11. [Related pages](#related-pages)

## Address and basics

- [ ] The portal's host name answers with HTTPS and the right siteaccess
  ([Separate hosts and siteaccesses](separate-hosts-and-siteaccesses.md)).
- [ ] `SiteURL` is the address partners use; mail links are built from it.
- [ ] The portal siteaccess has `RequireUserLogin=true`; the public site has `false`.
- [ ] The default administrator password has been changed
  ([Default administrator password](../../default-administrator-password.md)).
- [ ] The administration has its own host name or network limits
  ([Restricting the administration](../../hosting/security/restricting-the-administration.md)).
- [ ] The portal design shows who is signed in and a sign-out link.

## Separation

- [ ] Test users exist for at least two partner organizations and two tiers.
- [ ] Each test user sees its own area and not the other's, in pages, menus, search and downloads
  ([Customer and partner areas, Testing the separation](customer-and-partner-areas.md#testing-the-separation)).
- [ ] Logged out, the public host shows nothing of the portal; the portal host shows only the login page and the
  pages in `AnonymousAccessList[]`.
- [ ] No partner role shows *Full access* or *Can change roles*
  ([Roles and policies, 1](../../../guides/roles-and-policies.md#1-read-the-role-list)).
- [ ] `user/login` in each role is limited to the right siteaccesses.

## Accounts

- [ ] The way accounts are created is decided and documented
  ([Registration and account approval](registration-and-account-approval.md)).
- [ ] If registration is open: `DefaultUserPlacement` points to an applicants group without rights.
- [ ] The "New user registered" mail reaches a monitored mailbox; approve and reject links tested.
- [ ] If accounts are held until approved: one approved and one rejected in a test, with the workflow cronjob
  running.
- [ ] CAPTCHA on the registration form ([Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md)).
- [ ] The registration form and the privacy notice say what data is kept and why
  (Consent and forms).

## Sign-in

- [ ] Password rules set ([Two-factor and sign-in](../intranets/two-factor-and-sign-in.md#password-rules)).
- [ ] `MaxNumberOfFailedLogin` set to a value other than `0`.
- [ ] Two-step sign-in decided: opt-in, required for all, or required for sensitive tiers
  ([Two-step sign-in](../../../guides/two-factor-login.md)).
- [ ] Password reset tested from the login page
  ([Reset a user's password](../../../features/6.0/reset-user-password.md)).
- [ ] Support contact named for partners who are locked out.

## Files

- [ ] Every file area in the right section or partner subtree
  ([Sharing files with partners](sharing-files-with-partners.md)).
- [ ] A protected download tried as a user without access: refused.
- [ ] Partner upload roles limited to the File class and the partner's folder; no remove.
- [ ] Staff follow the upload folders.

## Ordering, if offered

- [ ] `shop/buy` only in the dealer role ([Dealer and supplier portals](dealer-and-supplier-portals.md)).
- [ ] Discount groups and rules match the price policy.
- [ ] VAT rules and currencies checked.
- [ ] A full test order placed and processed in the Store tab
  ([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)).

## Mail and notifications

- [ ] Sender address and delivery tested to an outside mailbox
  ([Mail delivery](../../hosting/operations/mail-delivery.md)).
- [ ] The e-mail preferences footer has the organization name and postal address
  ([E-mail preferences: setting them up](../../../guides/mail-preferences-administrator.md)).
- [ ] Notification run visible on **Setup > Notification**
  ([Notifications: the administrator's guide](../../../guides/notifications-administrator.md)).

## Operations

- [ ] Cronjobs scheduled, including the `frequent` group ([Cronjobs](../../../guides/cronjobs.md)).
- [ ] Backups scheduled and one restore tested ([Backups that restore](../reliability/backups-that-restore.md)).
- [ ] Audit trail running: `./console exp:audit status`
  ([The audit trail](../../../features/6.0/audit-trail.md#try-it-in-two-minutes)).
- [ ] Monitoring of the portal address ([Health checks and monitoring](../reliability/health-checks-and-monitoring.md)).
- [ ] Update plan agreed ([Upgrades without surprises](../reliability/upgrades-without-surprises.md)).

## Pilot partner

- [ ] One friendly partner invited first.
- [ ] The partner signs in, finds three named documents, uploads one file and (if offered) places a test order.
- [ ] Feedback collected and fixed before more partners are invited.

## After launch

- [ ] First permissions review dated ([Extranet permissions review](extranet-permissions-review.md)).
- [ ] Procedure for ended partnerships agreed with the relationship managers.
- [ ] Unactivated registrations cleared regularly
  ([Roles and policies, 7](../../../guides/roles-and-policies.md#7-clean-up-unactivated-users)).

## Related pages

- [Extranets and partner portals](README.md)
- [Intranet rollout checklist](../intranets/intranet-rollout-checklist.md)
- Go-live checklist template
- [Security review checklist for hosts](../../hosting/checklists/security-review.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
