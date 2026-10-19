---
title: "Lesson 7.3: Finding an error by its reference"
audience: trainer, admin
category: training
order: 73
summary: Learners turn a visitor's report ("Something went wrong, reference ERR-...") into the log lines of that request, search by time and address when there is no reference, open the audit of the same moment and share the result safely. 35 minutes.
keywords: site administration, error reference, ERR-, error page, log viewer, Error reference, same request, exp:logs --reference, support ticket, troubleshooting
type: help
---
# Lesson 7.3: Finding an error by its reference

When a page fails, the visitor sees an error page with a reference such as `ERR-0123456789`. The same reference is
written to `error.log` next to the details. An administrator who knows how to follow that reference answers a support
ticket in minutes instead of hours. This lesson practises the path from the report to the cause.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 7: Logs and the audit trail](README.md) |
| Duration | 35 minutes (10 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Site administrators, support staff |
| Prerequisites | [Lesson 7.2](the-log-viewer.md); an administrator account and a shell on the training installation |

## Learning objectives

After this lesson, learners can:

- say what an error reference looks like and what to ask a visitor who reports an error;
- find the entry of a reference and the lines of the same request with the **Error reference** field;
- search by time window and request address when there is no reference;
- find the same reference from a shell with `exp:logs --reference=`;
- share a finding without exposing personal data.

## Trainer's notes

### How to present it

Source: [Finding an error by its reference](../../../../guides/finding-an-error-by-its-reference.md).

1. **What to ask** (2 minutes). The reference the error page showed, roughly when it happened, and the address of
   the page. A reference is `ERR-` followed by ten digits or letters A to F.
2. **Find it** (3 minutes). **Setup > Log files**, type the reference into **Error reference** and press **Search**.
   It is looked for in every kernel log and its rotations. The entry shows with **Same request** below it: the
   warnings, notices and debug lines of the same request, in time order.
3. **Read the request** (2 minutes). The *source* of each entry says which part of the code wrote it. Node and
   object ids, class identifiers and template paths are links. **The audit of this moment** opens the audit console
   at the same time: an edit or a settings change right before the error is often the cause.
4. **Not found?** (2 minutes). The log may have rotated away, or the error happened on another server: each server
   keeps its own `var/log`. Then search by **From** and **To** a few minutes around the report and the address in
   **Request address contains**.
5. **Share** (1 minute). Send the permalink, never unmasked lines.

### Common questions

**The visitor did not note the reference.** Ask for the time and the address of the page, and search by time window,
levels **Error** and **Warning**, scope **All kernel logs**.

**Is the reference random?** It is a label for one failure, written on the error page and in the log; it carries no
personal data, so visitors can safely send it.

**What does exit code 1 of `exp:logs --reference` mean?** The reference was not found.

### Pitfalls

- **No error on a healthy training site.** The **Error digest** may show an earlier `Unexpected error ERR-...` group;
  if not, the trainer prepares one reference on the projector installation before class, or learners practise the
  time-window search only. Do not break a learner's site on purpose without a way back.
- **Looking at the wrong server.** On a site with several web nodes, the reference is only in the log of the node
  that answered.
- **Unmasked lines in tickets.** They can hold customers' e-mail and IP addresses. Keep masking on.

## Hands-on exercise

Use your own training installation. Your trainer may give you a reference from the projector installation instead.

1. Open **Setup > Log files**, then **Error digest**. Look for a group whose message contains `ERR-`. If there is
   one, open its newest line and copy the reference from the message.
2. If there is none, use the reference your trainer gives you, or go on with step 6.
3. Back on **Logs**, type the reference into **Error reference** and press **Search**.
4. Read the entry: its time, level, siteaccess, masked client and request. Read **Same request** below it.
5. Follow one link in the lines (a node, an object or a template) and say what it points to.
6. Without a reference: in a second tab, open a missing page of your training site and note the time. In the search
   form, set **From** and **To** to a few minutes around that time, type the page's path in **Request address
   contains**, choose **All kernel logs** and press **Search**.
7. Press **Copy permalink** and paste it into your notes.
8. In a terminal, find the same reference or time window from the shell:

   ```bash
   php bin/php/console exp:logs --reference=ERR-0123456789
   ```

   (with your own reference), or for the time window:

   ```bash
   php bin/php/console exp:logs --search=no-such-page --scope=all --rotations --limit=5
   ```

9. Write a three-line answer to the imagined visitor: what happened, what you will do, and no personal data.

## Expected result

- Step 3 shows the error entry and, under **Same request**, the other lines of that request.
- Step 6 finds the missing page's line (in `404.log`) by time and address.
- Step 8 prints the entry (exit code 0), or exit code 1 when the reference was not found.
- The trainer checks the short answer of step 9: no address, IP or login of anyone appears in it.

## Quiz

1. What should you ask a visitor who reports an error?
2. Where does the log viewer look for an error reference?
   A. Only `error.log` B. Every kernel log and its rotations C. Only the web server's log D. The audit trail
3. A reference is not found. Name two possible reasons.
4. What does **The audit of this moment** help you find?
5. Which command finds a reference from a shell?

<details>
<summary>Answers</summary>

1. The reference on the error page, roughly when it happened, and the page's address.
2. **B.** ([Finding an error by its reference, section 2](../../../../guides/finding-an-error-by-its-reference.md#2-find-it)).
3. The log has rotated away; the error happened on another server, which keeps its own logs.
4. Who did what at the same time, for example an edit or a settings change that caused the error.
5. `php bin/php/console exp:logs --reference=<reference>`.

</details>

## Further reading

- [Finding an error by its reference](../../../../guides/finding-an-error-by-its-reference.md): the guide this lesson
  follows.
- [The log viewer, Searching](../../../../features/6.0/log-viewer.md#searching): every search option.
- [When something goes wrong](../../../when-something-goes-wrong.md): what editors and visitors can do first.
- [Site down runbook](../../../hosting/runbooks/site-down.md): when the whole site fails, not one request.

## Related pages

- [Module 7: Logs and the audit trail](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
