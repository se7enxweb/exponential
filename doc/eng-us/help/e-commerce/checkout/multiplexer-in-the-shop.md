---
title: Multiplexer in the shop
audience: ecommerce, website-creator, app-creator
category: e-commerce
order: 46
summary: Using the Multiplexer workflow event on shop triggers in Exponential - what it matches (sections, classes, excluded user groups, languages), what it can see on each shop trigger, practical uses such as skipping payment for staff, and its limits.
keywords: multiplexer, ezmultiplexer, eZMultiplexerType, conditional workflow, sub-workflow, user groups, sections, classes, shop triggers, staff orders, addtobasket, checkout
type: help
---
# Multiplexer in the shop

This page is for developers and administrators who want a shop workflow to run only in some cases. The **Multiplexer**
event (`ezmultiplexer`, `kernel/classes/workflowtypes/event/ezmultiplexer/ezmultiplexertype.php`) runs another workflow
when its conditions match. It was designed for publishing, so on shop triggers it can see less than you might expect.
This page says exactly what works.

## Contents

1. [What the multiplexer checks](#what-the-multiplexer-checks)
2. [What it sees on shop triggers](#what-it-sees-on-shop-triggers)
3. [Uses that work](#uses-that-work)
4. [How the sub-workflow runs](#how-the-sub-workflow-runs)
5. [Limits](#limits)

## What the multiplexer checks

| Condition (stored in the event) | Matches when |
|---|---|
| Sections | The content object's section is in the list, or "Any" is selected |
| Classes | The object's class is in the list, or "Any" is selected |
| User groups | The user is **not** in any of the listed groups: this list excludes |
| Languages | The version's initial language is in the selection (all by default) |
| Version option | First version only, all but the first, or all |
| Workflow to run | The workflow started when everything matches |

If all conditions match, the chosen workflow runs as a child process. If not, the multiplexer accepts and the parent
workflow continues.

## What it sees on shop triggers

The multiplexer looks for an `object_id` (or `node_id`) in the process parameters to find a content object:

| Trigger | Parameters | Object found? | What can be matched |
|---|---|---|---|
| `addtobasket` | `object_id`, `option_list`, `quantity`, `basket_id` | Yes, the product | Section, class, excluded user groups |
| `updatebasket` | `item_count_list`, `item_id_list` | No | Excluded user groups only, with sections and classes set to "Any" |
| `confirmorder` | `order_id` | No | Same |
| `checkout` | `order_id` | No | Same |

Without an object, a section or class condition other than "Any" never matches, so the sub-workflow never runs. On
these triggers, set sections and classes to "Any" and use the user group list.

## Uses that work

| Goal | Trigger | Multiplexer settings | Sub-workflow |
|---|---|---|---|
| Export placed orders to another system, except test orders made by staff | `checkout` after | Sections and classes "Any"; exclude the "Shop staff" group | An export event of your own |
| A check only when products of one class are added | `addtobasket` before | Class "Gift card", sections "Any" | An event of your own that checks the item |
| A check only for products in one section | `addtobasket` before | Section "Restricted", classes "Any" | Same |

> **Note**
> A payment gateway inside a multiplexer's sub-workflow is a different matter: the gateway's payment object belongs to
> the child process, and the provider's callback continues that process. We did not find this combination documented
> or tested in the repository. Keep the payment event directly in the checkout workflow, and if you must branch around
> payment, test the complete path, including the callback, in the provider's test mode.

## How the sub-workflow runs

The child process gets the parent's parameters plus its own workflow id. Its result is passed up: a redirect (a
payment provider) or a template (gateway selection) is passed to the customer as if the parent had produced it; a
deferral to cron makes the parent wait; done means accepted, cancelled and failed mean cancelled and rejected.

## Limits

- One workflow per multiplexer event. For several cases, put several multiplexer events in the parent workflow.
- The user group list excludes; there is no "only these groups" list.
- No condition on order total, country or payment method. For those, write an event of your own
  ([Writing a workflow event type](../../creators/app-creators/writing-a-workflow-event-type.md)).

## Related pages

- [Checkout and workflows](README.md)
- [Workflows on shop triggers](workflows-on-shop-triggers.md)
- [Workflows](../../../guides/workflows.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
