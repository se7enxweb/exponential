---
title: What cannot be exported
audience: government, admin, project-manager, developer
category: public-sector
order: 160
summary: For public bodies planning how to answer access and portability requests - the limits of Exponential's exports (no single export of a person's data, accounts as content objects, form submissions per form, pseudonymized index, masked logs, data outside Exponential) and options to close the gap.
keywords: export limits, data portability, access request, no export all my data, user content object, information collection, pseudonymized, masked logs, web server logs, backups, custom export, developer, public sector
type: help
---
# What cannot be exported

This page is for data protection officers, administrators and developers. Knowing the limits in advance lets you plan
the access request procedure and, if requests are frequent, commission a custom export. The facts come from
[Privacy standards: Known gaps](../../../standards/privacy.md#known-gaps).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [The limits](#the-limits)
2. [Why](#why)
3. [Options](#options)
4. [Related pages](#related-pages)

## The limits

| Limit | Consequence |
|---|---|
| No single "export all my data" function | each place is exported separately (see "The right of access") |
| A user account is a content object | its fields are read on its node; there is no dedicated account export beyond the Check user accounts CSV |
| Form submissions are exported per form | you export each form's CSV and pick the person's rows |
| Audit index pseudonymized after 90 days | `exp:audit export --subject-user` still finds pseudonymized records; full values remain in archives |
| Logs are masked when shown | masked logs cannot identify a person; unmasked reading needs `setup/unmasklogs` and is recorded |
| Search phrases have no person attached | they cannot be attributed to a requester |
| Suppression list stores hashes | it can confirm an address is listed, not list it |
| Web server, Velocity, request-shield and mail server logs | outside Exponential: ask the host |
| Backups | not searched per person; they expire with your backup rotation |

## Why

Exponential's content model stores many kinds of data as content objects in classes your site defines; a generic
"everything about this person" export would have to know which of your classes and fields are personal. The project
provides dedicated exports where it owns the data model (mail preferences, consent log, audit).

## Options

- **A procedure** with a checklist per place (the inventory in "Where personal data lives in Exponential").
- **A custom export** by a developer, for your classes and forms: the persistent object API and template fetch functions
  give access to the data; put it in an extension of your own and protect it with a policy.
- **Fewer places**: minimize what you collect (see "Data minimization"), and remove form submissions when cases close.

## Related pages

- [The right of access](right-of-access.md)
- [Privacy standards](../../../standards/privacy.md)
- [Extension development](../../extension-development/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
