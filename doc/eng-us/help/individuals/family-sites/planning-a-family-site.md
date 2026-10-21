---
title: Planning a family site
audience: individual, website-creator
category: personal-sites
order: 1701
summary: How to plan a private family site on Exponential before building it - fully private or partly public, who gets an account, who may post, what never goes online, the tree, the classes, and the agreements to make with relatives first.
keywords: family site, planning, private, public, accounts, relatives, children, consent, photos, structure, content tree, classes, roles, agreements, house rules
type: help
---
# Planning a family site

This page is for individuals who are about to make a site for their family. The technical part is modest; the
decisions about people are what matter. Make them before the first account exists. Write them in the
[site plan worksheet](../checklists/site-plan-worksheet.md).

## Contents

1. [Private, or partly public](#private-or-partly-public)
2. [Who gets an account](#who-gets-an-account)
3. [Who may post](#who-may-post)
4. [House rules](#house-rules)
5. [The tree](#the-tree)
6. [The classes](#the-classes)
7. [A worked example](#a-worked-example)

## Private, or partly public

| Model | How | Suits |
|---|---|---|
| **Fully private** | The whole public siteaccess requires sign-in (`RequireUserLogin=true`) | Families who want nothing public |
| **Public door, private house** | A small public front page; everything else in a section anonymous visitors cannot read | Families who want a contact page or a public family history |

Both are explained in [Private areas with roles and sections](private-areas-with-roles-and-sections.md). Fully
private is simpler to get right; start there unless you have a reason not to.

## Who gets an account

- **Each person their own account.** A shared family password cannot be withdrawn from one person and leaves no
  trace of who did what.
- **Children**: decide with their parents whether they get an account, from what age, and with what rights.
- **Partners of relatives, in-laws, close friends**: decide the rule once rather than case by case.
- **When someone leaves the family circle** (a separation, a falling-out): you need a way to remove access quietly.
  Disabling an account is that way ([Inviting family members](inviting-family-members.md)).

## Who may post

| Role | Can | Typical |
|---|---|---|
| Reader | Read the private area | Most relatives |
| Contributor | Read, post news, upload photos, comment | A few active relatives |
| Administrator | Everything | You, and one trusted deputy |

A deputy matters: if you are ill or away, someone else must be able to reset a password or take something down.

## House rules

Agree these with the family and put them on a page inside the site:

1. No photos of anyone who asked not to be shown; ask before posting other people's children.
2. No addresses, school names, travel dates or other details that help a stranger find someone.
3. Nothing is copied from the site to public social media without asking.
4. Anyone can ask for something about them to be removed, and it is removed.
5. Accounts are personal; passwords are not shared.

See [Privacy for your family](privacy-for-your-family.md).

## The tree

Keep it shallow and obvious to people who rarely use computers:

| Folder | Holds |
|---|---|
| News | *Blog posts* |
| Photos | One *Gallery* per event or year |
| Recipes | *Articles* |
| Our history | *Articles*, with images and documents |
| Dates | *Event calendar* for gatherings; the list of birthdays ([Birthdays and family dates](birthdays-and-family-dates.md)) |
| House rules | One *Article* |

## The classes

The default site package has every class above. You only need a new class for structured family data, such as a
"Relative" with name, date of birth (`ezbirthday`) and photo
([Designing your content model](../../creators/website-creators/designing-your-content-model.md)). Think twice before
building a register of relatives: it is exactly the kind of personal data that needs care.

## A worked example

An invented example: a family spread over several countries wants to share photos and news.

| Decision | Choice |
|---|---|
| Model | Fully private |
| Accounts | Every adult relative; teenagers with their parents' agreement |
| Contributors | Four relatives who post regularly |
| Administrators | The site owner and one cousin |
| Content | News, Photos by year, Recipes |
| Not online | Addresses, phone numbers, school names, travel plans |
| Security | Two-step sign-in for administrators and contributors |

## Related pages

- [Family sites](README.md)
- [Private areas with roles and sections](private-areas-with-roles-and-sections.md)
- [Family privacy checklist](../checklists/family-privacy-checklist.md)
- [Planning your website](../../creators/website-creators/planning-your-website.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
