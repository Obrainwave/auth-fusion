# Contributing to Auth Fusion

Thank you for considering contributing to Auth Fusion! This document provides guidelines and information for contributors.

## Code of Conduct

By participating in this project, you agree to:
- Be respectful and inclusive
- Welcome newcomers
- Be patient with questions
- Focus on constructive feedback

## How Can I Contribute?

### Reporting Bugs

Before reporting bugs:
1. Check existing issues to avoid duplicates
2. Make sure it's a bug, not a feature request

When reporting bugs, include:
- Laravel version
- PHP version
- Package version
- Clear description of the issue
- Steps to reproduce
- Expected vs actual behavior
- Code examples if possible

### Suggesting Features

Feature suggestions are welcome! Please:
1. Check if the feature already exists
2. Explain the use case
3. Describe the expected behavior
4. Consider backward compatibility

### Pull Requests

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Write or update tests
5. Ensure tests pass
6. Update documentation if needed
7. Commit with clear messages (`git commit -m 'Add amazing feature'`)
8. Push to your branch (`git push origin feature/amazing-feature`)
9. Open a Pull Request

### Development Setup

```bash
# Clone the repository
git clone https://github.com/yourusername/auth-fusion.git
cd auth-fusion

# Install dependencies
composer install

# Run tests
composer test

# Run linting
composer lint
```

### Coding Standards

- Follow PSR-12 coding style
- Use descriptive variable and function names
- Add docblocks to public methods
- Keep functions small and focused
- Write meaningful commit messages

### Testing

- Write tests for new features
- Ensure all tests pass
- Aim for high coverage
- Test edge cases

### Documentation

- Update README for user-facing changes
- Add examples for new features
- Update CHANGELOG.md
- Keep inline documentation current

## Development Workflow

1. Create an issue or comment on an existing one
2. Get assigned to the issue
3. Create a feature branch
4. Implement changes
5. Write tests
6. Update documentation
7. Create PR
8. Address review feedback
9. Merge after approval

## Questions?

Feel free to open an issue for questions or discussions.

Thank you for contributing! 🎉

