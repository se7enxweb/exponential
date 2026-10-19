---
title: Go-live checklist
audience: businesses, project-manager, admin, website-creator
category: business-solutions
order: 50
summary: A copy-ready go-live checklist for the business side of launching an Exponential site - content, legal pages, accounts, store and forms, security, backups, monitoring, communication and the decision to launch - linking the technical go-live lists for hosts and website creators.
keywords: go-live, launch, checklist, launch day, cut-over, DNS, HTTPS, backups, monitoring, accounts, two-step sign-in, privacy notice, store, forms, acceptance, rollback
type: help
---
# Go-live checklist

This page is for owners and project managers who must say "yes, we launch". It covers what the business must
confirm; the technical lists for whoever does the work are the
[Launch checklist for website creators](../../creators/website-creators/launch-checklist.md), the
[New customer site go-live checklist for hosts](../../hosting/checklists/new-customer-site-go-live.md) and the launch
checklist in [Project templates](../../leadership/project-templates.md#launch-checklist). Ask for those to be
completed and signed; then use this one.

## Contents

1. [How to use it](#how-to-use-it)
2. [The checklist](#the-checklist)
3. [Launch day plan](#launch-day-plan)
4. [The first two weeks](#the-first-two-weeks)

## How to use it

- Go through it with the people responsible, one week and again one day before launch.
- Every line has an owner and evidence (a ticket, a screenshot, a test result).
- A "no" on a line marked **(blocker)** means the launch moves.

## The checklist

```text
GO-LIVE CHECKLIST - <site>   Planned launch: <date, time, time zone>   Decision by: <name>

CONTENT                                                        Owner     Done  Evidence
[ ] All planned content published and proofread                ______    [ ]   ______
[ ] Contact details, opening hours, prices correct             ______    [ ]   ______
[ ] Old URLs redirect to new ones (if replacing a site)        ______    [ ]   ______
[ ] Search finds the main pages                                ______    [ ]   ______

LEGAL AND DATA (ask your adviser)
[ ] Privacy notice and imprint published (blocker)             ______    [ ]   ______
[ ] Forms ask only for needed data and link the notice         ______    [ ]   ______
[ ] Mail footer: organization name and postal address set      ______    [ ]   ______
[ ] Store terms, VAT and shipping texts checked                ______    [ ]   ______
[ ] Retention periods decided and set                          ______    [ ]   ______

ACCOUNTS AND ACCESS
[ ] Every person has a personal account; no shared admin       ______    [ ]   ______
[ ] Initial administrator password changed (blocker)           ______    [ ]   ______
[ ] Two-step sign-in on for administrators                     ______    [ ]   ______
[ ] Test and agency accounts removed or disabled               ______    [ ]   ______

STORE AND FORMS (if used)
[ ] Test order paid, confirmed, received by mail (blocker)     ______    [ ]   ______
[ ] Payment provider switched from test to live mode           ______    [ ]   ______
[ ] Each form tested; submissions reach the right people       ______    [ ]   ______
[ ] Newsletter sign-up and unsubscribe tested                  ______    [ ]   ______

SAFETY NET
[ ] HTTPS on every address; certificate renews by itself       ______    [ ]   ______
[ ] Backup taken and a restore tested (blocker)                ______    [ ]   ______
[ ] External monitoring set up; alerts reach a person          ______    [ ]   ______
[ ] Audit alert recipients set                                 ______    [ ]   ______
[ ] Rollback plan written (old site or backup ready)           ______    [ ]   ______

PEOPLE AND COMMUNICATION
[ ] Editors trained; handover document delivered               ______    [ ]   ______
[ ] Support contact and response times known to staff         ______    [ ]   ______
[ ] Announcement to customers, members or staff prepared       ______    [ ]   ______

DECISION
Technical checklists signed by: ______________   on: ________
Go / no-go: ________   Decided by: ______________   on: ________
```

What each line rests on: privacy and mail footer in [Consent and forms](../security/consent-and-forms.md); accounts
in [User accounts and passwords](../security/user-accounts-and-passwords.md); the store in the
[Store launch checklist](../stores/store-launch-checklist.md); backups in
[Backups that restore](../reliability/backups-that-restore.md); monitoring in
[Health checks and uptime](../../hosting/operations/health-checks-and-uptime.md); audit alerts in
[The audit trail](../../../features/6.0/audit-trail.md#try-it-in-two-minutes).

## Launch day plan

```text
LAUNCH DAY - <date>
<time>  Content freeze on the old site (if any)
<time>  Final backup of old and new site
<time>  Maintenance mode on, if the switch needs it
<time>  Switch (DNS or server change) - by <name>
<time>  Smoke test: home page, sign-in, a form, a test order - by <name>
<time>  Maintenance mode off
<time>  Announcement sent
<time>  Check logs and monitoring - by <name>
Rollback decision point: <time>; rollback decided by <name>
```

Maintenance mode shows visitors a friendly page while work is going on
([Maintenance mode](../../../features/6.0/maintenance-mode.md)). Choose a quiet hour
([Cost of downtime worksheet](../costs/cost-of-downtime-worksheet.md)).

## The first two weeks

- [ ] Look at not-found errors (`404.log` in the log viewer) and add redirects where needed
      ([Log viewer](../../../features/6.0/log-viewer.md)).
- [ ] Read error references reported by visitors ([Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md)).
- [ ] Collect editors' questions and update the handover document.
- [ ] Confirm the first scheduled backup ran and the first cron jobs did their work.
- [ ] Hold a short review; note what to improve for the next launch.

## Related pages

- [Monthly operations checklist](monthly-operations-checklist.md)
- [Store launch checklist](../stores/store-launch-checklist.md)
- [Intranet rollout checklist](../intranets/intranet-rollout-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
