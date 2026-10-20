---
title: "Einen Fork fair umbenennen"
audience: designer, sales-marketing
category: theme-lifecycle
order: 320
summary: Einem geforkten Theme eine eigene Identität geben, ohne seine Herkunft zu tilgen - ein neuer Name, Paket- und Design-Name, beibehaltene Hinweise und Lizenz, eine sichtbare Nennung „basiert auf“ und keine angedeutete Billigung - mit dem, was in welcher Datei zu ändern ist.
keywords: umbenennen, Rebranding, Fork, Namensnennung, basiert auf, Fairness, Hinweise, Billigung, Paketname, rebrand, rebranding, fork, rename, attribution, based on, ethics, notices, endorsement, package name
type: help
---
# Einen Fork fair umbenennen

Diese Seite ist für Designer, die einen Fork unter eigenem Namen veröffentlichen. Umbenennen ist unter der GPL erlaubt;
die Menschen auszulöschen, die das Original gemacht haben, nicht, und es ist unfair, selbst wo das Recht unklar ist.

## Ändern

| Was | Wo |
|---|---|
| Die Namen von Erweiterung und Design | Ordner, `DesignExtensions[]`, Dokumentation zu `SiteDesign` ([Ihre Theme-Erweiterung benennen](../erweiterungen/ihre-theme-erweiterung-benennen.md)) |
| Der lesbare Name | `Name` in `ezinfo.php`, `<name>` in `extension.xml`, README |
| Der Paketname | `name` in `composer.json`, `extra.ezpublish-legacy-extension-name` |
| Ihre Website und Ihr Support | `Info_url`, `homepage`, `support` |
| Ihre eigene neue Arbeit | Fügen Sie dafür Ihre Copyright-Zeile hinzu |

## Behalten

| Was | Warum |
|---|---|
| `LICENSE` und die Lizenz des Codes | Die GPL verlangt es |
| Jeden vorhandenen Copyright-Hinweis, in Dateien und Metadaten | Die GPL verlangt es; es ist auch die Wahrheit |
| Hinweise Dritter | Deren Lizenzen verlangen es |
| Eine Zeile „basiert auf <Original> von <Autor>“ im README | Fairness; hilft Nutzern, den Ursprung zu finden |

## Nicht tun

- Andeuten, dass der ursprüngliche Autor oder das Projekt Exponential Ihren Fork billigt.
- Den Namen des Originals als Namen Ihres Produkts verwenden ([Namen und Marken](../../../../eng-us/help/designers/sales/names-and-trademarks.md)).
- Die Historie umschreiben, um die ursprünglichen Autoren aus Commits zu entfernen.

## Schritt für Schritt

1. Nehmen Sie die Änderungen der ersten Tabelle in einem Commit vor: `Renamed: <old> to <new>`.
2. Fügen Sie die Zeile „basiert auf“ und Ihre Copyright-Zeile hinzu.
3. Suchen Sie nach dem alten Namen: `git grep -n -i "<old name>"`; ändern Sie nur die Identität, nie Copyright-Hinweise.
4. Veröffentlichen Sie unter Ihrem Paketnamen; sagen Sie Nutzern des Originals, wie sie wechseln, falls Ihr Fork es
   fortführt.

## Verwandte Seiten

- [Danksagungen und Namensnennung](danksagungen-und-namensnennung.md)
- [An Upstream zurückgeben](an-upstream-zurueckgeben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
