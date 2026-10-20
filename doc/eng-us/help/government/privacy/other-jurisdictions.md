---
title: Privacy rules outside the EU
audience: government, project-manager
category: public-sector
order: 310
summary: For public bodies outside the EU, or with audiences in several jurisdictions - how Exponential's privacy features relate to rules in general terms, with only the laws the project's own documents name (CAN-SPAM, CASL, CCPA/CPRA) and a feature-based approach that works under most regimes.
keywords: other jurisdictions, privacy laws, CAN-SPAM, CASL, CCPA, CPRA, United States, Canada, California, data protection, feature mapping, public sector, international
type: help
---
# Privacy rules outside the EU

This page is for data protection and project staff of public bodies outside the EU, and of bodies serving people in
several jurisdictions. Privacy rules differ widely, and public bodies are often subject to rules of their own. This
page does not summarize foreign laws; it names only the ones the project's documents map, and shows a feature-based
approach.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Laws the project maps](#laws-the-project-maps)
2. [A feature-based approach](#a-feature-based-approach)
3. [Public sector specifics](#public-sector-specifics)
4. [Related pages](#related-pages)

## Laws the project maps

| Law | What the project documents | Where |
|---|---|---|
| CAN-SPAM Act (United States, 15 U.S.C. 7701 ff.) | **Complies** for mail through the mail gate: unsubscribe link and `List-Unsubscribe` honored at once; sender postal address left to the site | [Privacy standards](../../../standards/privacy.md#can-spam-act-and-one-click-unsubscribe) |
| CASL (Canada) and CCPA/CPRA (California) | the mail preference features mapped in a checklist | [the law checklist](../../../specifications/6.0/mail-preferences-compliance.md) |

Mail sent by public bodies is not always "commercial" in the sense of such laws; ask your counsel which rules apply.

## A feature-based approach

Most privacy regimes ask similar things. Map your law's requirements to these features:

| Common requirement | Feature |
|---|---|
| notice | your privacy notice (see "A privacy notice outline") |
| consent or opt-out for marketing mail | categories, double opt-in, one-click unsubscribe, consent log |
| access to one's data | mail preference export, audit export, admin tools |
| deletion | account removal with mail data erasure; `exp:mail:preferences erase` |
| security safeguards | the security section of this help |
| retention limits | audit, log and consent retention settings |
| breach detection | audit alerts, security log |

## Public sector specifics

- Public records and freedom of information laws may require keeping content that a privacy request asks to remove;
  your counsel decides which prevails.
- Some jurisdictions restrict where public data may be hosted; Exponential runs on your own servers or your host's, so
  hosting location is a procurement choice.
- Accessibility of privacy information (notice, forms, consent banners) is part of accessibility law in many places.

## Related pages

- [Privacy standards](../../../standards/privacy.md)
- [Mapping the GDPR to Exponential](gdpr-mapping.md)
- [One-click unsubscribe](one-click-unsubscribe.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
