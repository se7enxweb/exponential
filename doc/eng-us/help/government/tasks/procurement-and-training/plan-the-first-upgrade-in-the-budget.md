---
title: Plan the first upgrade in the budget
audience: government, project-manager, admin
category: public-sector
order: 210
summary: Step by step, plan and budget the first routine upgrade of a new Exponential site in a public body - the version you start from, the upgrade check, staging, hours from the supplier, a maintenance window and the record that makes the next upgrade cheaper.
keywords: first upgrade, upgrade budget, planning, upgrade check, staging, maintenance window, supplier hours, routine update, procurement task
type: help
---
# Plan the first upgrade in the budget

**Who:** the project manager with the site administrator. **When:** at go-live, for the first months of operation. The
first upgrade shows whether the site was built to be upgradable; plan it early. Background:
[Planning upgrades in the budget](../../procurement/lifecycle/planning-upgrades-in-the-budget.md).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Result](#result)
4. [Pitfalls](#pitfalls)

## Before you start

- The version delivered at acceptance, from **Setup > System information** ([System information](../../../../guides/system-information.md)).
- The supplier's hours per routine update, from the bid.

## Steps

1. **Record the starting point**: kernel version, extension versions, engine, PHP, database.
2. **Run Setup > Upgrade check** right after go-live: every file changed in place is a future upgrade cost; ask the
   supplier to move such changes into extensions or overrides now ([The upgrade check](../../../../guides/upgrade-check.md)).
3. **Check that staging exists** and matches production.
4. **Pick the trigger**: the next 6.0.x release, or the first security release, whichever comes first
   ([Supported versions](../../procurement/lifecycle/supported-versions.md)).
5. **Budget it**: the supplier's hours for the seven steps plus a regression test, your staff's time for testing, and
   a contingency ([Update with Git](../../../update-with-git.md#the-seven-steps)).
6. **Agree a maintenance window** and how it is announced ([Maintenance mode](../../../../guides/maintenance-mode.md)).
7. **Write the test list** you will use after every upgrade (home page, search, a form, sign-in, publishing).
8. **After the upgrade, record** hours, problems and fixes; use them to budget the following upgrades
   ([Upgrade readiness](../../checklists/upgrade-readiness.md)).

## Result

A first upgrade done on time and on budget, and real numbers for the yearly upgrade line.

## Pitfalls

- No budget line for upgrades in the first year.
- Accepting a site with changed kernel files.
- Postponing the first upgrade until several releases have piled up.

## Related pages

- [Procurement and training tasks](README.md)
- [Upgrade and maintenance costs](../../procurement/total-cost-of-ownership/upgrade-and-maintenance-costs.md)
- [Upgrading](../../../../guides/upgrading.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
