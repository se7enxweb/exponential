---
title: Intranet rollout checklist
audience: businesses, project-manager, admin
category: business-solutions
order: 100
summary: A checklist for rolling out an Exponential intranet, from the first test installation through permissions, sign-in, content migration, notifications and operations to the day staff are invited and the first review after launch.
keywords: intranet rollout, launch checklist, go-live, intranet launch, permissions test, sign-in test, content migration, training, backups, cronjobs, access review, pilot group
type: help
---
# Intranet rollout checklist

This page is for the project lead who takes an intranet from plan to daily use. Each block lists what to check and
links to the page that explains it. Copy the list into your project tool, assign a name to each line, and do not
invite all staff until every block is ticked.

## Contents

1. [Phase 1: foundations](#phase-1-foundations)
2. [Phase 2: structure and permissions](#phase-2-structure-and-permissions)
3. [Phase 3: sign-in](#phase-3-sign-in)
4. [Phase 4: content](#phase-4-content)
5. [Phase 5: communication and approval](#phase-5-communication-and-approval)
6. [Phase 6: operations](#phase-6-operations)
7. [Phase 7: pilot group](#phase-7-pilot-group)
8. [Launch day](#launch-day)
9. [After launch](#after-launch)
10. [Related pages](#related-pages)

## Phase 1: foundations

- [ ] Purpose, owner and scope written down ([Intranet planning](intranet-planning.md)).
- [ ] Hosting chosen and the installation running (Choosing hosting).
- [ ] Host name and HTTPS certificate in place
  ([Going live: hosts and HTTPS](../../creators/website-creators/going-live-hosts-https.md)).
- [ ] The intranet siteaccess has `RequireUserLogin=true`
  ([What visitors may see, Siteaccess settings](../../creators/website-creators/visitor-permissions.md#siteaccess-settings)).
- [ ] Network restriction decided and configured in the web server if wanted
  ([Network restrictions](../../hosting/security/restricting-the-administration.md#network-restrictions)).
- [ ] The default administrator password changed
  ([Default administrator password](../../default-administrator-password.md)).

## Phase 2: structure and permissions

- [ ] One section per area, each with an identifier ([Sections](../../../guides/sections.md#5-creating-a-section)).
- [ ] User groups per department ([Departments, roles and sections](departments-roles-and-sections.md)).
- [ ] Staff role: `content/read` for the staff sections, `user/login` for the intranet siteaccess,
  `notification/use`.
- [ ] Editor role assigned per department with a Section limitation.
- [ ] No role except Administrator shows *Full access* or *Can change roles*
  ([Roles and policies, 1](../../../guides/roles-and-policies.md#1-read-the-role-list)).
- [ ] If the intranet shares an installation with a public site: the public Anonymous role cannot read any intranet
  section; tested in a private browser window
  ([Testing as a visitor](../../creators/website-creators/visitor-permissions.md#testing-as-a-visitor)).

## Phase 3: sign-in

- [ ] Account source decided: local, LDAP, or both ([Directory sign-in and LDAP](directory-sign-in-and-ldap.md)).
- [ ] Password rules set in `[PasswordSettings]` ([Two-factor and sign-in](two-factor-and-sign-in.md#password-rules)).
- [ ] `MaxNumberOfFailedLogin` set to a value other than `0`.
- [ ] Two-step sign-in active; administrators and editors enrolled first
  ([Two-step sign-in](../../../guides/two-factor-login.md)).
- [ ] Help desk knows how to reset a password and how to handle a lost second step
  ([Two-step sign-in](../../../guides/two-factor-login.md#what-a-user-sees),
  [Reset a user's password](../../../features/6.0/reset-user-password.md)).
- [ ] Open registration closed: `user/register` not given to Anonymous on the intranet
  ([Roles and policies, 8](../../../guides/roles-and-policies.md#8-decide-who-may-register-and-who-may-read-node-data)).

## Phase 4: content

- [ ] Document inventory done; owners named ([Documents and file libraries](documents-and-file-libraries.md)).
- [ ] Documents migrated; duplicates removed.
- [ ] Protected downloads tested as a user without access.
- [ ] Search tested as members of two departments ([Intranet search](intranet-search.md)).
- [ ] Search index rebuilt after the migration.
- [ ] Front page with latest news ([News and announcements](news-and-announcements.md)).
- [ ] Editors trained ([Handing over to editors](../../creators/website-creators/handing-over-to-editors.md)).

## Phase 5: communication and approval

- [ ] Approval workflow configured where needed, one test approved and one denied
  ([Approval workflows](approval-workflows.md)).
- [ ] Approvers have ticked the Approval collaboration notification.
- [ ] Notification run tested; the status page shows a recent run
  ([Notifications for staff](notifications-for-staff.md)).
- [ ] Mail sender and delivery tested ([Mail delivery](../../hosting/operations/mail-delivery.md)).

## Phase 6: operations

- [ ] Cronjobs scheduled, including the `frequent` group (workflow, notification, audit)
  ([Cronjobs](../../../guides/cronjobs.md)).
- [ ] Backups scheduled and one restore tested (Backups that restore).
- [ ] Audit trail status checked: `./console exp:audit status`
  ([The audit trail](../../../features/6.0/audit-trail.md#try-it-in-two-minutes)).
- [ ] Update plan agreed (Upgrades without surprises).
- [ ] Monitoring of the intranet address in place
  (Health checks and monitoring).

## Phase 7: pilot group

- [ ] A small pilot group with people from different departments invited first.
- [ ] Each pilot user signs in, sets up the second step, finds three named documents and follows one folder.
- [ ] Feedback collected; search statistics reviewed if enabled.
- [ ] Problems fixed before wider rollout.

## Launch day

- [ ] Accounts for all staff exist or are created at first directory sign-in.
- [ ] Invitation mail with the address, how to sign in and how to get help.
- [ ] Help desk staffed for the first days.
- [ ] Old shared drive or old intranet set to read-only, with a date to switch it off.

## After launch

- [ ] Review after 30 days: what staff searched for and did not find, which areas are not updated.
- [ ] First access review after 90 days (Access reviews).
- [ ] Unused accounts checked on **Users > Check user accounts**
  ([Check user accounts](../../../features/6.0/user-hygiene.md)).
- [ ] Monthly operations routine in place
  (Monthly operations checklist).

## Related pages

- [Intranets](README.md)
- Go-live checklist template
- [Quality gates and acceptance](../../leadership/quality-gates-and-acceptance.md)
- [Launch checklist for website creators](../../creators/website-creators/launch-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
