---
title: Information collection forms and privacy
audience: government, admin, editor, project-manager
category: public-sector
order: 260
summary: For public bodies collecting data through web forms - what Exponential stores per submission, who may read and export it, the audit records of exports and removals, the absence of automatic retention, and the consent text, notice and retention routine each form needs.
keywords: information collection, forms, form submissions, collected information, infocollector/read, CSV export, retention, removal, consent text, privacy notice, data.export.csv, data.infocollection.remove, mail to receiver, public sector
type: help
---
# Information collection forms and privacy

This page is for administrators, editors who own forms, and data protection officers. Contact, complaint and
registration forms of a public site often collect sensitive information. In Exponential a form is a content object
whose class has **Information collector** attributes; each sending is stored as a collection
([Collected information](../../../guides/collected-information.md)). The project lists forms among the known gaps: "the
site must add a consent text and a retention rule itself" ([Privacy standards](../../../standards/privacy.md#known-gaps)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [What is stored](#what-is-stored)
2. [Who sees it](#who-sees-it)
3. [Retention: a manual routine](#retention-a-manual-routine)
4. [Each form needs](#each-form-needs)
5. [Related pages](#related-pages)

## What is stored

- One collection per sending, with one value per collector attribute, the time, and the user id (the anonymous user for
  visitors).
- If the form sends mail, a copy goes to the receiver (`site.ini [InformationCollectionSettings] EmailReceiver`, or a
  `recipient` attribute in the ezwebin templates): that mailbox is another place the data lives.
- Visitors who are not signed in may send by default (`collect.ini [CollectionSettings] CollectAnonymousData=enabled`).

## Who sees it

- **Setup > Collected information** and every page under it need `infocollector/read`; give it to the people who
  process the forms.
- **Export CSV** downloads all collections of a form (up to 50 000); each export is recorded in the audit as
  `data.export.csv` with the number of rows, never the values.
- Removals are recorded as `data.infocollection.remove`.

## Retention: a manual routine

Nothing removes collections automatically. Set a routine:

1. Define the retention per form in your schedule (for example: until the case is closed plus a period).
2. On the schedule, open Setup > Collected information, export what must be kept in your case system, then tick the
   objects (or the individual collections) and **Remove selected**; the confirmation lists what goes.
3. Delete the exported CSV files when they have served their purpose.
4. Check the receiver mailbox's retention too.

## Each form needs

- [ ] A short notice next to the form: who processes the data, why, how long, and a link to the privacy notice.
- [ ] Only the fields the purpose needs; optional fields marked as such.
- [ ] A consent checkbox only where consent is really the basis (often a public task is).
- [ ] A named owner who reads the submissions.
- [ ] A retention rule and the routine above.
- [ ] An accessible form (the accessibility section of this help).
- [ ] No CAPTCHA unless abuse is real (see "CAPTCHAs and privacy").

## Related pages

- [Collected information](../../../guides/collected-information.md)
- [Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md)
- [Form submissions](../../creators/website-creators/form-submissions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
