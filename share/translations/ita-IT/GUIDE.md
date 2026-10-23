# Italian (ita-IT): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Italian as used in Italy. Swiss Italian (`ita-CH`) falls back to this catalogue, so avoid regional words.
The tone is natural, concise and friendly, as in the Italian versions of common operating systems and office
software; translate the meaning, not word for word.

## 2. Form of address

- **Buttons, menu items, links and commands** use the imperative of **tu**: "Salva", "Elimina", "Annulla",
  "Modifica", "Aggiungi", "Crea nuovo", "Cerca", "Carica", "Scarica", "Applica le modifiche", "Pubblica",
  "Seleziona tutto", "Indietro". Never the infinitive ("Salvare") and never the Lei form ("Salvi").
- **Titles, column headings and tabs** are nouns: "Struttura contenuti", "Account utenti", "Tipo di layout",
  "Lingua dell'interfaccia".
- **Sentences, hints, questions and error messages** address the user with **tu**:
  - "Sei sicuro di voler rimuovere questa classe?"
  - "Scegli una collocazione" (Please choose a location)
  - "Non sei abilitato a rimuovere le classi."
  - "Premi il pulsante "Annulla" e ..." (a button); "Fai clic per copiare" (a link, an icon, a column)
  - "Puoi modificare le impostazioni della cronologia versioni in content.ini ..."
- Possessives: "il tuo account", "la tua password". The negative imperative is "non" + infinitive: "Non eliminare".
- In a description that speaks of no one in particular, an impersonal form ("si può", the passive) is fine.
- Not allowed: Lei, Suo, Sua as a form of address, "È sicuro ...?", "Clicchi". Prefer "Premi" or "Fai clic" to
  "Clicca".

## 3. Script and typography

- Latin script with à, è, é, ì, ò, ù; the capital is "È", never "E'". Sentence case: "Struttura contenuti",
  "Utenti e gruppi"; product names keep their capitals.
- The apostrophe is the straight ASCII one: "l'oggetto", "dell'interfaccia".
- Quotation marks as in the source.
- English words that Italian uses as they are (template, workflow, layout, cache, siteaccess, file, token) are
  invariable: "i template", "i siteaccess".
- Counts: a label form reads best: "Elementi: %count".

## 4. Glossary

| English | Italian | Note |
|---|---|---|
| content | contenuto | |
| content object / object | oggetto contenuto / oggetto | |
| node | nodo | main node: nodo principale |
| location / main location | collocazione / collocazione principale | |
| content class / class | classe di contenuto / classe | class group: gruppo di classi |
| attribute | attributo | |
| datatype | tipo di dato | |
| version | versione | |
| draft | bozza | Store draft: Salva bozza; Discard draft: Annulla bozza |
| publish / published | pubblica / pubblicato | Send for publishing: Pubblica |
| siteaccess | siteaccess | m., invariable |
| site | sito | sitemap: mappa sito |
| section | sezione | |
| object state / state | stato dell'oggetto / stato | |
| role | ruolo | |
| policy | politica di accesso | pl. politiche di accesso |
| limitation | limitazione | |
| permission / access denied | permesso / accesso negato | Permission denied: Permesso negato |
| user / user group | utente / gruppo utenti | |
| user accounts | account utenti | |
| owner / creator | proprietario / creatore | |
| created / modified | creato il / modificato il | column headings with a date |
| cache | cache | f.; Clear cache: Svuota la cache |
| template | template | m., invariable |
| override | override | override template: template di override |
| fallback | fallback | fallback chain: catena di fallback |
| design | design | |
| workflow | workflow | m., invariable |
| trigger / event | trigger / evento | |
| trash | cestino | |
| bookmarks | preferiti | |
| settings | impostazioni | |
| Setup (tab) | Impostazioni | |
| Dashboard | Dashboard | |
| Content structure | Struttura contenuti | |
| Media library | Media | |
| layout | layout | m., invariable; layout type: tipo di layout |
| block / zone | blocco / zona | |
| extension | estensione | |
| package | pacchetto | |
| module / view | modulo / vista | the button "View": Visualizza |
| search | cerca | noun: ricerca; search engine: motore di ricerca |
| search index / reindex | indice di ricerca / reindicizzazione | verb: reindicizza |
| log / log file | log / file di log | |
| audit / audit trail | audit / registro di audit | |
| job / queue | processo / coda | job queue: coda dei processi |
| worker | worker | |
| cronjob | cronjob | |
| preview | anteprima | |
| upload / download | carica / scarica | |
| export / import | esporta / importa | |
| remove | elimina | taking something out of a list without destroying it: rimuovi |
| delete | cancella | |
| edit | modifica | |
| save / store | salva | Store (shop tab): Negozio |
| discard | annulla | |
| apply | applica | Apply changes: Applica le modifiche |
| cancel | annulla | |
| OK / yes / no | OK / sì / no | |
| back / next / previous | indietro / successivo / precedente | |
| create / add | crea / aggiungi | |
| select / browse | seleziona / sfoglia | Select all: Seleziona tutto; Deselect all: Deseleziona tutto |
| move / copy | sposta / copia | |
| hide / reveal | nascondi / rivela | hidden / visible: nascosto / visibile |
| enable / disable | attiva / disattiva | enabled / disabled: abilitato / disabilitato |
| restore / assign | ripristina / assegna | |
| approve / reject / pending | approva / rifiuta / in attesa | |
| archive | archivio | |
| order (shop) / order (sorting) | ordine | the context tells them apart |
| shop / basket / customer / product | negozio / carrello / cliente / prodotto | |
| VAT / discount / price / currency | IVA / sconto / prezzo / valuta | |
| wish list | lista desideri | |
| newsletter / subscription | newsletter / iscrizione | subtree subscription: sottoscrizione sottoalbero |
| tag / keywords | tag / parole chiave | the "Tags" tab and attribute: Parole chiave |
| URL alias / wildcard | alias URL / carattere jolly | |
| translation / language | traduzione / lingua | main language: lingua principale; interface language: lingua dell'interfaccia |
| locale | localizzazione | |
| relation / related objects | relazione / oggetti correlati | reverse related: oggetti correlati inversi |
| subtree / parent / sub items | sottoalbero / padre / sotto-elementi | children: figli |
| notification / collaboration | notifica / collaborazione | |
| identifier / remote ID | identificatore / ID remoto | |
| password / username | password / nome utente | |
| log in / log out | accedi / esci | |
| e-mail | e-mail | |
| file / folder / image | file / cartella / immagine | |
| item / items | elemento / elementi | in the shop: articolo / articoli |
| name / title / description | nome / titolo / descrizione | |
| type / status | tipo / stato | |
| default / required | predefinito / obbligatorio | |
| error / warning | errore / attenzione | |
| filter / details | filtro / dettagli | |
| run / refresh / update / reset | esegui / ricarica / aggiorna / azzera | |
| dry run | simulazione | |
| debug | debug | |
| editor (software) / editor (person) | editor / redattore | template editor: editor dei template |
| provider | provider | |
| prompt / model / token | prompt / modello / token | |
| embedding | embedding | |
| thread / chain | thread / catena | |

## 5. Decisions after the translation

- **Store**: the shop tab of the administration header is "Negozio"; every other "Store" is the verb "Salva".
- **Remove and delete**: "Remove" on a button that deletes content is "Elimina", like most of the administration;
  "Rimuovi" is kept for taking an item out of a list, a group or a relation; "Delete" is "Cancella".
