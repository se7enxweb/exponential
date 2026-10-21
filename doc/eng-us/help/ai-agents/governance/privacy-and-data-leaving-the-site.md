---
title: Privacy and data leaving the site
audience: ai-agents, admin, businesses, government
category: ai-agents
order: 81
summary: What content leaves an Exponential installation when AI is used and how to control it - what Exponential Intelligence sends and never sends, provider region and retention, data policy rules per section and class, images and web tools, what the installation itself keeps and for how long, the EI: Summary page for a data protection officer, and the same questions for agents that read content through the REST API or expservices.
keywords: privacy, data protection, GDPR, data leaving the site, data policy, region, retention, zero data retention, SectionRegions, ClassRegions, ZeroRetention, EI: Summary, data protection officer, retention settings, transcripts, personal data
type: help
---
# Privacy and data leaving the site

This page is for data protection officers, administrators and managers who must answer "where does our content go
when we use AI?". It explains what is sent, how to limit it, what is kept, and how to document it. It is not legal
advice; whether a provider's terms meet your obligations is your organization's decision.

## Contents

1. [Nothing leaves until you configure it](#nothing-leaves-until-you-configure-it)
2. [What the assistant sends](#what-the-assistant-sends)
3. [Region and retention of each provider](#region-and-retention-of-each-provider)
4. [Data policy rules](#data-policy-rules)
5. [Images and web tools](#images-and-web-tools)
6. [What the installation keeps](#what-the-installation-keeps)
7. [The EI: Summary page](#the-ei-summary-page)
8. [Agents that read content](#agents-that-read-content)

## Nothing leaves until you configure it

Exponential Intelligence sends nothing anywhere until an administrator adds a provider key or a local model. The REST
API and expservices call no provider at all; they answer the agent you connect, and that agent's provider receives
what the agent reads.

## What the assistant sends

| Sent to the model's provider | Never sent |
|---|---|
| The instructions, with the open node's name, class and path | The editor's name, login or e-mail |
| The editor's messages and instructions | Passwords and session data |
| Names and field values of content the editor may read, as the tools return them | User account fields and files |
| Class definitions | The content of other users' drafts |
| In the edit form: the object's fields as they are in the form (about 30 KB at most) | The provider key (it goes only in the request header to the endpoint) |
| For alt text: the image of that field, scaled down, inside the request | |

Source: the extension's [security page](https://github.com/se7enxweb/expai/blob/main/doc/security.md).

## Region and retention of each provider

Each provider declares where it processes requests and how long it keeps them:

| Setting | Values | Meaning |
|---|---|---|
| `Region` | `us`, `eu`, `local` | `local`: the data never leaves the server |
| `Retention` | `none`, `zdr`, `30d`, `unknown` | `zdr`: zero data retention agreed; `none`: keeps nothing (local) |
| `TermsCheckedAt` | a date | The day someone read the provider's terms; "not checked" until set |

These values are **declarations you make** after reading the provider's terms and your contract. The values shipped
for example providers are a starting point, not a legal statement. They are recorded with every request.

## Data policy rules

```ini
[DataPolicy]
DefaultRegions[]                  # empty: every region
SectionRegions[6]=eu;local        # section 6 only to EU or local providers
ClassRegions[contract]=local      # contracts never leave the server
ZeroRetention=disabled
ZeroRetentionSections[]=7         # section 7 only to providers that keep nothing
ZeroRetentionClasses[]
```

The requirement of a content object is the intersection of the default regions and its section's and class's rules. A
provider with an unknown region meets no region requirement. Without any rule, everything may go to every active
provider ([extension documentation: data policy](https://github.com/se7enxweb/expai/blob/main/doc/data-policy.md)).

Where it is enforced: before the first request in the panel; on every tool (refused objects are tool errors, lists
leave them out and count them as withheld); in the edit form; for alt text; for service tool answers; and again when a
conversation moves to another model. When a model's provider may not take the content, the editor is told and can
choose another model.

For content that must never be sent anywhere, the [veto rules](../expai/master-switch-and-veto.md#the-veto-rules) can
refuse requests by section, class or subtree outright.

## Images and web tools

- **Images** follow the section and class rules of their object, and can be held to stricter rules
  (`[AltTextSettings] ImageRegions[]`, `ImageZeroRetention`). The request log never keeps the pixels.
- **web_fetch** sends nothing but the URL to an allowlisted site. **web_search** sends its query to a search service,
  whose region and retention are checked against the node's policy
  ([Web and history tools](../expai/web-and-history-tools.md)).

## What the installation keeps

| What | How long |
|---|---|
| The usage log: user id (no names or e-mail), node, provider, model, tokens, cost, tools, errors; not the texts | `[UsageSettings] RetentionDays` |
| Full request texts | Only with `[UsageSettings] LogRequests=enabled`, a debugging setting to leave off in production |
| The draft log's short request summary | Blanked after `[RetentionSettings] PromptSummaryKeepDays` (365) by the cron part `expai` |
| Conversations (messages, answers, tool results) per user and node | Until "New conversation", or after `ConversationKeepDays` (365) without change; threads after `ThreadKeepDays` |
| The audit channel `ai`: actions and metadata, never a prompt, answer or key | Per the channel's settings (90 days live, 730 archived by default) |

Conversations of a user who no longer exists are removed by the next cron run with `[RetentionSettings] Mode=delete`.

## The EI: Summary page

**Setup > EI: Summary** (`expai/summary`) is one printable page for a data protection officer: every provider with
host, region, retention, the date the terms were checked and **where the key comes from** (never the key), who may
use it, this month's use; the lanes and their retention; the data policy; the limits and budget; the tools that reach
outside the site; what the site keeps; and every role with an expai policy. Two free texts (`[SummarySettings]
Contact` and `Note`) can name who answers questions and your record of processing. It is read from the live settings
every time, so it never goes stale. "Print or save as PDF" gives a copy for your records.

The policy `expai/summary` alone opens it, without any content access.

## Agents that read content

For an agent that reads through the REST API or expservices, the agent's own provider receives whatever the agent
reads. The data policy of Exponential Intelligence does not apply there; the account's permissions are the only
filter. Therefore:

- give the agent's account read access only to content that may go to that provider;
- leave personal data (user accounts, form submissions, orders) out of its role unless the task needs it;
- treat the agent platform's transcripts as data your organization holds;
- use a development copy without real personal data for development work
  ([Credentials for agents](../agentic-workflows/credentials-for-agents.md)).

## Related pages

- [Personal data in Exponential](../../businesses/security/personal-data-in-exponential.md)
- [GDPR basics](../../businesses/security/gdpr-basics.md)
- [Local models](../expai/local-models.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
