---
title: Content model for national government
audience: government, website-creator, project-manager
category: public-sector
order: 30
summary: Which content classes and datatypes of Exponential fit a ministry site, which shipped classes to reuse, and three classes to add in the class editor - Publication, Consultation and Press release - with real datatypes.
keywords: content model, content class, datatype, ministry, publication class, consultation class, press release class, ezbinaryfile, ezdatetime, eztags, ezobjectrelationlist, class editor
type: help
---
# Content model for national government

This page is for website creators and project managers who design the content classes of a ministry or central
department site. It names the classes that ship with the default site package and fit as they are, and gives
recipes for the classes most national sites add. Every class and datatype named here exists in Exponential: the
classes in the `sevenx_classes` package and its base data
([sevenx_classes](../../../site-packages/sevenx-multisite/sevenx-classes.md)), the datatypes in
`kernel/classes/datatypes/` and in the extensions named.

## Contents

1. [Shipped classes that fit](#shipped-classes-that-fit)
2. [Class to add: Publication](#class-to-add-publication)
3. [Class to add: Consultation](#class-to-add-consultation)
4. [Class to add: Press release](#class-to-add-press-release)
5. [Taxonomy and reuse](#taxonomy-and-reuse)
6. [Rules of thumb](#rules-of-thumb)

## Shipped classes that fit

On a new installation of the default package these classes exist (identifiers as in **Setup > Classes**):

| Class | Identifier | Use on a ministry site | Notes |
|---|---|---|---|
| Folder | `folder` | topic pages, unit pages, containers | has Summary, Description, Display sub items, Tags (`ezkeyword`) and Publish date (`ezdatetime`) |
| Article | `article` | policy pages, news, speeches | Title, Short title, Author (`ezauthor`), Intro and Body (`ezxmltext`), Image (`ezobjectrelation`) |
| File | `file` | any downloadable document | Name, Description, File (`ezbinaryfile`) |
| Image | `image` | photos, charts as images | the image field can require an alternative text |
| Link | `link` | a link to a transactional service on another system | `ezurl` |
| Event | `event` | ministerial events, launches | From Time and To Time (`ezdatetime`), Category (`ezkeyword`) |
| Feedback form | `feedback_form` | a contact form | its Sender name, Subject, Message and Email are information collector fields |
| Job position | `ng_job_position` | vacancies | Location, Engagement type, Application deadline (`ezdatetime`) |
| Person | `ng_person` | office holders' public profiles | Name, CV, Image; keep only what the office makes public |

The `ng_*` classes come with the demo content packages of the default site package, in both the full and the clean
variant ([The demo content packages](../../../site-packages/sevenx-multisite/the-demo-content-packages.md)).

## Class to add: Publication

A report, strategy or guidance document with a landing page that summarizes it. Create it in **Setup > Classes**,
group Content, **New class** ([Content classes for websites](../../../creators/website-creators/content-classes-for-websites.md)).

| Attribute | Identifier | Datatype (class editor name) | Required | Why |
|---|---|---|---|---|
| Title | `title` | Text line (`ezstring`) | yes | the name of the publication |
| Summary | `summary` | XML block (`ezxmltext`) | yes | an accessible HTML summary of the document |
| Document | `document` | File (`ezbinaryfile`) | no | the PDF or other file; or use Related files below |
| Related files | `files` | Object relations (`ezobjectrelationlist`) | no | several files (language versions, annexes) as File objects |
| Publication date | `publication_date` | Date (`ezdate`) | yes | the official date, independent of the publish time |
| Reference number | `reference` | Text line (`ezstring`) | no | the document number used in citations |
| Publication type | `publication_type` | Selection (`ezselection`) | yes | report, strategy, guidance ... |
| Topics | `tags` | Tags (`eztags`) | no | cross-unit topics ([eztags](../../../../features/6.0/extensions/eztags.md)) |
| Meta data | `metadata` | Metadata (`xrowmetadata`) | no | page title and description for search engines |

Object name pattern: `<title>`. Mark Summary searchable so the search finds the publication even when the file is not
indexed.

## Class to add: Consultation

| Attribute | Identifier | Datatype | Collector | Why |
|---|---|---|---|---|
| Title | `title` | `ezstring` | no | |
| Proposal | `proposal` | `ezxmltext` | no | what is proposed, in plain language |
| Opens | `opens` | `ezdatetime` | no | shown on the page |
| Closes | `closes` | `ezdatetime` | no | shown on the page; the form template can hide the form after it |
| Documents | `documents` | `ezobjectrelationlist` | no | the consultation paper and annexes |
| Your response | `response` | `eztext` | yes | the visitor's answer |
| Responding as | `respondent_type` | `ezselection` | yes | individual, organization ... |
| Organization name | `organization` | `ezstring` | yes | optional for individuals |
| E-mail | `email` | `ezemail` | yes | only if you will reply; otherwise leave it out |
| Outcome | `outcome` | `ezxmltext` | no | the published result after closing |

Ticking **Information collector** on an attribute makes visitors, not editors, fill it in
([Forms with the information collector](../../../creators/website-creators/forms-with-the-information-collector.md)).
Responses appear in **Setup > Collected information** and export as CSV
([Collected information](../../../../guides/collected-information.md)). Collect only what the consultation needs.

## Class to add: Press release

| Attribute | Identifier | Datatype | Why |
|---|---|---|---|
| Title | `title` | `ezstring` | |
| Release date and time | `release_date` | `ezdatetime` | the embargo time; can drive a *Wait until date* workflow event |
| Lead | `intro` | `ezxmltext` | the first paragraph |
| Body | `body` | `ezxmltext` | |
| Press contact | `press_contact` | `ezxmltext` | a role address such as the press office, not a private person |
| Attachments | `attachments` | `ezobjectrelationlist` | photos and documents |
| Topics | `tags` | `eztags` | |

A *Wait until date* event, set to the class and its date attribute, holds a version until that time and the workflow
cronjob publishes it ([Workflows](../../../../guides/workflows.md)). Test this on a copy of the site first.

## Taxonomy and reuse

- **Tree for ownership, tags for topics.** The content tree says which unit owns a page; tags say which topics it is
  about. A press release lives under the press office and carries the tags of every topic it touches.
- **Relations, not copies.** A publication linked from five topic pages is one object with five relations, so an
  update is made once ([Relations and reuse](../../../creators/website-creators/relations-and-reuse.md)).
- **Several locations** when one page must appear in two menus ([Content model and editing](../../../../guides/content-model-and-editing.md)).

## Rules of thumb

| Rule | Reason |
|---|---|
| Add a class only when its fields differ | every class needs templates, roles and training |
| Make dates attributes, not text | lists, feeds and expiry use them |
| Keep personal data out of content classes unless the office makes it public | data minimization |
| Decide translatable attributes up front | dates and codes are usually not translated ([Content languages](../../../../guides/content-languages.md)) |
| Move classes between installations as packages | [Packages](../../../../guides/packages.md) |

## Related pages

- [National government](README.md)
- [Structure, sections and roles for national government](structure-sections-and-roles.md)
- [Choosing datatypes for your fields](../../../creators/website-creators/choosing-datatypes.md)
- [Designing your content model](../../../creators/website-creators/designing-your-content-model.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
