---
title: Before connecting an agent
audience: ai-agents, developer, admin, app-creator
category: ai-agents
order: 92
summary: A checklist before an AI agent gets access to an Exponential installation through the REST API, expservices or the console - purpose, account and role, credential, tool design, data that may reach the agent's provider, writes, untrusted input, records and revocation.
keywords: checklist, connect agent, REST API key, expservices token, console, agent account, least privilege, scopes, prompt injection, audit, revoke
type: help
---
# Before connecting an agent

This checklist is for developers and administrators who are about to give an AI agent access to an installation.
Go through it for each agent; each item links to the page that explains it.

## Contents

1. [Purpose](#purpose)
2. [Account and credential](#account-and-credential)
3. [Tool design](#tool-design)
4. [Data](#data)
5. [Writes](#writes)
6. [Records and revocation](#records-and-revocation)

## Purpose

- [ ] The agent's task is written down, with what it must read and what it must change.
- [ ] The interface is chosen for that task ([Choosing an interface for an agent](../agentic-workflows/choosing-an-interface.md)).
- [ ] The agent works on a development or test installation first.

## Account and credential

- [ ] A dedicated account in a group for agents; no person's account, no administrator
  ([Roles and least privilege](../governance/roles-and-least-privilege.md)).
- [ ] Its role is limited by subtree, section, class and language as far as the task allows.
- [ ] Effective rights checked with `./console exp:access:check` or a test sign-in.
- [ ] The credential is the narrowest one: a key with only the needed scopes, a token with a short lifetime
  ([A read-only API key, step by step](../agentic-workflows/a-read-only-api-key-step-by-step.md)).
- [ ] The credential lives in a secret store or an environment variable, never in a prompt, a repository or a shared
  configuration file ([Credentials for agents](../agentic-workflows/credentials-for-agents.md)).

## Tool design

- [ ] One tool per allowed REST route or expservices service in the agent's definition, no generic HTTP or shell tool
  ([Agents and the REST API](../agentic-workflows/agents-and-the-rest-api.md#describing-the-routes-to-a-model)).
- [ ] Inputs validated in code; the key added by code, never shown to the model.
- [ ] Lists paged with small limits.
- [ ] For a coding agent on the server: read commands only, on a development copy
  ([Coding agents on an Exponential project](../agentic-workflows/coding-agents-on-an-exponential-project.md)).

## Data

- [ ] Everything the agent's account can read may go to the agent's model provider
  ([Privacy and data leaving the site](../governance/privacy-and-data-leaving-the-site.md#agents-that-read-content)).
- [ ] Personal data (accounts, form submissions, orders) is outside the account's role unless the task needs it.
- [ ] The agent platform's transcripts are covered by your retention rules.

## Writes

- [ ] The agent starts read-only.
- [ ] If it writes: the write scope or policy granted on purpose, for one subtree and class, rehearsed on a test
  installation ([A content import workflow](../agentic-workflows/workflow-content-import.md)).
- [ ] Writes that need human review go into a section with an approval workflow, not straight to visitors
  ([Keeping a person in the loop](../agentic-workflows/keeping-a-person-in-the-loop.md)).
- [ ] Untrusted input (feeds, user content, web pages) is handled as data
  ([Untrusted data and prompt injection](../agentic-workflows/untrusted-data-and-prompt-injection.md)).

## Records and revocation

- [ ] You know where its actions are recorded: the audit trail under its own account
  ([The audit trail for AI](../governance/audit-trail-for-ai.md)).
- [ ] Someone reads them after each automated run.
- [ ] You know how to stop it at once: disable the account, revoke the key or token
  ([Incident response for AI](../governance/incident-response-for-ai.md)).

## Related pages

- [AI and agent checklists](README.md)
- [Agentic workflows](../agentic-workflows/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
