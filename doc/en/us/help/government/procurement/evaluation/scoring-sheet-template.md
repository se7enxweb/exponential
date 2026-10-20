---
title: Scoring sheet template for CMS bids
audience: government, project-manager, sales-marketing
category: public-sector
order: 30
summary: A scoring sheet template for comparing content management bids, with a score scale, evidence rules, a consensus step and a worked layout; every weight and score is a blank for your panel to fill.
keywords: scoring sheet, scoring matrix, bid scoring, evaluation score, consensus scoring, weighted score, tender, procurement, template
type: help
---
# Scoring sheet template for CMS bids

This page is for the procurement officer who sets up the scoring of a tender for a website or intranet content
management system, and for the evaluators who fill it in. It gives a scoring scale, rules that keep scores
defensible, and a sheet layout. The weights are blanks: they come from your tender documents, never from this page.

> **Template** - adapt to your procurement rules; not legal advice.

## Contents
1. [Before you score](#before-you-score)
2. [A score scale with evidence rules](#a-score-scale-with-evidence-rules)
3. [The sheet layout](#the-sheet-layout)
4. [Individual and consensus scoring](#individual-and-consensus-scoring)
5. [Scoring status words from vendor documentation](#scoring-status-words-from-vendor-documentation)
6. [Pitfalls](#pitfalls)

## Before you score

- The criteria and their weights must be the ones published in the tender. Most procurement rules forbid adding a
  criterion after bids are opened.
- Mandatory requirements (pass or fail) are checked first, with the evaluation checklist template of this folder.
  Only bids that pass are scored.
- Each evaluator declares conflicts of interest before seeing the bids.
- Agree in advance which evidence counts: the written bid, the demo, the proof of concept, the public documentation.
  For Exponential, the public documentation is the `doc/` directory of the repository, for example the
  [Accessibility standards](../../../../standards/accessibility.md) and [Security standards](../../../../standards/security.md).

## A score scale with evidence rules

| Score | Meaning | Evidence needed |
|---|---|---|
| 0 | Not addressed, or contradicted by evidence | none, or evidence that contradicts the claim |
| 1 | Claimed, no evidence | a statement in the bid only |
| 2 | Partly met, with evidence | documentation or demo shows part of it; gaps named |
| 3 | Met, with evidence | documentation and demo or proof of concept show it |
| 4 | Met, with independent or repeatable evidence | the panel reproduced it (proof of concept, test run) or an independent report exists |

Adapt the number of steps to your rules. What matters is that each step names the evidence it needs, so two
evaluators reach the same score for the same facts.

## The sheet layout

```text
Tender <ref>                    Bid <bidder> / Exponential <version>          Evaluator <initials>

Criterion                       Weight   Score (0-4)   Weighted   Evidence (link, demo minute, PoC step)
------------------------------  -------  ------------  ---------  -------------------------------------
1 Editorial functions           <w1>     <s>           <w1*s>     ...
2 Accessibility                 <w2>     <s>           <w2*s>     doc/eng-us/standards/accessibility.md; demo ...
3 Security                      <w3>     <s>           <w3*s>     doc/eng-us/standards/security.md; PoC step ...
4 Privacy and data protection   <w4>     <s>           <w4*s>     doc/eng-us/standards/privacy.md
5 Languages                     <w5>     <s>           <w5*s>     ...
6 Hosting and operations        <w6>     <s>           <w6*s>     ...
7 Support and maintenance       <w7>     <s>           <w7*s>     the support offer
8 Training and documentation    <w8>     <s>           <w8*s>     ...
9 Exit and portability          <w9>     <s>           <w9*s>     doc/eng-us/install/migration/leaving-exponential.md
10 Price / total cost           <w10>    <s>           <w10*s>    the TCO worksheet
                                -------                ---------
Total                           <100>                  <sum>
```

Price is often scored by a formula set in the tender rather than on the 0 to 4 scale; follow your rules. The total
cost of ownership worksheet in the sibling folder total-cost-of-ownership helps you compare prices on the same basis.

## Individual and consensus scoring

1. **Individual scoring.** Each evaluator scores alone and writes the evidence for every score.
2. **Moderation meeting.** A moderator, who does not score, goes through the criteria one by one. Where scores differ
   by more than one step, the evaluators explain their evidence.
3. **Consensus score.** The panel agrees one score per criterion and records the reason. Do not average: an average
   hides the disagreement a debrief will ask about.
4. **Sign-off.** Every evaluator signs the consensus sheet.

## Scoring status words from vendor documentation

The Exponential standards pages use fixed status words. A fair way to translate them to the scale above:

| Status in `doc/eng-us/standards/` | Score it as at most | Why |
|---|---|---|
| **Complies** | 3, or 4 when you reproduced it | the page names the evidence (tests, settings, files) |
| **Partly complies** | 2 | the page names what is covered and the gaps |
| **Unverified** | 1 | no assessment was made; for example EN 301 549, Section 508 and OWASP ASVS are "Unverified" |
| **Does not comply** | 0 | stated plainly, for example unsigned commits in [Source control and releases](../../../../standards/source-control-and-releases.md) |

A bidder may lift a score with its own evidence, for example an accessibility audit of the public design it builds
for you. Score that evidence, not the product documentation alone.

## Pitfalls

- Scoring the demo's polish instead of the requirement. Write down which requirement each demo minute showed.
- Rewarding claims of "full compliance" that no document backs. The Exponential documentation itself claims no
  WCAG conformance and no VPAT ([Accessibility standards](../../../../standards/accessibility.md#section-508)).
- Letting the price evaluator see quality scores first, or the other way round, when your rules separate them.
- Losing the evidence links: keep them in the sheet.

## Related pages
- [Evaluation: overview](README.md)
- [Procurement: overview](../README.md)
- [Standards: how to read the status](../../../../standards/README.md#how-to-read-the-status)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
