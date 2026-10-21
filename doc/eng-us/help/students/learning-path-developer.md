---
title: "Learning path: developer"
audience: student
category: learning
order: 7
summary: A step-by-step learning path for future Exponential developers, built from existing help pages and guides - a development installation from Git clones, the content model, the template language, settings, fetch functions, extensions and modules, the RAD tools, the REST API, tests and contributing - with a practice task and a check for every step.
keywords: learning path, developer, PHP, git, template language, extension, module, content class, datatype, REST API, RAD, tests, contributing, track
type: help
---
# Learning path: developer

This path is for learners who will build on Exponential with PHP: extensions, modules, templates, content classes
and integrations. You should know basic PHP and be able to use a terminal. Git is taught along the way. Finish the
[editor path](learning-path-editor.md) first; it shows you what the code you write is for.

## Contents

1. [The steps](#the-steps)
2. [Going further](#going-further)
3. [Where the path leads next](#where-the-path-leads-next)

## The steps

### Step 1: A development installation from Git clones (2 hours)

- Read: [Develop with Git](../develop-with-git.md) steps 1 and 2,
  [Setting up a development installation](../extension-development/part-1-from-user-to-developer/setting-up-a-development-installation.md).
- Practice: [Installing from Git clones for study](installing-from-git-clones-for-study.md).
- Check: what does `git switch -c my-site` give you, and why does "HEAD detached" in an extension not mean anything
  is broken?

### Step 2: Reading the code you have (1 hour)

- Read: [Reading the code you have](../extension-development/part-1-from-user-to-developer/reading-the-code-you-have.md),
  [What an extension is](../extension-development/part-1-from-user-to-developer/what-an-extension-is.md).
- Practice: use `git log --oneline -10 -- <file>` and `git blame <file>` on one kernel file
  ([Develop with Git](../develop-with-git.md#find-out-why-and-when)).

### Step 3: The content model (2 hours)

- Read: [Understanding the content model](understanding-the-content-model.md),
  [The content model and editing content](../../guides/content-model-and-editing.md) sections 1 to 3.
- Practice: [project 1](project-01-a-content-class-of-your-own.md).
- Check: what is the difference between a content object and a node?

### Step 4: The template language (4 hours)

- Read: [Learning the template language](learning-the-template-language.md),
  [Templates and design](../../guides/templates-and-design.md) sections 1 to 6.
- Practice: [project 3](project-03-a-template-override.md).
- Check: why does `{$node.name|wash}` use `wash`?

### Step 5: Settings, siteaccesses and the console (2 hours)

- Read: [Understanding siteaccesses and settings](understanding-siteaccesses-and-settings.md),
  [Settings scopes and their order](../extension-development/part-2-builder/settings-scopes-and-their-order.md),
  [The exp:ini command](../extension-development/part-2-builder/the-exp-ini-command.md).
- Practice: read one setting with `exp:ini where` and explain which file wins.

### Step 6: Extensions and modules (4 hours)

- Read: [Extensions](../../guides/extensions.md) section 6.1, [RAD tools](../../features/6.0/rad-tools.md).
- Practice: [project 8](project-08-your-first-extension.md).
- Check: a module view without a `'functions'` entry has what kind of access control? (None: anyone who can reach
  the site can open it, as the extensions guide warns.)

### Step 7: The REST API (2 hours)

- Read: [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md), [Personal API keys](../../guides/api-keys.md)
  sections 1 to 3.
- Practice: [project 7](project-07-reading-content-over-the-rest-api.md).
- Check: which versions answer reads, and which answer writes?

### Step 8: Search and roles from the developer's side (2 hours)

- Read: [Roles, policies and unactivated users](../../guides/roles-and-policies.md#for-developers),
  the trainers' [Search](../trainers/search/README.md) course, module 1.
- Practice: [project 6](project-06-search-that-finds-the-right-things.md).

### Step 9: Quality and contributing (2 hours)

- Read: [Quality checks](../../features/6.0/quality-checks.md), [Coding standards](../../standards/coding-standards.md),
  [Contributing code](../supporters/contributing-code.md), [Contributing as a student](contributing-as-a-student.md).
- Practice: [Your first contribution, step by step](your-first-contribution-step-by-step.md).
- Check: which five words may a commit subject start with? (`Added:`, `Updated:`, `Fixed:`, `Removed:`, `Renamed:`, as
  `CONTRIBUTING.md` lists them.)

## Going further

| Topic | Pages |
|---|---|
| The whole developer handbook | [Extension development](../extension-development/README.md), [Reading paths](../extension-development/reading-paths.md) |
| Template language in depth | [Template language](../template-language/README.md), [Template operators](../template-operators/README.md) |
| Workflows and events | [Workflows](../../guides/workflows.md) |
| Releasing an extension | [Extensions](../../guides/extensions.md#7-release-it), [Contribute extensions back](../contribute-extensions-back.md) |
| Upgrading code | [Update with Git](../update-with-git.md) |

## Where the path leads next

- Study projects: [1](project-01-a-content-class-of-your-own.md), [3](project-03-a-template-override.md),
  [6](project-06-search-that-finds-the-right-things.md), [7](project-07-reading-content-over-the-rest-api.md),
  [8](project-08-your-first-extension.md), [capstone](project-10-capstone-a-complete-small-site.md).
- With a trainer: [Developers](../trainers/developers/README.md), 13 modules.
- Contribute: [Contributing as a student](contributing-as-a-student.md).

## Related pages

- [Using Git as a student](using-git-as-a-student.md)
- [Git quick reference for learners](git-quick-reference-for-learners.md)
- [Learning path: designer](learning-path-designer.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
