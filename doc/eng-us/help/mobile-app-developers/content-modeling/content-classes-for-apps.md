---
title: Content classes for apps
audience: mobile-app-developer, app-creator, developer, website-creator
category: mobile-apps
order: 410
summary: Map Exponential content classes to app screens - finding the classes and fields a folder holds, reading class definitions with expclass services, classIdentifier as the switch for cells and views, handling unknown classes and changed fields, and asking the site team for app-friendly fields.
keywords: content class, classIdentifier, fields, attributes, expclass, class definition, required fields, unknown class, schema change, cell, view, model, mobile app
type: help
---
# Content classes for apps

This page is for app developers who turn content classes into screens and data models. It explains how to learn what
a class contains, how to use `classIdentifier` in the app, and how to stay robust when the site team changes a class.

## Contents

1. [Learning the classes](#learning-the-classes)
2. [classIdentifier as the switch](#classidentifier-as-the-switch)
3. [Surviving class changes](#surviving-class-changes)
4. [Fields that help apps](#fields-that-help-apps)

## Learning the classes

Three ways, from simplest to most complete:

| Way | How | Needs |
|---|---|---|
| Read real content | `GET /api/ezp/v2/content/node/<id>?ResponseGroups=Fields`: every field with `type` and `identifier` | `content/read` |
| Ask the site team | the class definitions in **Setup > Classes** of the administration | an editor or administrator |
| Read the definition | `expclass::get::<identifier>`, `expclass::attributes::<identifier>`, `expclass::requiredAttributes::<identifier>` | the `class` policy, so use a developer account, not the app's |

`expclass::identifiers` gives a light map of class ids, identifiers and names to any signed-in user. The class
services are declared in `extension/expservices/classes/content/expclassservices.php`.

## classIdentifier as the switch

Every node, object and list entry carries `classIdentifier`. Use it to choose the cell and the detail screen:

```text
when (item.classIdentifier) {
    "article" -> ArticleCell
    "event"   -> EventCell
    "folder"  -> FolderCell (drill down with the list route)
    else      -> GenericCell (objectName only)
}
```

Always have the generic case: editors can place a class the app does not know in a folder it shows, and the app must
not crash or hide the whole list because of it.

## Surviving class changes

Administrators can add, remove and reorder fields and change classes while the app is in users' hands. To stay robust:

- Read fields by `identifier`, never by position.
- Treat every field as optional, even if the class marks it required; old content may predate the requirement.
- Check `type` before parsing; skip a field whose type changed.
- Agree with the site team that renaming or removing a field the app uses is announced, like an API change.
- Add a test that reads one item of every class the app uses on a test installation and fails on unexpected types
  ([A local test installation](../testing/a-local-test-installation.md)).

## Fields that help apps

If the site team builds or extends classes with the app in mind, a few fields make an app simpler:

| Field | Why |
|---|---|
| A short teaser text (plain `ezstring` or `eztext`) | rich text reaches REST as plain text; a dedicated teaser field is clean for list cells |
| One main image field | one place for the cell image ([Images in REST answers](../media-and-images/images-in-rest-answers.md)) |
| A stable identifier for app-relevant folders, as a remote id | entry points survive reinstalls ([Node ids, remote ids and deep links](node-ids-remote-ids-and-deep-links.md)) |

How classes are designed is the site team's subject: [Content classes for websites](../../creators/website-creators/content-classes-for-websites.md),
[Choosing datatypes](../../creators/website-creators/choosing-datatypes.md).

## Related pages

- [Fields and field values](../rest-api/fields-and-field-values.md)
- [Designing your content model](../../creators/website-creators/designing-your-content-model.md)
- [Content modeling for apps](README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
