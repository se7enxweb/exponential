---
title: "FAQ zum Request Shield"
audience: admin, hosting, developer
category: administration
order: 80
summary: Kurze Antworten auf die Fragen, die zum Request Shield gestellt werden - was er kostet, ob er eine Firewall oder ein CDN ersetzt, was Besucher sehen, Datenschutz, Shared Hosting, PHP-Versionen, Lizenzen, Ausschalten und welche Installationen ihn bekommen.
keywords: Request Shield, FAQ, Fragen, Kosten, Leistung, Firewall, WAF, CDN, Captcha, Browserprüfung, Datenschutz, IP-Adresse, DSGVO, Shared Hosting, PHP 7.4, PHP 8.5, Lizenz, MIT, GPL, ausschalten, pausieren, Velocity, PHP-FPM, 6.0.15, composer, questions, privacy, license
type: help
---
# FAQ zum Request Shield

## Was Sie am Ende haben

Eine Antwort auf Ihre Frage oder einen Link auf die Seite, die sie hat.

## Was er ist

**Was ist der Request Shield?** Ein Filter vor der Website, der jede Anfrage entscheidet, bevor Exponential startet:
Er lässt echte Besucher durch und weist Scanner, Anfragefluten, verirrte Formularsendungen und Anfragen nach internen
Dateien ab. Es ist die Bibliothek [request-shield](https://github.com/cjw-network/request-shield) 0.4.0 von JAC
Systeme GmbH (CJW Network), für Exponential verpackt als Extension `exp_request_shield` 0.0.1.1.

**Ersetzt er eine Netzwerk-Firewall oder ein CDN?** Nein. Er arbeitet auf der Ebene der Web-Anfragen, in PHP, und
kennt die Adressen von Exponential. Eine Netzwerk-Firewall, ein CDN oder die eigenen Limits des Webservers haben vor
ihm weiterhin ihren Platz; der Shield funktioniert aber auch auf einem Hosting, auf dem Sie nichts davon haben.

**Was kostet er?** Wenige Mikrosekunden pro Anfrage. Die Regeln werden einmal in eine PHP-Datei übersetzt, die OPcache
vorhält. Eine abgewiesene Anfrage kostet weit weniger als die Seite, die sie sonst angestoßen hätte.

## Besucher und Redaktion

**Was sieht ein Besucher?** Meist nichts. Wo eine Browserprüfung verlangt wird (bei der Anmeldung, in der
Administration einmal pro Pass, bei Clients, die zu oft anfragen), sagt eine Seite „einen Moment“, und der Browser
löst die Prüfung in einem Bruchteil einer Sekunde von selbst. Es gibt kein Bilderrätsel.

**Und Besucher ohne JavaScript?** Die Browserprüfung braucht JavaScript und Cookies; eine Seite sagt, was fehlt.
Seiten, die keine Prüfung verlangen, funktionieren ohne beides.

**Speichert er die IP-Adressen der Besucher?** Das Protokoll hält das Netz eines Clients fest (IPv4 /24, IPv6 /48),
nicht die vollständige Adresse einer Person. Die Administration zeigt diesen maskierten Wert.

## Betrieb

**Ist er nach der Installation aktiv?** Wenn der Hook in `config.php` steht, ja, im Modus enforce. Ohne den Hook wird
nichts gefiltert, auch nicht bei aktiver Extension.

**Wie schalte ich ihn schnell aus?** **Pausieren** auf der Übersicht, oder legen Sie `var/request-shield/PAUSED` an.
[Die Schalter](die-schalter.md).

**Kann ich ihn ausprobieren, ohne jemanden abzuweisen?** Ja: im Beobachtungsmodus.
[Beobachten und durchsetzen](beobachten-und-durchsetzen.md).

**Funktioniert er auf Shared Hosting?** Ja. Er braucht PHP und ein beschreibbares `var/request-shield/`; APCu ist
optional.

**Welche PHP-Versionen?** PHP 7.4 bis 8.5. Unter PHP 7.4 wird ein Build derselben Bibliotheksdateien mit älterer
Syntax geladen.

**Funktioniert er mit Exponential Velocity?** Ja, mit identischen Antworten, ab dem `index.php` von 6.0.15.
[Unter Velocity](unter-velocity.md).

**Funktioniert er mit dem HTTP-Cache?** Ja. Er läuft vor dem frühen Ausstieg des Caches, sodass auch
zwischengespeicherte Seiten geschützt sind, und eine Anfrage, die er als nicht cachebar markiert, wird nie
gespeichert.

## Bezug

**Welche Installationen bekommen ihn?** Jede Website mit Exponential 6.0 kann ihn ab sofort mit
`composer require se7enxweb/exp_request_shield` installieren. Neue Installationen aus `se7enxweb/exponential` `main`
und die Website-Pakete von 6.0.15 enthalten ihn. Das `config.php` einer bestehenden Website ändert eine
Aktualisierung nie: Die Hook-Zeile wird von Hand hinzugefügt. `composer create-project` der Distributionen liefert
v6.0.14, bis 6.0.15 getaggt ist. Die ganze Tabelle:
[Wer ihn bekommt und wann](../../funktionen/6.0/request-shield.md#wer-ihn-bekommt-und-wann).

**Unter welcher Lizenz steht er?** Die Extension steht unter der GPL-2.0-or-later. Die mitgelieferte Bibliothek und
die drei daraus kopierten Exponential-Regeldateien behalten die MIT-Lizenz.

**Wo sind die Regeln dokumentiert?** In der Dokumentation der Bibliothek (auf Englisch):
[Regeldateien](https://github.com/cjw-network/request-shield/blob/main/docs/reference/rule-files.md).
