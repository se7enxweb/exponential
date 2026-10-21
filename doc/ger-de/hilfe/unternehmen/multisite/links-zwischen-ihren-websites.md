---
title: Links zwischen Ihren Websites
audience: businesses, project-manager, admin
category: business-solutions
order: 50
summary: Warum ein Link von der Seite einer Marke in die Inhalte einer anderen Marke in einer Multisite-Installation zu einem 404 führen kann, wie die Erweiterung ezurlmultisite Besucher auf die richtige Website schickt und was Sie vor dem Start testen sollten.
keywords: Links zwischen Websites, fehlerhafte Links, Multisite, Marken, 404, ezurlmultisite, PathPrefix, SiteAccessList, RootNodeList, Unternehmen, cross-site links, multisite, brands, broken links, business
type: help
---
# Links zwischen Ihren Websites

Diese Seite richtet sich an Führungskräfte und Projektleiter von Organisationen, die mehrere Marken aus einem
Inhaltsbaum betreiben. Wenn eine Seite der einen Marke Inhalte auflistet, die zu einer anderen gehören (eine Tag-Seite,
ein Suchergebnis, ein Teaser „Verwandte Inhalte“), muss der Link zur Adresse der anderen Marke führen. Diese Seite
erklärt, warum das nicht von selbst geschieht, was es behebt und was Sie prüfen sollten, damit Besucher zwischen Ihren
eigenen Websites nie auf einer Fehlerseite landen.

## Inhalt

