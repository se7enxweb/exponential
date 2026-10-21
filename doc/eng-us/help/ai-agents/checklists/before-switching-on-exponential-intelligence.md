---
title: Before switching on Exponential Intelligence
audience: ai-agents, admin, project-manager
category: ai-agents
order: 91
summary: A checklist for administrators before editors get the Exponential Intelligence writing assistant on a live installation - decisions, provider and key, data policy, roles, review and approval, costs, the job queue, records, testing and the way back.
keywords: checklist, switch on AI, go live, expai setup, provider key, data policy, roles, approval workflow, limits, budgets, cron parts, audit, test editor, master switch
type: help
---
# Before switching on Exponential Intelligence

This checklist is for administrators who are about to let editors use the assistant on a live installation. Work
through it in order; each item links to the page that explains it.

## Contents

1. [Decisions](#decisions)
2. [Provider and key](#provider-and-key)
3. [Data policy](#data-policy)
4. [Roles](#roles)
5. [Review and approval](#review-and-approval)
6. [Costs and the queue](#costs-and-the-queue)
7. [Records and testing](#records-and-testing)
8. [The way back](#the-way-back)

## Decisions

- [ ] Who will use it, for what, in which sections ([An AI use policy](../governance/an-ai-use-policy.md)).
- [ ] Which content must never go to an outside provider.
- [ ] Who answers questions about the processing.

## Provider and key

- [ ] Provider chosen; its terms read; `Region`, `Retention`, `TermsCheckedAt` set
  ([Configuring a provider](../expai/configuring-a-provider.md)).
- [ ] Key in `settings/override/expai.ini.append.php` only, mode 0640; not in any committed file.
- [ ] Setup > EI: Models shows "key present" and no warning about key locations.
- [ ] Test prompt of the default model answered.
- [ ] Every model that should write drafts passed the probe or is trusted
  ([The write gate and the probe](../expai/the-write-gate-and-the-probe.md)).

## Data policy

- [ ] `[DataPolicy]` rules set for sensitive sections and classes
  ([Privacy and data leaving the site](../governance/privacy-and-data-leaving-the-site.md)).
- [ ] Veto rules for content that must never be sent ([The master switch and the veto hook](../expai/master-switch-and-veto.md)).
- [ ] Images: stricter rules for images set if needed.
- [ ] `[UsageSettings] LogRequests` left disabled.

## Roles

- [ ] `expai/write` and `ezjscore/call` given only to the intended editors' role
  ([Permissions for AI features](../expai/permissions-for-ai-features.md)).
- [ ] `expai/vector`, `expai/chain`, `expai/web`, `expai/services` given only where needed, with
  limitations.
- [ ] `expai/summary` given to the data protection officer; `audit/read` on channel `ai` to an auditor if wanted.

## Review and approval

- [ ] Approval workflow on `content / publish / before` for the sections that need it; tested with a test editor
  ([Drafts and human review](../expai/drafts-and-human-review.md)).
- [ ] Editors know that "Send for approval" publishes at once where no approval applies.
- [ ] Editors have the [review checklist](reviewing-ai-drafts.md).

## Costs and the queue

- [ ] Monthly site budget and per-user daily limit set ([Cost control](../governance/cost-control.md)).
- [ ] Cron parts `expqueue` (every minute) and `expai` (every five minutes) running, if queued work is used
  ([Chains and queued work](../expai/chains-and-queued-work.md)).

## Records and testing

- [ ] The audit channel `ai` is written; `./console exp:audit tail --channel=ai` shows a test request
  ([The audit trail for AI](../governance/audit-trail-for-ai.md)).
- [ ] Signed in as a test editor: the Intelligence tab appears, a draft is written, nothing is published by itself.
- [ ] EI: Summary printed and filed with the policy.

## The way back

- [ ] Everyone who may need it knows how to switch it off: `[AISettings] Enabled` or `DisabledSiteAccesses[]`.

## Related pages

- [AI and agent checklists](README.md)
- [Exponential Intelligence for AI users](../expai/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
