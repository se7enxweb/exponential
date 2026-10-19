---
title: "Lesson 2.6: Exponential Intelligence, practical task"
audience: trainer, editor, admin
category: training
order: 26
summary: A 40-minute practical task for the end of the Exponential Intelligence course, improve an article with the edit form actions and review every suggestion, let the assistant write a draft from the Intelligence tab, discard what is not wanted, and for administrators read the EI Summary, scored with a five-criterion rubric. 65 minutes with scoring.
keywords: assessments and practical exercises, exponential intelligence, expai, practical task, rubric, rewrite, improve, alt text, intelligence tab, drafts, review, EI Summary, cost
type: help
---
# Lesson 2.6: Exponential Intelligence, practical task

The skill being assessed is not "using AI"; it is **reviewing** what the assistant wrote before anything reaches the
site. In this task each learner uses the assistant on their own article, keeps what is right, rejects what is not,
and shows the trainer where every draft went.

> **Note**
> This is the trainer's own assessment, not an official certification. Use a training installation with the assistant
> switched on and a provider the trainer controls: a local model, or a provider key with a small budget and limit.
> Never put personal data into prompts, and never use a live site.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 2: Assessing the editorial courses](README.md) |
| Duration | 65 minutes (5 briefing, 40 task, 20 scoring) |
| Level | Intermediate |
| Audience | Learners at the end of the Exponential Intelligence course |
| Prerequisites | [Lesson 2.5](intelligence-written-test.md); an Editor account with the assistant's policies on a training installation |

## Learning objectives

Learners who pass this task have shown that they can:

- use field actions in the edit form and accept or reject each suggestion;
- check facts and tone of a suggestion before storing it;
- let the assistant write a draft from the **Intelligence** tab and find it again;
- discard a draft they do not want;
- (administrators) read the **EI: Summary** page and explain its sections.

## Trainer's notes

### How to run it

1. **Before**: switch the assistant on as described in
   [expai: Exponential Intelligence](../../../../features/6.0/extensions/expai.md#switch-it-on) on the training
   installation: the extension active for the admin siteaccess, the key in `settings/override/expai.ini.append.php`,
   the tables created, the policies `expai/write` and `ezjscore/call` for the learners' role, and the `expqueue` and
   `expai` cron parts run by hand when needed. Set limits per user and per day. Prepare one article per learner with a
   deliberately weak intro and one factual error in the body (for example a wrong opening date), and an image without
   alternative text.
2. **Briefing**: the task is about review. Say that a suggestion stored without checking is scored as an error.
3. **Scoring**: read each learner's article, its versions and the drafts list in the **Intelligence** tab.

### Common questions

**The model is slow or unavailable.** Stop the learner's clock and give the time back, as for any broken lab
([Lesson 1.5](../module-01-designing-assessments/running-an-assessment-session.md)).

**Different learners get different suggestions.** That is expected. The rubric scores the review, not the wording the
model produced.

### Pitfalls

- **Cost surprises.** Watch the cost line at the top of the edit form during a rehearsal and set limits before class.
- **Learners pasting their own data into prompts.** Say before the task: only the training article's text.

## Hands-on exercise

The task, as handed to learners. Use your training installation and your Editor account.

1. Open the article your trainer prepared for you and press **Edit**. Read the box at the top of the form: write down
   which service is used and what a click costs.
2. On **Intro**, use **Improve** (or **Rewrite**). Read the suggestion. Accept it only if it is correct and keeps the
   meaning; otherwise reject it and write the intro yourself.
3. Read the **Body**. It contains one factual error your trainer planted. Use **Shorten** or **Rewrite** on the body if
   you like, but make sure the error is corrected in the version you keep.
4. Press **Store draft and exit**. Do not publish yet.
5. Open the image without alternative text, edit it and let the assistant suggest an alternative text. Correct it if
   needed and publish the image.
6. On your article's page, open the **Intelligence** tab and ask: "Write a short teaser article for the newsletter based
   on this article." Look at the steps it shows.
7. Find the draft it wrote in the list of drafts on the tab. Open it, read it, and decide: keep it as a draft, or
   discard it. Write one sentence why.
8. Go back to your own draft from step 4, check it once more, and publish it.
9. (Administrators only) Open **EI: Summary**. Write down the names of its sections and, for one provider, its region
   and retention.

## Expected result

- The article is published with a correct intro and without the planted error; its **Manage versions** page shows the
  new version.
- The image has a sensible alternative text.
- The **Intelligence** tab lists the teaser draft as kept or discarded, matching the learner's sentence.
- The learner's notes name the service and cost from step 1, and (administrators) the sections of **EI: Summary**:
  Master switch and surfaces, Providers, Lanes and their retention, Data policy, Limits, budget and usage this month,
  Tools that reach outside the site, What this site keeps, Roles with Exponential Intelligence policies.

## Scoring rubric

| Criterion | 0: Not shown | 1: Partly | 2: Mostly | 3: Fully | Evidence |
|---|---|---|---|---|---|
| Review of suggestions (must) | Planted error published | Error found, but a wrong suggestion kept elsewhere | Error fixed, tone or meaning slightly off | Error fixed, every kept suggestion correct | The published article |
| Using field actions | None used | One action, result not checked | Actions used, one suggestion stored without reading | Actions used and each decided deliberately | The learner's account, the versions |
| Alternative text | Missing | Generated, not checked, wrong | Present, vague | Present and describes the image | The image's page |
| Drafts from the tab | No draft made | Draft made, not found | Found, no decision | Found, kept or discarded with a reason | The **Intelligence** tab, the note |
| Cost and policy awareness | Nothing noted | Service only | Service and cost | Service, cost, and (administrators) the summary sections | The learner's notes |

**Pass**: 10 of 15 points or more and **Review of suggestions** at 2 or 3. For editors, score the last criterion on
service and cost only.

## Quiz

Debrief questions to ask after scoring.

1. Who published the article in step 8: you or the assistant?
2. Why was the planted error the most important part of the task?
3. What happened to the published article while you worked in steps 2 to 4?
4. Where would an administrator see that you used the assistant today?

<details>
<summary>Answers</summary>

1. You. The assistant never publishes on its own
   ([expai](../../../../features/6.0/extensions/expai.md#in-short)).
2. A model can write fluent text that is wrong; the editor remains responsible for what is published.
3. Nothing changed until you published; suggestions change nothing until accepted and stored
   ([Creating and editing content](../../../editing-content.md#exponential-intelligence)).
4. In the audit, channel `ai`, and in the usage log, which holds user ids only
   ([expai](../../../../features/6.0/extensions/expai.md#for-administrators)).

</details>

## Further reading

- [expai: Exponential Intelligence](../../../../features/6.0/extensions/expai.md): what to switch on before class.
- [Running an assessment session](../module-01-designing-assessments/running-an-assessment-session.md): broken labs and
  lost time.

## Related pages

- [Lesson 2.5: Exponential Intelligence, written test](intelligence-written-test.md)
- [Module 2: Assessing the editorial courses](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
