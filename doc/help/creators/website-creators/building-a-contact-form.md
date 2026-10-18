---
title: Building a contact form, step by step
audience: website-creator, editor
category: website-creation
order: 460
summary: A complete contact form in ten steps - class with collector fields, the form object, its template, mail to your team, a thank-you redirect, spam protection, permissions for visitors and a final test.
keywords: contact form, feedback form, tutorial, information collector, collect.ini, thank you page, redirect, EmailReceiver, recaptcha, hcaptcha, website creator
type: help
---
# Building a contact form, step by step

This page is for website creators who need a working contact form. It uses the information collector
([Forms with the information collector](forms-with-the-information-collector.md)). If your site package ships the
**Feedback form** class (`feedback_form`), steps 1 and 2 are already done; start at step 3.

## Steps

**1. The class.** In **Setup > Classes > Content > New class**: name `Contact form`, identifier `contact_form`,
container off. Attributes:

| Name | Identifier | Datatype | Required | Information collector |
|---|---|---|---|---|
| Title | `title` | Text line | yes | no |
| Introduction | `description` | Rich text | no | no |
| Your name | `sender_name` | Text line | yes | yes |
| Your e-mail | `email` | E-mail | yes | yes |
| Message | `message` | Text block | yes | yes |

Object name pattern: `<title>`.

**2. Spam protection.** Add a reCAPTCHA or hCaptcha attribute when one of those extensions is active
([Spam protection for forms](spam-protection-for-forms.md)).

**3. The form object.** Create a Contact form called "Contact" under the root folder, write an introduction, publish.

**4. The template.** Copy the shipped feedback form template into your design as
`override/templates/full/contact_form.tpl`, rename the fields to your identifiers, and add the override rule:

```ini
# settings/siteaccess/<site>/override.ini.append.php
[full_contact_form]
Source=node/view/full.tpl
MatchFile=full/contact_form.tpl
Subdir=templates
Match[class_identifier]=contact_form
```

Give every input a visible `<label>`; mark required fields in text, not only by color.

**5. Mail.** Set the receiver:

```ini
# settings/siteaccess/<site>/site.ini.append.php
[InformationCollectionSettings]
EmailReceiver=team@example.com
```

and override `content/collectedinfomail/form.tpl` if the mail should look different.

**6. Thank-you page.** Create a page "Thank you" and redirect to it:

```ini
# settings/siteaccess/<site>/collect.ini.append.php
[DisplaySettings]
Display=redirect
RedirectURL=/Thank-you
```

**7. Visitors may send.** Anonymous needs `content/read` on the form (Section Standard is enough) and the
`content/collectinformation` view uses the read function, so nothing else is needed. Leave
`CollectAnonymousData=enabled`.

**8. Caches.** `php bin/php/ezcache.php --clear-id=template-override --allow-root-user`, then `--clear-tag=template`
and `--clear-tag=content`.

**9. Test, logged out.** Send the form with an empty required field (an error message appears beside it), then
correctly (the thank-you page appears). With the file mail transport, check the mail in the mail directory.

**10. Check the submission** in **Setup > Collected information**: the Contact object has one collection.

## Checklist

- [ ] Every input has a label; errors are announced next to the field.
- [ ] Spam protection is on.
- [ ] The privacy note says what happens with the data.
- [ ] Mail arrives at the team address (with real mail switched on).
- [ ] Reloading the thank-you page does not resend.

## Related pages

- [Forms with the information collector](forms-with-the-information-collector.md)
- [Reading and exporting form submissions](form-submissions.md)
- [Template overrides for site builders](template-overrides.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
