# Spec Delta

## ADDED Requirements

### Requirement: Notify on every review step
The notify action SHALL accept the events it reacts to: entering the stage, a comment, a vote and a transition, so
that assignees and the author are informed of each step of a review, not only when it starts.

#### Scenario: Vote notification
- **WHEN** a review stage notifies the author on votes and a reviewer votes
- **THEN** the author receives a notification naming the reviewer and the vote
