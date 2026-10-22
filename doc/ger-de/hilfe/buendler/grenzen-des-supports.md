---
title: Grenzen des Supports für Bundles
audience: bundler, partner, hosting
category: bundling
order: 460
summary: Wer in einem Bundle was unterstützt - Sie, 7x, die Community, die Autoren von Erweiterungen - die drei Arten von Support, die das Projekt nennt, wie die Supportstufen für Partner hineinpassen, wohin Fehler- und Sicherheitsmeldungen gehen und eine Vorlage für die Supportaussage Ihres Bundles.
keywords: Support, Grenzen, Verantwortung, Community-Support, Partner-Support, bezahlter Support, Tickets, Reaktionszeit, Fehlermeldung, Sicherheitsmeldung, Supportaussage, support, boundaries, responsibility, community support, partner support, 7x, paid support, tickets, response time, bug report, security report, support statement, bundle
type: help
---
# Grenzen des Supports für Bundles

Diese Seite richtet sich an Bündler, die die Supportbedingungen ihres Bundles schreiben. Die Empfänger eines Bundles
rufen zuerst **Sie** an, gleich was kaputtgegangen ist. Legen Sie vorab fest, was Sie selbst erledigen, was Sie
weitergeben und wohin.

## Inhalt

1. [Die Beteiligten](#die-beteiligten)
2. [Der Support, den das Projekt nennt](#der-support-den-das-projekt-nennt)
3. [Die Grenze ziehen](#die-grenze-ziehen)
4. [Probleme weiterleiten](#probleme-weiterleiten)
5. [Eine Supportaussage](#eine-supportaussage)

## Die Beteiligten

| Beteiligter | Unterstützt |
|---|---|
| Sie, der Bündler | das Bundle: Build, Installer, Konfiguration, Ihre Erweiterungen, Ihr Seitenpaket, die Updates, die Sie liefern |
| 7x | Exponential, über Partner-Support und bezahlten Support |
| Die Community | jeden, ohne Verpflichtung |
| Autoren von Erweiterungen | ihre Erweiterungen, nach eigenem Ermessen |
| Ihre Zulieferer | das Betriebssystem, die Datenbank, das Panel |

## Der Support, den das Projekt nennt

| Art | Zusage zur Reaktion |
|---|---|
| Community-Support | "Keine: 'its developers are not obligated to provide free support...'" (seine Entwickler sind nicht verpflichtet, kostenlosen Support zu leisten) |
| Partner-Support | Reaktionszeiten je Stufe |
| Bezahlter Support von 7x | mit 7x vereinbart |

Quelle: [Supportstufen und Reaktionszeiten](../partner/support-stufen-und-reaktionszeiten.md#drei-arten-von-support).
Die Partnerstufen sind Student, Now, Starter, Basic, Professional, Silver, Gold und Platinum; die Tickets und
Reaktionszeiten je Stufe stehen unter [Partnerpläne und Preise](../partner/partnertarife-und-preise.md#die-tarife).
"Nicht veröffentlicht: ob Reaktionszeit die erste Antwort oder die Lösung meint, Geschäfts- oder Uhrstunden, die
Zeitzone": Lassen Sie sich das schriftlich geben, bevor Sie etwas zusagen, das davon abhängt.

## Die Grenze ziehen

| Problem | Ihres | Weitergeben |
|---|---|---|
| Das Bundle installiert oder startet nicht | ja | |
| Eine Einstellung, die Ihr Bundle vorgenommen hat | ja | |
| Ein Fehler in Exponential, den Sie auf einer reinen Installation nachstellen können | Einordnung, Umgehung | beim Projekt melden ([Korrekturen zurückgeben](bundle-korrekturen-zurueckgeben.md)) |
| Ein Sicherheitsproblem in Exponential | eindämmen, informieren | security@se7enx.com, vertraulich (`SECURITY.md`) |
| Eine Erweiterung Dritter | Einordnung | ihr Autor |
| Das Betriebssystem, der Datenbankserver | wie Ihre Bedingungen es sagen | sein Hersteller |
| Eigene Änderungen und Erweiterungen des Empfängers | wie Ihre Bedingungen es sagen | |

Stellen Sie das Problem auf einer reinen Installation nach, bevor Sie es beim Projekt melden: Tritt es ohne die
Änderungen Ihres Bundles nicht auf, ist es Ihres.

## Probleme weiterleiten

- **Fehler:** der Issue-Tracker von <https://github.com/se7enxweb/exponential> oder des Repositorys der Erweiterung.
  Nennen Sie die Version (`composer show se7enxweb/exponential`), PHP, die Engine und die Datenbank, wie es
  `SECURITY.md` für Sicherheitsmeldungen verlangt. Partner:
  [Fehler als Partner melden](../partner/fehler-gut-melden.md).
- **Sicherheit:** nie öffentlich; "Sie können innerhalb eines Werktags mit einer ersten Antwort rechnen"
  (`SECURITY.md`).
- **Fragen:** Foren <https://share.exponential.earth/forums>, Telegram <https://t.me/exponentialcms>.
- **Bezahlte Hilfe:** <https://se7enx.com/contact>.

## Eine Supportaussage

```text
Support for <Bundle> <version>
Supported by <your company>: installation, configuration, updates of <Bundle>, <your extensions>.
Contact: <address>, response <time> on <days>, <time zone>.
Exponential itself: we report reproducible problems to the Exponential project and deliver its fixes
in our next bundle release.
Not supported: <your exclusions>.
Supported versions: <Bundle> <versions> until <date>.
```

Veröffentlichen Sie sie neben dem Bundle und in seiner Dokumentation, für deutschsprachige Empfänger gern auch in
einer deutschen Fassung.

## Verwandte Seiten

- [Updates und Upgrade-Pfade](updates-und-upgrade-pfade.md)
- [Eskalationen und Sicherheitsprobleme](../partner/eskalationen-und-sicherheitsprobleme.md)
- [Supportstufen für Hoster](../hosting/geschaeft/support-stufen-fuer-exponential-hosting.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
