---
title: "Das Lebensende ankündigen"
audience: designer, sales-marketing
category: theme-lifecycle
order: 260
summary: Was die Ankündigung des Lebensendes eines Themes sagt - das Datum, was aufhört, was verfügbar bleibt, den Nachfolger oder Wege nach vorn und wie Sie zahlende Kunden erreichen - mit einem Text zum Anpassen.
keywords: Ankündigung des Lebensendes, Hinweis, Abkündigungshinweis, Nachfolger, Kunden, Status im README, aufgegeben, end of life announcement, notice, deprecation notice, successor, customers, README status, abandoned
type: help
---
# Das Lebensende ankündigen

Diese Seite ist für Designer, die die Pflege beenden ([Lebensende](lebensende.md)).

## Was die Ankündigung sagt

1. Das Theme und das Datum, an dem die Pflege endet.
2. Was aufhört: Korrekturen, Kompatibilitäts-Updates, Support.
3. Was bleibt: das Repository (nur lesbar), jedes Release, das Packagist-Paket, die GPL-Rechte zum Forken.
4. Der Weg nach vorn: ein Nachfolger, ein neuer Betreuer oder „forken Sie es“.
5. Für zahlende Kunden: was mit ihren Verträgen geschieht.

## Ein Text zum Anpassen

```markdown
## End of life: My Theme

My Theme will no longer be maintained after 31 March 2027. Release 1.4.9 is the last one.

All releases stay installable from Packagist and GitHub, and the theme remains free software under the GPL:
anyone may fork and continue it. Its successor is Harbor (https://github.com/yourvendor/harbor), which
covers the same content classes; see UPGRADE-TO-HARBOR.md.

Customers with a support contract: we will contact you about the remaining term.
```

## Wo Sie sie veröffentlichen

- Im README (oben) und in einem GitHub-Release oder einem angehefteten Issue.
- In den Neuigkeiten der Projektseite auf projects.exponential.earth.
- In Ihrem Ankündigungs-Thread auf share.exponential.earth.
- Direkt bei zahlenden Kunden, vor der öffentlichen Ankündigung.

## Verwandte Seiten

- [Ein Repository archivieren](ein-repository-archivieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
