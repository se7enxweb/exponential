---
title: "Lesson 2.4: Operators, wash and translations"
audience: trainer, developer
category: training
order: 24
summary: Learners chain string, array and arithmetic operators, choose the right wash type, format dates with l10n and datetime, translate interface texts with i18n, and inspect any variable with attribute show. 50 minutes.
keywords: developers course, template operators, wash, email, javascript, shorten, upcase, concat, implode, explode, append, merge, count, inc, sum, l10n, datetime, i18n, attribute show, ezini
type: help
---
# Lesson 2.4: Operators, wash and translations

Operators turn values into other values: shorten a title, join a list, format a date, translate a button label.
In this lesson learners chain operators with pipes, meet the families of operators the kernel ships, choose the
right `wash` for each place a value is printed, and inspect an unknown variable.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 2: The template language](README.md) |
| Duration | 50 minutes (20 presenting, 30 exercise) |
| Level | Beginner |
| Audience | Developers and template builders |
| Prerequisites | [Lesson 2.3](loops.md) |

## Learning objectives

After this lesson, learners can:

- chain operators with pipes, such as `{$text|shorten( 40 )|wash}`;
- use string, array and arithmetic operators from the kernel's lists;
- choose `wash`, `wash( 'email' )` or `wash( 'javascript' )` for the place a value is printed;
- format dates with `l10n` and `datetime`, and translate interface texts with `i18n`;
- inspect a variable with `attribute( 'show' )` and read a setting with `ezini`.

## Trainer's notes

### How to present it

Sources: the operator lists in `lib/eztemplate/classes/eztemplateautoload.php` and
`kernel/common/eztemplateautoload.php`, and [String template operators](../../../../features/6.0/string-template-operators.md).

1. **Families** (5 minutes). Show the registrations in `lib/eztemplate/classes/eztemplateautoload.php` on the
   projector. Families worth knowing:

   | Family | Examples |
   |---|---|
   | Strings | `upcase`, `downcase`, `upfirst`, `shorten`, `trim`, `wash`, `concat`, `pad` |
   | Arrays | `array`, `hash`, `append`, `merge`, `contains`, `implode`, `explode`, `reverse`, `unique` |
   | Arithmetic | `sum`, `sub`, `inc`, `dec`, `mul`, `div`, `mod`, `round`, `count` |
   | Logic and types | `eq`, `gt`, `and`, `is_set`, `is_array`, `cond`, `first_set` |
   | Dates and locale | `l10n`, `datetime`, `currentdate` |
   | Kernel | `ezurl`, `ezdesign`, `ezimage`, `ezini`, `i18n`, `fetch` |

2. **Chaining** (3 minutes). `{concat( 'a', 'b' )|upcase}` prints `AB`: each pipe hands its result to the next.
   Wash last: `{$title|shorten( 40 )|wash}`.
3. **Wash types** (4 minutes). The default (`xhtml`) escapes for HTML. `wash( 'email' )` additionally hides the
   `@` and `.` of an address from harvesters, as set in `site.ini [WashSettings]`. `wash( 'javascript' )` escapes a
   value for a JavaScript string literal.
4. **Dates** (3 minutes). `{$node.object.published|l10n( 'shortdate' )}` uses the locale's format;
   `{$node.object.published|datetime( 'custom', '%Y-%m-%d' )}` a format of your own.
5. **Translations** (3 minutes). `{'Search'|i18n( 'design/standard/content/search' )}`: the text and its context.
   With no translation the English text is shown.
6. **Inspecting** (2 minutes). `{$node|attribute( 'show', 1 )}` prints a table of the attributes, their types and
   values, one level deep.

### Common questions

**Is there a list of every operator?** The registrations named above are the complete list for the kernel; each
extension adds its own in `autoloads/eztemplateautoload.php`. Lesson 7.5 writes one.

**Can I call any PHP function?** No. Only functions listed in `settings/template.ini [PHP] PHPOperatorList[]` are
available as operators, for example `rawurlencode`.

**Where do translations come from?** From `share/translations/<locale>/translation.ts` and the `translations/`
folders of extensions. Add a context of your own for your extension's texts.

### Pitfalls

- **Washing twice.** `{$text|wash|wash}` shows `&amp;lt;`. Wash once, at the end.
- **`attribute( 'show' )` on a live page.** It prints internal values; use it in the playground or with debug
  output on a development installation only.
- **`ezini` and secrets.** A template can print any setting it reads. Never print database or mail settings.

## Hands-on exercise

Use the playground from lesson 2.1 and run it after each step.

1. Chain operators: `{concat( 'exponential', ' ', 'course' )|upcase}`.
2. Shorten a long text: `{'A long sentence that needs shortening'|shorten( 15 )}`.
3. Build and join a list:

   ```
   {def $list = array( 3, 1 )|append( 7 )}
   {$list|implode( '-' )} has {$list|count} items, sum {$list|array_sum}
   {undef $list}
   ```

4. Split a string: `{'a,b,c'|explode( ',' )|count}`.
5. Compare the three wash types:

   ```
   [{'<b>x</b>'|wash}]
   [{'ann@example.invalid'|wash( 'email' )}]
   [{"It's \"quoted\""|wash( 'javascript' )}]
   ```

6. Format the publishing date of node 2 in two ways:

   ```
   {$node.object.published|l10n( 'shortdate' )}
   {$node.object.published|datetime( 'custom', '%Y-%m-%d' )}
   ```

7. Translate an interface text: `{'Search'|i18n( 'design/standard/content/search' )}`.
8. Read a setting: `{ezini( 'SiteSettings', 'SiteName' )|wash}`.
9. Inspect the node: `{$node|attribute( 'show', 1 )}`. Find `node_id`, `depth` and `url_alias` in the table.
10. Commit the template.

## Expected result

- Step 1 prints `EXPONENTIAL COURSE`; step 2 `A long sente...`.
- Step 3 prints `3-1-7 has 3 items, sum 11`; step 4 prints `3`.
- Step 5: `&lt;b&gt;x&lt;/b&gt;`, the address with the `@` replaced by the text set in `[WashSettings]`, and the
  quotes escaped as `\047` and `\042`.
- Step 6 prints the date in the locale's short format and as `YYYY-MM-DD`.
- Step 9 prints an HTML table of the node's attributes.
- The trainer checks step 5 with the learners and asks where each wash type belongs.

## Quiz

1. In `{$title|shorten( 40 )|wash}`, why does `wash` come last?
2. Which wash type do you use for a value inside a JavaScript string?
   A. `xhtml` B. `email` C. `javascript` D. `pdf`
3. Which two values does `i18n` need?
4. Where is the list of PHP functions a template may call as operators?
5. How do you see every attribute of an unknown variable?

<details>
<summary>Answers</summary>

1. So the final text is escaped; operators after `wash` could add unescaped characters or cut an entity in half.
2. **C.** `wash( 'javascript' )`; the default `xhtml` is for HTML.
3. The text and its translation context, for example `'design/standard/content/search'`.
4. `settings/template.ini`, `[PHP] PHPOperatorList[]`
   ([Templates and design, part 10](../../../../guides/templates-and-design.md#10-encoding-a-value-for-a-url-rawurlencode-and-rawurldecode)).
5. `{$var|attribute( 'show', 1 )}`.

</details>

## Further reading

- [String template operators](../../../../features/6.0/string-template-operators.md): the string operators added in
  Exponential 6.
- [Role and policy template operators](../../../../features/6.0/role-and-policy-template-operators.md): `has_role`,
  `has_policy` and friends.
- [Templates and design](../../../../guides/templates-and-design.md): site data and URL encoding in templates.

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
