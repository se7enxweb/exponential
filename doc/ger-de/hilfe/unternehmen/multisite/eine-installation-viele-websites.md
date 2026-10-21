---
title: Eine Installation, viele Websites
audience: businesses, project-manager, admin
category: business-solutions
order: 20
summary: Wie Exponential aus einer Installation mehrere Websites macht - Siteaccesses, Adressen nach Pfad, Host oder Port, ein Wurzelordner pro Website, die Sicht der Administration auf alle Websites und was das Unternehmen mit DNS und Zertifikaten regeln muss.
keywords: Siteaccess, Multisite, Host-Zuordnung, Pfad, Port, Zertifikat, Marken, Unternehmen, siteaccess, multisite, host matching, path, port, RootNode, IndexPage, HostMatchMapItems, DNS, HTTPS, certificate, brands, business
type: help
---
# Eine Installation, viele Websites

Diese Seite richtet sich an Führungskräfte und Projektleiter, die das Modell hinter mehreren Websites in einer
Exponential-Installation gut genug verstehen wollen, um Entscheidungen zu treffen und mit dem technischen Team zu
sprechen. Sie erklärt, was ein Siteaccess ist, wie die Adresse eines Besuchers die richtige Marke erreicht und welche
Teile eines Starts Sache des Unternehmens sind und nicht der Entwickler.

## Inhalt

