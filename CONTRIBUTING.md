# Contributing to E-Commerce MVP

Thank you for your interest in contributing to this project! This document provides guidelines for contributing.

## 🤝 How to Contribute

### Reporting Bugs

Before creating bug reports, please check existing issues. When creating a bug report, include:

- **Clear title and description**
- **Steps to reproduce** the problem
- **Expected behavior**
- **Actual behavior**
- **Screenshots** (if applicable)
- **Environment details** (OS, Node version, browser)

### Suggesting Enhancements

Enhancement suggestions are welcome! Please include:

- **Clear description** of the enhancement
- **Use case** - why is this needed?
- **Possible implementation** approach
- **Alternatives** you've considered

### Pull Requests

1. **Fork** the repository
2. **Create a branch** for your feature (`git checkout -b feature/AmazingFeature`)
3. **Make your changes**
4. **Test thoroughly**
5. **Commit** your changes (`git commit -m 'Add some AmazingFeature'`)
6. **Push** to your branch (`git push origin feature/AmazingFeature`)
7. **Open a Pull Request**

## 📝 Coding Standards

### JavaScript Style Guide

- Use **ES6+** features where appropriate
- Use **const** and **let** instead of var
- Use **arrow functions** for callbacks
- Use **async/await** instead of callbacks or .then()
- Follow **camelCase** for variables and functions
- Follow **PascalCase** for classes
- Use **meaningful variable names**

### Code Example

```javascript
// Good
const getUserOrders = async (userId) => {
  try {
    const orders = await Order.find({ user: userId });
    return orders;
  } catch (error) {
    throw error;
  }
};

// Avoid
var get_orders = function(id) {
  Order.find({ user: id }).then(function(o) {
    return o;
  });
};
```

### File Organization

```
Feature/
├── model.js         # Database model
├── controller.js    # Business logic
├── routes.js        # API routes
└── validation.js    # Input validation (if needed)
```

### Comments

- Add comments for **complex logic**
- Use **JSDoc** for functions
- Avoid **obvious comments**

```javascript
/**
 * Calculate order total including tax
 * @param {Array} items - Array of order items
 * @param {Number} taxRate - Tax rate (e.g., 0.1 for 10%)
 * @returns {Number} Total amount
 */
const calculateTotal = (items, taxRate) => {
  const subtotal = items.reduce((sum, item) => sum + item.price * item.quantity, 0);
  return subtotal * (1 + taxRate);
};
```

## 🧪 Testing Guidelines

### Before Submitting

- [ ] Code runs without errors
- [ ] All existing tests pass
- [ ] New features have tests (if applicable)
- [ ] Manual testing completed
- [ ] No console.log() or debugger statements
- [ ] Code is properly formatted

### Testing Checklist for New Features

**Backend:**
- [ ] API endpoint works correctly
- [ ] Error handling is implemented
- [ ] Authentication/authorization works
- [ ] Input validation works
- [ ] Database operations succeed

**Frontend:**
- [ ] UI displays correctly
- [ ] Forms validate input
- [ ] API calls succeed
- [ ] Error messages display properly
- [ ] Responsive design works

## 🔒 Security Guidelines

### Do NOT:
- Commit sensitive data (API keys, passwords)
- Commit .env files
- Store passwords in plain text
- Skip input validation
- Trust user input

### Do:
- Hash passwords
- Validate all inputs
- Use parameterized queries
- Implement rate limiting
- Use HTTPS in production
- Keep dependencies updated

## 📦 Commit Message Guidelines

### Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types

- **feat**: New feature
- **fix**: Bug fix
- **docs**: Documentation changes
- **style**: Code style changes (formatting)
- **refactor**: Code refactoring
- **test**: Adding or updating tests
- **chore**: Maintenance tasks

### Examples

```
feat(cart): add quantity validation

Added validation to prevent adding more items than available in stock.
Displays appropriate error message to user.

Closes #123
```

```
fix(auth): correct JWT token expiration

Changed JWT expiration from string to number format
to fix token validation issues.
```

## 🌿 Branch Naming

- `feature/feature-name` - New features
- `fix/bug-description` - Bug fixes
- `docs/doc-description` - Documentation
- `refactor/what-changed` - Refactoring

## 📋 Pull Request Template

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
Describe testing done

## Checklist
- [ ] Code follows style guidelines
- [ ] Self-review completed
- [ ] Comments added where needed
- [ ] Documentation updated
- [ ] No new warnings
- [ ] Tests added/updated
- [ ] Tests pass
```

## 🎯 Priority Areas for Contribution

### High Priority
- Bug fixes
- Security improvements
- Performance optimization
- Documentation improvements

### Medium Priority
- New features (aligned with MVP scope)
- UI/UX enhancements
- Test coverage

### Low Priority
- Code refactoring
- Minor optimizations

## 🚫 Out of Scope

To maintain the MVP focus, these are currently out of scope:
- Real payment gateway integration
- Microservices architecture
- Advanced analytics
- Social media integration
- Real-time chat support

## ⚖️ Code of Conduct

### Our Standards

- **Be respectful** and inclusive
- **Be patient** with others
- **Give constructive feedback**
- **Accept constructive criticism**
- **Focus on what's best** for the community

### Unacceptable Behavior

- Harassment or discrimination
- Trolling or insulting comments
- Public or private harassment
- Publishing others' private information
- Other unprofessional conduct

## 📞 Getting Help

If you need help:
1. Check existing documentation
2. Search existing issues
3. Ask in discussions
4. Create a new issue with the question tag

## 🏆 Recognition

Contributors will be:
- Listed in CONTRIBUTORS.md
- Mentioned in release notes
- Credited in documentation (for significant contributions)

## 📄 License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

Thank you for contributing! 🎉
