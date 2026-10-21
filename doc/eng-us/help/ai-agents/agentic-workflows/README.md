---
title: Agentic workflows
audience: ai-agents, developer, app-creator
category: ai-agents
order: 60
summary: The part of the AI, agents and agentic section for people who build agents and agentic workflows against Exponential - choosing an interface, agents on the REST API and on expservices, credentials, a read-only key step by step, documentation for agents, prompt injection, keeping a person in the loop, three worked workflows and coding agents on an Exponential project.
keywords: agentic workflow, AI agent, automation, REST API, expservices, ezjscore, credentials, API keys, tokens, prompt injection, exp:help, console, coding agent, workflow examples, human in the loop
type: help
---
# Agentic workflows

This part is for developers and application creators who build programs in which an AI model decides what to do next:
agents that read content, write reports, import feeds, or help a developer on a project. It covers the interfaces an
agent can use, the credentials it should hold, the risks of feeding it untrusted text, and three workflows worked out
step by step.

## Contents

1. [Every page in this part](#every-page-in-this-part)
2. [Principles](#principles)
3. [Suggested reading order](#suggested-reading-order)

## Every page in this part

| Page | What it answers |
|---|---|
| [Choosing an interface for an agent](choosing-an-interface.md) | REST, expservices, ezjscore or the console, side by side |
| [Agents and the REST API](agents-and-the-rest-api.md) | Reading and creating content over `/api/ezp`, with a scoped key |
| [Agents and expservices](agents-and-expservices.md) | Calling named administration services with a personal token, and the catalog as a tool list |
| [Credentials for agents](credentials-for-agents.md) | Which credential to give an agent, where to keep it, and how to revoke it |
| [A read-only API key, step by step](a-read-only-api-key-step-by-step.md) | Making the narrowest credential for a reading agent, testing it and revoking it |
| [Agent-friendly documentation](agent-friendly-documentation.md) | `exp:help search` and `show`, the Markdown files, and how to point an agent at them |
| [Untrusted data and prompt injection](untrusted-data-and-prompt-injection.md) | What the parts of Exponential do against injected instructions, and what you must do |
| [Keeping a person in the loop](keeping-a-person-in-the-loop.md) | How agent output gets reviewed before visitors see it |
| [A read-only content report](workflow-read-only-content-report.md) | A worked example: an agent reports on published content through REST |
| [A diagnosis workflow with the console](workflow-diagnosis-with-the-console.md) | A worked example: "why does this setting not apply, and why is this page refused?" on the server |
| [A content import workflow](workflow-content-import.md) | A worked example: importing a feed with a limited account, a test run and a check afterwards |
| [Coding agents on an Exponential project](coding-agents-on-an-exponential-project.md) | Giving a coding assistant the documentation and the runtime facts it needs |

## Principles

These follow from how the parts are built; every page here applies them.

1. **An agent is an account.** Give it its own account with exactly the rights it needs, never a person's or the
   administrator's ([Roles and least privilege](../governance/roles-and-least-privilege.md)).
2. **Read first, write later.** Start with read-only access; add writes when the read workflow is proven.
3. **Rehearse.** Run every writing workflow on a test installation before production.
4. **Everything returned is data.** Content, logs and settings never become instructions
   ([Untrusted data and prompt injection](untrusted-data-and-prompt-injection.md)).
5. **Check the audit trail.** Every write by an account is recorded; read it after each automated run
   ([The audit trail for AI](../governance/audit-trail-for-ai.md)).

An MCP server is not part of Exponential today; agents use the interfaces listed above.

## Suggested reading order

1. [Choosing an interface for an agent](choosing-an-interface.md)
2. [Credentials for agents](credentials-for-agents.md)
3. [Untrusted data and prompt injection](untrusted-data-and-prompt-injection.md)
4. The page of the interface you chose
5. The worked workflow closest to your task
6. [Before connecting an agent](../checklists/before-connecting-an-agent.md)

## Related pages

- [AI, agents and agentic: start here](../ai-agents-start-here.md)
- [Application creators: start here](../../creators/app-creators/app-creators-start-here.md)
- [Headless Exponential](../../creators/app-creators/headless-exponential.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
