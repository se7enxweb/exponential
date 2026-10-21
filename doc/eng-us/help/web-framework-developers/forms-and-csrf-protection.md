---
title: Forms and CSRF protection
audience: web-framework-developer, developer
category: web-frameworks
order: 260
summary: How forms work in Exponential for developers used to form components, form requests and CSRF middleware - templates that write the form, module actions that read it, eZHTTPTool for input, ezformtoken's automatic token for signed-in users, the X-CSRF-Token header for scripts, POST-redirect-GET, uploads, forms for visitors, and what has no direct equivalent.
keywords: forms, CSRF, form token, ezformtoken, ezxFormToken, ezxform_token, X-CSRF-Token, HTMLForms Secret, eZHTTPTool, postVariable, module actions, single_post_actions, POST redirect GET, eZHTTPFile, uploads, information collector, Symfony Form, Django forms, web framework developer
type: help
---
# Forms and CSRF protection

This page is for developers used to Symfony Form types, Laravel form requests, Django forms or Rails form helpers.
Exponential has no form component that builds, binds and validates a form object. A form is HTML in a template; the
view reads the posted values through the kernel's helpers and **module actions**; and the `ezformtoken` extension
protects POST requests against cross-site request forgery without code in your forms.

## Contents

1. [The parts of a form](#the-parts-of-a-form)
2. [CSRF protection with ezformtoken](#csrf-protection-with-ezformtoken)
3. [Reading input](#reading-input)
4. [POST, redirect, GET](#post-redirect-get)
5. [Uploads](#uploads)
6. [Forms for visitors](#forms-for-visitors)
7. [Compared with other frameworks](#compared-with-other-frameworks)

## The parts of a form

| Part | Where |
|---|---|
| The HTML form | your template, posting to your view: `<form method="post" action={'expdemo/edit/42'\|ezurl}>` |
| Buttons as actions | `single_post_actions` in `module.php`: `array( 'StoreButton' => 'Store', 'CancelButton' => 'Cancel' )` |
| Values per action | `post_action_parameters`: `array( 'Store' => array( 'Title' => 'ItemTitle' ) )` |
| Reading | `$Module->isCurrentAction( 'Store' )`, `$Module->actionParameter( 'Title' )`, or `eZHTTPTool` |
| Validation and errors | your code; errors passed back to the template ([Validation](validation.md)) |

A worked form is in [Task: build a small CRUD module](your-first-module-step-by-step.md).

## CSRF protection with ezformtoken

`ezformtoken` registers two listeners ([Middleware](middleware-and-request-rules.md#input-and-output-listeners)):

- `response/output`: adds a hidden token field to every `method="post"` form in the page for a **signed-in user with a
  session**. Your templates need nothing.
- `request/input`: refuses a POST of such a user without the right token, taken from the form field or from the
  `X-CSRF-Token` header. The answer is a 403 "This form has expired" page (or a JSON body for XHR and JSON requests)
  and one line in `var/log/warning.log`, never the token itself ([Form expired page](../../features/6.0/form-expired-page.md)).

Set your own secret per installation in `settings/override/site.ini.append.php`:

```ini
[HTMLForms]
Secret=<a long random value of your own>
```

JavaScript that posts with `fetch()` sends the token in the `X-CSRF-Token` header; read it from the page rather than
switching the check off ([Forms, CSRF protection and HTTP input](../creators/app-creators/forms-csrf-and-http-input.md#form-tokens)).

Anonymous visitors have no session to bind a token to, so their POSTs are not checked by `ezformtoken`. Public forms
are protected with other means: a CAPTCHA from the `recaptcha` or `hcaptcha` extension and server-side validation
([Spam protection for forms](../creators/website-creators/spam-protection-for-forms.md)).

## Reading input

```php
$http = eZHTTPTool::instance();
if ( $http->hasPostVariable( 'Title' ) )
    $title = trim( (string)$http->postVariable( 'Title' ) );
$page = $http->hasGetVariable( 'page' ) ? max( 1, (int)$http->getVariable( 'page' ) ) : 1;
```

Never read `$_POST` or `$_GET` directly: under Velocity the kernel prepares them per request.

## POST, redirect, GET

```php
if ( $Module->isCurrentAction( 'Store' ) && $errors === array() )
{
    $item->store();
    return $Module->redirectTo( '/expdemo/item/' . (int)$item->attribute( 'id' ) );
}
```

Redirect targets taken from the request must stay on the site ([Safe redirects](../../features/6.0/safe-redirects.md)).

## Uploads

```php
if ( eZHTTPFile::canFetch( 'File' ) )
{
    $file = eZHTTPFile::fetch( 'File' );
    // check $file->attribute( 'mime_type' ) and size against an allow list before storing
}
```

For files that belong to content, create an object of the `file` or `image` class instead; it handles storage,
clustering and SVG sanitizing ([Creating and updating content in code](../creators/app-creators/creating-content-in-code.md)).

## Forms for visitors

Contact forms, polls and feedback usually need no PHP: a content class with **information collector** attributes
collects the answers, and the administration lists them
([Forms with the information collector](../creators/website-creators/forms-with-the-information-collector.md),
[Building a contact form](../creators/website-creators/building-a-contact-form.md)).

## Compared with other frameworks

| Framework | Concept | Exponential |
|---|---|---|
| Symfony | form types, `handleRequest()`, CSRF option | template plus module actions; `ezformtoken` |
| Laravel | form requests, `@csrf` | your validation in the view; token added automatically for signed-in users |
| Django | `Form` classes, `{% csrf_token %}` | the same split; information collector for simple public forms |
| Rails | `form_with`, strong parameters | action parameters declared in `module.php` |

## Related pages

- [Forms, CSRF protection and HTTP input](../creators/app-creators/forms-csrf-and-http-input.md)
- [Forms in templates](../designers/templates/forms-in-templates.md)
- [Responses, redirects, errors and JSON](responses-redirects-and-errors.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
