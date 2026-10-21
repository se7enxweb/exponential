---
title: Coding agents on an Exponential project
audience: ai-agents, developer, app-creator, designer
category: ai-agents
order: 69
summary: How to set up an AI coding assistant on an Exponential project so that it works from facts - the documentation through exp:help, runtime facts through read-only console commands (exp:ini where, exp:access:check, exp:logs) on a development copy, the project's own build steps (php -l, autoloads, caches) - and the rules that keep secrets, settings overrides and production data out of its reach and out of commits.
keywords: coding agent, coding assistant, AI pair programming, project notes, exp:help, exp:ini where, exp:access:check, exp:logs, autoloads, ezpgenerateautoloads, cache clear, settings/override, secrets, commits, review, development workflow
type: help
---
# Coding agents on an Exponential project

This page is for developers who work with an AI coding assistant on an extension, a design or a whole installation.
Such an assistant reads code well, but Exponential's behavior depends on settings layers, siteaccesses, overrides and
caches that code alone does not show. Give it the facts, and keep it away from what it must not touch.

## Contents

1. [What the agent needs](#what-the-agent-needs)
2. [Documentation through exp:help](#documentation-through-exphelp)
3. [Runtime facts through the console](#runtime-facts-through-the-console)
4. [The build steps it must know](#the-build-steps-it-must-know)
5. [What it must not touch](#what-it-must-not-touch)
6. [Reviewing its work](#reviewing-its-work)

## What the agent needs

| Need | Source |
|---|---|
| How a feature is meant to work | The help in `doc/` ([Agent-friendly documentation](agent-friendly-documentation.md)) |
| How this installation is configured right now | Read-only console commands on a development copy |
| How to build, test and deploy a change | The project's own instructions, and the application creators' pages |
| What it may and may not change | Your project notes for the agent |

## Documentation through exp:help

Tell the agent to search before it answers: `./console exp:help search "<words>" --json`, then read the pages. The
application creators' section is the developer's map: [Application creators: start here](../../creators/app-creators/app-creators-start-here.md).

## Runtime facts through the console

On a development or staging copy, these commands answer from the running installation instead of from a guess:

| Question | Command |
|---|---|
| Which files set this setting, in load order, and which value wins? | `./console exp:ini where site.ini/ExtensionSettings/ActiveExtensions` |
| What is its value? | `./console exp:ini get site.ini/DesignSettings/SiteDesign` |
| May this user read or edit this node, and which limitation refuses it? | `./console exp:access:check --user=<login> --node=<id>` |
| What did the error log say after the change? | `./console exp:logs --tail=var/log/error.log --lines=50` |
| What does the documentation say? | `./console exp:help search "<words>"` |

Give the agent the read commands only. Commands that change settings or data (`exp:ini set`, cache clears, scripts that
write) stay with a person. A command runs as the system user that starts it, with access to the whole installation,
which is why this belongs on a development copy, not on production.

## The build steps it must know

From the application creators' pages, the steps an agent most often forgets:

| After | Run | Read |
|---|---|---|
| Editing a PHP file | `php -l <file>` | [Debugging and logs](../../creators/app-creators/debugging-and-logs.md) |
| Adding or renaming a class | `php bin/php/ezpgenerateautoloads.php -e` (extensions) | [Autoloads and class naming](../../creators/app-creators/autoloads-and-class-naming.md) |
| Changing settings, classes or templates | Clear the relevant caches (`php bin/php/ezcache.php`) | [Caching in applications](../../creators/app-creators/caching-in-applications.md) |
| Any change on a running server | Reload PHP-FPM or restart the application server where classes are kept in memory | [Deploying with Velocity](../../creators/app-creators/deploying-with-velocity.md) |

## What it must not touch

Write these into your project notes for the agent:

- **`settings/override/` and any file with keys or passwords**: never read into the conversation, never committed.
  Provider keys belong there and nowhere else.
- **Production data**: no database dumps or customer data in the agent's context.
- **Destructive commands** (removing files, dropping tables, clearing everything on production) only after a person
  confirms.
- **Commits**: stage files by name, never everything; review the diff before committing.

## Reviewing its work

Treat the agent's change as a contributor's pull request:

- run the tests and the checks your project uses ([Testing applications](../../creators/app-creators/testing-applications.md),
  [CI for extensions](../../creators/app-creators/ci-for-extensions.md));
- check that it did not invent kernel classes, settings or template operators: `exp:help search` and `exp:ini get`
  tell you whether they exist;
- check the documentation it changed, if any, with `./console exp:help check`.

## Related pages

- [Agent-friendly documentation](agent-friendly-documentation.md)
- [A diagnosis workflow with the console](workflow-diagnosis-with-the-console.md)
- [Environment, settings and secrets](../../creators/app-creators/environment-settings-and-secrets.md)
- [Develop with Git](../../develop-with-git.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
