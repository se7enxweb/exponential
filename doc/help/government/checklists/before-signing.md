---
title: Before signing a contract
audience: government, project-manager, partner
category: public-sector
order: 110
summary: What a public body checks before it signs a contract for an Exponential site, its hosting or its support - scope, versions, ownership of code, service levels, security, privacy, accessibility, training, exit - so nothing that matters is left to goodwill.
keywords: before signing, contract, contract review, ownership, source code, service levels, data processing agreement, exit, warranty, acceptance, public sector
type: help
---
# Before signing a contract

This checklist is for the contract manager and project manager of a public body after the award and before signature.
Each item is something that is hard to add later.

> **Note** This page describes features and common practice; it is not legal advice. Have the contract reviewed by
> your legal counsel.

## Contents

1. [Scope and versions](#scope-and-versions)
2. [Ownership and source](#ownership-and-source)
3. [Service and support](#service-and-support)
4. [Security, privacy and accessibility](#security-privacy-and-accessibility)
5. [People and knowledge](#people-and-knowledge)
6. [Exit](#exit)

## Scope and versions

- [ ] The deliverables match the tender requirements, each with an acceptance test.
- [ ] The Exponential version to be delivered is named (a 6.0.x release tag), with the extensions and their versions ([Release policy and version numbers](../procurement/lifecycle/release-policy-and-version-numbers.md)).
- [ ] The supplier keeps the site on a supported version for the term ([Supported versions](../procurement/lifecycle/supported-versions.md)).
- [ ] Platform responsibilities (PHP, database, operating system) assigned ([PHP and platform support](../procurement/lifecycle/php-and-platform-support.md)).

## Ownership and source

- [ ] Custom extensions, designs and settings are delivered as source into a repository you control ([Develop with Git](../../develop-with-git.md)).
- [ ] Custom code that extends Exponential is under the GPL version 2 or later when it is distributed to you, and you receive its source ([Licensing questions](../../project/licensing-faq.md)).
- [ ] No changes to kernel files; customization in extensions and overrides, so upgrades stay possible ([The upgrade check](../../../guides/upgrade-check.md)).
- [ ] Third-party components and their licenses listed.

## Service and support

- [ ] Service levels with your own figures: availability, response per severity, RPO and RTO, maintenance windows ([SLA options for Exponential hosting](../../hosting/business/sla-options.md)). The project publishes no SLA.
- [ ] How service levels are measured and reported.
- [ ] Time to apply released security fixes ([Security updates and patching cadence](../procurement/lifecycle/security-updates-and-patching-cadence.md)).
- [ ] Routine updates included, or priced ([Planning upgrades in the budget](../procurement/lifecycle/planning-upgrades-in-the-budget.md)).
- [ ] Escalation path and named contacts.

## Security, privacy and accessibility

- [ ] Processing agreement signed with each processor; data location stated.
- [ ] Hardening baseline agreed ([Security checklist](security-checklist.md)).
- [ ] Incident notification duty and time agreed ([Incident readiness](incident-readiness.md)).
- [ ] Accessibility target for the public site, with who tests and who fixes ([Accessibility checklist](accessibility-checklist.md)).
- [ ] No production personal data in development or training copies.

## People and knowledge

- [ ] Training sessions, participants and material rights defined ([Buying training from a partner](../procurement/training/buying-training-from-a-partner.md)).
- [ ] Documentation to be delivered: content model, roles, settings, deployment, runbooks.
- [ ] Administrator accounts for named staff of your organization from day one.

## Exit

- [ ] Exit plan annexed: export formats, handover period, cooperation with a successor ([Exit plans](../procurement/exit-plans/README.md)).
- [ ] Keys and secrets handed over at exit: audit signing keys, mail preferences secret, TLS keys ([Backups: keys and secrets](../../hosting/operations/backups-what-to-back-up.md#keys-and-secrets-that-cannot-be-recreated)).
- [ ] Return and deletion of data by the supplier at the end, with confirmation.
- [ ] [Exit readiness](exit-readiness.md) checklist added to the contract file.

## Related pages

- [Government checklists](README.md)
- [Before a tender](before-a-tender.md)
- [Tender templates](../procurement/tender-templates/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
