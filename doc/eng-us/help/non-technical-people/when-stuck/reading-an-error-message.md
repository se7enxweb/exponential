---
title: Reading an error message
audience: non-technical, editor, user
category: plain-language
order: 52
summary: What the messages of Exponential mean in plain words - permission messages that are not errors, "not found", edit conflicts, form messages, and real errors with an ERR reference - and exactly what to note and pass on.
keywords: error message, ERR reference, access denied, not found, could not be displayed, something went wrong, form error, what it means, non-technical
type: help
---
# Reading an error message

Many messages that look alarming are not errors at all: the site is telling you something is not allowed or not there.
This page sorts them out.

## Contents

1. [Messages that are not errors](#messages-that-are-not-errors)
2. [Messages in the edit form](#messages-in-the-edit-form)
3. [A real error](#a-real-error)
4. [What to note](#what-to-note)

## Messages that are not errors

| The message says | In plain words | What to do |
|---|---|---|
| *The requested page could not be displayed. (1)*, *Your account does not have the proper privileges* | You may not open this page | Ask your administrator, if you need it |
| *The requested page could not be displayed. (20)*, *The requested address or module could not be found* | The address is wrong, or that part is not on this site | Check the address; use the menus |
| *Access denied!* | Your account is fine, but your role may not use this | Ask your administrator |
| *Possible edit conflict* | A draft of this item already exists | [When someone else is editing](../../editing-content.md#when-someone-else-is-editing) |
| *This link has expired.* (password reset) | The reset link is older than allowed | Ask for a new one |
| *This installation is a public demo* | A trial site whose administrator password is public | Never enter anything private there ([Demo mode](../../demo-mode.md)) |

([First: is it an error?](../../when-something-goes-wrong.md#first-is-it-an-error))

## Messages in the edit form

When you press **Send for publishing** and something is wrong, the form comes back with a message at the top and at
the field. Typical reasons:

- a **required field** (red star) is empty;
- a value has the wrong form, for example an e-mail address without `@`, or a date in the wrong format;
- a picture lacks its **alternative text** where the site requires one.

Fix the field and publish again. Your other changes are kept.

## A real error

A real error of the site shows a page such as **Something went wrong on our side**, with a **reference** at the
bottom, for example `ERR-0123456789`. The error has already been logged; the reference lets an administrator find
exactly that moment ([When something really went wrong](../../when-something-goes-wrong.md#when-something-really-went-wrong)).

1. Copy or photograph the reference.
2. Try once more. If it works now, still mention that it happened once.
3. Pass the reference on, with your notes.

## What to note

| Note | Example |
|---|---|
| The reference, if any | `ERR-0123456789` |
| The time | Tuesday, 10:42 |
| The address of the page | copied from the address bar |
| What you did, step by step | Opened the article *Summer fair*, pressed Edit, uploaded a photo, pressed Send for publishing |
| What you expected, and what happened | Expected the article to publish; got the error page |

A screenshot helps, as long as it shows no passwords or other people's personal data. Then follow
[How to ask for help](how-to-ask-for-help.md).

## Related pages

- [When you are stuck](README.md)
- [Something looks wrong](something-looks-wrong.md)
- [When something goes wrong](../../when-something-goes-wrong.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
