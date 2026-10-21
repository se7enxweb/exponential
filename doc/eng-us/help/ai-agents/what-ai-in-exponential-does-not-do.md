---
title: What AI in Exponential does not do
audience: ai-agents, developer, app-creator, admin, editor
category: ai-agents
order: 7
summary: The limits of AI in Exponential in one place - the assistant does not publish on its own, use a service account, upload images or files, or send anything before you configure a provider; agents get no rights beyond their account; and what is not part of Exponential today - so that nobody promises a feature that is not there.
keywords: limits, does not, cannot, no auto publish, drafts only, no service account, no uploads, not included, claims, accuracy
type: help
---
# What AI in Exponential does not do

This page is for everyone who explains, buys, configures or builds on the AI features of Exponential. It collects the
limits in one place. Each one is a deliberate decision, documented with the part that makes it.

## Contents

1. [Exponential Intelligence](#exponential-intelligence)
2. [Interfaces for agents](#interfaces-for-agents)
3. [Across the system](#across-the-system)
4. [Claims to avoid](#claims-to-avoid)

## Exponential Intelligence

| It does not | What it does instead |
|---|---|
| Publish on its own | Writes drafts; "Send for approval" runs the normal publish operation as the editor, and an approval workflow can hold it |
| Use a service account or switch users | Runs every lookup and every draft as the signed-in editor, with the editor's content policies; queued work runs as the user who queued it |
| Delete, move, change permissions or send mail | Can only read what the editor may read and write drafts the editor may write |
| Upload images or files, or set relations | Says so; the editor sets them in the edit form. With a vision model it can suggest the alt text of an image that is already there |
| Read the whole site at once | Reads only what the model asks for through its tools, with limits on list and field length, and only what the data policy lets go to that provider |
| Send anything before a provider is configured | Stays idle; it needs a key or a local model |
| Let any model write | Only models with native tool calling that passed the live probe, or are trusted by configuration, may write drafts ([The write gate and the probe](expai/the-write-gate-and-the-probe.md)) |
| Offer write services of expservices to the model | Only read services that opted in ([Service tools](expai/service-tools.md)) |
| Serve other programs | It works inside the administration for signed-in editors; programs use the REST API or expservices |
| Keep the texts of requests and answers in its usage log | Records tokens, model, cost, tools and errors; full requests only with `[UsageSettings] LogRequests=enabled`, a debugging setting not meant for production |

Sources: the expai [FAQ](https://github.com/se7enxweb/expai/blob/main/doc/faq.md) and
[security](https://github.com/se7enxweb/expai/blob/main/doc/security.md) pages.

## Interfaces for agents

| They do not | What they do instead |
|---|---|
| Give an agent more than its account holds | REST and expservices act as the owner of the key or token, through the kernel's policy checks |
| Give a key more than its scopes | A REST route needs the scope it is mapped to; a write route of an extension stays closed to keys until its scope is named |
| Accept writes without POST | expservices writes need POST, and with a session a form token too |
| Keep working for a disabled user | Every key and token of a disabled owner stops at once |

An MCP server for agents is not part of Exponential today.

## Across the system

- **No AI model is built in.** Exponential calls providers you configure; a local model runs in Ollama, not in PHP.
- **No training on your content** is done by Exponential. What a provider does with requests is governed by your
  contract with it; expai records the provider's declared region and retention
  ([Privacy and data leaving the site](governance/privacy-and-data-leaving-the-site.md)).
- **No legal assessment.** The data policy settings and the "EI: Summary" page help a data protection officer; they
  are not a statement of compliance.

## Claims to avoid

| Do not say | Say |
|---|---|
| "The AI publishes content for you." | "It writes drafts; a person publishes." |
| "It is GDPR compliant." | "It records where every request went, and lets you keep sections and classes on EU or local providers." |
| "It is free." | "The software is free; a hosted provider may charge per request, and expai estimates and caps that cost." |
| "Exponential AI" or "ExpAI" | "Exponential Intelligence", or `expai` in technical text |
| "Agents can do anything in the CMS." | "Agents act as an account you create, with the rights you give it." |

The advocates' version of these rules: [Talking points: Exponential Intelligence](../advocates/talking-points-exponential-intelligence.md)
and [Claims you must not make](../advocates/claims-you-must-not-make.md).

## Related pages

- [What exists today](what-exists-today.md)
- [Drafts and human review](expai/drafts-and-human-review.md)
- [Credentials for agents](agentic-workflows/credentials-for-agents.md)
- [Objection: "What about the AI features?"](../advocates/objection-ai-features.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
