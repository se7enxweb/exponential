---
title: "Lesson 6.1: Privacy: what goes to the provider, and what never should"
audience: trainer, editor, admin
category: training
order: 61
summary: Learners explain what the assistant sends to a model provider, apply the rule never to put personal data into a request, recognize the data policy's refusals, and rewrite unsafe briefs. 45 minutes.
keywords: exponential intelligence, privacy, personal data, data protection, provider, region, retention, data policy, what is sent, prompts, briefs, images of people, local model
type: help
---
# Lesson 6.1: Privacy: what goes to the provider, and what never should

Every request to a hosted model sends text to a company outside the site. The assistant is built to send as little as
needed, and the site can keep sections and classes away from some providers. The last line of defense is the editor:
**no personal data in a request**. This lesson makes that a habit.

> **Note**
> This lesson describes what the software does. Your organization's data protection rules and your provider contract
> decide what is allowed; ask your data protection contact when in doubt.

## At a glance

| | |
|---|---|
| Course | [Exponential Intelligence](../README.md) |
| Module | [Module 6: Responsible use](README.md) |
| Duration | 45 minutes (25 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Every user of the assistant; administrators |
| Prerequisites | [Module 2](../module-02-the-intelligence-tab/README.md) |

## Learning objectives

After this lesson, learners can:

- list what the Intelligence tab, the edit form and alt text send to the provider, and what is never sent;
- explain region and retention of a provider and where the site records them;
- recognize a data policy refusal and choose an allowed model;
- rewrite a brief so it contains no personal data;
- name when a local model is the right choice.

## Trainer's notes

### How to present it

Sources: the security and data policy pages under **EI: Help** (`extension/expai/doc/security.md`,
`extension/expai/doc/data-policy.md`).

1. **What is sent** (6 minutes):
   - Intelligence tab: the instructions (with the open item's name, class, path and the site's default language), the
     tool definitions, your messages, and the results of the tools the model calls: names and field values of content
     you may read, class definitions. Images are not sent here (only their alt texts); files and user account fields
     never.
   - Edit form: the item's name and class, the field, its value and the other fields' values (about 30 KB at most),
     your instruction.
   - Alt text: the stored image, the page title, the other fields' text and the language.
   - Not sent: your name, login or e-mail, passwords, session data, other users' drafts.
2. **The rule** (4 minutes). Write it on the board: *No personal data in a request.* No names of private persons, no
   addresses, phone numbers, e-mail addresses, customer or patient details, no internal case numbers. Content that is
   already public is a different matter than a customer's letter pasted into a brief.
3. **Region and retention** (5 minutes). Each provider is configured with a region (`us`, `eu`, `local`) and a
   retention (none, zero data retention, a number of days, unknown); **EI: Summary** lists them. `local` means the data
   never leaves the server.
4. **The data policy** (5 minutes). The site can require regions or zero retention per section and class. The panel
   then refuses: *The data policy keeps "<name>" (object <id>) away from <provider>: ...* and the editor chooses
   another model. Lists leave withheld items out. Images can have stricter rules.
5. **Images of people** (2 minutes). Never send photos of people to a model without consent.

### Common questions

**Is it safe to ask about a published article?** Its text is public already; the provider still receives it. Follow
your organization's rules for the provider.

**Can I paste a customer e-mail and ask for a reply draft?** Not with personal data in it. Remove names and details
first, or do not use the assistant for it.

**Does the provider train on our content?** That depends on the provider's terms; the administrator records retention
and the date the terms were checked.

### Pitfalls

- **"Just this once."** Personal data in a brief is sent at once; it cannot be called back.
- **Pasted documents.** Long pastes carry signatures, footers and addresses. Clean them first.
- **Assuming the data policy covers everything.** It covers content in the tree by section and class, not what you type.

## Hands-on exercise

Work on paper first, then in the training installation.

1. The trainer projects five briefs. For each, write whether it is safe and, if not, a safe version:
   - `Write a thank-you note to Maria Example from 12 Sample Street for her complaint about invoice 4711.`
   - `Summarize the published article on our opening hours for the newsletter.`
   - `Rewrite this patient's question in plain language: ...`
   - `Write three FAQ answers about returning products, general, no names.`
   - `Here is our customer list, write a personal greeting to each.`
2. Compare your answers with a neighbor.
3. Open **EI: Summary** if your role has it (or the trainer shows it): read the region and retention of the provider
   your training model uses.
4. If the trainer set a data policy on a training section, open an item there and try a request with a model outside
   the allowed region: read the refusal and choose an allowed model.

## Expected result

- Safe: briefs 2 and 4. Unsafe: 1, 3 and 5; safe versions remove every personal detail (or the task is not given to
  the assistant at all).
- The learner knows the region and retention of the training provider.
- Where shown, the refusal named the item, the provider and the required regions.
- The trainer checks the rewritten briefs of two learners.

## Quiz

1. Name three things the Intelligence tab sends to the provider.
2. Name three things it never sends.
3. What does region `local` mean?
4. A request is refused by the data policy. What do you do?
   A. Try again B. Choose a model whose provider is allowed C. Switch the data policy off D. Paste the text elsewhere
5. Rewrite safely: `Draft a reply to Jan Example about his late order 5512.`

<details>
<summary>Answers</summary>

1. Any three of: instructions with the item's name, class and path; tool definitions; your messages; tool results
   with content you may read; class definitions.
2. Any three of: your name, login or e-mail; passwords; session data; other users' drafts; files; user account fields.
3. The model runs on the server; the data never leaves it.
4. **B.**
5. For example: `Draft a general, friendly reply template for a customer whose order is late, without names or order
   numbers.`

</details>

## Further reading

- [Privacy standards](../../../../standards/privacy.md): the project's privacy statement.
- The security, data policy and summary pages under **EI: Help**.

## Related pages

- [Module 6: Responsible use](README.md)
- [Exponential Intelligence: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
