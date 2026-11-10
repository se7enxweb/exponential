# Spec Delta

## Purpose

Administrators configure where content can be posted, check that it works, and switch a destination off.

## ADDED Requirements

### Requirement: Destinations
An administrator with the right to manage channels SHALL be able to create, edit and delete destinations. A
destination SHALL have a name, a provider and the provider's target (an account, a room). Its credentials
SHALL be referenced by name and SHALL NOT be stored in the database or shown in the administration.

#### Scenario: Matrix room added
- **WHEN** an administrator adds the destination "Newsroom" for a Matrix room with the credential name "matrix_bot"
- **THEN** the destination is listed with its provider and room, and the page shows the credential name but not its value

#### Scenario: Missing credential
- **WHEN** the named credential does not exist in the site's secrets
- **THEN** the destination is shown as not ready and cannot be switched on

### Requirement: Test post
An administrator SHALL be able to send a test post to a destination and see the provider's answer.

#### Scenario: Test succeeds
- **WHEN** an administrator sends a test post to "Newsroom"
- **THEN** the post appears in the room and the page shows success with the provider's message id

### Requirement: Switching off
A destination SHALL be switchable off and on. While it is off, no post SHALL be delivered to it, and posts that
were pending for it SHALL be cancelled.

#### Scenario: Switched off with pending posts
- **WHEN** a destination with three pending posts is switched off
- **THEN** the three posts are cancelled and none is delivered later
