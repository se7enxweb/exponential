---
title: "OW Simple Operator: limitations and risks"
audience: developer, app-creator
category: development
order: 920
summary: What OW Simple Operator does not do and where it bites - the pipe form that drops an argument, null arguments that are left out, no parameter checks, no compile-time optimization, name clashes, caching and the security rule for permitted PHP functions - with the failures rendered on alpha.
keywords: owsimpleoperator, limitations, risks, ArgumentCountError, pipe form, null, PermittedFunctionList, security, cache-block, operatorTemplateHints, name clash
type: help
---
# OW Simple Operator: limitations and risks

OW Simple Operator saves code by giving every operator the same generic definition. That generic definition is
also where its limits come from. Everything below is read from `extension/owsimpleoperator/autoloads/owsimpleoperator.php`
and `autoloads/eztemplateautoload.php` and, where an example is shown, rendered on alpha.

## Contents

1. [The pipe form drops an argument](#the-pipe-form-drops-an-argument)
2. [Null arguments are left out](#null-arguments-are-left-out)
3. [No parameter checks](#no-parameter-checks)
4. [No compile-time code](#no-compile-time-code)
5. [Name clashes](#name-clashes)
6. [Cached output](#cached-output)
7. [Security of permitted PHP functions](#security-of-permitted-php-functions)
8. [When to write a full operator class instead](#when-to-write-a-full-operator-class-instead)

## The pipe form drops an argument

`OWSimpleOperator::modify()` handles a piped value like this:

```php
// If there are an operator value, we switch it with our parameter
if ( $operator_value !== null )
{
    array_pop( $method_arguments );
    array_unshift( $method_arguments, $operator_value );
}
```

For a **class method** the argument list always has ten entries (one per declared parameter), so the entry that
is removed is the tenth, unused one, and `{1|example_sum( 2 )}` works as `example_sum( 1, 2 )`
([example](owsimpleoperator-your-own-operators.md#using-them)).

For a **PHP function** the list only holds the arguments you gave (see the next section), so the removed entry
is your last argument. `{'This is a great example!'|str_replace( 'great', 'very good' )}` calls
`str_replace( 'This is a great example!', 'great' )`, and PHP throws an `ArgumentCountError`; see
[the example on the first page](owsimpleoperator.md#calling-a-php-function-operator). With a function that has
optional parameters there is no error but a wrong result, which is worse. Rule: call PHP function operators in
the call form with every argument in the parentheses, and pipe only into functions with exactly one argument
(`{$text|str_rot13}`).

A piped value of `null` counts as no piped value at all (the test is `$operator_value !== null`), so
`{$missing|str_rot13}` with an unset value calls `str_rot13()` without arguments.

## Null arguments are left out

For PHP functions, `modify()` copies only the arguments that are set:

```php
foreach( $named_parameters as $index => $parameter )
{
   if( isset( $parameter ) )
   {
       $method_arguments[$index] = $parameter;
   }
}
```

and then calls `call_user_func_array()` with that list. An argument whose value is `null` is not passed, and
every argument after it moves one place forward into the wrong parameter of the PHP function. Make sure the
values you pass are set (`first_set()` gives a fallback, see
[Control operators](control-operators.md)).

For class methods the opposite happens: all ten values are passed, so a parameter you leave out arrives as `null`
and never gets your PHP default ([Optional arguments](owsimpleoperator-your-own-operators.md#optional-arguments)).

## No parameter checks

`namedParameterList()` declares ten parameters of type `mixed`, none required, default `null`, for every operator.
The template engine therefore never reports a missing or wrong argument for these operators; PHP does, at run
time, and the template engine does not catch PHP's errors:

```tpl
{str_rot13()}
```

Output on alpha:

```text
(no output)
```

Logged by the template engine:

```text
EXCEPTION ArgumentCountError: str_rot13() expects exactly 1 argument, 0 given
```

Operators of the template library check their parameters themselves and log a template warning instead, for
example `Missing required parameter: start` from `substr` in `eZTemplateStringsOperator`
([PHP string functions](php-string-functions-transform.md)).

If a name is listed in `owsimpleoperator.ini` but is neither a method of the class nor a PHP function, `modify()`
writes `The method "<name>" doesn't exists.` to the debug output and returns `null`.

## No compile-time code

`OWSimpleOperator` has no `operatorTemplateHints()`. The template compiler (`eZTemplateCompiler::operatorHint()`)
then has no hints for these operators and cannot turn them into inline PHP, so compiled templates call the
operator object at run time, every time. The operators of the template library declare hints and many of them are
compiled into plain PHP (see [The operator class interface](operator-class-reference.md) and
[The template compiler](../template-language/template-compiler.md)). For an operator called a few times per
page this does not matter; inside a loop over hundreds of items it can.

## Name clashes

Every PHP function you permit and every method you register becomes a global operator name. On alpha,
`str_replace` is registered by both extension swark and owsimpleoperator; owsimpleoperator's
`eztemplateautoload.php` is read later and wins. A permitted name that matches an operator of a class with several
operators (for example `strlen` of `eZTemplateStringsOperator`) is worse: the template library's class registers
all its names again when it loads, and takes the name back in the middle of a request
([Registration order and overrides](registration-order-and-overrides.md#a-loaded-class-takes-its-names-back)).
Use names that no other operator has.

## Cached output

Operator results end up in the output like any other text, so they are stored in the view cache and in
cache blocks. `{time()}` inside a cached full view shows the time the cache was written, not the time of the
request. The extension's README says the same: use `cache-block` with the right keys or expiry, or keep such
calls out of cached templates ([Template caches](../template-language/template-caches.md)).

## Security of permitted PHP functions

A template can call a permitted function with any argument. Templates are written by designers, edited through
the admin's template editor and shipped by extensions, so treat `PermittedFunctionList[]` like code:

- list only functions without side effects that read nothing outside their arguments (the rule in the shipped
  settings file);
- never file, process, network, configuration or code-evaluation functions (`file_get_contents`, `exec`,
  `getenv`, `ini_get`, `call_user_func`, ... the extension's test lists the ones it refuses);
- escape what an operator returns when it contains user content (`|wash`), because nothing escapes it for you.

## When to write a full operator class instead

Write an ordinary operator class ([Creating an operator, step by step](creating-an-operator-step-by-step.md)) when
you need any of these:

- required parameters with clear template warnings, or typed parameters;
- an operator that works on its piped input and also takes several parameters;
- compile-time optimization (`operatorTemplateHints()`);
- registration in your own extension without touching `owsimpleoperator.ini`;
- no dependency on a third extension being active.

OW Simple Operator stays a good fit for small helpers with one or two arguments that you call in the call form.

## Related pages

- [OW Simple Operator: PHP functions as template operators](owsimpleoperator.md)
- [Your own operators with OW Simple Operator](owsimpleoperator-your-own-operators.md)
- [Registration order and overrides](registration-order-and-overrides.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
