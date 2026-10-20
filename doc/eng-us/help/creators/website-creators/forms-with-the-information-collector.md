---
title: Forms with the information collector
audience: website-creator, developer
category: website-creation
order: 450
summary: How visitor forms work in Exponential - collector attributes, the content/action post, validation, the result page, mail to a receiver, form types (form, feedback, poll) in collect.ini, and where submissions are stored.
keywords: information collector, forms, feedback form, contact form, poll, collect.ini, ActionCollectInformation, collectedinfo, EmailReceiver, form types, website creator
type: help
---
# Forms with the information collector

This page is for website creators who build forms visitors fill in: contact forms, feedback forms, polls, lead forms.
Exponential does this with the **information collector**: a class whose attributes are marked
**Information collector**, an object of it shown as a form, and a stored **collection** for every submission
([Collected information](../../../guides/collected-information.md)).

## Contents

1. [How it works](#how-it-works)
2. [The form template](#the-form-template)
3. [Form types: form, feedback, poll](#form-types-form-feedback-poll)
4. [Mail to a receiver](#mail-to-a-receiver)
5. [After sending](#after-sending)
6. [Privacy](#privacy)

## How it works

1. A class (the shipped **Feedback form**, `feedback_form`) has attributes marked **Information collector**: sender
   name, e-mail, subject, message. Other attributes (a description) are written by editors as usual.
2. An editor creates an object of the class; its full view shows the form.
3. The visitor posts the form to `content/action` with the button `ActionCollectInformation`; the kernel's
   `content/collectinformation` view validates each collector attribute with its datatype.
4. On success one collection is stored, a mail may be sent, and the visitor sees the result page or is redirected.
5. Editors read and export the collections in **Setup > Collected information** ([Reading and exporting form submissions](form-submissions.md)).

## The form template

The shipped ezwebin template (`extension/ezwebin/design/ezwebin/override/templates/full/feedback_form.tpl`) shows the
pattern:

```text
{include name=Validation uri='design:content/collectedinfo_validation.tpl'
         class='message-warning' validation=$validation collection_attributes=$collection_attributes}
<form method="post" action={'content/action'|ezurl}>
  <label for="sender">{$node.data_map.sender_name.contentclass_attribute.name|wash}</label>
  {attribute_view_gui attribute=$node.data_map.sender_name}
  ...
  <input type="submit" name="ActionCollectInformation" value="{'Send form'|i18n( 'design/mysite' )}">
  <input type="hidden" name="ContentNodeID" value="{$node.node_id}">
  <input type="hidden" name="ContentObjectID" value="{$node.object.id}">
  <input type="hidden" name="ViewMode" value="full">
</form>
```

A collector attribute's view template renders its input field. The form token that protects POST forms is added by
the `ezformtoken` extension; leave it active ([Spam protection for forms](spam-protection-for-forms.md)).

## Form types: form, feedback, poll

`settings/collect.ini` decides behavior per type. The type of an object comes from its `collection_type` attribute,
else `[InfoSettings] Type=form`:

| Block | Key | Shipped | Meaning |
|---|---|---|---|
| `InfoSettings` | `TypeList[]` | `poll`, `form`, `feedback` | known types |
| `EmailSettings` | `SendEmail`, `SendEmailList[poll]` | `enabled`, `disabled` for polls | mail on submission |
| `DisplaySettings` | `Display`, `RedirectURL` | `result`, `/` | show the result page or redirect |
| `CollectionSettings` | `CollectAnonymousData` | `enabled` | visitors who are not logged in may send |
| `CollectionSettings` | `CollectionUserData`, `CollectionUserDataList[poll]` | `multiple`, `unique` | a poll can be answered once per user |

The result templates are `content/collectedinfo/<type>.tpl` (form, feedback, poll in `design/standard`), the mail
templates `content/collectedinfomail/<type>.tpl`; override them in your design.

## Mail to a receiver

`site.ini [InformationCollectionSettings] EmailReceiver` is the default receiver. The mail template can set the
receiver per form with `{set-block scope=root variable=email_receiver}...{/set-block}`; the ezwebin mail templates
(`extension/ezwebin/design/ezwebin/templates/content/collectedinfomail/feedback.tpl`) take it from a `recipient`
attribute of the form object when it has one, so each form can mail its own team. The same templates can set
`redirect_to_node_id` to send the visitor to a node afterwards. Mail goes through the site's mail settings
([10.4 Mail](../../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending)); test with the file
transport first.

## After sending

`Display=result` shows `content/collectedinfo/<type>.tpl` with the values; `Display=redirect` sends the visitor to
`RedirectURL` (a "thank you" page). Use the redirect so a reload does not resend.

## Privacy

Forms collect personal data. Collect only what you need, say what happens with it next to the form, limit
`infocollector/read` to the people who process it, and remove collections when they are no longer needed.

## Related pages

- [Building a contact form, step by step](building-a-contact-form.md)
- [Reading and exporting form submissions](form-submissions.md)
- [Spam protection for forms](spam-protection-for-forms.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
