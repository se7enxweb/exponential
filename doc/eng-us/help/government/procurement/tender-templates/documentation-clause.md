---
title: "Tender template: documentation clause"
audience: government, project-manager, partner, admin
category: public-sector
order: 130
summary: Clause text for the documentation a supplier delivers with an Exponential site - system description, content model, roles, configuration differences, operations runbooks, editor guides - in editable form under an open license, built on the free product documentation.
keywords: documentation clause, system documentation, handover, runbooks, content model, configuration, editor guide, as-built, GFDL, open license, tender template
type: help
---
# Tender template: documentation clause

This page is for procurement officers and IT managers who want documentation that lets another team take over. The
product itself is documented in the repository, in `doc/`, shown in the administration's help, and is free
documentation under the GNU FDL 1.3 or later ([The license of this documentation](../../../../README-LICENSE.md)).
The supplier therefore does not need to re-document the product: the clause asks for what is specific to your site.

**Template** - adapt to your procurement rules; not legal advice

## Contents
1. [The clause](#the-clause)
2. [The documentation set](#the-documentation-set)
3. [Using the product documentation](#using-the-product-documentation)
4. [Notes for the evaluator](#notes-for-the-evaluator)

## The clause

```text
D. DOCUMENTATION

D.1 Deliverables. The Supplier delivers the documentation set in <annex> at
    acceptance and keeps it current with every release to production.

D.2 Form. Documentation is delivered in an editable, open format (for example
    Markdown in the system's repository) and is licensed to the Customer under
    <license> so that the Customer and its future suppliers may change it.

D.3 Reference, do not copy. Where the published product documentation covers a
    topic, the Supplier links the exact page and documents only what is specific
    to the Customer's system.

D.4 Language. Documentation for editors is in <languages>; technical
    documentation in <languages>.

D.5 Accessibility. Documentation meets <accessibility standard for documents>.

D.6 Verification. The Customer may ask a third party to install or restore the
    system using only the documentation; gaps found are defects.
```

## The documentation set

| Document | Contents | Product pages to link instead of copying |
|---|---|---|
| System overview | siteaccesses, designs, extensions, integrations, environments | [Introduction](../../../../install/01-introduction.md), [Extensions](../../../../guides/extensions.md) |
| Content model | content types, fields, sections, object states, workflows | [Content model](../../../../guides/content-model-and-editing.md), [Sections](../../../../guides/sections.md), [Object states](../../../../guides/object-states.md), [Workflows](../../../../guides/workflows.md) |
| Roles and permissions | each role, its policies, who holds it | [Roles and policies](../../../../guides/roles-and-policies.md) |
| Configuration record | every setting that differs from the shipped default, with the reason | [The settings page](../../../../guides/settings-page.md) |
| Installation and deployment | how to build and deploy from the repository | [Deploying](../../../../guides/deploying.md), [Installing Exponential: the book](../../../../install/README.md) |
| Operations runbooks | caches, cronjobs, backups, restore, incidents | [Operating a site](../../../../guides/operating-a-site.md), [Hosting runbooks](../../../hosting/runbooks/README.md) |
| Security record | hardening done, secrets inventory (not the secrets) | [Security hardening for production](../../../../install/13-security-hardening.md) |
| Editor guide | site-specific how-tos for your content types | [Editing content](../../../editing-content.md), [Publishing and versions](../../../publishing-and-versions.md) |
| Accessibility statement draft | per the accessibility clause | [Accessibility clause](accessibility-clause.md) |

## Using the product documentation

- Editors and administrators read the help inside the administration (**Help center**, `/exphelp/dashboard`), and
  the "?" button opens help for the current page ([Help system](../../../../features/6.0/help-system.md)).
- The help can be exported, for example as EPUB ([Documentation and publishing standards](../../../../standards/documentation-and-publishing.md)).
- Because the product documentation is under the GFDL, your own site documentation may quote and adapt it, and must
  keep that license for the adapted parts ([Free documentation](../../../free-documentation.md)).

## Notes for the evaluator

- Ask for a sample configuration record from a past project, with customer details removed.
- D.6 is the strongest test; schedule it before the warranty ends.

## Related pages
- [Tender templates](README.md)
- [Training clause](training-clause.md)
- [Exit and data return clause](exit-and-data-return-clause.md)
- [Handing over to editors](../../../creators/website-creators/handing-over-to-editors.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
