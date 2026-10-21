---
title: "The building blocks of an extension: modules, datatypes, operators, workflow events, notifications"
audience: developer
category: books
order: 530
summary: The rest of the book's extending chapter (module definitions, view parameters, actions, navigation, module coding, datatypes, operators, workflow events, notifications, SOAP and RSS) as a map to the developer pages that now cover each.
keywords: module, module.php, ViewList, view parameters, post actions, navigation part, Result, datatype, template operator, workflow event type, notification handler, SOAP, RSS, REST, Learning book
type: help
---
# The building blocks of an extension

The book's fifth chapter goes through the kinds of extension one by one. This page keeps its order, says what each piece
is, and sends you to the page that covers it now. The code in the book is PHP 4 style (`&` references, `include_once`, `EZ_`
constants). Exponential's PHP is 8: classes are autoloaded, constants belong to classes, and no references are needed.

## Modules

A **module** is a set of views reached by an address: `/hello/world` is the view `world` of the module `hello`. A module
lives in `modules/<name>/` of an extension.

- **Module definition** (`module.php`): the module's name, its `$ViewList` (each view with its `script`, the `params` it takes,
  `unordered_params`, `functions`, `default_navigation_part`, and `single_post_actions` or `post_actions`) and its
  `$FunctionList` for permissions ([Modules and views](../extension-development/part-3-extension-developer/modules-and-views.md),
  [Policy functions and access](../extension-development/part-3-extension-developer/policy-functions-and-access.md)).
- **View parameters**: the parts of the address after the view name, in the order `params` names them
  ([View parameters](../extension-development/part-3-extension-developer/view-parameters.md)).
- **View actions and post variables**: a form button's name maps to an action, and `isCurrentAction()` tells the view script
  what was pressed. Forms must carry the form token.
- **View navigation**: `default_navigation_part` chooses which tab of the administration is active.
- **Module coding**: a view script reads its input (`$Params`, `$_POST` through the module's `http` object) and ends by
  filling `$Result`: `content` (the output of a template), `path` (the breadcrumb), and optionally `left_menu`, `title_path`,
  `pagelayout` ([View results and the pagelayout](../extension-development/part-3-extension-developer/view-results-and-the-pagelayout.md)).
  Redirect with `$Module->redirectTo( ... )`, and return errors with `$Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' )`.

## Datatypes

A datatype is a class that knows how to validate, store, display and edit one kind of value, with templates for each of
those. The book's chapter on datatypes builds one by overriding the methods of `eZDataType`. Same pattern now:
[Datatypes](../extension-development/part-3-extension-developer/datatypes.md) and
[Datatype storage, validation and templates](../extension-development/part-3-extension-developer/datatype-storage-validation-and-templates.md).

## Template operators and functions

An operator (`{$x|myoperator}`) is a class registered by a template autoload file. For simple cases, a PHP function in
`template.ini` is enough ([caching in templates](caching-in-templates.md), last section). Real ones:
[Template operators](../extension-development/part-3-extension-developer/template-operators.md),
[Template functions](../extension-development/part-3-extension-developer/template-functions.md).

## Workflow events and triggers

A new **event type** is a class in `eventtypes/event/<type>/` that decides, when it runs, whether the workflow goes on
(`STATUS_ACCEPTED`), waits (`STATUS_FETCH_TEMPLATE_REPEAT`, `STATUS_DEFERRED_TO_CRON_REPEAT`) or stops (`STATUS_REJECTED`)
([workflow event types](../extension-development/part-3-extension-developer/workflow-event-types.md),
[Triggers and workflows for developers](../extension-development/part-3-extension-developer/triggers-and-workflows-for-developers.md)).
The user side is in [workflows and triggers](workflows-and-triggers.md).

## Notifications

A notification handler turns the events the system collects into mails. The statuses the book lists
(`EZ_NOTIFICATIONEVENTHANDLER_EVENT_HANDLED`, `_SKIPPED`, `_UNKNOWN`, `_ERROR`) are the class constants `EVENT_HANDLED`, `EVENT_SKIPPED` and so on of
`eZNotificationEventHandler` today. See [Notifications for developers](../../guides/notifications-developer.md).

## Moving data in and out: SOAP and RSS

The book finishes with web services: SOAP for programs, RSS for feeds. Feeds are still RSS and Atom, from the administration
([RSS feeds](../../guides/rss-feeds.md)). For programs the interface is the REST API, with keys and OAuth
([REST API](../../guides/rest-api-v1-and-v2.md), [API keys](../../guides/api-keys.md)); build against it rather than
adding SOAP.

Next: [extension development practices](extension-development-practices.md).

## Source and license

Adapted from chapter 5, "Extending eZ publish", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged as a map to the current developer pages; folder names, settings and constants checked against Exponential 6; SOAP replaced by the REST API.
