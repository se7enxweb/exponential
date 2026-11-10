# Spec Delta

## Purpose

Posts to channels are recorded before they are sent, delivered in the background with retries, never twice, and
only after a person approved the content.

## ADDED Requirements

### Requirement: Channel post action
A workflow definition SHALL be able to post the content of an instance to one or more destinations with a
channel post action, using a text rendered from the content. The action SHALL be outward.

#### Scenario: Post on publishing
- **WHEN** an instance of "News article" enters the stage "Published" whose on-enter posts to "Newsroom"
- **THEN** one post for that article is recorded for "Newsroom"

### Requirement: Recorded before sent
A post SHALL be recorded per destination as pending before any delivery is attempted. Recording SHALL NOT depend
on the provider being reachable, so publishing never fails or waits because of a channel.

#### Scenario: Provider down while publishing
- **WHEN** an article that posts to Mastodon is published while Mastodon does not answer
- **THEN** the article is published at once and the post stays pending

### Requirement: Delivery and retries
Pending posts SHALL be delivered by background workers. A failed delivery SHALL be retried with increasing
delay; after the last attempt the post SHALL be marked failed and stay visible in the destination's log.

#### Scenario: Provider back
- **WHEN** Mastodon answers again before the last attempt
- **THEN** the post is sent and marked sent with the provider's message id

#### Scenario: Provider gone
- **WHEN** every attempt fails
- **THEN** the post is marked failed and the log shows each attempt and its error

### Requirement: Never twice
A post SHALL be delivered at most once per instance, action and destination. A retry of an attempt whose answer
was lost SHALL NOT create a second message at the provider. A new version that runs through the definition again
is a new instance and SHALL post again only when the action allows posting for later versions; by default it
posts for the first published version only.

#### Scenario: Timeout after the provider accepted
- **WHEN** the provider accepted a post but the answer timed out and the post is retried
- **THEN** the channel shows the message once

#### Scenario: Typo fixed later
- **WHEN** an editor publishes a second version of an article that was already posted, with the default setting
- **THEN** no second post is made

### Requirement: A person's approval causes a post
A channel post action SHALL record posts only when the instance has had a transition fired by a person since the
content was last written by the AI service or a system user. A job or timer transition after such a person
transition SHALL carry the approval forward. When no such person transition exists, the action SHALL record no
post and the instance history SHALL say why.

#### Scenario: AI draft reaches the posting stage
- **WHEN** an AI draft job finishes and its done transition leads straight to a stage that posts
- **THEN** no post is recorded, and the history says that no person approved the content

#### Scenario: Approved, then live by date
- **WHEN** an editor fires "Approve" and a timer later fires "Go live" into the stage that posts
- **THEN** the post is recorded when the timer fires

#### Scenario: AI rewrite after approval
- **WHEN** the AI service rewrites the teaser after "Approve" and before the timer fires
- **THEN** no post is recorded until a person approves again

### Requirement: Post log
Each destination SHALL have a log of its posts with the content, the instance, the state, the attempts and, for
sent posts, a link to the message.

#### Scenario: Editor looks up a post
- **WHEN** an editor opens the log of "Newsroom"
- **THEN** each post shows its article, state and a link to the message in the room
