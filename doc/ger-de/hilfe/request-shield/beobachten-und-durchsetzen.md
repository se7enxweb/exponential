---
title: "Request Shield: beobachten und durchsetzen"
audience: admin, hosting
category: administration
order: 50
summary: Die Modi des Request Shields - enforce nach der Installation, monitor zum Beobachten, ohne jemanden abzuweisen, strict und off -, wann Sie zuerst beobachten, wie Sie die beobachteten Entscheidungen lesen und wie Sie zu enforce zurückkehren.
keywords: Request Shield, Beobachtungsmodus, Modus monitor, Modus enforce, durchsetzen, beobachten, strict, off, set mode, monitor-reject, monitor-challenge, beobachtet, EXP-BAN, EXP-STRICT, Ausnahme, expect, erste Tage, Fehlalarm, Seiten-Alias, recheck, monitor mode, enforce mode, false positive
type: help
---
# Request Shield: beobachten und durchsetzen

Der Shield hat einen **Modus**, der in der Hauptregeldatei der Website steht (`set mode ...`) und mit den
Schaltflächen auf der Übersicht gewechselt wird.

## Inhalt

1. [Die Modi](#die-modi)
2. [Durchsetzen nach der Installation](#durchsetzen-nach-der-installation)
3. [Wann Sie zuerst beobachten](#wann-sie-zuerst-beobachten)
4. [Zurück zum Durchsetzen](#zurück-zum-durchsetzen)

## Die Modi

| Modus | Wirkung |
|---|---|
| **enforce** (Durchsetzen) | die Entscheidungen werden ausgeführt. Regeln mit vorangestelltem `monitor` werden weiterhin nur beobachtet |
| **monitor** (Beobachten) | jede Entscheidung wird so protokolliert, wie sie fiele (`monitor-reject`, `monitor-challenge` ...); niemand wird abgewiesen |
| **strict** (Streng) | enforce, und dazu die als strict markierten Regeln. Nur in der Regeldatei, auf keiner Schaltfläche |
| **off** (Aus) | nichts wird entschieden |

Ein geänderter Modus erreicht jeden Server innerhalb der `recheck` Sekunden der Regeln (standardmäßig 10); nichts
muss neu gestartet werden.

## Durchsetzen nach der Installation

Der Shield wird im Modus **enforce** installiert: Der Schutz ist ab der ersten Anfrage aktiv. Scanner,
Anfragefluten, verirrte Formulare und System-URLs werden abgewiesen, und die Anmeldung und die Formulare der
Administration verlangen die Browserprüfung. Einige Regeln des mitgelieferten Satzes (`EXP-BAN`, `EXP-STRICT`) sind
mit `monitor` markiert und beobachten auch dann nur.

Für die meisten Websites ist das von Anfang an richtig: Die mitgelieferten Regeln wurden für die eigenen Adressen von
Exponential geschrieben und mit Beispielen getestet.

## Wann Sie zuerst beobachten

Schalten Sie für einige Tage auf **Beobachten**, wenn:

- die Website ungewöhnliche Adressen hat: Seiten-Aliase, deren erster Teil ein Modulname ist (`content`, `user`,
  `shop` ...), Formulare eigener Extensions, eigene Suchseiten;
- Sie einer Beschwerde nachgehen, dass ein echter Besucher abgewiesen wurde, und in der Zwischenzeit niemand sonst
  abgewiesen werden soll.

Während der Shield beobachtet, lesen Sie **Setup > Request Shield > Protokoll**, Entscheidung **Beobachtet
(Monitor)**. Jede Zeile ist eine Anfrage, die der Shield abgewiesen oder geprüft *hätte*. Lassen Sie für jede, die von
einem echten Besucher oder Redakteur stammt, eine Ausnahme mit einem `expect`-Beispiel in die Regeldatei der Website
aufnehmen ([der Leitfaden](../../anleitungen/request-shield.md#5-eine-ausnahme-hinzufügen)).

## Zurück zum Durchsetzen

Wenn die beobachteten Zeilen nur noch zeigen, was Sie abweisen wollen (Scanner, Bots, Anfragen nach Dateien, nach
denen niemand fragen sollte), klicken Sie **Durchsetzen**. Der Hinweis zum Beobachtungsmodus verschwindet, und die
Kennzahlen zeigen wieder Abweisungen statt beobachteter Entscheidungen.

Lassen Sie eine Website nicht dauerhaft im Beobachtungsmodus: Dann protokolliert sie Angriffe, ohne sie aufzuhalten.
