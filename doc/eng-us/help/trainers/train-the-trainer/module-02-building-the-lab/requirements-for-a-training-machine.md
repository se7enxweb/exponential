---
title: "Lesson 2.1: Requirements for a training machine"
audience: trainer, admin
category: training
order: 21
summary: Check that a learner's laptop or a training server can run Exponential before the course, PHP version, the required PHP extensions, php.ini values, Composer and Git, with the commands that show what is missing. 40 minutes.
keywords: requirements, training lab, laptop, PHP 8, PHP extensions, php -m, intl, xsl, sqlite3, composer, git, velocity ext check, system check
type: help
---
# Lesson 2.1: Requirements for a training machine

Most lost time in a hands-on course is lost in the first half hour, on a laptop that lacks one PHP extension. This
lesson gives the trainer the exact requirements of Exponential 6 and the commands to check a machine in two minutes,
so the check happens days before the course, not during it.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 2: Building the training lab](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Trainers who prepare labs; administrators who help them |
| Prerequisites | A terminal on the machine to check; basic command-line use |

## Learning objectives

After this lesson, learners can:

- state the PHP version Exponential needs, and the higher one Velocity needs;
- list the PHP extensions the setup checks treat as critical;
- check a machine with `php -v` and `php -m` and read the result;
- write a one-page pre-course checklist for learners.

## Trainer's notes

### How to present it

1. Show the checklist table of [Requirements, 2.1](../../../../install/02-requirements.md#21-the-checklist)
   (5 minutes). The key numbers: PHP 8.0 minimum for Exponential; PHP 8.0 with `sockets` for Exponential Velocity;
   SQLite 3 needs no database server; Composer 2.x.
2. Go through the required extensions (5 minutes): `dom`, `libxml`, `simplexml`, `xsl`, `mbstring`, `iconv`, `intl`,
   `pcre`, `json`, `reflection`, `spl`, `session`, `zlib`, one database extension (for a lab: `sqlite3`), and `gd` or
   ImageMagick's `convert`.
3. Show `php -m` on your own machine and find two of them in the list (2 minutes).
4. Explain why SQLite is the right database for a class (3 minutes): no server, no password, one file per
   installation, and it is the first database in the official order of preference.

### Common questions

**Windows laptops?** Check [Operating system support](../../../os-support.md) for what the operating system has to
provide. If you are unsure, prepare a fallback: a pre-installed copy per learner on a machine you control.

**Do learners need Velocity?** No. PHP's built-in server, which Velocity drives as its `php` engine, is enough for a
lab. Velocity's `qbix` engine needs PHP 8.0 or later and `sockets`.

**Which `php.ini` values matter?** `memory_limit` 64M or more, `max_execution_time` 30, `date.timezone` set,
`file_uploads` on, `allow_url_fopen` on.

### Pitfalls

- **Two PHPs.** The command-line PHP and the web server's PHP can differ. For a lab served by the built-in server,
  only the command-line PHP matters.
- **`intl` and `xsl` missing.** These are the two most often missing on fresh systems; the setup check does not
  continue without them.
- **No `date.timezone`.** Set it in `php.ini` before the course.

## Hands-on exercise

Check one machine: your laptop, a learner's laptop or the training server.

1. Open a terminal and run `php -v`. Note the version (it must be 8.0 or later; 8.1 or later if you will use the
   Velocity `qbix` engine).
2. Run `php -m` and compare the output with the required list above. Write down every missing extension.
3. Run `php -i | grep -E 'memory_limit|max_execution_time|date.timezone|file_uploads|allow_url_fopen'` and compare
   the values with the trainer's notes.
4. Run `composer --version` and `git --version`. Both must answer.
5. If an Exponential checkout with Velocity is already on the machine, run
   `php bin/php/console exp:velocity ext check` in its root and read the `ok` lines.
6. Write a learner checklist of no more than ten lines: PHP version, the extensions, the `php.ini` values, Composer,
   Git, and where to ask for help before the course.

## Expected result

- A list of the machine's PHP version, missing extensions (ideally none) and `php.ini` values.
- A learner checklist of ten lines or fewer that a learner can follow alone.
- The trainer reviews: are `intl`, `xsl` and `sqlite3` on the list? Is the PHP minimum right?

## Quiz

1. What is the minimum PHP version for Exponential 6, and for Velocity?
2. Name four PHP extensions the setup's critical tests require.
3. Why is SQLite the usual choice for a training lab?
4. Which command lists the PHP extensions of the command-line PHP?

<details>
<summary>Answers</summary>

1. PHP 8.0 for Exponential; PHP 8.0 with `sockets` for Velocity
   ([Requirements](../../../../install/02-requirements.md#21-the-checklist)).
2. Any four of `dom`, `xsl`, `mbstring`, `iconv`, `intl`, `session`, `zlib`, a database extension, `gd` (or
   ImageMagick).
3. No database server and no password are needed; each installation is one file; it is the default of `exp:install`.
4. `php -m`.

</details>

## Further reading

- [Requirements](../../../../install/02-requirements.md): the complete chapter.
- [Operating system support](../../../os-support.md): what each operating system has to provide.
- [Database support](../../../database-support.md): the databases and their order of preference.

## Related pages

- [Module 2: Building the training lab](README.md)
- [Train the trainer: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
