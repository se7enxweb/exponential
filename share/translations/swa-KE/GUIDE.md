# Swahili (swa-KE): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Swahili (Kiswahili sanifu) as written in Kenya, with the terminology of the major software vendors'
Swahili interfaces. Tanzanian users (`swa-TZ`) fall back to this catalogue, so write the shared standard language:
no Sheng, no colloquial forms, no regional words where a standard word exists. The tone is factual and short.

Where the field in Kenya really uses an English loanword (faili, folda, tovuti, seva, URL, VAT), use the loanword
rather than a new coinage. A long text may add the English term in parentheses at its first mention, as in
"kazi iliyoratibiwa (cronjob)" or "upachikaji (embedding)"; a short label does so only where the glossary shows it.

## 2. Form of address

Swahili has no formal "you". The user is addressed in the **second person singular (wewe)**; politeness comes from
**tafadhali** and from full sentences.

- **Buttons, menu items and links** use the short singular imperative, without tafadhali: Hifadhi (Save, Store),
  Futa (Delete), Ondoa (Remove), Hariri (Edit), Ghairi (Cancel), Tumia (Apply), Chapisha (Publish), Pakia (Upload),
  Chagua zote (Select all), Tupa rasimu (Discard draft), Sawa (OK).
- **Titles, column headings and tabs** are nouns or noun phrases: Jina, Aina, Mmiliki, Imebadilishwa, Rasimu zako.
  A page title that is a verb phrase in English ("Edit object") may keep the imperative; stay consistent within
  one page.
- **Sentences, hints and error messages** use the singular, and a request starts with Tafadhali:
  - Tafadhali chagua eneo (Please choose a location)
  - Tafadhali weka nambari sahihi kati ya 1 na 10000 (Please enter a valid number between 1 and 10000)
  - Huna ruhusa ya kuondoa madarasa. (You do not have permission to remove classes.)
  - Chagua faili unayotaka kupakia. (Select the file that you want to upload.)
  - Possessives in the singular: akaunti yako, rasimu zako.
- **Questions**: "Are you sure you want to …" is Una uhakika kwamba unataka …:
  Una uhakika kwamba unataka kuondoa darasa hili? A short confirmation question ("Delete this block?",
  "Cancel this workflow process?") uses the **subjunctive** of the second person singular: Ufute bloku hii?,
  Ughairi mchakato huu wa mtiririko wa kazi?, Ubatilishe ufunguo "%name"?. The button that answers it stays
  imperative (Futa, Ghairi).
- **Status messages** use the perfect with the subject prefix of the noun class: Faili imepakiwa.,
  Kitu hiki kimefutwa., Mabadiliko yamehifadhiwa., Vipengee %count vimefutwa. Mind the agreement
  (kitu → kimefutwa, vitu → vimefutwa, faili → imefutwa, mtumiaji → amefutwa).
- **Not allowed**: the plural address (ninyi, mnataka, chagueni, tafadhalini), Sheng and colloquial speech.

## 3. Script and typography

- Latin alphabet without diacritics. The apostrophe of ng' (ng'ombe) is part of the word.
- Capitals: a sentence starts with a capital; labels capitalise only the first word, except names.
- Swahili joins prefixes and suffixes to the stem, so a placeholder must stand free: never `%countni` or
  `%namewa`. Build the sentence around it: "Idadi: %count", "vipengee %count", "kwa %name",
  "Michakato ya kazi: %count".
- A number after a noun follows it: vipengee %count, akaunti %count.
- Punctuation as in English. Quotation marks: straight "…" or as in the source.
- Numbers: digits 0–9, decimal point as in English.

## 4. Glossary

Binding for the kernel and every extension.

