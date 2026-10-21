---
title: "Validation: input, datatypes and escaping"
audience: web-framework-developer, developer
category: web-frameworks
order: 270
summary: Where validation happens in Exponential when there is no validator component - content is validated by its datatypes when editors save, your own forms and parameters are validated in your view or service, and output is escaped where it is printed - with the patterns, the eZInputValidator states and a comparison with validation libraries.
keywords: validation, validator, constraints, form request, datatype, validateObjectAttributeHTTPInput, eZInputValidator, STATE_ACCEPTED, STATE_INVALID, allow list, casting, escaping, wash, Symfony Validator, Laravel validation, Django forms, web framework developer
type: help
---
# Validation: input, datatypes and escaping

This page is for developers who declare constraints (Symfony Validator attributes, Laravel validation rules, Django
form fields, Rails model validations). Exponential has no general validator component with declarative rules. Validation
happens in three places, and knowing which one applies saves a lot of duplicate code.

## Contents

1. [Three places](#three-places)
2. [Content: the datatypes validate](#content-the-datatypes-validate)
3. [Your input: validate in the view or service](#your-input-validate-in-the-view-or-service)
4. [Showing errors](#showing-errors)
5. [Output: escape where you print](#output-escape-where-you-print)
6. [Compared with validation libraries](#compared-with-validation-libraries)

## Three places

| Data | Validated by | You write |
|---|---|---|
| Content edited in the administration or a front-end edit form | the datatype of each attribute, with the class attribute's settings (required, length, ranges) | nothing, or a custom datatype |
| Parameters and forms of your own views | your view or the service it calls | casts, allow lists, checks, error messages |
| API input | the REST layer for content; your controller or server function for the rest | the same checks as for forms |

## Content: the datatypes validate

When content is saved through the edit views, each attribute's datatype checks the input in
`validateObjectAttributeHTTPInput()` (`kernel/classes/ezdatatype.php`) and answers with an `eZInputValidator` state:
`STATE_ACCEPTED`, `STATE_INTERMEDIATE` or `STATE_INVALID`, with a message the edit form shows next to the field. Whether
a field is required, its length limits and ranges are settings of the class attribute, set in the class editor. A new
kind of rule is a new datatype ([Custom datatypes](../creators/app-creators/custom-datatypes.md),
[The datatype life cycle](../creators/app-creators/datatype-lifecycle.md)).

When code creates content with `eZContentFunctions::createAndPublishObject()`, values are given in the datatype's
`fromString()` form; validate them in your import code before you call it.

## Your input: validate in the view or service

From [Input validation and output escaping](../creators/app-creators/input-validation-and-escaping.md#validating-input):

| Input | Validate |
|---|---|
| IDs and numbers | `(int)`, then a range check (`max( 1, min( 100, (int)$limit ) )`) |
| Choices | an allow list: `in_array( $sort, array( 'name', 'published' ), true )` |
| Text | length limits, trim, the characters allowed for the field |
| E-mail, URL | `filter_var()`; for URLs also the scheme (`https` only) |
| Files | type by content and size, stored through the kernel |
| Redirect targets | only addresses of the site ([Safe redirects](../../features/6.0/safe-redirects.md)) |

Address parameters (`$Params`) are strings from the URL, as untrusted as POST data. Put the rules in a small validator
class your view, command and API share, and test it with PHPUnit without a database ([Testing](testing.md)).

```php
$errors = array();
$title = trim( (string)$Module->actionParameter( 'Title' ) );
if ( $title === '' )
    $errors['title'] = ezpI18n::tr( 'extension/expdemo', 'Enter a title.' );
elseif ( mb_strlen( $title ) > 200 )
    $errors['title'] = ezpI18n::tr( 'extension/expdemo', 'The title is too long.' );
```

## Showing errors

Re-render the form with the values the user typed, show each error next to its field and a summary at the top, and
link the summary to the fields so keyboard and screen reader users find them
([Forms, CSRF protection and HTTP input](../creators/app-creators/forms-csrf-and-http-input.md#showing-validation-errors)):

```php
$tpl->setVariable( 'errors', $errors );
$tpl->setVariable( 'values', array( 'title' => $title ) );
```

## Output: escape where you print

Templates do not escape automatically: every value from users or the database goes through `|wash`, except values that
are already safe HTML by design (`$module_result.content`, `attribute_view_gui` output, rendered rich text)
([The view layer](templates-vs-twig-and-blade.md#five-differences-that-bite)). In PHP, use
`htmlspecialchars( $v, ENT_QUOTES, 'UTF-8' )`, `json_encode()`, `$db->escapeString()` for their contexts.

## Compared with validation libraries

| Library feature | Exponential |
|---|---|
| Declarative constraints on a model | class attribute settings for content; nothing for your own tables |
| Validation groups, cascading | no |
| Translated messages | `ezpI18n::tr()` in your code; datatypes ship translated messages |
| Form error rendering | your template |

A validation library from Composer can be used inside your extension if you prefer declarative rules; the kernel does
not depend on one.

## Related pages

- [Forms and CSRF protection](forms-and-csrf-protection.md)
- [Datatype input hardening](../../specifications/6.0/datatype-input-hardening.md)
- [Security for applications](../creators/app-creators/security-for-applications.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
