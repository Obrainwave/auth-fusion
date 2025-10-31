# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-01-XX

### Added
- Initial release of Auth Fusion
- Support for Laravel 10, 11, and 12
- Support for PHP 8.0+
- Sanctum driver implementation
- JWT driver implementation (tymon/jwt-auth)
- Passport driver implementation
- Unified `AuthDriverInterface` for consistent API
- `AuthDriverManager` for managing multiple drivers
- Service provider with auto-discovery
- Configuration file publishing
- Facade for easy access
- Helper class for common operations
- Comprehensive documentation
- Usage examples for all scenarios
- MIT license
- Custom driver extensibility

### Features
- Dynamic driver switching
- Runtime driver selection
- Token validation across all drivers
- User retrieval from tokens
- Logout functionality
- Refresh token support (driver-dependent)
- Extensible driver system
- Clean, consistent API

### Technical Details
- Uses Laravel's service container
- PSR-4 autoloading
- Composer autoload support
- Laravel package discovery
- Environment-based configuration

