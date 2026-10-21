---
title: Roles and permissions for the rollout
audience: adopter, admin, project-manager, customer
category: adoption
order: 53
summary: How adopters set up roles and permissions in Exponential for the rollout - from the people roles of the project to roles and policies in the administration, least privilege, sections and subtrees, testing with test accounts, and keeping it tidy later.
keywords: roles, permissions, policies, role-based permissions, least privilege, sections, subtrees, editors, administrators, test accounts, offboarding
type: help
---
# Roles and permissions for the rollout

Exponential has a "Fine grained role based permissions system" (`README.md`). Setting it up well before launch avoids
both blocked editors and editors who can change what they should not.

## Contents

1. [From people to roles](#from-people-to-roles)
2. [Principles](#principles)
3. [Testing](#testing)
4. [Keeping it tidy](#keeping-it-tidy)

## From people to roles

| People | Typical role in the administration |
|---|---|
| Editors of one department | Edit and create in their section or subtree |
| Senior editors | Also publish and approve |
| Translators | Edit in their language |
| Administrators | Full access, few people |
| Members of the site | Access to member areas only |

Recommendations for teams: [Roles for your team](../../customers/day-to-day/roles-for-your-team.md),
[Choosing roles for new staff](../../customers/day-to-day/choosing-roles-for-new-staff.md).

## Principles

- **Least privilege**: give each role only what the work needs.
- **Roles for groups, not for people**: assign users to groups and roles to groups.
- **Few administrators**: each one is a risk ([Admin accounts and access](../../customers/healthy/admin-accounts-and-access.md)).
- **Two-step sign-in** for administrators where possible ([Accounts, passwords and two-factor](../../customers/day-to-day/accounts-passwords-and-two-factor.md)).

## Testing

Create one test account per role on staging, sign in as each, and try what they must and must not be able to do.
Remove the test accounts before launch.

## Keeping it tidy

- Remove access when people leave ([Offboarding staff](../../customers/day-to-day/offboarding-staff.md)).
- Review roles at least once a year ([Annual value review](../../customers/value/annual-value-review.md)).

## Related pages

- [Launching](README.md)
- [Accounts and sign-in](../../accounts-and-sign-in.md)
- [Team and roles](../planning/team-and-roles.md)
- [Governance after launch](../after-adoption/governance-after-launch.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
