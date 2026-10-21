---
title: Über ein Release einer Erweiterung berichten
audience: press
category: press-media
order: 107
summary: Wie Sie über ein Release einer der Erweiterungen rund um Exponential berichten - was eine Erweiterung ist, wo ihre Release Notes und ihre Version stehen, wie sich ihre Version zu der des Kernels verhält und wie Sie die Projekte dahinter nennen.
keywords: Erweiterung, Release einer Erweiterung, eztags, cjw_newsletter, ezjscore, expai, Exponential Layouts, Composer, ezinfo.php, extension.xml, Release Notes, Nennung, extension, extension release, release notes, credit
type: help
---
# Über ein Release einer Erweiterung berichten

Diese Seite ist für Fachpresse, Blogger und Creator, die über ein Release einer Erweiterung von Exponential schreiben
statt über Exponential selbst. Erweiterungen haben eigene Repositorys, Versionsnummern und Release Notes. Ein guter
Beitrag hält die Version der Erweiterung von der des Kernels getrennt und nennt die Menschen, deren Arbeit sie trägt.

## Inhalt

1. [Was eine Erweiterung ist](#was-eine-erweiterung-ist)
2. [Wo Sie die Fakten finden](#wo-sie-die-fakten-finden)
3. [Versionen von Erweiterungen und Versionen des Kernels](#versionen-von-erweiterungen-und-versionen-des-kernels)
4. [Nennung und Namen](#nennung-und-namen)
5. [Eine kurze Vorlage](#eine-kurze-vorlage)

## Was eine Erweiterung ist

Erweiterungen fügen dem Kernel Funktionen hinzu. "Every extension except the four kernel-shipped ones is a Composer
package of type `ezpublish-legacy-extension`", installiert nach `extension/<name>`, und "Being on disk does not make an
extension active" ([Den Code beschaffen](../../installation/03-den-code-beschaffen.md#35-extensions-sind-composer-pakete)). Die
`composer.json` des Kernels verlangt 66 se7enxweb-Pakete ([Wer ist 7x?, 7x in Zahlen](../wer-ist-7x.md#7x-in-zahlen));
der [Leitfaden zu Extensions](../../anleitungen/extensions.md) erklärt, wie sie gefunden, installiert und veröffentlicht werden.

„Eine neue Version der Erweiterung X“ bedeutet also meist: Inhaber von Websites können dieses eine Paket aktualisieren;
für sie ändert sich nichts, bis sie es tun.

## Wo Sie die Fakten finden

| Fakt | Wo |
|---|---|
| Neuestes getaggtes Release jeder Erweiterung | Der Index [Release Notes der Erweiterungen ...](../../../changelogs/extensions/README.md) |
| Was ein Release enthält | Die Seite der Erweiterung in diesem Index, zum Beispiel [eztags](../../../changelogs/extensions/eztags.md) |
| Notes, die im eigenen Repository der Erweiterung geführt werden | expai (Exponential Intelligence) und expqueue, verlinkt vom Index |
| Die auf einer Website installierte Version | `ezinfo.php` und `extension.xml` der Erweiterung sowie **Setup > Extensions** ([Index, How to find the version you run](../../../changelogs/extensions/README.md#how-to-find-the-version-you-run)) |
| Welche Version die Entwicklungslinie des Kernels verlangt | [Changelog 6.0.15, benötigte Pakete](../../../changelogs/6.0/6.0.15.md#required-packages-root-composerjson-2026-10-10-to-2026-10-12) |
| Die Geschichte einer Erweiterung über die Zeit | [Chroniken der Erweiterungen](../../../eng-us/history/extensions/README.md) |

Die Release-Regel für Erweiterungen ist schriftlich festgehalten: Die Version wird in `ezinfo.php` und `extension.xml`
geschrieben, committet und dann getaggt; eine falsche Version wird durch die nächste korrigiert
([Leitfaden zu Extensions, Veröffentlichen](../../anleitungen/extensions.md#7-die-extension-veröffentlichen)).

## Versionen von Erweiterungen und Versionen des Kernels

| Fehler | Warum er falsch ist | Richtig |
|---|---|---|
| „eztags 2.4.11 ist Teil von Exponential 6.0.14“ | Versionen von Erweiterung und Kernel sind unabhängig | „eztags 2.4.11, eine Erweiterung für Exponential“ |
| „Exponential bekommt ein Newsletter-Update“ | Der Kernel hat sich nicht geändert | „Die Erweiterung cjw_newsletter hat ein neues Release“ |
| „Jede Website hat jetzt Funktion Y“ | Erweiterungen müssen aktualisiert werden, und manche sind inaktiv, bis man sie einschaltet | „Websites, die die Erweiterung aktualisieren, bekommen Y“ |
| Die Nummer einer Erweiterung mit der des Kernels vergleichen | Sie zählen getrennt; die letzte Stelle zählt immer weiter | Drucken Sie jede Nummer, wie sie ist ([Wie Versionsnummern funktionieren](wie-versionsnummern-funktionieren.md)) |

Vier Erweiterungen werden im Kernel selbst mitgeliefert ([Den Code beschaffen](../../installation/03-den-code-beschaffen.md#35-extensions-sind-composer-pakete)).
Eine davon, ezjscore, hat außerdem eigene Release Notes, mit 1.5.5 als neuestem Release im Index am 18. Oktober 2026
([Release Notes von ezjscore](../../../changelogs/extensions/ezjscore.md)). Zitieren Sie im Zweifel die Version von der
Seite mit den Release Notes der Erweiterung und verlinken Sie sie.

## Nennung und Namen

- Viele Erweiterungen entstanden in den Jahren von eZ Publish und tragen historische Namen wie `eztags` oder `ezjscore`.
  Das sind Bezeichner im Code, und sie werden nie umbenannt ([Marken- und Namensregeln](../geschaeft/marken-und-namensregeln.md#bezeichner-im-code-bleiben-wie-sie-sind)).
- Nennen Sie die Herkunft, wo die Quellen sie nennen: `cjw_newsletter` stammt von CJW Network; Exponential Layouts
  bringt "the Netgen Layouts concept to the Exponential kernel" ([Wer ist 7x?, Beitragende und Freunde](../wer-ist-7x.md#mitwirkende-und-freunde-des-projekts)).
- Nennen Sie keinen einzelnen Entwickler, wenn er nicht zugestimmt hat; nennen Sie das Projekt oder die Organisation
  ([Aussagen zuordnen](aussagen-zuordnen.md)).

## Eine kurze Vorlage

```text
<Name der Erweiterung> <Version> für Exponential veröffentlicht

<Name der Erweiterung>, eine Erweiterung für das Content-Management-System Exponential, hat ein neues
Release, <Version>, getaggt am <Datum> (<Link zu den Release Notes>). Sie <ein Satz: was die Erweiterung tut>.
Das Release <zwei oder drei Änderungen, in den Worten der Release Notes>.

Websites aktualisieren sie als ein Composer-Paket; die Release Notes nennen, was zu prüfen ist. Das
Repository der Erweiterung ist <Link>. <Zeile zur Herkunft, falls die Quellen eine nennen.>
```

## Verwandte Seiten

- [Über ein Release berichten](ueber-ein-release-berichten.md)
- [Wo Changelogs und Release Notes stehen](wo-changelogs-und-release-notes-stehen.md)
- [Vorlagen für Release-Beiträge](vorlagen-fuer-release-beitraege.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
