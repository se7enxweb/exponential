---
title: Volunteer sign-up form, step by step
audience: non-profit, website-creator, editor, businesses
category: business-solutions
order: 20
summary: Building a volunteer sign-up form in Exponential from start to finish - a class with information collector attributes, the form object on a Volunteer page, the receiver and mail, a redirect to a thank-you page, a CAPTCHA, optional file uploads with enhancedezbinaryfile, the text next to the form, and the tests to run logged out.
keywords: volunteer form, sign-up form, information collector, class, attributes, feedback_form, receiver, recipient, EmailReceiver, collect.ini, Display=redirect, thank-you page, CAPTCHA, enhancedezbinaryfile, testing
type: help
---
# Volunteer sign-up form, step by step

This page is for the website creator who builds the form and the coordinator who tests it. Decide the fields first with
the [Volunteer form field worksheet](../templates/volunteer-form-field-worksheet.md). The mechanism is the information
collector, explained in full in [Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md).

## Contents

1. [Step 1: the class](#step-1-the-class)
2. [Step 2: the form object](#step-2-the-form-object)
3. [Step 3: who gets the mail](#step-3-who-gets-the-mail)
4. [Step 4: after sending](#step-4-after-sending)
5. [Step 5: spam protection](#step-5-spam-protection)
6. [Step 6: file uploads, only if needed](#step-6-file-uploads-only-if-needed)
7. [Step 7: test](#step-7-test)
8. [Checklist](#checklist)

## Step 1: the class

The shipped `feedback_form` class is a ready example; a volunteer form is a class of your own built the same way
([Building a contact form, step by step](../../creators/website-creators/building-a-contact-form.md)).

1. In **Setup > Classes**, create a class "Volunteer form" (identifier `volunteer_form`).
2. Add a title and a description (rich text) for the editor-written text above the form.
3. Add one attribute per field from the worksheet, and tick **Information collector** on each field visitors fill in
   ([Content classes for websites, Attribute settings](../../creators/website-creators/content-classes-for-websites.md#attribute-settings)).
4. Add a `recipient` attribute (e-mail) if each form should mail its own coordinator (step 3).
5. Mark the fields that are required.

| Field | Datatype |
|---|---|
| Name | `ezstring` |
| E-mail | `ezemail` |
| Areas of interest | `ezselection` |
| Availability | `ezselection` or `eztext` |
| Message | `eztext` |
| Consent | `ezboolean` |

## Step 2: the form object

1. Create a "Volunteer with us" page in the menu.
2. Below or on it, create an object of your form class. Write the description: what volunteering involves, who reads
   the form, how long you keep it, a link to the privacy notice.
3. Publish. The object's page now shows the form.

Templates for a new class may need a template for the form; your website creator places it in the design
([Template overrides](../../creators/website-creators/template-overrides.md)).

## Step 3: who gets the mail

From [Forms with the information collector, Mail to a receiver](../../creators/website-creators/forms-with-the-information-collector.md#mail-to-a-receiver):

- `site.ini [InformationCollectionSettings] EmailReceiver` is the default receiver.
- The ezwebin mail templates take the receiver from a `recipient` attribute of the form object when it has one, "so
  each form can mail its own team".
- `collect.ini [EmailSettings] SendEmail` is enabled for forms by default.

Use a shared mailbox (for example volunteers@ your domain) rather than one person's address, so applications are not
lost when a coordinator leaves. Test with the file transport before real mail is switched on.

## Step 4: after sending

Prefer a redirect to a thank-you page (`Display=redirect`), "so a reload does not resend" (same page). The
thank-you page says when the volunteer will hear from you.

## Step 5: spam protection

The form token is active by default. Add a CAPTCHA attribute (`hcaptcha` or `recaptcha`) to the class; see
[Spam protection for volunteer forms](spam-protection-for-volunteer-forms.md).

## Step 6: file uploads, only if needed

`enhancedezbinaryfile` keeps uploaded files in a download folder with a limit, `MaxFiles` 25 by default, and mails
the collected information as a multi-part mail when a file is attached. "`0` keeps an unlimited number, which is not
advisable for a site with anonymous uploads" ([enhancedezbinaryfile](../../../features/6.0/extensions/enhancedezbinaryfile.md)).
Often it is better to ask for documents later, from accepted volunteers only.

## Step 7: test

Test while **logged out**, as a visitor would:

- [ ] The form appears; labels are visible and read by a screen reader.
- [ ] Required fields refuse an empty submission with a clear message.
- [ ] The CAPTCHA appears and is solvable, including its accessible alternative.
- [ ] A submission arrives in **Setup > Collected information**.
- [ ] The coordinator's mailbox receives the mail.
- [ ] The thank-you page appears, and reloading does not send twice.
- [ ] The form works on a phone and with the keyboard only.

## Checklist

- [ ] Fields match the approved worksheet.
- [ ] Description text approved, privacy link present.
- [ ] Receiver is a shared mailbox.
- [ ] Redirect to a thank-you page.
- [ ] CAPTCHA added and tested.
- [ ] Uploads only if needed, with `MaxFiles` set.
- [ ] `infocollector/read` only for coordinators ([Processing volunteer applications](processing-volunteer-applications.md)).

## Related pages

- [Volunteers](README.md)
- [Volunteers and sign-up forms](../../businesses/nonprofits/volunteers-and-sign-up-forms.md)
- [Accessible forms](../../government/accessibility/accessible-forms.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
