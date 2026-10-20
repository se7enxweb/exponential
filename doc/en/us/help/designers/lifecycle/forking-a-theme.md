---
title: "Forking a theme"
audience: designer, developer
category: theme-lifecycle
order: 310
summary: When to fork a theme and when to contribute instead, how to fork with history so you can still exchange changes, what the GPL asks of a fork, and how to tell users what your fork is.
keywords: fork, GitHub fork, GPL fork, contribute instead, history, upstream, downstream, rename
type: help
---
# Forking a theme

This page is for designers considering a copy of someone else's theme under their own control.

## Fork or contribute?

| Contribute (pull requests) when | Fork when |
|---|---|
| You need a fix or a feature others would want too | Your goals differ from the original's |
| The maintainer is active and responsive | The original is abandoned ([Adopting an abandoned theme](adopting-an-abandoned-theme.md)) |
| You want to share the maintenance work | You must ship faster than upstream can review |

A fork for a client's private changes is often better solved as a small extension that overrides the original
([Customization and white-label work](../sales/customization-and-white-label.md)).

## Step by step

1. Fork with history (GitHub **Fork**, or `gh repo fork upstreamvendor/theme --clone`), so you can merge from and
   contribute to the original later ([Keeping a fork in sync](keeping-a-fork-in-sync.md)).
2. Keep `LICENSE`, every copyright notice and the authors.
3. If you will publish it, rename it ([Rebranding a fork, fairly](rebranding-a-fork.md)) and give it its own package name.
4. Write at the top of the README: "A fork of <original> by <author>; differences: ...".
5. Send fixes that help everyone back upstream ([Contributing back upstream](contributing-back-upstream.md)).

## What the GPL asks of a fork

That you keep the license and the notices, pass on the source with any copy you distribute, and add no further
restrictions. It does not ask permission to fork.

## Related pages

- [Credits and attribution](credits-and-attribution.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
