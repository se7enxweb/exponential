---
title: "Bootcamp 3.4: The three-day site builder bootcamp"
audience: trainer, website-creator, editor
category: training
order: 34
summary: Three days for people who build websites without writing PHP, the content structure, content classes, layouts and blocks, shared layouts and mappings, languages, and a complete small website as capstone.
keywords: three days, bootcamp, site builder, website creators, layouts, content structure, content classes, shared layouts, mappings, languages, capstone
type: help
---
# Bootcamp 3.4: The three-day site builder bootcamp

Site builders plan a site's structure, define its content types and compose its pages, mostly without code. This
bootcamp combines editor skills, content classes and Exponential Layouts into one three-day path ending in a small,
complete website.

## At a glance

| | |
|---|---|
| Course | [Workshops and bootcamps](../README.md) |
| Module | [Module 3: Three-day bootcamps](README.md) |
| Duration | 3 days, 6 hours of sessions each |
| Level | Intermediate |
| Audience | Website creators, agency project staff, power editors, 4 to 12 learners |
| Prerequisites | One installation per learner with administrator access; Editors 101 basics |

## Learning objectives

At the end of the bootcamp, learners can:

- plan a content structure and the content classes it needs;
- create a content class without code;
- compose pages from blocks, use shared layouts for header and footer, and map layouts to pages;
- add a second language and translate content.

## The agenda

| Day | Morning (3 h) | Afternoon (3 h) |
|---|---|---|
| 1 | Planning a site: structure, sections, classes ([The content model and editing content](../../../../guides/content-model-and-editing.md), [Sections](../../../../guides/sections.md)) | Creating content classes and content ([Class groups](../../../../guides/class-groups.md)); the site structure built |
| 2 | Layouts, zones and blocks ([The layouts editor](../../../layouts-editor.md)) | Shared layouts, linked zones and mappings ([Which pages use a layout](../../../layouts-editor.md#which-pages-use-a-layout)) |
| 3 | Languages ([Languages](../../../languages.md), [Content languages](../../../../guides/content-languages.md)); URLs ([URLs and aliases](../../../../guides/urls-and-aliases.md)) | Capstone: a complete small website; presentations |

Breaks: 15 minutes mid-morning and mid-afternoon, 45 minutes lunch, each day.

## Trainer's notes

- **Plan on paper first** (day 1 morning): a site map and a list of content types with their fields.
- **Class changes affect existing content**; build classes before content.
- **Mappings** decide what whole parts of a site look like; review each learner's mappings before they publish.

## Hands-on exercise: the bootcamp capstone

Build a website for an invented sports club:

1. Structure: Home, News, Events, Teams, Contact.
2. A content class "Team" (name, coach, training times, photo).
3. A landing page layout for Home with a hero, a news list and an events list; a shared header and footer.
4. Mappings for Home and for every news article.
5. A second language for the Home page and two news items.

## Expected result

- The site has the five sections, three teams, a mapped landing page with a shared header and footer, and two
  translated items.
- Each learner presents their site in three minutes.

## Quiz (end of bootcamp)

1. Why build content classes before content?
2. How does a header built once reach every layout?
3. What decides which layout a page uses?
4. What changes when you add a translation: the object, the node, or a version?

<details>
<summary>Answers</summary>

1. Class changes affect all existing content of that class.
2. Through a shared layout: other layouts link a zone to its zone ([The layouts editor](../../../layouts-editor.md)).
3. The first enabled mapping by priority whose targets and conditions match.
4. The object gets a new language version; the node stays the same.

</details>

## Further reading

- [The layouts editor](../../../layouts-editor.md)
- [Content languages](../../../../guides/content-languages.md)

## Related pages

- [Module 3: Three-day bootcamps](README.md)
- [Workshops and bootcamps: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
