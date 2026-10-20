# expai: Exponential Intelligence

Read this page to decide whether to switch on Exponential Intelligence, the writing assistant of the administration
interface, and to find its own documentation. The extension is the Composer package `se7enxweb/expai`, required by
`composer.json` from 6.0.15 on (`~0.0.1.0`), so it is in `extension/expai` on every installation and stays inactive
until it is activated. Its full documentation lives with it, in the
[expai repository](https://github.com/se7enxweb/expai).

## In short

| | |
|---|---|
| What it does | Editors ask in plain language for new content or changes; the assistant looks up what it needs and writes **drafts**, as the signed-in editor and with that editor's permissions. It never publishes on its own. |
| Where | The **Intelligence** tab of a node's full view, the edit form (field actions, presets, Improve menu, alt text), **Vector** (threads), Dashboard > Exponential Intelligence, Setup > EI: Summary |
| Providers | Anthropic, any OpenAI-compatible service (Melious.ai in the EU, Hugging Face, OpenRouter, Mistral ...), a local open-weight model through Ollama |
| Needs | Exponential 6.0.14 or later, PHP 8.0+ with `curl`, `dom`, `json`, `mbstring`; ezjscore, ezformtoken; [expqueue](expqueue.md) for queued work; a provider key or a local model |
| Release | [0.0.1.0](https://github.com/se7enxweb/expai/releases/tag/v0.0.1.0) |

## For editors

- **The Intelligence tab**: a conversation per user and node; each lookup and each draft is a step; the drafts it
  wrote are listed with links to edit, view and discard them. **Save as drafts** keeps them, **Send for approval**
  runs the normal publish operation as the editor, so an approval workflow holds the version.
- **The edit form**: "Fill the fields" from a brief, actions per field (Write, Rewrite, Shorten, Translate, Suggest),
  presets per class (title, summary, meta description, keywords), an **Improve** menu on every text field and a line
  with the model, region and cost of a click. Values go into the form for review.
- **Alt text** for images from a vision model, and a "Missing alt text" report that queues suggestions for a subtree.
- **Vector**: threads with scope, plan and write modes, plan mode with a cost estimate, staged review of every change
  per field and part, parallel runs of an approved plan, checkpoints, live progress over Server-Sent Events, and
  **What changed** with revert for drafts written directly.
- **AI chains** (write, review, revise, SEO, a human checkpoint, translate) and **translation sync** run through the
  job queue.

## For administrators

- **Providers and models**, with a fallback to the site default, and a **probe**: only models that passed a live
  tool-calling probe (or are trusted by configuration) may write drafts.
- **Data policy**: a region (`us`, `eu`, `local`) and retention per provider, rules per section and class, enforced
  everywhere and recorded per request.
- **Usage, cost, limits and budgets**: every request in a usage log (user ids only), the estimated cost from a price
  table (prices in another currency converted with the ECB reference rates), limits per user, day, month, site and
  provider, lanes and batch APIs for bulk work, budgets for queued work.
- **Master switch** (`[AISettings] Enabled`, also off per siteaccess) and a **veto hook** that refuses a request with
  a reason.
- **Audit**: every request, draft and draft state change is an event of the channel `ai` of the
  [audit trail](../../../bc/6.0/audit.md), with its own source in the audit console (dashboard panel, links, charts,
  CSV columns). Prompts, answers and keys are never recorded.
- **EI: Summary** (`expai/summary`): one printable page for a data protection officer with every provider's region,
  retention, terms date and key source (never the key), roles, lanes, limits and outside tools.

## For developers

- Tools the model can call, read services of expservices opted in with `'ai' => true`
  ([backend services](../../../bc/6.0/backend_ezjscore_services.md)), the in-process bridge to the expmcp extension
  when it is installed, and external MCP servers with an allowlist.
- It builds on generic kernel features: [install as drafts and the JSON package format](../../../bc/6.0/package-json-format.md),
  the event `package/install/drafts`, [audit sources](../../../bc/6.0/audit.md), and registries declared in
  [rad.ini](../../../bc/6.0/rad-extension-surface.md).

## Switch it on

1. Activate it for the admin siteaccess(es): in the `site.ini.append.php` of **each** admin siteaccess,
   `[ExtensionSettings] ActiveAccessExtensions[]=expai` (or `ActiveExtensions[]=expai` in
   `settings/override/site.ini.append.php` for every siteaccess). With the per-siteaccess form, every script and cron
   line that needs expai names that siteaccess with `-s <admin siteaccess>`.
2. Keys go into `settings/override/expai.ini.append.php` only (mode 0640, never committed):

   ```ini
   [AISettings]
   Enabled=true
   ApiKey=<your provider key>
   ```

3. Create the tables, on any engine of the kernel (idempotent; also the upgrade):

   ```bash
   php extension/expai/bin/php/expaischema.php --install -s <admin siteaccess>
   ```

4. Give editors `expai/write` and `ezjscore/call` (function list `expai`); further policies open Vector, chains, web
   and MCP tools.
5. `php bin/php/ezpgenerateautoloads.php -e`, clear the INI and template caches, reload PHP-FPM or run
   `./console exp:velocity deploy --packer`.
6. Cron: `php runcronjobs.php -q -s <admin siteaccess> expqueue` every minute and
   `php runcronjobs.php -q -s <admin siteaccess> expai` every few minutes.

The complete steps, with streaming behind Apache or nginx and the checks: the
[installation guide](https://github.com/se7enxweb/expai/blob/main/INSTALL.md). Day to day use: the
[usage guide](https://github.com/se7enxweb/expai/blob/main/USAGE.md). Every topic:
[the documentation index](https://github.com/se7enxweb/expai/blob/main/doc/index.md), also shown in the administration
at `expai/help`.

## Related pages

- [expqueue](expqueue.md), the job queue
- [Installing Exponential, section 10](../../../INSTALL.md#10-after-installing)
- [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#12-october-2026-exponential-intelligence-in-the-distribution)
- [Extensions index](README.md)
