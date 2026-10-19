---
title: "Lesson 4.5: Security and privacy in class"
audience: trainer, admin
category: training
order: 45
summary: Keep a course safe, training installations bound to the local machine, no shared administrator accounts, test addresses at example.invalid, no real data, passwords that meet the rules, security problems reported privately, and lab removal afterwards. 40 minutes.
keywords: security, privacy, classroom, passwords, example.invalid, personal data, SECURITY.md, training lab, cleanup, two-factor
type: help
---
# Lesson 4.5: Security and privacy in class

Training installations are often the least protected sites an organization runs: default ports, simple passwords,
forgotten afterwards. Learners copy what they see in a course into their real work. This lesson makes security and
privacy part of every course, from the lab to the clean-up.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 4: Delivering a course](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Trainers and lab administrators |
| Prerequisites | [Module 2](../module-02-building-the-lab/README.md) |

## Learning objectives

After this lesson, learners can:

- apply the classroom security rules: local binding, own accounts, real passwords, no real data;
- explain why test accounts use `example.invalid`;
- tell learners where security problems go (privately, never in a public issue);
- remove a lab completely after a course.

## Trainer's notes

### How to present it

1. The rules (8 minutes):
   - labs bound to `127.0.0.1` (the Velocity default) or behind a proper web server with HTTPS;
   - one account per person, never a shared administrator;
   - passwords that meet the site's minimum length; generated passwords changed at the first login;
   - only invented content and test addresses at `example.invalid`; no mail to real people;
   - no real customer data, not even "just for the exercise".
2. Security reports (3 minutes): the Help center's **Report an error** page has a **Security issues** part; a
   vulnerability goes privately, as `SECURITY.md` describes, never to a forum or a public issue.
3. Clean-up (4 minutes): stop servers, remove training accounts from shared sites, delete lab directories you
   created.

### Common questions

**Should we turn on two-factor sign-in in class?** On a shared training site it is good practice to show it; the
Site administration course has a lesson on it, and the guide is [Two-factor login](../../../../guides/two-factor-login.md).

**What about learners' own laptops?** The lab lives in a directory they own; tell them how to stop the server and
remove the directory after the course.

### Pitfalls

- **Open ports on a conference network.** Never bind a lab to all addresses on a public network.
- **"Temporary" real data.** It never stays temporary.
- **Forgotten shared sites.** A training server left online with weak accounts is a risk; switch it off or clean it.

## Hands-on exercise

1. On your training installation, run `php bin/php/console exp:velocity status` and check the address the site
   listens on (it should be `127.0.0.1` for a laptop lab).
2. Open **Users** and look through every user group. List any account with an address that is not at
   `example.invalid`, and any account shared by several people.
3. Open the Help center's **Report an error** page and read **Security issues**.
4. Write a clean-up checklist for the end of your course: stop servers; remove learner accounts on shared sites;
   delete lab directories; delete any copies of `var/log/initial-admin-password`.
5. Rehearse the clean-up on one lab: stop the server with `php bin/php/console exp:velocity stop`.

## Expected result

- The lab listens on `127.0.0.1` only.
- The user list has no real e-mail address and no shared account (or the learner wrote down what to fix).
- A clean-up checklist of at least four items.

## Quiz

1. Why should a laptop lab listen on `127.0.0.1`?
2. Where must a vulnerability found during a course be reported?
3. Why use `example.invalid` for test accounts?
4. Name two items of an end-of-course clean-up.

<details>
<summary>Answers</summary>

1. It is reachable only from the machine itself; the development server has no authentication of its own.
2. Privately, as `SECURITY.md` describes ([When something goes wrong](../../../when-something-goes-wrong.md)).
3. The domain can never receive mail, so no test can send mail to a real person.
4. Any two of: stop servers, remove learner accounts, delete lab directories, delete password files.

</details>

## Further reading

- [Security hardening](../../../security-hardening.md)
- [Security reports stay private](../../../advocates/security-reports-stay-private.md)
- [Protecting people's privacy](../../../advocates/protecting-peoples-privacy.md)

## Related pages

- [Module 4: Delivering a course](README.md)
- [Train the trainer: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
