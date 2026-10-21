---
title: About page and contact form
audience: individual, website-creator, editor
category: personal-sites
order: 1603
summary: How one person writes an About page that works and sets up a contact form with the shipped Feedback form class - where messages are stored and mailed, how to keep your mail address private, how to stop spam and what to tell visitors about the data the form collects.
keywords: about page, contact form, feedback form, information collector, recipient, mail address, spam, CAPTCHA, form token, privacy notice, collected information, personal site
type: help
---
# About page and contact form

This page is for individuals who want visitors to know who is behind the site and to be able to write without
your mail address being printed on a page. Both are short to build; both matter more than any other page of a
personal site.

## Contents

1. [The About page](#the-about-page)
2. [The contact form: what ships](#the-contact-form-what-ships)
3. [Setting up the form](#setting-up-the-form)
4. [Where messages go](#where-messages-go)
5. [Keeping spam out](#keeping-spam-out)
6. [Telling visitors what you store](#telling-visitors-what-you-store)
7. [Checklist](#checklist)

## The About page

An *Article* with a title, an intro and a body is enough. A structure that works for most people:

| Part | Content | Length |
|---|---|---|
| Intro | Who you are and what you do, in one sentence | One line |
| Picture | A photo or an image that stands for your work, with alternative text | One image |
| Body: now | What you are working on | A paragraph |
| Body: before | The path that led here | A paragraph or a short list |
| Body: next | What you are looking for (work, collaborators, readers) | A sentence |
| Link | "Write to me" pointing to the contact form | One link |

Write in the first person, keep it current, and leave out what you would not tell a stranger
([Planning a personal site](planning-a-personal-site.md#what-you-publish-about-yourself)).

## The contact form: what ships

The default site package has a *Feedback form* class with these fields: name, description, sender name, subject,
message, e-mail, recipient and metadata (the class definition in the `sevenx_classes` package). Visitor fields are
*information collector* fields: what a visitor types is stored by the site and can be mailed
([Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md)).

## Setting up the form

1. Create a *Feedback form* object where your Contact page should be, for example below the home folder.
2. Give it a name ("Contact") and a description that says what to write about and how soon you answer.
3. Set the **recipient** to the address that should receive messages. It is stored on the object, not shown to visitors.
4. Publish and open the page as a visitor (in a private browser window). Anonymous visitors must be able to read
   the form's page to send it, so keep it in a section they may read. If the form does not appear or does not send,
   see [Visitor permissions](../../creators/website-creators/visitor-permissions.md).
5. Send yourself a test message and check it arrives.

The detailed builder version, including a form of your own design, is
[Building a contact form](../../creators/website-creators/building-a-contact-form.md).

## Where messages go

| Place | What you find there |
|---|---|
| Your mailbox | A mail per message, if outgoing mail works on your server |
| The administration | Every submission, stored even if the mail failed ([Form submissions](../../creators/website-creators/form-submissions.md), [Collected information](../../../guides/collected-information.md)) |

If test messages do not arrive, the form is usually fine and the mail is not: the server cannot send, or the mail
lands in spam. That is a hosting question ([Mail preferences for administrators](../../../guides/mail-preferences-administrator.md)).
Check the stored submissions now and then anyway.

## Keeping spam out

A public form will receive spam within days. Use the layers that ship:

- **Form tokens** (`ezformtoken`, part of the default installation) stop forged posts from other sites.
- **A CAPTCHA field**: `recaptcha` or `hcaptcha` add a datatype you put on the class; each needs keys from its service.
- **Limit what anonymous visitors may create** to the form only.

See [Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md) and
[Spam and security basics](../running-it/spam-and-security-basics.md).

## Telling visitors what you store

The form stores the sender's name, address and message on your server. Say so in one or two sentences next to the
form or on a privacy page: what is stored, why, and when you delete it. Then actually delete old submissions on that
schedule ([Data retention and deletion](../../businesses/security/data-retention-and-deletion.md)). This is general
information, not legal advice.

## Checklist

- [ ] About page: intro, picture with alternative text, current text, link to Contact.
- [ ] Feedback form published with the right recipient.
- [ ] Test message arrived by mail and is stored in the administration.
- [ ] Form token active; CAPTCHA added if spam arrives.
- [ ] A sentence on what the form stores and for how long.
- [ ] A reminder to delete old submissions.

## Related pages

- [Personal sites](README.md)
- [Building a contact form](../../creators/website-creators/building-a-contact-form.md)
- [Forms, polls and sign-ups](../club-sites/forms-polls-and-sign-ups.md)
- [Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
