---
title: Publish a poll
audience: government, editor
category: public-sector
order: 500
summary: For editors - publish a quick opinion question with the shipped Poll class, set its options, show results, and present them honestly as indicative; and when a consultation is the better tool.
keywords: poll, Poll class, ezoption, options, vote, results, information collector, collect.ini, unique, indicative, opinion
type: help
---
# Publish a poll

This page is for editors who ask a quick question ("Which day suits you for the clean-up event?"). The shipped **Poll**
class (`poll`) has an option list; each answer is stored as a collection, and `collect.ini` treats polls specially: no
mail per vote, one answer per user, results shown after voting ([Consultations and polls](../../building/forms/consultations-and-polls.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Pitfalls](#pitfalls)

## Before you start

- A poll gives an indication, not a decision; for views on a proposal use a consultation
  ([Publish a consultation and collect responses](publish-a-consultation-and-collect-responses.md)).
- The question has a few clear options.
- Your design shows polls (the ezwebin design has a poll template; others may need one).

## Steps

1. Open the place for the poll, **Create new**, choose **Poll**.
2. Fill the name (the question) and the description.
3. In the option list, add each option with **Add option**; tick empty ones and press **Remove selected**.
4. **Preview**, then **Send for publishing**.
5. Vote once yourself signed out to test, then remove the test answer in **Setup > Collected information**
   ([Remove form responses that are no longer needed](remove-form-responses-that-are-no-longer-needed.md)).

## Check the result

- [ ] Signed out, the poll shows the question and options and accepts a vote.
- [ ] After voting, the result page shows the counts.
- [ ] **Setup > Collected information** lists the poll with its collections.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Presenting results as representative | anonymous visitors share one account, so answers cannot be tied to a person; say "indicative" |
| Leading questions | neutral wording, balanced options |
| Changing options after votes came in | earlier votes refer to the old options; start a new poll |
| Polls left open for ever | hide them when the question is answered |

## Related pages

- [Publishing tasks](README.md)
- [Information collector basics](../../building/forms/information-collector-basics.md)
- [Collected information](../../../../guides/collected-information.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
