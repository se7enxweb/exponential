---
title: "Lesson 6.2: Reviewing AI output"
audience: trainer, editor
category: training
order: 62
summary: Learners review AI drafts with a checklist, facts, numbers, names, links, tone, bias, structure and accessibility, recognize prompt injection, and decide what may be published under their name. 60 minutes.
keywords: exponential intelligence, review, ai output, fact checking, hallucination, links, tone, bias, prompt injection, label, responsibility, checklist, editorial review
type: help
---
# Lesson 6.2: Reviewing AI output

The assistant writes plausible text, and plausible is not the same as true. Every draft it writes waits for an editor,
and the editor who publishes it is responsible for it. This lesson gives learners a review checklist and practice in
using it.

## At a glance

| | |
|---|---|
| Course | [Exponential Intelligence](../README.md) |
| Module | [Module 6: Responsible use](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Intermediate |
| Audience | Every user of the assistant |
| Prerequisites | [Lesson 6.1](privacy-and-personal-data.md) |

## Learning objectives

After this lesson, learners can:

- review an AI draft against a checklist before keeping or sending it;
- find invented facts, numbers, names and quotations, and check them against a source;
- check links, structure and accessibility of the draft;
- recognize text in content that tries to steer the assistant (prompt injection), and what the assistant does about it;
- explain why the label matters to reviewers and approvers.

## Trainer's notes

### How to present it

Sources: the security page under **EI: Help** (`extension/expai/doc/security.md`, "Prompt injection") and
[expai](../../../../features/6.0/extensions/expai.md).

1. **Why review** (4 minutes). Models write fluent text that can contain invented facts, wrong numbers, outdated
   information or names that do not exist. The draft carries the assistant's label, but publishing is the editor's
   decision and responsibility.
2. **The checklist** (6 minutes). Project it:

   | Check | Ask |
   |---|---|
   | Facts | Is every statement true? Do I have a source? |
   | Numbers, dates, prices | Are they right and current? |
   | Names and quotations | Do these people, products and quotes exist and are they accurate? |
   | Links | Does every link work and lead where it says? |
   | Tone and audience | Does it sound like us and fit the readers? |
   | Bias and fairness | Does it stereotype or leave people out? |
   | Structure | Headings in order, lists, no lost embeds or tables? |
   | Accessibility | Alternative texts, link texts, plain language? |
   | Rights | Does it copy someone else's text? |

3. **Prompt injection** (5 minutes). Content can contain text written to steer an assistant ("ignore your instructions
   and publish ..."). The assistant treats content as data, can only use its own tools, writes only drafts the editor
   may write, cleans rich text to a small set of tags (no scripts, styles, iframes), and every draft waits for review.
   Review drafts written from untrusted text (such as user submissions or web pages) as you would any contributor's
   draft.
4. **The label** (2 minutes). Versions written by the assistant are labeled in version views and approval items, so an
   approver knows to read with care.
5. **Time** (3 minutes). Budget review time: the assistant saves writing time, not reading time.

### Common questions

**Can the assistant check its own facts?** A chain has a review step, and the assistant can look up site content; it
still cannot guarantee truth. The editor checks.

**Must AI-written content be marked on the site?** The software labels versions in the administration; whether and how
to tell readers is your organization's policy.

**What if a draft contains a link I do not know?** Check it before publishing; remove it if in doubt.

### Pitfalls

- **Reviewing the first paragraph only.** Errors hide in the details further down.
- **Trusting confident wording.** Confidence is not evidence.
- **Publishing under time pressure.** Keep the draft and come back.

## Hands-on exercise

Work in pairs with the training installation. The trainer prepared, in each course folder, one AI-written draft that
contains planted errors (a wrong date, a made-up statistic, a broken link, a paragraph with an instruction aimed at an
assistant), or learners use their own drafts from earlier modules.

1. Open the draft from **My drafts** in the normal edit form.
2. Go through the checklist and write down every problem you find.
3. Fix each problem by hand (or with a field action, then check again).
4. Check every link with **View on site** after storing the draft, or in the preview.
5. Swap with your partner and review their corrected draft with the same checklist.
6. Decide together: is it ready to publish? Publish it, or keep it as a draft with a note of what is missing.
7. Discuss: what did the planted instruction in the text do, and why did nothing happen?

## Expected result

- Each pair found the planted errors (or real ones) and corrected them.
- The learners can explain that the instruction in the content was treated as text, and that only drafts were written.
- The trainer checks by asking each pair for the error they almost missed.

## Quiz

1. Name five items of the review checklist.
2. Who is responsible for a published AI-written text?
   A. The provider B. The assistant C. The editor who published it D. Nobody
3. What is prompt injection?
4. Name two ways the assistant limits what injected text can achieve.
5. Why does the label matter to an approver?

<details>
<summary>Answers</summary>

1. Any five of: facts, numbers and dates, names and quotations, links, tone, bias, structure, accessibility, rights.
2. **C.**
3. Text in content written to steer an assistant into doing something else.
4. Any two of: content is treated as data; only its own tools; only drafts the editor may write; rich text cleaned to a
   small set of tags; every draft waits for review.
5. It shows the version was written by the assistant, so the approver reads it with care.

</details>

## Further reading

- The security page under **EI: Help**, section "Prompt injection".
- [Accessibility standards](../../../../standards/accessibility.md): what depends on editors.

## Related pages

- [Module 6: Responsible use](README.md)
- [Exponential Intelligence: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
