---
title: Modules, views and access control
audience: software-engineer, developer
category: software-engineering
order: 140
summary: How Exponential routes a request to code and decides whether the user may run it - modules, views and policy functions in module.php, class-based views, roles, policies and limitations, the access checks of the dispatch loop, request rules, and the rules a new view must follow.
keywords: module, view, module.php, ViewList, FunctionList, ModuleView, policy, role, limitation, eZRole, eZPolicy, hasAccessTo, PolicyOmitList, request rules, routing, authorization, software engineer
type: help
---
# Modules, views and access control

This page is for engineers who add pages or endpoints, or who review whether a change can be reached by the wrong
user. In Exponential, routing and authorization are declared together: the definition of a view names the permission
it needs. The authoritative rules are in [Kernel architecture](../../standards/architecture/kernel-architecture.md#modules-views-and-functions).

## Contents

1. [Modules, views and functions](#modules-views-and-functions)
2. [Class-based views](#class-based-views)
3. [Roles, policies and limitations](#roles-policies-and-limitations)
4. [The checks of the dispatch loop](#the-checks-of-the-dispatch-loop)
5. [Request rules](#request-rules)
6. [Rules for a new view](#rules-for-a-new-view)

## Modules, views and functions

| Term | Meaning | Example |
|---|---|---|
| Module | a URL namespace | `content`, `user`, `exphelp` |
| View | one page or endpoint of a module | `content/view`, `exphelp/viewer` |
| Function | a permission that views require | `content/read`, `exphelp/read` |

`kernel/<module>/module.php` defines `$Module`, `$ViewList` (for each view: `script`, `params`, `functions`,
`default_navigation_part`, ...) and `$FunctionList` (the policy functions and the limitations each accepts). The
module repositories are `settings/module.ini [ModuleSettings] ModuleRepositories[]`, and only modules listed in
`ModuleList[]` are reachable. An extension adds modules through `ExtensionRepositories[]` and
`extension/<ext>/modules/<module>/module.php` ([Module definition reference](../creators/app-creators/module-definition-reference.md)).

Addresses are usually not typed as module paths: a URL alias such as `/About-us` is translated to
`content/view/full/58` before the module is found ([URLs and aliases](../../guides/urls-and-aliases.md)).

## Class-based views

Since 6.0.15 the code of a kernel view is a class. `kernel/content/view.php` is a one-line entry file that calls
`\Exponential\View\Kernel\Content\View::main()` in `kernel/private/classes/views/content/view.php`, a subclass of
`Exponential\Runnable\ModuleView`. Commands and cronjob parts follow the same pattern. Plain-file views still run, so
extensions are not forced to move ([Commands, cronjob parts and module views as classes](../../bc/6.0/cli_cronjob_view_abstractions.md)).

A view class can be replaced through `site.ini [RunnableSettings] Implementation[]`, and a whole kernel module can be
replaced by an extension ([Extension module override](../../features/6.0/extension-module-override.md)).

## Roles, policies and limitations

| Object | Class | Meaning |
|---|---|---|
| Role | `eZRole` | a named set of policies |
| Policy | `eZPolicy` | grants one module function, optionally limited |
| Limitation | `eZPolicyLimitation`, `eZPolicyLimitationValue` | narrows a policy: by class, section, subtree, owner, language, state ... |
| Assignment | | a role given to a user or a group, optionally limited to a subtree or section |

The limitations a function accepts are declared in the module's `$FunctionList`. The checks are
`eZUser::hasAccessTo( $module, $function )` and `eZUser::hasAccessToView()`; templates use
`fetch( 'user', 'has_access_to', ... )`. Content fetches apply read permissions in SQL through
`eZContentObjectTreeNode`, so a list never shows a node the user may not read. Custom content limitations are
possible through limitation handlers ([Content limitation handlers](../../features/6.0/content-limitation-handlers.md)).

**Access is decided by policies, never by checking a user or group id in code or templates.** Guide:
[Roles and policies](../../guides/roles-and-policies.md).

## The checks of the dispatch loop

For every web request, in order:

1. `eZModule::accessAllowed()`: is the module allowed on this siteaccess?
2. The siteaccess login check: does the user hold `user/login` for this siteaccess?
3. `eZUser::hasAccessToView()`: does the user hold the functions the view names?
4. Request rules, when configured (below).
5. The view itself may check finer permissions (for example `canEdit()` on an object).

Views listed in `site.ini [RoleSettings] PolicyOmitList[]` skip the policy check by design; every entry there is a
public endpoint and deserves review.

## Request rules

Request rules run after URL translation and after the role policies allowed the view, and can allow, deny, redirect,
rewrite or log. They close the gap between a page and its system URL (`/content/view/full/123` against its alias).
Check what the rules decide for any address without changing anything:

```bash
php bin/php/ezrequestrules.php -s site --check
php bin/php/ezrequestrules.php -s site --uri=content/view/full/2
```

See [Request rules](../../features/6.0/request-rules.md) and [View full security](../../bc/6.0/view_full_security.md).

## Rules for a new view

- It **must** name the policy functions it needs in `functions`; a view without them is open to every user who passes
  the siteaccess check.
- It **should** be a `ModuleView` subclass, not a plain script.
- It reads input through `eZHTTPTool`, relies on the form token check for POST requests, and renders through
  `eZTemplate::factory()`.
- A new module **should** add its function to an installer or update script so existing sites get it (for example
  `bin/php/addexphelpreadpolicy.php` for `exphelp/read`).

## Related pages

- [Custom modules and views](../creators/app-creators/custom-modules-and-views.md)
- [Policy functions and limitations](../creators/app-creators/policy-functions-and-limitations.md)
- [Security review checklist](security-review-checklist.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
