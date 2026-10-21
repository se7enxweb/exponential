---
title: Die Community um Hilfe bitten
audience: user, developer, designer, app-creator
category: extension-development
order: 200
summary: Wo Sie eine Entwicklungsfrage stellen (Foren, Chat, Issue-Tracker, das eigene Repository der Erweiterung), wie Sie eine Frage schreiben, die andere beantworten können, was Sie nie veröffentlichen, und wie Sie ein Sicherheitsproblem vertraulich melden.
keywords: Hilfe, fragen, Frage, Forum, Telegram, Issue, minimales Beispiel, Fehlerbericht, Sicherheit, Sicherheitslücke, vertrauliche Meldung, Community, help, ask, question, github issues, minimal example, bug report, security, vulnerability, private disclosure, community
type: help
---
# Die Community um Hilfe bitten

Teil 1, Kapitel 20. Früher oder später kommen Sie nicht weiter. Die Menschen, die Exponential und seine Erweiterungen
bauen, beantworten Fragen an öffentlichen Orten, und eine gut gestellte Frage bekommt in der Regel eine Antwort.
Dieses Kapitel sagt Ihnen, wo Sie fragen, wie Sie fragen und was Sie nie veröffentlichen.

## Bevor Sie fragen

Die meisten Fragen beantwortet eine dieser Quellen, in dieser Reihenfolge:

1. Der Debug-Bericht und `var/log/error.log` ([Debug-Ausgabe und Logs für Entwickler](debug-ausgabe-und-logs-fuer-entwickler.md)).
2. Die Suche des Hilfezentrums: Geben Sie die Klasse, die Einstellung oder die Fehlermeldung ein.
3. Die eigene `README.md` und das `doc/` der Erweiterung.
4. `git log -S "<the text of the error>"` im Kernel oder in der Erweiterung: Vielleicht wurde die Stelle kürzlich
   geändert, mit einer Nachricht, die erklärt, warum.
5. Die Foren und eZpedia, durchsucht nach der Fehlermeldung.

Wenn keine davon die Frage beantwortet, fragen Sie. Schreiben Sie in Ihre Frage, was Sie schon versucht haben; das
erspart allen eine Runde.

## Wo Sie fragen

