---
title: "Lesson 2.3: Loops with foreach"
audience: trainer, developer
category: training
order: 23
summary: Learners loop over arrays, hashes and the children of a node with foreach, use keys, delimiters, sequences for alternating classes, offset, max and reverse, and leave a loop early with break, continue and skip. 45 minutes.
keywords: developers course, template language, foreach, loop, delimiter, sequence, offset, max, reverse, break, continue, skip, children, array operators, count
type: help
---
# Lesson 2.3: Loops with foreach

Most pages show lists: the articles of a folder, the items of a menu, the images of a gallery. `{foreach}` is the
loop of the template language. In this lesson learners loop over arrays, hashes and the children of a node, and
use the parts of `foreach` that save extra code: delimiters, sequences, offset and max.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 2: The template language](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Beginner |
| Audience | Developers and template builders |
| Prerequisites | [Lesson 2.2](conditions-and-logic.md) |

## Learning objectives

After this lesson, learners can:

- loop over an array or a hash with `{foreach $list as $item}` and `{foreach $hash as $key => $value}`;
- put separators between items with `{delimiter}`;
- alternate CSS classes with `sequence ... as $class`;
- limit and reorder a loop with `offset`, `max` and `reverse`;
- leave or skip with `{break}`, `{continue}` and `{skip}`, and count with `|count`.

## Trainer's notes

### How to present it

Source: the syntax comment in `lib/eztemplate/classes/eztemplateforeachfunction.php`:

```
{foreach <array> as [$keyVar =>] $itemVar
         [sequence <array> as $sequenceVar]
         [offset <offset>]
         [max <max>]
         [reverse]}
    [{delimiter}...{/delimiter}]
    [{break}] [{continue}] [{skip}]
{/foreach}
```

1. **The plain loop** (3 minutes). `{foreach $list as $item}{$item}{/foreach}`. Show it over an array and over
   `$node.children`, an attribute of the node that returns its child nodes.
2. **Keys** (2 minutes). `{foreach $hash as $key => $value}` gives the key too; for an array the key is the index,
   counted from 0.
3. **Delimiter** (2 minutes). `{delimiter}, {/delimiter}` is printed between items, never after the last.
4. **Sequence** (3 minutes). `sequence array( 'odd', 'even' ) as $class` cycles through the values, for zebra rows.
5. **Offset, max, reverse** (3 minutes). Skip the first items, stop after a number, go backwards.
6. **Leaving early** (2 minutes). `{break}` ends the loop, `{continue}` and `{skip}` go to the next item.

### Common questions

**Should I loop over `$node.children` on a large folder?** No. It loads every child. For real lists use
`fetch( 'content', 'list', ... )` with a `limit` (module 5); `$node.children` is fine for a few items.

**Is there a counter?** Use the key of an array loop, or `{set $i = $i|inc}` inside the loop with a variable
declared before it.

**What about `{section}`?** It is the older loop, still found in old templates. Write new templates with
`foreach`.

### Pitfalls

- **Washing inside loops.** Every printed name still needs `|wash`.
- **`def` inside a loop.** A `{def}` inside the loop runs on every item and warns from the second time. Declare
  before the loop, `set` inside.
- **Large loops in the playground.** On a big folder, keep `max` small so the output stays readable.

## Hands-on exercise

Use the playground from lesson 2.1 (`extension/mysite_hello/scripts/play.tpl`), running
`php bin/php/ezexec.php extension/mysite_hello/scripts/play.php` after each step.

1. Loop over an array with its keys and a delimiter:

   ```
   {def $colors = array( 'red', 'green', 'blue' )}
   {foreach $colors as $i => $color}{$i}:{$color}{delimiter}, {/delimiter}{/foreach}
   ```

2. Add alternating classes:

   ```
   <ul>
   {foreach $colors as $color sequence array( 'odd', 'even' ) as $class}
       <li class="{$class}">{$color|wash}</li>
   {/foreach}
   </ul>
   ```

3. Show only the second and third item: `{foreach $colors as $c max 2 offset 1}{$c} {/foreach}`.
4. Show them backwards: `{foreach $colors as $c reverse}{$c} {/foreach}`.
5. Count them: `{$colors|count} colors`.
6. Loop over the children of the node, at most five:

   ```
   <ul>
   {foreach $node.children as $child max 5}
       <li>{$child.name|wash} ({$child.class_identifier})</li>
   {/foreach}
   </ul>
   ```

7. Skip hidden children: add `{if $child.is_hidden}{continue}{/if}` as the first line inside the loop.
8. Loop over a hash: `{foreach hash( 'a', 1, 'b', 2 ) as $key => $value}{$key}={$value} {/foreach}`.
9. Free the variables at the end with `{undef $colors}` and commit the template.

## Expected result

- Step 1 prints `0:red, 1:green, 2:blue`.
- Step 2 prints three `<li>` elements with the classes `odd`, `even`, `odd`.
- Step 3 prints `green blue`; step 4 `blue green red`; step 5 `3 colors`.
- Step 6 prints the names and class identifiers of up to five children of node 2.
- Step 8 prints `a=1 b=2`.
- The trainer checks the output of step 6 against the content tree in the administration.

## Quiz

1. How do you print `, ` between items but not after the last one?
2. Which part of `foreach` alternates CSS classes?
   A. `delimiter` B. `sequence` C. `offset` D. `reverse`
3. What does `{foreach $list as $c max 2 offset 1}` print for `array( 'a', 'b', 'c', 'd' )`?
4. Why is `$node.children` a poor choice for a folder with thousands of items?
5. What is the difference between `{break}` and `{continue}`?

<details>
<summary>Answers</summary>

1. With `{delimiter}, {/delimiter}` inside the loop.
2. **B.** `sequence array( 'odd', 'even' ) as $class`.
3. `b` and `c`: skip one, then take two.
4. It loads every child; a `fetch` with a `limit` loads one page (module 5).
5. `{break}` ends the loop; `{continue}` goes on with the next item.

</details>

## Further reading

- [Templates and design, part 6](../../../../guides/templates-and-design.md#6-the-template-language-in-ten-minutes):
  the language in ten minutes, with a complete list template.
- [String template operators](../../../../features/6.0/string-template-operators.md): operators to use on the items.

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
