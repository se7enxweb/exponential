---
title: Demo-Modus in Bundles
audience: bundler, hosting
category: bundling
order: 340
summary: Wann ein Bundle den Demo-Modus (EXP_DEMO_MODE=1) verwenden darf und wann nie - was er ändert, wie ein Demo-Bundle ihn für jeden Prozess setzt, die Installation mit einem veröffentlichten Passwort, der Neuaufbau nach Zeitplan und wie Sie ihn aus jedem Bundle für echte Websites heraushalten.
keywords: Demo-Modus, öffentliche Demo, veröffentlichtes Passwort, Demo-Bundle, Umgebungsvariable, Neuaufbau, Hinweisbalken, demo mode, EXP_DEMO_MODE, public demo, published password, demo bundle, environment variable, PHP-FPM, Velocity envvars, container, cron, rebuild, banner
type: help
---
# Demo-Modus in Bundles

Diese Seite richtet sich an Bündler, die öffentliche Vorführungen bauen, und an alle anderen Bündler, damit sie den
Demo-Modus aus ihren Bundles heraushalten. Der Demo-Modus wird ausschließlich durch die Umgebungsvariable
`EXP_DEMO_MODE=1` eingeschaltet ([Demo-Modus](../demomodus.md)).

## Inhalt

1. [Was der Demo-Modus bewirkt](#was-der-demo-modus-bewirkt)
2. [Die Regel für Bundles](#die-regel-für-bundles)
3. [Ihn in einem Demo-Bundle setzen](#ihn-in-einem-demo-bundle-setzen)
4. [Mit einem veröffentlichten Passwort installieren](#mit-einem-veröffentlichten-passwort-installieren)
5. [Neuaufbau nach Zeitplan](#neuaufbau-nach-zeitplan)
6. [Prüfungen](#prüfungen)

## Was der Demo-Modus bewirkt

- Administratorpasswörter "lassen sich weder ändern noch zurücksetzen": Das Formular **Passwort ändern**,
  **Passwort vergessen?**, das Bearbeiten des Benutzers, `exp:resetuserpassword`, Webservices und Importe lehnen es für
  Administratoren ab.
- Jede Seite der Administration zeigt einen Hinweisbalken **Demo-Modus**.
- Die Installer akzeptieren ein veröffentlichtes, allgemein bekanntes oder kurzes Administratorpasswort (`publish`,
  `publishing`); ein leeres wird weiterhin abgelehnt.
- Er "macht die Website nicht schreibgeschützt: Ein Besucher kann Inhalte bearbeiten, Benutzer anlegen, Rollen und
  Einstellungen ändern. Deshalb wird eine Demo nach Zeitplan von Grund auf neu gebaut."

Quelle: [Demo-Modus](../demomodus.md), [Demo-Modus: der Upgrade-Hinweis](../../bc/6.0/demomodus.md).

## Die Regel für Bundles

| Bundle | Demo-Modus |
|---|---|
| Öffentliche Demo mit veröffentlichter Anmeldung | an, nur für die Prozesse dieser Demo |
| Messe- oder Vertriebs-Laptop mit veröffentlichter Anmeldung | an, wenn die Anmeldung veröffentlicht ist |
| Schulungsraum, in dem jeder Teilnehmer das Passwort kennt | an, nur wenn das Image nie zu einer echten Website wird |
| **Jedes Bundle für eine echte Website** | **nie** |

"Demo-Modus bedeutet, dass das Administratorpasswort allen bekannt ist." Weil er nur über die Umgebung wirkt ("Keine
INI-Einstellung, kein Anfrageparameter und keine Seite der Administration kann ihn einschalten"), trägt eine von einer
Demo kopierte Einstellungsdatei oder Datenbank ihn nicht weiter. Auch Ihre Images, Units und Panel-Vorlagen für echte
Websites dürfen ihn nicht setzen.

## Ihn in einem Demo-Bundle setzen

Jeder Prozess, der die Demo ausliefert oder pflegt, braucht ihn
([Demo-Modus für Hoster](../hosting/sicherheit/demomodus-fuer-hoster.md#sie-für-jeden-server-setzen)):

| Wo PHP läuft | Einstellung |
|---|---|
| PHP-FPM | `env[EXP_DEMO_MODE] = 1` im eigenen Pool der Demo |
| Apache | `SetEnv EXP_DEMO_MODE 1` im virtuellen Host der Demo |
| Velocity | `export EXP_DEMO_MODE=1` in den Site-envvars der Demo, `/etc/vc/sites-available/<site>.envvars`, oder in der Umgebung des Prozesses, der den Server dieser Installation startet; nie in der gemeinsamen `/etc/vc/envvars`, die ein Worker dafür ablehnt |
| Cron | `EXP_DEMO_MODE=1` am Anfang der Crontab des Demo-Benutzers |
| Container | `ENV EXP_DEMO_MODE=1` im Demo-Image oder `-e EXP_DEMO_MODE=1` nur für den Demo-Dienst |

"Nur genau der Wert `1`" schaltet ihn ein. `config.env.php` sagt ausdrücklich, der Demo-Modus "IS NOT A SETTING OF
THIS FILE" (ist keine Einstellung dieser Datei): Eine Konstante dort bewirkt nichts (`config.env.php.example`).

## Mit einem veröffentlichten Passwort installieren

```bash
EXP_DEMO_MODE=1 php bin/php/console exp:install --db=sqlite --password='<the published demo password>'
```

Dasselbe funktioniert für den Kickstarter (`[site_admin] Password`) und den Einrichtungsassistenten. "Gelockert wird
nur das Administratorpasswort der neuen Installation."

## Neuaufbau nach Zeitplan

"Eine Demo, die nach Zeitplan neu gebaut wird (alle paar Stunden, aus dem aktuellen Code), tut das in ihrem
Build-Skript: mit der Variable installieren, die neue Website einwechseln, die Variable in der PHP-Umgebung und der
Crontab der laufenden Website behalten. Das Skript darf die Variable nicht in `config.php`, `settings/` oder in irgendetwas
schreiben, das auf andere Websites kopiert wird." `exp:install` auf SQLite ist für solche Demos "die übliche Wahl"
([4.7](../../installation/04-installationsweg-waehlen.md)).

## Prüfungen

- [ ] Die Administration der Demo zeigt den Hinweisbalken **Demo-Modus**.
- [ ] Bundles für echte Websites zeigen ihn nicht, und keine ihrer Dateien erwähnt `EXP_DEMO_MODE`.
- [ ] Die Seite der Demo sagt klar, dass sie öffentlich ist und regelmäßig zurückgesetzt wird ("Geben Sie hier nichts
      Privates ein").

## Verwandte Seiten

- [Bundles für Schulung und Demo](bundles-fuer-schulung-und-demo.md)
- [FAQ zum Demo-Modus](../demomodus-faq.md), [Probleme mit dem Demo-Modus](../hosting/runbooks/probleme-mit-dem-demomodus.md)
- [Sicherheit bei Live-Demos](../fuersprecher/sicherheit-bei-live-demos.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
