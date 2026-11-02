# Spec Delta

## Purpose

What each built-in action does when a definition runs it.

## ADDED Requirements

### Requirement: Notify
The notify action SHALL notify the named recipients (author, owner, assignees, users or groups) through the
site's notification system, as the kernel's approval does; reminders SHALL use the same path.

#### Scenario: Author informed
- **WHEN** "approve" fires with a notify action to the author
- **THEN** the author receives a notification naming the content and the new stage

### Requirement: Hide and unhide
The hide action SHALL hide the content object's locations and keep the object; unhide SHALL make them visible
again.

#### Scenario: Expired content kept
- **WHEN** an instance enters "Expired" with the hide action
- **THEN** the article is hidden from the site and still exists in the administration

### Requirement: Webhook
The webhook action SHALL send the event to the subscribed webhooks, or to one named webhook, through the queue.

#### Scenario: Named hook
- **WHEN** a transition runs the webhook action with the hook "newsroom"
- **THEN** one delivery to "newsroom" is queued

### Requirement: AI draft
The AI draft action SHALL request a draft from the site's AI service as a job, using the configured prompt
source, and its result SHALL drive the stage's done and failed transitions.

#### Scenario: Draft from the intro
- **WHEN** an instance enters "AI draft" with the prompt taken from the intro attribute
- **THEN** a draft job is queued with the intro as prompt

### Requirement: Translation check
The translation check action SHALL start the AI translation of the approved source into the target languages
configured for the content, as a job.

#### Scenario: Approved source
- **WHEN** "approve" fires on a German source with English and French as targets
- **THEN** translation jobs for English and French are queued
