# Auth Fusion - Complete Package Index

Welcome to Auth Fusion! This is your central hub for all documentation and resources.

## 🎯 What is Auth Fusion?

Auth Fusion is a unified authentication package for Laravel that allows you to seamlessly work with multiple authentication systems (Sanctum, JWT, Passport) through a single, consistent API.

## 📚 Documentation Index

### Getting Started
- **[README.md](README.md)** - Main documentation and overview
- **[GETTING_STARTED.md](GETTING_STARTED.md)** - 5-minute quick start guide
- **[Quick Reference](QUICK_REFERENCE.md)** - Cheat sheet for common tasks

### Detailed Guides
- **[INSTALLATION.md](INSTALLATION.md)** - Complete installation instructions
- **[USAGE_EXAMPLES.md](USAGE_EXAMPLES.md)** - Real-world code examples
- **[PACKAGE_OVERVIEW.md](PACKAGE_OVERVIEW.md)** - Architecture and design

### Reference
- **[CONTRIBUTING.md](CONTRIBUTING.md)** - How to contribute to the project
- **[CHANGELOG.md](CHANGELOG.md)** - Version history and changes
- **[LICENSE.md](LICENSE.md)** - MIT License
- **[PROJECT_SUMMARY.md](PROJECT_SUMMARY.md)** - Complete project overview

## 🚀 Quick Navigation

### For New Users
1. Start with [GETTING_STARTED.md](GETTING_STARTED.md)
2. Read [README.md](README.md) for features
3. Try [QUICK_REFERENCE.md](QUICK_REFERENCE.md) for examples

### For Developers
1. Study [PACKAGE_OVERVIEW.md](PACKAGE_OVERVIEW.md) for architecture
2. Browse [USAGE_EXAMPLES.md](USAGE_EXAMPLES.md) for patterns
3. Check [INSTALLATION.md](INSTALLATION.md) for setup

### For Contributors
1. Read [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines
2. Review [CHANGELOG.md](CHANGELOG.md) for changes
3. Check [PROJECT_SUMMARY.md](PROJECT_SUMMARY.md) for overview

## 📁 Package Structure

```
auth-fusion/
├── README.md                    # Main documentation
├── GETTING_STARTED.md          # Quick start guide
├── INSTALLATION.md             # Installation guide
├── USAGE_EXAMPLES.md           # Code examples
├── QUICK_REFERENCE.md          # Quick reference
├── PACKAGE_OVERVIEW.md         # Architecture
├── CONTRIBUTING.md             # Contribution guide
├── CHANGELOG.md                # Version history
├── LICENSE.md                  # MIT License
├── PROJECT_SUMMARY.md          # Project summary
├── INDEX.md                    # This file
│
├── composer.json               # Package manifest
├── phpunit.xml                 # Test configuration
├── .gitignore                  # Git ignore rules
│
├── config/
│   └── auth-fusion.php        # Configuration file
│
└── src/
    ├── AuthFusionServiceProvider.php
    ├── Contracts/
    │   └── AuthDriverInterface.php
    ├── Drivers/
    │   ├── SanctumDriver.php
    │   ├── JWTDriver.php
    │   └── PassportDriver.php
    ├── Facades/
    │   └── AuthFusion.php
    ├── Manager/
    │   └── AuthDriverManager.php
    └── Support/
        ├── AuthFusionHelper.php
        └── TokenData.php
```

## 🎓 Learning Path

### Beginner
1. [GETTING_STARTED.md](GETTING_STARTED.md) - Get up and running
2. [README.md](README.md) - Understand basics
3. [QUICK_REFERENCE.md](QUICK_REFERENCE.md) - Common tasks

### Intermediate
1. [USAGE_EXAMPLES.md](USAGE_EXAMPLES.md) - Advanced patterns
2. [PACKAGE_OVERVIEW.md](PACKAGE_OVERVIEW.md) - Architecture
3. [CONTRIBUTING.md](CONTRIBUTING.md) - Contribute code

### Advanced
1. Source code in `src/` directory
2. Tests in `tests/` directory
3. Custom driver implementation

## 🔧 Common Tasks

### Installation
- [INSTALLATION.md](INSTALLATION.md) - Full installation steps

### Basic Usage
- [QUICK_REFERENCE.md](QUICK_REFERENCE.md) - Quick lookup

### Examples
- [USAGE_EXAMPLES.md](USAGE_EXAMPLES.md) - Complete examples

### Troubleshooting
- [INSTALLATION.md](INSTALLATION.md) - Troubleshooting section
- [README.md](README.md) - Error handling examples

## 📞 Support

Need help? Check these resources:

1. **Documentation** - Start here for answers
2. **GitHub Issues** - Report bugs
3. **GitHub Discussions** - Ask questions
4. **Examples** - Find similar use cases

## 🎉 Features at a Glance

✅ **Unified API** - One interface for all drivers
✅ **Flexible** - Switch drivers on the fly
✅ **Extensible** - Create custom drivers
✅ **Compatible** - Laravel 10-12, PHP 8.0+
✅ **Well Documented** - Comprehensive docs
✅ **Tested** - Test suite included
✅ **Production Ready** - Battle-tested code

## 📊 Package Stats

- **Files**: 30+
- **Lines of Code**: ~1500+
- **Documentation Pages**: 10
- **Drivers**: 3 (Sanctum, JWT, Passport)
- **Test Coverage**: Basic suite included

## 🔗 External Resources

- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [tymon/jwt-auth](https://github.com/tymondesigns/jwt-auth)
- [Laravel Passport](https://laravel.com/docs/passport)
- [Laravel Documentation](https://laravel.com/docs)

## 📝 License

MIT License - See [LICENSE.md](LICENSE.md)

---

**Built with ❤️ for the Laravel community**

*Auth Fusion - Unifying Authentication Systems*

