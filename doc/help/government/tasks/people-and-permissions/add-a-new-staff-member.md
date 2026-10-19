---
title: Add a new staff member
audience: government, admin
category: public-sector
order: 40
summary: Step by step - create the account of a new member of staff in their department's user group, give them a first password safely, ask for two-step sign-in, and check their access.
keywords: new staff, new user, joiner, onboarding, create user, user group, first password, account, department, editor account
type: help
---
# Add a new staff member

This task is for administrators and service-desk staff. At the end, the new colleague has a personal account in the
right group, with the access that group's roles give. Background:
[Joiners, movers and leavers](../../operations/people-and-permissions/joiners-movers-leavers.md).

## Contents
1. [Before you start](#before-you-start)
2. [Create the account](#create-the-account)
3. [First sign-in](#first-sign-in)
4. [Check it worked](#check-it-worked)
5. [Pitfalls](#pitfalls)
6. [Related pages](#related-pages)

## Before you start

- An approved request naming the department, the job and the start date.
- The department's user group exists and carries the right roles
  ([Create a user group for a department](create-a-user-group-for-a-department.md)).
- You may create users in that group (`content/create` of the user class there; administrators may).

## Create the account

1. In the administration, open **Users** and the department's user group.
2. Create a new item of the user class (usually "User") in the group.
3. Fill in first name, last name, the login (your organization's naming rule), the work e-mail address, and a first
   password. Use **Generate a strong password** where the form offers it.
4. **Send for publishing** (or publish). The account's main location is in the group, so the group's roles apply.

The password must meet `site.ini [UserSettings] MinPasswordLength` (10 on a new installation) and, from 6.0.15, is
checked against common passwords (`WeakPasswordCheck=enabled`) ([Changing your password](../../../../features/6.0/modern-password-change.md)).

## First sign-in

1. Give the first password through a separate channel (not in the same e-mail as the login), or ask the person to
   use **Forgot your password?** on the sign-in page.
2. Ask them to change it at once under **My account** > **Password**.
3. If your policy requires it, ask them to set up two-step sign-in under **My account > Two-step sign-in**
   ([Turn on two-step sign-in for staff](turn-on-two-step-sign-in-for-staff.md)).

## Check it worked

- Open the account page (`/user/edit/<id>`): it lists the groups and roles, and hints such as "two-step sign-in is
  off".
- **Users > Check user accounts**: the account appears under **Never signed in** until the first sign-in.
- The new colleague sees the expected Edit buttons and nothing more.

Change record: login (not the password), group, date, request reference.

## Pitfalls

- Creating the account in the wrong group gives the wrong roles; fix it with a move, not with extra roles.
- A shared login for a team cannot be told apart in the audit trail; create one account per person.
- Two accounts with the same login cannot both sign in reliably; the edit form refuses a login another account has.

## Related pages
- [Move a staff member to another department](move-a-staff-member-to-another-department.md)
- [Reset a staff password](reset-a-staff-password.md)
- [User account hygiene](../../operations/people-and-permissions/user-account-hygiene.md)
- [Accounts and sign-in (help)](../../../accounts-and-sign-in.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
