# Spec Delta

## ADDED Requirements

### Requirement: Quorum transitions fire after enough votes
A person transition with a quorum SHALL fire only when the required number of distinct users allowed to fire it
have chosen it; until then each choice SHALL be recorded as a vote, not as a transition.

#### Scenario: Second vote fires
- **WHEN** "approve" needs two votes and the second distinct reviewer chooses it
- **THEN** the transition fires and the history names both voters
