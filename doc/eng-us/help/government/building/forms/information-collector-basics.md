---
title: Information collector basics
audience: government, website-creator, admin
category: public-sector
order: 20
summary: How Exponential's information collector works for public-sector forms - collector attributes in a class, the form template posting to content/action, the form, feedback and poll types of collect.ini, the result page or redirect, and where submissions are stored.
keywords: information collector, collector attribute, content/action, ActionCollectInformation, collect.ini, form type, feedback, poll, Display, RedirectURL, CollectAnonymousData, Setup Collected information
type: help
---
# Information collector basics

This page is for website creators and administrators who build their first form for a public-sector site. It
summarizes how the information collector works, with the settings that matter, and points to the full guides:
[Forms with the information collector](../../../creators/website-creators/forms-with-the-information-collector.md)
and [Collected information](../../../../guides/collected-information.md).

## Contents

1. [How it works](#how-it-works)
2. [The shipped form classes](#the-shipped-form-classes)
3. [Form types and their settings](#form-types-and-their-settings)
4. [After sending](#after-sending)
5. [A first form, step by step](#a-first-form-step-by-step)

## How it works

1. A content class has attributes marked **Information collector** in the class editor. Visitors fill those in;
   editors fill the other attributes (a title, an introduction).
2. An editor creates an object of the class; its full view shows the form.
3. The form posts to `content/action` with the button `ActionCollectInformation`; each collector attribute is checked
   by its datatype (required, valid e-mail ...).
4. On success one **collection** is stored, a mail may be sent, and the visitor sees a result page or is redirected.
5. Staff with the `infocollector/read` policy read and export the collections in **Setup > Collected information**
   (`/infocollector/overview`).

## The shipped form classes

| Class | Identifier | Collector attributes |
|---|---|---|
| Feedback form | `feedback_form` | Sender name, Subject, Message (Text block), Email; plus Name, Description and a Recipient (Email) filled by editors |
| Poll | `poll` | an option list (`ezoption`) |
| Contact form | `ng_contact_form` | in the default package's demo content classes |
| Advanced feedback form | `cjw_feedback_form` | where the CJW extensions are installed |

Add your own class when you need other fields ([Content classes for websites](../../../creators/website-creators/content-classes-for-websites.md)).

## Form types and their settings

`settings/collect.ini` decides behavior per type; an object's type comes from a `collection_type` attribute, else
`[InfoSettings] Type=form`:

| Block | Key | Shipped | Meaning |
|---|---|---|---|
| `InfoSettings` | `TypeList[]` | poll, form, feedback | known types |
| `EmailSettings` | `SendEmail`, `SendEmailList[poll]` | enabled; disabled for polls | mail on submission |
| `DisplaySettings` | `Display`, `RedirectURL` | `result`, `/` | result page or redirect |
| `CollectionSettings` | `CollectAnonymousData` | enabled | visitors who are not signed in may send |
| `CollectionSettings` | `CollectionUserData`, `CollectionUserDataList[poll]` | multiple; unique for polls | one answer per user for polls |

Change them in `settings/override/collect.ini.append.php` or per siteaccess, then clear the INI cache
(`php bin/php/ezcache.php --clear-tag=ini`).

## After sending

| Setting | Effect | Use for |
|---|---|---|
| `Display=result` | the type's result template shows the sent values | polls (results) |
| `Display=redirect` with `RedirectURL` | the visitor goes to a page | forms: a "thank you" page that says what happens next and when |

A redirect also stops a reload from sending the form again.

## A first form, step by step

1. Create a folder "Contact" in the tree.
2. **Create new** > **Feedback form**: name "Contact us", a description saying what happens with the message and how
   long it is kept, and the recipient mailbox of the team.
3. **Send for publishing**; open it logged out; send a test message.
4. Open **Setup > Collected information**: the form is listed with one collection.
5. Check the mail arrived (test with the file transport first: [Mail to receivers](mail-to-receivers.md)).

For every detail: [Building a contact form, step by step](../../../creators/website-creators/building-a-contact-form.md).

## Related pages

- [Building forms](README.md)
- [Accessible forms](accessible-forms.md)
- [Reading and exporting form submissions](../../../creators/website-creators/form-submissions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