| English | Swahili | Note |
|---|---|---|
| content | maudhui | |
| content object / object | kitu cha maudhui / kitu | plural vitu; a content object is always kitu |
| item (list, menu, basket entry) | kipengee | plural vipengee |
| node | nodi | |
| location | eneo | plural maeneo; main location = eneo kuu |
| content class / class | darasa la maudhui / darasa | plural madarasa; class group = kundi la madarasa |
| attribute | sifa | |
| datatype | aina ya data | |
| version | toleo | plural matoleo |
| draft | rasimu | Your drafts = Rasimu zako |
| publish / published / publishing | chapisha / imechapishwa / uchapishaji | |
| translation / language | tafsiri / lugha | main language = lugha kuu |
| siteaccess | siteaccess | noun class 9 (siteaccess hii); in a long text first "ufikiaji wa tovuti (siteaccess)" |
| site, website | tovuti | |
| design | dizaini | |
| structure | muundo | Content structure = Muundo wa maudhui |
| section | sehemu | the Exponential section only |
| object state / state group | hali ya kitu / kundi la hali | |
| role / policy | jukumu / sera | plural majukumu |
| permission / access | ruhusa / ufikiaji | Access denied = Ufikiaji umekataliwa |
| limitation | kizuizi | plural vizuizi |
| user / user group | mtumiaji / kundi la watumiaji | plural watumiaji; anonymous user = mtumiaji asiyejulikana |
| user account | akaunti ya mtumiaji | |
| administrator / admin interface | msimamizi / kiolesura cha usimamizi | |
| log in / log out | Ingia / Toka | login name (field) = jina la kuingia |
| password / username | nenosiri / jina la mtumiaji | |
| e-mail | barua pepe | |
| cache / clear cache | akiba / futa akiba | |
| template | kiolezo | plural violezo |
| override | ubatilishaji, batilisha | override template = kiolezo cha ubatilishaji |
| fallback | mbadala | fallback chain = mnyororo wa mbadala |
| workflow / workflow event | mtiririko wa kazi / tukio la mtiririko wa kazi | plural mitiririko ya kazi |
| trigger | kichochezi | |
| trash | pipa la taka | |
| bookmark | alamisho | |
| settings | mipangilio | one setting = mpangilio |
| Setup (admin tab) / configuration | Usanidi / usanidi | configure = sanidi |
| Dashboard | Dashibodi | |
| Media library | Maktaba ya midia | |
| layout / block / zone | mpangilio wa ukurasa / bloku / kanda | Exponential Layouts stays as is |
| extension | kiendelezi | plural viendelezi |
| plugin | programu-jalizi | not kiendelezi |
| package | kifurushi | plural vifurushi |
| module / view / function | moduli / mwonekano / kitendakazi | View as a button = Tazama |
| search / search engine | tafuta, utafutaji / injini ya utafutaji | |
| search index / index (verb) / reindex | faharasa ya utafutaji / weka kwenye faharasa / weka faharasa upya | index file = faili ya faharasa |
| log / log file | kumbukumbu / faili ya kumbukumbu | |
| audit / audit trail | ukaguzi / kumbukumbu za ukaguzi | |
| cronjob | kazi iliyoratibiwa (cronjob) | schedule = ratiba, ratibu |
| job / queue / worker | kazi / foleni / mchakato wa kazi (worker) | |
| run (noun) | uendeshaji | Next run = Uendeshaji unaofuata; run (button) = Endesha |
| script | skripti | |
| kernel | kernel | in a long text: kiini cha mfumo (kernel) |
| notification | arifa | |
| collaboration | ushirikiano | |
| preview | Hakiki | as a noun: onyesho la kukagua |
| upload / download | pakia / pakua | |
| export / import | hamisha / leta | nouns: uhamishaji / uletaji |
| remove | ondoa | take out of a list or place |
| delete | futa | destroy |
| edit / save / store | Hariri / Hifadhi / Hifadhi | only the shop tab of the page header is Duka |
| discard | Tupa | |
| apply / cancel / OK | Tumia / Ghairi / Sawa | |
| back / next / previous / close | Nyuma / Ifuatayo / Iliyotangulia / Funga | |
| yes / no | Ndiyo / Hapana | |
| add / create / new | ongeza / unda / mpya | |
| move / copy / swap | sogeza / nakili / badilisha nafasi | move to the trash = hamishia kwenye pipa la taka |
| refresh / reset / restore / update | onyesha upya / weka upya / rejesha / sasisha | |
| select / select all / deselect all | chagua / Chagua zote / Ondoa uteuzi wote | |
| enable / disable | wezesha / zima | enabled = imewezeshwa, disabled = imezimwa |
| active / inactive | amilifu / si amilifu | |
| visible / hidden | inaonekana / imefichwa | hide = ficha, reveal = funua, show = onyesha |
| expand / collapse | panua / kunja | Collapse all = Kunja zote |
| archive | see section 5 | |
| order (shop) / order (sorting) | agizo / mpangilio | plural maagizo; Order status = Hali ya agizo |
| sort / ascending / descending | panga / kupanda / kushuka | |
| tag (keyword, eztags) / keyword | lebo / neno kuu | plural maneno makuu |
| tag (HTML, XML) / custom tag | tagi / tagi maalum | |
| owner / creator / modifier | mmiliki / muundaji / mrekebishaji | |
| created / modified | Imeundwa / Imebadilishwa | lower case inside a sentence |
| relation / related objects | uhusiano / vitu vinavyohusiana | plural mahusiano; reverse related = uhusiano wa kinyume |
| parent / children / sub-items | mzazi / watoto / vipengee vidogo | |
| subtree | mti mdogo | |
| ID / identifier / remote ID | ID / kitambulisho / kitambulisho cha mbali | a bare column heading "ID" stays ID |
| URL alias / URL wildcard | lakabu ya URL / kadi-pori ya URL | |
| priority / default / required / optional | kipaumbele / chaguomsingi / inahitajika / si lazima | |
| status, state | hali | |
| warning / error / notice | onyo / hitilafu / ilani | |
| file / folder / directory | faili / folda / saraka | |
| image / thumbnail / media | picha / kijipicha / midia | |
| path / browse | njia / vinjari | |
| database | hifadhidata | |
| statistics / system information | takwimu / taarifa za mfumo | |
| shop / basket / customer / product | duka / kikapu / mteja / bidhaa | |
| checkout / price / currency | Lipia (button), malipo / bei / sarafu | |
| VAT / discount / discount rule | VAT / punguzo / kanuni ya punguzo | rule = kanuni |
| wish list | orodha ya matamanio | |
| newsletter / subscription | jarida / usajili | subscribe = jisajili |
| session | kipindi | plural vipindi |
| field | sehemu ya data | a form field = kisanduku |
| format / formatting | umbizo / uumbizaji | rich text = maandishi yaliyoumbizwa |
| feed | mlisho | |
| tab / theme / icon | kichupo / mandhari / aikoni | |
| menu / toolbar | menyu / upau wa zana | |
| button / click | kitufe / bofya | |
| name / title / description / type / value / date | jina / kichwa / maelezo / aina / thamani / tarehe | |
| page / link | ukurasa / kiungo | plurals kurasa, viungo |
| AI / model / prompt / token | AI (long text: akili bandia) / modeli / kidokezo / tokeni | |
| embedding / chunk / vector | upachikaji (embedding) / kipande / vekta | |
| embed / embedded | pachika / iliyopachikwa | |
| provider / endpoint / backend | mtoa huduma / sehemu ya mwisho (endpoint) / upande wa nyuma (backend) | |
| filter | kichujio | plural vichujio; to filter = chuja |
| overview, summary | muhtasari | |
| server / client | seva / kiteja | client ≠ customer (mteja) |
| deploy, deployment | usambazaji | to deploy = sambaza |
| editor | mhariri (person) / kihariri (software) | Editor as a kind of siteaccess = uhariri |
| user interface | kiolesura | |

