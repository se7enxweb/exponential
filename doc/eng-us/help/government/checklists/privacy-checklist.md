---
title: Privacy checklist
audience: government, admin, project-manager
category: public-sector
order: 50
summary: A privacy and data protection checklist for a public body running Exponential - inventory, notices, cookies, forms, e-mail consent, logs and retention, requests from individuals and processors - separating the features Exponential provides from the decisions that stay with the organization.
keywords: privacy, data protection, GDPR, checklist, privacy notice, cookies, consent, forms, retention, logs, erasure, access request, processor, processing agreement, public sector
type: help
---
# Privacy checklist

This checklist is for the data protection officer and site administrator of a public body. It follows
[Privacy standards](../../../standards/privacy.md), which states: Exponential is software, not a data controller;
compliance is the responsibility of the organization that runs the site.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel. In the EU: [GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj),
> [ePrivacy Directive](https://eur-lex.europa.eu/eli/dir/2002/58/oj).

## Contents

1. [Inventory](#inventory)
2. [Notices and cookies](#notices-and-cookies)
3. [Forms and e-mail](#forms-and-e-mail)
4. [Logs and retention](#logs-and-retention)
5. [Requests from individuals](#requests-from-individuals)
6. [Suppliers](#suppliers)

## Inventory

- [ ] List where personal data lives: user accounts, form submissions ([Collected information](../../../guides/collected-information.md)), newsletter subscribers, orders, logs, search phrases ([Privacy standards](../../../standards/privacy.md#known-gaps)).
- [ ] List every active extension that sends data to a third party: `ActiveExtensions[]` in `settings/override/site.ini.append.php` ([Privacy standards](../../../standards/privacy.md#third-party-services-in-shipped-extensions)).
- [ ] Record purposes and legal bases in your record of processing.

## Notices and cookies

- [ ] Privacy notice published and linked from every page.
- [ ] The kernel sets only functional cookies; list them in the notice ([Privacy standards](../../../standards/privacy.md#eprivacy-directive-cookies)).
- [ ] If the site adds analytics, embeds or advertising, add a consent banner: Exponential has none ([Statistics and analytics](../../creators/website-creators/statistics-and-analytics.md)).
- [ ] CAPTCHA services (reCAPTCHA, hCaptcha) named in the notice if used ([Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md)).

## Forms and e-mail

- [ ] Each form asks only for what it needs and shows a consent or information text ([Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md)).
- [ ] Each form has a retention rule; old submissions are removed ([Form submissions](../../creators/website-creators/form-submissions.md)).
- [ ] E-mail preferences set up: categories off by default, consent log kept ([E-mail preferences: setting them up](../../../guides/mail-preferences-administrator.md)).
- [ ] Sender details (postal address) set for the mail footer ([Privacy standards](../../../standards/privacy.md#can-spam-act-and-one-click-unsubscribe)).
- [ ] Newsletter statistics only with prior consent ([Newsletters with cjw_newsletter](../../creators/website-creators/newsletters-with-cjw-newsletter.md)).

## Logs and retention

- [ ] Audit privacy settings reviewed: truncated addresses, pseudonymization after `PseudonymiseAfterDays` ([Audit trail](../../../features/6.0/audit-trail.md)).
- [ ] Request logs mask IP addresses (`MaskIP=enabled`) ([Request logs](../../../features/6.0/request-logs.md)).
- [ ] Kernel logs other than 404, security and CSP have no age limit by default; set `MaxAgeDays` or rotate them yourself ([Log rotation](../../../features/6.0/log-rotation.md)).
- [ ] Web server access logs handled by the host's retention rule.
- [ ] Backups have a retention period too ([Backup schedules and retention](../../hosting/operations/backup-schedules-and-retention.md)).

## Requests from individuals

- [ ] Access and portability: mail preferences and consent history export (`mailpreferences/export`, `./console exp:mail:preferences export`); audit records with `./console exp:audit export --subject-user=<id>` ([Privacy standards](../../../standards/privacy.md#gdpr)).
- [ ] Other data (form submissions, content the person wrote, orders) found through the separate admin tools; there is no single "export all my data" ([Privacy standards](../../../standards/privacy.md#known-gaps)).
- [ ] Erasure: removing the account erases mail preferences and anonymizes the consent log; remove form submissions separately.
- [ ] Restriction: **Deactivate and move** where enabled ([Check user accounts](../../../features/6.0/user-hygiene.md)).
- [ ] Each request logged with date and answer in your own register.

## Suppliers

- [ ] Processing agreement with the host and the support supplier.
- [ ] Suppliers never use production personal data in training or test copies.
- [ ] Data location stated in the hosting contract ([Choosing a hosting model](../../hosting/models/choosing-a-hosting-model.md)).
- [ ] Support tickets and error reports carry no personal data ([Getting support](../../project/getting-support.md)).

## Related pages

- [Government checklists](README.md)
- [Privacy standards](../../../standards/privacy.md)
- [Compliance and GDPR (for hosts)](../../hosting/security/compliance-and-gdpr.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
