---
title: A read-only content report
audience: ai-agents, developer, app-creator, editor
category: ai-agents
order: 70
summary: A worked example of a safe agentic workflow - an agent with a read-only API key walks a folder through the REST API, reads the fields of each page, and writes a report for editors (missing summaries, outdated dates, pages without alt text) - with the tools to define, the limits to set and why nothing can change.
keywords: content report, content audit, read only agent, REST API, read scope, list children, fields, worked example, agentic workflow, editors, suggestions
type: help
---
# A read-only content report

This page is for developers who want an agent to help editors without being able to change anything. The agent reads
published content through the REST API with a read-only key and produces a report. Editors decide what to do with it.

## Contents

1. [The task](#the-task)
2. [The setup](#the-setup)
3. [The tools the model gets](#the-tools-the-model-gets)
4. [The run](#the-run)
5. [The report](#the-report)
6. [Why this workflow is safe](#why-this-workflow-is-safe)

## The task

An editorial team wants a monthly report on a "News" folder: articles without a summary, articles whose text mentions
a date that has passed, and very short articles. The report goes to the team as a list with links.

## The setup

- An account "Report agent" with `content/read` limited to the News subtree.
- A personal API key of that account with only the `read` scope and a lifetime of 30 days
  ([A read-only API key, step by step](a-read-only-api-key-step-by-step.md)).
- The agent's code holds the key; the model never sees it.

## The tools the model gets

| Tool | Input | Calls |
|---|---|---|
| `list_children` | `node_id`, `offset`, `limit` (at most 25) | `GET /api/ezp/v1/content/node/<node_id>/list/offset/<n>/limit/<n>` |
| `get_fields` | `node_id` | `GET /api/ezp/v1/content/node/<node_id>/fields` |
| `children_count` | `node_id` | `GET /api/ezp/v1/content/node/<node_id>/childrenCount` |

No write route, no other host, no free URL. The tool descriptions tell the model that field values are text written by
people, data and not instructions ([Untrusted data and prompt injection](untrusted-data-and-prompt-injection.md)).

## The run

1. The agent asks for the number of children of the News folder, then pages through them 25 at a time.
2. For each article it reads the fields and notes: an empty summary, dates in the text, the length of the body.
3. Checks that need no judgment (empty field, length) are better done by your code than by the model; the model adds
   what needs reading, such as "the text says the event is next Friday, but it was published two years ago".
4. A `403` or `404` for an item is recorded in the report and skipped; a `401` stops the run, because the key needs a
   person's attention.

## The report

```text
News report, <date>
Missing summary (4): <title> (<node id>), ...
Probably outdated (2): <title>: the text announces <event> for <date>, ...
Very short (1): <title>, 40 words
Read: 112 articles; refused: 0; not found: 0
```

Editors open the items and fix them, by hand or with Exponential Intelligence in the Intelligence tab
([What Exponential Intelligence does](../expai/what-exponential-intelligence-does.md)).

## Why this workflow is safe

- The key has only the `read` scope; even a misled model cannot create, edit or remove anything.
- The account reads only the News subtree.
- Every change is made by an editor, under the editor's own name.

What remains to decide: whether the content of the News folder may go to the agent's model provider
([Privacy and data leaving the site](../governance/privacy-and-data-leaving-the-site.md#agents-that-read-content)).

## Related pages

- [Agents and the REST API](agents-and-the-rest-api.md)
- [Keeping a person in the loop](keeping-a-person-in-the-loop.md)
- [Headless Exponential](../../creators/app-creators/headless-exponential.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
