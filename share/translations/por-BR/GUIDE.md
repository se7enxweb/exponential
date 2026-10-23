# Portuguese, Brazil (por-BR): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Brazilian Portuguese, in the current orthography (ação, seção, objeto, ideia, conexão), in the natural, concise
style of Brazilian software. Brazilian vocabulary only: arquivo (not ficheiro), tela (not ecrã), usuário (not
utilizador), registro (not registo), salvar (not guardar), equipe (not equipa).

The other Portuguese-speaking countries read this catalogue: `por-PT`, `por-AO`, `por-CV` and `por-MZ` have no
translation of their own and fall back to `por-BR`. Prefer wording that a reader in Portugal or Africa also
understands without effort, but never mix in European spellings.

Technical terms that Brazilian IT uses in English stay English: template, workflow, layout, cache, cronjob, token,
prompt, embedding, worker, plugin, feed, thread, design.

## 2. Form of address

The user is **você** (seu, sua), never tu, vós or o senhor.

- **Buttons, menu items, links and short actions** use the infinitive: Salvar, Excluir, Remover, Editar,
  Cancelar, Aplicar, Publicar, Carregar, Selecionar todos, Descartar o rascunho, Adicionar favorito.
- **Titles, column headings and tabs** are nouns or noun phrases.
- **Sentences, hints and instructions** use the você imperative (Clique, Selecione, Digite, Escolha, Verifique)
  or a você sentence:
  - Tem certeza de que deseja remover estes itens? (Are you sure you want to remove these items?)
  - Você não tem permissão para remover classes. (You do not have permission to remove classes.)
  - Selecione o arquivo que você deseja carregar. (Select the file that you want to upload.)
- "Are you sure you want to ...?" is always "Tem certeza de que deseja ...?"; "want" in a sentence is desejar.
- "Please" is mostly left out: "Please enter a valid number" = "Digite um número válido". Use "Por favor, ..."
  only where a bare imperative would sound abrupt.
- A label ending without a full stop takes the infinitive; a tooltip written as a sentence may take the
  imperative. Follow the source: a bare label is a label.
- Success messages: "... foi salvo com sucesso".

## 3. Script and typography

- Sentence case as in the source; only names are capitalised inside a label.
- Quotation marks "" or “ ” as in the source.
- Numbers: digits 0–9; the decimal comma and date formats come from the locale, never from the text.
- In square-bracket labels translate the words: `%name [Class]` = `%name [Classe]`.

## 4. Glossary

Binding for the kernel and every extension.

