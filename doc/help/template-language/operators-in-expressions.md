---
title: Operators in expressions
audience: developer, designer, app-creator
category: development
order: 110
summary: How operators are written and evaluated - the pipe form and the call form, what the input value is in each, chaining, nesting operators as parameters, attributes after an operator, operators inside function parameters, how parameters are matched to named parameters with defaults, and what happens with an unknown operator or a missing parameter.
keywords: operators, pipe, call form, input value, chaining, nesting, parameters, named parameters, namedParameterList, default, missing parameter, unknown operator, processOperator, modify
type: help
---
# Operators in expressions

An operator is a named transformation of a value. In the parse tree it is an element of type `TYPE_OPERATOR` that holds
the operator name and its parameter expressions (`parseVariableTag()` in
`lib/eztemplate/classes/eztemplateelementparser.php`); at run time `eZTemplate::processOperator()` in
`lib/eztemplate/classes/eztemplate.php` matches the parameters and calls the operator class's `modify()`. This page
shows what that means for writing expressions.

## Contents

1. [Pipe form and call form](#pipe-form-and-call-form)
2. [Chaining](#chaining)
3. [Nesting](#nesting)
4. [Attributes after an operator](#attributes-after-an-operator)
5. [Operators inside function parameters](#operators-inside-function-parameters)
6. [How parameters are matched](#how-parameters-are-matched)
7. [Unknown operators and missing parameters](#unknown-operators-and-missing-parameters)
8. [Related pages](#related-pages)

## Pipe form and call form

The same operator can be written two ways:

- **Pipe form**, `{$value|name( params )}`: the value before the pipe is the operator's **input**.
- **Call form**, `{name( params )}`: there is no input; the input is null.

Operators differ in what they do with the input. `upcase` works only on its input, so the call form gives nothing.
`sum` adds the input to its parameters when there is one. `concat` puts the input in front of its parameters:

```tpl
[{'abc'|upcase}] [{upcase( 'abc' )}]
[{2|sum( 3 )}] [{sum( 2, 3 )}]
[{'a'|concat( 'b', 'c' )}] [{concat( 'a', 'b', 'c' )}]
```

Output on alpha:

```text
[ABC] []
[5] [5]
[abc] [abc]
```

An input that an operator cannot use is not an error. `sum` treats a non-numeric input as nothing to add:

```tpl
[{'ignored'|sum( 1, 2 )}] [{5|sum( 1, 2 )}]
```

Output on alpha:

```text
[3] [8]
```

Which form an operator accepts is part of its documentation. The generated page of each operator says whether it works
on its input ([every template operator](../../examples/template-operators/README.md)).

## Chaining

Each `|` hands the result of the operator before it to the next one, left to right:

```tpl
[{'  Hello Template World  '|trim|downcase|shorten( 12 )|upfirst}]
[{array( 3, 1, 2 )|count|mul( 10 )|sum( 5 )}]
```

Output on alpha:

```text
[Hello tem...]
[35]
```

## Nesting

Parameters are full expressions: variables, literals, other operators in either form, with their own attributes.
Conditions are built this way, because there are no infix operators:

```tpl
{def $a = 4 $b = 2}
[{concat( 'sum=', sum( $a, $b ), ', product=', mul( $a, $b ) )}]
[{if and( $a|gt( 1 ), $b|lt( 5 ), or( $a|eq( 4 ), $b|eq( 0 ) ) )}all true{/if}]
[{cond( $a|gt( $b ), 'a wins', 'b wins' )}]
```

Output on alpha:

```text
[sum=6, product=8]
[all true]
[a wins]
```

## Attributes after an operator

A dot or brackets can follow an operator call. The attribute is read from the operator's result:

```tpl
[{fetch( 'content', 'node', hash( 'node_id', 89 ) ).name}]
[{fetch( 'content', 'node', hash( 'node_id', 89 ) ).children|count}]
[{array( 'x', 'y', 'z' )|reverse.0}]
```

Output on alpha:

```text
[Fit & Healthy]
[16]
[z]
```

## Operators inside function parameters

Function parameters are expressions too, so an operator can supply the array of a `foreach` or the value of a `def`:

```tpl
{foreach array( 'b', 'a', 'c' )|reverse as $letter}{$letter|upcase}{/foreach}
{def $label = concat( 'Item ', 3|inc )}[{$label}]
```

Output on alpha:

```text
CAB[Item 4]
```

The reverse does not hold: a function is not a value and cannot be called inside an expression. `if( ... )` in an
expression is looked up as an operator named `if`, which does not exist:

```tpl
[{concat( 'a', if( true() ) )}]
```

Output on alpha:

```text
[a]
```

Logged by the template engine:

```text
WARNING : Operator 'if' is not registered
```

## How parameters are matched

Parameters are positional. An operator class may declare names for them in `namedParameterList()` (per operator when
`namedParameterPerOperator()` returns true). `processOperator()` walks that list in order:

1. If the template gives a value at that position, it is evaluated and stored under the parameter's name.
2. If not, and the parameter is `required`, the warning "Parameter '<name>' (<position>) missing" is written.
3. A missing parameter gets its `default` from the list. When the list has no default either, it gets null and the
   engine warns once per operator and parameter that there is no default value.

The operator's `modify()` then receives the input value (by reference; what it leaves there is the result), the raw
parameter expressions and the named values. Operators that take any number of parameters, like `sum` or `concat`,
declare none and evaluate the raw expressions themselves. How to write such a class is in
[Writing template operators](../creators/app-creators/template-operators.md).

## Unknown operators and missing parameters

An operator name that is not registered writes "Operator '<name>' is not registered" and leaves the input unchanged.
The page renders on, which hides typing mistakes:

```tpl
[{'kept as it is'|nosuchoperator( 1 )}]
```

Output on alpha:

```text
[kept as it is]
```

Logged by the template engine:

```text
WARNING : Operator 'nosuchoperator' is not registered
```

A missing required parameter is reported, and then the operator runs with what it has. `datetime` needs the format
class as its first parameter; without it the timestamp is printed unformatted and two messages are logged:

```tpl
[{1674667522|datetime}]
```

Output on alpha:

```text
[1674667522]
```

Logged by the template engine:

```text
ERROR datetime: DateTime class '' is not defined
WARNING eZTemplateOperatorElement: Parameter 'class' (0) missing
```

## Related pages

- Previous: [Attribute access](attribute-access.md)
- Next: [Functions versus operators](functions-vs-operators.md)
- [Operators overview](../designers/templates/operators-overview.md)
- [Operator reference](../designers/templates/operator-reference.md)
- [Every template operator](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
