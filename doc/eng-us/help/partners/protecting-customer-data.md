---
title: Protecting customer data
audience: partner
category: partnership
order: 710
summary: How partners protect personal data of their customers and their customers' users in every part of the partnership - support tickets, bug reports, activity logs, leads, case studies, staging copies and training - with rules, placeholders and a checklist.
keywords: privacy, data protection, personal data, customer data, GDPR, anonymize, placeholders, staging, tickets, leads, consent
type: help
---
# Protecting customer data

This page is for everyone in a partner company. Partners handle personal data of customers and of their customers'
users. The partnership must never become a way that data leaks. This page sets practical rules; the law that applies
to you (for example data protection law in your country or your customer's) decides the details.

## The rules

1. **Share only what is needed.** A ticket needs the error, not the customer's user list.
2. **Placeholders in public.** In issues, forums, chats and screenshots, replace names, e-mail addresses, private URLs
   and host names with placeholders such as `customer-site.example`.
3. **Consent for leads and references.** Pass a person's contact details to another party only with their consent
   ([Referrals and leads](referrals-and-leads.md)).
4. **No production data in test and training.** Staging copies are anonymized; training uses invented test accounts.
5. **No personal data in activity logs.** Evidence for points is a public URL and a description
   ([Logging your partner activity](logging-partner-activity.md)).
6. **Delete when done.** Exports, dumps and screenshots are deleted after use.

## Where data leaks in practice

| Place | Risk | Prevention |
|---|---|---|
| Support tickets and bug issues | Logs with e-mail addresses, session ids | Clean logs before pasting ([Opening a partner support ticket](opening-a-partner-support-ticket.md)) |
| Screenshots | Names in lists, user menus | Use a test account; blur or crop |
| Database dumps for debugging | Whole user tables | Share a reproduction, not a dump |
| Staging copies | Real users' data on a weaker server | Anonymize; protect staging like production |
| Case studies | Names and quotes without approval | Written approval ([Writing a case study](writing-a-case-study.md)) |
| Shared leads | Contact data passed on without consent | Consent recorded first |

## Contracts

- Agree with each customer who processes which data for what purpose.
- If 7x or another partner works on the site for you (white label, subcontracting), they are part of that chain;
  name them as your law requires ([White-label 7x services](white-label-services.md)).

## Checklist

- [ ] Staff trained on these rules
- [ ] Placeholders used in every public post
- [ ] Staging anonymized
- [ ] Consent records for leads and references
- [ ] Deletion routine for exports and dumps

## Related pages

- [Security responsibilities of partners](security-responsibilities.md)
- [Partner code of conduct](partner-code-of-conduct.md)
- [Security hardening](../security-hardening.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
