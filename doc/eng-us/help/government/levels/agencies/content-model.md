---
title: Content model for agencies
audience: government, website-creator, project-manager
category: public-sector
order: 30
summary: Shipped Exponential classes that fit an agency site and recipes for Guidance, Enforcement notice and Program classes, with effective dates, change notes and a status kept as an object state.
keywords: content model, agency, regulator, guidance class, enforcement notice, program class, effective date, change note, object state, superseded, class editor
type: help
---
# Content model for agencies

This page is for website creators and project managers who design the classes of a regulator, inspectorate or
executive agency site. Agencies need guidance that is dated, versioned and labeled with its status. The classes and
datatypes named exist in Exponential ([sevenx_classes](../../../site-packages/sevenx-multisite/sevenx-classes.md),
`kernel/classes/datatypes/`, extensions named); the class editor is in
[Content classes for websites](../../../creators/website-creators/content-classes-for-websites.md).

## Contents

1. [Shipped classes that fit](#shipped-classes-that-fit)
2. [Class to add: Guidance](#class-to-add-guidance)
3. [Status as an object state](#status-as-an-object-state)
4. [Class to add: Enforcement notice](#class-to-add-enforcement-notice)
5. [Class to add: Program](#class-to-add-program)
6. [Notes](#notes)

## Shipped classes that fit

| Class | Identifier | Use |
|---|---|---|
| Folder | `folder` | audience entry points, topics |
| Article | `article` | news, warnings, explanations |
| Documentation page | `documentation_page` | multi-level handbooks (container) |
| File | `file` | reports, data exports, forms |
| Event | `event` | webinars, information sessions |
| Feedback form | `feedback_form` | contact, complaints entry |
| Link | `link` | the register search, the online portal |

## Class to add: Guidance

| Attribute | Identifier | Datatype | Why |
|---|---|---|---|
| Title | `title` | Text line (`ezstring`) | |
| Reference | `reference` | Text line | the citable code of the guidance |
| In short | `summary` | Text block (`eztext`) | who it applies to and what it requires |
| Body | `body` | XML block (`ezxmltext`) | |
| Effective from | `effective_from` | Date (`ezdate`) | when it applies, which is not the publish time |
| Change note | `change_note` | Text block | what changed in this version, shown on the page |
| Applies to | `applies_to` | Tags (`eztags`) | sectors or kinds of regulated party |
| Replaces | `replaces` | Object relation (`ezobjectrelation`) | the earlier guidance it supersedes |
| Documents | `documents` | Object relations (`ezobjectrelationlist`) | annexes, forms |

The **Change note** is written for every new version. With the version history it gives a clear trail: the page says
what changed, and the versions page shows exactly how ([Versions of an object](../../../../guides/content-history.md)).

## Status as an object state

A status such as *In force*, *Superseded*, *Withdrawn* fits an object state group better than an attribute:

- create a group `guidance_status` with the states in **Setup > States** ([Object states](../../../../guides/object-states.md));
- the first state is the default for new objects;
- templates show the state on the page and in lists;
- roles can limit who may set *Withdrawn* with the `NewState` limitation of `state/assign`;
- changing a state does not create a new version, so the published text stays exactly as it was.

## Class to add: Enforcement notice

| Attribute | Identifier | Datatype | Why |
|---|---|---|---|
| Title | `title` | Text line | |
| Party | `party` | Text line | the organization, as your publication policy allows |
| Decision date | `decision_date` | Date | |
| Type | `type` | Selection (`ezselection`) | warning, fine, license condition ... |
| Summary | `summary` | XML block | |
| Decision document | `document` | File (`ezbinaryfile`) | redacted as required |
| Publish until | `hide_date` | Date and time (`ezdatetime`) | used by `hide.php` if notices are shown for a limited period |

Notices published for a limited period can be hidden automatically: name the class and attribute in
`content.ini [HideSettings] HideDateAttributeList[]` and the folder in `RootNodeList[]`; the `hide.php` cronjob does
the rest ([Cronjobs](../../../../guides/cronjobs.md)).

## Class to add: Program

| Attribute | Identifier | Datatype |
|---|---|---|
| Title | `title` | Text line |
| Objective | `objective` | XML block |
| Who can apply | `eligibility` | XML block |
| Opens, closes | `opens`, `closes` | Date and time |
| Apply | `application_url` | URL (`ezurl`) |
| Documents | `documents` | Object relations |
| Results | `results` | XML block |

## Notes

| Rule | Reason |
|---|---|
| Effective date separate from publish date | guidance may be published before it applies |
| Never overwrite guidance silently | the change note and versions keep it traceable |
| Raise the version history limit for guidance if every version must be kept | `DefaultVersionHistoryLimit` is 45 in the shipped `settings/content.ini`; `VersionHistoryClass[<class id>]` sets a limit for one class ([Content model and editing](../../../../guides/content-model-and-editing.md)) |
| Names of people in enforcement notices only where the law requires | data minimization |

## Related pages

- [Agencies](README.md)
- [Guidance for regulated parties](guidance-for-regulated-parties.md)
- [Choosing datatypes for your fields](../../../creators/website-creators/choosing-datatypes.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
