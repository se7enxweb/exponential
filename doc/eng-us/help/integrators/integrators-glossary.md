---
title: Integrators' glossary
audience: integrator, developer, project-manager
category: integration
order: 530
summary: The terms integrators meet when connecting Exponential with other systems - Exponential's own concepts (class, object, node, remote id, siteaccess, operation, trigger, cronjob part) and general integration terms (outbox, idempotency, watermark, dead letter) - each with a link.
keywords: glossary, terms, definitions, remote id, node, object, class, siteaccess, operation, trigger, workflow, cronjob part, policy, role, API key, expservices, ezjscore, outbox, idempotency, watermark, dead letter, service account
type: help
---
# Integrators' glossary

Terms in alphabetical order. Exponential's own terms are explained as an integrator needs them; the full product
glossary is [Glossary](../../glossary.md), and developers have [the glossary for extension developers](../extension-development/part-8-reference/glossary-for-extension-developers.md).

## A to D

| Term | Meaning |
|---|---|
| **Access extension** | An extension activated for some siteaccesses only (`ActiveAccessExtensions`); scripts must name such a siteaccess with `-s` |
| **API key (personal)** | A credential `expk_...` that a user makes for one program; scoped, revocable, sent as `Authorization: Bearer` ([page](personal-api-keys-for-integrations.md)) |
| **Audit trail** | The record of what happened in the installation, with sinks for syslog, mail and a signed webhook ([page](outgoing-webhooks-from-the-audit-trail.md)) |
| **Class (content class)** | The definition of a kind of content: its attributes and their datatypes ([mapping](mapping-content-classes-to-external-data.md)) |
| **Collection** | One sending of a form (information collection) ([page](forms-and-collected-information.md)) |
| **Content job** | A large operation (remove, copy, move, hide a subtree) run in batches in the background |
| **Cronjob part** | A named group of scripts run by `runcronjobs.php <part>` ([page](cronjobs-for-integrations.md)) |
| **Datatype** | The type of an attribute (`ezstring`, `ezimage` ...); its `fromString()` defines the import format |
| **Dead letter** | Where an item goes after its last failed attempt, for a person to look at |
| **Delta run** | A run that moves only what changed since the last watermark |
| **Dry run** | A run that reports what it would change and changes nothing |

## E to N

| Term | Meaning |
|---|---|
| **Event listener** | A function attached to a kernel event of `ezpEvent` (`site.ini [Event] Listeners[]`) ([page](event-listeners-for-integrations.md)) |
| **`expservices`** | The extension that offers the administration's functions as JSON services under `/ezjscore/call/` ([page](remote-services-with-expservices.md)) |
| **Extension** | The unit an integration is built and shipped as ([page](building-an-integration-as-an-extension.md)) |
| **ezjscore server function** | A static PHP method callable at `/ezjscore/call/<server>::<function>` ([page](ezjscore-server-functions-for-integrations.md)) |
| **Idempotency** | Running something twice has the same effect as once; the basis of safe retries ([page](idempotent-imports-keyed-by-remote-id.md)) |
| **Inbox** | A table where incoming messages are stored before processing, so duplicates are recognized |
| **Location** | A place of an object in the content tree; same as node |
| **Login handler** | A class that checks a login and password (`standard`, `LDAP`, `textfile`, your own) ([page](login-handlers-ldap-and-textfile.md)) |
| **Node** | A location of an object in the tree, with its own id and URL alias |

## O to R

| Term | Meaning |
|---|---|
| **Object (content object)** | One piece of content of a class, with versions and translations; may have several nodes |
| **Operation** | A named kernel action (publish, move, checkout) run through `eZOperationHandler`, with triggers before and after |
| **Outbox** | A table where changes are recorded inside Exponential and sent later by a separate process ([pattern](integration-patterns-push-pull-batch-real-time.md#outbox-and-inbox)) |
| **Package** | An `.ezpkg` archive with classes, content and files ([page](packages-import-and-export.md)) |
| **Policy** | A right of a role: module, function and limitations (Subtree, Class, Section, Language ...) |
| **Publish operation** | The operation that makes a version current; runs workflows, clears caches, indexes |
| **Remote id** | A string key of an object (up to 100 characters, unique) that an integration sets from the source's id |
| **Role** | A set of policies assigned to users or groups ([page](service-accounts-roles-and-policies.md)) |

## S to Z

| Term | Meaning |
|---|---|
| **Scope** | What a personal API key may be used for: `read`, `publish`, `edit`, `remove` |
| **Service account** | A user made for one integration, with a narrow role |
| **Siteaccess** | A configuration through which the installation is reached (a site, a language, the administration); scripts choose one with `-s` |
| **SSO handler** | A class named in `SingleSignOnHandlerArray[]` that recognizes a user without a login form ([page](single-sign-on-and-social-login.md)) |
| **Trigger** | The link between an operation (before or after) and a workflow ([page](workflows-and-triggers-as-hooks.md)) |
| **Velocity** | The persistent-worker web server of Exponential ([page](velocity-and-performance-for-integrations.md)) |
| **Watermark** | The point up to which data was synchronized (a time, an id) |
| **Workflow** | An ordered list of events that runs on a trigger |
| **Workflow event type** | A class providing one step of a workflow; your integration hook |

## Related pages

- [Glossary](../../glossary.md)
- [Partner glossary](../partners/partner-glossary.md)
- [Integrators' FAQ](integrators-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
