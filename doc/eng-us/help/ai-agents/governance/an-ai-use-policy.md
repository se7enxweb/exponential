---
title: An AI use policy for your organization
audience: ai-agents, admin, businesses, project-manager, government
category: ai-agents
order: 85
summary: A template for an internal AI use policy on an Exponential installation, rule by rule, with the setting or feature that enforces each rule - who may use AI, which content may go where, human review, accountability for published text, costs, agents and their accounts, records, and how to stop it.
keywords: AI use policy, AI policy template, acceptable use, governance, rules, human review, accountability, data policy, budgets, agents, records, switch off, organization policy
type: help
---
# An AI use policy for your organization

This page is for managers and administrators who write down how their organization uses AI with Exponential. It is a
template: adapt the wording, delete what does not apply, and keep the right-hand column, which names the setting or
feature that makes each rule more than a promise. It is not legal advice.

## Contents

1. [How to use this template](#how-to-use-this-template)
2. [The template](#the-template)
3. [Rules the software cannot enforce](#rules-the-software-cannot-enforce)
4. [Keeping the policy alive](#keeping-the-policy-alive)

## How to use this template

1. Read [What exists today](../what-exists-today.md) so the policy names only features you have.
2. Fill in the brackets.
3. Configure each enforcing setting at the same time as you approve the policy.
4. Publish the policy where editors find it, for example as a page of your intranet.

## The template

| Rule | Enforced by |
|---|---|
| **Scope.** AI writing help is used in [sections] by [groups] for [purposes]. | Roles with `expai/write`, assigned with subtree limitations ([Permissions for AI features](../expai/permissions-for-ai-features.md)) |
| **Providers.** Requests go only to [providers], checked on [date]. | `[ProviderSettings] Providers[]`, `TermsCheckedAt` ([Configuring a provider](../expai/configuring-a-provider.md)) |
| **Sensitive content.** [Classes/sections] go only to EU or local providers; [classes/sections] never go to any provider. | `[DataPolicy]` rules; veto rules ([Privacy and data leaving the site](privacy-and-data-leaving-the-site.md)) |
| **Human review.** AI output is a draft; a person reviews it before publication; [sections] need approval by [role]. | Drafts only; an approval workflow ([Drafts and human review](../expai/drafts-and-human-review.md)) |
| **Accountability.** The person who publishes a text is responsible for it, as for any text. | The publish operation runs as that person; the label marks AI-written versions |
| **Accessibility.** AI alt text is checked by the editor; decorative images are the editor's choice. | The alt text buttons ([Alt text for images](../expai/alt-text-for-images.md)) |
| **Translations.** Machine translations are reviewed by someone who knows the language. | Translation sync drafts; `expai/translation_sync` |
| **Costs.** AI costs at most [amount] per month, [amount] per user per day. | `[Limits]`, `[Budgets] SiteMonthly` ([Cost control](cost-control.md)) |
| **Agents.** Every agent has its own account and role, approved by [role]; agents start read-only. | Dedicated accounts; API keys with the `read` scope only ([Roles and least privilege](roles-and-least-privilege.md)) |
| **Credentials.** Keys and tokens live in [secret store]; never in prompts or repositories. | `settings/override`; key warnings on EI: Models ([Credentials for agents](../agentic-workflows/credentials-for-agents.md)) |
| **Records.** AI use is recorded for [period]; [role] reviews it [monthly]. | The audit channel `ai`, its retention; the usage log ([The audit trail for AI](audit-trail-for-ai.md)) |
| **Data protection.** [Contact] answers questions about the processing. | `[SummarySettings] Contact`, EI: Summary |
| **Stopping.** [Role] may switch AI off at once, on all or some siteaccesses. | `[AISettings] Enabled`, `DisabledSiteAccesses[]` ([The master switch and the veto hook](../expai/master-switch-and-veto.md)) |

## Rules the software cannot enforce

Some rules depend on people. Write them down anyway, and train for them:

- Do not paste confidential material into a request that the data policy would not cover (for example text from
  another system).
- Do not present AI-written text as a person's own statement where that matters (quotes, signed opinions).
- Check facts, names, numbers and links in every AI draft ([Reviewing AI drafts](../checklists/reviewing-ai-drafts.md)).
- Report odd behavior (a draft that follows instructions found in content, an unexpected cost) to [contact].

## Keeping the policy alive

- Review it when you add a provider, a model or an agent.
- Compare it with EI: Summary twice a year; the summary is read from the live settings.
- Keep the version history of the policy with the dates of the settings changes it caused.

## Related pages

- [Governance of AI and agents](README.md)
- [Before switching on Exponential Intelligence](../checklists/before-switching-on-exponential-intelligence.md)
- [Exponential Intelligence course for trainers](../../trainers/exponential-intelligence/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
