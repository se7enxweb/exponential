---
title: "Lesson 1.5: Names and history in class"
audience: trainer
category: training
order: 15
summary: Introduce Exponential correctly in the first minutes of a course, the product name, the product family, 7x and the Exponential Foundation, and the old name only as past-tense history, with a two-minute opening you can rehearse. 20 minutes.
keywords: naming, brand, history, Exponential, 7x, Exponential Foundation, product family, Velocity, Exponential Intelligence, opening, introduction
type: help
---
# Lesson 1.5: Names and history in class

The first five minutes of a course set the tone. Learners ask "what is this, and who makes it?", and a trainer who
answers with the wrong names or an invented history loses trust. This lesson gives trainers the facts and the words
for the opening of any course.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 1: Preparing to teach Exponential](README.md) |
| Duration | 20 minutes (8 presenting, 12 exercise) |
| Level | Beginner |
| Audience | Trainers |
| Prerequisites | [Lesson 1.1](how-these-courses-work.md) |

## Learning objectives

After this lesson, learners can:

- name the product, the company and the nonprofit correctly;
- refer to the old product name only in the past tense, as history;
- leave code identifiers such as `eZContentObject` or `bin/php/ezcache.php` unchanged when they appear on a slide;
- give a two-minute introduction of Exponential with a source for every fact.

## Trainer's notes

### How to present it

1. Show the naming card from [Naming rules quick card](../../../advocates/naming-rules-quick-card.md) (3 minutes).
   The product is **Exponential**; 7x is the company that leads it; the **Exponential Foundation** is the nonprofit
   that operates and funds the project.
2. Explain the history in one breath (2 minutes): the code has been developed since 1999; for many years it was called
   eZ Publish, a product its creator discontinued after almost eighteen years; 7x took over leadership in 2023 and
   has released Exponential 6 since January 2024. Source:
   [The story of Exponential in two minutes](../../../advocates/the-story-of-exponential-in-two-minutes.md).
3. Explain why code keeps the old prefix (3 minutes): class names like `eZContentObject`, `eZINI` and file names like
   `bin/php/ezcache.php` are real identifiers. Learners will type them; renaming them on a slide would make the
   exercise fail.

### Common questions

**Why do some commands start with `ez`?** They are the real script names, kept for compatibility. Newer commands
use the `exp:` namespace of the console (`php bin/php/console exp:install --apply`).

**Is Exponential related to eZ Systems or Ibexa?** No. Exponential is not associated with them
([Brand and naming rules](../../../business/brand-and-naming-rules.md)).

**Who do I contact about the project?** Point to [Who is 7x?](../../../who-is-7x.md) and
[Who is the Exponential Foundation?](../../../who-is-the-exponential-foundation.md).

### Pitfalls

- **Invented figures.** Do not quote numbers of sites or customers that no source gives.
- **"Version 6 of eZ Publish".** Wrong: the current line is Exponential 6.
- **Renaming code on slides.** Keep `eZ` in identifiers exactly as the code has it.

## Hands-on exercise

1. Open [The story of Exponential in two minutes](../../../advocates/the-story-of-exponential-in-two-minutes.md) and
   read the 30-second version aloud once.
2. Write your own two-minute opening for a course. It must name: the product, what it is, 7x, the Exponential
   Foundation, the license ("free, open source, GNU GPL" for the code) and the version your learners use.
3. For every fact, note the help page that supports it.
4. Find the version of your training installation: sign in to the administration; the dashboard's **System** panel
   shows it in the line **Version** (for example `6.0.15stable`).
5. Do the "Spot the mistake" exercise on [Naming rules quick card](../../../advocates/naming-rules-quick-card.md)
   without looking at the answers, then check them.
6. Present your opening to a partner. The partner checks every name against the card.

## Expected result

- A written two-minute opening in which every fact has a source page.
- The old name appears only in the past tense, and no code identifier is renamed.
- The partner has found no naming mistake, or the speaker has corrected the ones found.

## Quiz

1. What is the name of the nonprofit that operates the project?
2. Rewrite correctly: "eZ Publish 6 is installed on our server."
3. A slide shows `bin/php/expcache.php`. What is wrong?
4. Since when has 7x released Exponential 6?

<details>
<summary>Answers</summary>

1. The Exponential Foundation.
2. "Exponential 6 is installed on our server."
3. The script is `bin/php/ezcache.php`; file names and code identifiers keep their real names.
4. Since January 2024 ([The story of Exponential in two minutes](../../../advocates/the-story-of-exponential-in-two-minutes.md)).

</details>

## Further reading

- [Brand and naming rules](../../../business/brand-and-naming-rules.md): the full rules.
- [Who is 7x?](../../../who-is-7x.md) and [Who is the Exponential Foundation?](../../../who-is-the-exponential-foundation.md).
- [Knowing the product family](../../../advocates/knowing-the-product-family.md): every Exponential product in a sentence.

## Related pages

- [Module 1: Preparing to teach Exponential](README.md)
- [Train the trainer: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
