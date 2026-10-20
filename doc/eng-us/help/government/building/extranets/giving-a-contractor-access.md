---
title: Giving a contractor access
audience: government, admin
category: public-sector
order: 40
summary: Step by step for administrators: create a personal account for a contractor's employee in the right external user group, with no self-registration, a recorded end date, two-step sign-in if required, and a first-sign-in check.
keywords: contractor account, external user, create user, user group, onboarding, end date, two-step sign-in, password reset, extranet access
type: help
---
# Giving a contractor access

This page is for administrators who open extranet access for a person working for a contractor or partner. It
assumes the user group and section of the party exist (see "User groups and sections for extranets").

## Contents

1. [Before you start](#before-you-start)
2. [Create the account](#create-the-account)
3. [First sign-in](#first-sign-in)
4. [Record it](#record-it)

## Before you start

- A request from the responsible project manager naming the person, the party, the scope and the end date.
- One account per person; never a shared account for a company.
- The contractor's own work address, not a private one.

## Create the account

1. In **Users**, open the party's user group (for example *External > Contractor A*).
2. Under **Sub items**, press **Create new** and choose the user class.
3. Fill in names, the e-mail address and a login; leave the password to the person (below).
4. **Send for publishing**.

Registration through `user/register` places new users in `[UserSettings] DefaultUserPlacement`, the public members
group; it must never be the way contractors get access.

## First sign-in

Let the person set their own password with **Forgot password** (`user/forgotpassword`), whose keys expire
([Reset user password](../../../../features/6.0/reset-user-password.md)). If two-step sign-in is required, they set it up
right after the password (see "Two-step sign-in for staff" in the intranets section). Check with them that they see
their project and nothing else.

## Record it

Write down in your access register: the person, the party, the group, the date and the planned end date. Exponential
has no expiry date for accounts, so this register is what tells you when to end access (see "Ending access").

## Related pages

- [Accounts and sign-in](../../../accounts-and-sign-in.md)
- [Ending access](ending-access.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
