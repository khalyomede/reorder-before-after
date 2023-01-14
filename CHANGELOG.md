# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Ability to specify how to match values together ([#15](https://github.com/khalyomede/reorder-before-after/issues/15)).
- Typehints on closures for `Listing::outOf()` `Listing::applyWith()` and `Listing::matchWith()` ([#16](https://github.com/khalyomede/reorder-before-after/issues/16)).
- `@throws` PHPDoc for methods that can throw ([#19](https://github.com/khalyomede/reorder-before-after/issues/19)).

### Breaking

- The "BadOutOfCallbackException" has been renamed to "InvalidOutOfCallbackException" to match the other similar exceptions prefixes ([#17](https://github.com/khalyomede/reorder-before-after/issues/17)).

## [0.2.0] - 2023-01-14

### Added

- Ability to create the listing from an array ([#3](https://github.com/khalyomede/reorder-before-after/issues/3)).
- Ability to get all items from a listing ([#2](https://github.com/khalyomede/reorder-before-after/issues/2)).
- Ability to set a callback to apply the order on your value ([#1](https://github.com/khalyomede/reorder-before-after/issues/10)).
- Support for PHP 8.1 ([#10](https://github.com/khalyomede/reorder-before-after/issues/10)).
- Reordering using the raw value instead of passing an Item ([#8](https://github.com/khalyomede/reorder-before-after/issues/8)).
- Ability to create a listing from values, and specifying how to retrieve the order ([#9](https://github.com/khalyomede/reorder-before-after/issues/9)).

## [0.1.0] - 2023-01-11

### Added

- Added first working version.
