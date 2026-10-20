---
title: Talking points for developers
audience: advocate
category: advocacy
order: 12
summary: Talking points for conversations with PHP developers, about the content model, the stack, Git-first installation, extensions and the RAD tools, the REST API, Velocity, tests and how contributions are treated, each with its source and a command to try.
keywords: talking points, developers, PHP, Composer, Git, extensions, RAD tools, REST API, templates, content model, Velocity, CI, tests, pull requests, contribute
type: help
---
# Talking points for developers

This page is for advocates who talk to developers. Developers trust code, commands and commit history more than
adjectives. Each point gives the sentence, the source, and something to try.

## Contents

1. [What developers want to know](#what-developers-want-to-know)
2. [The points](#the-points)
3. [Commands to offer](#commands-to-offer)
4. [Questions developers ask](#questions-developers-ask)
5. [Do not say](#do-not-say)

## What developers want to know

- "What is the stack, and is it current?"
- "How do I extend it without forking the kernel?"
- "How do I install it, and how do I keep up to date?"
- "Will my pull request be ignored?"

## The points

| Say | Source | Try |
|---|---|---|
| "The content model is data, not code: content classes with attributes, defined in the administration." | [The content model and editing content](../../guides/content-model-and-editing.md) | **Setup > Classes** |
| "Plain stack: PHP 8, Composer, a template language, INI settings, the database of your choice." | [Who is 7x?, The application stack](../who-is-7x.md#the-application-stack) | `composer show se7enxweb/exponential` |
| "PHP 8.0 to 8.5 are supported, and CI runs the tests on 8.1 to 8.5." | [PHP versions](../who-is-the-exponential-foundation.md#php-versions), [CI](../../bc/6.0/ci.md) | Read the CI page |
| "Git first: the recommended way is to install the kernel and every extension as Git clones with their full history." | [Develop with Git](../develop-with-git.md) | `git log` inside `extension/<name>` |
| "Extensions add modules, classes, templates and settings; the RAD tools list 68 extension points and write a working extension for most of them." | [Extensions](../../guides/extensions.md), [RAD tools](../../features/6.0/rad-tools.md) | **Setup > RAD** |
| "A REST API, versions 1 and 2, for apps and other systems." | [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md) | A request in a terminal |
| "Velocity keeps the application loaded between requests; one command deploys a PHP change." | [Velocity](../../features/6.0/velocity-persistent-worker-server.md) | `./console exp:velocity status` |
| "Every pull request is merged, under its author's name. While it is open, the author squashes and rebases it onto main as the reviews ask; after the merge, main is never rewritten." | [How the project grows](../who-is-the-exponential-foundation.md#how-the-project-grows) | Look at merged pull requests on GitHub |
| "Commit messages start with what they do: Added, Updated, Fixed, Removed, Renamed; a hook checks it." | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md), [Quality checks](../../features/6.0/quality-checks.md) | `make hooks` in a clone |
| "The documentation is in the repository, in `doc/`, so a code change and its docs go in one pull request." | [The help system](../../features/6.0/help-system.md) | `ls doc/` |
| "Published versions never change; a fix comes as the next release." | [Releases and version numbers](../who-is-the-exponential-foundation.md#releases-and-version-numbers) | `git tag -l 'v6.0.*' --sort=version:refname` |
| "If you prefer Symfony, the family has Exponential Platform DXP, and Legacy or Nexus with both kernels." | [Knowing the product family](knowing-the-product-family.md) | The repositories under se7enxweb |

## Commands to offer

```bash
# Get the code with its full history
git clone https://github.com/se7enxweb/exponential.git
cd exponential
git log --oneline -10

# A complete site on SQLite in one command (in an installed copy)
./console exp:install --dry-run
./console exp:install

# The help from the command line
./console exp:help search "extension"
```

The install commands are described in [Installing Exponential in one command](../../features/6.0/install-in-one-command.md);
the help commands in [The help system, On the command line](../../features/6.0/help-system.md#on-the-command-line).

## Questions developers ask

| Question | Answer | Source |
|---|---|---|
| "Do I have to learn a special template language?" | Yes, Exponential has its own template language with operators and fetch functions | [Templates and design](../../guides/templates-and-design.md) |
| "Can I override a template from an extension?" | Yes, through design overrides | [Templates and design](../../guides/templates-and-design.md) |
| "Where do I start building an extension?" | Section 6 of the extensions guide, by hand or with the RAD tools | [Extensions, Build your own extension](../../guides/extensions.md#6-build-your-own-extension) |
| "How do I send a fix?" | Fork, branch, commit, pull request | [Develop with Git, Contribute a change](../develop-with-git.md#contribute-a-change-with-a-pull-request) |
| "Is there a coding style?" | PSR standards and the project's coding style, linked from CONTRIBUTING.md | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md) |
| "Can I learn from old books?" | Yes, training books are mirrored and "Learning eZ Publish" is under the GFDL | [Community places](../who-is-the-exponential-foundation.md#community-places) |

## Do not say

- "It is a modern framework like X." Describe what it is; let them judge ([Respecting other projects](respecting-other-projects-and-companies.md)).
- "There are thousands of developers." No source counts them ([Claims you must not make](claims-you-must-not-make.md)).
- "Velocity makes it N times faster." Do not quote speed figures unless you measured them yourself and say how
  ([Talking points: speed and Velocity](talking-points-speed-and-velocity.md)).

## Related pages

- [Develop with Git](../develop-with-git.md)
- [Mentoring a first contribution](mentoring-a-first-contribution.md)
- [Writing a tutorial that works](writing-a-tutorial-that-works.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
