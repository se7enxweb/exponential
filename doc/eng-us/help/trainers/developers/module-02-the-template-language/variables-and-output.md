---
title: "Lesson 2.1: Variables and output"
audience: trainer, developer
category: training
order: 21
summary: Learners set up a template playground, print variables and their attributes, declare, change and free variables with def, set and undef, build arrays and hashes, and escape output with wash. 50 minutes.
keywords: developers course, template language, tpl, def, set, undef, variable, attribute, array, hash, wash, comment, template playground
type: help
---
# Lesson 2.1: Variables and output

Pages are made of templates: HTML with expressions in curly braces. This lesson starts the template language with
what every template does first: print a value, declare variables and escape what users typed. Learners build a
small playground that renders a template on the command line, so they can try every expression of this module
without touching a site's design.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 2: The template language](README.md) |
| Duration | 50 minutes (15 presenting, 35 exercise) |
| Level | Beginner |
| Audience | Developers and template builders |
| Prerequisites | [Lesson 1.4](../module-01-the-development-installation/a-script-with-the-kernel-loaded.md): `extension/mysite_hello` exists |

## Learning objectives

After this lesson, learners can:

- render a template file from the command line with `eZTemplate::factory()` and `ezexec.php`;
- print a variable and an attribute of it with `{$node.name}`;
- declare, change and free variables with `{def}`, `{set}` and `{undef}`;
- build lists with `array()` and named lists with `hash()`;
- escape every value a user typed with `|wash`.

## Trainer's notes

### How to present it

Source: [Templates and design, part 6](../../../../guides/templates-and-design.md#6-the-template-language-in-ten-minutes).

1. **What a template is** (3 minutes). A `.tpl` file is HTML with `{ ... }` expressions; `{* ... *}` is a
   comment. The kernel's templates live in `design/standard/templates/`; open `node/view/full.tpl` on the projector.
2. **The playground** (3 minutes). Show the two files of the exercise. `eZTemplate::factory()` gives the
   template engine, `setVariable()` hands it a value, `fetch()` renders a file and returns the text.
3. **Variables and attributes** (4 minutes). `{$node.name}` reads the attribute `name` of the node. The same dot
   reads a key of a hash (`{$person.name}`) or an index (`{$list.0}`); `{$person['role']}` works too.
4. **`def`, `set`, `undef`** (3 minutes). `{def}` declares once per template; a second `{def}` of the same name
   gives a warning. `{set}` changes a declared variable. `{undef}` frees it at the end, a habit that avoids
   surprises in included templates.
5. **`wash`** (2 minutes). `{$value|wash}` turns `<` into `&lt;`. Use it on anything that comes from users: names,
   titles, search words. Rich text attributes are shown with `attribute_view_gui` instead (lesson 2.6).

### Common questions

**Why `def` and not just `set`?** `def` creates the variable in the current template; `set` changes one that
exists. A second `def` of the same name in one template writes the warning "Variable '...' is already defined",
which catches copy-and-paste mistakes.

**Do I need `undef`?** Variables live until the end of the template. Freeing them is good practice in templates
that are included many times, so nothing leaks between calls.

**What about the old `let` and `section`?** Older templates use `{let}` and `{section}`. They still work; new
templates use `def` and `foreach`.

### Pitfalls

- **Whitespace.** A tag at the end of a line eats the line break after it; the playground output looks
  squeezed. That is normal.
- **No `wash` on a title.** A title with `&` or `<` breaks the page or opens a cross-site scripting hole. Make
  `|wash` a reflex.
- **Missing attribute.** `{$node.nonexistent}` prints nothing, often without any message. When a value is
  unexpectedly empty, list the attributes the variable has with `{$node|attribute( 'show' )}` (lesson 2.4).

## Hands-on exercise

On the development installation, from its root folder.

1. Create `extension/mysite_hello/scripts/play.php`:

   ```php
   <?php
   // Template playground: php bin/php/ezexec.php extension/mysite_hello/scripts/play.php
   $tpl = eZTemplate::factory();
   $tpl->setVariable( 'node', eZContentObjectTreeNode::fetch( 2 ) );
   eZCLI::instance()->output( $tpl->fetch( 'extension/mysite_hello/scripts/play.tpl' ) );
   ```

2. Create `extension/mysite_hello/scripts/play.tpl`:

   ```
   {* My first template *}
   {def $title = $node.name
        $count = 3}
   <h1>{$title|wash}</h1>
   {set $count = $count|inc}
   <p>Count: {$count}</p>
   {undef $title $count}
   ```

3. Check both: `php -l extension/mysite_hello/scripts/play.php` and
   `php bin/php/eztemplatecheck.php extension/mysite_hello/scripts/play.tpl`.
4. Run the playground: `php bin/php/ezexec.php extension/mysite_hello/scripts/play.php`.
5. Add an array and a hash at the end of `play.tpl` and print parts of them:

   ```
   {def $colors = array( 'red', 'green', 'blue' )
        $person = hash( 'name', 'Ada', 'role', 'editor' )}
   {$colors.1} {$person.name} {$person['role']}
   {undef $colors $person}
   ```

6. Run it again.
7. Print a value with HTML in it, once without and once with `wash`:
   `{'<b>bold</b>'}` and `{'<b>bold</b>'|wash}`. Run it and compare.
8. Print three attributes of the node: `{$node.node_id}`, `{$node.class_identifier}`, `{$node.children_count}`.
9. Add `{def $count = 1}` twice at the end of the file, and run the playground with the warnings shown:
   `php bin/php/ezexec.php --debug=warning extension/mysite_hello/scripts/play.php`. Read the warning, then remove
   the two lines.
10. Commit both files in the extension's repository.

## Expected result

- Step 4 prints `<h1>` with the name of node 2 and `Count: 4`.
- Step 6 adds `green Ada editor`.
- Step 7: the first prints `<b>bold</b>`, the washed one `&lt;b&gt;bold&lt;/b&gt;`.
- Step 8 prints the ID 2, the class identifier (for example `folder`) and a number.
- Step 9: the debug section at the end names `play.tpl`, line and column, with "Variable 'count' is already
  defined."
- The trainer checks each learner's playground output on the screen.

## Quiz

1. How do you write a comment in a template?
2. Which function changes the value of a variable that is already declared?
   A. `def` B. `set` C. `undef` D. `let`
3. What does `{$person.name}` read when `$person` is a hash?
4. What does `|wash` do with `<`?
5. True or false: a rich text attribute should be printed with `{$attribute.content|wash}`.

<details>
<summary>Answers</summary>

1. `{* ... *}`.
2. **B.** `set`; `def` declares, `undef` frees.
3. The value under the key `name`; the dot reads attributes of objects and keys of hashes alike.
4. It turns it into `&lt;`, so the browser shows the character instead of reading a tag.
5. **False.** Use `attribute_view_gui`, which renders the rich text with its datatype template (lesson 2.6).

</details>

## Further reading

- [Templates and design](../../../../guides/templates-and-design.md): how templates are found, overrides, debugging.
- [String template operators](../../../../features/6.0/string-template-operators.md): the string operators of
  Exponential 6.

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
