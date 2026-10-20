---
title: "Lesson 3.5: Adapting and improving these courses"
audience: trainer, developer
category: training
order: 35
summary: Adapt the trainer courses to your organization and send improvements back, Edit on GitHub, a fork and a pull request for a fix, your own changed copy under the GFDL, and reporting what is wrong. 40 minutes.
keywords: adapt, improve, edit on github, pull request, fork, contribute, report an error, course material, GFDL, community
type: help
---
# Lesson 3.5: Adapting and improving these courses

Every trainer finds something to improve: an unclear step, a changed button, a missing question. These courses are
part of Exponential's documentation, so a fix can help every trainer after you. This lesson shows the three paths:
adapt a private copy, send a fix back, or report a problem.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 3: Designing a course](README.md) |
| Duration | 40 minutes (10 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Trainers; developers who help them |
| Prerequisites | [Lesson 1.4](../module-01-preparing-to-teach/your-material-and-the-gfdl.md); a GitHub account for the pull request path |

## Learning objectives

After this lesson, learners can:

- find the source file of any help page and its **Edit on GitHub** link;
- choose between a private adaptation, a fix sent back and an error report;
- prepare a small change as a pull request;
- report a problem in the right place, without personal data.

## Trainer's notes

### How to present it

1. Open any lesson in the Help center and show the page facts: the source file (under `doc/en/us/help/trainers/...`) and
   the **Edit on GitHub** link (3 minutes).
2. Explain the three paths (5 minutes):
   - **Private adaptation** for your organization: allowed under the GFDL, with the conditions of lesson 1.4.
   - **Fix sent back**: a pull request; accepted under the same license, so it stays free for everyone.
   - **Error report**: the Help center's **Report an error** page shows which tracker to use and what to write.
3. Show [Develop with Git](../../../develop-with-git.md) and its section on contributing a change with a pull request
   (2 minutes).

### Common questions

**Do I need to be a developer to send a fix?** No. **Edit on GitHub** opens the file in the browser; GitHub creates the
fork and the pull request for you.

**Which tracker for a documentation error?** The **Report an error** page lists the trackers (the software, Velocity,
the project and community) and the extensions; documentation of the kernel belongs to the software.

### Pitfalls

- **Big rewrites in one pull request.** Small, focused changes are reviewed faster.
- **Personal data in a report.** Never paste learner names, e-mail addresses or passwords.
- **Security problems.** Never in a public issue; follow `SECURITY.md` (the **Security issues** part of the report
  page).

## Hands-on exercise

1. In the Help center, open this lesson and find its source file path and the **Edit on GitHub** link.
2. Open the Help center's **Report an error** page (`/exphelp/report`) and read the headings **Before you report**,
   **Where to report**, **Write the report**, **Diagnostics** and **Security issues**.
3. Find one thing to improve in any trainer lesson (a typo, an unclear step).
4. If you have a GitHub account: press **Edit on GitHub**, make the change, describe it in one sentence and open a
   pull request. If not: write the change as an error report draft with title, what happened, steps and what you
   expected, and do not send it.
5. Write down the license under which your change is accepted.

## Expected result

- The learner knows the source path of the lesson and where the report page is.
- A pull request link, or a complete error report draft without personal data.
- The learner can name the license (GNU FDL 1.3 or any later version).

## Quiz

1. Where do you find the source file of a help page?
2. Under which license is a change to these pages accepted?
3. Where should a security problem go?
4. Name the three paths for improving a course.

<details>
<summary>Answers</summary>

1. In the page facts of the viewer, with the **Edit on GitHub** link.
2. The GNU Free Documentation License 1.3 or any later version ([Free documentation](../../../free-documentation.md)).
3. Privately, as `SECURITY.md` describes; never a public issue.
4. A private adaptation, a fix sent back as a pull request, and an error report.

</details>

## Further reading

- [Develop with Git](../../../develop-with-git.md): from user to contributor.
- [Improving the help](../../../advocates/improving-the-help.md): the advocates' guide to help fixes.
- [When something goes wrong](../../../when-something-goes-wrong.md): bug reports and security problems.

## Related pages

- [Module 3: Designing a course](README.md)
- [Train the trainer: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