| Ihre Frage betrifft | Fragen Sie in |
|---|---|
| Wie man etwas mit den APIs des Kernels macht | [Foren: Developer](https://share.exponential.earth/forums/developer) |
| Eine bestimmte Erweiterung | In den GitHub-Issues der Erweiterung (wenn sie gepflegt wird) oder in [Foren: Extensions](https://share.exponential.earth/forums/extensions) |
| Templates, Overrides, Designs | [Foren: Setup & design](https://share.exponential.earth/forums/setup-design) |
| Eine kurze Frage, eine Diskussion | Der Telegram-Kanal [t.me/exponentialcms](https://t.me/exponentialcms) und die anderen Kanäle, die auf [Share Exponential](share-exponential-earth.md) aufgeführt sind |
| Einen Fehler im Kernel, in der Administration oder in den mitgelieferten Erweiterungen | [github.com/se7enxweb/exponential/issues](https://github.com/se7enxweb/exponential/issues) |
| Einen Fehler im Anwendungsserver Velocity | [github.com/se7enxweb/exponential-velocity/issues](https://github.com/se7enxweb/exponential-velocity/issues) |
| Das Projekt, die Website, das Ökosystem | [github.com/se7enxweb/exponential-community/issues](https://github.com/se7enxweb/exponential-community/issues) |
| Ein Sicherheitsproblem | **Vertraulich**, siehe unten |

Die Administration hat eine Anleitung für Fehlerberichte: **Hilfe > Fehler melden** (`exphelp/report`) fügt eine
Zusammenfassung der Installation ein und öffnet ein vorbereitetes GitHub-Issue. Siehe
[Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md).

Bevor Sie einen Fehler melden, bedenken Sie: Manches Verhalten ist beabsichtigt oder Auslegungssache, und andere Sites
verlassen sich darauf. Eine Änderung daran braucht eine klare Begründung und oft mehrere Runden der Prüfung; eine
Option ist besser als ein neuer Standard
([Negative Funktionen](../../projekt/unterstuetzung-erhalten.md#negative-funktionen)).

## Wie Sie eine Frage schreiben, die andere beantworten können

Eine gute Frage hat fünf Teile:

```text
What I want to do:   <one sentence>
What I did:          <the code or template, as small as possible>
What I expected:     <the result you wanted>
What happened:       <the exact error message, the debug report lines>
My environment:      Exponential <version>, PHP <version>, database <type>, design <name>
```

(Auf Deutsch: was ich tun möchte, was ich getan habe, was ich erwartet habe, was passiert ist, meine Umgebung.)

- **Machen Sie es klein.** Kürzen Sie Ihren Code auf das kleinste Stück, das das Problem noch zeigt. Oft finden Sie
  die Antwort schon dabei.
- **Fügen Sie Text ein, keine Screenshots** von Code und Fehlern, in Codeblöcken, damit andere sie kopieren und
  durchsuchen können.
- **Nennen Sie Versionen.** `./console --version` gibt die Version von Exponential aus, `php -v` die PHP-Version.
  **Setup > Systeminformationen** hat **Bericht als Text kopieren**, das Passwörter weglässt.
- **Sagen Sie, was Sie versucht haben** und was jeder Versuch bewirkt hat.
- **Kommen Sie zurück.** Beantworten Sie Rückfragen, und wenn das Problem gelöst ist, schreiben Sie, wie. Die nächste
  Person mit demselben Problem findet Ihren Beitrag.

## Was Sie nie veröffentlichen

- Passwörter, API-Schlüssel, Tokens, den Inhalt von `settings/override/` oder `config.php`.
- Datenbank-Dumps, Benutzerlisten, E-Mail-Adressen oder sonstige personenbezogene Daten der Benutzer oder Kunden Ihrer
  Website.
- Interne Hostnamen und IP-Adressen Ihrer Server.
- Code, den Sie nicht weitergeben dürfen (proprietären Code eines Kunden). Reduzieren Sie ihn stattdessen auf ein
  neutrales Beispiel.

Prüfen Sie die Fehlerzeilen, die Sie einfügen: Manche enthalten Pfade oder Benutzernamen. Ersetzen Sie diese durch
Platzhalter.

## Sicherheitsprobleme: vertraulich

Ein Sicherheitsproblem (ein Weg, Daten ohne Berechtigung zu lesen, Code auszuführen oder ein Konto zu übernehmen) wird
**nie** in einem öffentlichen Tracker oder Forum gemeldet. Die `SECURITY.md` des Kernels sagt, wie es geht: per E-Mail
an `security@se7enx.com` oder über die vertrauliche Meldung von Sicherheitslücken (private vulnerability reporting)
von GitHub im Repository. Sie erfahren, ob die Meldung angenommen wird, und werden auf dem Laufenden gehalten, während
eine Korrektur vorbereitet wird. Bei einer Erweiterung eines Drittanbieters suchen Sie nach deren eigener
`SECURITY.md`; gibt es keine, nehmen Sie vertraulich Kontakt zu den Maintainern auf, bevor Sie öffentlich etwas sagen.

## Geben Sie etwas zurück

Sobald Sie nicht mehr feststecken, steckt jemand anderes fest. Beantworten Sie jede Woche eine Frage in dem Forum, von
dem Sie am meisten gelernt haben.
[Die Community durch Code wachsen lassen](../teil-5-erweitern-forken-zurueckgeben/die-community-durch-code-wachsen-lassen.md) nennt
weitere Möglichkeiten zu helfen.

## Sie haben Teil 1 abgeschlossen

Sie wissen, was eine Erweiterung ist, Sie haben eine Entwicklungsinstallation, Sie können den Code und die Logs lesen,
und Sie wissen, wo Sie Ideen finden und wie Sie sie fair wiederverwenden. Machen Sie weiter mit
[Teil 2: Der Builder](../teil-2-builder/README.md).

## Verwandte Seiten

- Zurück: [Debug-Ausgabe und Logs für Entwickler](debug-ausgabe-und-logs-fuer-entwickler.md)
- Weiter: [Teil 2: Der Builder: Einstellungen, Templates und Overrides](../teil-2-builder/README.md)
- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [Gute Issues schreiben](../teil-5-erweitern-forken-zurueckgeben/gute-issues-schreiben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
