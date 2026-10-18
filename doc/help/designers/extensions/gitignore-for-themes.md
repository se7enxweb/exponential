---
title: "A .gitignore for themes"
audience: designer
category: theme-extensions
order: 290
summary: What a theme's .gitignore keeps out of the repository - editor leftovers, backups, logs, build output and local settings - the file the design wizard writes, and how to check that nothing secret or private slipped in.
keywords: gitignore, ignore, backups, editor files, node_modules, vendor, logs, secrets, git status
type: help
---
# A .gitignore for themes

This page is for designers preparing a theme repository. A `.gitignore` keeps files that should never be published
out of every commit.

## What the design wizard writes

```
# Editor leftovers
*~
#*#
.#*
*.orig
*.rej
*.bak
*.swp
*.swo

# Operating system
.DS_Store
Thumbs.db

# Build output and dependencies
/vendor/
/node_modules/
*.log
```

## What to add for a theme

| Add | When |
|---|---|
| `var/` | Your scripts write caches or logs inside the theme |
| `share/*.bak*`, `share/*.backup-*` | You back up `.dba` files next to them (as `sevenx_themes_media` ignores `share/db_data.dba.bak.*`) |
| `/design/*/stylesheets/*.map` | You build CSS from sources and do not ship source maps |
| `.env`, `*.key`, `*.pem` | Never needed in a theme; ignore them so they cannot be committed by accident |

If you compile stylesheets from Sass or another source, decide whether you commit the result. Installations do not
run your build tools, so the compiled CSS normally must be committed.

## Step by step: check before every release

1. `git status --short --ignored` shows what is ignored (`!!`) and what is not.
2. `git ls-files | grep -E '\.(bak|orig|log|key|pem)$|/\.env$'` prints nothing.
3. Search tracked files for secrets: `git grep -n -i -E 'password|secret|api[_-]?key|token' -- settings/` and read
   every match.
4. If something private was committed and pushed, removing it in a new commit does not remove it from the history.
   Change the secret (it is no longer secret) and ask for help before rewriting a published history.

## Related pages

- [The secret check before an extension is packaged](package-secret-check.md)
- [Putting a theme under Git](putting-a-theme-under-git.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
