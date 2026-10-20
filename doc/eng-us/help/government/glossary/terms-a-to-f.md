---
title: "Government glossary: A to F"
audience: government, project-manager, admin, editor
category: public-sector
order: 20
summary: Public-sector and Exponential terms from A to F in one to three sentences each - accessibility statement, audit trail, content class, data controller, data processor, exit plan, freedom of information and more - with a link to the page that explains each.
keywords: glossary, terms, definitions, accessibility statement, acceptance testing, audit trail, content class, consent, data controller, data processor, data residency, exit plan, freedom of information, public sector
type: reference
---
# Government glossary: A to F

This page is for readers in a public body who meet a term they do not know in this help or in a tender. Each entry has
one to three sentences and a link to the page that explains it. Entries about Exponential were checked against the
documentation; for the full product glossary see [the Exponential glossary](../../../glossary.md).

> **Note** Definitions of legal terms are plain-language descriptions, not legal definitions. Check the law that
> applies to your organization with your legal counsel.

## Contents

1. [A](#a)
2. [B and C](#b-and-c)
3. [D](#d)
4. [E and F](#e-and-f)

## A

**Acceptance testing.** The tests a public body runs before it accepts delivered work and pays for it. Base them on
the requirements in the tender, written so they can be verified.

**Access review.** A periodic check of who has which permissions, removing those no longer needed. In Exponential,
roles are listed at `/role/list` and **Check user accounts** (`/user/hygiene`) finds unused accounts
([Check user accounts](../../../features/6.0/user-hygiene.md)).

**Accessibility conformance report (ACR).** A vendor's report on how a product meets an accessibility standard, often
produced with a VPAT. No ACR exists for Exponential ([Accessibility standards](../../../standards/accessibility.md#section-508)).

**Accessibility statement.** A public statement on a website about its accessibility, its known problems and how to
give feedback; the EU Web Accessibility Directive requires one from public bodies. Exponential ships no template for
it ([Accessibility standards](../../../standards/accessibility.md#known-gaps-2); W3C guidance:
[w3.org/WAI/planning/statements](https://www.w3.org/WAI/planning/statements/)).

**admin4.** The administration design of Exponential that the accessibility work concentrates on, with light and dark
modes; its status against WCAG 2.2 AA is "Partly complies" ([The admin4 design](../../../features/6.0/admin4-design.md)).

**Approval workflow.** A process in which a version waits for someone to approve it before it is published. In
Exponential this is built with workflows and triggers ([Workflows](../../../guides/workflows.md)).

**Audit trail.** A tamper-evident log of security-relevant events such as sign-ins, content changes and settings
writes. In Exponential it is on by default and hash-chained ([Audit trail](../../../features/6.0/audit-trail.md)).

## B and C

**Backup.** A copy of the database and the files taken at the same moment, so a site can be restored
([Backups: what to back up](../../hosting/operations/backups-what-to-back-up.md)).

**Bid.** A supplier's response to a tender. Compare bids with a scoring sheet prepared before they arrive.

**Cache.** Stored results that make pages faster; they are rebuilt when cleared. **Setup > Cache management**
(`/setup/cache`) lists them ([Caches](../../../guides/caches.md)).

**Consent record.** Proof of what a person agreed to, when, and with which text. Exponential keeps one for e-mail
categories in the table `expmail_consent_log` ([Privacy standards](../../../standards/privacy.md#gdpr)).

**Content class.** The definition of a kind of content, such as an article or a press release, as a list of fields
([The content model and editing content](../../../guides/content-model-and-editing.md)).

**Content Security Policy (CSP).** A browser rule set, sent by the site, that limits where scripts and styles may come
from. Exponential sends it report-only on the administration by default and none on public sites
([Content Security Policy](../../../features/6.0/content-security-policy.md)).

**Cronjob.** A task that runs on a schedule on the server, such as sending notifications or publishing timed content
([Cronjobs](../../../guides/cronjobs.md)).

## D

**Data controller.** In the GDPR, the organization that decides why and how personal data is processed; for a public
website, usually the public body. Exponential is software, not a controller
([Privacy standards](../../../standards/privacy.md); [GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj)).

**Data processor.** In the GDPR, an organization that processes personal data on behalf of the controller, such as a
hosting company or support supplier. A processing agreement sets the terms.

**Data residency.** The requirement that data is stored in a particular country or region. It is a property of the
hosting, not of the software; Exponential can be installed wherever you host it
([Choosing a hosting model](../../hosting/models/choosing-a-hosting-model.md)).

**Datatype.** The code that stores, validates and shows one kind of field, such as text, image or relation
([the Exponential glossary](../../../glossary.md#d)).

**Draft.** A version of content that is being edited and not yet published
([My drafts and my pending items](../../../guides/drafts-and-pending.md)).

## E and F

**Editor siteaccess.** A simplified administration for editors, installed by the setup wizard
([Setup wizard and editor siteaccess](../../../features/6.0/setup-wizard-and-editor-siteaccess.md)).

**Erasure.** Deleting a person's personal data on request. Removing a user account in Exponential erases its mail
preferences and anonymizes its consent log; other data is reached through separate tools
([Privacy standards](../../../standards/privacy.md#known-gaps)).

**Evaluation panel.** The group that scores bids against published criteria.

**Exit plan.** A plan, agreed before signing, for how a contract ends: data export, handover, transition. Exponential's
exports are listed in [Migrating away from Exponential](../../../install/migration/leaving-exponential.md).

**Extension.** A folder under `extension/` that adds features to Exponential, activated in the settings
([Extensions](../../../guides/extensions.md)).

**Freedom of information (FOI).** Laws that give the public a right to request records held by public bodies; in the
United States the federal law is FOIA ([foia.gov](https://www.foia.gov/)).

**Functional requirement.** A requirement about what the system must do, as opposed to how well (performance,
security, accessibility).

## Related pages

- [Government glossary](README.md)
- [Government glossary: G to L](terms-g-to-l.md)
- [The Exponential glossary](../../../glossary.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
