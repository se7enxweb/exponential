---
title: Exponential terms for government readers
audience: government, project-manager, admin, editor
category: public-sector
order: 70
summary: The Exponential terms a public body meets most often, explained by what they mean for a department, a website team or a procurement - siteaccess, section, role, object state, workflow, package, extension, Velocity, audit trail and more - with links to the pages that teach them.
keywords: Exponential terms, siteaccess, section, role, policy, object state, workflow, content class, node, package, extension, Velocity, audit trail, Editor siteaccess, multisite, public sector
type: reference
---
# Exponential terms for government readers

This page is for managers and procurement staff in a public body who hear Exponential terms from their website team or
a supplier. Each term is explained by what it means for your organization, with the page that teaches it. It does not
repeat [the Exponential glossary](../../../glossary.md), which lists every term in one line.

## Contents

1. [How content is organized](#how-content-is-organized)
2. [Who may do what](#who-may-do-what)
3. [Several sites and departments](#several-sites-and-departments)
4. [Building and changing the system](#building-and-changing-the-system)
5. [Running it](#running-it)

## How content is organized

| Term | What it means for you |
|---|---|
| **Content class** | Your kinds of content (news item, service page, document, consultation) are defined as classes with fields. Deciding them is the first design step ([Designing your content model](../../creators/website-creators/designing-your-content-model.md)) |
| **Content object** and **version** | Every page is an object with a history of versions; older versions can be compared and restored ([Versions of an object](../../../guides/content-history.md)) |
| **Node** or **location** | Where an object appears in the content tree; one object can appear in several places, for example a notice shown under two departments ([Content model](../../../guides/content-model-and-editing.md)) |
| **Translation** | Each object can have a version per language ([Content languages](../../../guides/content-languages.md)) |
| **Tags** | Keywords from a managed tag tree, with the eztags extension ([Tagging with eztags](../../creators/website-creators/tagging-with-eztags.md)) |

## Who may do what

| Term | What it means for you |
|---|---|
| **User group** | Accounts organized in groups, for example per department |
| **Role** and **policy** | A role is a set of permissions given to groups; a policy is one permission with optional limitations ([Roles and policies](../../../guides/roles-and-policies.md)) |
| **Section** | A label on a part of the content that roles can be limited to; often one per department ([Sections](../../../guides/sections.md)) |
| **Object state** | A stage of your own, such as "draft for legal review", that permissions can depend on ([Object states](../../../guides/object-states.md)) |
| **Workflow** | Rules that run on events, such as holding a version for approval before publishing ([Workflows](../../../guides/workflows.md)) |
| **Audit trail** | The tamper-evident record of who did what, on by default ([Audit trail](../../../features/6.0/audit-trail.md)) |

## Several sites and departments

| Term | What it means for you |
|---|---|
| **Siteaccess** | A way into the installation with its own address, language, design and settings. The public site, the administration and each extra site are siteaccesses ([the Exponential glossary](../../../glossary.md#s)) |
| **Multisite** | Several websites from one installation, for example a main site and campaign sites, sharing users and content where you want ([Multisite setup](../../creators/website-creators/multisite-setup.md)) |
| **Editor siteaccess** | A simpler administration for editors ([Setup wizard and editor siteaccess](../../../features/6.0/setup-wizard-and-editor-siteaccess.md)) |
| **Design** | The templates and styles of a site; the public design is where your accessibility work matters most ([Choosing a design or theme](../../creators/website-creators/choosing-a-design-or-theme.md)) |

## Building and changing the system

| Term | What it means for you |
|---|---|
| **Extension** | A folder that adds features; your supplier's custom work usually lives in extensions, which you should own in a repository ([Extensions](../../../guides/extensions.md)) |
| **Override** | A rule that uses a custom template for a class, node or section, so the core files stay unchanged and upgrades stay simple ([Template overrides](../../creators/website-creators/template-overrides.md)) |
| **Package** | A bundle of classes, content or design that can be exported and installed; also a route for moving content between installations ([Packages](../../../guides/packages.md)) |
| **Exponential Layouts** | A page builder with layouts, zones and blocks ([Layouts editor](../../layouts-editor.md)) |
| **Exponential Intelligence** | The writing assistant in the administration; it writes drafts with the editor's permissions and never publishes on its own ([the Exponential glossary](../../../glossary.md#e)) |

## Running it

| Term | What it means for you |
|---|---|
| **Velocity** | Exponential's own PHP application server, an alternative to Apache or nginx with PHP-FPM ([Velocity web server](../../../features/6.0/velocity-web-server.md)) |
| **Cache** | Stored page parts that make the site fast; clearing the right one makes a change visible ([Caches](../../../guides/caches.md)) |
| **Cronjob** | Scheduled server tasks, such as notifications ([Cronjobs](../../../guides/cronjobs.md)) |
| **Maintenance mode** | Takes the site offline with a notice during work ([Maintenance mode](../../../guides/maintenance-mode.md)) |
| **Setup wizard**, **kickstarter**, `exp:install` | The three ways to install ([4. Choosing an install method](../../../install/04-choosing-an-install-method.md)) |
| **Upgrade check** | Compares installed files with the release before an upgrade ([The upgrade check](../../../guides/upgrade-check.md)) |

## Related pages

- [Government glossary](README.md)
- [Acronyms for government readers](acronyms.md)
- [The Exponential glossary](../../../glossary.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
