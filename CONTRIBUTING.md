# Exponential CMS Contributors Guidelines

Thank you for considering contributing to Exponential CMS! We appreciate the time and effort you invest in making this project better.

## Licensing of code improvements to Exponential
Exponential is licensed under the GPLv2 or later license, commonly known as the GNU General Public License, version 2 or any later version, grants users the freedom to modify and distribute software. When it comes to code improvements, contributors must ensure that any modifications made to the project are also released under the same GPLv2 or later license. This means that enhancements, bug fixes, or any changes to the codebase must be open-sourced, allowing others to benefit from and build upon these improvements. The "or later" clause allows the project to upgrade to future versions of the GPL, providing flexibility and ensuring alignment with the principles of free software. It's essential for contributors to understand and comply with these licensing requirements to maintain the open nature of the project.

## Code of Conduct

Please make sure to review our [Code of Conduct](CODE_OF_CONDUCT.md) before contributing. All contributors are expected to adhere to it.

## Getting Started

1. Fork the repository and clone your fork to your local machine.

```bash
git clone https://github.com/your-username/expontential.git
```

2. Install the project dependencies.

```bash
composer install
```

3. Set up the development environment.

4. Create a feature branch for your contribution.

```bash
git checkout -b feature/your-feature
```

## Making Changes

1. Always create a new branch for your work to keep the main branch clean.

2. Follow the [PSR standards](https://www.php-fig.org/psr/) and our [coding style](CODING_STYLE.md).

3. Write clear, concise commit messages whose subject starts with what the change does: `Added: `, `Updated: `,
   `Fixed: `, `Removed: ` or `Renamed: `, e.g. `Fixed: The redirect keeps the query string on the own host`. The
   commit-msg hook of `.githooks/` (`make hooks`) and CI check it; see
   [the quality checks](doc/features/6.0/quality-checks.md).

4. Keep your pull request focused. If you have multiple contributions, submit multiple pull requests.

5. Templates and stylesheets of the administration designs (and of extensions' admin designs) follow
   [Styling the administration](doc/bc/6.0/admin4-styling.md): no `<style>` block or `style=""` attribute, the
   shared `.a4-*` components and `--a4-*` tokens with dark values, module CSS in `stylesheets/modules/`. Its code
   review checklist applies to every such pull request; `tests/tests/design/AdminStylingRulesTest.php` checks it.

## Submitting Changes

1. Ensure all tests pass.

2. Update the documentation if necessary.

3. Push your changes to your fork.

```bash
git push origin feature/your-feature
```

4. Submit a pull request to the main repository.

## Code Reviews

All contributions will be reviewed before merging. Be open to feedback and be willing to make changes if requested. Code reviews are essential to maintain code quality.

## Issues and Discussions

Feel free to open an issue for bug reports, feature requests, or general discussions. Follow the provided templates to provide necessary details.

## Licensing

By contributing, you agree that your contributions will be licensed under the [GNU General Public License, version 2.0 or later](LICENSE). Make sure that your contributions comply with this license.

Thank you for contributing to Exponential CMS!

---
