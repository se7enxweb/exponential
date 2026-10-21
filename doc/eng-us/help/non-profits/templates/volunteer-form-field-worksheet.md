---
title: Volunteer form field worksheet
audience: non-profit, businesses, website-creator, editor
category: business-solutions
order: 30
summary: A worksheet to design a volunteer sign-up form before anyone builds it - each field with its purpose, datatype, whether it is required, who reads it and how long it is kept - plus the receiver, thank-you page, spam protection, file uploads and the text shown next to the form, ready to hand to the website creator.
keywords: volunteer form, worksheet, form design, fields, datatype, ezstring, ezemail, ezselection, ezboolean, eztext, enhancedezbinaryfile, required, retention, data minimization, CAPTCHA, receiver
type: help
---
# Volunteer form field worksheet

Fill this in with the volunteer coordinator and whoever is responsible for data protection, then hand it to the website
creator who builds the form ([Volunteer sign-up form, step by step](../volunteers/volunteer-sign-up-form-step-by-step.md)).
The question for every field is: **do we need this to place a volunteer?** If not, leave it out.

## Contents

1. [Form details](#form-details)
2. [Fields](#fields)
3. [Text next to the form](#text-next-to-the-form)
4. [After sending](#after-sending)
5. [Protection](#protection)
6. [Sign-off](#sign-off)

## Form details

```text
Form name (shown to visitors) .......................... ______________________
Where it appears (page address) ........................ ______________________
Purpose, in one sentence ............................... ______________________
Coordinator who reads the submissions .................. ______________________
Second person with access (holidays) ................... ______________________
```

## Fields

Datatypes available in Exponential are listed in [Choosing datatypes](../../creators/website-creators/choosing-datatypes.md).
Each field must be marked **Information collector** in the class
([Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md)).

| Field | Why we need it | Datatype | Required? | Kept for |
|---|---|---|---|---|
| Name | To address the person | Text line (`ezstring`) | Yes | |
| E-mail | To reply | `ezemail` | Yes | |
| Phone | Only if you call volunteers | Text line | Optional | |
| Areas of interest | To place the person | Selection (`ezselection`) | Yes | |
| Availability | To plan | Selection or text block (`eztext`) | Optional | |
| Message | Anything else | Text block (`eztext`) | Optional | |
| Consent to be contacted | Lawful basis, documented | Checkbox (`ezboolean`) | Yes | |
| A document (certificate, CV) | Only if required at sign-up | `enhancedezbinaryfile` | Optional | |
| _your field_ | | | | |
| _your field_ | | | | |

Fields to avoid at sign-up unless there is a clear need: date of birth, home address, health information, ID
numbers. Ask for them later, from the people you accept, by a person.

The example field set comes from [Volunteers and sign-up forms](../../businesses/nonprofits/volunteers-and-sign-up-forms.md#designing-a-volunteer-form).

## Text next to the form

Write the wording here so it is approved before the form goes live:

```text
What volunteering involves ............................. ______________________
Who reads this form .................................... ______________________
How long we keep it .................................... ______________________
Consent wording (next to the checkbox) ................. ______________________
Link to the privacy notice ............................. ______________________
```

## After sending

| Decision | Choice |
|---|---|
| Mail goes to (receiver, or the form's own `recipient` attribute) | |
| Visitor sees a result page, or is redirected to a thank-you page (`Display=redirect`, recommended) | |
| Thank-you page text | |
| Who answers within how many days | |

Source: [Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md).

## Protection

| Measure | Decision |
|---|---|
| Form token (`ezformtoken`) | Active by default; keep it |
| CAPTCHA: `hcaptcha` or `recaptcha` | |
| File uploads: `MaxFiles` limit (25 by default; 0 is unlimited and not advisable) | |
| Who gets `infocollector/read` | |
| Deletion schedule for old submissions | |

Sources: [Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md),
[enhancedezbinaryfile](../../../features/6.0/extensions/enhancedezbinaryfile.md).

## Sign-off

```text
Coordinator ............................ date ________
Data protection contact ................ date ________
Website creator (built and tested) ..... date ________
```

## Related pages

- [Templates and worksheets](README.md)
- [Volunteer sign-up form, step by step](../volunteers/volunteer-sign-up-form-step-by-step.md)
- [Data minimization](../../government/privacy/data-minimization.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
