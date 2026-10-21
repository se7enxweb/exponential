---
title: Permissions for AI features
audience: ai-agents, admin
category: ai-agents
order: 31
summary: The expai policies (write, vector, log, settings, model_use, model_manage, lane_fast, chain, web, translation_sync, summary, services) and their limitations, how they combine with the editor's own content permissions, and role setups for editors, translators, auditors and data protection officers.
keywords: permissions, policies, roles, expai/write, expai/vector, expai/model_use, expai/model_manage, expai/web, expai/summary, expai/log, limitations, ezjscore/call, least privilege, editor role
type: help
---
# Permissions for AI features

This page is for administrators who decide who may use Exponential Intelligence and how far. Two layers decide: the
**expai policies** say which features a user gets; the user's **own content policies** say what the assistant may
read and write for them. The assistant never adds rights of its own.

## Contents

1. [Two layers](#two-layers)
2. [The policies](#the-policies)
3. [What runs as the editor](#what-runs-as-the-editor)
4. [Role setups that work](#role-setups-that-work)
5. [Setting up a role](#setting-up-a-role)

## Two layers

| Layer | Decides | Example |
|---|---|---|
| expai policies | Which features a user sees and may call | `expai/write` shows the Intelligence tab |
| Content policies | What the assistant may read and write as that user | `content/create` for the article class below a folder |
| On top of both | The data policy (which content may go to which provider) and the write gate (which models may write) | [Privacy and data leaving the site](../governance/privacy-and-data-leaving-the-site.md) |

In a new installation, only the Administrator role holds the expai policies. Anonymous users and members without the
policy see nothing of the assistant.

## The policies

From the extension's [permissions page](https://github.com/se7enxweb/expai/blob/main/doc/permissions.md):

| Policy | Allows | Limitation |
|---|---|---|
| `expai/write` | The Intelligence tab, the edit form controls, the dashboard (own usage), the own log, the Missing alt text report | none |
| `expai/vector` | Vector and its threads (with `expai/write`) | Subtree, Section, Scope, Review |
| `expai/chain` | Starting and acting on AI chains (with `expai/write`) | Chain |
| `expai/translation_sync` | Check translations, Approve, Translate in full, Skip (with `expai/write`) | none |
| `expai/model_use` | Models of providers outside `ProvidersWithoutPolicy[]` | Provider |
| `expai/model_manage` | Setup > EI: Models: providers, key presence, probe results, the local model server | none |
| `expai/web` | The web and history tools in the panel | Tool (`web_fetch`, `web_search`, `project_history`) |
| `expai/services` | Read services of expservices that opted in for the assistant | Domain |
| `expai/lane_fast` | Asking for the fast (premium) lane | none |
| `expai/log` | Everyone's requests and versions, the site view of the dashboard, budgets | none |
| `expai/settings` | Where the site default is set; resuming held jobs | none |
| `expai/summary` | The printable EI: Summary page; needs no other expai policy and no content access | none |

The browser reaches the assistant through `ezjscore/call`, so editors also need `ezjscore/call` (without limitation, or
with a function list that contains `expai`). Many installations give that to all signed-in users through the Member
role.

## What runs as the editor

- **Reading** goes through the kernel's checks for the current user: read access on nodes and objects, the user's
  limitations for children and siblings, the creatable classes.
- **Creating** checks `content/create` on the parent for that class, parent class and language, with every limitation
  the role has. The object is created in the parent's section, owned by the editor.
- **Changing** checks `content/edit` in that language.
- **Keep, Send and Discard** work only on versions the assistant wrote for this editor that are still the editor's
  own drafts.

The assistant never uses another account, never switches the current user and has no setting to publish on its own.

## Role setups that work

| Role | Policies |
|---|---|
| Editor who may use the assistant | `expai/write`, `ezjscore/call` (function list `expai`), plus the editor's usual content policies |
| Editor for one section, bulk work always reviewed | Add `expai/vector` with Subtree or Section, Scope `node`, Review `staged` |
| Translator | `expai/write`, `expai/translation_sync`, `content/edit` limited to the target languages |
| AI auditor | `audit/read` limited to the channel `ai`, and `expai/log` if they should see the usage log |
| Data protection officer | `expai/summary` only (add `exphelp/read` to read the help) |
| Administrator of providers | `expai/model_manage`, `expai/settings` |

Give `expai/web` and `expai/services` only where they are needed: they bring more data into the conversation, and
web results are outside text
([Untrusted data and prompt injection](../agentic-workflows/untrusted-data-and-prompt-injection.md)).

## Setting up a role

1. Setup > Roles and policies > the role > New policy.
2. Module `expai`, the function, and the limitation if it has one.
3. Assign the role to the group of editors, optionally limited to a subtree.
4. Sign in as a test editor and check that the Intelligence tab appears and does what you expect.

The general guide: [Roles and policies](../../../guides/roles-and-policies.md).

## Related pages

- [Roles and least privilege](../governance/roles-and-least-privilege.md)
- [Access control in code](../../creators/app-creators/access-control-in-code.md)
- [The master switch and the veto hook](master-switch-and-veto.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
