---
title: Site-Pakete für Lernende
audience: student
category: learning
order: 14
summary: Was Lernende über Site-Pakete wissen müssen - was der Installer installiert, das Standardpaket sevenx_multisite mit seinen zwei Demo-Websites, die leere Variante ohne Demo-Inhalte, die CJW-Starter-Website, wie Sie mit exp:install eines wählen und welche Seiten des Bereichs Site-Pakete Sie lesen, wenn Sie tiefer einsteigen möchten.
keywords: site-paket, sevenx_multisite, sevenx_multisite_clean, cjw_multisite, ezpkg, paketserver, demo-inhalte, exp:install --package, Fit & Healthy, Bold Agency, lernende, site package, package server, demo content, learner
type: help
---
# Site-Pakete für Lernende

Wenn Sie Exponential installieren, wird die neue Website nicht nur aus Code aufgebaut. Sie entsteht aus
**Site-Installer-Paketen**: Archiven, die Inhaltsklassen, Inhalte, Einstellungen, Rollen und Installationsskripte
enthalten ([Site-Pakete: hier beginnen](../seitenpakete/README.md)). Wenn Sie wissen, welches Paket Sie installiert
haben, wissen Sie, was Ihre Lernwebsite enthält und warum.

## Inhalt

1. [Pakete in einer Minute](#pakete-in-einer-minute)
2. [Der Standard: sevenx_multisite](#der-standard-sevenx_multisite)
3. [Weitere Pakete, die sich zum Lernen eignen](#weitere-pakete-die-sich-zum-lernen-eignen)
4. [Ein Paket bei der Installation wählen](#ein-paket-bei-der-installation-wählen)
5. [Was Sie im Bereich Site-Pakete lernen](#was-sie-im-bereich-site-pakete-lernen)

## Pakete in einer Minute

- Ein **Paket** ist ein Ordner mit einer `package.xml` und den Dateien, die sie nennt, ausgeliefert als ein Archiv mit
  der Endung `.ezpkg`.
- Ein **Site-Paket** ist ein Paket vom Typ `site`. Es benötigt andere Pakete (Inhaltsklassen, Inhalte) und kann
  Installationsskripte enthalten, die Siteaccesses, Erweiterungen, Rollen und Voreinstellungen einrichten.
- Die Installer (der Setup-Assistent, der Kickstarter und `exp:install`) lesen Pakete von einem **Paketserver**,
  [exponential.packages.exponential.earth](https://exponential.packages.exponential.earth/).
- Der Index von 6.0.15 bietet zwölf Site-Pakete an.

Quelle: [Site-Pakete: hier beginnen](../seitenpakete/README.md#die-kurzfassung).

## Der Standard: sevenx_multisite

`sevenx_multisite` ist das Paket, das installiert wird, wenn nichts anderes gewählt ist. Es richtet in einer
Installation zwei öffentliche Websites ein, **Fit & Healthy** und **Bold Agency**, dazu die Siteaccesses für die
Administrationsoberfläche und den Editor, mit Seitenlayouts aus den Erweiterungen `explayouts`
([sevenx_multisite](../seitenpakete/sevenx-multisite/README.md#in-einem-absatz)).

Was das für Ihr Lernen bedeutet:

| Sie bekommen | Nützlich für |
|---|---|
| Demo-Artikel, Rezepte und Bilder | Übungen zu Bearbeiten, Suche und Übersetzung an realistischen Inhalten |
| Die Siteaccesses `site`, `bold` und `bold_ger` | Sehen, wie eine Installation mehrere Websites und Sprachen bedient |
| Veröffentlichte Layouts mit Zonen, Blöcken und Regeln | Das [Projekt zu Layouts](projekt-04-eine-seite-mit-layouts.md) |
| Rollen für die Benutzer Anonymous, Member, Editor und Partner | Das [Projekt zu Rollen](projekt-09-rollen-fuer-einen-eingeschraenkten-redakteur.md) |
| Viele aktive Erweiterungen | Sehen, was Erweiterungen hinzufügen, unter **Setup > Extensions** |

Quelle: [sevenx_multisite: was eine Installation Ihnen gibt](../seitenpakete/sevenx-multisite/README.md#was-eine-installation-ihnen-gibt).

## Weitere Pakete, die sich zum Lernen eignen

| Paket | Was es installiert | Warum Lernende es wählen könnten |
|---|---|---|
| `sevenx_multisite_clean` | Dasselbe Site-Paket ohne die Demo-Artikel, Rezepte und Bilder | Um in [Projekt 2](projekt-02-eine-kleine-website-struktur.md) eine Website-Struktur aus dem Nichts aufzubauen |
| `cjw_multisite` | Die CJW-Starter-Website, mit Starterinhalten auf Deutsch und Englisch | Um zwei Pakete zu vergleichen und eine Website zu studieren, die um eine Theme-Erweiterung herum gebaut ist |
| `sevenx_site`, `sevenx_site_clean` | Einfachere Site-Pakete | Um eine kleinere Installation zu sehen |

Quellen: [Die leere Variante und sevenx_site](../seitenpakete/sevenx-multisite/die-clean-variante-und-sevenx-site.md),
[cjw_multisite](../seitenpakete/cjw-multisite/README.md).

## Ein Paket bei der Installation wählen

`exp:install` nimmt das Paket mit `--package` entgegen ([Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md)).
Die Seite zu cjw_multisite zeigt dieses Beispiel:

```bash
php bin/php/console exp:install --apply --package=cjw_multisite --languages=eng-US,ger-DE
```

Fügen Sie die Optionen Ihrer ersten Installation hinzu (`--random-password`, `--url=...`) und `--force`, wenn Sie eine
bestehende Lernwebsite ersetzen ([Die Lernwebsite zurücksetzen und neu aufbauen](die-lernwebsite-zuruecksetzen-und-neu-aufbauen.md)).
Im Setup-Assistenten wird das Paket auf der Seite **Site Paket** gewählt.

## Was Sie im Bereich Site-Pakete lernen

Der Bereich ist für Administratoren, Entwickler und Hoster geschrieben; für Lernende sind diese Seiten eine gute
Reihenfolge:

1. [Was ein Site-Installer-Paket ist](../seitenpakete/grundlagen/was-ein-seiteninstallationspaket-ist.md)
2. [Die Datei .ezpkg](../seitenpakete/grundlagen/die-datei-ezpkg.md) und die
   [Referenz zu package.xml](../seitenpakete/grundlagen/referenz-zu-package-xml.md)
3. [Was Sie nach der Installation von sevenx_multisite haben](../seitenpakete/sevenx-multisite/was-sie-nach-der-installation-von-sevenx-multisite-haben.md)
4. [sevenx und cjw im Vergleich](../seitenpakete/cjw-multisite/sevenx-multisite-und-cjw-multisite-im-vergleich.md)
5. [Was auf dem Paketserver liegt](../seitenpakete/paketserver/was-auf-dem-paketserver-liegt.md)
6. [Ein eigenes Site-Paket erstellen](../seitenpakete/eigene/README.md), wenn Sie eine Website gebaut haben, die es
   wert ist, geteilt zu werden
7. [Ihr Site-Paket beitragen](../seitenpakete/ihr-seitenpaket-beitragen.md)

Der Kurs der Trainer [Multisite und Site-Pakete](../trainer/multisite-und-pakete/README.md) deckt dasselbe Gebiet
mit Übungen ab.

## Verwandte Seiten

- [Ihre erste Website auf drei Wegen](ihre-erste-website-auf-drei-wegen.md)
- [Glossar der Site-Pakete](../seitenpakete/glossar-der-seitenpakete.md)
- [Siteaccesses und Einstellungen verstehen](siteaccesses-und-einstellungen-verstehen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