1. [Der Siteaccess in einem Absatz](#der-siteaccess-in-einem-absatz)
2. [Wie Besucher eine Marke erreichen](#wie-besucher-eine-marke-erreichen)
3. [Startseite und Inhaltswurzel jeder Marke](#startseite-und-inhaltswurzel-jeder-marke)
4. [Was zum Hinzufügen einer Marke gehört](#was-zum-hinzufügen-einer-marke-gehört)
5. [Was das Unternehmen regeln muss](#was-das-unternehmen-regeln-muss)
6. [Test-Hosts und Staging](#test-hosts-und-staging)

## Der Siteaccess in einem Absatz

Ein Siteaccess ist „ein Weg in die Installation“: ein Name, ein Satz von Einstellungen in `settings/siteaccess/<name>/`
und eine Regel, welche Anfragen er beantwortet
([10.2 Siteaccesses und Website-Adressen](../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).
Jede öffentliche Marke, jede Sprache einer Marke und jede Administration ist ein Siteaccess. Sie teilen den Code und
meist die Datenbank; sie unterscheiden sich in Name, Adresse, Sprachliste, Design und Startseite.

## Wie Besucher eine Marke erreichen

Der Installer hat eine von drei Anordnungen gewählt, und sie lassen sich später ändern:

| Anordnung | Beispiel | Was die Quellen sagen |
|---|---|---|
| URL (Pfad) | `/admin`, `/de` | „ein Hostname für alles; am einfachsten hinter jedem Webserver“ |
| Host | `admin.example.com` | „ein Hostname pro Siteaccess; am besten für den Produktivbetrieb und für HTTPS-Zertifikate pro Host“ |
| Port | `:8081` | „ein Port pro Siteaccess; nützlich für die lokale Arbeit“ |

Zitiert aus [Multisite- und mehrsprachige Projekte planen](../../fuehrung/multisite-und-mehrsprachigkeit-planen.md#wie-besucher-eine-site-erreichen).
Für getrennte Marken ist ein Host pro Marke (`www.brand-a.example`, `www.brand-b.example`) die übliche Wahl: Jede Marke
behält ihre eigene Domain, ihre eigene Webanalyse und ihr eigenes Zertifikat. Ein Pfad pro Marke passt zu Microsites,
die unter der Hauptdomain liegen.

> **Tipp**
> Pfade und Hosts lassen sich mischen. Die Hauptmarke kann ihren eigenen Host nutzen, während Microsites für Kampagnen
> Pfade darunter verwenden.

## Startseite und Inhaltswurzel jeder Marke

Jeder Siteaccess nennt die Seite, auf der er öffnet (`IndexPage`), und den Knoten, den er als Wurzel seines
Inhaltsbaums behandelt (`content.ini [NodeSettings] RootNode`). Für getrennte Marken ist die übliche Einrichtung ein
Ordner pro Marke unter der Wurzel, wobei jeder Siteaccess seinen Ordner als Wurzel nutzt
([Schritt für Schritt: eine zweite Website](../../ersteller/website-ersteller/multisite-einrichten.md#schritt-für-schritt-eine-zweite-website)).

Für das Unternehmen bedeutet das:

- Jede Marke hat ihr eigenes Menü und ihre eigene Startseite.
- Redakteure arbeiten im Ordner der Marke; Rollen können sie dort halten.
- Inhalte außerhalb des Ordners einer Marke können dort trotzdem gezielt verlinkt oder angezeigt werden (siehe
  [Inhalte zwischen Marken teilen](inhalte-zwischen-marken-teilen.md)).

## Was zum Hinzufügen einer Marke gehört

Die technischen Schritte, zusammengefasst aus dem Leitfaden für Website-Ersteller:

| Schritt | Wer | Was geschieht |
|---|---|---|
| 1. Inhalte | Redakteur oder Website-Ersteller | Unter der Wurzel wird ein Ordner für die Marke angelegt |
| 2. Siteaccess | Entwickler | Ein Einstellungsverzeichnis für die Marke: Name, Adresse, Startseite, Design |
| 3. Registrieren und zuordnen | Entwickler oder Administrator | Die Marke wird der Liste der Siteaccesses hinzugefügt, und ihr Host oder Pfad wird ihr zugeordnet |
| 4. Webserver und DNS | Hosting | Der Hostname wird in DNS, im Webserver oder in Velocity und im Zertifikat eingetragen |
| 5. Caches leeren und prüfen | Administrator | Der INI-Cache wird geleert und die neue Adresse getestet |
| 6. Die Administration informieren | Administrator | Die Marke wird in die `SiteList[]` der Administration aufgenommen, damit Vorschauen und Links sie nutzen können |

Die vollständigen Schritte mit den genauen Einstellungen stehen unter
[Mehrere Websites in einer Installation einrichten](../../ersteller/website-ersteller/multisite-einrichten.md#schritt-für-schritt-eine-zweite-website).

## Was das Unternehmen regeln muss

- [ ] **Domainnamen**: registriert und auf den Server zeigend.
- [ ] **Zertifikate**: Jeder Hostname, den die Marke nutzt, ist durch HTTPS abgedeckt
      ([Online gehen: Hosts, HTTPS und Siteaccesses](../../ersteller/website-ersteller/livegang-hosts-und-https.md)).
- [ ] **Konten für Webanalyse und Search Console** pro Marke, wenn Sie Marken getrennt messen.
- [ ] **Rechtliche Seiten** pro Marke (Impressum, Datenschutzhinweis, Nutzungsbedingungen), vom Markenverantwortlichen
      verfasst.
- [ ] **Ein Markenverantwortlicher**, der Inhalte und den Start freigibt.

## Test-Hosts und Staging

Eine Einstellung namens `HostMatchMethod` lässt die Host-Zuordnung nach dem Anfang, dem Ende oder einem Teil eines Hosts
vergleichen. Mit `start` erreicht ein Test-Host wie `www.example.com.test.local` denselben Siteaccess wie
`www.example.com`, ohne zusätzliche Einträge
([Siteaccess nach Host-Präfix und die Weiterleitung zur Sprache](../../../funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md)).
Dieselbe Seite warnt, dass `start` und `part` auch Hosts akzeptieren, die Sie nicht gemeint haben; sie gehören daher
dorthin, wo der Webserver nur für Ihre eigenen Hosts antwortet. Fragen Sie Ihr technisches Team, welche Methode Ihre
Installation verwendet.

> **Hinweis**
> Die mitgelieferte `site.ini` verwendet `HostMatchMethod=strict`: Ohne Änderung verhält sich nichts anders als vorher.

## Verwandte Seiten

- [Mehrere Marken planen](mehrere-marken-planen.md)
- [Einstellungen, Logs und Caches pro Website](einstellungen-logs-und-caches-pro-website.md)
- [Mehrere Websites in einer Installation einrichten](../../ersteller/website-ersteller/multisite-einrichten.md)
- [Multisite in einer Installation (für Hoster)](../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md)
- [Kapitel 10: Nach der Installation](../../../installation/10-nach-der-installation.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
