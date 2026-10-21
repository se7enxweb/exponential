---
title: Zuständigkeitskette (Chain of Responsibility)
audience: developer
category: standards
order: 332
summary: "Eine Anfrage wird entlang einer Liste von Handlern weitergereicht, bis einer sie übernimmt: Login-Handler, URL-Matcher, Router-Routen. 7 Instanzen in Exponential."
keywords: Entwurfsmuster, Kette, Verhalten, Musterkatalog, design pattern, chain, pattern catalogue
type: catalogue
---
# Zuständigkeitskette (Chain of Responsibility)

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Eine Anfrage wird entlang einer Liste von Handlern weitergereicht, bis einer sie übernimmt: Login-Handler, URL-Matcher, Router-Routen.

|  |  |
| --- | --- |
| Gruppe | Verhalten |
| Instanzen | 7 |
| Erkannt an | Der Klassenname enthält LoginHandler, Chain, Pipeline, Middleware, Router oder Matcher. |
| Erweiterungspunkt | site.ini [UserSettings] LoginHandler[] (der Reihe nach versucht), Siteaccess-MatchOrder. |

## Klassen nach Bereich

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
