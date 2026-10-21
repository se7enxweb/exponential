---
title: Asking a good question
audience: community, user, admin, developer, editor
category: community
order: 14
summary: How to ask a question in the Exponential forums or chats so that someone can answer it in one reply - search first, describe the goal, include the environment from the system card, show what you tried, and close the loop.
keywords: question, ask, forums, Telegram, help, environment, system information, exp:info, version, PHP version, template, reproducible
type: help
---
# Asking a good question

The people who answer in the forums and chats give their own time. A question that contains everything needed gets
answered sooner and better, and helps the next person who searches for the same thing.

## Contents

1. [Before you ask](#before-you-ask)
2. [What to include](#what-to-include)
3. [Getting the environment](#getting-the-environment)
4. [A template](#a-template)
5. [After the answer](#after-the-answer)

## Before you ask

1. **Search the help** in your administration (the "?") and this documentation.
2. **Search the forums**, the [documentation](https://exponential.doc.exponential.earth/Exponential) and
   [eZpedia](https://ezpedia.exponential.earth/); the help lists these sources under "Asking for help" in
   [When something goes wrong](../../when-something-goes-wrong.md).
3. **Check that it is a question, not a bug.** If the software clearly does something wrong, report it instead
   ([Filing a bug](../contributing/filing-a-bug.md)).
4. **Check that it is not a security problem.** Those go privately to security@se7enx.com (`SECURITY.md`).

## What to include

| Part | Why |
|---|---|
| **The goal**: what you are trying to achieve | Often there is a better way than the one you are stuck on |
| **What you did**, step by step | So someone can repeat it |
| **What you expected and what happened**, with the exact message | An exact message can be searched in the code |
| **The environment**: Exponential version, PHP version, web server or engine, database, design | Many answers depend on them |
| **What you already tried** | Saves everyone a round |
| **Relevant settings or template code**, shortened | Only the part that matters, without passwords |

## Getting the environment

You do not need to collect the environment by hand:

- In the administration, **Setup** > **System information** has **Copy report as text**.
- On the command line, `./console exp:info --plain --no-logo` prints a summary that "never prints a password, key,
  token, database user or full database address, so you can paste it as it is"
  ([When something goes wrong](../../when-something-goes-wrong.md), [The system card](../../../features/6.0/system-card.md)).

## A template

```text
Goal: <what you want to achieve>
Steps: 1. ... 2. ... 3. ...
Expected: <...>
Happened: <...> (exact message: "...")
Environment: <paste of exp:info --plain, or version / PHP / engine / database>
Tried: <...>
```

Choose a title that names the problem: "Multilingual URL aliases missing after adding a language" is answered
sooner than "Help please".

## After the answer

- **Say whether it worked.** A thread without an ending leaves the next reader guessing.
- **Post your own solution** if you found it elsewhere.
- **Thank the people who helped.**
- **Write it up** on eZpedia or in a forum post if the answer was hard to find
  ([Writing tutorials and posts](../helping-others/writing-tutorials-and-posts.md)).

## Related pages

- [Joining the community](README.md)
- [Getting help step by step](getting-help-step-by-step.md)
- [When something goes wrong](../../when-something-goes-wrong.md)
- [Answering questions](../helping-others/answering-questions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
