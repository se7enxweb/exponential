---
title: Muster in kernel/classes/webauthn
audience: developer
category: standards
order: 523
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/webauthn mit den Entwurfsmustern.
keywords: kernel/classes/webauthn, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/webauthn

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/webauthn` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (4)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expWebAuthn` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/webauthn/expwebauthn.php:34` | Die Serverseite von WebAuthn (W3C Web Authentication, Level 2/3) für Passkeys: die Optionen, die ein Browser erhält, und die Prüfungen dessen, was er zurücksendet, wenn… |
| `expWebAuthnCbor` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/webauthn/expwebauthncbor.php:20` | Der Teil von CBOR (RFC 8949), den WebAuthn braucht: das Dekodieren des Attestation-Objekts eines neuen Passkeys und des COSE-Public-Keys in seinen Authenticator-Daten… |
| `expWebAuthnChallenge` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/webauthn/expwebauthnchallenge.php:19` | Die WebAuthn-Challenges, die eine Sitzung erhalten hat: Jede Anfrage nach Optionen bekommt eine neue zufällige Challenge, die auf dem Server (in der Sitzung) mit dem gehalten wird, wofür sie… |
| `expWebAuthnCose` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/webauthn/expwebauthncose.php:26` | Der öffentliche Schlüssel eines Passkeys als COSE (RFC 9052/9053) und die Prüfung einer damit erstellten Signatur. |
