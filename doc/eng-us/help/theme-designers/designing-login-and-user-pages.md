---
title: "Designing login and user pages"
audience: theme-designer
category: theme-design
order: 23
summary: How theme designers design the account pages of an Exponential site - sign in, register, forgotten and changed passwords, profile, the mails that go with them, the two-factor pages where that extension is installed, and the signed-in state in the frame.
keywords: theme designer, login, sign in, register, forgot password, profile, user pages, account, two-factor, 2fa, password manager, user templates
type: help
---
# Designing login and user pages

This page is for theme designers whose site lets visitors sign in: members, customers, contributors, or only the
editors on the public siteaccess. The account pages are drawn by the **user module**'s templates inside your frame,
and they are among the pages people use with the least patience. The templates and the field names that must stay are
listed in [User and login templates](../designers/templates/user-and-login-templates.md).

## Contents

1. [The pages](#the-pages)
2. [Sign in](#sign-in)
3. [Register](#register)
4. [Forgotten and changed passwords](#forgotten-and-changed-passwords)
5. [Profile and settings](#profile-and-settings)
6. [Two-factor pages](#two-factor-pages)
7. [The mails](#the-mails)
8. [The signed-in frame](#the-signed-in-frame)
9. [Checks](#checks)

## The pages

| Page | Template in the kernel design | Design note |
|---|---|---|
| Sign in | `user/login.tpl` | the most used; also embedded in the access-denied page |
| Register | `user/register.tpl`, `user/success.tpl` | only if visitors may register |
| Activate | `user/activate.tpl` | after the activation mail |
| Forgotten password | `user/forgotpassword.tpl` | must not reveal whether an address has an account |
| Change password | `user/password.tpl` | |
| Edit profile, settings | `user/edit.tpl`, `user/setting.tpl` | fields come from the user class |

Copy only the templates you restyle into your design ([Override basics](../designers/templates/override-ini-basics.md)
explains templates and overrides).

## Sign in

- A short page: heading, two fields, "stay signed in" if the site offers it, one button, and links to "Forgot your
  password?" and "Register".
- Labels visible, and the right autocomplete hints so password managers fill the form
  ([User and login templates](../designers/templates/user-and-login-templates.md#accessible-and-password-manager-friendly)).
- An error for a wrong password that does not say which of the two was wrong.
- The same form also appears inside the access-denied page for anonymous visitors, with the shipped error settings
  ([Designing empty and error states](designing-empty-and-error-states.md#access-denied)). Design it to work in both
  places: as a page of its own and as part of another page.

## Register

The registration form draws the fields of the **user class** of the installation, through the datatype edit templates,
so its fields can change when the class changes. Design the field types, not a fixed form ([Designing forms](designing-forms.md)).
Design the result pages: "check your mail to activate" and "your account is active".

## Forgotten and changed passwords

- The forgotten password page always shows the same confirmation, whether or not the address exists. Keep the
  kernel's wording; it was written that way on purpose.
- The change password page asks for the new password twice; show the password rules before the visitor types, not
  only after an error.

## Profile and settings

Profile pages show and edit the user object's fields. Keep them plain and close to the form design of the rest of the
site. If visitors can upload a picture, it is an image field: give it an image alias and a design without a picture.

## Two-factor pages

Where the sevenx_authentication_2fa extension is installed, signing in can need a second step. The extension ships
templates in a `user2fa/` folder (`verify.tpl` for entering a code, `setup.tpl` for enrolling, `callback.tpl`) in its
standard design, and the demonstration installation's media design has its own versions. Design them like the sign-in
page: one field for the code, a clear explanation, and a way out ("use another method", "sign out"). Ask the developer
which steps your site uses before designing them.

## The mails

Registration, activation and forgotten-password mails are templates too (`registrationinfo.tpl`,
`forgotpasswordmail.tpl`, `password_changed_mail.tpl`). They are short plain messages; keep the link on its own line
and say who sent the mail and why. Test mails go to `example.invalid` addresses on a development installation, never
to real people.

## The signed-in frame

After signing in, the frame shows the visitor's name and links to their profile and to sign out
([Designing headers and footers](designing-headers-and-footers.md#signed-in-or-not)). The administration has its own
sign-in page, styled by the administration design; a public theme does not restyle it
([Admin designs overview](../designers/theme-design/admin-designs-overview.md)).

## Checks

- [ ] Sign in as a page and inside the access-denied page.
- [ ] Wrong password, locked or inactive account messages drawn.
- [ ] Password manager fills the form (tested in two browsers).
- [ ] Registration form with the real user class fields.
- [ ] Mails read well as plain text.
- [ ] Light and dark, three widths, keyboard only.

## Related pages

- [User and login templates](../designers/templates/user-and-login-templates.md)
- [Accessible forms](../designers/theme-design/accessible-forms.md)
- [Accounts and sign-in](../accounts-and-sign-in.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