| English | Portuguese | Note |
|---|---|---|
| content | conteúdo | |
| content object / object | objeto de conteúdo / objeto | |
| node | nó | plural nós; parent node = nó pai, main node = nó principal |
| location | local | main location = local principal |
| content class / class | classe de conteúdo / classe | class group = grupo de classes; a PHP class is also classe |
| attribute | atributo | |
| datatype | tipo de dados | |
| version | versão | |
| draft | rascunho | My drafts = Meus rascunhos |
| publish / published | publicar / publicado | |
| translation / language | tradução / idioma | main language = idioma principal |
| siteaccess | acesso ao site | plural acessos ao site |
| site | site | |
| design | design | |
| section | seção | the Exponential section only |
| object state / state group | estado do objeto / grupo de estados | |
| role / policy | papel / política | plural papéis |
| permission / access | permissão / acesso | Access denied = Acesso negado |
| limitation | limitação | |
| user / user group | usuário / grupo de usuários | anonymous user = usuário anônimo |
| My account | Minha conta | |
| log in / log out | entrar / sair | the label "Login" (a field) = Usuário |
| password / username | senha / nome de usuário | |
| e-mail | e-mail | |
| cache / clear cache | cache (o cache) / limpar cache | |
| template | template | |
| override | substituição | override template = template de substituição; to override a setting = sobrescrever |
| fallback | alternativo | fallback language = idioma alternativo |
| workflow / workflow event | workflow / evento de workflow | |
| trigger | gatilho | |
| trash / empty trash | lixeira / esvaziar a lixeira | |
| bookmark | favorito | |
| settings | configurações | |
| Setup (admin tab) | Configuração | |
| Dashboard | Painel | |
| layout / block / zone | layout / bloco / zona | Exponential Layouts stays as is; to block a user = bloquear |
| extension / plugin | extensão / plugin | |
| package | pacote | |
| module / view / function | módulo / visualização / função | View (button) = Visualizar |
| search | pesquisar (button) / pesquisa (noun) | search engine = mecanismo de pesquisa |
| search index / index / reindex | índice de pesquisa / índice, indexar / reindexar | |
| log / log file | log / arquivo de log | |
| audit / audit trail | auditoria / trilha de auditoria | |
| cronjob | cronjob | |
| job / queue / worker | tarefa / fila / worker | |
| preview | pré-visualizar (button) / pré-visualização | kept apart from View = Visualizar |
| upload / download | carregar / baixar (noun: download) | |
| export / import | exportar / importar | |
| remove | remover | take out of a list or place |
| delete | excluir | destroy; deleted = excluído |
| edit / save / store | editar / salvar / salvar | "Store" is the save button; only the shop tab of the header is Loja |
| discard | descartar | Discard draft = Descartar o rascunho |
| apply / cancel / OK | aplicar / cancelar / OK | |
| back / next / previous | voltar / próximo / anterior | |
| yes / no | sim / não | |
| add / create / new | adicionar / criar / novo | |
| move / copy | mover / copiar | |
| close / update, refresh | fechar / atualizar | |
| reset / restore | redefinir / restaurar | |
| select / select all | selecionar / selecionar todos | |
| enable / disable | ativar / desativar | enabled = ativado, disabled = desativado |
| visible / hidden | visível / oculto | hide = ocultar, reveal = revelar |
| archive | arquivo, arquivar | archived = arquivado |
| order (shop) / order (sorting) | pedido / ordem | |
| sort / ascending / descending | ordenar / crescente / decrescente | |
| tag / custom tag | tag / tag personalizada | for keyword tags and HTML tags alike |
| keyword | palavra-chave | |
| owner / creator | proprietário / criador | column "Creator" = Criado por |
| created / modified | criado / modificado | |
| relation / related objects | relação / objetos relacionados | reverse related = com relação reversa |
| parent / children / sub-items | pai / filhos / subitens | item = item |
| subtree | subárvore | |
| ID / identifier / remote ID | ID / identificador / ID remoto | |
| URL alias / URL wildcard | apelido de URL / URL com curinga | |
| priority / default / required | prioridade / padrão / obrigatório | |
| status / state | status / estado | |
| warning / error | aviso / erro | |
| notification | notificação | |
| collaboration | colaboração | |
| information collection / collected information | coleta de informações / informações coletadas | |
| file / folder / directory | arquivo / pasta / diretório | |
| image / media | imagem / mídia | |
| thumbnail | miniatura | |
| path / browse | caminho / navegar | |
| database | banco de dados | |
| statistics / system information | estatísticas / informações do sistema | |
| install / uninstall | instalar / desinstalar | |
| shop / basket / customer / product | loja / carrinho / cliente / produto | |
| VAT / discount / price / currency | imposto / desconto / preço / moeda | |
| wish list | lista de desejos | |
| newsletter / subscription | newsletter / assinatura | |
| session | sessão | |
| field | campo | |
| format | formato | |
| feed | feed | |
| tab | aba | |
| theme | tema | |
| menu / toolbar | menu / barra de ferramentas | |
| name / title / description / type / value / date | nome / título / descrição / tipo / valor / data | |
| AI / model / prompt / token | IA / modelo / prompt / token | |
| embedding / vector | embedding / vetor | |
| provider | provedor | |
| deploy, deployment | implantar / implantação | |
| mapping | mapeamento | |
| editor | editor | |
| interface language | idioma da interface | |
| screen | tela | |

## 5. Decisions after the translation

| English | Portuguese | Note |
|---|---|---|
| Class | classe | not "tipo de conteúdo": the whole catalogue says classe |
| URL alias | apelido de URL | |
| Order (sorting) | ordem | a sort order is never pedido; pedido is only the shop order |
| Preview / View | pré-visualizar / visualizar | |
| Approve / Approval | aprovar / aprovação | |
| Confirm / Reject | confirmar / rejeitar | rejeitar is never used for Discard |
| Pending | pendente | |
| Container (class property) | contêiner | the label may say "Pode conter subitens" |
| Locale | localidade | |
| Charset | conjunto de caracteres | |
| Wildcard | curinga | |
| Redirect | redirecionar | |
| License | licença | |
| Embed | incorporar | |
| Placement | posição | |
| Overview | visão geral | |
| Rich text | texto formatado | |
| Preset | predefinição | |
| Batch | lote | |
| Scope / assign | escopo / atribuir | |
| Validate | validar | |
