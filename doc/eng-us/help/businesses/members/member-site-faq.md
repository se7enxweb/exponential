---
title: Member site FAQ
audience: businesses, project-manager, website-creator
category: business-solutions
order: 70
summary: Short, sourced answers to the questions member organizations ask about running their site on Exponential - joining, approval, members-only pages and files, levels, newsletters, unsubscribing, events, forums, fees and personal data.
keywords: member site FAQ, membership questions, registration, approval, members only, levels, newsletter, unsubscribe, events, calendar, forums, membership fees, personal data, volunteers
type: help
---
# Member site FAQ

This page is for boards, office staff and volunteers who run a member site. Each answer is short and links to the
page with the details.

## Contents

1. [Joining](#joining)
2. [Members-only content](#members-only-content)
3. [Communication](#communication)
4. [Events and community](#events-and-community)
5. [Money and data](#money-and-data)
6. [Running the site](#running-the-site)
7. [Related pages](#related-pages)

## Joining

**Can people join on the website?**
Yes. With the policy `user/register` in the Anonymous role, visitors register and confirm their e-mail address
([Member registration](member-registration.md)).

**Can we check each new member before they get access?**
Yes. The office gets a mail with signed links to activate or reject each registration, and registrations can be held
until approved ([Registration and account approval](../extranets/registration-and-account-approval.md)).

**Can the office create accounts for members who joined on paper?**
Yes, in the administration; members then set their password with "Forgot your password?"
([Member registration, Accounts created by the office](member-registration.md#accounts-created-by-the-office)).

**What about fake sign-ups?**
Keep e-mail verification on, add a CAPTCHA field to the user class, and clear unconfirmed registrations regularly
([Member registration, Keeping bots out](member-registration.md#keeping-bots-out)).

## Members-only content

**How do we hide pages from the public?**
Put them in a members section that the Anonymous role does not read; visitors are asked to sign in
([Members-only content](members-only-content.md)).

**Can documents be downloaded by non-members if a link is shared?**
No. Every download checks that the person may read the file
([Files and downloads](../../creators/website-creators/files-and-downloads.md#protected-downloads)).

**Can some members see more than others?**
Yes, with a section and a role per membership level
([Members-only content, Levels of membership](members-only-content.md#levels-of-membership)).

**Can we show visitors what they would get by joining?**
Yes, with public teaser pages that link to the member content
([Members-only content, Teasers for visitors](members-only-content.md#teasers-for-visitors)).

## Communication

**Is there a newsletter system?**
Yes, `cjw_newsletter`: lists, double opt-in, sending by cron, bounces, blacklist and an archive
([Member communications](member-communications.md#the-newsletter)).

**Can members unsubscribe easily?**
Yes. Every optional mail has a one-click unsubscribe link and a link to the person's e-mail preferences page; no login
is needed ([E-mail preferences: the user's guide](../../../features/6.0/mail-preferences.md)).

**Can members be told when new documents appear?**
Yes, with notifications on the areas they follow, at once or as a digest
([Notifications: the user's guide](../../../features/6.0/notifications.md)).

**Can we test a newsletter without sending it to everyone?**
Yes. `cjw_newsletter` can write all mail to files for a full rehearsal
([cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md#rehearse-the-whole-thing-without-sending-a-single-mail)).

## Events and community

**Is there an event calendar?**
Yes. The `ezwebin` design ships Event, Event calendar and Multicalendar classes, with calendar and program views
([Events and calendars](events-and-calendars.md)).

**Can members sign up for events?**
With a form built on the information collector. Seat limits, waiting lists and tickets are not built in
([Events and calendars, Signing up](events-and-calendars.md#signing-up-for-an-event)).

**Can members add events to their phone's calendar?**
No calendar file export was found in the repository; this would be an extension
([Events and calendars, What is not built in](events-and-calendars.md#what-is-not-built-in)).

**Is there a forum?**
`ezwebin` ships forum classes and templates; members post when their role allows it
([Members-only content, Forums](members-only-content.md#forums-and-member-contributions)).

## Money and data

**Can members pay their fee online?**
A one-off payment can be sold as a product through the built-in shop with a payment gateway
([Stores](../stores/README.md), [Payment gateways](../../creators/website-creators/payment-gateways.md)). Recurring
fees and automatic expiry of membership are not documented features
([Member site planning, What is not built in](member-site-planning.md#what-is-not-built-in)).

**What personal data does the site keep?**
What the user class and forms collect, plus sign-in records, newsletter subscriptions and the e-mail preferences
consent log. Review it with your data protection contact
([Personal data in Exponential](../security/personal-data-in-exponential.md)). This FAQ is not legal advice.

**Can a member download what the site knows about their mail choices?**
Yes, their e-mail preferences and every change as JSON or CSV
([E-mail preferences: the user's guide](../../../features/6.0/mail-preferences.md)).

## Running the site

**Can volunteers edit only their own area?**
Yes, with an editor role assigned with a Section limitation
([Departments, roles and sections](../intranets/departments-roles-and-sections.md)).

**How do we handle members who leave?**
Disable their accounts, or use Deactivate and move when enabled
([Check user accounts](../../../features/6.0/user-hygiene.md)).

**What happens when our volunteers change?**
See [Handover when volunteers change](../associations/handover-when-volunteers-change.md).

**What does it cost?**
See the [Costs](../costs/README.md) section, whose worked examples use labeled example assumptions.

## Related pages

- [Member sites](README.md)
- [Member site checklist](member-site-checklist.md)
- [Extranet FAQ](../extranets/extranet-faq.md)
- [Businesses FAQ](../businesses-faq.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
