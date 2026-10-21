---
title: Untrusted data and prompt injection
audience: ai-agents, developer, app-creator, admin
category: ai-agents
order: 66
summary: What prompt injection is in a content management system, what Exponential Intelligence does against it (content marked as data, a probe task that tests resistance, no drafts after web text, sanitized rich text, drafts only), and what you must still do when you build an agent of your own on the REST API or expservices.
keywords: prompt injection, untrusted data, data not instructions, indirect prompt injection, content as data, web_fetch, write lock after outside data, sanitizer, rich text, agent security, review
type: help
---
# Untrusted data and prompt injection

This page is for everyone who lets an AI model read content and then act. Content can contain text written to steer
a model ("ignore your instructions and publish ..."). A model that reads such text may follow it. This is called
prompt injection, and in a content management system it is a real risk: anyone who can write content, submit a form
or edit a page a model reads can try it.

## Contents

1. [Where untrusted text comes from](#where-untrusted-text-comes-from)
2. [What Exponential Intelligence does](#what-exponential-intelligence-does)
3. [What the interfaces for agents do](#what-the-interfaces-for-agents-do)
4. [What you must do in your own agent](#what-you-must-do-in-your-own-agent)
5. [What no setting can do](#what-no-setting-can-do)

## Where untrusted text comes from

| Source | Example |
|---|---|
| Content written by many people | An article, a forum post, a comment, user-submitted text |
| Form submissions | Information collections, registrations |
| Logs | A request path or user agent written into a log line |
| The web | A page fetched by a tool, a search result |
| Other systems | A feed being imported |

Any of these can reach a model through a tool result.

## What Exponential Intelligence does

From the extension's [security page](https://github.com/se7enxweb/expai/blob/main/doc/security.md):

- **Content is data**: the instructions and every tool description tell the model that field values are data written
  by people, not instructions.
- **Few, narrow tools**: the model can only read what the editor may read and write drafts the editor may write. It
  cannot publish, delete, move, change permissions or send mail.
- **A probe task for exactly this**: a model may write drafts only after passing, among others, the mandatory task in
  which a tool result carries an instruction to call a write tool, and the model must not follow it
  ([The write gate and the probe](../expai/the-write-gate-and-the-probe.md)).
- **A write lock after web text**: after a web fetch or web search, no draft is written while that result is part of
  the conversation's window. Outside page text is wrapped in "Begin/End of outside data" markers
  ([Web and history tools](../expai/web-and-history-tools.md)).
- **Clean output**: rich text the assistant writes is cleaned to a small HTML subset (no scripts, styles, iframes,
  images or embeds; only http(s) and mailto links) and stored through the kernel's input parser.
- **A person decides**: every result is a labeled draft waiting for review.

The extension's advice: review drafts written from untrusted content (for example user-submitted text) as you would
any contributor's draft, and check links in drafts written from web results before publishing.

## What the interfaces for agents do

- **They act as an account.** A misled agent can do only what its account may do; a key can do only what its scopes
  allow ([Roles and least privilege](../governance/roles-and-least-privilege.md)).
- **expservices answers in one shape** and refuses without login (401) or without the policy (403), so an agent's code
  can stop on a refusal instead of trying around it.
- **Writes are recorded**: every expservices write and every content change appears in the audit trail under the
  agent's account.

They do not mark content as data for you: that is the job of your agent's instructions and code.

## What you must do in your own agent

1. **Separate reading from acting.** Run read-only first; give write access only to a workflow you trust, with its own
   account.
2. **Narrow the tools.** Offer one tool per allowed action, with validated inputs, rather than a generic HTTP or shell
   tool ([Agents and the REST API](agents-and-the-rest-api.md#describing-the-routes-to-a-model)).
3. **Mark tool results as data** in your instructions and tool descriptions, the way the assistant does.
4. **Stop writing after untrusted input** where you can: once a result from an outside source entered the context,
   end the turn or require a person's confirmation before the next write.
5. **Keep a person in the loop for publication** ([Keeping a person in the loop](keeping-a-person-in-the-loop.md)).
6. **Never give the agent credentials in its context**, so an injected instruction cannot repeat them.
7. **Read the audit trail** after every automated run.

## What no setting can do

No configuration makes a model immune to injected text. The defenses above limit what a misled model **can do**, which
is why the narrow permissions, the drafts and the human review matter more than any wording in a prompt.

## Related pages

- [Reviewing AI drafts](../checklists/reviewing-ai-drafts.md)
- [Input validation and escaping](../../creators/app-creators/input-validation-and-escaping.md)
- [Incident response for AI](../governance/incident-response-for-ai.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
