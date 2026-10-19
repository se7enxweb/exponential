---
title: "Lesson 5.3: Request logs and the security log"
audience: trainer, hosting, admin
category: training
order: 53
summary: Learners read 404.log and security.log lines field by field, explain the request classifier and why it never blocks, tune it with an exclude, and build a weekly security routine. 40 minutes.
keywords: hosting and operations, request logs, 404.log, security.log, notice.log, request classifier, probe, traversal, injection, scanner, threshold, requestclassifier.ini, auth.log, csp.log, audit trail, masking
type: help
---
# Lesson 5.3: Request logs and the security log

Exponential writes not-found answers and suspicious requests to logs of their own, so `error.log` keeps only the real
faults. For a host this means faster support ("why do visitors get errors?") and a clear security signal. This lesson
teaches learners to read both logs, to tune the classifier, and to know what it does not do.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 5: Monitoring and logs](README.md) |
| Duration | 40 minutes (20 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Hosting support and security staff |
| Prerequisites | [Lesson 5.2](log-viewer-and-rotation.md) |

## Learning objectives

After this lesson, learners can:

- say which of `error.log`, `404.log`, `notice.log` and `security.log` a request lands in;
- read the fields of a `404.log` line, especially `reason=`;
- read a `security.log` line (`<category>/<rule>`, confidence, status) and the threshold rule;
- explain that the classifier only classifies, and name where blocking belongs;
- exclude a legitimate path from a rule instead of switching a rule set off.

## Trainer's notes

### How to present it

Source: [Request logs](../../../hosting/operations/request-logs.md), [Security log](../../../hosting/operations/security-log.md)
and [Request logs: 404.log and security.log](../../../../features/6.0/request-logs.md).

1. **Three logs instead of one** (4 minutes). `error.log` for real faults, `404.log` for 404 and 410 with the reason,
   `notice.log` for 401 and 403, `security.log` for classified requests with any status. Each request is written to
   one of them, never to two.
2. **A 404.log line** (4 minutes). Masked address, status, method and URI with query values as `***`, siteaccess,
   `reason=` (`node:<id>` is a link to removed content, `module:wp-admin` a robot looking for another product),
   referrer, a short hash of the user agent.
3. **A security.log line** (4 minutes). `probe/wp-login confidence=high 404 GET /wp-login.php ...`. Categories:
   `probe`, `traversal`, `injection`, `scanner`, `threshold`. The threshold rule flags more than 30 not-found answers
   within 60 seconds, keyed by a salted hash, never the address.
4. **Never blocks** (3 minutes). "Nothing is blocked." Blocking belongs to a shield in front of the kernel, the web
   server or firewall, or a listener class. A request the shield refuses is not in `security.log`; do not count it
   twice.
5. **A weekly routine** (5 minutes). The error digest groups `security.log` by rule; a sudden rise of one rule is
   worth a look. Add `auth.log` (two-step sign-in), `csp.log` (Content Security Policy reports) and the audit trail.

### Common questions

**Are IP addresses in the logs?** Masked by default (`MaskIP=enabled`); the raw address is never written. Writing it
in full needs a reason and a legal basis; how long logs are kept is the customer's and the host's policy.

**A customer's own application uses `/administrator/`. Do I switch off the probe rules?** No. Add an `Excludes[<name>]=<regex>`
entry for that path in an override of `requestclassifier.ini`.

**Can a 200 be in security.log?** Yes: an injection attempt against a search page answers normally.

**Why do I not see a request that was refused at the web server?** It never reached Exponential; look in the web
server's own logs.

### Pitfalls

- **Treating security.log as a list of breaches.** It lists attempts. Most are robots looking for other products.
- **Sending probes at someone else's site.** In the exercise learners request paths on their own installation only.
- **Editing the shipped `requestclassifier.ini`.** Use an override.

## Hands-on exercise

Use your own training installation and a shell in its root. Replace the address with your own.

1. Request a missing page with a query value:
   `curl -s -o /dev/null "http://127.0.0.1:8088/no-such-page?campaign=test"`.
2. Read the last line of `404.log`: `tail -n 1 var/log/404.log`. Identify the masked address, the `***` query value and
   `reason=`.
3. Request a path a probe rule recognizes, on your own site: `curl -s -o /dev/null http://127.0.0.1:8088/wp-login.php`.
4. Read the last line of `security.log`: `tail -n 1 var/log/security.log`. Identify the category, rule, confidence and
   status.
5. Check that the probe is not also in `404.log` (compare the last lines).
6. Open `settings/requestclassifier.ini` read-only (`less settings/requestclassifier.ini`) and find
   `Max404PerMinute`, the block `[RuleSet-probes]` and the comment that explains `Excludes[<name>]=<regex>`.
7. Write, but do not save, the override lines that would exclude a legitimate path `/administrator/` from the probe
   rules: a `[RuleSet-probes]` block with one `Excludes[<name>]=<regex>` line, as the shipped file describes.
8. In the administration, open **Setup**, **Log files**, then **Error digest**, and find your probe grouped by rule.

## Expected result

- Step 2: the line shows a masked address, `campaign=***` and a `reason=`.
- Step 4: the line starts with `probe/` and a rule name, with a confidence and the status.
- Step 5: the probe is in `security.log` only.
- Step 7: an override in `settings/override/requestclassifier.ini.append.php` with an `Excludes[<name>]=<regex>` line in the
  `[RuleSet-probes]` block.
- The trainer checks two learners' step 7 lines against the shipped file.

## Quiz

1. A robot requests `/wp-login.php` and gets a 404. Which log has the line?
   A. `error.log` B. `404.log` C. `security.log` D. both `404.log` and `security.log`
2. What does `reason=node:9999` in `404.log` tell support?
3. Does the request classifier block attacks?
4. When does the threshold rule flag a client?
5. Why is the raw client address not in these logs?

<details>
<summary>Answers</summary>

1. **C.** Each request is written to one log; a probe that ends in a 404 is in `security.log` only.
2. A link points to content (node 9999) that was removed.
3. No. It only classifies; blocking belongs to a shield, the web server or firewall, or a listener
   ([Security log](../../../hosting/operations/security-log.md#blocking-is-not-the-classifiers-job)).
4. When it receives more than `Max404PerMinute` (30) not-found answers within `WindowSeconds` (60).
5. `MaskIP=enabled` is the default; the address is masked before it is written.

</details>

## Further reading

- [Request logs](../../../hosting/operations/request-logs.md): fields, privacy, disk use.
- [Security log](../../../hosting/operations/security-log.md): the classifier, tuning, a weekly routine.
- [Request shield and request rules](../../../hosting/security/request-shield-and-request-rules.md): where blocking
  belongs.
- [Log and data retention](../../../hosting/security/log-and-data-retention.md): how long to keep logs.

## Related pages

- [Module 5: Monitoring and logs](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
