---
title: A content import workflow
audience: ai-agents, developer, app-creator, admin
category: ai-agents
order: 68
summary: A worked example of an agentic import - an agent maps an outside feed onto a content class and writes it into Exponential through the REST API v2 - with a dedicated account limited to one subtree, a key with only the scopes it needs, a test installation, a person who checks the first batch, a reviewed section, and a check of the audit trail.
keywords: content import, feed import, agentic workflow, REST v2 create, publish scope, import account, subtree limitation, test installation, audit trail, review, expservices
type: help
---
# A content import workflow

This page is for developers who let an agent import content, for example items from a partner's feed, into a folder
of the site. It shows a workflow in which every write is limited, rehearsed and checked.

## Contents

1. [The task](#the-task)
2. [Prepare the account](#prepare-the-account)
3. [Choose the interface](#choose-the-interface)
4. [Rehearse on a test installation](#rehearse-on-a-test-installation)
5. [Run it](#run-it)
6. [Check it](#check-it)
7. [Variations](#variations)

## The task

An outside feed lists events. The agent reads each item, maps it onto the fields of the `event` class (title, date,
summary, body) and creates one object per new item in a folder "Partner events".

The feed is **untrusted text**: its fields can contain anything
([Untrusted data and prompt injection](untrusted-data-and-prompt-injection.md)).

## Prepare the account

1. Create an account for the import, in its own user group.
2. Give its role `content/read`, and `content/create` limited to the class `event` and the subtree of "Partner events".
   Add `content/remove` there only if the import should remove items.
3. Check the result before any write. `exp:access:check` answers for read, edit, remove and the other functions of an
   existing node, for example that the account may read the folder but not edit other pages:

   ```bash
   ./console exp:access:check --user=<import login> --node=<Partner events node id>
   ./console exp:access:check --user=<import login> --node=<another page> --function=edit
   ```

   Check the create right itself on the test installation by creating one item as that account
   ([Roles and least privilege](../governance/roles-and-least-privilege.md)).
4. Signed in as that account, make a personal API key with the scope `publish` (and `read`), and a short lifetime
   ([A read-only API key, step by step](a-read-only-api-key-step-by-step.md) shows the same steps for `read`).

## Choose the interface

| Interface | Fits when | Note |
|---|---|---|
| REST v2 `content/node/create` with the import account's key | The agent runs anywhere; items are only ever added | Create and publish ([Agents and the REST API](agents-and-the-rest-api.md#writing-through-rest)) |
| expservices with a token of the import account | You need services beyond content as well | Check the catalog entry and the `write` flag of each service ([Agents and expservices](agents-and-expservices.md)) |

The steps below use REST v2.

## Rehearse on a test installation

Never let a new import write to production first. Use a copy or a fresh test installation, run the whole workflow
there, read the results as an editor would, and only then point the agent at production.

## Run it

1. **Map**: the agent reads the class definition from documentation or a sample object (`GET` on an existing event
   with `ResponseGroups=Fields`) and maps feed fields onto attribute identifiers. Your code, not the model, checks that
   every identifier exists.
2. **Validate in code**: dates parse, required fields are present, text is plain or allowed markup, links use http(s).
   The model proposes; your code decides.
3. **A person reads the first batch**: a few mapped items as plain data, before anything is sent.
4. **Write**: one `POST /api/ezp/v2/content/node/create` per item, with `parentNodeID`, `classIdentifier`,
   `languageLocale` and the attributes. Record the new node ids.
5. **Remember what was imported**: keep the feed id with the new node id in your own store, so a second run creates
   only new items.

## Check it

- Look at the kernel's audit trail for the import account's actions
  ([The audit trail for AI](../governance/audit-trail-for-ai.md)).
- Open a few items in the administration as an editor.
- Compare counts: items in the feed, items sent, `201` answers, objects in the folder.

## Variations

- **People must approve imported items**: import into a section that visitors cannot see and that an approval
  workflow covers ([Keeping a person in the loop](keeping-a-person-in-the-loop.md)).
- **Updates and removals**: give the account `content/edit` or `content/remove` only for the folder, use the matching
  key scopes, and rehearse them separately.
- **Large imports**: send in small batches and stop the run on the first `401` or `403`; those mean a person must look.

## Related pages

- [Choosing an interface for an agent](choosing-an-interface.md)
- [Creating content in code](../../creators/app-creators/creating-content-in-code.md)
- [Before connecting an agent](../checklists/before-connecting-an-agent.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
