---
title: Inviting family members
audience: individual, website-creator, customer
category: website-creation
order: 1703
summary: How one person gives relatives access to a private family site on Exponential - creating accounts yourself or letting relatives register, placing them in the right group, first sign-in and password reset, help for less technical relatives, two-step sign-in, and disabling an account quietly.
keywords: invite, family members, relatives, accounts, create user, registration, user group, DefaultUserPlacement, activation mail, password, forgot password, disable account, two-factor, family site
type: help
---
# Inviting family members

This page is for individuals who run a family site and now need to let relatives in. You can create every account
yourself, or let relatives register and approve them by placing them in the right group. For a family, creating
accounts yourself is usually better.

## Contents

1. [Two ways in](#two-ways-in)
2. [Creating an account yourself](#creating-an-account-yourself)
3. [Letting relatives register](#letting-relatives-register)
4. [The first sign-in](#the-first-sign-in)
5. [Helping less technical relatives](#helping-less-technical-relatives)
6. [Two-step sign-in](#two-step-sign-in)
7. [When someone should no longer have access](#when-someone-should-no-longer-have-access)
8. [Checklist](#checklist)

## Two ways in

| | You create the account | The relative registers |
|---|---|---|
| Who starts | You | The relative, on `user/register` |
| Who is in | Only people you chose | Anyone who finds the page, until you act |
| Mail needed | For password reset | For activation and password reset |
| Suits | Family sites | Clubs and larger groups ([Members and registration](../club-sites/members-and-registration.md)) |

## Creating an account yourself

1. In the administration, open **Users** and the group the relative belongs in, for example "Family"
   ([Private areas with roles and sections](private-areas-with-roles-and-sections.md)).
2. **Create here** > *User*. Fill in first and last name, the e-mail address and a user name.
3. Set a temporary password, or leave the relative to set one through "Forgot password".
4. Publish. The account exists and has the rights of its group.

Tell the relative personally (by phone, in person, or by a message they will recognize) that an account is coming,
so a mail from your site is not taken for spam or a scam.

## Letting relatives register

If you prefer that relatives sign up themselves, the registration page `user/register` must be open to visitors, and
new accounts land in the group set by `site.ini [UserSettings] DefaultUserPlacement`. With
`VerifyUserType=email` the account is activated by a link in a mail
([Member registration and members-only areas](../../creators/website-creators/member-registration-and-areas.md)).

For a family site:

- Point `DefaultUserPlacement` to a waiting group with **no** access to the private area.
- When you recognize a new registration, move that user into the Family group.
- Remove registrations you do not recognize.

So nobody sees family content just because they found the registration page.

## The first sign-in

Send each relative:

- the address of the site;
- their user name;
- how to set their password: "Forgot password" on the login page sends a link that expires
  ([Reset user password](../../../features/6.0/reset-user-password.md));
- one sentence on the house rules ([Planning a family site](planning-a-family-site.md#house-rules)).

Outgoing mail must work for the reset link; test it with your own test account first.

## Helping less technical relatives

- Write a one-page "how to" with screenshots of your own site: sign in, look at news, look at photos, sign out.
- Offer a short video call for the first sign-in.
- Keep the menu short and the words plain ("Photos", not "Media library").
- Give each relative only reading rights at first; add posting rights to those who want them.
- The pages of [Non-technical people](../../non-technical-people/non-technical-people-start-here.md) can be shared
  with them.

## Two-step sign-in

For accounts that can post or administer, two-step sign-in (an app code or a code by mail) protects against stolen
passwords. It can be optional or required ([Two-factor sign-in](../running-it/two-factor-sign-in.md)). For readers who
struggle with it, the code by mail is the gentler choice.

## When someone should no longer have access

Life changes. To remove access quietly:

1. Open the user in **Users**.
2. Disable the account in its user settings, or move it out of the Family group. Disabling keeps everything they
   wrote; removing the account is final.
3. If they had contributor rights, look over what they posted recently.
4. If you suspect the password was shared, ask everyone to change theirs, or reset them.

Review the list of accounts twice a year ([Monthly upkeep checklist](../checklists/monthly-upkeep-checklist.md)).

## Checklist

- [ ] A Family group with the right roles exists.
- [ ] Each relative has a personal account in that group.
- [ ] Registration is either closed or lands in a group without access.
- [ ] Password reset tested.
- [ ] A short "how to" for relatives.
- [ ] Two-step sign-in for contributors and administrators.
- [ ] You know how to disable an account.

## Related pages

- [Family sites](README.md)
- [Accounts and sign-in](../../accounts-and-sign-in.md)
- [Member registration](../../businesses/members/member-registration.md)
- [Offboarding staff](../../customers/day-to-day/offboarding-staff.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
