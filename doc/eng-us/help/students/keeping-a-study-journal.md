---
title: Keeping a study journal
audience: student
category: learning
order: 3
summary: A simple study journal for learning Exponential - what to record for each session, a template you can copy, how to keep it in Git next to your study site, what never belongs in it, and how the journal later becomes part of your portfolio.
keywords: study journal, learning log, notes, template, record, reflection, portfolio, git, progress, learning diary
type: help
---
# Keeping a study journal

This page is for learners who want to see their own progress and remember what they learned. A study journal is a
plain text file with one entry per session. It takes five minutes a day and pays back every time you meet the same
problem twice.

## Contents

1. [Why a journal helps](#why-a-journal-helps)
2. [What to record](#what-to-record)
3. [A template you can copy](#a-template-you-can-copy)
4. [Where to keep it](#where-to-keep-it)
5. [What never goes in it](#what-never-goes-in-it)
6. [From journal to portfolio](#from-journal-to-portfolio)

## Why a journal helps

- **You learn from differences.** The help pages say what you should see ("Expected: ..."). Writing down what you
  really saw makes you notice when it differs.
- **You stop solving the same problem twice.** A cache that needed clearing, a siteaccess you forgot to name, a
  role without a policy: these come back.
- **You can ask better questions.** A question with the steps, the expected result and the real result gets an
  answer faster ([Asking for help and study groups](asking-for-help-and-study-groups.md)).
- **You can show your work.** A teacher, a trainer or an employer can read what you did and how you thought
  ([Building a portfolio](building-a-portfolio.md)).

## What to record

For each session:

| Field | Example |
|---|---|
| Date and duration | 2026-10-20, 70 minutes |
| Release | `console (Exponential) 6.0.15stable`, from `php bin/php/console --version` |
| Goal | Make an override for the full view of one article |
| Page used | [Templates and design](../../guides/templates-and-design.md), section 4 |
| What I did | The commands or clicks, in order |
| Expected | What the page said I would see |
| What happened | What I saw, with the exact message if there was one |
| Why (if different) | What I found out |
| Next step | What I will do next time |

## A template you can copy

```text
## <date> (<minutes> min) - <short goal>

Release:   <output of php bin/php/console --version, first line>
Path:      <learning path and step, or study project number>
Page(s):   <titles of the help pages you used>

Did:
  1. <step>
  2. <step>

Expected:  <what the page said>
Happened:  <what you saw; copy error messages exactly>
Learned:   <one or two sentences in your own words>
New words: <terms to look up in the glossary>
Next:      <the next small step>
```

Keep entries short. One honest line in "Learned" is worth more than a page copied from the help.

## Where to keep it

- **A Markdown file in a Git repository** of your own, for example `journal.md` in a folder next to your study site.
  Commit after each session; the history then shows your progress day by day
  ([Using Git as a student](using-git-as-a-student.md)).
- **A paper notebook** works as well, if you prefer it. Copy the important commands into a file later.
- If your course or school gives you a learning platform, follow its rules for where work is kept.

Do not keep the journal inside the installation's own folders (such as `var/` or `settings/`): a reinstall can
remove them ([Resetting and rebuilding your study site](resetting-and-rebuilding-your-study-site.md)).

## What never goes in it

- **Passwords, keys and tokens.** The installer prints the administrator password once and writes it to
  `var/log/initial-admin-password` ([Getting started](../../guides/getting-started.md#5-first-login)). Keep it in a
  password manager, not in the journal. The same goes for personal API keys, which are shown only once
  ([Personal API keys](../../guides/api-keys.md)).
- **Other people's personal data.** Use invented names and addresses at `example.invalid` for test accounts.
- **Private server details** of an employer, a school or a customer: host names, IP addresses, paths.

If you ever commit a secret by mistake, treat it as public: change the password or revoke the key at once.

## From journal to portfolio

At the end of a learning path or a study project:

1. Reread your entries for that period.
2. Write a short summary: what you built, which pages you used, the two hardest problems and how you solved them.
3. Add screenshots of your study site (no passwords on screen).
4. Link the summary from your portfolio ([Building a portfolio](building-a-portfolio.md)).

## Related pages

- [How to study with the help](how-to-study-with-the-help.md)
- [Grading your own work](grading-your-own-work.md)
- [Student checklists](student-checklists.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
