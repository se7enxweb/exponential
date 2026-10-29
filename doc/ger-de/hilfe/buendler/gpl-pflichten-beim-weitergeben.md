---
title: GPL-Pflichten beim Weitergeben eines Bundles
audience: bundler, partner, hosting
category: bundling
order: 110
summary: Was die GNU General Public License, Version 2 oder später, von einem Bündler verlangt, der Exponential weitergibt - Lizenztexte, Hinweise, zugehöriger Quellcode einschließlich Änderungen, keine zusätzlichen Einschränkungen - wie das für Images, Pakete, Installer und Appliances gilt, und eine Checkliste. Keine Rechtsberatung.
keywords: GPL, GPL-2.0-or-later, Lizenz, Pflichten, Weitergabe, Quellcode, zugehöriger Quellcode, Hinweise, schriftliches Angebot, keine Rechtsberatung, license, obligations, distribution, source code, corresponding source, notices, written offer, appliance, image, installer, not legal advice
type: help
---
# GPL-Pflichten beim Weitergeben eines Bundles

Diese Seite richtet sich an Bündler und ihre Rechts- und Produktteams. Sie fasst zusammen, was die Lizenz des Codes
von Exponential verlangt, wenn Sie Kopien an andere weitergeben. **Sie ist keine Rechtsberatung.** Maßgeblich sind die
Lizenztexte, und Ihr Rechtsbeistand entscheidet, wie sie auf Ihr Bundle anzuwenden sind.

## Inhalt

