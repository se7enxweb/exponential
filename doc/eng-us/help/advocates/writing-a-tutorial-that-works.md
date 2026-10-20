---
title: Writing a tutorial that works
audience: advocate
category: advocacy
order: 51
summary: How to write an Exponential tutorial that readers can actually follow - a tested starting point, prerequisites, numbered steps with a checkpoint each, real commands, expected results, troubleshooting and a version line - with a template and a test protocol.
keywords: tutorial, how-to, guide, steps, checkpoints, prerequisites, commands, testing, troubleshooting, version, template, developers, editors
type: help
---
# Writing a tutorial that works

This page is for advocates who write step-by-step tutorials. A tutorial fails the moment a reader's screen does not
match yours. The method below, borrowed from how the help itself is written (every part "ends in something you can
check", as the [templates and design guide](../../guides/templates-and-design.md) puts it), makes tutorials that work.

## Contents

1. [The rules of a working tutorial](#the-rules-of-a-working-tutorial)
2. [Choose the starting point](#choose-the-starting-point)
3. [The template](#the-template)
4. [Writing steps](#writing-steps)
5. [Test it like a stranger](#test-it-like-a-stranger)
6. [Keep it alive](#keep-it-alive)

## The rules of a working tutorial

1. **One goal**, stated in the first sentence.
2. **A reproducible start**: a fresh installation made in a documented way.
3. **Every step ends in a checkpoint**: something the reader can see.
4. **Real commands and real button names**, copied from the screen, not from memory.
5. **The version** you used, and the date you checked it.
6. **What to do when it fails**, for the two or three likely failures.
7. **Links to the help pages** that go deeper.

## Choose the starting point

The simplest reproducible start is a fresh installation on SQLite
([Install in one command](../../features/6.0/install-in-one-command.md)):

```bash
./console exp:install --dry-run
./console exp:install
```

For tutorials about extensions, start from a Git installation so readers can contribute later
([Develop with Git](../develop-with-git.md)). For editor tutorials, say which role the reader needs (for example
Editor or Administrator).

## The template

```text
# <Do something specific>

After this tutorial you can <goal>. It takes about <n> minutes.

Tested with Exponential <6.0.x>, PHP <8.x>, <engine>, <database>, admin4 design, on <date>.

## Before you start
- <prerequisite 1, with a link>
- <prerequisite 2>

## Step 1: <verb + object>
<What to do, in one or two sentences.>
    <command or click path in bold>
You should now see: <checkpoint>.

## Step 2: ...

## If something goes wrong
| You see | Do |
|---|---|
| <message> | <fix, with a link> |

## Where to go next
- <help page>
- Questions: share.exponential.earth/forums, t.me/exponentialcms
```

## Writing steps

- **Start with a verb**: "Open", "Press", "Run", "Type".
- **Write click paths in bold**, as on screen: **Setup > Classes**, **Send for publishing**.
- **One action per step**, or a short numbered list inside the step.
- **Show expected output**, shortened if long, with `...` where you cut.
- **Explain why** in one sentence where it helps; link the concept page for more.
- **No secrets** in examples: placeholders like `<your-password>`, invented names, `example.com` addresses.
- **Code blocks** have a language (`bash`, `php`) so the help and blogs highlight them.

| Instead of | Write |
|---|---|
| "Click the button." | "Press **Send for publishing**." |
| "Configure the settings." | "In `settings/site.ini`, set ..." (with the exact block and key) |
| "It should work now." | "The page now shows the new title." |

## Test it like a stranger

1. Make a fresh installation exactly as the tutorial says.
2. Follow the tutorial word by word, copying commands from the text, not from your terminal history.
3. Note every place you had to guess. Fix the text there.
4. Ask one other person to follow it and watch silently. Their questions are your missing sentences.

## Keep it alive

- After each release, re-run the tutorial or at least check the changelog for changed screens
  ([changelogs](../../../../changelogs/README.md)).
- Update the version line when you re-test.
- If a step no longer works, put a visible note at the top until you fix it.
- If your tutorial would help every user, propose it for the help itself ([Improving the help](improving-the-help.md)).

## Related pages

- [Writing a blog post](writing-a-blog-post.md)
- [Recording a screencast](recording-a-screencast.md)
- [Publishing on Share and Projects](publishing-on-share-and-projects.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
