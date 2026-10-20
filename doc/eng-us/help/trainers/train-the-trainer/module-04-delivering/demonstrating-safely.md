---
title: "Lesson 4.2: Demonstrating safely"
audience: trainer
category: training
order: 42
summary: Demonstrate Exponential on a projector without leaking anything, a clean demo installation, invented content, no passwords or personal data on screen, a prepared machine, and a plan for when the demo breaks. 40 minutes.
keywords: demo, live demonstration, projector, safety, privacy, passwords, personal data, demo installation, invented content, backup plan
type: help
---
# Lesson 4.2: Demonstrating safely

A trainer demonstrates all day. Every demonstration is a chance to leak a password, a customer name or a private
message on a projector, or to change a real site by accident. This lesson applies the advocates' live-demo safety
rules to the classroom.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 4: Delivering a course](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Trainers |
| Prerequisites | [Lesson 2.3](../module-02-building-the-lab/one-install-per-learner.md) |

## Learning objectives

After this lesson, learners can:

- list what must never be on screen;
- prepare a demo installation with invented content only;
- prepare the machine (notifications off, one browser profile, no saved passwords visible);
- recover calmly when a demo goes wrong.

## Trainer's notes

### How to present it

1. Walk through [Live demo safety](../../../advocates/live-demo-safety.md) (10 minutes): what must never be on
   screen; prepare a clean demo installation; prepare the machine; during the demo; if something leaks; when the
   demo goes wrong.
2. Add the classroom view (5 minutes): demonstrate on your own training installation, never on a customer's or
   your organization's live site, and never with the shared administrator account of a real site.

### Common questions

**May I show our real intranet to make it concrete?** Only if no personal or confidential data can appear, and only
read-only. A copy with invented content is better.

**What if a learner asks to see a real customer site?** Point to public demos and to your own installation.

### Pitfalls

- **The install summary.** It prints the administrator password; never project that terminal.
- **Browser autofill.** It shows other accounts' logins. Use a separate browser profile.
- **Debug output.** It can show paths and settings; switch it on only when you teach it.

## Hands-on exercise

1. Read [Live demo safety](../../../advocates/live-demo-safety.md) completely.
2. Prepare your demo browser: a new browser profile with no saved logins and no extensions you do not need.
3. Open your training installation and check that its content is invented (demo content or your own made-up items).
4. Check the user list for real e-mail addresses; replace any with addresses at `example.invalid`.
5. Turn off desktop notifications for the length of a demo.
6. Run a three-minute demo for a partner: open an article, edit it, store a draft, discard it.
7. The partner notes anything on screen that should not have been there.

## Expected result

- A demo browser profile and an installation with invented content only.
- A three-minute demo during which the partner saw nothing private.

## Quiz

1. Name three things that must never be on screen during a demo.
2. Why should the terminal with the install summary not be projected?
3. Where should you demonstrate: your training installation or a live site?
4. What do you do if a password appears on screen?

<details>
<summary>Answers</summary>

1. Any three of: passwords, keys, personal data, customer names, private messages, real user lists
   ([Live demo safety](../../../advocates/live-demo-safety.md)).
2. It shows the generated administrator password.
3. Your own training installation.
4. Follow "If something leaks": change it at once after the demo (or during a break) and treat it as exposed.

</details>

## Further reading

- [Live demo safety](../../../advocates/live-demo-safety.md)
- [Giving demos as an advocate](../../../advocates/giving-demos-as-an-advocate.md)
- [Demo scripts](../../../business/demo-scripts.md): click-by-click demos of 5, 15 and 45 minutes.

**Demo mode for shared sign-ins.** When participants share a published administrator sign-in, run the installation in demo mode (`EXP_DEMO_MODE=1`): the password cannot be changed or reset during the session. It does not undo other changes; reinstall afterwards. [Demo mode FAQ](../../../demo-mode-faq.md).

## Related pages

- [Module 4: Delivering a course](README.md)
- [Train the trainer: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
