---
title: Governance of AI and agents
audience: ai-agents, admin, project-manager, businesses
category: ai-agents
order: 80
summary: The part of the AI, agents and agentic section for the people who set the rules - privacy and data leaving the site, cost control, the audit trail for AI, roles and least privilege for assistants and agents, and a template for an AI use policy.
keywords: governance, AI policy, privacy, data protection, cost control, budgets, audit trail, roles, least privilege, accountability, data protection officer, compliance
type: help
---
# Governance of AI and agents

This part is for managers, administrators and data protection officers who decide how AI is used on an Exponential
installation, and who must answer for it afterwards. It explains the controls the software gives you and how to turn
them into rules your organization can follow.

## Contents

1. [Every page in this part](#every-page-in-this-part)
2. [The questions governance must answer](#the-questions-governance-must-answer)

## Every page in this part

| Page | What it answers |
|---|---|
| [Privacy and data leaving the site](privacy-and-data-leaving-the-site.md) | What goes to an AI provider, how the data policy limits it, what is kept, and the EI: Summary page |
| [Cost control](cost-control.md) | Estimated cost per request, limits, budgets for queued work, lanes and batches |
| [The audit trail for AI](audit-trail-for-ai.md) | The audit channel `ai`, API key and token events, and how to give an auditor exactly that |
| [Roles and least privilege](roles-and-least-privilege.md) | Accounts and roles for assistants and agents, and how to check them |
| [An AI use policy for your organization](an-ai-use-policy.md) | A template to adapt, with the settings that enforce each rule |
| [Incident response for AI](incident-response-for-ai.md) | What to stop first, where to find out what happened, and how to recover |

## The questions governance must answer

| Question | Where the answer comes from |
|---|---|
| Who may use AI, and for what? | Roles with the `expai/*` policies ([Permissions for AI features](../expai/permissions-for-ai-features.md)) |
| Which content may go to which provider? | The data policy ([Privacy and data leaving the site](privacy-and-data-leaving-the-site.md)) |
| Who approves AI-written content? | An approval workflow ([Drafts and human review](../expai/drafts-and-human-review.md)) |
| How much may it cost? | Limits and budgets ([Cost control](cost-control.md)) |
| What happened, and who did it? | The audit trail ([The audit trail for AI](audit-trail-for-ai.md)) |
| Which agents may act on the system, as whom? | Accounts and credentials ([Roles and least privilege](roles-and-least-privilege.md)) |
| How do we stop it? | The master switch and the veto hook ([The master switch and the veto hook](../expai/master-switch-and-veto.md)) |

The software records and enforces; it does not assess. Whether a provider's terms meet your legal obligations is a
decision for your organization and its advisers.

## Related pages

- [AI, agents and agentic: start here](../ai-agents-start-here.md)
- [Security basics for businesses](../../businesses/security/security-basics-for-businesses.md)
- [GDPR basics](../../businesses/security/gdpr-basics.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