1. [Das Problem in einfachen Worten](#das-problem-in-einfachen-worten)
2. [Die Lösung: ezurlmultisite](#die-lösung-ezurlmultisite)
3. [Wie ein Link seine Website findet](#wie-ein-link-seine-website-findet)
4. [Was unverändert bleibt](#was-unverändert-bleibt)
5. [Was das Unternehmen verlangen sollte](#was-das-unternehmen-verlangen-sollte)
6. [Tests vor dem Start](#tests-vor-dem-start)

## Das Problem in einfachen Worten

Jede Marke blendet den Pfad ihres eigenen Ordners in ihren Adressen aus. Eine Seite der Marke B behält den Pfad der
Marke B in ihrer Adresse, und der Link-Generator der aktuellen Website setzt die aktuelle Marke davor. Das Ergebnis ist
eine Adresse auf Marke A, die es nicht gibt, und der Besucher erhält einen 404
([Links zu Inhalten einer anderen Website einer Multisite-Installation](../../../funktionen/6.0/websiteuebergreifende-links-im-multisite.md#kurz-gesagt)).

Die Funktionsseite verwendet die Multisite-Demo als Beispiel: Fit & Healthy unter `/site` oder `/`, Bold Agency unter
`/bold` und ihre Übersetzungen. Ein Link von Bold Agency zu einem Video von Fit & Healthy würde zu
`/bold/fit-healthy/video/...`, worauf die Antwort „module not found“ kommt.

Für ein Unternehmen ist das wichtig, weil solche Links oft automatisch entstehen: Listen nach Tag, Suchergebnisse und
verwandte Elemente. Niemand tippt sie ein, also bemerkt es niemand, bis ein Kunde es bemerkt.

## Die Lösung: ezurlmultisite

Die Erweiterung `ezurlmultisite` (`se7enxweb/ezurlmultisite`, vom `composer.json` im Wurzelverzeichnis verlangt) sorgt
dafür, dass der Standard-Link-Generator `ezurl` und die eigenen Link-Operatoren des Media-Themes die Adresse auf dem
Siteaccess liefern, der die Inhalte ausliefert (dieselbe Quelle).

| | |
|---|---|
| Konfiguration | `settings/override/ezurlmultisite.ini.append.php`: die Liste der beteiligten Websites in der Reihenfolge der Bevorzugung und der Wurzelknoten des Teilbaums jeder Website |
| Neue Installationen | Die Multisite-Installer (`sevenx_multisite`, `sevenx_multisite_clean`, `cjw_multisite`) aktivieren die Erweiterung und schreiben die Datei mit den Knoten-IDs der Installation, die sie anlegen |
| Ohne die Datei | Öffentliche Siteaccesses mit einem Pfadpräfix nehmen teil, jeder mit dem Knoten seiner Wurzel- oder Startseite |
| Kosten | Keine Datenbankabfrage pro Link; die Siteaccess-Tabelle wird einmal pro Anfrage gelesen (unter Exponential Velocity einmal pro Worker) |

> **Warnung**
> Die Knoten-IDs in der Konfiguration unterscheiden sich zwischen Installationen. Sie dürfen nie aus einer anderen
> Installation kopiert werden; Ihr technisches Team liest sie aus Ihren eigenen Siteaccess-Einstellungen.

## Wie ein Link seine Website findet

Kurz gefasst, nach der Funktionsseite:

1. Für ein Inhaltselement ist die Website diejenige, deren Wurzelordner auf dem Pfad des Inhalts am tiefsten liegt.
2. Ist das eine andere Website, wird die Adresse aus der eigenen Adresse dieser Website gebildet, ohne ihren
   ausgeblendeten Pfad.
3. Auf demselben Host bleibt der Link host-relativ, sodass Schema und Port erhalten bleiben; auf einem anderen Host wird
   er zu einer vollständigen Adresse wie `https://bold.example.com/services`.
4. Eine Website und ihre Übersetzungen teilen einen Teilbaum: Der aktuelle Siteaccess hat Vorrang, dann einer in der
   aktuellen Sprache, dann der erste in der Liste.

Die letzte Regel ist für mehrsprachige Marken wichtig: Ein deutschsprachiger Besucher, der einem Link in die andere Marke
folgt, bleibt auf Deutsch, wo diese Marke einen deutschen Siteaccess hat.

## Was unverändert bleibt

- Links innerhalb der aktuellen Website.
- Modul-Links wie die Anmeldeseite oder die Suche.
- Dateien und Design-Pfade.
- Jeder Link, der auf einem Siteaccess erzeugt wird, der nicht teilnimmt, und dazu gehört jeder Siteaccess der
  Administration.

## Was das Unternehmen verlangen sollte

- [ ] Ist `ezurlmultisite` in unserer Installation aktiv, und führt seine Konfiguration jede öffentliche Marke auf?
- [ ] Wurden die Knoten-IDs aus **unserer** Installation übernommen?
- [ ] Wurde nach dem Hinzufügen einer neuen Marke die Konfiguration aktualisiert, und wurden die Caches geleert?
- [ ] Erzeugen eigene Templates oder Erweiterungen Links selbst? Wenn ja, fragen sie ebenfalls die Erweiterung? (Die
      Funktionsseite zeigt Entwicklern unter „For extension developers“, wie das geht.)

Nach einer Änderung erzeugt das technische Team die Autoloads neu und leert den INI-, Template- und Inhalts-Cache, denn
kompilierte Templates behalten den alten Link-Code, bis sie erneut kompiliert werden. Unter Velocity erledigt
`./console exp:velocity deploy` all das (dieselbe Quelle).

## Tests vor dem Start

| Test | Erwartet |
|---|---|
| Eine Tag-Seite auf Marke A listet ein Element von Marke B; klicken Sie darauf | Die Seite von Marke B öffnet sich, unter der Adresse von Marke B |
| Eine Suche auf Marke A findet Inhalte von Marke B; klicken Sie darauf | Die Seite von Marke B, kein 404 |
| Dasselbe in jedem Sprach-Siteaccess | Der Besucher bleibt nach Möglichkeit in seiner Sprache |
| Ein Link innerhalb von Marke A | Unverändert, bleibt auf Marke A |
| Eine Vorschau in der Administration | Unverändert |

Nehmen Sie diese Tests in die Start-Checkliste jeder neuen Marke auf ([Checkliste für mehrere Marken](checkliste-fuer-mehrere-marken.md)).

## Verwandte Seiten

- [Inhalte zwischen Marken teilen](inhalte-zwischen-marken-teilen.md)
- [Links zu Inhalten einer anderen Website einer Multisite-Installation](../../../funktionen/6.0/websiteuebergreifende-links-im-multisite.md)
- [Mehrere Websites in einer Installation einrichten](../../ersteller/website-ersteller/multisite-einrichten.md#links-zwischen-den-websites)
- [Fehlerhafte Links und Inhaltsprüfungen](../../ersteller/website-ersteller/link-und-inhaltspruefungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
