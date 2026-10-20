---
title: Content model for schools and universities
audience: government, website-creator, project-manager
category: public-sector
order: 30
summary: Shipped Exponential classes that fit an education site and recipes for Program, Course, Department and Policy document classes with real datatypes, including relations between programs, courses, departments and people.
keywords: content model, school, university, program class, course class, department class, policy document, person, documentation page, ezobjectrelationlist, eztags, class editor
type: help
---
# Content model for schools and universities

This page is for website creators and project managers who design the classes of a school, college or university
site. The classes and datatypes named exist in Exponential ([sevenx_classes](../../../site-packages/sevenx-multisite/sevenx-classes.md),
[The demo content packages](../../../site-packages/sevenx-multisite/the-demo-content-packages.md), `kernel/classes/datatypes/`);
the class editor is in [Content classes for websites](../../../creators/website-creators/content-classes-for-websites.md).

## Contents

1. [Shipped classes that fit](#shipped-classes-that-fit)
2. [Class to add: Program](#class-to-add-program)
3. [Class to add: Course](#class-to-add-course)
4. [Class to add: Department](#class-to-add-department)
5. [Class to add: Policy document](#class-to-add-policy-document)
6. [How the classes relate](#how-the-classes-relate)

## Shipped classes that fit

| Class | Identifier | Use |
|---|---|---|
| Folder | `folder` | sections of the site, department areas |
| Article | `article` | news, explanations |
| Documentation page | `documentation_page` | handbooks with chapters (a container) |
| Event, Event calendar | `event`, `event_calendar` | open days, term dates, events |
| File | `file` | forms, reports, policies as documents |
| Person | `ng_person` | public role profiles: Name, CV (XML block), Image, social links |
| Job position | `ng_job_position` | vacancies with an application deadline |
| Feedback form | `feedback_form` | contact forms |
| Gallery, Image | `gallery`, `image` | photos, with consent where people are shown |

## Class to add: Program

| Attribute | Identifier | Datatype | Why |
|---|---|---|---|
| Name | `name` | Text line (`ezstring`) | |
| Level | `level` | Selection (`ezselection`) | for schools: grade or stage; for universities: undergraduate, postgraduate ... |
| In short | `summary` | Text block (`eztext`) | |
| What you will learn | `content` | XML block (`ezxmltext`) | |
| Entry requirements | `requirements` | XML block | |
| Duration | `duration` | Text line | |
| Application deadline | `deadline` | Date and time (`ezdatetime`) | lists of programs still open |
| Apply | `apply_url` | URL (`ezurl`) | the admissions system |
| Courses | `courses` | Object relations (`ezobjectrelationlist`) | Course objects |
| Department | `department` | Object relation (`ezobjectrelation`) | |
| Topics | `tags` | Tags (`eztags`) | subject areas |

## Class to add: Course

| Attribute | Identifier | Datatype |
|---|---|---|
| Title | `title` | Text line |
| Code | `code` | Text line |
| Description | `description` | XML block |
| Credits or hours | `credits` | Integer (`ezinteger`) |
| Teachers | `teachers` | Object relations (Person objects) |
| Language of instruction | `language` | Selection |

For a large catalog of courses kept in a student information system, link to it instead of copying hundreds of
courses by hand; an import through the REST API is a development task ([REST API v1 and v2](../../../../guides/rest-api-v1-and-v2.md)).

## Class to add: Department

| Attribute | Identifier | Datatype |
|---|---|---|
| Name | `name` | Text line |
| About | `about` | XML block |
| Head (public role) | `head` | Object relation (Person) |
| Contact | `contact` | XML block (a shared mailbox and phone) |
| Image | `image` | Image (`ezimage`), alternative text required |

A Department is a container if its pages live below it; set **Container** in the class.

## Class to add: Policy document

| Attribute | Identifier | Datatype | Why |
|---|---|---|---|
| Title | `title` | Text line | |
| Summary | `summary` | XML block | the policy in short, readable on the page |
| Document | `document` | File (`ezbinaryfile`) | the approved text |
| Approved on | `approved_on` | Date (`ezdate`) | |
| Next review | `review_date` | Date | policies are usually reviewed on a cycle |
| Owner | `owner_role` | Text line | a role ("Head of admissions"), not a name |

## How the classes relate

```text
Department ──< Program ──< Course >── Person (teachers)
     │                         
     └── head ── Person
```

Relations keep each fact in one place: a teacher's profile is one Person object related from every course
([Relations and reuse](../../../creators/website-creators/relations-and-reuse.md)). When a person leaves, check
**Relations** on their object (the reverse relations) before removing it.

## Related pages

- [Schools and universities](README.md)
- [Structure, sections and roles for schools and universities](structure-sections-and-roles.md)
- [Choosing datatypes for your fields](../../../creators/website-creators/choosing-datatypes.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
