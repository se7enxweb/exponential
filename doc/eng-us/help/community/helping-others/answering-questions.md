---
title: Answering questions in the community
audience: community, advocate, supporter, developer, admin
category: community
order: 41
summary: How to answer questions in the Exponential forums and chats - understand the goal, ask for the environment, answer with a source, check AI-generated answers, protect privacy, send security problems to the private channel, and close the loop.
keywords: answering, answer, forums, Telegram, support, help, sources, accuracy, AI answers, privacy, security, link to help
type: help
---
# Answering questions in the community

A good answer solves the problem, explains why, and points to a source the person can come back to.

## Contents

1. [Before you answer](#before-you-answer)
2. [The answer](#the-answer)
3. [Things to avoid](#things-to-avoid)
4. [Special cases](#special-cases)
5. [Answer patterns](#answer-patterns)

## Before you answer

- **Understand the goal**, not only the error. Ask "What are you trying to achieve?" if it is unclear.
- **Ask for the environment** if it matters: the output of `./console exp:info --plain --no-logo` or **Setup** >
  **System information** > **Copy report as text** is safe to share
  ([Asking a good question](../joining/asking-a-good-question.md)).
- **Check whether you know.** If you are guessing, say so.

## The answer

1. Give the short answer first.
2. Give the steps, numbered.
3. Link the help page or the documentation the answer comes from
   ([Pointing to the right help page](../../advocates/pointing-to-the-right-help-page.md)).
4. Say which version you checked it on, if versions differ.
5. Ask them to confirm whether it worked.

## Things to avoid

| Avoid | Why |
|---|---|
| "Read the manual" without a link | It helps nobody |
| Asking for passwords or database dumps | Never in a public place ([When something goes wrong](../../when-something-goes-wrong.md)) |
| Posting a generated answer as fact | The code of conduct: "Do not post generated answers as facts. Check them against the software and its documentation first, or say that they are unchecked" |
| Offering your paid services as the answer | "Use community spaces to help, not to advertise" (`CODE_OF_CONDUCT.md`) |
| Promising a fix or a release date | Only maintainers can, and the sources give no dates |

## Special cases

| The question is | Do |
|---|---|
| A security problem | Stop the public discussion and point to security@se7enx.com (`SECURITY.md`); [Security reports stay private](../../advocates/security-reports-stay-private.md) |
| A bug | Help them file it ([Filing a bug](../contributing/filing-a-bug.md)) |
| About a paid product or contract | Point to 7x or the partner concerned |
| Hostile | Stay calm; do not escalate ([Handling hostile threads](../../advocates/handling-hostile-threads.md)) |
| Containing personal data | Ask them to remove it; tell a moderator |

## Answer patterns

```text
Short answer: <one sentence>.
Steps:
1. ...
2. ...
Source: <link to the help page or documentation>.
Checked on: Exponential <version>, <setup>.
Did that solve it?
```

```text
I'm not sure, but this is where I would look: <page>. If it does not help, please post the output of
./console exp:info --plain --no-logo (it leaves out passwords and keys) so others can see your setup.
```

## Related pages

- [Helping others in the community](README.md)
- [Answering questions well](../../advocates/answering-questions-well.md)
- [Responding in public](../../advocates/responding-in-public.md)
- [Working in the forums](../../advocates/working-in-the-forums.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
