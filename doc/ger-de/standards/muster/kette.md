---
title: Zuständigkeitskette (Chain of Responsibility)
audience: developer
category: standards
order: 332
summary: "Eine Anfrage wird entlang einer Liste von Handlern weitergereicht, bis einer sie übernimmt: Login-Handler, URL-Matcher, Router-Routen. 19 Instanzen in Exponential."
keywords: Entwurfsmuster, Kette, Verhalten, Musterkatalog, design pattern, chain, pattern catalogue
type: catalogue
---
# Zuständigkeitskette (Chain of Responsibility)

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Eine Anfrage wird entlang einer Liste von Handlern weitergereicht, bis einer sie übernimmt: Login-Handler, URL-Matcher, Router-Routen.

| | |
| --- | --- |
| Gruppe | Verhalten |
| Instanzen | 19 |
| Erkannt an | Der Klassenname enthält LoginHandler, Chain, Pipeline, Middleware, Router oder Matcher. |
| Erweiterungspunkt | site.ini [UserSettings] LoginHandler[] (der Reihe nach versucht), Siteaccess-MatchOrder. |

## Klassen nach Bereich

### extension/expai (12)

[Alle Muster von extension/expai](area-extension-expai.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expAiBatchOpenRouter` | class extends expAiBatchApi | `extension/expai/classes/lanes/expaibatchopenrouter.php:25` | Die OpenRouter Batch API (https://openrouter.ai/docs/batch-quickstart und https://openrouter.ai/docs/api/api-reference/batch/get-a-batch, gelesen 2026-1… | – |
| `expAiChain` | class | `extension/expai/classes/chain/expaichain.php:23` | Die Definition einer KI-Kette (doc/chains.md): eine geordnete Liste von Schritten, jeder ein strukturierter Aufruf an ein Modell, ein menschlicher Kontrollpunkt oder eine Übergabe, gelesen… | – |
| `expAiChainApplier` | class | `extension/expai/classes/chain/expaichainapplier.php:14` | Der Applier „chain“ der eingereihten Anfragen ([QueueSettings] Appliers[chain]): übergibt die gespeicherte Antwort eines Kettenschritts an expAiChainEngine::onAnswer(), das… | extension/expai/settings/expai.ini [QueueSettings] Appliers[chain] |
| `expAiChainCriteria` | class | `extension/expai/classes/chain/expaichaincriteria.php:23` | Die Bestehenskriterien eines Kettenschritts ([ChainStep_.] PassCriteria[]): jeder Eintrag ist ein Vergleich „ “, alle müssen gelten. | – |
| `expAiChainEngine` | class | `extension/expai/classes/chain/expaichainengine.php:24` | Führt KI-Ketten (doc/chains.md) über die Job-Warteschlange aus. | – |
| `expAiChainFunctionCollection` | class | `extension/expai/classes/chain/expaichainfunctioncollection.php:18` | Template-Fetch-Funktionen von KI-Ketten: | – |
| `expAiChainPolicy` | class | `extension/expai/classes/chain/expaichainpolicy.php:20` | Zugriff auf KI-Ketten: Die Richtlinie expai/chain (Einschränkung Chain) startet Läufe und handelt an der Kontrollstelle auf sie. | – |
| `expAiChainPrompt` | class | `extension/expai/classes/chain/expaichainprompt.php:21` | Die Prompts der Kettenschritte. | – |
| `expAiChainRun` | class extends eZPersistentObject | `extension/expai/classes/chain/expaichainrun.php:20` | Ein Lauf einer KI-Kette (Tabelle expai_chain_run). | – |
| `expAiChainSchema` | class | `extension/expai/classes/chain/expaichainschema.php:19` | Die Tabellen der KI-Ketten, ergänzt in expAiSchema::definition() (bin/php/expaischema.php --install legt sie auf jeder Engine an und aktualisiert sie wie die ander… | – |
| `expAiChainStep` | class extends eZPersistentObject | `extension/expai/classes/chain/expaichainstep.php:19` | Ein Schritt eines Kettenlaufs (Tabelle expai_chain_step): Jeder Aufruf eines Modells ist eine Zeile mit seinem Warteschlangen-Job, Modell, seiner Lane, den Tokens, den Kosten und der Nutzungszeile; a… | – |
| `expAiChainTranslateSync` | class | `extension/expai/classes/chain/expaichaintranslatesync.php:20` | Die mitgelieferte Übersetzungsübergabe der Ketten ([ChainSettings] TranslateHandler=expAiChainTranslateSync): Der Übersetzungsschritt übergibt das Objekt des Laufs an den… | extension/expai/settings/expai.ini [ChainSettings] TranslateHandler |

### extension/ezjscore (1)

[Alle Muster von extension/ezjscore](bereich-extension-ezjscore.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezjscServerRouter` | class | `extension/ezjscore/classes/ezjscserverrouter.php:39` | – | – |

### extension/hcaptcha (1)

[Alle Muster von extension/hcaptcha](bereich-extension-hcaptcha.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `hcaptchaInfo` | class | `extension/hcaptcha/ezinfo.php:22` | hCaptcha-Extension für Exponential, geschrieben von 7x, basierend auf Arbeiten von: Bruce Morrison | – |

### extension/recaptcha (1)

[Alle Muster von extension/recaptcha](bereich-extension-recaptcha.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `recaptchaInfo` | class | `extension/recaptcha/ezinfo.php:22` | reCAPTCHA-Extension für Exponential, geschrieben von Bruce Morrison | – |

### kernel/classes/datatypes (1)

[Alle Muster von kernel/classes/datatypes](bereich-kernel-classes-datatypes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZUserLoginHandler` | class | `kernel/classes/datatypes/ezuser/ezuserloginhandler.php:19` | – | – |

### kernel/classes/ini (1)

[Alle Muster von kernel/classes/ini](bereich-kernel-classes-ini.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expSettingsChain` | class | `kernel/classes/ini/expsettingschain.php:32` | Die Überschreibungskette einer INI-Datei, einmal aufgebaut aus den Dateien, die eZINI dafür liest, in eZINIs eigener Reihenfolge. | – |

### kernel/private (1)

[Alle Muster von kernel/private](bereich-kernel-private.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpRestRouter` | class extends ezcMvcRouter | `kernel/private/rest/classes/router.php:11` | – | – |

### kernel/private/classes/commands (1)

[Alle Muster von kernel/private/classes/commands](bereich-kernel-private-classes-commands.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\Command\Kernel\VelocityRouter` | class | `kernel/private/classes/commands/velocity-router.php:46` | Der Code von bin/php/velocity-router.php, in eine Klasse verschoben (#207). | – |
