---
title: Forms and information collection
audience: cms-developer
category: cms-development
order: 34
summary: For CMS developers building contact forms, polls and lead forms without code, using information collection. It covers collector attributes, the form and result templates, the collect.ini settings for mail, display, anonymous and repeated answers, reading and exporting collections, spam and privacy, and when a module of your own is the better tool.
keywords: forms, contact form, feedback form, poll, information collection, information collector, collect.ini, ActionCollectInformation, content/action, collectedinfo, collectedinfomail, infocollector, collected information, CSV export, bccie, captcha, privacy
type: help
---
# Forms and information collection

This page is for developers who need forms on a site: contact, feedback, polls, event sign-up, lead capture.
Exponential builds most of them from the content model: a form is an object of a class whose attributes are marked
**information collector**, editors create and change forms like any content, and the answers are stored and mailed by
the kernel.

## Contents

1. [How it works](#how-it-works)
2. [A form class](#a-form-class)
3. [The form template](#the-form-template)
4. [After sending: result page and mail](#after-sending-result-page-and-mail)
5. [Settings per form type](#settings-per-form-type)
6. [Reading and exporting answers](#reading-and-exporting-answers)
7. [Spam and privacy](#spam-and-privacy)
8. [When to write a module instead](#when-to-write-a-module-instead)

## How it works

```text
form object (class with collector attributes), full view
  -> <form> posts to /content/action with ActionCollectInformation
     -> the kernel validates each collector attribute, stores one collection
        -> result page (content/collectedinfo/<type>.tpl) and, if enabled, a mail (content/collectedinfomail/<type>.tpl)
```

Answers are not content objects: they are collections in their own tables, read under **Setup** > **Collected
information** ([Collected information](../../guides/collected-information.md)).

## A form class

Create a class (for example `exp_contact_form`) with:

- normal attributes for what editors write: a title, an intro (`ezxmltext`), and, if you use them, the attributes that
  `collect.ini` reads per object (see below);
- **information collector** attributes for what visitors fill: Text line for a name, Email for the address, Text block
  for the message, Selection for a choice, Checkbox for consent.

Mark the collector attributes **Required** where the form needs them; the kernel then refuses incomplete answers with
a validation message.

## The form template

The form object's full view prints the form. Each collector attribute is drawn with `attribute_view_gui`, which uses
the datatype's collect template and prints an input; the submit button is named `ActionCollectInformation`, with the
hidden fields `ContentNodeID`, `ContentObjectID` and `ViewMode`. A complete template with labels and validation
messages: [Forms in templates](../designers/templates/forms-in-templates.md#the-form-template). Start from the form
template of the design you build on, so the hidden fields and the form token stay right, and give inputs ids for their
labels ([Accessible forms](../designers/theme-design/accessible-forms.md)).

## After sending: result page and mail

The result page is `content/collectedinfo/<type>.tpl` and the mail `content/collectedinfomail/<type>.tpl`, where the
type comes from `collect.ini` (shipped: `form`, `poll`, `feedback`). Override both in your design: the result page for
the thank-you message, the mail for its subject and body (set with `{set-block scope=root variable=subject}`).
Test mail with a recipient at `example.invalid` and a file transport on development machines.

## Settings per form type

`settings/collect.ini` decides behavior per type, and lets a form object override it through an attribute of its own:

| Group | Setting | Shipped | With the attribute |
|---|---|---|---|
| `[InfoSettings]` | `Type`, `TypeList[]` | `form`; `poll`, `form`, `feedback` | `collection_type` |
| `[EmailSettings]` | `SendEmail`, `SendEmailList[poll]` | `enabled`; polls `disabled` | `collection_email` |
| `[DisplaySettings]` | `Display`, `RedirectURL` | `result`, `/` | `collection_display`, `collection_redirect` |
| `[CollectionSettings]` | `CollectAnonymousData` | `enabled` | `collection_anonymous` |
| `[CollectionSettings]` | `CollectionUserData` | `multiple`; polls `unique` | `collection_userdata` |
| `[CollectionSettings]` | `CollectSensitiveData` | `true`; polls `false` | |

So one class can serve many forms, and an editor can, for example, choose per form where the answers go. Put your
changes in your extension's `collect.ini.append.php`.

## Reading and exporting answers

**Setup** > **Collected information** lists the objects that collected, with each object's collections, a CSV
export and removal after confirmation; it needs the policy `infocollector/read`. In templates, the content fetch
functions `collected_info_count`, `collected_info_count_list`, `collected_info_collection` and `collected_info_list`
read answers, for example to show poll results. The `bccie` extension of the default distribution exports collected
information as well ([Mapping content: forms](../../install/migration/content-model-mapping.md#9-comments-forms-products)).

## Spam and privacy

- Add a captcha datatype (`recaptcha` or `hcaptcha`) as a collector attribute to public forms
  ([Choosing datatypes](choosing-datatypes.md)).
- Keep the form token (ezformtoken) in the template; do not remove hidden fields the original prints.
- Collect only what the form needs, say on the form what happens with the answers, and remove old collections on a
  schedule; answers are personal data ([Privacy standards](../../standards/privacy.md)).
- Never print answers back unwashed.

## When to write a module instead

Information collection fits forms whose answers are stored and mailed. When a form must create content (a member
submits an article), call another system, or follow a multi-step flow, write a module view with a policy in your
extension, or let users create content with `content/create` limited by role
([Modules and views](../extension-development/part-3-extension-developer/modules-and-views.md),
[Forms and actions](../trainers/developers/module-06-modules-and-views/forms-and-actions.md)).

## Related pages

- [Collected information](../../guides/collected-information.md)
- [Workflows and triggers](workflows-and-triggers.md)
- [Users and registration](users-and-registration.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
