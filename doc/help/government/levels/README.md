---
title: Levels of government
audience: government, project-manager, admin, website-creator
category: public-sector
order: 10
summary: An index of the ten kinds of public-sector organization this section covers, from national government to emergency services, with the same seven questions answered for each.
keywords: levels of government, public sector, national, regional, state, municipal, local, council, agency, library, school, university, court, parliament, public health, emergency services
type: help
---
# Levels of government

This section is for project managers, administrators and website creators who plan an Exponential site for a
public-sector organization. Public bodies differ a great deal in what they publish and how carefully they must
publish it: a court is not a library, and a parliament is not a fire service. Each folder below answers the same
questions for one kind of organization, so you can read only the one that fits you, or compare two.

The facts about Exponential in these pages come from the documentation in this repository (the guides in `doc/guides/`,
the standards in `doc/standards/` and the feature pages in `doc/features/6.0/`), from the content classes and datatypes
that ship with the kernel and the default site package, and from the administration screens of the demonstration
server. Descriptions of what public bodies publish are general public-sector practice, not reports about any
particular government.

## Contents

1. [The folders](#the-folders)
2. [What every folder answers](#what-every-folder-answers)
3. [How to read this section](#how-to-read-this-section)
4. [Organizations that span several levels](#organizations-that-span-several-levels)

## The folders

| Folder | Covers | Level-specific page |
|---|---|---|
| [National government](national/README.md) | ministries, departments, central government portals | many ministries on one platform |
| [Regional and state government](regional-and-state/README.md) | states, provinces, regions, counties | shared services for the municipalities of a region |
| [Municipal and local government](municipal-and-local/README.md) | cities, towns, councils, districts | council meetings and local services |
| [Agencies](agencies/README.md) | regulators, inspectorates, executive and arm's-length bodies | guidance, programs and regulated parties |
| [Public libraries](public-libraries/README.md) | public and community libraries, library networks | events and catalogs linked rather than built |
| [Schools and universities](schools-and-universities/README.md) | schools, school districts, colleges, universities | intranets and department subsites |
| [Courts](courts/README.md) | courts and tribunals | publishing decisions with care for personal data |
| [Parliaments](parliaments/README.md) | parliaments, assemblies, councils as legislatures | agendas, bills and voting records |
| [Public health](public-health/README.md) | health departments and public health agencies | alerts, many languages and plain language |
| [Emergency services](emergency-services/README.md) | fire, rescue, civil protection, emergency management | alerts, availability, maintenance mode and static cache |

## What every folder answers

| Page | The question it answers |
|---|---|
| README | What this kind of organization is, and the reading order |
| What it publishes | What this kind of organization typically publishes and what it needs from a CMS |
| Content model | Which Exponential content classes and datatypes fit, and which classes to add in the class editor |
| Structure, sections and roles | How to arrange the content tree, which sections to create and which roles staff get |
| Requirements emphasis | Which requirements weigh most here: accessibility, languages, security, records |
| First 90 days | A phased plan from kick-off to a running site, with checks at each step |
| The level-specific page | The one topic that sets this kind of organization apart |

## How to read this section

1. Start with the folder that matches your organization and read its README and **What it publishes**.
2. Read **Content model** together with [Designing your content model](../../creators/website-creators/designing-your-content-model.md)
   and [Choosing datatypes for your fields](../../creators/website-creators/choosing-datatypes.md).
3. Read **Structure, sections and roles** together with [Sections](../../../guides/sections.md) and
   [Roles and policies](../../../guides/roles-and-policies.md).
4. Read **Requirements emphasis** together with the standards pages:
   [Accessibility standards](../../../standards/accessibility.md), [Security standards](../../../standards/security.md),
   [Privacy standards](../../../standards/privacy.md) and
   [Internationalization standards](../../../standards/internationalization.md).
5. Use **First 90 days** as the skeleton of your project plan.

The requirement sections of this Governments help ([accessibility](../accessibility/README.md),
[security](../security/README.md), [privacy](../privacy/README.md) and
[records and archiving](../records-and-archiving/README.md)) and the [building guides](../building/README.md) go
deeper on each topic. The step-by-step [publishing tasks](../tasks/publishing/README.md) for editors are in the tasks folder of this
section. Up: [Governments: start here](../government-start-here.md).

## Organizations that span several levels

Many public bodies do not fit one folder. A city may run a library, schools and a fire service; a ministry may run
agencies; a university hospital may publish public health information. Exponential can serve several sites from one
installation, each with its own siteaccess, design and languages
([Multisite setup](../../creators/website-creators/multisite-setup.md)), and sections and roles can keep each
organization's editors to its own part of the content tree ([Sections](../../../guides/sections.md#132-an-intranet-with-departments)).
Read the folder of each part, then decide whether one installation or several fits your governance.

| Situation | Usual choice |
|---|---|
| One organization, one public site, one team of editors | one installation, one public siteaccess |
| One organization with several brands or services under one administration | one installation, several siteaccesses, one section per unit |
| Separate legal bodies with separate data controllers or separate security requirements | separate installations, shared design as an extension |

The last row is a governance decision, not a technical limit; your data protection and security officers decide it.

## Related pages

- [Multisite setup](../../creators/website-creators/multisite-setup.md)
- [Planning your website](../../creators/website-creators/planning-your-website.md)
- [Website creators start here](../../creators/website-creators/website-creators-start-here.md)
- [Course catalog for trainers](../../trainers/course-catalogue.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
