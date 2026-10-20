---
title: "Lesson 1.4: Choosing a search backend"
audience: trainer, admin, hosting
category: training
order: 14
summary: Learners weigh the built-in engine against Solr for a given site, using its size, its needs and the cost of running a second server, and write a short recommendation. 25 minutes.
keywords: search course, search backend, choose, built-in search, Solr, ezfind, hosting, cost, requirements, recommendation
type: help
---
# Lesson 1.4: Choosing a search backend

Most sites run well with the built-in engine; some need Solr. The decision is not technical only: Solr is a second
server that someone has to run, monitor and back up. This lesson gives learners a short method to decide and to
explain the decision to a site owner.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Module | [Module 1: How search works](README.md) |
| Duration | 25 minutes (10 presenting, 15 exercise in pairs) |
| Level | Intermediate |
| Audience | Site administrators, project leads, hosting staff |
| Prerequisites | [Lesson 1.2](the-built-in-search-engine.md) and [Lesson 1.3](solr-and-ezfind-at-a-glance.md) |

## Learning objectives

After this lesson, learners can:

- list the questions that decide between the built-in engine and Solr;
- name what running Solr costs in work: installation, memory and disk, monitoring, backups or rebuilds;
- choose a backend for a described site and justify it in three sentences;
- name the three ways a host can offer Solr: none, as an add-on on its own server, or customer-provided.

## Trainer's notes

### How to present it

Source: [Search backends](../../../hosting/basics/search-backends.md#offering-search-in-a-plan) and
[Search with ezfind and Solr](../../../creators/website-creators/search-with-ezfind-and-solr.md#when-solr-is-worth-it).

1. **Five questions** (5 minutes). Write them on the board:
   1. Do visitors need to filter results (by type, tag, date), that is facets? Only Solr has them.
   2. Must the best match come first, with boosting of titles or certain classes? Solr ranks by relevance; the
      built-in engine does not.
   3. How many objects, and how fast does the site grow? The built-in engine slows down with very large numbers.
   4. Who will run the Solr server, watch it and rebuild its index after a failure?
   5. Is the search used much at all? Search statistics (Module 3) answer that once they are switched on.
2. **The cost side** (3 minutes). Solr runs as its own Java service; plan its memory and disk separately from PHP,
   add `exp:solr status` to monitoring, and either back up its index or be able to rebuild it.
3. **Hosting options** (2 minutes). From the hosting guide: built-in search only; Solr as an add-on on a server the
   host runs (`Url` set) or on the customer's dedicated server (`BinaryPath`); or a customer-provided Solr that the
   installation reaches through `Url`.

### Common questions

**Can we start with the built-in engine and move later?** Yes. Switching means installing ezfind, configuring Solr and
reindexing; content does not change. Templates that use Solr-only features (facets) are new work.

**Is Solr faster?** For large indexes and complex queries it is built for it; on a small site the difference is small
and the extra server is not worth it. Measure before deciding (Module 5).

**Does the course recommend one?** No. The documentation says the built-in engine suits most sites; the decision
depends on the answers to the five questions.

### Pitfalls

- **Deciding by feature lists only.** Pairs that tick every Solr feature forget question 4. Ask each pair who will be
  called when Solr is down at night.
- **Inventing numbers.** The documentation gives no object count where the built-in engine stops being enough. Do
  not give one in class; tell learners to measure their own site.

## Hands-on exercise

Work in pairs, on paper or in a text file. No installation is needed.

1. Read the three site descriptions below.
   - **A.** A municipality's site with about 2,000 pages, one editor team, search used by visitors to find forms.
   - **B.** An online magazine with a growing archive, readers who filter by topic and year, and its own operations
     team.
   - **C.** A small association's site on shared hosting, without shell access.
2. For each site, answer the five questions from the board. Write "unknown" where the description does not say.
3. Choose a backend for each site.
4. For each choice write three sentences for the site owner: what you recommend, why, and what it costs to run.
5. For each "unknown", write the question you would ask the owner.
6. Present one site to the group in one minute.

## Expected result

- Each pair has three recommendations with reasons and open questions.
- A reasonable outcome: A with the built-in engine (with search statistics to check later), B with Solr if the team
  can run it (facets by topic and year), C with the built-in engine (no place to run a Solr server). Other answers are
  fine when the reasons hold.
- The trainer checks that every Solr recommendation names who runs the server.

## Quiz

1. Name one feature that only Solr offers and that visitors notice directly.
2. Which tool tells you how much visitors actually use the search?
3. Name three tasks that come with running a Solr server.
4. A host offers Solr on its own server for several customers. Which `solr.ini` key points an installation to it?
   A. `BinaryPath` B. `Url` C. `DataDir` D. `RunDir`
5. True or false: the documentation states a number of objects above which Solr is required.

<details>
<summary>Answers</summary>

1. Facets (filtering results by class, tag or date), or highlighting of the search words.
2. The search statistics (**Setup > Search statistics**, with `LogSearchStats` enabled).
3. Any three of: installing it, planning memory and disk, monitoring with `exp:solr status`, backing up or rebuilding
   the index, upgrading it.
4. **B.** `Url` points to a running Solr managed elsewhere ([Search backends](../../../hosting/basics/search-backends.md)).
5. **False.** It says the built-in engine suits most sites and slows down with very large numbers of objects.

</details>

## Further reading

- [Search backends](../../../hosting/basics/search-backends.md): the host's options and checklist.
- [Search with ezfind and Solr](../../../creators/website-creators/search-with-ezfind-and-solr.md): when Solr is worth
  it.
- [Sizing memory, CPU and disk](../../../hosting/basics/sizing-memory-cpu-and-disk.md): planning the server side.

## Related pages

- [Module 1: How search works](README.md)
- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
