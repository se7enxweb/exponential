---
title: Web and history tools
audience: ai-agents, admin, editor
category: ai-agents
order: 34
summary: The three read-only tools of Exponential Intelligence that look outside the content tree - web_fetch for pages of allowlisted sites, web_search through a search service, and project_history for the team's own commits and release notes - with the policy expai/web, their safeguards (allowlist, SSRF guard, robots.txt, caps), the data policy for searches, and why no draft is written after web text.
keywords: web_fetch, web_search, project_history, web tools, expai/web, AllowedDomains, SSRF guard, robots.txt, Brave Search, outside data, write lock, git history, release notes
type: help
---
# Web and history tools

This page is for administrators who decide whether the assistant may look beyond the site, and for editors who use
it. Three read-only tools bring outside text into a conversation. Each is offered only to users with the policy
`expai/web` and only once configured ([extension documentation: tools](https://github.com/se7enxweb/expai/blob/main/doc/tools.md)).

## Contents

1. [The three tools](#the-three-tools)
2. [web_fetch](#web_fetch)
3. [web_search](#web_search)
4. [project_history](#project_history)
5. [No drafts after web text](#no-drafts-after-web-text)
6. [Who gets them](#who-gets-them)

## The three tools

| Tool | Reads | Sends out |
|---|---|---|
| `web_fetch` | One page of an allowlisted site, as Markdown | Only the URL |
| `web_search` | Search results from a search service | The query |
| `project_history` | Commits of local clones and public release notes | Nothing about the content |

Every result says it is data, not instructions. The usage log records the host the tool read, never the URL or the
query.

## web_fetch

- Only hosts on `[WebSettings] AllowedDomains[]` (an exact host or `*.domain`), compared after lower-casing and
  punycode, so look-alike hosts do not match.
- An **SSRF guard**: the host is resolved once and refused if any address is loopback, private, link-local (including
  the cloud metadata address), carrier-grade NAT, unspecified or multicast; the connection is pinned to the checked
  address; redirects only to the same host and checked again; only ports 80 and 443 by default.
- Respects robots.txt; caps on size, time and the number of fetches per turn (5 by default); text types only.
- Nothing but the URL is sent: no cookies, credentials, referrer or body. A URL can still carry text in its query, so
  keep the allowlist to sites you trust.
- Page text is wrapped in "Begin/End of outside data" markers.

## web_search

- The service is configured in `[SearchSettings] Provider` (Brave Search is the shipped one); the tool is offered only
  when its key is set in `settings/override`.
- The query leaves the site, so the panel's node is checked against the search service's `Region` and `Retention`
  first, as the data policy requires, and the tool description tells the model never to put site content into a
  query ([Privacy and data leaving the site](../governance/privacy-and-data-leaving-the-site.md)).
- At most 3 searches per turn by default.

## project_history

- Reads the team's own repositories: commits from local clones under `[HistorySettings] Roots[]`, and public release
  notes.
- git runs without a shell, with a fixed format and literal paths, only `log`, `ls-files` and `rev-parse`, in a clean
  environment, with time and size limits.
- Excluded paths and untracked files are never named or read; author names are off by default.
- Useful for questions such as "what changed in the newsletter extension this month?" when writing release news.

## No drafts after web text

After a `web_fetch` or `web_search` result, **no draft is written while that result is part of the conversation**,
until a new conversation starts. The model tells the editor what it found; to write from it, the editor starts a new
conversation with what is needed. `project_history` (the team's own committed work) does not stop writing.

This is the assistant's main defense against instructions hidden in web pages
([Untrusted data and prompt injection](../agentic-workflows/untrusted-data-and-prompt-injection.md)). Check links in
drafts written from web results before publishing.

## Who gets them

The policy `expai/web` with the limitation **Tool** (`web_fetch`, `web_search`, `project_history`); without the
limitation, all three ([Permissions for AI features](permissions-for-ai-features.md)). EI: Summary lists the allowed
domains, the search service with its region, retention and key source, and the history repositories.

## Related pages

- [What Exponential Intelligence does](what-exponential-intelligence-does.md)
- [Service tools](service-tools.md)
- [Reviewing AI drafts](../checklists/reviewing-ai-drafts.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
