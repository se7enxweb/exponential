---
title: "Lesson 2.2: Conditions and logic"
audience: trainer, developer
category: training
order: 22
summary: Learners write if, elseif and else blocks, compare values with eq, ne, gt, lt, ge and le, combine tests with and, or and not, and use cond, first_set, is_set and switch. 45 minutes.
keywords: developers course, template language, if, elseif, else, eq, ne, gt, lt, and, or, not, cond, first_set, is_set, is_unset, switch, case, contains
type: help
---
# Lesson 2.2: Conditions and logic

Templates decide: show the image if there is one, a different heading for the front page, a message when a list
is empty. In this lesson learners write conditions, meet the comparison and logic operators, and learn the two
ways of writing an operator: as a pipe after a value, or as a function.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 2: The template language](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Beginner |
| Audience | Developers and template builders |
| Prerequisites | [Lesson 2.1](variables-and-output.md): the template playground |

## Learning objectives

After this lesson, learners can:

- write `{if}`, `{elseif}` and `{else}` blocks;
- compare values with `eq`, `ne`, `gt`, `lt`, `ge` and `le` in pipe and function form;
- combine tests with `and()`, `or()` and `not()`;
- choose a value inline with `cond()` and `first_set()`, and test for a variable with `is_set()`;
- branch on one value with `{switch}` and `{case}`.

## Trainer's notes

### How to present it

Source: [Templates and design, part 6](../../../../guides/templates-and-design.md#6-the-template-language-in-ten-minutes)
and the operator lists in `lib/eztemplate/classes/eztemplateautoload.php`.

1. **Two forms of one operator** (3 minutes). `{$node.depth|gt( 2 )}` and `{gt( $node.depth, 2 )}` are the same
   test. The pipe passes the value on its left as the first input.
2. **Comparisons** (3 minutes). `eq`, `ne`, `lt`, `gt`, `le`, `ge`. `eq` takes several values:
   `{$a|eq( 'x', 'y' )}` is true only when all are equal.
3. **Logic** (3 minutes). `and( ... )`, `or( ... )`, `not( ... )`, and the constants `true()` and `false()`.
4. **Truthiness** (2 minutes). A non-empty string is true; an empty array is false. `{if $node.name}` works,
   `{if array()}` goes to `{else}`.
5. **Inline and fallback** (2 minutes). `cond( $test, 'yes', 'no' )` is the inline condition; `first_set( $a, $b,
   'default' )` returns the first value that exists.
6. **`switch`** (2 minutes). `{switch match=$value}{case match=3}...{/case}{case}...{/case}{/switch}`: the `case`
   without `match` is the default.

### Common questions

**How do I test whether an array holds a value?** `{if array( 'folder', 'article' )|contains( $node.class_identifier )}`.
`contains` also works on strings: `{'folder'|contains( 'old' )}` is true.

**Is there a `!=`?** No symbols: use `ne` or `not( eq( ... ) )`.

**What does `is_set` do for an attribute that does not exist?** `is_set( $node.missing )` is false. Use it when a
template may be included with or without a parameter.

### Pitfalls

- **`eq` with several values.** `{$x|eq( 'a', 'b' )}` does not mean "a or b". Use `contains` on an array for
  that.
- **Wrong closing tag.** Every `{if}` needs `{/if}`. A wrong closing tag is reported by `eztemplatecheck.php`
  with the template and line ("Unterminated tag"); a missing `{/if}` at the very end of a file may pass the check,
  so count opening and closing tags in long templates.
- **Strings and numbers.** `$node.depth|eq( '1' )` and `|eq( 1 )` compare as PHP does; prefer numbers for numbers.

## Hands-on exercise

Use the playground from lesson 2.1. Replace the content of `extension/mysite_hello/scripts/play.tpl` at each step
or add below it, and run `php bin/php/ezexec.php extension/mysite_hello/scripts/play.php` after each step.

1. Write a condition on the depth of the node:

   ```
   {if $node.depth|eq( 1 )}top level{elseif $node.depth|lt( 4 )}near the top{else}deep{/if}
   ```

2. Write the same test in function form: `{if eq( $node.depth, 1 )}top level{/if}`.
3. Combine two tests:

   ```
   {if and( $node.depth|eq( 1 ), $node.children_count|gt( 0 ) )}top level with children{/if}
   ```

4. Test the class with an array:

   ```
   {if array( 'folder', 'article' )|contains( $node.class_identifier )}a folder or an article{/if}
   ```

5. Use `cond()` to print `visible` or `hidden`: `{cond( $node.is_hidden, 'hidden', 'visible' )}`.
6. Use `first_set()` for a variable that was never declared: `{first_set( $subtitle, 'No subtitle' )}`.
7. Write a `switch` on the number of children:

   ```
   {switch match=$node.children_count}
   {case match=0}no children{/case}
   {case}some children{/case}
   {/switch}
   ```

8. Change the `{/if}` of step 1 into `{/foreach}` and run `php bin/php/eztemplatecheck.php extension/mysite_hello/scripts/play.tpl`.
   Read the message, then put `{/if}` back.
9. Commit the template.

## Expected result

- Each step prints one short phrase; when node 2 is a visible folder at the top of the tree, step 1 prints `top level`, step 4
  `a folder or an article`, step 5 `visible`, step 6 `No subtitle`.
- Step 8: the checker prints `parser error` with the file, line and column, `Unterminated tag "if" does not match
  tag "foreach"`, and `Template file invalid` instead of `Template file valid`.
- The trainer checks by asking two learners to explain their output of step 3.

## Quiz

1. Write the test "depth greater than 2" in pipe form.
2. What does `{cond( false(), 'a', 'b' )}` print?
   A. `a` B. `b` C. nothing D. `false`
3. How do you test whether `$node.class_identifier` is `folder` or `article`?
4. What does `first_set( $x, 'fallback' )` return when `$x` was never declared?
5. True or false: `{if array()}` goes to the `{else}` branch.

<details>
<summary>Answers</summary>

1. `{$node.depth|gt( 2 )}`.
2. **B.** `cond` returns the value after the first true condition, here the last value.
3. `{array( 'folder', 'article' )|contains( $node.class_identifier )}`; `eq` with two values would need all three
   to be equal.
4. `'fallback'`.
5. **True.** An empty array is false.

</details>

## Further reading

- [Templates and design](../../../../guides/templates-and-design.md): the language in ten minutes and debugging.
- [Role and policy template operators](../../../../features/6.0/role-and-policy-template-operators.md): conditions on
  what the current user may do.

## Related pages

- [Module 2: The template language](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
