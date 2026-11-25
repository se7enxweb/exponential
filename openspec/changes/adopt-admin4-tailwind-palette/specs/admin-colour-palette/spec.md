## ADDED Requirements

### Requirement: The administration's colours come from one palette

Every colour the administration's own stylesheets define SHALL be a colour of Tailwind CSS 3.4's default palette,
white, black, or the logo's orange. A translucent colour SHALL take the red, green and blue of one of these. This
holds for admin4 in its light and its dark mode and for the classic admin's copy of the shared tokens.

#### Scenario: A stylesheet defines a colour outside the palette

- **WHEN** a colour that is not in the palette, not white, not black and not the logo's orange is added to one of
  these stylesheets
- **THEN** the design test suite fails and names the file and the colour

#### Scenario: A translucent tint

- **WHEN** a token is a translucent colour (a tinted background, a shadow, the overlay)
- **THEN** its red, green and blue are those of a palette colour, white, black or the logo's orange, and only the
  opacity is its own

#### Scenario: A comment names a colour

- **WHEN** a comment in a stylesheet names a colour value
- **THEN** the check ignores it

### Requirement: The greys are one ramp

The administration's greys SHALL all come from the `slate` ramp of the palette, in both modes.

#### Scenario: A grey of another ramp

- **WHEN** a colour of the `gray`, `zinc`, `neutral` or `stone` ramp is added to one of these stylesheets
- **THEN** the design test suite fails and names the file, the colour and its ramp

### Requirement: The logo's orange is the accent

The accent colour and the brand colour of the administration SHALL be the orange of the logo artwork, in both modes.
Text in the accent colour and filled controls with white text SHALL use darker shades from the palette's orange ramp.

#### Scenario: The logo and the accent agree

- **WHEN** the administration is shown in the light or in the dark mode
- **THEN** the logo's orange square, the brand colour and the accent colour are the same colour

#### Scenario: White text on an accent fill

- **WHEN** a control is filled with the accent and carries white text
- **THEN** its fill is a darker orange of the palette that gives the text at least 4.5:1, also when hovered

### Requirement: The palette keeps the contrast

Moving the colours to the palette SHALL keep every contrast the administration guarantees: text 4.5:1 on every
surface and state tint, the focus ring and field edges 3:1, chart colours 3:1 on a card, the environment badge's text
4.5:1 on its fill, in the light and the dark mode.

#### Scenario: A palette step falls short

- **WHEN** the nearest palette step of a colour does not reach the contrast its pairs need
- **THEN** the next darker or lighter step of the same ramp is taken, not a value between two steps

#### Scenario: A surface between two steps in dark mode

- **WHEN** a dark surface needs a shade between two steps of the ramp (the soft surface, the even table rows)
- **THEN** it is a palette colour laid translucently over the card, and text on it keeps its contrast
