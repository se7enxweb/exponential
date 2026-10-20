---
title: Upgrade day checklist
audience: customer, editor, project-manager
category: customer-success
order: 5
summary: A checklist for the site owner and the editorial team for the days before, the day of and the day after an upgrade of an Exponential site - announcements, the backup question, the pages to test, and what to write down.
keywords: upgrade day, checklist, maintenance window, testing, smoke test, announcement, rollback, editors
type: help
---
# Upgrade day checklist

This checklist is for the site owner and the editors on the day an upgrade happens. The technical steps belong to
whoever upgrades ([Upgrading](../../../guides/upgrading.md), [Update with Git](../../update-with-git.md)); these are
the steps on your side, so that the upgrade is planned, checked and recorded.

## A week before

- [ ] The date, start time and expected duration are agreed with the person upgrading.
- [ ] You know the old and the new version ([Release line and support](release-line-and-support.md)).
- [ ] You received a summary of what changes for editors and visitors.
- [ ] The upgrade was tried on a copy of the site, or you accepted the risk of not doing so.
- [ ] No campaign, newsletter send or sale is planned for the window
      ([Peak traffic planning](../../businesses/reliability/peak-traffic-planning.md)).
- [ ] You decided what happens if it fails: go back to the backup, or fix forward.

## A day before

- [ ] Editors know the window. Content published during it may need to be checked again.
- [ ] Visitors are told, if the site will show a maintenance page; the page can carry a message and the expected
      end ([Maintenance mode and planned work](maintenance-mode-and-planned-work.md)).
- [ ] Your test list is ready (below).
- [ ] Your [escalation contacts](../partners-and-hosts/escalation-contacts-template.md) are current.

## Your test list

Keep it short, five to ten items, and always the same, so a difference stands out. Example items:

| Check | Good result |
|---|---|
| The front page | Loads, with images |
| A deep page, such as an article | Loads, with its images and links |
| Site search | Finds a known page ([Searching](../../searching.md)) |
| Sign in to the administration | The dashboard opens ([Logging into the admin](../../logging-into-the-admin.md)) |
| Edit and publish a test item | It appears on the site ([Publishing and versions](../../publishing-and-versions.md)) |
| Upload an image | It is stored and shown ([Media and uploads](../../media-and-uploads.md)) |
| A form, such as contact or sign-up | It submits |
| The shop, if you have one | A product page loads, the basket works |
| Each language or site you run | Its front page loads |

Replace the examples with the pages that matter to your business.

## On the day

- [ ] The person upgrading confirms the backup of database and files was taken right before
      ([Backups](backups.md)).
- [ ] During the window, report nothing about pages that show the maintenance page; that is expected.
- [ ] When you are told the site is back, run your test list.
- [ ] Report any difference at once, with the page address, the time and any error reference
      ([Reporting problems](reporting-problems.md)).

## The day after

- [ ] You received the evidence: the new version, the upgrade check result, and anything left to do
      ([Upgrades with your provider](upgrades-with-your-provider.md)).
- [ ] Editors report anything that behaves differently.
- [ ] The upgrade is noted in your records: date, old and new version, who did it, problems met.

> **Note**
> If something is wrong after an upgrade, the technical guide has a table of symptoms and what to do, including
> "restore the backup of step 1" when a database update failed halfway
> ([Upgrading, if something goes wrong](../../../guides/upgrading.md)). Leave the decision to roll back to the owner,
> and the doing to the person who upgraded.

## Related pages

- [How updates work](how-updates-work.md)
- [Maintenance mode and planned work](maintenance-mode-and-planned-work.md)
- [Upgrades without surprises](../../businesses/reliability/upgrades-without-surprises.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
