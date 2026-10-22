---
title: Namen und Geschichte für Lernende
audience: student
category: learning
order: 49
summary: Die Namen, denen Lernende begegnen, und wie Sie sie verwenden - Exponential, 7x, die Exponential Foundation, Velocity, Exponential Layouts, Exponential Intelligence, Exponential Basic -, die kurze Geschichte von 1999 bis heute, warum Kernel-Bezeichner immer noch mit eZ beginnen und wie Sie in Berichten darüber schreiben.
keywords: namen, geschichte, Exponential, 7x, Exponential Foundation, eZ Publish, Velocity, Layouts, Intelligence, Exponential Basic, benennung, präfixe, ez, exp, sevenx, lernende, names, history, naming, prefixes, learner
type: help
---
# Namen und Geschichte für Lernende

Beim Lernen begegnen Ihnen mehrere Namen: in der Administrationsoberfläche, im Code, in alten Forenbeiträgen. Diese
Seite sagt Ihnen, was jeder bedeutet und wie Sie in Ihren Berichten darüber schreiben.

## Inhalt

1. [Die Namen](#die-namen)
2. [Die Geschichte in einem Absatz](#die-geschichte-in-einem-absatz)
3. [Warum der Code noch eZ sagt](#warum-der-code-noch-ez-sagt)
4. [Darüber schreiben](#darüber-schreiben)

## Die Namen

| Name | Was es ist | Quelle |
|---|---|---|
| **Exponential** | Das Produkt: ein freies und quelloffenes Content-Management-System und Anwendungsframework in PHP | [Presse: hier beginnen](../presse/presse-hier-beginnen.md#exponential-in-einem-absatz) |
| **7x** | Das Unternehmen, das seine Entwicklung leitet | [Wer ist 7x?](../wer-ist-7x.md) |
| **die Exponential Foundation** | Die 2026 gegründete gemeinnützige Organisation, die das Projekt betreibt und finanziert | [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md) |
| **Exponential Velocity** | Der Anwendungsserver, seit 6.0.15 Teil von Exponential; in den Einstellungen heißt seine Engine `qbix` | [Velocity ist Teil von Exponential 6.0.15](../velocity-ist-teil-von-exponential-6-0-15.md), [Glossar](../../glossar.md#q) |
| **Exponential Layouts** | Der visuelle Seitenbaukasten mit Layouts, Zonen und Blöcken | [Der Layouts-Editor](../layout-editor.md) |
| **Exponential Intelligence** | Die KI-Funktionen des Editors und der Administrationsoberfläche | [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#exponential-intelligence) |
| **Exponential Basic** | Ein kleines CMS „für alle, für Bildungszwecke, für den persönlichen Gebrauch“ | [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#exponential-basic) |

Die Produktfamilie hat weitere Mitglieder (Exponential Platform Legacy, Nexus, DXP und andere); siehe
[Die Produktfamilie auf einen Blick](../wer-ist-die-exponential-foundation.md#die-produktfamilie-im-überblick). Bevor
Sie eines davon beschreiben, prüfen Sie, was es tatsächlich enthält.

## Die Geschichte in einem Absatz

Die Codebasis wird seit 1999 entwickelt, zuerst unter dem Namen eZ Publish, einem Produkt, das sein Hersteller nach
fast 18 Jahren eingestellt hat. Das Projekt von 7x hat es am Leben erhalten und im Dezember 2023 neu gestartet, und
seit August 2025 heißt das Produkt Exponential. Die Exponential Foundation wurde 2026 gegründet
([Presse: hier beginnen](../presse/presse-hier-beginnen.md#exponential-in-einem-absatz)). Diese lange Geschichte ist der
Grund, warum Sie zwanzig Jahre an Forenbeiträgen, Büchern und Erweiterungen finden; vieles davon gilt noch, aber
prüfen Sie es am aktuellen Release.

## Warum der Code noch eZ sagt

Kernel-Bezeichner behalten ihre echten Namen: `eZContentObject`, `eZINI`, `eZTemplate` und viele mehr werden niemals
umbenannt. Neuer Code von 7x verwendet die Präfixe `exp` oder `sevenx` (`sevenx`, weil ein PHP-Bezeichner nicht mit
einer Ziffer beginnen kann) ([Glossar](../../glossar.md#kurz-gefasst), [Richtlinien für Mitwirkende](../../../../CONTRIBUTING.md#making-changes)).

| Sie sehen | Bedeutet |
|---|---|
| Klasse `eZ...`, Erweiterung `ez...` | Ein älterer Kernel-Bezeichner oder eine ältere Erweiterung, Name beibehalten |
| `exp...` | Arbeit, die 7x dem Exponential-Projekt und der Foundation zuordnet |
| `sevenx...` | Das öffentliche PHP-Präfix von 7x |

Wenn Sie selbst neuen Code schreiben, verwenden Sie Ihr eigenes Präfix, nicht `ez`, `exp` oder `sevenx`, es sei denn,
Sie tragen zu diesen Projekten bei.

## Darüber schreiben

- Schreiben Sie **Exponential** für das Produkt, **7x** für das Unternehmen (mit der Ziffer), **die Exponential
  Foundation** für die gemeinnützige Organisation, bei der ersten Nennung vollständig.
- Verwenden Sie den alten Namen nur für die Geschichte, in der Vergangenheitsform: „Exponential setzt die Codebasis
  von eZ Publish fort.“
- Verwechseln Sie das Unternehmen nicht mit der gemeinnützigen Organisation.
- Behaupten Sie keine Tatsachen, die die Quellen nicht hergeben. Die Presseseiten listen auf, was nicht dokumentiert
  ist ([Presse: hier beginnen](../presse/presse-hier-beginnen.md)).

Die vollständigen Regeln: [Marken- und Namensregeln](../geschaeft/marken-und-namensregeln.md).

## Verwandte Seiten

- [Wer ist 7x?](../wer-ist-7x.md)
- [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md)
- [Glossar für Lernende](glossar-fuer-lernende.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