## 5. Decisions after the translation

Terms settled when the translations were compared. They take precedence over the glossary where both name a term.

| English | Swahili | Note |
|---|---|---|
| archived (version, object, order) | iliyohifadhiwa kwenye kumbukumbu | agrees with the noun: matoleo yaliyohifadhiwa…, vitu vilivyohifadhiwa… kwenye kumbukumbu |
| Archive / Unarchive (button) | Hifadhi kwenye kumbukumbu / Toa kwenye kumbukumbu | |
| archive (a place: menu item, audit or log archives) | hifadhi ya kumbukumbu | plural hifadhi za kumbukumbu |
| archive (file: .tar, .zip, .phar, .ezpkg) | kumbukumbu iliyobanwa | plural kumbukumbu zilizobanwa |
| run (noun) | uendeshaji | class 14, no plural: rephrase "runs" (michakato miwili ya uendeshaji); never mzunguko |
| rotation / rotate (log files) | mzunguko / zungusha | mzunguko is never "run" |
| dry run / self-test | jaribio bila mabadiliko (dry run) / kujipima | |
| pattern | ruwaza | not mfumo, not kiolezo |
| repository | hazina | |
| maintainer | mtunzaji | plural watunzaji |
| install / uninstall / installation | sakinisha / ondoa usakinishaji / usakinishaji | |
| checksum | jumla ya ukaguzi | plural jumla za ukaguzi |
| quarantine | karantini | move to the quarantine = hamishia karantini |
| thread | uzi | plural nyuzi |
| mapping | uhusishaji | also language and siteaccess mapping |
| resolve (a path, a name, a setting) | bainisha | resolvable = inaweza kubainishwa; resolver = kibainishi; solving a problem stays tatua |
| checkpoint | kituo cha ukaguzi | plural vituo vya ukaguzi |
| layout (directory or release structure) | muundo (wa saraka) | mpangilio wa ukurasa only for Exponential Layouts |
| child documents (Solr) | hati za watoto | |
| Approve / approval / approver | Idhinisha / uidhinishaji / mwidhinishaji | approved = imeidhinishwa |
| Accept / Reject, Deny / Confirm | Kubali / Kataa / Thibitisha | |
| Pending | inasubiri | |
| Container (class property) | chombo | Container (with children) = Chombo (chenye watoto); a CSS container = kontena |
| Flags (class properties) | alama | a command-line flag = chaguo |
| Locale / localization | locale / ujanibishaji | |
| Charset | seti ya herufi | |
| Redirect | elekeza upya | forward = peleka |
| License | leseni | never ruhusa, which is permission |
| Preset / placeholder | mpangilio tayari / kishikilia nafasi | |
| Batch | kundi la kazi (batch) | |
| Teaser / digest | kionjo / muhtasari wa mara kwa mara | |
| Assign / scope | kabidhi / upeo | |
| Storage / registry / schema | hifadhi / sajili / skima | |
| Port | mlango (port) | |
