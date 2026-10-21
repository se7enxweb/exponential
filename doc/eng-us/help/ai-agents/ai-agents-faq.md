---
title: "AI, agents and agentic: FAQ"
audience: ai-agents, developer, app-creator, admin, editor
category: ai-agents
order: 3
summary: Short answers to the questions people ask about AI in Exponential - does it publish, which providers, does content leave the server, what does it cost, can it be switched off, how do agents connect and authenticate, and how is prompt injection handled - each with the page that has the full answer.
keywords: FAQ, questions, AI, Exponential Intelligence, expai, providers, local model, privacy, cost, switch off, agents, REST API, expservices, prompt injection
type: help
---
# AI, agents and agentic: FAQ

This page is for anyone with a quick question about AI or agents in Exponential. Each answer is short and links to the
page with the details.

## Contents

1. [Exponential Intelligence](#exponential-intelligence)
2. [Privacy and cost](#privacy-and-cost)
3. [Agents](#agents)

## Exponential Intelligence

**Does the AI publish content on its own?**
No. It writes drafts as the signed-in editor. Publishing happens only when an editor presses "Send for approval",
through the normal publish operation, so approval workflows apply
([Drafts and human review](expai/drafts-and-human-review.md)).

**Is it installed on my site?**
It is required by the root `composer.json`, so it is in `extension/expai`, and a new installation activates it for
the administration siteaccesses. It does nothing until a provider key or local model is configured
([What exists today](what-exists-today.md)).

**Which AI providers can it use?**
Anthropic, any OpenAI-compatible service through one adapter, a local open-weight model through Ollama, or a class of
your own. Anthropic and a local model are proven live; several others only against test servers so far
([Providers and models](expai/providers-and-models.md)).

**Whose permissions does it use?**
The signed-in editor's, for every lookup and every draft. There is no service account
([Permissions for AI features](expai/permissions-for-ai-features.md)).

**Can it translate?**
Yes: a Translate field action, translated drafts on request in the Intelligence tab, and a translation sync that keeps
translations in step with their source, always as drafts ([Translation help](expai/translation-help.md)).

**Can it add images?**
It cannot upload images or files or set relations. With a vision model it can suggest alt text for images that are
there ([Alt text for images](expai/alt-text-for-images.md)).

**Can it read the web?**
Only with the policy `expai/web` and only once configured: fetching pages of allowlisted sites, a web search, and the
project's own commit history ([Web and history tools](expai/web-and-history-tools.md)).

**Can every model write drafts?**
No. Only models with native tool calling that passed a live probe, or are trusted by configuration
([The write gate and the probe](expai/the-write-gate-and-the-probe.md)).

## Privacy and cost

**Does our content leave the server?**
Only to the providers you configure, and only what the data policy allows. With a local model, nothing leaves
([Privacy and data leaving the site](governance/privacy-and-data-leaving-the-site.md)).

**Does the browser ever see the API key?**
No. Keys live in `settings/override` and only the server calls the providers
([Configuring a provider](expai/configuring-a-provider.md)).

**What does it cost?**
The software is free; hosted providers bill per request. The assistant estimates every request's cost, shows it, and
stops at the limits and budgets you set ([Cost control](governance/cost-control.md),
[The dashboard and the log](expai/dashboard-and-log.md)).

**Can we switch it off?**
Yes, everywhere or per siteaccess, with `[AISettings] Enabled` and `DisabledSiteAccesses[]`; a veto hook can refuse
single requests ([The master switch and the veto hook](expai/master-switch-and-veto.md)).

**Is it recorded?**
Every request, refusal and draft is an event of the audit channel `ai`, without prompts, answers or keys
([The audit trail for AI](governance/audit-trail-for-ai.md)).

## Agents

**How does an agent connect to Exponential?**
Over the REST API or expservices with its own account, or, on the server itself, through the console and the help
files ([Choosing an interface for an agent](agentic-workflows/choosing-an-interface.md)). An MCP server is not part of Exponential today.

**How should an agent authenticate?**
With its own account and the narrowest credential: a scoped personal API key for REST, or a personal token with a
short lifetime for expservices ([Credentials for agents](agentic-workflows/credentials-for-agents.md)).

**Will an agent's writes be reviewed?**
REST and expservices writes take effect as the account's own actions. For review, write into a section with an
approval workflow that visitors cannot see ([Keeping a person in the loop](agentic-workflows/keeping-a-person-in-the-loop.md)).

**What about prompt injection?**
The assistant marks content as data, limits what a misled model can do, and stops writing after web text; your own
agent needs the same discipline ([Untrusted data and prompt injection](agentic-workflows/untrusted-data-and-prompt-injection.md)).

**Can an agent read the documentation?**
Yes: `./console exp:help search` and `show`, or the Markdown files in `doc/`
([Agent-friendly documentation](agentic-workflows/agent-friendly-documentation.md)).

**What if something goes wrong?**
Switch the assistant off, disable the agent's account, revoke its keys, and read the audit trail
([Incident response for AI](governance/incident-response-for-ai.md)).

## Related pages

- [AI, agents and agentic: start here](ai-agents-start-here.md)
- [AI, agents and agentic glossary](ai-agents-glossary.md)
- [Objection: "What about the AI features?"](../advocates/objection-ai-features.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
