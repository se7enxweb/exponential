---
title: Wann Sie forken sollten und wann nicht
audience: developer, app-creator
category: extension-development
order: 20
summary: Ein Fork ist eine Kopie, die Sie zu pflegen versprechen. Wann ein Fork die richtige Antwort ist - ein verwaistes Original, eine Korrektur, die auf ein Review wartet, eine andere Richtung - und wann ein Override, ein Issue oder ein Pull Request besser ist.
keywords: Fork, wann forken, verwaist, Pull Request, Pflege, Upstream, Downstream, Abweichung, Entscheidung, fork, when to fork, abandoned, pull request, maintenance, upstream, downstream, divergence, decision
type: help
---
# Wann Sie forken sollten und wann nicht

Teil 5, Kapitel 2. Auf GitHub ist ein Fork ein Klick. Seine Kosten kommen später: Jede Änderung, die das Original von
da an macht, muss in Ihre Kopie geholt werden, und jeder Benutzer Ihrer Kopie ist darauf angewiesen, dass Sie das tun.
Dieses Kapitel hilft Ihnen zu entscheiden, ob sich diese Kosten lohnen.

## Drei Arten von Fork

Das Wort „Fork“ wird für drei verschiedene Dinge verwendet, mit sehr unterschiedlichen Kosten:

| Art | Was es ist | Kosten |
|---|---|---|
| Ein **Beitrags-Fork** | Ihre Kopie auf GitHub, die nur dazu dient, einen Pull Request an das Original vorzubereiten | fast keine: Sie löschen oder setzen ihn zurück, nachdem der Pull Request übernommen wurde |
| Ein **Übergangs-Fork** | Ihre Kopie mit einer Korrektur, die Sie jetzt brauchen, verwendet, bis das Original dieselbe Korrektur veröffentlicht | gering, wenn er kurz ist: Sie kehren danach zum Original zurück |
| Ein **dauerhafter Fork** | eine Kopie, die ihren eigenen Weg geht, veröffentlicht unter Ihrem Namen | dauerhaft: Sie sind jetzt Maintainer |

Meistens wollen Sie die erste Art. Die zweite ist in Ordnung, wenn sie vorübergehend ist und Sie das sagen. Die dritte
braucht einen guten Grund.

## Gute Gründe für einen dauerhaften Fork

- **Das Original ist verwaist.** Seit Jahren keine Commits, Issues unbeantwortet, der Autor nicht erreichbar oder er
  sagt, dass er aufgehört hat. Lesen Sie zuerst
  [Eine verwaiste Erweiterung übernehmen](eine-verwaiste-erweiterung-uebernehmen.md): Manchmal übergibt der Autor sie
  gern, und es ist kein Fork nötig.
- **Das Original kann dort, wo Sie es brauchen, nicht laufen**, und seine Maintainer möchten die Änderung nicht, zum
  Beispiel weil sie eine andere Plattform unterstützen.
- **Sie möchten eine andere Richtung einschlagen**, und die Maintainer des Originals haben das klar abgelehnt.
  Uneinigkeit ist erlaubt; die Lizenz gibt Ihnen das Recht, mit dem Code Ihren eigenen Weg zu gehen.

se7enxweb/expchangeclass ist ein Beispiel für die ersten beiden zugleich: Ihre README erklärt, dass zwei vorhandene
Forks einer alten Erweiterung im Umlauf waren und „neither was safe to run here“ auf PHP 8, und dass der neue Fork das
Beste aus beiden übernimmt und behebt, was keiner von beiden hatte.

## Schlechte Gründe

- **„Ich muss ein Template ändern.“** Verwenden Sie ein Override ([Erweitern, ohne zu forken](erweitern-ohne-zu-forken.md)).
- **„Ich brauche eine Einstellung, die das Original nicht hat.“** Bitten Sie darum, oder schicken Sie einen Pull
  Request, der sie hinzufügt.
- **„Der Autor antwortet langsam.“** Warten Sie eine angemessene Zeit und fragen Sie dann noch einmal höflich nach.
  Wochen sind bei ehrenamtlichen Projekten normal.
- **„Ich möchte den Namen meiner Firma darauf haben.“** Das ist Rebranding, und dafür gelten eigene Regeln
  ([Ethisches Rebranding](ethisches-rebranding.md)).

## Eine Entscheidung in fünf Fragen

1. Kann ich es aus meiner eigenen Erweiterung heraus tun (Einstellungen, Override, Event, Unterklasse)? Wenn ja, tun
   Sie das.
2. Ist die Änderung für andere nützlich? Wenn ja, schicken Sie sie als Pull Request und forken Sie nur, um ihn
   vorzubereiten.
3. Brauche ich sie, bevor das Original sie veröffentlichen kann? Wenn ja, verwenden Sie einen Übergangs-Fork und
   planen Sie den Tag, an dem Sie ihn verlassen.
4. Wird das Original gepflegt? Wenn nein, siehe „übernehmen“.
5. Bin ich bereit, jahrelang Issues zu beantworten und Änderungen aus dem Original zu mergen? Wenn nein, legen Sie
   keinen dauerhaften Fork an.

## Wenn Sie endgültig forken

- Verwenden Sie einen neuen Paketnamen und einen Anzeigenamen, der nicht vorgibt, das Original zu sein.
- Behalten Sie jede Copyright-Zeile und die Lizenz; fügen Sie Ihre hinzu
  ([Namensnennung und Urheberrechtshinweise](namensnennung-und-urheberrechtshinweise.md)).
- Schreiben Sie in die README, warum es den Fork gibt und was er ändert, so wie es die README von expchangeclass tut.
- Behalten Sie die Git-Historie: Forken Sie auf GitHub, oder klonen und pushen Sie, statt Dateien zu kopieren. Die
  Historie ist Teil der Namensnennung, und mit ihr können Sie später aus dem Original mergen
  ([Einen Fork mit seiner Quelle synchron halten](einen-fork-synchron-halten.md)).
- Bieten Sie Ihre Korrekturen trotzdem zurück an. Auch ein dauerhafter Fork kann Pull Requests an das Original
  schicken.

## Verwandte Seiten

- Zurück: [Erweitern, ohne zu forken](erweitern-ohne-zu-forken.md)
- Weiter: [Auf GitHub forken, Schritt für Schritt](auf-github-forken.md)
- [Eine alte Erweiterung bewerten](../../../../eng-us/help/extension-development/part-1-from-user-to-developer/evaluating-an-old-extension.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
