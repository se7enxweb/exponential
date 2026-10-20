---
title: "Lesson 1.4: A script with the kernel loaded"
audience: trainer, developer
category: training
order: 14
summary: Learners create their first extension folder under Git, write a small PHP script that reads settings and content, and run it with ezexec.php so the kernel is loaded for them. 45 minutes.
keywords: developers course, ezexec, eZINI, eZCLI, eZContentObjectTreeNode, fetch node, php -l, mysite_hello, first script, extension folder
type: help
---
# Lesson 1.4: A script with the kernel loaded

The fastest way to learn the kernel's classes is to call them. `bin/php/ezexec.php` loads the kernel (settings,
database, autoloads) and then runs any PHP file. In this lesson learners create the extension folder they will use
for the rest of the course, and write a script that reads a setting and a node.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 1: The development installation](README.md) |
| Duration | 45 minutes (10 presenting, 35 exercise) |
| Level | Beginner |
| Audience | PHP developers new to Exponential |
| Prerequisites | [Lesson 1.3](debug-output-and-logs.md); a text editor |

## Learning objectives

After this lesson, learners can:

- create an extension folder `extension/mysite_hello` and put it under Git;
- write a script that uses `eZINI`, `eZCLI` and `eZContentObjectTreeNode`;
- check a PHP file with `php -l` before running it;
- run a script with `php bin/php/ezexec.php <file>` and read its output.

## Trainer's notes

### How to present it

Source: [Your first hour as a developer](../../../extension-development/part-1-from-user-to-developer/your-first-hour-as-a-developer.md),
exercise 6.

1. **The course extension** (3 minutes). Every learner builds one extension through the course, `mysite_hello`.
   It is not active yet; module 7 switches it on. A script run with `ezexec.php` needs no activation.
2. **Three classes** (4 minutes). `eZINI::instance( 'site.ini' )` reads settings with every override applied;
   `eZCLI::instance()` writes to the terminal; `eZContentObjectTreeNode::fetch( $nodeID )` loads a node (a place in
   the content tree).
3. **Checking first** (1 minute). `php -l` is the compile check before anything else.
4. **Running** (2 minutes). `php bin/php/ezexec.php extension/mysite_hello/scripts/hello.php`.

### Common questions

**What is the difference between a node and an object?** An object is the content (with versions and
translations); a node is one of its places in the tree. Most templates and fetches work with nodes. Module 4 goes
deeper.

**Is a script run this way allowed to change content?** It runs with the kernel's full rights. In this course,
scripts only read until module 9.

**How do I pass options to the script?** Scripts that need options are written as console commands; lesson 9.2
shows how.

### Pitfalls

- **A file outside the installation.** Keep scripts inside the extension; paths in examples are relative to the
  installation root.
- **Running as root.** Add nothing: work as the learner's own user. As root, scripts stop with a warning unless
  `--allow-root-user` is given.
- **Node 2 is not always the front page.** On the training package node 2 is the top of the content tree. Learners
  can check the ID in the administration.

## Hands-on exercise

On the development installation, from its root folder.

1. Create the extension folder and its Git repository:

   ```bash
   mkdir -p extension/mysite_hello/scripts
   git -C extension/mysite_hello init
   ```

2. Create `extension/mysite_hello/scripts/hello.php` with this content:

   ```php
   <?php
   // Run: php bin/php/ezexec.php extension/mysite_hello/scripts/hello.php
   $cli = eZCLI::instance();
   $ini = eZINI::instance( 'site.ini' );
   $cli->output( 'Site name: ' . $ini->variable( 'SiteSettings', 'SiteName' ) );
   $cli->output( 'Active extensions: ' . count( $ini->variable( 'ExtensionSettings', 'ActiveExtensions' ) ) );

   $node = eZContentObjectTreeNode::fetch( 2 );
   if ( $node instanceof eZContentObjectTreeNode )
   {
       $cli->output( 'Node 2: ' . $node->attribute( 'name' ) . ' (class ' . $node->attribute( 'class_identifier' ) . ')' );
       $cli->output( 'Children: ' . $node->subTreeCount( array( 'Depth' => 1 ) ) );
   }
   else
   {
       $cli->error( 'Node 2 was not found' );
   }
   ```

3. Check the syntax: `php -l extension/mysite_hello/scripts/hello.php`.
4. Run it: `php bin/php/ezexec.php extension/mysite_hello/scripts/hello.php`.
5. Compare the site name with `php bin/php/console exp:ini get site.ini/SiteSettings/SiteName`.
6. Change the script to fetch the node ID of an article you can see in the administration, and run it again.
7. Add the line `eZDebug::writeError( 'Test error from hello.php', 'mysite_hello' );` at the end, run the script,
   and find the line in `var/log/error.log`.
8. Remove the line from step 7.
9. Commit the script in the extension's own repository:

   ```bash
   git -C extension/mysite_hello add scripts/hello.php
   git -C extension/mysite_hello commit -m "Added: First script that reads settings and a node"
   ```

## Expected result

- Step 3 prints `No syntax errors detected`.
- Step 4 prints the site name, the number of active extensions, the name and class of node 2 and its number of
  children.
- Step 7: `var/log/error.log` ends with a line containing `Test error from hello.php` and the label `mysite_hello`.
- Step 9: `git -C extension/mysite_hello log --oneline` shows one commit.
- The trainer checks the output of step 4 on two learners' screens.

## Quiz

1. What does `bin/php/ezexec.php` do before it runs your file?
2. Which class reads settings with every override applied?
   A. `eZCLI` B. `eZINI` C. `eZDB` D. `eZScript`
3. What is the difference between a content object and a node?
4. Which command checks a PHP file's syntax without running it?
5. Why does the course extension live in a Git repository of its own and not in the kernel's?

<details>
<summary>Answers</summary>

1. It loads the kernel: settings, database connection and autoloads.
2. **B.** `eZINI::instance( 'site.ini' )`.
3. The object is the content with its versions and translations; a node is one place of it in the content tree.
4. `php -l <file>`.
5. Extensions are separate repositories; the kernel is updated through Composer and Git, and your work is
   committed in your own extension ([Setting up a development installation](../../../extension-development/part-1-from-user-to-developer/setting-up-a-development-installation.md)).

</details>

## Further reading

- [Your first hour as a developer](../../../extension-development/part-1-from-user-to-developer/your-first-hour-as-a-developer.md):
  six short exercises around settings, templates, debug output and scripts.
- [The tools you need](../../../extension-development/part-1-from-user-to-developer/tools-you-need.md): PHP,
  Composer, Git, an editor and the browser's developer tools.

## Related pages

- [Module 1: The development installation](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