1. [Die Lizenz des Codes](#die-lizenz-des-codes)
2. [Was Sie dürfen](#was-sie-dürfen)
3. [Was Sie beim Weitergeben tun müssen](#was-sie-beim-weitergeben-tun-müssen)
4. [Wie das für jede Art von Bundle gilt](#wie-das-für-jede-art-von-bundle-gilt)
5. [Ihr eigener Code im Bundle](#ihr-eigener-code-im-bundle)
6. [Checkliste](#checkliste)

## Die Lizenz des Codes

| Punkt | Aussage | Wo |
|---|---|---|
| Lizenz | GNU General Public License, Version 2, "or (at your option) any later version" (oder, nach Ihrer Wahl, jede spätere Version) | `COPYRIGHT.md`, `LICENSE.md` (der Text der GPL v2) |
| Lizenzfeld in Composer | `"license": "GPL-2.0-or-later"` | `composer.json` |
| Urheberrecht | "Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved." und "Copyright (C) 1999 - 2014 eZ Systems AS" | `COPYRIGHT.md`, `LICENSE` |
| Beiträge | "Mit Ihrem Beitrag stimmen Sie zu, dass Ihre Beiträge unter der GNU General Public License, Version 2.0 oder später, lizenziert werden" | `CONTRIBUTING.md`, zitiert unter [Lizenzierung für Partner](../partner/lizenzierung-fuer-partner.md) |
| Erweiterungen | jede hat ihre eigene; die des Projekts stehen unter der GPL | `extension.xml`, `ezinfo.php` und Lizenzdatei jeder Erweiterung |

## Was Sie dürfen

- Exponential für jeden Zweck ausführen, untersuchen, ändern und kombinieren.
- Kopien, geändert oder nicht, als Teil eines Bundles weitergeben.
- Geld verlangen für das Bundle, die Weitergabe, die Installation, Support, Hosting und Ihre eigene Arbeit. "Die
  Nutzung von Exponential unter den Bedingungen der GNU GPL ist kostenlos" (README); wofür Ihre Kunden Sie bezahlen,
  ist die Arbeit und der Service, nie eine Lizenz für die Software
  ([Lizenzierung für Partner](../partner/lizenzierung-fuer-partner.md#was-partner-dürfen)).

## Was Sie beim Weitergeben tun müssen

Aus [Lizenzierung für Partner](../partner/lizenzierung-fuer-partner.md#was-partner-bei-der-verbreitung-tun-müssen) und
[Distributionspartner](../partner/vertriebspartner.md#pflichten-eines-distributors):

1. **Geben Sie die Lizenz und die Hinweise weiter.** Legen Sie die Lizenztexte bei (`LICENSE`, `LICENSE.md`,
   `COPYRIGHT.md`) und behalten Sie jeden Urheberrechtshinweis in den Dateien bei.
2. **Stellen Sie den Quellcode bereit**, einschließlich Ihrer Änderungen, wie es die GPL verlangt. Für die GPL
   Version 2 beschreibt Abschnitt 3 die Wege: der Quellcode zusammen mit den Binärdateien oder ein schriftliches, für
   mindestens drei Jahre gültiges Angebot, ihn bereitzustellen. Lesen Sie den Abschnitt selbst, bevor Sie wählen.
3. **Fügen Sie keine Einschränkungen hinzu**, die die GPL verbietet: keine Klausel in Ihren Bedingungen, die den
   Empfängern das Recht nimmt, die GPL-Teile zu kopieren, zu ändern und weiterzugeben.
4. **Behalten Sie die Hinweise der Dokumentation bei**, wenn Sie die Dokumentation mitliefern (GFDL; siehe
   [Mitzuliefernde Dokumentation](mitzuliefernde-dokumentation.md)).

## Wie das für jede Art von Bundle gilt

| Bundle | Praktischer Weg, die Quellcode-Pflicht zu erfüllen | Hinweise |
|---|---|---|
| Container-Image | die Build-Dateien und die genauen Quellen (Tag, Lock-Datei, Ihre Erweiterungen) neben dem Image veröffentlichen | ein Image ist eine Weitergabe jeder GPL-Komponente darin |
| Server- oder VM-Image | dasselbe; zusätzlich die Quellen der GPL-Teile des Betriebssystems, wie es deren eigene Lizenzen verlangen | für das Betriebssystem gelten die Bedingungen seines Herstellers |
| Panel-Installer, der Exponential herunterlädt | der Empfänger bezieht den Code vom Projekt; Ihre Installer-Skripte sind Ihr eigener Code | klären Sie mit einem Rechtsbeistand, wer was weitergibt |
| Distributionspaket | das Quellpaket Ihrer Distribution | folgen Sie den Richtlinien der Distribution |
| Starter-Kit-Repository | das Repository ist der Quellcode | fügen Sie Ihre Lizenzdatei hinzu |
| Appliance | Quellcode der GPL-Software auf der Box und keine Sperren, die die Empfänger an der Ausübung ihrer Rechte hindern | fragen Sie einen Rechtsbeistand; bei Appliances passieren die meisten Fehler |

## Ihr eigener Code im Bundle

| Ihr Teil | Frage, die Sie mit einem Rechtsbeistand klären |
|---|---|
| Eine Erweiterung, die die APIs von Exponential verwendet | Muss sie GPL-kompatibel sein, wenn sie mit Exponential weitergegeben wird? Die Partnerseiten raten: "Wenn sie mit GPL-Code oder als Teil davon weitergegeben werden, müssen sie GPL-kompatibel sein; fragen Sie Ihren Rechtsbeistand" ([Distributionspartner](../partner/vertriebspartner.md#was-sie-verbreiten-dürfen)) |
| Ein Theme oder Seitenpaket | Welche Lizenz für Templates, Styles, Bilder und Inhalte? Siehe [Lizenzfelder in Ihren Paketen](lizenzfelder-in-ihren-eigenen-paketen.md) |
| Eigenständige Programme im Image (ein Überwachungsagent, ein Panel) | Sie behalten ihre eigenen Lizenzen; führen Sie sie auf ([Komponenten Dritter](komponenten-dritter-und-ihre-lizenzen.md)) |

## Checkliste

- [ ] `LICENSE`, `LICENSE.md` und `COPYRIGHT.md` sind unverändert im Bundle.
- [ ] Die Urheberrechtsköpfe in den Dateien sind intakt.
- [ ] Der zugehörige Quellcode (Kernel-Tag, `composer.lock`, Ihre Erweiterungen, Ihre Änderungen, Build-Dateien) ist
      veröffentlicht oder angeboten, und die Release-Hinweise sagen, wo.
- [ ] Jede Komponente Dritter und ihre Lizenz ist aufgeführt.
- [ ] Ihre Bedingungen schränken die GPL-Teile nicht ein.
- [ ] Die mitgelieferte Dokumentation behält ihre GFDL-Hinweise und den Lizenztext.
- [ ] Ein Rechtsbeistand hat die Lizenzierung des Bundles einmal geprüft, und erneut, wenn sich seine Form ändert.

## Verwandte Seiten

- [Mitzuliefernde Lizenztexte und Hinweise](mitzuliefernde-lizenztexte-und-hinweise.md)
- [Lizenzierung für Partner](../partner/lizenzierung-fuer-partner.md), [Distributionspartner und Wiederverkäufer](../partner/vertriebspartner.md)
- [Lizenzierung für Hoster](../hosting/geschaeft/lizenzierung-fuer-hoster.md), [Fragen zur Lizenzierung](../projekt/lizenz-faq.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->

## Urheberrecht und Copyleft

Seit dem 29. Oktober 2026 halten die Exponential Foundation und 7x das Copyright an Exponential gemeinsam, die
Foundation zuerst genannt: „Exponential Copyright © 1998-2026 Exponential Foundation and 7x“, in Dateien
„Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.“ Wer es hält und warum, was die
GPL Ihnen erlaubt, welche Hinweise Dritter bleiben und wie der Hinweis in neue Dateien und Erweiterungen kommt:
[Urheberrecht und Copyleft](../urheberrecht-und-copyleft.md).
