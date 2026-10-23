# Norwegian Bokmål (nor-NO): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Norwegian Bokmål as used in Norway, never Nynorsk. Nynorsk (`nno-NO`) is a separate locale and does not read this
catalogue. The tone is plain and direct, as in the Norwegian versions of common operating systems and office
software.

## 2. Form of address

- **Buttons, menu items, links and commands** use the imperative: "Lagre", "Slett", "Fjern", "Rediger",
  "Legg til", "Avbryt", "Opprett ny", "Velg", "Søk", "Bruk endringer", "Publiser", "Last opp", "Last ned",
  "Lukk", "Tilbake".
- **Titles, column headings and tabs** are nouns: "Innholdsstruktur", "Brukerkontoer", "Layouttype",
  "Varslingsinnstillinger".
- **Sentences, hints, questions and error messages** address the user with **du** (deg, din, ditt, dine), in lower
  case, and instructions use the imperative:
  - "Er du sikker på at du vil fjerne denne klassen?"
  - "Velg en plassering" (Please choose a location)
  - "Du har ikke tilgang til å fjerne klasser."
  - "Klikk på "Avbryt"-knappen og prøv å slette bare de plasseringene du har lov til å slette."
  - "Du kan redigere"
- "Please" is not translated: no "vennligst", no "vær så snill".
- Status messages are plain statements: "Objektet ble lagret.", "Filen finnes ikke."
- Not allowed: the formal De, Dem, Deres as a form of address. ("De" as the plural "they" is fine.)

## 3. Script and typography

- Latin script with æ, ø, å. Sentence case: "Innholdsstruktur", "Brukere og grupper".
- Where the source has curly quotes (“…”), use «…»; straight quotes in the source stay straight.
- No comma before "at" or "som" in a subordinate clause: "Er du sikker på at du vil ...".
- Compounds are written as one word ("klassegruppe", "brukerkonto", "jobbkø", "søkeindeks"); after a code, an
  abbreviation or a name with a hyphen: "INI-fil", "URL-alias", "Node-ID", "REST-leverandør", "CSS-fil".
- Counts: "%count elementer", or a label form: "Elementer: %count".

## 4. Glossary

| English | Bokmål | Note |
|---|---|---|
| content | innhold | |
| content object / object | innholdsobjekt / objekt | |
| node | node | pl. noder; main node: hovednode |
| location / main location | plassering / hovedplassering | |
| content class / class | innholdsklasse / klasse | class group: klassegruppe |
| attribute | attributt | et attributt, pl. attributter |
| datatype | datatype | |
| version | versjon | |
| draft | utkast | Store draft: Lagre utkast; Discard draft: Forkast utkast |
| publish / published | publiser / publisert | Send for publishing: Send til publisering |
| siteaccess | nettstedstilgang | pl. nettstedstilganger |
| site | nettsted | sitemap: nettstedskart |
| section | seksjon | |
| object state / state | objekttilstand / tilstand | |
| role | rolle | |
| policy | tilgangsregel | pl. tilgangsregler |
| limitation | begrensning | |
| permission / access denied | tillatelse / ingen tilgang | "You do not have permission": Du har ikke tilgang |
| user / user group | bruker / brukergruppe | |
| user accounts | brukerkontoer | |
| owner / creator | eier / opprettet av | |
| created / modified | opprettet / endringstidspunkt | column headings |
| cache | cache | Clear cache: Tøm cache |
| template | mal | pl. maler |
| override | overstyring | override template: overstyringsmal |
| fallback | reserve | fallback chain: reservekjede |
| design | design | |
| workflow | arbeidsflyt | |
| trigger / event | utløser / hendelse | |
| trash | papirkurv | |
| bookmarks | bokmerker | |
| settings | innstillinger | |
| Setup (tab) | Oppsett | |
| Dashboard | Oversikt | |
| Content structure | Innholdsstruktur | |
| Media library | Mediebibliotek | |
| layout | layout | pl. layouter; layout type: layouttype |
| block / zone | blokk / sone | a block of an INI file: seksjon |
| extension | utvidelse | |
| package | pakke | |
| module / view | modul / visning | the button "View": Vis |
| search | søk | search engine: søkemotor |
| search index / reindex | søkeindeks / reindeksering | verb: indekser på nytt |
| log / log file | logg / loggfil | |
| audit / audit trail | revisjon / revisjonsspor | |
| job / queue | jobb / kø | job queue: jobbkø |
| worker | arbeidsprosess | |
| cronjob | cronjobb | |
| preview | forhåndsvis | noun: forhåndsvisning |
| upload / download | last opp / last ned | |
| export / import | eksporter / importer | |
| remove | fjern | Remove selected: Fjern valgte |
| delete | slett | |
| edit | rediger | |
| save / store | lagre | Store (shop tab): Butikk |
| discard | forkast | |
| apply | bruk | Apply changes: Bruk endringer |
| cancel | avbryt | |
| OK / yes / no | OK / ja / nei | |
| back / next / previous | tilbake / neste / forrige | |
| create / add | opprett / legg til | |
| select / browse | velg / bla igjennom | Select all: Velg alle; Deselect all: Velg ingen |
| move / copy | flytt / kopier | |
| hide / reveal | skjul / vis | hidden / visible: skjult / synlig |
| enable / disable | aktiver / deaktiver | enabled / disabled: aktivert / deaktivert |
| restore / assign | gjenopprett / tilordne | |
| approve / reject / pending | godkjenn / avvis / venter | |
| archive | arkiv | |
| order (shop) | ordre | order status: ordrestatus |
| order (sorting) | rekkefølge | |
| shop / basket / customer / product | butikk / handlekurv / kunde / produkt | |
| VAT / discount / price / currency | mva. / rabatt / pris / valuta | |
| wish list | ønskeliste | |
| newsletter / subscription | nyhetsbrev / abonnement | |
| tag / keywords | tagg / nøkkelord | |
| URL alias / wildcard | URL-alias / jokertegn | |
| translation / language | oversettelse / språk | main language: hovedspråk; interface language: grensesnittspråk |
| locale | stedsinformasjon | |
| relation / related objects | relasjon / relaterte objekter | reverse related: omvendt relaterte objekter |
| subtree / parent / sub items | undertre / overordnet / underelementer | children: underordnede |
| notification / collaboration | varsling / samarbeid | pl. varsler |
| identifier / remote ID | identifikator / ekstern ID | |
| password / username | passord / brukernavn | |
| log in / log out | logg inn / logg ut | |
| e-mail | e-post | |
| file / directory / folder / image | fil / katalog / mappe / bilde | |
| item / items | element / elementer | in the shop: vare / varer |
| name / title / description | navn / tittel / beskrivelse | |
| type / status | type / status | |
| default / required | standard / påkrevet | |
| error / warning | feil / advarsel | |
| filter / details | filtrer / detaljer | the noun: filter |
| run / refresh / update / reset | kjør / oppdater / oppdater / nullstill | |
| dry run | prøvekjøring | |
| debug | feilsøking | |
| editor (software) / editor (person) | redigeringsprogram / redaktør | template editor: malredigering |
| provider | leverandør | |
| prompt / model / token | ledetekst / modell / token | |
| embedding | vektorinnbygging | |
| thread / chain | tråd / kjede | |

## 5. Decisions after the translation

- **Store**: the shop tab of the administration header is "Butikk"; every other "Store" is the verb "Lagre".
- **VAT** is "mva." with the full stop, also at the start of a label ("Mva.").
- **Event** is "hendelse", also in the audit log and the job queue; "arrangement" is only an event in a calendar.
