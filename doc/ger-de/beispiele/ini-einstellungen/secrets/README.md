---
title: secrets.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von secrets.ini.
keywords: Beispiele, Referenz, secrets.ini, examples, reference
type: generated
generated: exp:help:examples
---
# secrets.ini

Die ausgelieferten Einstellungen von `settings/secrets.ini`: 1 Blöcke, 2 Einstellungen.

> The kernel's secret store: passwords, tokens and API keys of the tools (data table connections, import sources,  
> remote servers), stored encrypted in the table exp\_secret. A tool keeps the NAME of a secret, never its value.  
> See doc/eng-us/features/6.0/secret-store.md. The administration: Setup > Secrets (module secrets, policy  
> secrets/manage); it shows names only.  

| Block | Einstellungen |
|---|---|
| [SecretSettings](SecretSettings.md) | 2 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/secrets.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
