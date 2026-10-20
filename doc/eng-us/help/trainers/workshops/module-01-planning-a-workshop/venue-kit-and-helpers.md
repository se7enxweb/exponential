---
title: "Lesson 1.2: Venue, kit and helpers"
audience: trainer
category: training
order: 12
summary: Prepare the room and the equipment for a hands-on workshop, network, power, projector, spare machines, printed quick cards, one helper per eight to ten learners, and an accessible room. 30 minutes.
keywords: venue, room, kit, equipment, network, projector, power, spare laptops, helpers, accessibility, workshop logistics
type: help
---
# Lesson 1.2: Venue, kit and helpers

A workshop can fail on logistics alone: no power sockets, a network that blocks Composer, a projector nobody can
read. This lesson is a planning walk-through of the room, the kit and the people a hands-on Exponential workshop needs.

## At a glance

| | |
|---|---|
| Course | [Workshops and bootcamps](../README.md) |
| Module | [Module 1: Planning a workshop](README.md) |
| Duration | 30 minutes (10 presenting, 20 exercise) |
| Level | Beginner |
| Audience | Trainers and organizers |
| Prerequisites | [Lesson 1.1](choosing-a-format.md) |

## Learning objectives

After this lesson, learners can:

- list the room, network and equipment needs of a hands-on workshop;
- plan an offline fallback for code and packages;
- staff the workshop with one helper per eight to ten learners;
- check the room for accessibility.

## Trainer's notes

### How to present it

1. Room (3 minutes): a power socket per learner, tables for laptops, a screen everyone can read, step-free access,
   quiet space for breaks.
2. Network (3 minutes): installs fetch code from GitHub and Packagist and site packages from the package index;
   test the venue network in advance or prepare the code and packages on each machine
   ([Lesson 2.8 of Train the trainer](../../train-the-trainer/module-02-building-the-lab/choosing-the-site-package-for-class.md)
   covers the package index and offline installs).
3. Kit and helpers (4 minutes): spare laptops with a prepared installation; printed
   [Lab quick card](../../lab-quick-card.md); one helper per eight to ten learners, as the advocates' workshop outline
   recommends.

### Common questions

**What if the network blocks GitHub?** Prepare the code in advance (USB drive or pre-installed machines).

**Online instead?** See [Lesson 3.6 of Train the trainer](../../train-the-trainer/module-03-designing-a-course/online-and-hybrid-delivery.md).

### Pitfalls

- **Testing the network on the day.** Test it a week before.
- **Too few helpers.** Installs take the longest with too few helpers.

## Hands-on exercise

Write the logistics plan for a one-day workshop with 16 learners.

1. Room: list five requirements and how you will check each.
2. Network: write the test you will run in the venue a week before (for example, `git clone` the repository and
   `composer create-project` a release on a laptop connected to the venue network).
3. Fallback: describe what you prepare if the test fails.
4. Kit: list the equipment, including two spare laptops with a prepared installation.
5. People: how many helpers, and what each one needs (the lab quick card, a learner list).

## Expected result

- A one-page plan with room, network test, fallback, kit and two helpers for 16 learners.

## Quiz

1. How many helpers for 16 learners?
2. Why test the venue network a week before?
3. What is a good fallback when installs cannot reach the internet?
4. Name two accessibility checks of a room.

<details>
<summary>Answers</summary>

1. Two (one per eight to ten learners; [Hands-on workshop outline](../../../advocates/hands-on-workshop-outline.md)).
2. Installs fetch code and packages from the internet; a blocked network stops the workshop.
3. Code and packages prepared on each machine, or spare machines with a prepared installation.
4. Step-free access; a screen readable from every seat (also quiet space, seating near the screen).

</details>

## Further reading

- [Hands-on workshop outline](../../../advocates/hands-on-workshop-outline.md)
- [Event checklists](../../../advocates/event-checklists.md)
- [Trainer toolkit](../../trainer-toolkit.md)

## Related pages

- [Module 1: Planning a workshop](README.md)
- [Workshops and bootcamps: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
