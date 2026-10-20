---
title: "Module 6: Modules and views"
audience: trainer, developer
category: training
order: 60
summary: The sixth module of the Developers course. Learners read module.php files, write a view with parameters and a template, protect it with a policy function granted through a role, and handle a form with actions, validation, a redirect and the form token. 3 hours 35 minutes.
keywords: developers course, module 6, module, view, module.php, ViewList, Params, Result, policy, FunctionList, role, single_post_actions, isCurrentAction, redirectTo, ezformtoken
type: help
---
# Module 6: Modules and views

A module view is how an extension gets pages of its own. This module explains how an address reaches a view,
then learners build the view `hello/greet` step by step: parameters and a template, a policy function and a role,
and finally a form with an action, validation and a redirect, protected by the form token.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Duration | 3 hours 35 minutes (with one 10-minute break) |
| Lessons | 4 |
| Level | Intermediate |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 6.1: How modules and views work](how-modules-and-views-work.md) | 45 minutes | Split addresses, read `module.php`, follow a view to its class |
| [Lesson 6.2: A view of your own](a-view-of-your-own.md) | 55 minutes | Write `hello/greet` with parameters, a template and a breadcrumb |
| [Lesson 6.3: Policies for your views](policies-for-your-views.md) | 50 minutes | Declare `greet` as a policy function, grant it to the Anonymous role |
| [Lesson 6.4: Forms and actions in a view](forms-and-actions.md) | 55 minutes | Post a form, validate, redirect, see the form token at work |

## Preparing this module

- Each learner has the `hello` module from lesson 5.4 and an administrator account on their own training
  installation; lesson 6.3 changes the Anonymous role, which must not be shared between learners.
- Know how your training site is matched (by path such as `/site/`, or by host) so learners type the right
  addresses.
- Keep a finished `module.php`, `greet.php` and `greet.tpl` at hand for learners who fall behind.
- Rehearse lesson 6.4 step 8 in the browser you will use on the projector: removing a field with the developer
  tools differs slightly between browsers.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 6.1 | 45 |
| Lesson 6.2 | 55 |
| Break | 10 |
| Lesson 6.3 | 50 |
| Lesson 6.4 | 55 |
| **Total** | **215** |

## Related pages

- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
