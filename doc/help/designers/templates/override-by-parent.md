---
title: Override by parent
audience: designer, developer
category: templates
order: 620
summary: Templates that depend on where content sits: matching the parent's class (parent_class_identifier), the parent's remote id, the parent node, a URL subtree (url_alias prefix) or the depth, with examples for blog posts, news and top-level pages.
keywords: override parent, parent_class_identifier, parent_node, parent_node_remote_id, url_alias subtree, depth, location based template, blog post template, news folder template
type: help
---
# Override by parent

This page is for designers who want content to look different depending on where it is placed: an article under a blog,
items in the news folder, top-level pages.

## Contents

1. [By the parent's class](#by-the-parents-class)
2. [By a specific parent](#by-a-specific-parent)
3. [By a subtree](#by-a-subtree)
4. [By depth](#by-depth)

## By the parent's class

```ini
[mytheme_line_post_in_blog]
Source=node/view/line.tpl
MatchFile=line/blog_post_in_blog.tpl
Subdir=templates
Match[class_identifier]=blog_post
Match[parent_class_identifier]=blog
Priority=10
```

Portable: works wherever blogs are placed.

## By a specific parent

`Match[parent_node_remote_id]=mytheme-news-folder` (portable) or `Match[parent_node]=61` (this installation only).
Matches only the direct children.

## By a subtree

`Match[url_alias]=news` matches every page whose URL alias starts with `news`: the whole News area at any depth.
Note that it follows the URL, so renaming the folder breaks the rule; a section is the more robust tool for a large
area ([Override by section](override-by-section.md)).

## By depth

`Match[depth]=2` matches pages directly under the root (the root node of the tree has depth 1); handy for a different
template for top-level pages.

## Related pages

- [Override match keys](override-match-keys.md)
- [node_view_gui](node-view-gui.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
