# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased](https://github.com/bbatsche/ConsoleColor/compare/1.0.1...develop)

## 1.0.1 - 2022-11-14

### Added

- `ApplierInterface` to make mocking the `Style` class possible - [#1][PR1]

### Deprecated

- Public access to `supportsStyles`, `supports256Colors`, and `supportsRGBColors` properties; use the method defined in `ApplierInterface` instead. - [#1][PR1]

## 1.0.0 - 2022-11-11

Initial release

[PR1]: https://github.com/bbatsche/ConsoleColor/pull/1 "Style Applier Interface"
