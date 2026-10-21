---
title: Which path fits your task
audience: ai-agents, developer, app-creator, admin, editor
category: ai-agents
order: 6
summary: A short decision guide - answer a few questions about what you want an AI to do with Exponential and get the part to use (Exponential Intelligence, the REST API, expservices, ezjscore or the help on the command line), the account it should run as and the page to read next.
keywords: decision, which tool, path, choose, assistant or agent, REST or expservices, expai, expservices, REST API, read only, write, agentic workflow
type: help
---
# Which path fits your task

This page is for anyone who knows what they want an AI to do but not which part of Exponential to use. Answer the
questions in order; each answer points to one part and to the page to read next.

## Contents

1. [Question 1: who works, a person or a program?](#question-1-who-works-a-person-or-a-program)
2. [Question 2: does it only read, or also write?](#question-2-does-it-only-read-or-also-write)
3. [Question 3: where does the agent run?](#question-3-where-does-the-agent-run)
4. [The answers in one table](#the-answers-in-one-table)
5. [Combinations that work well](#combinations-that-work-well)
6. [What none of the paths gives you](#what-none-of-the-paths-gives-you)

## Question 1: who works, a person or a program?

- **A person in the administration interface** who wants help writing, rewriting, shortening, translating or
  filling fields: use **Exponential Intelligence**. It works as that person, inside the screens they already use,
  and every result is a draft for them to review ([Exponential Intelligence](expai/README.md)).
- **A program or an AI agent** that works on its own or for a developer: go on to question 2.

## Question 2: does it only read, or also write?

| The agent | Use |
|---|---|
| Only reads published content, for an app, a report or another site | The REST API with a key that has only the `read` scope ([A read-only API key, step by step](agentic-workflows/a-read-only-api-key-step-by-step.md)) |
| Reads administration data beyond content (users, caches, cronjobs, tags, the audit) | expservices with a token of an account limited to those services ([Agents and expservices](agentic-workflows/agents-and-expservices.md)) |
| Creates or changes content as part of a workflow | The REST API v2 or expservices with a dedicated account, ideally into a reviewed section ([Keeping a person in the loop](agentic-workflows/keeping-a-person-in-the-loop.md)) |
| Answers questions about how the installation is configured | The console on the server and the documentation ([A diagnosis workflow with the console](agentic-workflows/workflow-diagnosis-with-the-console.md)) |

## Question 3: where does the agent run?

| The agent runs | Consequence |
|---|---|
| On the same server, started by you (a coding assistant in a terminal) | It can use the console (`./console exp:help`, `exp:ini get`, `exp:audit`) and read the files, as the system user you start it as |
| On another machine, over the network | The REST API or expservices over HTTPS with a token |
| Inside the administration, for the signed-in editor | Exponential Intelligence, with its own tools ([Web and history tools](expai/web-and-history-tools.md), [Service tools](expai/service-tools.md)) |

## The answers in one table

| Task | Part | Runs as | Read next |
|---|---|---|---|
| Draft an article from a brief | Exponential Intelligence | the editor | [What Exponential Intelligence does](expai/what-exponential-intelligence-does.md) |
| Keep translations in step with the source language | Exponential Intelligence, translation sync | the editor who approves | [Translation help](expai/translation-help.md) |
| Suggest alt text for images | Exponential Intelligence | the editor | [Alt text for images](expai/alt-text-for-images.md) |
| Produce a report about published content | REST API, `read` scope | a dedicated reader account | [A read-only content report](agentic-workflows/workflow-read-only-content-report.md) |
| Find out why a setting or log entry is what it is | Console and help on the server | the system user | [A diagnosis workflow with the console](agentic-workflows/workflow-diagnosis-with-the-console.md) |
| Import content from a feed | REST v2 or expservices | a dedicated import account | [A content import workflow](agentic-workflows/workflow-content-import.md) |
| Look things up in the documentation | `./console exp:help` or the Markdown files | anyone with the files | [Agent-friendly documentation](agentic-workflows/agent-friendly-documentation.md) |

## Combinations that work well

- **A coding assistant on a project**: `exp:help` for the documentation plus the console for runtime facts
  ([Coding agents on an Exponential project](agentic-workflows/coding-agents-on-an-exponential-project.md)).
- **An editorial team**: Exponential Intelligence with an approval workflow, a data policy and monthly limits
  ([Governance](governance/README.md)).
- **An import pipeline**: a dedicated account with `content/create` limited to one subtree, a test run first, and the
  audit trail to check afterwards ([Roles and least privilege](governance/roles-and-least-privilege.md)).

## What none of the paths gives you

- No path lets an AI publish behind the editors' backs: the assistant writes drafts only, and every other path acts
  as an account whose permissions you set.
- No path makes an AI provider choice for you: Exponential sends nothing to a provider until an administrator
  configures one.
- No path turns content into instructions: content returned to an agent is data
  ([Untrusted data and prompt injection](agentic-workflows/untrusted-data-and-prompt-injection.md)).

## Related pages

- [What exists today](what-exists-today.md)
- [Choosing an interface for an agent](agentic-workflows/choosing-an-interface.md)
- [AI, agents and agentic: FAQ](ai-agents-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
