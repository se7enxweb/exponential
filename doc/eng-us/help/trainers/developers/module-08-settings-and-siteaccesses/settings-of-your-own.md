---
title: "Lesson 8.4: Settings of your own"
audience: trainer, developer
category: training
order: 84
summary: Learners give their extension its own INI file with documented defaults, read it in PHP with eZINI and in templates with ezini, let a siteaccess override it, and handle missing settings safely. 45 minutes.
keywords: developers course, own ini file, mysite.ini, eZINI instance, variable, hasVariable, hasGroup, variableArray, ezini operator, ezini_hasvariable, defaults, extension settings, siteaccess override
type: help
---
# Lesson 8.4: Settings of your own

Code that hard-codes a number or a node ID has to be edited for every site. An extension of quality reads such
values from an INI file of its own, with documented defaults that a site can override. In this lesson learners add
`mysite.ini` to their extension, read it from PHP and from templates, and override one value for a siteaccess.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 8: Settings and siteaccesses](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Developers |
| Prerequisites | [Lesson 8.3](changing-settings-with-exp-ini.md) |

## Learning objectives

After this lesson, learners can:

- create an INI file of their own in an extension, with a comment for every setting;
- read it in PHP with `eZINI::instance()`, `variable()`, `hasVariable()` and `variableArray()`;
- read it in templates with `ezini()` and `ezini_hasvariable()`;
- override a value for one siteaccess and check the result with `exp:ini where`;
- fall back to a safe default when a setting is missing.

## Trainer's notes

### How to present it

Sources: [Your own INI file](../../../designers/extensions/your-own-ini-file.md) and
[Settings files in an extension](../../../designers/extensions/settings-files-in-an-extension.md).

1. **The file** (3 minutes). An extension can carry a new INI file under `settings/`: here
   `extension/mysite_hello/settings/mysite.ini.append.php`. Every setting gets a comment saying what it does and its
   allowed values: the file is the documentation.
2. **PHP** (4 minutes). `$ini = eZINI::instance( 'mysite.ini' );` then `$ini->variable( 'GreetSettings',
   'MaxTimes' )`. Check with `hasVariable()` first, or use a default in code. `variableArray()` splits values that
   hold `;`-separated lists.
3. **Templates** (2 minutes). `{ezini( 'GreetSettings', 'MaxTimes', 'mysite.ini' )}`;
   `ezini_hasvariable()` checks first.
4. **Overrides** (3 minutes). A site changes a value with `exp:ini set mysite.ini/GreetSettings/MaxTimes 3
   siteaccess:<sa>`; the extension's file is never edited for one site.
5. **Prefixes** (2 minutes). INI file names are shared by all extensions; name yours after your extension or
   prefix (`mysite.ini`), not `settings.ini`.

### Common questions

**Can I add a block to `site.ini` instead?** You can, but a file of your own keeps your settings together and
cannot collide with a kernel block.

**Do settings files support types?** Values are strings; `enabled`/`disabled` and `true`/`false` are compared as
text by convention. Convert numbers with `(int)`.

**Should secrets go in an extension's settings?** No: secrets belong in the installation's own override files, never
in a repository.

### Pitfalls

- **Reading in a constructor of a class that runs at warm-up.** On Velocity, a value read once at warm-up stays
  until restart. Read settings when needed.
- **No defaults.** A missing setting returns `false` or an empty value and writes a warning; give every setting a
  default in the extension's file.
- **Templates printing settings.** Never print settings that may hold secrets.

## Hands-on exercise

On the development installation, in `extension/mysite_hello`.

1. Create `settings/mysite.ini.append.php`:

   ```ini
   <?php /* #?ini charset="utf-8"?

   [GreetSettings]
   # The highest number of greetings /hello/greet/<name>/(times)/<n> shows.
   # A whole number from 1 to 20.
   MaxTimes=5
   # Text in front of the name.
   Greeting=Hello

   */ ?>
   ```

2. Read it: `php bin/php/console exp:ini where mysite.ini/GreetSettings/MaxTimes <sa>`.
3. Use it in `modules/hello/greet.php`: replace the fixed `5` with

   ```php
   $ini = eZINI::instance( 'mysite.ini' );
   $maxTimes = $ini->hasVariable( 'GreetSettings', 'MaxTimes' ) ? (int)$ini->variable( 'GreetSettings', 'MaxTimes' ) : 5;
   $times = isset( $Params['UserParameters']['times'] ) ? max( 1, min( $maxTimes, (int)$Params['UserParameters']['times'] ) ) : 1;
   ```

4. Use the greeting text in `greet.tpl`:
   `{ezini( 'GreetSettings', 'Greeting', 'mysite.ini' )|wash}, {$name|wash}!`.
5. Check with `php -l`, clear the `ini` and template caches, open `/hello/greet/Ada/(times)/9`.
6. Override for your site only:
   `php bin/php/console exp:ini set mysite.ini/GreetSettings/MaxTimes 2 siteaccess:<sa>`, and open the address again.
7. Run `exp:ini where` from step 2 again and read both files.
8. Remove the override with `exp:ini rem mysite.ini/GreetSettings/MaxTimes siteaccess:<sa>` and commit the
   extension's files.

## Expected result

- Step 2: the extension's file with `MaxTimes=5` and `In effect:`.
- Step 5: five greetings (the maximum), each starting with "Hello".
- Step 6: two greetings.
- Step 7: two files in load order, the siteaccess value in effect.
- The trainer checks one learner's `mysite.ini.append.php` for comments on every setting.

## Quiz

1. Where does an extension keep an INI file of its own?
2. Which method checks that a setting exists before reading it?
   A. `variable()` B. `hasVariable()` C. `instance()` D. `variableArray()`
3. How does a template read `MaxTimes` from `mysite.ini`?
4. A site needs a different value. Which file changes?
5. Why name the file `mysite.ini` and not `settings.ini`?

<details>
<summary>Answers</summary>

1. In its `settings/` folder, as `<name>.ini.append.php`.
2. **B.** `hasVariable( 'GreetSettings', 'MaxTimes' )`.
3. `{ezini( 'GreetSettings', 'MaxTimes', 'mysite.ini' )}`.
4. An override in the site's scope (a siteaccess or the global override), never the extension's file.
5. INI file names are shared by all extensions; a prefixed name cannot collide.

</details>

## Further reading

- [Your own INI file](../../../designers/extensions/your-own-ini-file.md).
- [Settings files in an extension](../../../designers/extensions/settings-files-in-an-extension.md).
- [Siteaccess settings in an extension](../../../designers/extensions/siteaccess-settings-in-an-extension.md).

## Related pages

- [Module 8: Settings and siteaccesses](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
