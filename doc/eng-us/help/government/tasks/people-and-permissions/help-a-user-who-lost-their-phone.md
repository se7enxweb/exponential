---
title: Help a user who lost their phone
audience: government, admin
category: public-sector
order: 240
summary: Step by step - after verifying the person, switch their two-step sign-in to e-mail codes or off by editing the Two-step sign-in field of their user object, let them sign in and set up the app again, and record it.
keywords: lost phone, new phone, two-step sign-in, 2FA reset, authenticator, e-mail code, service desk, identity verification, account recovery
type: help
---
# Help a user who lost their phone

This task is for service-desk staff and administrators. At the end, a colleague whose authenticator phone is lost or
replaced can sign in again and has set up the app on the new phone. The procedure is the one in
[Two-step sign-in, "What a user sees"](../../../../guides/two-factor-login.md#what-a-user-sees).

## Contents
1. [Before you start](#before-you-start)
2. [If e-mail codes are allowed](#if-e-mail-codes-are-allowed)
3. [Switch the second step](#switch-the-second-step)
4. [Set up the new phone](#set-up-the-new-phone)
5. [Record and follow up](#record-and-follow-up)
6. [Related pages](#related-pages)

## Before you start

- **Verify the person** through your service-desk procedure (a call back to a known number, a manager's
  confirmation). Resetting a second factor for an unverified caller defeats it.
- The right to edit the user's object (`content/edit` on it; administrators have it).

## If e-mail codes are allowed

With `AllowEmailFallback` enabled (the default, as the [security and audit guide](../../../../guides/security-and-audit.md#5-two-factor-sign-in-optional-10-minutes)
notes), the person may be able to take an e-mail code at sign-in and set up the new phone without your help.
Try that first.

## Switch the second step

1. Open the user's object in the administration (Users tree) and **Edit**.
2. Set the *Two-step sign-in* field to *E-mail one-time code* or *Disabled*.
3. Publish.

## Set up the new phone

1. The person signs in (with an e-mail code, or with the password only if you chose *Disabled*).
2. They open **My account > Two-step sign-in** (`/user2fa/setup`), choose *Authenticator app*, scan the QR code and
   enter the code the app shows. Nothing changes until that code is entered.
3. If `Enforce2FA=enabled`, a user without a second step is sent to the setup page right after the password anyway.

## Record and follow up

- Record: date, who verified the person and how, what was switched.
- If the phone was stolen rather than lost, treat it as a possible compromise: change the password and review the
  account's recent sign-ins ([Suspected account compromise](../../operations/incidents/suspected-account-compromise.md)).
- Check that the person did not stay on *Disabled*: their account page shows a hint when two-step sign-in is off.

## Related pages
- [Turn on two-step sign-in for staff](turn-on-two-step-sign-in-for-staff.md)
- [Reset a staff password](reset-a-staff-password.md)
- [Two-step sign-in for staff](../../operations/people-and-permissions/two-factor-for-staff.md)
- [Two-step sign-in guide](../../../../guides/two-factor-login.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
