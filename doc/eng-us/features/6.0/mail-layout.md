# The mail layout: every mail as HTML with a plain-text part

Since 6.0.15 every mail Exponential sends is an HTML mail with a plain-text part (`multipart/alternative`) in one
shared layout: the site's name in a dark header, a heading, a short text, one button where there is a link, the
address of the button as text below it (for a mail program that shows no buttons), the security notes in small print
and a footer. Mail programs that show only text get the same content as clean text.

Before 6.0.15 most of these mails were plain text with addresses on their own lines and, in places, clumsy wording
("Use this code within this link for fast login by clicking this link to continue your login").

## Which mails

| Mail | Sent to | Template |
|---|---|---|
| Registration info, account approved, activation mail sent again | the user | `user/registrationinfo.tpl`, `user/registrationapproved.tpl`, `user/unactivated_resend_mail.tpl` |
| New user registered | the administrator | `user/registrationfeedback.tpl` |
| Account approval (workflow event) | the approvers | `workflow/eventtype/expuserapprove_mail.tpl` |
| Forgot your password: the reset link, then the new password | the user | `user/forgotpasswordmail.tpl` |
| Your password was changed | the user | `user/password_changed_mail.tpl` |
| New sign-in to your account | the user | `user/new_signin_mail.tpl` |
| New sign-in of a user | the administrators | `user/new_signin_admin_mail.tpl` |
| Confirm a subscription, confirm a new e-mail address, the link to the preferences | the person | `mailpreferences/mail/confirm.tpl`, `email_change.tpl`, `link.tpl` |
| The e-mail address of your account is being changed | the old address | `mailpreferences/mail/email_change_notice.tpl` |
| Tip a friend | the friend | `content/tipafriendmail.tpl` |
| Collected information (forms, feedback) | the form's receiver | `content/collectedinfomail.tpl`, `content/collectedinfomail/*.tpl` (all through `content/collectedinfomail_body.tpl`) |
| Order confirmation | the customer, a copy to the administrator | `shop/orderemail.tpl` |
| Notifications (new and updated content, approvals, digests) | the subscribers | the text templates of `notification/handler/*/view/`, made HTML when sent |
| Audit alerts | the audit's receivers | made HTML when sent (`expAuditMailSink`) |
| Two-step sign-in code (sevenx_authentication_2fa) | the user | the extension's `mail/2fa_code.tpl` |
| Reports of xrowextract and ezoracle, the newsletter's own system mails (cjw_newsletter) | the receivers they configure | made HTML when sent |

The newsletters of cjw_newsletter keep their own skins. The site registration of the setup wizard
(`setup/registration_email.tpl`) stays plain text: it is a report for the project, not a mail to a person.

## How it works

- `design:mail/layout.tpl` is the HTML layout and `design:mail/layout_text.tpl` its plain-text twin. A mail template
  passes only text and addresses (`mail_title`, `mail_intro`, `mail_code`, `mail_rows`, `mail_paragraphs`,
  `mail_notice`, `mail_buttons`, `mail_links_note`, `mail_outro`, `mail_footer`, `mail_site_name`, `mail_preheader`;
  the list with their meaning is at the top of `layout.tpl`). The layout escapes every value, so a name or a comment a
  visitor typed cannot add markup.
- The template sets `content_type` to `text/html` and `body_text` to the output of `layout_text.tpl`; the sender
  hands both to `expMailTemplateBody::apply()` (or `applyRendered()`), which builds the two parts.
- Mails written as text (notifications, alerts, reports) go through `expMailTemplateBody::fromText()`: the text stays
  the text part; for the HTML part its paragraphs are put into the layout, addresses become links, and the first
  address on a line of its own becomes the button. `notification.ini [MailSettings] HTMLLayout=disabled` sends
  notifications as text only.
- A code to type in (the sign-in code, a new password) is shown large in a box of its own, easy to select and copy.
- The layout is built for mail programs: tables, inline styles and `bgcolor` (mail programs drop style sheets, so the
  administration's rule of no inline CSS does not apply to mail bodies), at most 600 pixels wide, readable at 375
  pixels. Programs that support a dark mode (Apple Mail, iOS, Outlook for Mac) get dark colors from a media query;
  the others show the light colors, which every element carries itself.
- The footer of an optional mail (why you get it, unsubscribe, manage your preferences) is put inside the card of the
  layout, in the text part and in the HTML part.

## Your own look

Override `mail/layout.tpl` (and `mail/layout_text.tpl` if needed) in your design with the same inputs: the logo
(`mail_logo_url`, `mail_logo_alt`, `mail_logo_width`, `mail_logo_height`), the colors at the top of the file, the
fonts. Every mail then takes the new look. A content template is overridden as before; keep setting `content_type`
and `body_text` in it.

The test `tests/tests/kernel/classes/mail/expMailLayoutMailsTest.php` shows how each mail is rendered with sample data
and without a database; it checks a changed layout too.
