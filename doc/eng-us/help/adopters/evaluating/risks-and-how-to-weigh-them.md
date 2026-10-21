---
title: Adoption risks and how to weigh them
audience: adopter, businesses, project-manager
category: adoption
order: 17
summary: The risks an adopter of Exponential should consider - skills, provider dependence, migration, custom code, upgrades, security, funding of the project - with how likely and serious each is for you, and how to reduce it.
keywords: risks, risk register, skills, provider, migration, custom code, upgrades, security, project funding, mitigation
type: help
---
# Adoption risks and how to weigh them

Every adoption has risks. Writing them down and deciding how to reduce each is part of a good decision.

## Contents

1. [A risk register](#a-risk-register)
2. [Weighing](#weighing)
3. [What the sources say about the project itself](#what-the-sources-say-about-the-project-itself)

## A risk register

| Risk | How to reduce it | Page |
|---|---|---|
| Too few people know Exponential | Train your staff; choose a partner; contribute to keep skills public | [Skills and training](../planning/skills-and-training.md), [Objection: can we find people](../../advocates/objection-can-we-find-people.md) |
| Dependence on one provider | Own your code and accounts; keep the code in Git; document handover | [What you own](../../customers/what-you-own.md), [Handover checklist](../../customers/partners-and-hosts/handover-checklist.md) |
| Migration takes longer than planned | Rehearse and measure; migrate in stages | [Migrating](../migrating/README.md), [Adopting in stages](../planning/adopting-in-stages.md) |
| Custom code makes upgrades hard | Keep custom code in extensions; avoid patching the kernel | [Extending without forking](../../extension-development/part-5-extend-fork-contribute/extending-without-forking.md) |
| Updates are postponed | A routine from the first month | [Keeping current](../after-adoption/keeping-current.md) |
| Security incidents | Hardening, updates, private reporting | [Security after adoption](../after-adoption/security-after-adoption.md) |
| A needed feature is missing | Prove it in the proof of concept; plan an extension or sponsor it | [A proof of concept](proof-of-concept.md) |

## Weighing

For each risk, note how likely it is for you (low, medium, high) and how serious it would be. Act first on risks that
are both likely and serious. The project managers' page has a fuller method: [Risk management](../../leadership/risk-management.md).

## What the sources say about the project itself

- **Continuity.** The code base was kept alive after its original vendor's end of life in 2021, and the 6.0 line has
  been released since 1 January 2024 ([A short history of the ecosystem](../../ecosystem/a-short-history-of-the-ecosystem.md)).
- **Two organizations.** A company (7x) and a nonprofit (the Exponential Foundation) ([Organizations](../../ecosystem/organizations/README.md)).
- **Open code.** If every provider disappeared, the code would still be yours under the GPL.
- **Not published.** The Foundation's board, governance and budget are not published
  ([What the sources do not say](../../who-is-the-exponential-foundation.md#what-the-sources-do-not-say)). Ask if it
  matters to you.
- **Funding.** The project runs on donated time and sponsorship; adopters who rely on it can help secure it
  ([Funding the community](../../community/how-it-works/funding-the-community.md)).

## Related pages

- [Evaluating Exponential](README.md)
- [Risk management](../../leadership/risk-management.md)
- [Objection: is it still alive](../../advocates/objection-is-it-still-alive.md)
- [Writing the decision](../deciding/writing-the-decision.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
