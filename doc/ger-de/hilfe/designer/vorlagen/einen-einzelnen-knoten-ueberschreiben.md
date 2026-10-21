---
title: Einen einzelnen Knoten überschreiben
audience: designer, developer
category: templates
order: 600
summary: Ein besonderes Template für eine Seite (die Startseite, eine Kampagnenseite, eine Kontaktseite) - Abgleich über die Remote-ID statt der Knoten-ID, Vorrang vor Klassenregeln und wann ein Layout oder eine eigene Klasse das bessere Werkzeug ist.
keywords: einzelnen Knoten überschreiben, Template der Startseite, Template für eine Seite, Match node, Match remote_id, node_remote_id, besondere Seite, Override der Homepage, Kampagnenseite, override single node, front page template, one page template, special page, homepage override, campaign page
type: help
---
# Einen einzelnen Knoten überschreiben

Diese Seite ist für Designer, die eine Seite anders aussehen lassen müssen als alle anderen ihrer Klasse: die Startseite,
eine Landingpage, die Kontaktseite.

## Inhalt

1. [Die Regel](#die-regel)
2. [Knoten-ID oder Remote-ID](#knoten-id-oder-remote-id)
3. [Die Remote-ID finden](#die-remote-id-finden)
4. [Alternativen](#alternativen)

## Die Regel

```ini
[mytheme_full_front]
Source=node/view/full.tpl
MatchFile=full/front.tpl
Subdir=templates
Match[node_remote_id]=mytheme-front-node
Priority=0
```

`Priority=0` setzt sie vor die Klassenregeln ([Override-Reihenfolge](override-reihenfolge.md)).

## Knoten-ID oder Remote-ID

| Schlüssel | Wirkt auf | Übersteht eine Neuinstallation oder eine Kopie auf eine andere Installation |
|---|---|---|
| `Match[node]=2` | diese Knoten-ID | nein: IDs unterscheiden sich zwischen Installationen |
| `Match[node_remote_id]=...` | die Remote-ID des Knotens | ja, wenn die Inhalte aus einem Paket installiert werden, das Remote-IDs behält |
| `Match[remote_id]=...` | die Remote-ID des Objekts (alle seine Orte) | ja |

Das Theme starter gleicht seine Wurzelseite mit `Match[remote_id]=cjw-starter-o-root` ab, gesetzt von seinem Inhaltspaket.

## Die Remote-ID finden

In der Administration zeigen die Details des Knotens sie, oder in einem Template auf einer Testseite: `{$node.remote_id|wash}`
(Knoten) und `{$node.object.remote_id|wash}` (Objekt). Ein Entwickler kann eine lesbare Remote-ID setzen, wenn die Inhalte von
einem Paket oder Skript angelegt werden.

## Alternativen

- **Ein Layout** für die Seite, wenn Redakteure ihre Struktur ändern können sollen ([Layouts für Designer](../theme-design/layouts-fuer-designer.md)).
- **Eine eigene Klasse** (zum Beispiel `frontpage`), wenn mehrere Seiten das besondere Aussehen teilen.
- **Eine Sektion**, wenn sich ein ganzer Bereich unterscheidet ([Override nach Sektion](override-nach-sektion.md)).

## Verwandte Seiten

- [Override-Match-Schlüssel](override-match-schluessel.md)
- [Override-Grundlagen](override-grundlagen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
