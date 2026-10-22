---
title: Private Bereiche mit Rollen und Sektionen
audience: individual, website-creator, customer
category: personal-sites
order: 1702
summary: Wie eine Person in Exponential einen Familien- oder Mitgliederbereich privat macht - wie Sektionen und die Rolle Anonymous bestimmen, was Besucher sehen, die Sektion Restricted und die Gruppen Members und Partners, die das Standardpaket anlegt, ein privater Bereich Schritt für Schritt, eine vollständig private Website mit RequireUserLogin und wie Sie das als Fremder testen.
keywords: privater Bereich, nur für Mitglieder, Sektionen, Sektion Restricted, Sektion Standard, Rolle Anonymous, Rollen, Richtlinien, content/read, Benutzergruppen, Members, Partners, RequireUserLogin, Familienseite, als Besucher testen, Suchmaschinen, private area, members only, sections, roles, policies, user groups, test as visitor
type: help
---
# Private Bereiche mit Rollen und Sektionen

Diese Seite ist für Privatpersonen, die einen Teil ihrer Website, oder die ganze, nur für angemeldete Personen
sichtbar machen möchten: einen Familienbereich, eine Ecke für Mitglieder, Entwürfe für Freunde. Sie erklärt den
Mechanismus einmal und gibt dann zwei Rezepte.

## Inhalt

