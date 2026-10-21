---
title: Local models
audience: ai-agents, admin, hosting
category: ai-agents
order: 25
summary: Running an open-weight model on your own server with Exponential Intelligence so that no content leaves it - what the extension provides (a pinned Ollama on the loopback address, systemd limits, a root-owned helper with an allowlist), what to expect on a CPU, and how the data policy can keep sections on local models only.
keywords: local model, open-weight, Ollama, on premises, self hosted, nothing leaves the server, Region local, Qwen, CPU, systemd, expai-llm-helper, models.allow, expai/models, Load model, Test prompt
type: help
---
# Local models

This page is for administrators and hosting staff who want the assistant to work without sending content to an
outside provider. Exponential Intelligence can use an open-weight model that runs on the same server, through the same
adapter as a hosted provider. The extension's [local models page](https://github.com/se7enxweb/expai/blob/main/doc/local-models.md)
has the complete installation.

## Contents

1. [What you get](#what-you-get)
2. [What to expect on a CPU](#what-to-expect-on-a-cpu)
3. [How it is installed](#how-it-is-installed)
4. [Managing it from the administration](#managing-it-from-the-administration)
5. [Keeping content on local models](#keeping-content-on-local-models)
6. [Security of the setup](#security-of-the-setup)

## What you get

- A pinned release of **Ollama**, listening on the loopback address only (`127.0.0.1:11434`).
- systemd limits (CPU quota and weight, nice level, memory limits, no swap) so the model cannot crowd out the sites on
  the same host. The service is not started at boot; it starts on demand and an idle timer stops it after 30 minutes
  without a loaded model.
- A model provider `local` with `Region=local` and `Retention=none`, price 0, and a compact prompt suited to small
  models.
- Start, stop, pull, remove, a test prompt and "Load model" in **Setup > EI: Models**, through a root-owned helper.

## What to expect on a CPU

From the extension's own measurements: on a CPU only small models are practical. A 4 to 5 billion parameter model in a
4-bit quantization needs about 3 to 4 GB of disk and 4 to 6 GB of memory at a 16k context. Qwen3.5 4B is the
shipped example; the extension names Gemma 4 E4B and Granite as alternatives and Qwen3.5 2B as the smaller option.

| Expect | On a busy shared CPU host |
|---|---|
| Loading the model | About a minute |
| Writing | One to two tokens per second |
| A plain question with the compact prompt | About a minute |
| A step with tools | Minutes |

Slow models therefore run in the background (Vector and the Intelligence tab), with their own time limit. For many
editors, the extension recommends a separate CPU host or a hosted open-model provider with the same adapter.

## How it is installed

The installation is done once, as root, from the files in `share/systemd/` of the extension:

1. Download a pinned Ollama release and check its checksum.
2. Create the `ollama` system user and its directories.
3. Install the service file (with the loopback address and the limits), the helper, its path and idle units, the
   allowlist of models (`/etc/expai-llm/models.allow`) and the Modelfiles.
4. Put the web server's user into the group `expai-llm` and reload the site's PHP-FPM.
5. In `settings/override/expai.ini.append.php`, add `local` to `[ProviderSettings] Providers[]`.

The shipped `[Provider_local]` and `[Model_local.qwen3.5-4b]` groups hold the rest (no key header, region `local`,
retention `none`, a longer timeout, native tool use with thinking off, prices 0).

## Managing it from the administration

**Setup > EI: Models** (policy `expai/model_manage`) shows whether the server runs, its version, the loaded and
installed models, and offers:

| Button | Does |
|---|---|
| Start, Stop | Starts or stops the Ollama service |
| Pull, Remove | Fetches or removes a model on the allowlist |
| Test prompt | Queues a test; shows the time to the first token, tokens per second, total time, cost and the answer |
| Load model | Loads the model into memory so the first editor does not wait |

Test prompt and Load model run as jobs of the job queue (the extension `expqueue`), because loading takes longer
than a web request may run. **Load the model before editors start working.**

## Keeping content on local models

The data policy can require local processing for parts of the site:

```ini
[DataPolicy]
ClassRegions[contract]=local      # contracts never leave the server
SectionRegions[6]=eu;local        # section 6 only to EU or local providers
```

A `local` provider meets every region requirement. With such rules, a request about a contract goes only to a local
model, and the picker offers only the models that may take the content
([Privacy and data leaving the site](../governance/privacy-and-data-leaving-the-site.md)).

## Security of the setup

- **The web process never runs a command.** A button writes a small request file into a spool directory; the
  root-owned helper handles one request at a time, only fixed actions, only models on the root-owned allowlist, and
  drops requests that are old, large, malformed or not a regular file. Every action goes to the system journal.
- **Loopback only.** Ollama listens on `127.0.0.1`; check it with `ss -ltn`.
- **A local model still reads content.** Its answers are drafts like any other, and the same review applies.

## Related pages

- [Providers and models](providers-and-models.md)
- [Configuring a provider](configuring-a-provider.md)
- [Queues with expqueue](../../creators/app-creators/queues-with-expqueue.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
