---
title: Content model for public health
audience: government, website-creator, project-manager
category: public-sector
order: 30
summary: Shipped Exponential classes that fit a public health site and recipes for Health topic, Alert and Clinic classes with real datatypes, including review dates, clinical sign-off and alert severity and end time.
keywords: content model, public health, health topic class, alert class, severity, clinic class, review date, clinical sign-off, ezdatetime, ezselection, eztags, class editor
type: help
---
# Content model for public health

This page is for website creators and project managers who design the classes of a public health site. The classes
and datatypes named exist in Exponential ([sevenx_classes](../../../site-packages/sevenx-multisite/sevenx-classes.md),
`kernel/classes/datatypes/`, extensions named); the class editor is in
[Content classes for websites](../../../creators/website-creators/content-classes-for-websites.md).

## Contents

1. [Shipped classes that fit](#shipped-classes-that-fit)
2. [Class to add: Health topic](#class-to-add-health-topic)
3. [Class to add: Alert](#class-to-add-alert)
4. [Class to add: Clinic](#class-to-add-clinic)
5. [Translation settings](#translation-settings)

## Shipped classes that fit

| Class | Identifier | Use |
|---|---|---|
| Folder | `folder` | topic groups, alerts folder, professionals' area |
| Article | `article` | news, press releases |
| File | `file` | reports, posters, data files |
| Documentation page | `documentation_page` | protocols with chapters for professionals |
| Event | `event` | vaccination days, information sessions |
| Video | `video` | explainer videos (with captions from the source) |
| Feedback form | `feedback_form` | general contact (never patient data) |

## Class to add: Health topic

| Attribute | Identifier | Datatype | Why |
|---|---|---|---|
| Title | `title` | Text line (`ezstring`) | the name people use ("Flu", not only the clinical term) |
| In short | `summary` | Text block (`eztext`) | three key messages in plain language |
| Advice | `body` | XML block (`ezxmltext`) | headings: symptoms, what to do, when to get help |
| When to get help urgently | `urgent` | XML block | shown prominently |
| Sources | `sources` | XML block | evidence and references |
| Clinically reviewed on | `reviewed_on` | Date (`ezdate`) | shown on the page |
| Reviewed by (role) | `reviewed_by` | Text line | a role, not a name, if your policy prefers |
| Next review | `review_date` | Date | drives the overdue list |
| Topics | `tags` | Tags (`eztags`) | conditions, audiences, life stages |
| Audience | `audience` | Selection (`ezselection`) | public, professionals |

## Class to add: Alert

| Attribute | Identifier | Datatype | Why |
|---|---|---|---|
| Headline | `title` | Text line | short, plain, action first |
| Severity | `severity` | Selection | information, advisory, warning |
| What to do | `action` | Text block | one to three sentences |
| Details | `body` | XML block | |
| Area affected | `area` | Tags | a tag tree of areas |
| Issued | `issued` | Date and time (`ezdatetime`) | |
| Last updated | `updated` | Date and time | shown on the page |
| Ends | `hide_date` | Date and time | used by `hide.php` to take the alert down |

To end alerts automatically: in `settings/override/content.ini.append.php`, `[HideSettings] RootNodeList[]=<alerts
folder node>` and `HideDateAttributeList[alert]=hide_date`, then clear the INI cache. The `hide.php` cronjob (global
part) hides alerts whose end has passed ([Cronjobs](../../../../guides/cronjobs.md)). Check that the global part runs
often enough for your alerts; an alert that should end at 18:00 ends at the next run after that.

## Class to add: Clinic

| Attribute | Identifier | Datatype |
|---|---|---|
| Name | `name` | Text line |
| Services | `services` | Tags (vaccination, screening ...) |
| Address | `address` | Text block |
| Opening hours | `opening_hours` | XML block |
| How to book | `booking` | XML block and URL (`ezurl`) |
| Accessibility | `access` | XML block |

## Translation settings

| Attribute | Translatable | Why |
|---|---|---|
| Texts (title, summary, advice, action) | yes | every language has its own |
| Dates (issued, ends, review dates) | no | the same moment in every language |
| Severity, tags | no for the selection; tags have their own translations | one value for all languages |

Untick **Translatable** on date and selection attributes in the class editor before content is created
([Content languages](../../../../guides/content-languages.md)).

## Related pages

- [Public health](README.md)
- [Alerts in many languages and plain language](alerts-multilingual-and-plain-language.md)
- [Choosing datatypes for your fields](../../../creators/website-creators/choosing-datatypes.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
