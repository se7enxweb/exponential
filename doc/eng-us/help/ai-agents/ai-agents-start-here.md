---
title: "AI, agents and agentic: start here"
audience: ai-agents, developer, app-creator, admin
category: ai-agents
order: 1
summary: The landing page for people who use AI with Exponential and for people who build agents and agentic workflows against it. What really ships today (Exponential Intelligence, the REST API, expservices, ezjscore JSON, the audit trail and the help on the command line), what each one is for, the rules that keep AI safe on a site, and a map of every page in this section.
keywords: AI, artificial intelligence, agent, agentic, agentic workflow, LLM, large language model, Exponential Intelligence, expai, Claude, Anthropic, OpenAI-compatible, Ollama, local model, REST API, expservices, ezjscore, exp:help, prompt injection, data policy, audit, start here, overview
featured: true
type: help
---
# AI, agents and agentic: start here

This section is for two groups of readers. The first uses AI **with** Exponential: editors, site managers and
administrators who want help writing, rewriting and translating content, and who need to know where that content
goes. The second builds **against** Exponential: developers and application creators who connect an AI agent to an
installation, or who run agentic workflows that read and change content on their own. Both groups need the same
foundation: an honest picture of what ships, what it does, and what it will not do.

> **Note**
> Every statement in this section was checked against the code, the settings and the documentation of the parts it
> describes: the extension expai (Exponential Intelligence), the kernel, the extension expservices and the
> documentation in `doc/eng-us/`. Where something could not be verified, the page says less rather than more. Model
> names, prices and provider terms change; the settings of your installation are the authority.

## Contents

