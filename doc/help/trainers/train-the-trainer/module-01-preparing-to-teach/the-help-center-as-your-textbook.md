---
title: "Lesson 1.2: The Help center as your textbook"
audience: trainer, editor, admin
category: training
order: 12
summary: Use the Help center inside the administration as the textbook of every course, search, audience filters, the All pages tree, bookmarkable search addresses, context help, print and PDF. 45 minutes.
keywords: help center, exphelp, dashboard, search, audience filter, all pages, contents, print, pdf, context help, textbook, handout
type: help
---
# Lesson 1.2: The Help center as your textbook

Every Exponential installation carries its own manual: the **Help center**, which shows the documentation in `doc/`
inside the administration. For a trainer this is the best possible textbook. Learners find it again after the course
on their own installation, it matches the version they run, and every page can be printed or downloaded as a PDF for
a handout. This lesson teaches trainers to use it fluently, so they can teach learners to help themselves.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 1: Preparing to teach Exponential](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Beginner |
| Audience | Trainers; the same lesson works for editors and administrators |
| Prerequisites | [Lesson 1.1](how-these-courses-work.md); an account with the policy `exphelp/read` (every administrator; the Editor role on a new installation) |

## Learning objectives

After this lesson, learners can:

- open the Help center from the header, the user menu or its address `/exphelp/dashboard`;
- find a page by search, by audience and category filters and in the **All pages** tree;
- open the help for the page they are on with the **Help menu** ("?") in the header;
- build a search or browse address they can put on a handout;
- print a help page or download it as a PDF.

## Trainer's notes

### How to present it

1. Show the dashboard on the projector (`/exphelp/dashboard`): the heading **How can we help?**, the search field,
   the audience cards under **Start where you are**, **Featured guides**, **Recently updated**, **Browse by topic**
   and **Found an error?** (5 minutes).
2. Show the "?" button in the header (**Help menu**) on two different admin pages and point out that "Help for this
   page" changes with the page (3 minutes).
3. Search for a word your learners will need (for example `publish`) and show the filters (3 minutes).
4. Show the address bar after a filtered search: the filters are part of the address, for example
   `/exphelp/search/(category)/layouts/(audience)/editor`. That is how you put a ready search on a handout
   (2 minutes).
5. Open a page and show **Print this page** and **Download as PDF** (2 minutes).

### Common questions

**Can visitors of the public site read the help?** No. The help views need the policy `exphelp/read`; the Anonymous
role does not have it, and the help is never shown to visitors who are not signed in.

**An editor does not see the "?" button.** The Help button appears only for users who have `exphelp/read`. On an
installation from before the help system, an administrator grants it with `php bin/php/addexphelpreadpolicy.php`
(try it first with `--dry-run`).

**Is the help the same as the website `doc.exponential.earth`?** The Help center is generated from the `doc/`
directory of the installation itself, so it always matches the installed version.

**Which files does it show?** Only files tracked by Git below `doc/` (Markdown, text and images). Everything else is
a 404, by design.

### Pitfalls

- **Learners without the policy.** Check before the course that the learner accounts can open `/exphelp/dashboard`.
- **Instant search needs JavaScript.** Without it, the search still works after pressing **Search**.
- **The `/` key.** Pressing `/` jumps to the search field, but not while the cursor is in another field. Tell
  learners to click on an empty part of the page first.

## Hands-on exercise

Work on your own training installation, signed in to the administration.

1. Open the Help center: the "?" button in the header (**Help menu**), then **Open the Help center**. Note the
   address in the browser: it ends in `/exphelp/dashboard`.
2. Under **Start where you are**, open the card **Editors**. Note how many pages the audience shows.
3. Go back to the dashboard. Click into an empty part of the page, press `/` and type `version`. Watch the results
   appear while you type; then press **Search**.
4. On the search page, narrow the results with the audience filter **Editors**. Copy the address from the browser's
   address bar into a text file: this is a handout link.
5. Open **All pages** (`/exphelp/contents`). Type `layouts` into the filter field of the tree and open
   **The layouts editor**.
6. On that page, find the table of contents, the previous and next page links and the page facts (source file and
   last change).
7. Press **Print this page** and look at the print preview: no menus, only the document. Cancel the print.
8. Press **Download as PDF** and open the file.
9. Go to **Users > Roles and policies** in the administration, then open the "?" button again and choose the help
   for this page. Note which page opens.

## Expected result

- The learner has a text file with at least one search address of the form `/exphelp/search/...` that reopens the
  filtered search.
- The PDF of **The layouts editor** opens, with the page title and "Page n of N" at the foot of every page and the
  license notice at the end.
- On the roles page, the context help opens a page about roles and policies, not the dashboard. (If an installation
  has no context entry for a page, the help falls back to the dashboard; that is expected behavior.)

## Quiz

1. Which policy function opens the help views?
2. You want every learner to start from the same filtered list of layouts pages for editors. What do you put on the
   handout?
3. Which button opens the help for the administration page you are on?
4. True or false: the Help center shows any file in the installation directory if you know its path.
5. What does a downloaded PDF end with?

<details>
<summary>Answers</summary>

1. `exphelp/read` ([The help system](../../../../features/6.0/help-system.md#who-may-read-it)).
2. A browse address with view parameters, for example `/exphelp/search/(category)/layouts/(audience)/editor`;
   these addresses can be bookmarked and linked.
3. The "?" button in the header (**Help menu**), entry "Help for this page".
4. **False.** Only Git-tracked files below `doc/` with a page or image suffix; everything else is a 404.
5. The page's license notice (the GNU Free Documentation License).

</details>

## Further reading

- [The help system](../../../../features/6.0/help-system.md): every view, setting and command of the Help center.
- [Searching](../../../searching.md): searching the content of the site, which is different from searching the help.
- [Keyboard shortcuts](../../../keyboard-shortcuts.md): the keys of the administration and of the help pages.
- [A tour of the administration](../../../admin-tour.md): the header, the menus and the "?" button in context.

## Related pages

- [Module 1: Preparing to teach Exponential](README.md)
- [Train the trainer: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