1. [Wie Sichtbarkeit funktioniert](#wie-sichtbarkeit-funktioniert)
2. [Was das Standardpaket bereits eingerichtet hat](#was-das-standardpaket-bereits-eingerichtet-hat)
3. [Rezept 1: ein privater Bereich](#rezept-1-ein-privater-bereich)
4. [Rezept 2: eine vollständig private Website](#rezept-2-eine-vollständig-private-website)
5. [Als Fremder testen](#als-fremder-testen)
6. [Fehler, die Sie vermeiden sollten](#fehler-die-sie-vermeiden-sollten)

## Wie Sichtbarkeit funktioniert

Drei Dinge wirken zusammen ([Sektionen](../../../anleitungen/sektionen.md), [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)):

| Ding | Was es ist |
|---|---|
| **Sektion** | Eine Kennzeichnung eines Teils des Inhaltsbaums. Jedes Objekt gehört zu genau einer Sektion. |
| **Rolle** | Ein Satz von Berechtigungen (Richtlinien), etwa „Inhalte in Sektion X lesen". |
| **Benutzergruppe** | Wo Konten liegen. Rollen werden Gruppen (oder einzelnen Benutzern) zugewiesen. |

Die entscheidende Tatsache: Die Standardrolle **Anonymous** darf Inhalte nur in der Sektion **Standard** lesen.
Inhalte in jeder anderen Sektion sind für nicht angemeldete Besucher unsichtbar, und sie werden beim Öffnen zur
Anmeldung aufgefordert ([Sektionen, Kurz gesagt](../../../anleitungen/sektionen.md)).

## Was das Standardpaket bereits eingerichtet hat

Der Installer des Standard-Seitenpakets (`sevenx_multisite`) legt mehr an als eine leere Installation
(`var/storage/packages/7x/sevenx_multisite/settings/sevenx-multi-site-installer.php`):

| Angelegt | Zweck |
|---|---|
| Sektion **Restricted** | Inhalte, die anonyme Besucher nicht lesen können |
| Benutzergruppe **Members** (die umbenannte Gruppe „Guest accounts") mit der Rolle **Member** | Registrierte Besucher; dürfen in der Sektion Standard kommentieren und in Foren schreiben |
| Benutzergruppe **Partners** mit der Rolle **Partner** | Dürfen die Sektion Restricted lesen und dort schreiben |
| Der Ordner *Archives* auf oberster Ebene in Restricted | Ein Beispiel für Inhalte, die nur Partners sehen |

Auf einer Standardinstallation gibt es also bereits „Personen, die den privaten Bereich sehen dürfen": die Gruppe
Partners. Für eine Familienseite möchten Sie meist eigene Namen; das folgende Rezept legt sie an, aber Restricted
und Partners wiederzuverwenden funktioniert ebenfalls.

## Rezept 1: ein privater Bereich

1. **Den Bereich anlegen**: einen *Folder* namens etwa „Familie" unter der Startseite, mit allem Privaten darunter.
2. **Eine Sektion** „Familie" unter **Setup > Sektionen** anlegen (oder *Restricted* wiederverwenden)
   ([Sektionen und Objektzustände](../../ersteller/website-ersteller/sektionen-und-objektzustaende.md)).
3. **Den Ordner der Sektion zuweisen**, mit seinem Teilbaum. Neue Inhalte, die darunter angelegt werden, erben die
   Sektion.
4. **Eine Benutzergruppe** „Familie" unter **Benutzer** anlegen.
5. **Eine Rolle** „Familie lesen" mit `content/read`, beschränkt auf die Sektion Familie, und `user/login` für den
   öffentlichen Siteaccess anlegen. Für Beitragende eine zweite Rolle „Familie schreiben" mit `content/create` und
   `content/edit` für die Klassen, die sie hinzufügen dürfen (Blogbeiträge, Bilder, Kommentare), beschränkt auf die
   Sektion Familie.
6. **Die Rollen** der Gruppe Familie **zuweisen** ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).
7. **Die Konten der Verwandten in die Gruppe Familie stellen** ([Familienmitglieder einladen](familienmitglieder-einladen.md)).
8. **Als Fremder testen** (siehe unten).

Der Ordner Familie kann für Besucher weiterhin im Menü erscheinen, als Link, der zu einer Anmeldung führt. Wenn
Fremde nicht einmal seinen Namen sehen sollen, blenden Sie ihn im Template für anonyme Besucher aus den Menüs aus
oder verwenden Sie Rezept 2.

## Rezept 2: eine vollständig private Website

Lassen Sie den öffentlichen Siteaccess für alles eine Anmeldung verlangen, indem Sie seiner `site.ini.append.php`
Folgendes hinzufügen:

```ini
[SiteAccessSettings]
RequireUserLogin=true
```

Jede Seite fragt dann zuerst nach einer Anmeldung; erreichbar sind nur die Seiten für Anmeldung, Zurücksetzen des
Passworts und (falls erlaubt) Registrierung
([Mitgliederregistrierung und Bereiche nur für Mitglieder](../../ersteller/website-ersteller/mitgliederregistrierung-und-mitgliederbereiche.md#ein-bereich-nur-für-mitglieder)).
Kombinieren Sie das mit einer Rolle, mit der die Gruppe Familie die Sektion Standard lesen darf. Das ist der
einfachste Weg, eine ganze Familienseite aus Suchmaschinen und von Fremden fernzuhalten.

## Als Fremder testen

Nach jeder Änderung an Sektionen oder Rollen:

- [ ] Öffnen Sie ein privates Browserfenster (nicht angemeldet) und rufen Sie die Adresse des privaten Bereichs auf:
  Sie müssen zur Anmeldung aufgefordert werden.
- [ ] Öffnen Sie den Download-Link einer privaten Seite (einen Dateianhang): Sie müssen zur Anmeldung aufgefordert
  werden.
- [ ] Durchsuchen Sie die Website nach einem Wort von einer privaten Seite: kein Ergebnis.
- [ ] Melden Sie sich als verwandte Testperson an (nicht als Sie selbst, der Administrator): Der Bereich öffnet sich;
  Seiten nur für Administratoren nicht.
- [ ] Prüfen Sie die RSS-Exporte: Keiner darf private Inhalte auflisten.

Leeren Sie die Caches, wenn eine Änderung scheinbar nicht wirkt ([Caches](../../../anleitungen/caches.md)).

> **Wichtig**
> Bilddateien werden vom Webserver direkt ausgeliefert: Die mitgelieferten Rewrite-Regeln reichen
> `var/.../storage/images/...` unmittelbar durch (`.htaccess_root` und das nginx-Beispiel unter
> [Die Website ausliefern](../../../installation/08-die-website-ausliefern.md)). Seiten und Datei-Downloads sind durch Ihre
> Rollen geschützt; eine Bilddatei nicht. Wer die genaue Adresse eines privaten Bildes hat (weil eine verwandte
> Person sie zum Beispiel in eine Nachricht kopiert hat), kann es ohne Anmeldung öffnen. Die Adresse ist lang und
> nicht zu erraten, aber behandeln Sie private Fotos als „nicht gelistet", nicht als „verschlossen", und laden Sie
> nie ein Foto hoch, das Schaden anrichten würde, wenn es nach außen dränge.

## Fehler, die Sie vermeiden sollten

| Fehler | Folge |
|---|---|
| Nur als angemeldeter Administrator testen | Sie sehen alles; Sie lernen nichts |
| Inhalte in den privaten Ordner verschieben, ohne dass sich ihre Sektion ändert | Sie bleiben lesbar; prüfen Sie nach dem Verschieben die Sektion |
| Ein RSS-Export des privaten Blogs | Der Feed veröffentlicht private Beiträge für jeden, der die Adresse kennt |
| Ein gemeinsames Familienkonto | Sie können einer einzelnen Person den Zugriff nicht entziehen |
| Verwandten die Rolle Editor oder Administrator geben | Sie können weit mehr ändern, als Sie beabsichtigen |

## Verwandte Seiten

- [Familienseiten](README.md)
- [Sektionen und Objektzustände](../../ersteller/website-ersteller/sektionen-und-objektzustaende.md)
- [Besucherberechtigungen](../../ersteller/website-ersteller/rechte-fuer-besucher.md)
- [Inhalte nur für Mitglieder](../../unternehmen/mitglieder/inhalte-nur-fuer-mitglieder.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
