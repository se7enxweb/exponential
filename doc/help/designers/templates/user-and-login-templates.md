---
title: User and login templates
audience: designer, developer
category: templates
order: 710
summary: Styling the sign-in, registration, password and profile pages of the user module: the templates in user/, the field names the module reads, autocomplete and accessibility, the signed-in state in the pagelayout, and keeping user-specific output out of caches.
keywords: login template, user/login.tpl, register.tpl, forgotpassword.tpl, password.tpl, user edit, sign in page, Login, Password, RedirectURI, autocomplete, current_user, signed in
type: help
---
# User and login templates

This page is for designers who style the account pages of a public site: sign in, register, forgotten password, change
password, profile. They belong to the `user` module, whose templates live in `user/` of the design.

## Contents

1. [The templates](#the-templates)
2. [Field names you must keep](#field-names-you-must-keep)
3. [Accessible and password-manager friendly](#accessible-and-password-manager-friendly)
4. [The signed-in state in the frame](#the-signed-in-state-in-the-frame)
5. [Caching](#caching)

## The templates

`design/standard/templates/user/`: `login.tpl`, `register.tpl`, `success.tpl`, `activate.tpl`, `forgotpassword.tpl`,
`password.tpl`, `edit.tpl`, `setting.tpl` and the mail templates (`forgotpasswordmail.tpl`,
`password_changed_mail.tpl`, `registrationinfo.tpl`). Copy the ones you restyle into your design. The media design has
its own versions (its stylesheet `account.css` draws them, by its comment).

## Field names you must keep

The module reads the posted fields by name. In `user/login.tpl`: `Login`, `Password`, `LoginButton`, `RedirectURI`,
`Cookie` (stay signed in) and `RegisterButton`; the form posts to `/user/login`. Keep the names and the hidden
inputs; change only markup and classes. Registration and profile forms draw the user class's fields with
`attribute_edit_gui` ([Datatype edit templates](datatype-edit-templates.md)).

## Accessible and password-manager friendly

```tpl
<label for="login-name">{'Username or email'|i18n( 'design/mytheme/user' )}</label>
<input id="login-name" name="Login" type="text" autocomplete="username" required>
<label for="login-password">{'Password'|i18n( 'design/mytheme/user' )}</label>
<input id="login-password" name="Password" type="password" autocomplete="current-password" required>
```

On the change-password page use `autocomplete="new-password"`. Show errors next to the fields and in a summary
([Accessible forms](../theme-design/accessible-forms.md)). The forgotten password page must not reveal whether an
address has an account; the kernel's templates were changed for that, keep their wording.

## The signed-in state in the frame

In the pagelayout, `$current_user.is_logged_in` tells whether a visitor is signed in;
`$current_user.contentobject.name` is their name. Link to `/user/login`, `/user/logout` and `/user/edit` with `ezurl`
([Pagelayout variables](../theme-design/pagelayout-variables.md)).

## Caching

Anything showing the user's name or state must be outside the view cache (the pagelayout is not view-cached) and, in a
cache-block, keyed by the user (`$current_user.contentobject_id`) ([Cache-block](cache-block.md)).

## Related pages

- [Forms in templates](forms-in-templates.md)
- [Role and policy operators](role-and-policy-operators.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
