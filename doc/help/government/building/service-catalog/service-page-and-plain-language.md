---
title: The service page and plain language
audience: government, website-creator, editor, project-manager
category: public-sector
order: 30
summary: A fixed order for every service page, the template that shows the Service class in that order and hides empty parts, and writing rules for plain, accessible service text that editors can follow.
keywords: service page, page template, full view, plain language, writing rules, headings, accessible content, template override, attribute_view_gui, empty fields
type: help
---
# The service page and plain language

This page is for website creators who build the service page template and for editors and project managers who
write service content. It sets one order for every service page, shows how the template follows it, and gives the
writing rules. The class is the one from [Modeling services](modeling-services.md).

## Contents

1. [One order for every page](#one-order-for-every-page)
2. [The template](#the-template)
3. [Writing rules](#writing-rules)
4. [Accessibility of service pages](#accessibility-of-service-pages)
5. [A review checklist for editors](#a-review-checklist-for-editors)

## One order for every page

| Order | Part (attribute) | Heading shown |
|---|---|---|
| 1 | Service name | the page title (H1) |
| 2 | In short | no heading; the first paragraph |
| 3 | Apply online | a button-styled link "Apply online" near the top, when filled |
| 4 | Who can use it | "Who can use this service" |
| 5 | What you need | "What you need" |
| 6 | What it costs | "Cost" |
| 7 | How to apply | "How to apply" |
| 8 | How long it takes | "How long it takes" |
| 9 | What happens next | "What happens next" |
| 10 | Contact | "Contact", from the related Contact point |
| 11 | Related documents | "Documents", with file type and size |
| 12 | Next review | not shown to visitors, or as "Page checked on" if your policy shows it |

## The template

A full view override for the class, in your design extension, matched by class identifier
([Template overrides](../../../creators/website-creators/template-overrides.md)):

```ini
# extension/<your design>/settings/override.ini.append.php
[full_service]
Source=node/view/full.tpl
MatchFile=full/service.tpl
Subdir=templates
Match[class_identifier]=service
```

In `full/service.tpl`, render each part only when it has content, under its heading:

```text
<h1>{$node.name|wash}</h1>
<p class="lead">{$node.data_map.summary.content|wash}</p>
{if $node.data_map.online_link.has_content}
  <p><a class="button" href={$node.data_map.online_link.content|ezurl}>{'Apply online'|i18n( 'design/<your design>' )}</a></p>
{/if}
{if $node.data_map.eligibility.has_content}
  <h2>{'Who can use this service'|i18n( 'design/<your design>' )}</h2>
  {attribute_view_gui attribute=$node.data_map.eligibility}
{/if}
```

and so on for each part. Headings in the template, not in the editors' text, keep every page's structure the same and
the heading levels correct.

## Writing rules

| Rule | Example of the change |
|---|---|
| Start with what the person can do | "You can apply for ..." rather than "The department administers ..." |
| One idea per sentence; short sentences | split long sentences at "and", "which" |
| Use the words people use | "bin" or "trash" as your residents say it, the official term in brackets |
| Address the reader as "you" | "You need ..." |
| Steps as numbered lists | in How to apply |
| Avoid abbreviations; explain the needed ones once | |
| Dates and amounts written fully | "15 March", "25 euros a year" |

Guidance on plain language from government: [plainlanguage.gov](https://www.plainlanguage.gov/).

## Accessibility of service pages

| Point | Practice |
|---|---|
| Headings | from the template, in order (H1, then H2) |
| Links | text that says where it goes; "Apply online for <service>" rather than "click here" |
| Documents | an HTML explanation on the page; the file as a supplement |
| Language | `lang` set by the siteaccess; foreign words marked in the editor where needed |

The administration's own accessibility status and the gaps are in [Accessibility standards](../../../../standards/accessibility.md);
the public template is yours to test ([Testing accessibility](../../../creators/website-creators/testing-accessibility.md)).

## A review checklist for editors

- [ ] **In short** answers "what is this and is it for me?" in two or three sentences.
- [ ] Every required step is in **How to apply**, numbered.
- [ ] Cost and processing time are current.
- [ ] The online link opens the right form.
- [ ] The contact is the right desk.
- [ ] **Next review** is set to a date in the future.

## Related pages

- [Building a service catalog](README.md)
- [Modeling services](modeling-services.md)
- [Accessibility for site builders](../../../creators/website-creators/accessibility-for-site-builders.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
