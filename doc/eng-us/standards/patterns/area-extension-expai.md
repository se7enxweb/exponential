---
title: Patterns in extension/expai
audience: developer
category: standards
order: 411
summary: Every class, view, fetch function, operator and plugin point of extension/expai with its design patterns.
keywords: extension/expai, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/expai

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/expai` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (186)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expAiAltText` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/alttext/expaialttext.php:21` | Alt text for images (doc/alt-text.md): a vision model looks at the stored image of an ezimage field, with the object's title and text around it, and… |
| `expAiAltTextFieldAction` | class extends expAiPromptFieldAction | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/alttext/expaialttextfieldaction.php:15` | The field action "Write alt text" ([FieldAction_alt_text], doc/alt-text.md) of ezimage fields. |
| `expAiAltTextFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md), [Utility (static helper)](utility.md) | `extension/expai/classes/alttext/expaialttextfunctioncollection.php:14` | fetch( 'expai', 'alt_text_model', hash( 'object', $object ) ): the vision model the "Write alt text" button uses for the images of this object, or wh… |
| `expAiAltTextJobHandler` | class extends expQueueHandlerBase | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/expai/classes/alttext/expaialttextjobhandler.php:19` | The job type expai.alttext (expAiAltText::queue(), the "Missing alt text" report): asks a vision model for the alt text of one image and writes it in… |
| `expAiAltTextServerFunctions` | class extends expAiServerFunctions | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/alttext/expaialttextserverfunctions.php:21` | The ezjscore function of the "Write alt text" button (ezjscore.ini [ezjscServer_expaialttext]; the caller needs ezjscore/call with the function list… |
| `expAiAnthropicProvider` | class extends expAiProviderBase | [Factory and static factory methods](factory.md), [Strategy / handler](strategy-handler.md) | `extension/expai/classes/expaianthropicprovider.php:16` | The Anthropic Messages API (POST {Endpoint}, default https://api.anthropic.com/v1/messages) with tool use, adaptive thinking at the configured effort… |
| `expAiApprovalCheck` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/expaiapprovalcheck.php:19` | What "Send for approval" will do with a draft: the triggers before content/publish and their workflows, with the conditions of each approve event (ev… |
| `expAiAssistant` | class | – | `extension/expai/classes/expaiassistant.php:16` | The conversation of the full view panel, one provider call per request ("step"): the editor's message (or a continuation) goes to the model with the… |
| `expAiAudit` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/audit/expaiaudit.php:23` | What Exponential Intelligence records in the audit trail (doc/audit-trail.md): one record per model request, per draft written and per change of a dr… |
| `expAiAuditBranch` | class implements expAuditTaxonomyBranch | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/audit/expaiauditbranch.php:15` | The audit taxonomy branch of Exponential Intelligence (audit.ini [AuditEventSettings] Branches[expai]): the names expAiAudit records, all into the ch… |
| `expAiAuditSource` | class extends expAuditSourceBase | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/audit/expaiauditsource.php:18` | The source of Exponential Intelligence in the audit console (audit.ini [AuditSourceSettings] Sources[expai], doc/audit-trail.md): the dashboard panel… |
| `expAiBatch` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/expai/classes/lanes/expaibatch.php:17` | One provider batch (table expai_batch): which provider, lane and model, the provider's id, the state and counts, when it was submitted, ended, its re… |
| `expAiBatchAnthropic` | class extends expAiBatchApi | – | `extension/expai/classes/lanes/expaibatchanthropic.php:25` | Anthropic Message Batches (https://platform.claude.com/docs/en/build-with-claude/batch-processing, read 2026-10-11): 50% of the standard prices; up t… |
| `expAiBatchApi` | abstract class | [Facade / service](facade.md), [INI plugin point (extension point)](ini-plugin-point.md), [Template Method / abstract base class](template-method.md) | `extension/expai/classes/lanes/expaibatchapi.php:27` | A provider's batch API: submit many requests at once, poll the batch, fetch the results, cancel, delete. |
| `expAiBatchGemini` | class extends expAiBatchApi | – | `extension/expai/classes/lanes/expaibatchgemini.php:27` | The Gemini Batch API with inline requests (https://ai.google.dev/gemini-api/docs/batch-api, read 2026-10-11): 50% of the standard prices, a 24 hour t… |
| `expAiBatchMistral` | class extends expAiBatchApi | – | `extension/expai/classes/lanes/expaibatchmistral.php:26` | Mistral batch jobs with inline requests (https://docs.mistral.ai/studio/batch-processing and https://docs.mistral.ai/api/endpoint/batch, read 2026-10… |
| `expAiBatchOpenAi` | class extends expAiBatchApi | – | `extension/expai/classes/lanes/expaibatchopenai.php:24` | The OpenAI Batch API (https://developers.openai.com/api/docs/guides/batch, read 2026-10-11): 50% of the standard prices, a 24 hour completion window,… |
| `expAiBatchOpenRouter` | class extends expAiBatchApi | [Chain of Responsibility](chain.md) | `extension/expai/classes/lanes/expaibatchopenrouter.php:25` | The OpenRouter Batch API (https://openrouter.ai/docs/batch-quickstart and https://openrouter.ai/docs/api/api-reference/batch/get-a-batch, read 2026-1… |
| `expAiBatchPollHandler` | class extends expQueueHandlerBase | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/expai/classes/queue/expaibatchpollhandler.php:24` | The job type expai.batch.poll (class system): asks the provider for the state of one batch every PollSeconds (deferred in between, without using up a… |
| `expAiBatchSubmitHandler` | class extends expQueueHandlerBase | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/expai/classes/queue/expaibatchsubmithandler.php:20` | The job type expai.batch.submit (class system): collects the waiting expai.request jobs of one model and batch lane, at most MaxRequestsPerBatch, sub… |
| `expAiBudget` | class | – | `extension/expai/classes/budget/expaibudget.php:29` | The budgets of queued requests (expai.ini [Budgets]): a monthly budget of the whole site and a budget per job group (an expqueue group: "translate th… |
| `expAiBudgetFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `extension/expai/classes/budget/expaibudgetfunctioncollection.php:14` | The template fetch function fetch( 'expai', 'budget_status' ): the monthly site budget of queued requests (expAiBudget::status()), for users with exp… |
| `expAiChain` | class | [Chain of Responsibility](chain.md), [Factory and static factory methods](factory.md) | `extension/expai/classes/chain/expaichain.php:23` | The definition of an AI chain (doc/chains.md): an ordered list of steps, each one structured call to a model, a human checkpoint or a hand-off, read… |
| `expAiChainApplier` | class implements expAiRequestApplier | [Chain of Responsibility](chain.md), [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/chain/expaichainapplier.php:14` | The applier "chain" of queued requests ([QueueSettings] Appliers[chain]): hands the stored answer of a chain step to expAiChainEngine::onAnswer(), wh… |
| `expAiChainCriteria` | class | [Chain of Responsibility](chain.md), [Utility (static helper)](utility.md) | `extension/expai/classes/chain/expaichaincriteria.php:23` | The pass criteria of a chain step ([ChainStep_.] PassCriteria[]): each entry is one comparison " ", all of them must hold. |
| `expAiChainEngine` | class | [Chain of Responsibility](chain.md), [Strategy / handler](strategy-handler.md) | `extension/expai/classes/chain/expaichainengine.php:24` | Runs AI chains (doc/chains.md) through the job queue. |
| `expAiChainFunctionCollection` | class | [Chain of Responsibility](chain.md), [Fetch function (Query Object, Facade)](fetch-function.md) | `extension/expai/classes/chain/expaichainfunctioncollection.php:18` | Template fetch functions of AI chains: |
| `expAiChainPolicy` | class | [Chain of Responsibility](chain.md) | `extension/expai/classes/chain/expaichainpolicy.php:20` | Access to AI chains: the policy expai/chain (limitation Chain) starts runs and acts on them at the checkpoint. |
| `expAiChainPrompt` | class | [Chain of Responsibility](chain.md), [Utility (static helper)](utility.md) | `extension/expai/classes/chain/expaichainprompt.php:21` | The prompts of chain steps. |
| `expAiChainRun` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Chain of Responsibility](chain.md) | `extension/expai/classes/chain/expaichainrun.php:20` | One run of an AI chain (table expai_chain_run). |
| `expAiChainSchema` | class | [Chain of Responsibility](chain.md) | `extension/expai/classes/chain/expaichainschema.php:19` | The tables of AI chains, added to expAiSchema::definition() (bin/php/expaischema.php --install creates and upgrades them on every engine like the oth… |
| `expAiChainStep` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Chain of Responsibility](chain.md) | `extension/expai/classes/chain/expaichainstep.php:19` | One step of a chain run (table expai_chain_step): each call to a model is one row with its queue job, model, lane, tokens, cost and the usage row; a… |
| `expAiChainTranslateSync` | class | [Chain of Responsibility](chain.md), [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/chain/expaichaintranslatesync.php:20` | The shipped translation hand-off of chains ([ChainSettings] TranslateHandler=expAiChainTranslateSync): the translate step hands the run's object to t… |
| `expAiChange` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/expai/classes/vector/expaichange.php:23` | A staged proposal (table expai_change, doc/review.md): one object x language x attribute that a write tool of a thread with staged review proposed in… |
| `expAiChangeApplier` | class | – | `extension/expai/classes/vector/expaichangeapplier.php:20` | Writes the accepted parts of staged changes into drafts (doc/review.md "Apply"). |
| `expAiChangeStager` | class | [Factory and static factory methods](factory.md) | `extension/expai/classes/vector/expaichangestager.php:22` | Staged review (doc/review.md): in a Vector thread with Review=staged, create_draft and update_draft call this class instead of writing a version. |
| `expAiCheckpoint` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/expai/classes/vector/expaicheckpoint.php:28` | Checkpoints of a thread (table expai_checkpoint, doc/checkpoints.md): the state at the start of every turn of the lead run and before staged changes… |
| `expAiCompactPrompt` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `extension/expai/classes/expaicompactprompt.php:29` | The compact prompt profile for small and local models (doc/local-models.md "Making a local model usable"). |
| `expAiConfig` | class | [Singleton / instance()](singleton.md) | `extension/expai/classes/expaiconfig.php:14` | The settings of expai.ini, read fresh on every call (no static state: Velocity's persistent workers serve many requests and siteaccesses with one pro… |
| `expAiContentReader` | class | – | `extension/expai/classes/expaicontentreader.php:18` | Nodes, objects, fields and classes as small arrays for the assistant to read. |
| `expAiContentWriter` | class | – | `extension/expai/classes/expaicontentwriter.php:16` | Writes drafts as the current user and only drafts: a new object under a node (content/create of the user, its class, section, parent class and langua… |
| `expAiConversation` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/expai/classes/expaiconversation.php:16` | The conversation of one user on one node (table expai_conversation). |
| `expAiConversationBusyException` | class extends expAiException | [Exception hierarchy](exception.md) | `extension/expai/classes/expaiconversationlock.php:150` | Another step of the conversation holds its lock (expAiConversationLock). |
| `expAiConversationLock` | class | – | `extension/expai/classes/expaiconversationlock.php:19` | The lock of a conversation while one step runs (expAiAssistant::step()): a second step of the same conversation - a second browser tab, a double clic… |
| `expAiCost` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/expaicost.php:24` | The one place costs are computed: stored token counts (input, output, cache write, cache read) x the price table of expai.ini [Pricing], at read time… |
| `expAiDashboard` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/expaidashboard.php:15` | What expai/dashboard and the dashboard block show for the current user: their drafts from the log, the versions written with Exponential Intelligence… |
| `expAiDataPolicy` | class | [Factory and static factory methods](factory.md) | `extension/expai/classes/expaidatapolicy.php:31` | Where content may be sent: expai.ini [DataPolicy]. |
| `expAiDiff` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/diff/expaidiff.php:27` | The diff of two versions or languages of an object, per attribute, at block and sentence level (doc/diff.md). |
| `expAiDiffResult` | class | – | `extension/expai/classes/diff/expaidiffresult.php:14` | The result of expAiDiff::compare(): per attribute the blocks, operations, hunks and metrics, and the metrics of the whole object (characters weighted… |
| `expAiDraftLog` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/expai/classes/expaidraftlog.php:20` | One version the assistant wrote (table expai_draft_log): the object and version, the user it was written for, when, a summary of the request, the mod… |
| `expAiDraftRevert` | class | [Factory and static factory methods](factory.md) | `extension/expai/classes/diff/expaidraftrevert.php:29` | "What changed" of a draft the assistant wrote directly (the Intelligence tab, a Vector thread with Review: Direct; doc/review.md "Direct mode: what c… |
| `expAiDraftRevertServerFunctions` | class extends ezjscServerFunctions | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expai/classes/diff/expaidraftrevertserverfunctions.php:20` | "What changed" of a draft written directly (ezjscore.ini [ezjscServer_expairevert], doc/review.md): |
| `expAiEditForm` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/expaieditform.php:15` | The content edit form side: field actions and "fill the fields from a brief". |
| `expAiEditPresets` | class extends expAiEditForm | [Utility (static helper)](utility.md) | `extension/expai/classes/fieldactions/expaieditpresets.php:25` | Editor presets and the Improve menu of the content edit form (doc/presets.md): |
| `expAiException` | class extends Exception | [Exception hierarchy](exception.md) | `extension/expai/classes/expaiexception.php:15` | A failure of expai. |
| `expAiExchangeRates` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/expaiexchangerates.php:24` | The reference rates of the European Central Bank for converting the prices and reported costs of a provider that prices in another currency (Melious:… |
| `expAiExchangeRatesHandler` | class extends expQueueHandlerBase | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/expai/classes/queue/expaiexchangerateshandler.php:15` | The job type expai.exchange.rates (class system, no model call, #392): fetches the reference rates of the European Central Bank once a day (expAiExch… |
| `expAiFieldAction` | interface | [INI plugin point (extension point)](ini-plugin-point.md), [Interface (contract)](interface.md) | `extension/expai/classes/fieldactions/expaifieldaction.php:16` | An action next to a field of the content edit form (write, rewrite, shorten, translate, suggest, ...). |
| `expAiFieldActionRegistry` | class | [Registry](registry.md) | `extension/expai/classes/fieldactions/expaifieldactionregistry.php:13` | The field actions of expai.ini [EditViewSettings] FieldActions[], and the edit form's description of them. |
| `expAiFieldRequest` | class | – | `extension/expai/classes/fieldactions/expaifieldrequest.php:14` | What a field action works on: the draft (checked: the current user's own draft, editable), the field, its current value in the form, the other fields… |
| `expAiFollowup` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/expai/classes/vector/expaifollowup.php:16` | A message queued while a run works (table expai_followup). |
| `expAiFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `extension/expai/classes/expaifunctioncollection.php:14` | The template fetch functions of expai (modules/expai/function_definition.php). |
| `expAiGate` | class | – | `extension/expai/classes/expaigate.php:39` | The master switch and the veto hook (doc/switch-and-veto.md): checked before every request expai sends to a provider, and before a request is queued. |
| `expAiGitRunner` | class | – | `extension/expai/classes/history/expaigitrunner.php:16` | Runs read-only git commands for project_history without a shell: proc_open() with an argument list (no interpolation, no globbing), the binary of [Hi… |
| `expAiHelpLinks` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/expaihelplinks.php:21` | The addresses of the extension's documentation. |
| `expAiHttpClient` | class | – | `extension/expai/classes/expaihttpclient.php:16` | JSON POST to an AI provider (or an MCP server) with curl: http(s) only, no redirects, a total and a connect timeout and a limit on the size of the an… |
| `expAiImageInput` | class | [Factory and static factory methods](factory.md) | `extension/expai/classes/alttext/expaiimageinput.php:21` | An image prepared for a vision model (doc/alt-text.md): read from an ezimage field (the stored file of the version, never an upload that is not store… |
| `expaiInfo` | class | [Value object / struct](value-object.md) | `extension/expai/ezinfo.php:7` | – |
| `expAiJsonSchema` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/expaijsonschema.php:15` | A small check of tool input against the tool's JSON schema (the subset expai's tools use: type, required, properties, additionalProperties false, ite… |
| `expAiLane` | class | [Factory and static factory methods](factory.md) | `extension/expai/classes/lanes/expailane.php:37` | A lane: one way of sending a request to a provider, with its own price, speed and retention (expai.ini [Lane_.]). |
| `expAiLanePolicy` | class | – | `extension/expai/classes/lanes/expailanepolicy.php:24` | Which lane a queued request takes (expai.ini [LanePolicy]): |
| `expAiLimits` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/expailimits.php:34` | The limits of expai.ini [Limits], checked before every request to the model (expAiUsageRecorder) and shown on the dashboard. |
| `expAiLinks` | class | [Factory and static factory methods](factory.md) | `extension/expai/classes/expailinks.php:14` | The icon links of a row in expai/log and on the dashboard: View, Edit, Prompt again, Intelligence tab. |
| `expAiLocalModel` | class | – | `extension/expai/classes/expailocalmodel.php:17` | A local Ollama server of a provider with Managed=ollama, for the view expai/models. |
| `expAiMcp` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/mcp/expaimcp.php:22` | expai as an MCP client, two layers: |
| `expAiMcpBridgeTool` | class extends expAiTool | [Adapter / bridge / wrapper](adapter.md), [Factory and static factory methods](factory.md) | `extension/expai/classes/mcp/expaimcpbridgetool.php:22` | A bridge-safe read tool of the extension expmcp, called in process as the current user (exp__). |
| `expAiMcpClient` | class | [Factory and static factory methods](factory.md) | `extension/expai/classes/mcp/expaimcpclient.php:21` | A small client for one MCP server over the streamable HTTP transport (JSON-RPC 2.0 POSTs; the answer is JSON or an event stream whose data lines carr… |
| `expAiMcpRemoteTool` | class extends expAiTool | – | `extension/expai/classes/mcp/expaimcpremotetool.php:17` | A tool of an external MCP server (mcp____), allowlisted in [McpServer_] AllowedTools[]. |
| `expAiModelFallback` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/expaimodelfallback.php:35` | The fallback to the site default model ([AISettings] FallbackToDefaultModel, default enabled): when the model a request asked for (the user's choice,… |
| `expAiModelServerFunctions` | class extends ezjscServerFunctions | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expai/classes/expaimodelserverfunctions.php:24` | The ezjscore functions of the "Test prompt" and "Load model" actions of expai/models (ezjscore.ini [ezjscServer_expaimodel]; the caller needs ezjscor… |
| `expAiModelStats` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/expaimodelstats.php:25` | The track record of each model for the "Models" card of expai/dashboard: from the usage log of a period (one user or the site) the requests, the succ… |
| `expAiModelTest` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/expaimodeltest.php:25` | The "Test prompt" and "Load model" actions of expai/models as jobs of the job queue (job type expai.model.test, classes/queue/expaimodeltesthandler.p… |
| `expAiModelTestHandler` | class extends expQueueHandlerBase | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/expai/classes/queue/expaimodeltesthandler.php:16` | The job type expai.model.test: the "Test prompt" (action test) and "Load model" (action load) of expai/models, run by the worker as the user who clic… |
| `expAiObjects` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/expaiobjects.php:16` | Content objects by an id expai stored earlier (the draft log, staged changes, checkpoints, chain runs, queued jobs): such an object may have been rem… |
| `expAiOllamaNativeProvider` | class implements expAiProvider | [Factory and static factory methods](factory.md), [Strategy / handler](strategy-handler.md) | `extension/expai/classes/expaiollamanativeprovider.php:20` | A model of a local Ollama server (a provider with Managed=ollama) spoken to through Ollama's own API on the loopback address instead of its OpenAI co… |
| `expAiOpenAiChatProvider` | class extends expAiProviderBase | [Strategy / handler](strategy-handler.md) | `extension/expai/classes/expaiopenaichatprovider.php:36` | The OpenAI Chat Completions API and every server that speaks it ([Provider_] Type=openai_chat): OpenAI, Hugging Face Inference Providers (router), Op… |
| `expAiPackageDraftListener` | class | [Observer / listener / event](observer.md) | `extension/expai/classes/expaipackagedraftlistener.php:21` | Listens to the kernel's event package/install/drafts (eZPackage::notifyDraftsInstalled(): after any install of a package as drafts, in Setup › Packag… |
| `expAiPlan` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/expai/classes/vector/expaiplan.php:19` | A plan of a thread (table expai_plan, doc/plan-mode.md): what propose_plan stored for the editor to approve. |
| `expAiPlanBatch` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/vector/expaiplanbatch.php:22` | An approved plan on a batch lane (doc/parallel-runs.md "On a batch lane"): when the thread's lane is a batch lane (Anthropic Message Batches, the Ope… |
| `expAiPlanEstimate` | class | – | `extension/expai/classes/vector/expaiplanestimate.php:21` | The cost estimate of a plan (doc/plan-mode.md "The estimate"), shown as a range before the editor approves. |
| `expAiPlanItemApplier` | class implements expAiRequestApplier | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/vector/expaiplanitemapplier.php:17` | The applier vector_plan_item ([QueueSettings] Appliers[]): the answer of one item of an approved plan that ran on a batch lane (expAiPlanBatch) is st… |
| `expAiPlanner` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/vector/expaiplanner.php:22` | Plan mode (doc/plan-mode.md): the plan propose_plan stores, its validation against the thread's scope and the editor's permissions, the editor's appr… |
| `expAiPolicy` | class | – | `extension/expai/classes/expaipolicy.php:15` | The policies of expai with limitations (module.php): expai/model_use (limitation Provider) and expai/mcp (limitation Server). |
| `expAiPolicyMigration` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/expaipolicymigration.php:15` | Renames the policy functions of expai that pre-release builds called differently (RENAMED), in every role, with their limitations (they belong to the… |
| `expAiPresetFieldRequest` | class extends expAiFieldRequest | – | `extension/expai/classes/fieldactions/expaipresetfieldrequest.php:15` | A field request of a preset or an Improve entry (expAiEditPresets). |
| `expAiPresetFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `extension/expai/classes/fieldactions/expaipresetfunctioncollection.php:15` | fetch( 'expai', 'edit_presets', hash( 'object', $object, 'attributes', $content_attributes ) ): the presets, the Improve menu and the defaults line o… |
| `expAiPresetServerFunctions` | class extends expAiServerFunctions | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/fieldactions/expaipresetserverfunctions.php:21` | The ezjscore function of the editor presets and the Improve menu (ezjscore.ini [ezjscServer_expaipreset]; the caller needs ezjscore/call with the fun… |
| `expAiProbe` | class | – | `extension/expai/classes/expaiprobe.php:23` | The probe of a model's tool calling (bin/php/expaiprobe.php) and the write gate built on it. |
| `expAiProbeResult` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/expai/classes/expaiproberesult.php:15` | One run of bin/php/expaiprobe.php (table expai_probe_result): the model alias, provider and model id, the tool mode, live or mock, the tasks, how man… |
| `expAiProjectHistory` | class | – | `extension/expai/classes/history/expaiprojecthistory.php:19` | The history behind project_history: commits of local git clones ([HistoryRepository_] Path, under one of [HistorySettings] Roots[]) and release notes… |
| `expAiPromptedToolProvider` | class implements expAiProvider | [Strategy / handler](strategy-handler.md) | `extension/expai/classes/expaipromptedtoolprovider.php:21` | Tool use for models without native tool calling ([Model_] ToolUse=prompted): the tools are described in the instructions ("tool protocol") and the mo… |
| `expAiPromptFieldAction` | class implements expAiFieldAction | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/fieldactions/expaipromptfieldaction.php:15` | A field action driven by its INI section: [FieldAction_] Instruction is the task; the object, the field, its current value, the other fields and the… |
| `expAiPrompts` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/expaiprompts.php:15` | The instructions (system prompts) of the assistant. |
| `expAiProvider` | interface | [INI plugin point (extension point)](ini-plugin-point.md), [Interface (contract)](interface.md), [Strategy / handler](strategy-handler.md) | `extension/expai/classes/expaiprovider.php:16` | A model backend of expai. |
| `expAiProviderBase` | abstract class implements expAiProvider | [Strategy / handler](strategy-handler.md), [Template Method / abstract base class](template-method.md) | `extension/expai/classes/expaiproviderbase.php:16` | What every built-in provider shares: its settings (the [Provider_] values merged with the [Model_] values, see expAiProviderRegistry::settingsFor()),… |
| `expAiProviderHealth` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/expaiproviderhealth.php:24` | Whether a provider can be reached at the moment, for the model pickers (a stopped or unreachable model is shown and cannot be chosen) and for the fal… |
| `expAiProviderRegistry` | class | [Registry](registry.md), [Singleton / instance()](singleton.md) | `extension/expai/classes/expaiproviderregistry.php:24` | The providers and models of expai.ini: |
| `expAiPublisher` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/expaipublisher.php:15` | What the editor does with the drafts the assistant wrote: keep them ("Save as drafts"), send them ("Send for approval": the regular content/publish o… |
| `expAiQueue` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/queue/expaiqueue.php:25` | Queues requests to a model through the extension expqueue (job type expai.request): the lane is chosen (expAiLanePolicy), the cost estimated and rese… |
| `expAiQueueKick` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/queue/expaiqueuekick.php:39` | Starts detached queue workers right after a job is queued, so the job does not wait for the cron part of expqueue (doc/parallel-runs.md "Workers"). |
| `expAiQueueSchema` | class | – | `extension/expai/classes/queue/expaiqueueschema.php:20` | The tables and columns of lanes, batches and budgets, added to expAiSchema::definition() (so bin/php/expaischema.php --install creates and upgrades t… |
| `expAiRequestApplier` | interface | [INI plugin point (extension point)](ini-plugin-point.md), [Interface (contract)](interface.md) | `extension/expai/classes/queue/expairequestapplier.php:18` | Applies the answer of a queued request (expai.request), e.g. |
| `expAiRequestHandler` | class extends expQueueHandlerBase | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/expai/classes/queue/expairequesthandler.php:28` | The job type expai.request: one call to a model on a lane (expAiQueue::request() queues it). |
| `expAiRichTextTokens` | class | [Factory and static factory methods](factory.md) | `extension/expai/classes/expairichtexttokens.php:29` | Rich text (ezxmltext) for a model without losing structure (doc/configuration.md "[RichTextSettings]"). |
| `expAiRunWatch` | class extends ezjscServerFunctions | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expai/classes/expairunwatch.php:20` | "Watch this run" of the dashboard's "Start writing": the request opens the Intelligence tab of the chosen location, where it becomes a thread when th… |
| `expAiSchema` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/expaischema.php:24` | The two tables of expai, defined once here in the kernel's generic schema format. |
| `expAiScript` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/expaiscript.php:22` | The siteaccess of a command line script of expai (bin/php). |
| `expAiSegmenter` | interface | [INI plugin point (extension point)](ini-plugin-point.md), [Interface (contract)](interface.md) | `extension/expai/classes/diff/expaisegmenter.php:28` | Cuts the value of one datatype into blocks and sentences, and puts values together again (doc/diff.md). |
| `expAiSegmenterBase` | abstract class implements expAiSegmenter | [Template Method / abstract base class](template-method.md) | `extension/expai/classes/diff/expaisegmenterbase.php:14` | The plain-text segmenters' common part: blocks are pieces of text joined with a separator; merge() and compose() build the list of pieces and join it. |
| `expAiSegmenterGeneric` | class extends expAiSegmenterBase | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/diff/expaisegmentergeneric.php:15` | The fallback for datatypes without a segmenter of their own: the value as a string (toString()) is one block, not split into sentences. |
| `expAiSegmenterKeyword` | class extends expAiSegmenterBase | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/diff/expaisegmenterkeyword.php:14` | ezkeyword: a set. |
| `expAiSegmenterRegistry` | class | [Registry](registry.md), [Utility (static helper)](utility.md) | `extension/expai/classes/diff/expaisegmenterregistry.php:15` | The segmenter of a datatype: expai.ini [DiffSettings] Segmenters[]=, else FallbackSegmenter (expAiSegmenterGeneric). |
| `expAiSegmenterRichText` | class implements expAiSegmenter | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/diff/expaisegmenterrichtext.php:28` | Rich text (ezxmltext storage XML, or HTML): structure-aware blocks (doc/diff.md). |
| `expAiSegmenterString` | class extends expAiSegmenterBase | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/diff/expaisegmenterstring.php:13` | ezstring (and ezemail, ezisbn): the value is one block, split into sentences. |
| `expAiSegmenterText` | class extends expAiSegmenterBase | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/diff/expaisegmentertext.php:14` | eztext: blocks are paragraphs (separated by blank lines), split into sentences. |
| `expAiSentences` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/diff/expaisentences.php:21` | Text normalisation for hashing and a language-aware sentence splitter (doc/diff.md). |
| `expAiServerFunctions` | class extends ezjscServerFunctions | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expai/classes/expaiserverfunctions.php:24` | The ezjscore server functions of expai (ezjscore.ini [ezjscServer_expai]): |
| `expAiServiceTool` | class extends expAiTool | – | `extension/expai/classes/tools/expaiservicetool.php:20` | One read service of expservices as a tool of the assistant (see expAiServiceTools and doc/service-tools.md). |
| `expAiServiceTools` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/tools/expaiservicetools.php:28` | Service tools: the read services of the extension expservices that the assistant may call (doc/service-tools.md). |
| `expAiSlowModel` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/vector/expaislowmodel.php:23` | A running turn on a slow model (doc/parallel-runs.md "Slow models"): Vector and the Intelligence tab show "Running on - about N s per step (step k)"… |
| `expAiSummary` | class | – | `extension/expai/classes/expaisummary.php:15` | The content of the "EI: Summary" page (expai/summary, doc/summary.md): what goes where, kept how long, used by whom and at what cost, read from the s… |
| `expAiTemplateOperators` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/expai/autoloads/expaitemplateoperators.php:20` | {$amount\|expai_money( $approx )} "$0.06", "‹ $0.01", with "≈" for an approximate cost (expAiCost::money()) {$tokens\|expai_tokens} "950", "21.6k", "1.… |
| `expAiText` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/expaitext.php:14` | Text helpers: shortening, the HTML subset the assistant writes rich text in, and a plain HTML view of stored ezxmltext for the assistant to read. |
| `expAiThread` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/expai/classes/vector/expaithread.php:22` | A thread (table expai_thread): one unit of work of one editor. |
| `expAiThreadAccess` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/vector/expaithreadaccess.php:23` | Who may see and use a thread, and what the policy expai/vector allows (doc/permissions.md, doc/threads.md). |
| `expAiThreadEvent` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/expai/classes/vector/expaithreadevent.php:17` | The append-only event stream of a thread (table expai_thread_event). |
| `expAiThreadFanout` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/vector/expaithreadfanout.php:27` | Parallel runs of an approved plan (doc/parallel-runs.md). |
| `expAiThreadFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `extension/expai/classes/vector/expaithreadfunctioncollection.php:17` | Template fetch functions of threads (function_definition.php): |
| `expAiThreadItemHandler` | class extends expQueueHandlerBase | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/expai/classes/vector/expaithreaditemhandler.php:22` | The job type expai.thread.item: one item of an approved plan as a child run of the thread (doc/parallel-runs.md), side by side with the other items. |
| `expAiThreadMigration` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/vector/expaithreadmigration.php:17` | Turns the panel conversations of an earlier expai into threads (origin "panel", scope: the conversation's node, review "direct"), with a lead run eac… |
| `expAiThreadPrompts` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/vector/expaithreadprompts.php:15` | The texts a Vector thread adds for the model (English, as the rest of the instructions): the scope of the thread, plan mode (read tools and propose_p… |
| `expAiThreadRetention` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/vector/expaithreadretention.php:36` | The retention of thread data (owner decision 2026-10-11: events, proposals and checkpoints are kept 30 days), prune() run by the cronjob part "expai"… |
| `expAiThreadRun` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/expai/classes/vector/expaithreadrun.php:16` | One agent run of a thread (table expai_thread_run). |
| `expAiThreadRunner` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/vector/expaithreadrunner.php:23` | Background runs of threads (doc/vector.md "Background runs"). |
| `expAiThreadSchema` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/vector/expaithreadschema.php:27` | The tables of threads and Vector (doc/threads.md, doc/database.md), in the kernel's generic schema format. |
| `expAiThreadTurnHandler` | class extends expQueueHandlerBase | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/expai/classes/vector/expaithreadturnhandler.php:23` | The job type expai.thread.turn: one turn of a thread (the editor's message and every step until the model answers in words) run by the queue worker i… |
| `expAiTool` | abstract class | [INI plugin point (extension point)](ini-plugin-point.md), [Template Method / abstract base class](template-method.md) | `extension/expai/classes/expaitool.php:16` | A tool the assistant can call. |
| `expAiToolContext` | class | – | `extension/expai/classes/expaitoolcontext.php:14` | What a tool knows about the call: the user (always eZUser::currentUser()), the node the panel is open on, the conversation, the model, the editor's r… |
| `expAiToolCreateDraft` | class extends expAiTool | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/tools/expaitoolcreatedraft.php:12` | create_draft: a new object as a draft of the current user under a node (content/create of the user). |
| `expAiToolGetClassDefinition` | class extends expAiTool | [INI plugin point (extension point)](ini-plugin-point.md), [Value object / struct](value-object.md) | `extension/expai/classes/tools/expaitoolgetclassdefinition.php:9` | get_class_definition: the fields of a content class |
| `expAiToolGetNode` | class extends expAiTool | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/tools/expaitoolgetnode.php:9` | get_node: a node and the fields of its object (current version), as the current user may read them |
| `expAiToolGetTreeContext` | class extends expAiTool | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/tools/expaitoolgettreecontext.php:9` | get_tree_context: where a node sits: its ancestors, its siblings and the number of its children |
| `expAiToolListChildren` | class extends expAiTool | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/tools/expaitoollistchildren.php:9` | list_children: the children of a node the current user may read |
| `expAiToolListCreatableClasses` | class extends expAiTool | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/tools/expaitoollistcreatableclasses.php:9` | list_creatable_classes: the classes the current user may create under a node |
| `expAiToolProjectHistory` | class extends expAiWebTool | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/tools/expaitoolprojecthistory.php:13` | project_history: recent commits (short hash, date, title; the author only with [HistorySettings] ShowAuthors) and release notes of the repositories o… |
| `expAiToolProposePlan` | class extends expAiTool | – | `extension/expai/classes/tools/expaitoolproposeplan.php:14` | propose_plan (Vector threads only, doc/plan-mode.md): stores a structured plan for the editor to approve. |
| `expAiToolRegistry` | class | [Registry](registry.md) | `extension/expai/classes/expaitoolregistry.php:16` | The tools of expai.ini [ToolSettings] Tools[], each [Tool_] Class=, plus the MCP tools the current user may use (expAiMcp::tools(): the in-process br… |
| `expAiToolSearchContent` | class extends expAiTool | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/tools/expaitoolsearchcontent.php:17` | search_content: full-text search over the content, through the site's search engine (eZSearch: eZ Find's Solr when ezfind is active, else the kernel'… |
| `expAiToolUpdateDraft` | class extends expAiTool | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/tools/expaitoolupdatedraft.php:12` | update_draft: writes fields into a draft of an existing object: the draft this conversation wrote for it when there is one, else a new version (conte… |
| `expAiToolWebFetch` | class extends expAiWebTool | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/tools/expaitoolwebfetch.php:13` | web_fetch: reads one page of an allowlisted site ([WebSettings] AllowedDomains[]) as Markdown (title, headings, links with absolute URLs), cut to [We… |
| `expAiToolWebSearch` | class extends expAiWebTool | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/tools/expaitoolwebsearch.php:14` | web_search: a web search through the provider of [SearchSettings] Provider (Brave Search by default); results are title, URL and snippet. |
| `expAiTranslationCheckHandler` | class extends expQueueHandlerBase | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/expai/classes/queue/expaitranslationcheckhandler.php:15` | The job type expai.translation.check (class system, no model call): after a source-language version was published, decides for each translation of th… |
| `expAiTranslationSync` | class | – | `extension/expai/classes/diff/expaitranslationsync.php:32` | Keeps translations in step with their source language (doc/translation-sync.md). |
| `expAiTranslationSyncApplier` | class implements expAiRequestApplier | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/diff/expaitranslationsyncapplier.php:15` | The applier "translation_sync" (expai.ini [QueueSettings] Appliers[translation_sync]): writes the answer of a translation patch or full translation i… |
| `expAiTranslationSyncServerFunctions` | class extends ezjscServerFunctions | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expai/classes/diff/expaitranslationsyncserverfunctions.php:23` | The ezjscore functions of the "Translations" section of the Intelligence tab (ezjscore.ini [ezjscServer_expaitranslation]; the caller needs ezjscore/… |
| `expAiTranslationSyncType` | class extends eZWorkflowEventType | [Workflow event type (Command, plugin)](workflow-event.md) | `extension/expai/eventtypes/event/expaitranslationsync/expaitranslationsynctype.php:16` | The workflow event "Exponential Intelligence: translation sync" (event_expaitranslationsync). |
| `expAiTurnRecord` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/vector/expaiturnrecord.php:18` | The record of each turn of a thread (doc/parallel-runs.md "The record of a turn"), from the usage log: the model that actually answered (and the one… |
| `expAiUsage` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/expaiusage.php:14` | Usage tracking: recording a request (expAiUsageRecorder calls record()), the daily token limit, prices and cost, the filtered list of expai/log with… |
| `expAiUsageLog` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/expai/classes/expaiusagelog.php:17` | One request to the model (table expai_usage_log): when, for whom (user id, never a name), where (node, conversation, turn), what for (action), the mo… |
| `expAiUsageRecorder` | class implements expAiProvider | [Strategy / handler](strategy-handler.md) | `extension/expai/classes/expaiusagerecorder.php:16` | A provider that records every request of the provider it wraps in expai_usage_log (expAiUsage) and enforces [Limits] (expAiLimits) before each reques… |
| `expAiVector` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/vector/expaivector.php:17` | Vector (expai/vector, doc/vector.md): what the workspace needs from the rest of expai. |
| `expAiVectorPolicy` | class | – | `extension/expai/classes/vector/expaivectorpolicy.php:14` | The value lists the role editor offers for the limitations Scope and Review of expai/vector (module.php). |
| `expAiVectorServerFunctions` | class extends ezjscServerFunctions | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expai/classes/vector/expaivectorserverfunctions.php:23` | The ezjscore functions of threads, Vector and the keyboard (ezjscore.ini [ezjscServer_expaivector]; the caller needs ezjscore/call with the function… |
| `expAiVectorStream` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/vector/expaivectorstream.php:19` | The transport of thread events (modules/expai/stream.php): which one applies, the stream slots, the stream loop. |
| `expAiVectorStrings` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/vector/expaivectorstrings.php:14` | The texts of the command palette and the shortcut sheet (context extension/expai/keyboard). |
| `expAiVetoException` | class extends expAiException | [Exception hierarchy](exception.md) | `extension/expai/classes/expaigate.php:14` | A request refused by the veto hook (a rule of [RequestVeto] or a listener of expai/request/veto). |
| `expAiWebBudget` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/expaiwebbudget.php:26` | The time a web request may spend on one model call. |
| `expAiWebFetcher` | class | – | `extension/expai/classes/web/expaiwebfetcher.php:17` | Fetches one web page for web_fetch: GET only, http(s) only, the host on [WebSettings] AllowedDomains[] (or given as allowed by the caller), the SSRF… |
| `expAiWebGuard` | class | [Factory and static factory methods](factory.md) | `extension/expai/classes/web/expaiwebguard.php:17` | The SSRF guard of the web tools. |
| `expAiWebHtml` | class | [Factory and static factory methods](factory.md) | `extension/expai/classes/web/expaiwebhtml.php:16` | HTML to readable Markdown for the model: the title, headings (#), paragraphs, lists, quotes, code, tables as rows and links as [text](absolute URL). |
| `expAiWebPolicy` | class | – | `extension/expai/classes/web/expaiwebpolicy.php:14` | The value list of the limitation Tool of the policy expai/web (module.php): the tools that read outside the content tree. |
| `expAiWebRobots` | class | – | `extension/expai/classes/web/expaiwebrobots.php:15` | robots.txt as RFC 9309 reads it: the group of the product token ExponentialIntelligence, else the group "*"; the longest matching Allow or Disallow r… |
| `expAiWebSearchBrave` | class extends expAiWebSearchProvider | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expai/classes/web/expaiwebsearchbrave.php:15` | Brave Search API (web search): GET ?q=...&count=... |
| `expAiWebSearchProvider` | abstract class | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md), [Template Method / abstract base class](template-method.md) | `extension/expai/classes/web/expaiwebsearchprovider.php:16` | A web search service for web_search. |
| `expAiWebTool` | abstract class extends expAiTool | [Template Method / abstract base class](template-method.md) | `extension/expai/classes/web/expaiwebtool.php:19` | The base of the tools that read outside the content tree (web_fetch, web_search, project_history): - available(): offered only to a user with the pol… |
| `expAiWebUrl` | class | [Utility (static helper)](utility.md) | `extension/expai/classes/web/expaiweburl.php:16` | URLs of the web tools: parsing (http and https only, no user name or password, ports 80 and 443 only, at most 2048 characters), host normalization (l… |

## Module views (9)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/expai/log` | `log.php` | write | `extension/expai/modules/expai/module.php:41` |
| `/expai/budgets` | `budgets.php` | log | `extension/expai/modules/expai/module.php:52` |
| `/expai/dashboard` | `dashboard.php` | write | `extension/expai/modules/expai/module.php:60` |
| `/expai/models` | `models.php` | model_manage | `extension/expai/modules/expai/module.php:72` |
| `/expai/chain` | `chain.php` | chain | `extension/expai/modules/expai/module.php:83` |
| `/expai/vector` | `vector.php` | vector | `extension/expai/modules/expai/module.php:94` |
| `/expai/stream` | `stream.php` | vector | `extension/expai/modules/expai/module.php:102` |
| `/expai/summary` | `summary.php` | summary | `extension/expai/modules/expai/module.php:110` |
| `/expai/alttext` | `alttext.php` | write | `extension/expai/modules/expai/module.php:157` |

## Policy functions (13)

| Function | Limitations | Defined in |
|---|---|---|
| `expai/write` | – | `extension/expai/modules/expai/module.php:121` |
| `expai/log` | – | `extension/expai/modules/expai/module.php:122` |
| `expai/settings` | – | `extension/expai/modules/expai/module.php:123` |
| `expai/model_use` | Provider | `extension/expai/modules/expai/module.php:124` |
| `expai/model_manage` | – | `extension/expai/modules/expai/module.php:125` |
| `expai/lane_fast` | – | `extension/expai/modules/expai/module.php:126` |
| `expai/mcp` | Server | `extension/expai/modules/expai/module.php:127` |
| `expai/chain` | – | `extension/expai/modules/expai/module.php:129` |
| `expai/vector` | – | `extension/expai/modules/expai/module.php:133` |
| `expai/web` | – | `extension/expai/modules/expai/module.php:140` |
| `expai/translation_sync` | – | `extension/expai/modules/expai/module.php:145` |
| `expai/services` | – | `extension/expai/modules/expai/module.php:149` |
| `expai/summary` | – | `extension/expai/modules/expai/module.php:153` |

## Fetch functions (12)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'expai', 'my_drafts' )` | `expAiFunctionCollection::fetchMyDrafts()` | `extension/expai/modules/expai/function_definition.php:16` |
| `fetch( 'expai', 'my_draft_count' )` | `expAiFunctionCollection::fetchMyDraftCount()` | `extension/expai/modules/expai/function_definition.php:22` |
| `fetch( 'expai', 'waiting_for_my_approval' )` | `expAiFunctionCollection::fetchWaitingForMyApproval()` | `extension/expai/modules/expai/function_definition.php:27` |
| `fetch( 'expai', 'budget_status' )` | `expAiBudgetFunctionCollection::fetchStatus()` | `extension/expai/modules/expai/function_definition.php:33` |
| `fetch( 'expai', 'chains' )` | `expAiChainFunctionCollection::fetchChains()` | `extension/expai/modules/expai/function_definition.php:40` |
| `fetch( 'expai', 'chain_runs' )` | `expAiChainFunctionCollection::fetchRuns()` | `extension/expai/modules/expai/function_definition.php:45` |
| `fetch( 'expai', 'models' )` | `expAiFunctionCollection::fetchModels()` | `extension/expai/modules/expai/function_definition.php:51` |
| `fetch( 'expai', 'my_threads' )` | `expAiThreadFunctionCollection::fetchMyThreads()` | `extension/expai/modules/expai/function_definition.php:57` |
| `fetch( 'expai', 'switched_on' )` | `expAiGate::fetchSwitchedOn()` | `extension/expai/modules/expai/function_definition.php:64` |
| `fetch( 'expai', 'alt_text_model' )` | `expAiAltTextFunctionCollection::fetchModel()` | `extension/expai/modules/expai/function_definition.php:72` |
| `fetch( 'expai', 'alt_text_required' )` | `expAiAltTextFunctionCollection::fetchAltTextRequired()` | `extension/expai/modules/expai/function_definition.php:79` |
| `fetch( 'expai', 'edit_presets' )` | `expAiPresetFunctionCollection::fetchEditPresets()` | `extension/expai/modules/expai/function_definition.php:87` |

## Template operators (1)

| Operators | Class | Registered in |
|---|---|---|
| expai_money, expai_tokens, expai_number, expai_json, expai_retention, expai_help_url | `expAiTemplateOperators` | `extension/expai/autoloads/eztemplateautoload.php:11` |

## Cronjob parts (4)

| Cronjob | File | Description |
|---|---|---|
| `expai_exchange_rates` | `extension/expai/cronjobs/expai_exchange_rates.php` | Cronjob expai_exchange_rates: refreshes the reference rates of the European Central Bank once a day (the conversion of prices in another currency, ex… |
| `expai_prune` | `extension/expai/cronjobs/expai_prune.php` | Cronjob expai_prune: marks draft log rows whose content object was removed as "deleted"; removes old threads and conversations, blanks old request su… |
| `expai_queue` | `extension/expai/cronjobs/expai_queue.php` | Cronjob expai_queue (part "expai"): resumes the queued requests a budget held, as soon as they fit (a new month, a raised budget, requests that cost… |
| `expai_thread_prune` | `extension/expai/cronjobs/expai_thread_prune.php` | Cronjob expai_thread_prune (part "expai"): removes thread events, staged proposals, checkpoints and finished plans and follow-ups older than expai.in… |

## INI plugin points (69)

| Setting | Class | Set in |
|---|---|---|
| `[AuditEventSettings] Branches[expai]` | `expAiAuditBranch` | `extension/expai/settings/audit.ini.append.php:12` |
| `[AuditSourceSettings] Sources[expai]` | `expAiAuditSource` | `extension/expai/settings/audit.ini.append.php:28` |
| `[Tool_get_node] Class` | `expAiToolGetNode` | `extension/expai/settings/expai.ini.append.php:670` |
| `[Tool_list_children] Class` | `expAiToolListChildren` | `extension/expai/settings/expai.ini.append.php:673` |
| `[Tool_get_tree_context] Class` | `expAiToolGetTreeContext` | `extension/expai/settings/expai.ini.append.php:676` |
| `[Tool_list_creatable_classes] Class` | `expAiToolListCreatableClasses` | `extension/expai/settings/expai.ini.append.php:679` |
| `[Tool_get_class_definition] Class` | `expAiToolGetClassDefinition` | `extension/expai/settings/expai.ini.append.php:682` |
| `[Tool_search_content] Class` | `expAiToolSearchContent` | `extension/expai/settings/expai.ini.append.php:685` |
| `[Tool_create_draft] Class` | `expAiToolCreateDraft` | `extension/expai/settings/expai.ini.append.php:688` |
| `[Tool_update_draft] Class` | `expAiToolUpdateDraft` | `extension/expai/settings/expai.ini.append.php:691` |
| `[QueueSettings] Appliers[chain]` | `expAiChainApplier` | `extension/expai/settings/expai.ini.append.php:719` |
| `[ChainSettings] TranslateHandler` | `expAiChainTranslateSync` | `extension/expai/settings/expai.ini.append.php:739` |
| `[FieldAction_write] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:860` |
| `[FieldAction_rewrite] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:869` |
| `[FieldAction_shorten] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:878` |
| `[FieldAction_translate] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:886` |
| `[FieldAction_suggest] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:895` |
| `[QueueSettings] Appliers[vector_plan_item]` | `expAiPlanItemApplier` | `extension/expai/settings/expai.ini.append.php:1328` |
| `[Tool_web_fetch] Class` | `expAiToolWebFetch` | `extension/expai/settings/expai.ini.append.php:1358` |
| `[Tool_web_search] Class` | `expAiToolWebSearch` | `extension/expai/settings/expai.ini.append.php:1361` |
| `[Tool_project_history] Class` | `expAiToolProjectHistory` | `extension/expai/settings/expai.ini.append.php:1364` |
| `[SearchProvider_brave] Class` | `expAiWebSearchBrave` | `extension/expai/settings/expai.ini.append.php:1400` |
| `[DiffSettings] Segmenters[ezstring]` | `expAiSegmenterString` | `extension/expai/settings/expai.ini.append.php:1451` |
| `[DiffSettings] Segmenters[ezemail]` | `expAiSegmenterString` | `extension/expai/settings/expai.ini.append.php:1452` |
| `[DiffSettings] Segmenters[ezisbn]` | `expAiSegmenterString` | `extension/expai/settings/expai.ini.append.php:1453` |
| `[DiffSettings] Segmenters[eztext]` | `expAiSegmenterText` | `extension/expai/settings/expai.ini.append.php:1454` |
| `[DiffSettings] Segmenters[ezxmltext]` | `expAiSegmenterRichText` | `extension/expai/settings/expai.ini.append.php:1455` |
| `[DiffSettings] Segmenters[ezrichtext]` | `expAiSegmenterRichText` | `extension/expai/settings/expai.ini.append.php:1456` |
| `[DiffSettings] Segmenters[ezkeyword]` | `expAiSegmenterKeyword` | `extension/expai/settings/expai.ini.append.php:1457` |
| `[DiffSettings] FallbackSegmenter` | `expAiSegmenterGeneric` | `extension/expai/settings/expai.ini.append.php:1458` |
| `[QueueSettings] Appliers[translation_sync]` | `expAiTranslationSyncApplier` | `extension/expai/settings/expai.ini.append.php:1515` |
| `[FieldAction_alt_text] Handler` | `expAiAltTextFieldAction` | `extension/expai/settings/expai.ini.append.php:1531` |
| `[FieldAction_preset_title] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1640` |
| `[FieldAction_preset_summary] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1648` |
| `[FieldAction_preset_meta_description] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1657` |
| `[FieldAction_preset_keywords] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1666` |
| `[FieldAction_improve_shorten] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1675` |
| `[FieldAction_improve_expand] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1681` |
| `[FieldAction_improve_clarify] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1687` |
| `[FieldAction_improve_grammar] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1693` |
| `[FieldAction_improve_simplify] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1699` |
| `[FieldAction_improve_formal] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1705` |
| `[FieldAction_improve_casual] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1711` |
| `[JobType_expai.request] Handler` | `expAiRequestHandler` | `extension/expai/settings/expqueue.ini.append.php:9` |
| `[JobType_expai.batch.submit] Handler` | `expAiBatchSubmitHandler` | `extension/expai/settings/expqueue.ini.append.php:19` |
| `[JobType_expai.batch.poll] Handler` | `expAiBatchPollHandler` | `extension/expai/settings/expqueue.ini.append.php:29` |
| `[JobType_expai.thread.turn] Handler` | `expAiThreadTurnHandler` | `extension/expai/settings/expqueue.ini.append.php:63` |
| `[JobType_expai.thread.item] Handler` | `expAiThreadItemHandler` | `extension/expai/settings/expqueue.ini.append.php:74` |
| `[JobType_expai.translation.check] Handler` | `expAiTranslationCheckHandler` | `extension/expai/settings/expqueue.ini.append.php:85` |
| `[JobType_expai.alttext] Handler` | `expAiAltTextJobHandler` | `extension/expai/settings/expqueue.ini.append.php:97` |
| `[JobType_expai.model.test] Handler` | `expAiModelTestHandler` | `extension/expai/settings/expqueue.ini.append.php:110` |
| `[JobType_expai.exchange.rates] Handler` | `expAiExchangeRatesHandler` | `extension/expai/settings/expqueue.ini.append.php:120` |
| `[ezjscServer_expai] Class` | `expAiServerFunctions` | `extension/expai/settings/ezjscore.ini.append.php:11` |
| `[ezjscServer_expaivector] Class` | `expAiVectorServerFunctions` | `extension/expai/settings/ezjscore.ini.append.php:17` |
| `[ezjscServer_expaitranslation] Class` | `expAiTranslationSyncServerFunctions` | `extension/expai/settings/ezjscore.ini.append.php:24` |
| `[ezjscServer_expaialttext] Class` | `expAiAltTextServerFunctions` | `extension/expai/settings/ezjscore.ini.append.php:31` |
| `[ezjscServer_expaipreset] Class` | `expAiPresetServerFunctions` | `extension/expai/settings/ezjscore.ini.append.php:37` |
| `[ezjscServer_expaimodel] Class` | `expAiModelServerFunctions` | `extension/expai/settings/ezjscore.ini.append.php:43` |
| `[ezjscServer_expairevert] Class` | `expAiDraftRevertServerFunctions` | `extension/expai/settings/ezjscore.ini.append.php:50` |
| `[ezjscServer_expaiwatch] Class` | `expAiRunWatch` | `extension/expai/settings/ezjscore.ini.append.php:56` |
| `[Registry_expai_provider_types] Implements` | `expAiProvider` | `extension/expai/settings/rad.ini.append.php:35` |
| `[Registry_expai_batch_apis] Implements` | `expAiBatchApi` | `extension/expai/settings/rad.ini.append.php:61` |
| `[Registry_expai_tools] Entries[Class]` | `expAiTool` | `extension/expai/settings/rad.ini.append.php:79` |
| `[Registry_expai_search_providers] Entries[Class]` | `expAiWebSearchProvider` | `extension/expai/settings/rad.ini.append.php:88` |
| `[Registry_expai_field_actions] Entries[Handler]` | `expAiFieldAction` | `extension/expai/settings/rad.ini.append.php:145` |
| `[Registry_expai_presets] Entries[Handler]` | `expAiFieldAction` | `extension/expai/settings/rad.ini.append.php:156` |
| `[Registry_expai_appliers] Entries[Appliers]` | `expAiRequestApplier` | `extension/expai/settings/rad.ini.append.php:165` |
| `[Registry_expai_segmenters] Entries[Segmenters]` | `expAiSegmenter` | `extension/expai/settings/rad.ini.append.php:174` |
| `[Registry_expai_segmenters] Entries[FallbackSegmenter]` | `expAiSegmenter` | `extension/expai/settings/rad.ini.append.php:175` |
