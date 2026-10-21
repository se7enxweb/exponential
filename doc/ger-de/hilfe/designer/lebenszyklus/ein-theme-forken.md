---
title: "Ein Theme forken"
audience: designer, developer
category: theme-lifecycle
order: 310
summary: Wann Sie ein Theme forken und wann Sie stattdessen beitragen, wie Sie mit Historie forken, damit Sie weiter Änderungen austauschen können, was die GPL von einem Fork verlangt und wie Sie Nutzern sagen, was Ihr Fork ist.
keywords: Fork, GitHub-Fork, GPL-Fork, stattdessen beitragen, Historie, Upstream, Downstream, umbenennen, fork, GitHub fork, GPL fork, contribute instead, history, upstream, downstream, rename
type: help
---
# Ein Theme forken

Diese Seite ist für Designer, die eine Kopie des Themes eines anderen unter eigener Kontrolle erwägen.

## Forken oder beitragen?

| Beitragen (Pull-Requests), wenn | Forken, wenn |
|---|---|
| Sie eine Korrektur oder Funktion brauchen, die auch andere wollen würden | Ihre Ziele sich von denen des Originals unterscheiden |
| Der Betreuer aktiv ist und antwortet | Das Original verwaist ist ([Ein verwaistes Theme übernehmen](ein-verwaistes-theme-uebernehmen.md)) |
| Sie die Wartungsarbeit teilen wollen | Sie schneller ausliefern müssen, als das Original prüfen kann |

Ein Fork für die privaten Änderungen eines Kunden lässt sich oft besser als kleine Erweiterung lösen, die das Original
überschreibt ([Anpassung und White-Label-Arbeit](../verkauf/anpassung-und-white-label.md)).

## Schritt für Schritt

1. Forken Sie mit Historie (GitHub **Fork** oder `gh repo fork upstreamvendor/theme --clone`), damit Sie später aus dem
   Original zusammenführen und dazu beitragen können ([Einen Fork synchron halten](einen-fork-synchron-halten.md)).
2. Behalten Sie `LICENSE`, jeden Copyright-Hinweis und die Autoren.
3. Wenn Sie ihn veröffentlichen, benennen Sie ihn um ([Einen Fork fair umbenennen](einen-fork-umbenennen.md)) und geben Sie
   ihm einen eigenen Paketnamen.
4. Schreiben Sie oben ins README: „Ein Fork von <Original> von <Autor>; Unterschiede: ...“.
5. Schicken Sie Korrekturen, die allen helfen, an das Original zurück ([An Upstream zurückgeben](an-upstream-zurueckgeben.md)).

## Was die GPL von einem Fork verlangt

Dass Sie die Lizenz und die Hinweise behalten, den Quellcode mit jeder verbreiteten Kopie weitergeben und keine weiteren
Einschränkungen hinzufügen. Eine Erlaubnis zum Forken verlangt sie nicht.

## Verwandte Seiten

- [Danksagungen und Namensnennung](danksagungen-und-namensnennung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
