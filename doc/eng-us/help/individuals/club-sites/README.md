---
title: Club sites
audience: individual, website-creator, customer
category: personal-sites
order: 1710
summary: The section for people who run the site of a club, a team or a hobby group on Exponential, usually as a volunteer - members and registration, a members' area, newsletters with cjw_newsletter, events, forms, polls and forums, sharing the work, and what is not built in (dues, tickets).
keywords: club site, club, team, hobby group, society, volunteers, members, registration, members area, newsletter, cjw_newsletter, events, calendar, forms, polls, forums, sign-up, dues, tickets
type: help
---
# Club sites

This section is for individuals who run the website of a club, a sports team, a choir, a reading group or any hobby
group, often as a volunteer and often alone. A club site is public on the outside (who we are, when we meet, how to
join) and has a members' side (news, documents, sign-ups, a newsletter).

A registered charity or a larger association with staff will find more in
[Non-profits](../../non-profits/non-profits-start-here.md) and in
[Small groups and associations](../../businesses/associations/README.md).

## Contents

1. [What a club site usually holds](#what-a-club-site-usually-holds)
2. [The pages of this section](#the-pages-of-this-section)
3. [What ships for clubs](#what-ships-for-clubs)
4. [What is not built in](#what-is-not-built-in)

## What a club site usually holds

| Part | Public or members | Typical class |
|---|---|---|
| About the club, how to join, contact | Public | *Article*, *Feedback form* |
| News | Public, some members-only | *Blog* or *Folder* with *Article* |
| Events and meetings | Public calendar, members' details | *Event calendar* with *Event* |
| Photos | Public or members | *Gallery* |
| Documents (minutes, rules, forms) | Members | *File* in folders |
| Sign-ups and polls | Members | *Feedback form*, *Poll* |
| Discussion | Members | *Forums*, *Forum*, *Forum topic*, *Forum reply* |
| Newsletter | Subscribers | `cjw_newsletter` |

## The pages of this section

| Page | Read it when |
|---|---|
| [Planning a club site](planning-a-club-site.md) | Before you build, ideally with the committee |
| [Members and registration](members-and-registration.md) | Members should sign up and sign in |
| [Club newsletters](club-newsletters.md) | You want to mail news to members and friends |
| [Events and calendars for clubs](events-and-calendars-for-clubs.md) | You publish meetings, matches, concerts |
| [Forms, polls and sign-ups](forms-polls-and-sign-ups.md) | You collect sign-ups, answers and votes |
| [Sharing the work with volunteers](sharing-the-work-with-volunteers.md) | You do not want to be the only one who can update the site |

## What ships for clubs

| Need | What carries it | Source |
|---|---|---|
| Self-registration with activation by mail | `user/register`, `VerifyUserType=email` | kernel; [Member registration and areas](../../creators/website-creators/member-registration-and-areas.md) |
| A Members group and role, a Restricted section | Created by the default package's installer | `sevenx-multi-site-installer.php` |
| Events and calendars | *Event*, *Event calendar*, *Multicalendar* classes | `sevenx_classes`, `ezwebin` packages |
| Forms and polls | Information collector; *Feedback form*, *Poll* classes | kernel |
| Forums | *Forums*, *Forum*, *Forum topic*, *Forum reply* classes | `sevenx_classes` |
| Newsletters | `cjw_newsletter` | root `composer.json` |
| Maps for a venue | `ezgmaplocation` | root `composer.json` |
| Spam protection | `ezformtoken`, `recaptcha`, `hcaptcha` | root `composer.json` |

## What is not built in

- **Membership fees and dues collection.** The shop can sell items with a payment provider, but there is no
  membership-fee or renewal module.
- **Ticketing** and paid event registration.
- **Private messages between members**: `ezpm` exists but is only suggested in `composer.json`, not installed by default.
- **Calendar export** to phones.
- **Mail delivery itself**: newsletters and registration mails need a working mail server.

## Related pages

- [Individuals: start here](../individuals-start-here.md)
- [Member sites (businesses)](../../businesses/members/README.md)
- [Small groups and associations](../../businesses/associations/README.md)
- [Non-profits: start here](../../non-profits/non-profits-start-here.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
