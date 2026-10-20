---
title: "Lesson 3.6: Online and hybrid delivery"
audience: trainer
category: training
order: 36
summary: Run the courses online or with a mixed room, labs learners can reach, screen sharing without leaking data, check-ins instead of walking the room, and shorter blocks. 40 minutes.
keywords: online training, remote, hybrid, video call, screen sharing, breakout rooms, check-in, lab access, ssh tunnel
type: help
---
# Lesson 3.6: Online and hybrid delivery

Online courses lose the trainer's best tool: walking the room and seeing a stuck learner. This lesson replaces it
with explicit check-ins, and covers the technical side: how learners reach a lab, and how to share a screen without
showing what must not be seen.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 3: Designing a course](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Trainers who teach online |
| Prerequisites | [Lesson 2.4](../module-02-building-the-lab/serving-the-lab-sites.md), [Lesson 2.7](../module-02-building-the-lab/shared-server-or-one-per-learner.md) |

## Learning objectives

After this lesson, learners can:

- choose a lab design for an online course (installations on learners' machines or a shared site);
- run a check-in after every exercise step that matters;
- share a screen without exposing passwords, personal data or other windows;
- shorten blocks to 45 minutes with breaks.

## Trainer's notes

### How to present it

1. Lab access (5 minutes): an installation on each learner's machine, served at `127.0.0.1`, is reachable only by
   the learner; that is good for privacy, and the trainer cannot see it. A shared site needs a proper web server with
   HTTPS (see [Serving the site](../../../../install/08-serving-the-site.md)); Velocity's `php` engine is for
   development only and should not be exposed.
2. Check-ins (5 minutes): after each key step, ask everyone to react "done" or "stuck" in the chat. Never move on with
   anyone stuck without a helper assigned.
3. Screen sharing (5 minutes): share one window, not the desktop; close mail and chat; use a training installation
   with invented content ([Live demo safety](../../../advocates/live-demo-safety.md)).

### Common questions

**Recording?** Only with the consent of everyone recorded, and without learners' screens unless they agree.

**Breakout rooms?** Good for pair work. Give each pair a written task and a time limit.

### Pitfalls

- **Generated passwords on screen.** The install summary shows the administrator password; do not share that
  terminal.
- **Silence means nothing.** Ask for an explicit "done".
- **Long blocks.** Online attention is shorter; keep blocks to about 45 minutes.

## Hands-on exercise

1. Take one lesson of Module 2 and rewrite its timing for online delivery: 45-minute blocks, check-ins after the key
   steps.
2. Write the three check-in questions you will ask (for example "Do you see the 'Dry-run completed' line?").
3. Prepare your screen: a browser window with only the training site and the Help center; a terminal without
   passwords in its scroll-back.
4. Run the first five minutes of the lesson with a partner over a video call and share one window.
5. Ask the partner what they saw that should not have been visible.

## Expected result

- An online version of a lesson with check-ins and 45-minute blocks.
- The partner saw no password, no personal data and no other window.

## Quiz

1. Why is a lab site served at `127.0.0.1` private to the learner?
2. Which Velocity engine must not be exposed to the internet for a shared online lab?
3. What replaces walking the room online?
4. What do you share: the desktop or one window?

<details>
<summary>Answers</summary>

1. It is bound to the local machine and reachable only from it.
2. The `php` engine (PHP's built-in server), which is for development only.
3. Explicit check-ins ("done" or "stuck") after key steps.
4. One window.

</details>

## Further reading

- [Live demo safety](../../../advocates/live-demo-safety.md)
- [Running an online meetup](../../../advocates/running-an-online-meetup.md)
- [Serving the site](../../../../install/08-serving-the-site.md)

## Related pages

- [Module 3: Designing a course](README.md)
- [Train the trainer: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
