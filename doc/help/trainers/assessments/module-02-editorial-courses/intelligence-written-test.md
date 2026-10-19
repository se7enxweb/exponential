---
title: "Lesson 2.5: Exponential Intelligence, written test"
audience: trainer, editor, admin
category: training
order: 25
summary: A twelve-question written test for the end of the Exponential Intelligence course, on drafts and review, permissions, the edit form actions, alternative text, data policy, audit, costs and switching the assistant on, with an answer key that cites the documentation. 25 minutes for learners.
keywords: assessments and practical exercises, exponential intelligence, expai, written test, writing assistant, drafts, review, permissions, data policy, audit, cost, alt text, EI Summary
type: help
---
# Lesson 2.5: Exponential Intelligence, written test

This test closes the Exponential Intelligence course. Its center is one idea: the assistant writes **drafts**, as the
signed-in editor and with that editor's permissions, and never publishes on its own. Around it, the test checks the
editor's actions in the form, and for administrators the data policy, the audit and the switch.

> **Note**
> This is the trainer's own assessment, not an official certification. Questions 1 to 8 are for every learner;
> questions 9 to 12 are for administrators. Give editors only the first part if the course was for editors alone.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 2: Assessing the editorial courses](README.md) |
| Duration | 40 minutes (5 briefing, 25 test, 10 going through the answers) |
| Level | Intermediate |
| Audience | Learners at the end of the Exponential Intelligence course |
| Prerequisites | The Exponential Intelligence course; [Lesson 1.2](../module-01-designing-assessments/writing-written-tests.md) for the trainer |

## Learning objectives

Learners who pass this test have shown that they can:

- explain that the assistant writes drafts and never publishes on its own;
- explain whose permissions the assistant uses;
- name the field actions of the edit form and what happens to a suggestion;
- explain what the audit records and what it never records;
- (administrators) name where keys go, the master switch and the summary page for a data protection officer.

## Trainer's notes

### How to run it

1. Hand out the questions without the key; 25 minutes (15 for the editors' part alone).
2. Go through the answers with [expai: Exponential Intelligence](../../../../features/6.0/extensions/expai.md) and
   [Creating and editing content](../../../editing-content.md#exponential-intelligence) on the projector.
3. Score with the rule under **Scoring**.

### Common questions

**Our installation uses a local model.** The questions hold for every provider. Question 7 asks about the line that
shows the model and the cost; with a local model the cost may be shown as none.

**Are prompts stored anywhere?** The audit records every request, draft and state change, but never prompts, answers
or keys. Read the extension's own documentation for the usage log and retention per lane.

### Pitfalls

- **"The AI published it."** Learners sometimes say so. Correct it every time: the editor publishes, or a workflow
  does after approval.
- **Provider names in questions.** Do not ask which provider is best; it is not documented and changes.

## The test

1. True or false, and why: the assistant can publish a new article on its own.
2. An editor may edit only one folder. The editor asks the assistant to change an article in another folder. What
   happens, and why?
3. Name three actions offered on a text field of the edit form.
4. You press **Rewrite** on the intro. What happens to the published article at that moment?
5. In the **Intelligence** tab, what do **Save as drafts** and **Send for approval** each do?
6. What can the assistant suggest for images, and why does it matter?
7. What does the box at the top of the edit form tell you before you click an action?
8. Your site has an approval workflow. You use **Send for approval**. Where is the version now?
9. (Administrators) Where does the provider key go, and what must never happen to that file?
10. (Administrators) Which setting switches the assistant off for the whole site?
11. (Administrators) What does the audit record about the assistant, and what does it never record?
12. (Administrators) Your data protection officer asks which providers are used, in which region and with which
    retention. Which page do you print?

<details>
<summary>Answers</summary>

1. **False.** It writes drafts, as the signed-in editor, and never publishes on its own
   ([expai: Exponential Intelligence](../../../../features/6.0/extensions/expai.md#in-short)).
2. It cannot: the assistant works with that editor's permissions
   ([expai: Exponential Intelligence](../../../../features/6.0/extensions/expai.md#in-short)).
3. Three of **Write**, **Rewrite**, **Shorten**, **Translate**, **Suggest**, and the **Improve** menu
   ([Creating and editing content](../../../editing-content.md#exponential-intelligence),
   [expai](../../../../features/6.0/extensions/expai.md#for-editors)).
4. Nothing: suggestions change nothing until you accept them and store the draft, and the draft is published only
   when you publish ([Creating and editing content](../../../editing-content.md#exponential-intelligence)).
5. **Save as drafts** keeps the drafts it wrote; **Send for approval** runs the normal publish operation as the
   editor, so an approval workflow holds the version
   ([expai](../../../../features/6.0/extensions/expai.md#for-editors)).
6. Alternative text from a vision model, and a "Missing alt text" report for a subtree; images need an alternative
   text for people who cannot see them ([expai](../../../../features/6.0/extensions/expai.md#for-editors),
   [Creating and editing content](../../../editing-content.md#relations-and-images)).
7. Which service it uses and what a click costs
   ([Creating and editing content](../../../editing-content.md#exponential-intelligence)).
8. Pending, waiting for approval in the workflow
   ([Publishing, versions and the trash](../../../publishing-and-versions.md#publish)).
9. In `settings/override/expai.ini.append.php` only, readable by few (mode 0640), and never committed
   ([expai](../../../../features/6.0/extensions/expai.md#switch-it-on)).
10. `[AISettings] Enabled`, the master switch; it can also be off per siteaccess
    ([expai](../../../../features/6.0/extensions/expai.md#for-administrators)).
11. Every request, draft and draft state change, as events of the channel `ai`; never prompts, answers or keys
    ([expai](../../../../features/6.0/extensions/expai.md#for-administrators)).
12. **EI: Summary**: providers with region, retention, terms date and key source (never the key), roles, lanes,
    limits and outside tools ([expai](../../../../features/6.0/extensions/expai.md#for-administrators)).

</details>

## Scoring

- One point per question. Editors' part: pass with 6 of 8, and questions 1 and 4 correct. Administrators: pass with
  9 of 12, and questions 1, 9 and 11 correct.
- Give feedback as in [Lesson 1.6](../module-01-designing-assessments/giving-feedback-on-results.md).

## Further reading

- [expai: Exponential Intelligence](../../../../features/6.0/extensions/expai.md): editors, administrators and
  switching it on.
- [Creating and editing content](../../../editing-content.md#exponential-intelligence): the assistant in the edit form.
- [Workflows](../../../../guides/workflows.md): why **Send for approval** leads to a pending version.

## Related pages

- [Lesson 2.6: Exponential Intelligence, practical task](intelligence-practical-task.md)
- [Module 2: Assessing the editorial courses](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
