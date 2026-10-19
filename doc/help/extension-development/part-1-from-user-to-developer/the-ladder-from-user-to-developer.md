---
title: "The ladder: user, builder, extension developer, theme developer"
audience: user, developer, designer, app-creator
category: extension-development
order: 20
summary: Four steps from using Exponential to extending it - user, builder, extension developer, theme developer - what each one does, what each needs to know, and which part of this book teaches it.
keywords: ladder, career, learning path, user, builder, site builder, extension developer, theme developer, designer, application creator, skills
type: help
---
# The ladder: user, builder, extension developer, theme developer

Part 1, chapter 2. People grow into development with Exponential in small, safe steps. This chapter names the steps,
says what you do and need to know on each, and points to the part of the book that teaches it. Find yourself on the
ladder, then read upward.

## The four steps

| Step | You change | You need | Book part |
|---|---|---|---|
| **User** | Content: articles, images, menus, users | the administration | Part 1 (this part) |
| **Builder** | Settings, templates, overrides, content classes, roles, workflows | a text editor, the INI and template languages, patience | [Part 2](../part-2-builder/README.md) |
| **Extension developer** | New modules, datatypes, operators, workflow events, cronjobs, commands | PHP, the kernel's classes, Git | [Part 3](../part-3-extension-developer/README.md), [Part 4](../part-4-upgrading/README.md) |
| **Theme developer** | The look: a design extension with templates, CSS, JavaScript | HTML, CSS, the template language, the design chain | [Part 6](../part-6-theme-developers/README.md) |

Two more roles sit beside the ladder rather than on it:

- **Maintainer**: you publish your extension so others install it, and you look after it. Releasing is
  [Part 7](../part-7-releasing/README.md); contributing to other people's work and growing the community is
  [Part 5](../part-5-extend-fork-contribute/README.md).
- **Application creator**: you combine content classes, extensions and settings into a complete application (a
  members' area, a shop, a booking system). You use all four steps, mostly the builder's and the extension
  developer's.

## Step 1: user

You already know the administration. What helps you climb:

- Know **where things are configured**. Each page of the administration is drawn from templates and governed by
  settings. When you wonder "why does it do that?", the answer is almost always a setting or a template.
- Know **what is installed**. [Finding the extensions installed on your site](finding-the-extensions-on-your-site.md)
  shows you how.
- Learn to **read a log**. When something goes wrong, the error log names the cause far more often than people
  expect. See [Debug output and logs for developers](debug-output-and-logs-for-developers.md).

What you can already do without code: create content classes, sections, roles and policies, workflows, URL
aliases, translations. Each of those is a builder's tool too.

## Step 2: builder

A builder changes how the site behaves and looks **without writing PHP**:

- **Settings** (`.ini` files): which extensions run, which design a site uses, how images are scaled, which
  languages exist, what is cached.
- **Templates** (`.tpl` files): the HTML of every page, written in the template language with variables, loops,
  conditions, operators and fetch functions.
- **Overrides**: a rule that says "for articles in this section, use *this* template instead of the standard one".

The builder works in an extension too: a settings extension and a design extension keep every change in one folder
under version control, instead of scattered through `settings/override/`. Part 2 teaches all of it.

> **Note**
> Most sites need nothing beyond the builder's tools. Do not write PHP until a setting, a template or an override
> really cannot do the job. [When settings are not enough](../part-2-builder/when-settings-are-not-enough.md) helps
> you decide.

## Step 3: extension developer

You write PHP that the kernel calls:

- a **module** with views (a page with its own address, for example `/report/list`),
- a **template operator** or **fetch function** that templates can call,
- a **datatype** (a new kind of field),
- a **workflow event** (something that happens when content is published),
- a **cronjob part**, a **command**, an **ajax function**, a **handler** that replaces a part of the system.

You learn the anatomy of an extension and the kernel's classes (`eZINI`, `eZContentObject`, `eZTemplate` and the
rest, see Part 8). The RAD tools in **Setup > RAD** write a working skeleton of most
of these for you, so you start from code that runs; the tutorials in Part 3 show both the RAD way and the hand way.

## Step 4: theme developer

You build the look of a site as a **design extension**: templates, stylesheets, scripts, images, and the override
rules that use them. You work with the design chain, the stylesheet packer, dark mode tokens and the Content Security
Policy that forbids inline scripts. Part 6 covers what is special about themes; the Designers section of the help goes
much deeper into theme design and templates.

## How long does each step take?

That depends on you, and this book makes no promises about hours. What it does promise: each step builds only on the
one below it, each chapter ends with something you can check on your own installation, and nothing asks you to learn
something you do not need yet.

## A sensible order

1. Read Part 1 fully, and do [Your first hour as a developer](your-first-hour-as-a-developer.md).
2. Build something real with Part 2: a settings extension for your own site.
3. Do the first three tutorials in Part 3: the
   [smallest extension by hand](../part-3-extension-developer/tutorials/by-hand-minimal-extension.md), a
   [template operator](../part-3-extension-developer/tutorials/by-hand-template-operator.md) and a
   [module with a view](../part-3-extension-developer/tutorials/by-hand-module-with-a-view.md).
4. Take an old extension that you like and bring it up to date with Part 4. Nothing teaches the kernel faster.
5. Publish it (Part 7) and offer your changes to its author (Part 5).

Reading paths through the book gives ready-made orders for developers, designers and
application creators.

## Related pages

- Previous: [What an extension is](what-an-extension-is.md)
- Next: [The tools you need](tools-you-need.md)
- [Develop with Git: grow from user to developer](../../develop-with-git.md)
- Reading paths through the book
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
