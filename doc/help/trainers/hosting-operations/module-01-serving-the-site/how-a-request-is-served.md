---
title: "Lesson 1.1: How a request is served"
audience: trainer, hosting, admin
category: training
order: 11
summary: Learners compare the four ways to serve an Exponential site, trace a request through the routing rules and prove with four curl requests that the rules are active. 40 minutes.
keywords: hosting and operations, serving the site, request routing, htaccess_root, front controller, index.php, Velocity, Apache, nginx, FrankenPHP, curl check
type: help
---
# Lesson 1.1: How a request is served

An installed Exponential is a directory of PHP files. Something must accept connections, speak HTTP and HTTPS, hand
out static files and pass every other request to a front controller. This lesson gives learners the map they need
for the whole course: the four ways to serve a site, the one routing rule list they all share, and a quick check that
shows whether a server really applies it.

> **Note**
> Every exercise in this course runs on the learner's own training installation or virtual machine, never on a live
> customer site.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 1: Serving the site](README.md) |
| Duration | 40 minutes (20 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | System administrators and hosting support staff |
| Prerequisites | A training installation per learner, served on the learner's machine (see [One installation per learner](../../train-the-trainer/module-02-building-the-lab/one-install-per-learner.md)); a terminal with `curl` |

## Learning objectives

After this lesson, learners can:

- name the four ways to serve a site (Velocity with the `qbix` engine, FrankenPHP through Velocity, Apache with
  PHP-FPM, nginx with PHP-FPM) and say which one the project recommends;
- explain why the document root is the installation root;
- describe what happens to a request for a dot file, a PHP file below the root, an asset and any other path;
- check with four `curl` requests that the routing rules are active on a server.

## Trainer's notes

### How to present it

Source: [Chapter 8: Serving the site](../../../../install/08-serving-the-site.md), sections
[8.1](../../../../install/08-serving-the-site.md#81-the-choice-in-one-table) and
[8.2](../../../../install/08-serving-the-site.md#82-how-a-request-is-served).

1. **The choice in one table** (6 minutes). Put the table of section 8.1 on the projector. Point out three rows:
   *Separate web server needed* (no for Velocity and FrankenPHP), *Application kept in memory between requests*
   (only Velocity) and *Suits shared hosting* (usually only Apache with PHP-FPM). Velocity's `qbix` engine is the
   recommended way at every stage; FrankenPHP is a production-ready alternative; Apache and nginx with PHP-FPM are the
   traditional setups.
2. **When to choose a traditional server** (4 minutes). From section 8.1: hosting that forbids long-running
   processes, a tuned Apache or nginx the customer wants to keep, a machine with only PHP 8.0 (Velocity needs 8.1),
   or customers that need operating-system isolation from each other.
3. **One routing list** (6 minutes). Draw the diagram of section 8.2. The rules are written down once in
   `.htaccess_root`; Velocity's engines and the generated FrankenPHP configuration implement the same list. Stress
   the two security consequences: no PHP file runs because its path was asked for (except the front controllers at
   the root), and a file outside the asset list is never handed out.
4. **The check** (4 minutes). Show the four `curl` requests from section 8.2 against your own training site and
   explain each expected answer.

### Common questions

**Why is the document root not a `public/` directory?** The installation root is the document root under every
server; the routing rules decide what is reachable. A `404` for every asset under `design/` is the sign that the
document root is set wrong.

**Can I use PHP's built-in server?** It is the third Velocity engine (`php`) and the shipped default only because it
needs nothing installed. It has no TLS and is for development, not for visitors.

**Can two servers serve the same files?** Yes. Velocity beside Apache during a migration is a common shape; the file
ownership rules of [section 8.7.2](../../../../install/08-serving-the-site.md#872-one-user-or-one-group) then matter
(lesson 1.2).

**Where are the rules for nginx?** The project ships none; chapter 8 gives a derived server block, translated rule
for rule from `.htaccess_root` (lesson 1.2).

### Pitfalls

- **A `200` for `/.git/config`.** The rules are not active: Apache without `.htaccess` or `AllowOverride`, or an nginx
  block that is not the chapter's. Stop and fix it before anything else; this is the most important result of the
  lesson.
- **Wrong port.** Learners copy `127.0.0.1:8088` from the book while their site runs elsewhere. Have them run
  `php bin/php/console exp:velocity status` first; it lists every address as a full URL.
- **The qbix engine answers 403, not 404,** for dot paths. Both mean the file was refused.

## Hands-on exercise

Use your own training installation, served on your machine (for example with
`php bin/php/console exp:velocity start` after `exp:install`, see
[The console install](../../../../install/07-console-install.md)).

1. In a terminal, change to the installation root (the directory that holds `index.php`).
2. Run `php bin/php/console exp:velocity status` and note the address of the site.
3. Open `.htaccess_root` in a text viewer (`less .htaccess_root`). Find the rule for dot files and the final rule
   that sends everything else to `index.php`.
4. Request a dot file, replacing the address with your own:

   ```bash
   curl -s -o /dev/null -w '%{http_code}\n' http://127.0.0.1:8088/.git/config
   ```

5. Request a kernel source file:

   ```bash
   curl -s -o /dev/null -w '%{http_code}\n' http://127.0.0.1:8088/kernel/classes/ezcontentobject.php
   ```

6. Request `robots.txt` the same way and note the code.
7. Ask for the settings file and count whether its content came back:

   ```bash
   curl -s http://127.0.0.1:8088/settings/site.ini | grep -c '^\[DatabaseSettings\]'
   ```

8. Request a stylesheet path from the site's front page (copy one from the page source) and check that it answers
   `200`.
9. Write down, in one sentence each, what happened to the requests of steps 4 to 8 and why.

## Expected result

- Step 4: `404` (or `403` from the `qbix` engine).
- Step 5: `404`; the PHP file was neither run nor sent.
- Step 6: `200` when the file exists.
- Step 7: `0`: the request reached `index.php`, which answered with a page, not the file.
- Step 8: `200` for a listed asset path.
- The trainer checks by asking two learners to read their codes aloud and explain the step 7 result.

## Quiz

1. Which way of serving a site does the project recommend for every stage?
   A. Apache with PHP-FPM B. nginx with PHP-FPM C. Velocity with the `qbix` engine D. PHP's built-in server
2. What is the document root of an Exponential site?
3. A request for `/settings/site.ini` returns the file's contents. What does that tell you?
4. Name two reasons to choose Apache or nginx with PHP-FPM instead of Velocity.
5. True or false: a `.php` file inside an extension's design directory runs when its path is requested.

<details>
<summary>Answers</summary>

1. **C.** Chapter 8 calls Velocity's `qbix` engine the recommended way at every stage.
2. The installation root, the directory that holds `index.php` (section 8.2).
3. The routing rules are not active; fix the server before the site is reachable from outside.
4. Any two of: hosting that forbids long-running processes, an existing tuned server, only PHP 8.0 available,
   operating-system isolation between customers (section 8.1).
5. **False.** Scripts and dot paths are never served or run below the root, even in a listed asset directory.

</details>

## Further reading

- [Chapter 8: Serving the site](../../../../install/08-serving-the-site.md): the reference for this module.
- [Choosing a web server](../../../hosting/servers/choosing-a-web-server.md): the decision guide for hosts.
- [What hosting Exponential involves](../../../hosting/basics/what-hosting-exponential-involves.md): the bigger
  picture for hosting staff.
- [Requirements at a glance](../../../hosting/basics/requirements-at-a-glance.md): PHP and extension requirements.

## Related pages

- [Module 1: Serving the site](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