1. [Exponential and AI in one minute](#exponential-and-ai-in-one-minute)
2. [Who this section is for](#who-this-section-is-for)
3. [Five rules that hold everywhere](#five-rules-that-hold-everywhere)
4. [Every part of this section](#every-part-of-this-section)
5. [A first hour](#a-first-hour)
6. [Other sections you will use](#other-sections-you-will-use)

## Exponential and AI in one minute

| You want to | Use | Where it comes from |
|---|---|---|
| Write, rewrite, shorten or translate content with an AI model, in the administration | **Exponential Intelligence** (the extension `expai`) | Required by `composer.json` of the root repository, so it is in `extension/expai` of every installation; a new installation activates it for the administration siteaccesses |
| Let a program or an agent read and create content over HTTP | The **REST API** (`/api/ezp/v1`, `/api/ezp/v2`) with an OAuth2 token or a personal API key | The kernel |
| Let a program drive administration functions by name | **expservices** over the ezjscore call interface, with a personal token | The extension `expservices`, part of the default distribution |
| Answer small JSON requests from your own pages | **ezjscore** server functions | The extension `ezjscore` |
| Know what an agent or the assistant did | The **audit trail** | The kernel |
| Give an agent the documentation as plain text | The help system and `./console exp:help search` / `show` | The kernel |

Details and the exact status of each part: [What exists today](what-exists-today.md).

## Who this section is for

| You are | Start with |
|---|---|
| An editor who wants to know what the AI writing assistant does with your text | [What Exponential Intelligence does](expai/what-exponential-intelligence-does.md) |
| An administrator deciding whether to switch the assistant on, and with which provider | [Before switching on Exponential Intelligence](checklists/before-switching-on-exponential-intelligence.md) |
| A data protection officer or a manager who must answer "where does our content go?" | [Privacy and data leaving the site](governance/privacy-and-data-leaving-the-site.md) |
| A developer who wants a coding agent to work from facts on a project | [Coding agents on an Exponential project](agentic-workflows/coding-agents-on-an-exponential-project.md) |
| An application creator building an agent that reads or writes content | [Choosing an interface for an agent](agentic-workflows/choosing-an-interface.md) |
| Someone writing an internal policy on AI use | [An AI use policy for your organization](governance/an-ai-use-policy.md) |

Not sure which part fits? [Which path fits your task](which-path-fits-your-task.md) asks a few questions.

## Five rules that hold everywhere

These rules are built into the software, not just recommended. Each page of this section builds on them.

1. **The assistant writes drafts, never publications.** Exponential Intelligence has no setting to publish on its
   own; publishing happens only when an editor presses "Send for approval", through the normal publish operation,
   so approval workflows apply ([Drafts and human review](expai/drafts-and-human-review.md)).
2. **Everything runs as a real account with its own permissions.** The assistant acts as the signed-in editor; REST
   and expservices act as the owner of the token or key. None of them has a permission model of its own
   ([Roles and least privilege](governance/roles-and-least-privilege.md)).
3. **Narrow credentials.** API keys carry scopes, tokens carry lifetimes, and none of them gives more than its owner
   holds ([Credentials for agents](agentic-workflows/credentials-for-agents.md)).
4. **Content is data, not instructions.** The assistant's instructions and tool descriptions say so to the model, and
   after outside text from the web enters a conversation the assistant writes no draft while that text is in the
   conversation ([Untrusted data and prompt injection](agentic-workflows/untrusted-data-and-prompt-injection.md)).
5. **Keys never leave the server.** Provider keys live in `settings/override` only and never reach the browser, a log
   or a template ([Configuring a provider](expai/configuring-a-provider.md)).

## Every part of this section

| Part | What it answers |
|---|---|
| [Exponential Intelligence](expai/README.md) | What the writing assistant does and does not do, providers and models, local models, translation, alt text, Vector, chains, web and service tools, the dashboard, permissions, the master switch, extending it |
| [Agentic workflows](agentic-workflows/README.md) | Choosing an interface, agents on REST and expservices, credentials, documentation for agents, prompt injection, keeping a person in the loop, worked workflows, coding agents on a project |
| [Governance](governance/README.md) | Privacy and data leaving the site, cost control, the audit trail, roles and least privilege, an AI use policy, incident response |
| [Checklists](checklists/README.md) | Before switching on the assistant, before connecting an agent, reviewing AI drafts |

Short answers are in the [FAQ](ai-agents-faq.md), the words in the [glossary](ai-agents-glossary.md), and the order
of reading in the [reading path](ai-agents-reading-path.md). [What AI in Exponential does not do](what-ai-in-exponential-does-not-do.md)
lists the limits in one place.

## A first hour

| Minutes | Do | Read |
|---|---|---|
| 10 | Learn what is there and what is not | [What exists today](what-exists-today.md), [What AI in Exponential does not do](what-ai-in-exponential-does-not-do.md) |
| 15 | Pick your path | [Which path fits your task](which-path-fits-your-task.md) |
| 20 | Read the two or three pages of your path | the section README of your path |
| 15 | Go through the matching checklist | [Checklists](checklists/README.md) |

## Other sections you will use

| Section | For |
|---|---|
| [Application creators](../creators/app-creators/app-creators-start-here.md) | Building extensions and integrations, including [Exponential Intelligence for applications](../creators/app-creators/exponential-intelligence.md) and [Extending Exponential Intelligence](../creators/app-creators/extending-exponential-intelligence.md) |
| [Trainers: Exponential Intelligence course](../trainers/exponential-intelligence/README.md) | Teaching editors and administrators to use the assistant |
| [Advocates: talking points](../advocates/talking-points-exponential-intelligence.md) | Describing the assistant accurately in public |
| [Businesses](../businesses/businesses-start-here.md) | Decision makers who weigh cost and risk |

## Related pages

- [What exists today](what-exists-today.md)
- [AI, agents and agentic: reading path](ai-agents-reading-path.md)
- [AI, agents and agentic: FAQ](ai-agents-faq.md)
- [expai: Exponential Intelligence](../../features/6.0/extensions/expai.md)
- [The help system](../../features/6.0/help-system.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
