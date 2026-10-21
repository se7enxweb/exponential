---
title: Volunteers
audience: non-profit, businesses, editor, website-creator
category: non-profits
order: 10
summary: Everything a non-profit does with volunteers on its Exponential site - recruiting them with an information collector form protected by a form token and hCaptcha or reCAPTCHA, processing applications, a members area for active volunteers, and volunteers who edit the site safely with roles, sections, approval, versions and two-factor login.
keywords: volunteers, volunteer recruitment, sign-up form, information collector, hcaptcha, recaptcha, enhancedezbinaryfile, applications, members area, volunteer editors, roles, training
type: help
---
# Volunteers

This folder is for volunteer coordinators, staff and the website creator. Volunteers meet your site twice: first as
applicants who fill in a form, later perhaps as members of a volunteers' area or as editors of the site itself. Each
stage has its own pages.

## Contents

1. [What Exponential offers](#what-exponential-offers)
2. [The pages of this folder](#the-pages-of-this-folder)
3. [The volunteer journey on the site](#the-volunteer-journey-on-the-site)
4. [What is not built in](#what-is-not-built-in)

## What Exponential offers

| Need | What exists | Source |
|---|---|---|
| A sign-up form | The information collector: class attributes marked as collectors become a form; submissions are stored | [Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md) |
| Spam protection | `ezformtoken` (on by default), `hcaptcha` and `recaptcha` datatypes | [Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md) |
| Uploads with the form | `enhancedezbinaryfile` ("Enhanced File") for information collection | [enhancedezbinaryfile](../../../features/6.0/extensions/enhancedezbinaryfile.md) |
| Reading and exporting | **Setup > Collected information**, CSV export | [Collected information](../../../guides/collected-information.md) |
| Accounts for volunteers | User registration with mail verification, a Members group and role | [Member registration and areas](../../creators/website-creators/member-registration-and-areas.md) |
| Volunteers who edit | Roles limited by section, approval workflow, versions, audit trail, two-factor login | [Shared editing with volunteers](../../businesses/associations/shared-editing-with-volunteers.md) |
| Retiring accounts | **Check user accounts**, and the opt-in **Deactivate and move** | [Check user accounts](../../../features/6.0/user-hygiene.md) |

All the extensions named here are installed through `composer.json`.

## The pages of this folder

| Page | What it answers |
|---|---|
| [Volunteer sign-up form, step by step](volunteer-sign-up-form-step-by-step.md) | Building the form from the class to the thank-you page |
| [Spam protection for volunteer forms](spam-protection-for-volunteer-forms.md) | Form token, hCaptcha or reCAPTCHA, privacy and accessibility of a CAPTCHA |
| [Processing volunteer applications](processing-volunteer-applications.md) | Reading, answering, exporting and deleting submissions |
| [A members area for volunteers](volunteer-member-area.md) | Rotas, guides and news only volunteers see |
| [Volunteers as editors](volunteers-as-editors.md) | Letting volunteers publish without risking the site |
| [Training volunteer editors](training-volunteer-editors.md) | A first session, house rules and a cheat sheet |

Worksheets: [Volunteer form field worksheet](../templates/volunteer-form-field-worksheet.md) and
[Roles and permissions worksheet](../templates/roles-and-permissions-worksheet.md).

## The volunteer journey on the site

| Stage | On the site | Page |
|---|---|---|
| Finds you | A "Volunteer with us" page, linked from the menu, campaigns and newsletter | [Campaigns and appeals](../communications/campaigns-and-appeals.md) |
| Applies | The sign-up form | [Sign-up form](volunteer-sign-up-form-step-by-step.md) |
| Is accepted | A reply from the coordinator; perhaps an account | [Processing applications](processing-volunteer-applications.md) |
| Takes part | Members area, event pages, newsletter | [Members area](volunteer-member-area.md) |
| Helps with the site | An editor account with a limited role | [Volunteers as editors](volunteers-as-editors.md) |
| Leaves | Account deactivated, data deleted on schedule | [When volunteers leave](../operations/when-volunteers-leave.md) |

## What is not built in

A search of the repository found no volunteer management features beyond forms and accounts:

| Not built in | Plan instead |
|---|---|
| Shift scheduling and rotas with sign-up slots | A rota as a page or file in the members area, or an outside scheduling tool |
| Hours tracking | A spreadsheet or an outside tool |
| Background-check workflows | Your own process, outside the site |
| Automatic reminders to volunteers | A newsletter list for volunteers ([Supporter newsletter](../communications/supporter-newsletter-with-cjw-newsletter.md)) |

A partner can estimate an extension if you need one ([Working with a partner](../operations/working-with-a-partner.md)).

## Related pages

- [Non-profits: start here](../non-profits-start-here.md)
- [Volunteers and sign-up forms](../../businesses/nonprofits/volunteers-and-sign-up-forms.md)
- [Small groups and associations](../../businesses/associations/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
