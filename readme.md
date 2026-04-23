# KPT Utils

[![PHP](https://img.shields.io/badge/Up%20To-php8.4-777BB4?logo=php&logoColor=white&style=for-the-badge&labelColor=000)](https://php.net)
[![GitHub Issues](https://img.shields.io/github/issues/kpirnie/kp-php-utils?style=for-the-badge&logo=github&color=006400&logoColor=white&labelColor=000)](https://github.com/kpirnie/kp-php-utils/issues)
[![Last Commit](https://img.shields.io/github/last-commit/kpirnie/kp-php-utils?style=for-the-badge&labelColor=000)](https://github.com/kpirnie/kp-php-utils/commits/main)
[![License: MIT](https://img.shields.io/badge/License-MIT-orange.svg?style=for-the-badge&logo=opensourceinitiative&logoColor=white&labelColor=000)](LICENSE)
[![Kevin Pirnie](https://img.shields.io/badge/-KevinPirnie.com-000d2d?style=for-the-badge&labelColor=000&logoColor=white&logo=data:image/svg%2Bxml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSJ3aGl0ZSIgc3Ryb2tlLXdpZHRoPSIxLjgiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCI+CiAgPGNpcmNsZSBjeD0iMTIiIGN5PSIxMiIgcj0iMTAiLz4KICA8ZWxsaXBzZSBjeD0iMTIiIGN5PSIxMiIgcng9IjQuNSIgcnk9IjEwIi8+CiAgPGxpbmUgeDE9IjIiIHkxPSIxMiIgeDI9IjIyIiB5Mj0iMTIiLz4KICA8bGluZSB4MT0iNC41IiB5MT0iNi41IiB4Mj0iMTkuNSIgeTI9IjYuNSIvPgogIDxsaW5lIHgxPSI0LjUiIHkxPSIxNy41IiB4Mj0iMTkuNSIgeTI9IjE3LjUiLz4KPC9zdmc+Cg==)](https://kevinpirnie.com/)

A modern PHP 8.2+ utility library.

## Requirements

- PHP >= 8.2
- ext-openssl
- ext-sodium
- ext-curl

## Installation

```bash
composer require kevinpirnie/kpt-utils
```

## Classes

### `KPT\Sanitize`

Transforms and cleans input values. Covers scalars, HTML, email, URL, IP, domain, phone, MAC address, UUID, hex color, base64, slug, username, filename, path, JSON, XML, SVG, date, and more. Includes superglobal helpers, encoding utilities, and aggregate map/array sanitization.

---

### `KPT\Validate`

Returns `bool` for input validation. Mirrors `Sanitize` coverage and adds string length, pattern matching, password strength, numeric range, date/time comparison, file system checks, array inspection, ISBN, credit card (Luhn), color formats, coordinates, postal codes, and conditional validation.

---

### `KPT\Crypto`

Authenticated encryption and cryptographic utilities. Provides AES-256-GCM encryption with HKDF key derivation for machine-generated keys, Argon2id-based passphrase encryption for human-provided passwords, HMAC hashing, timing-safe comparison, and cryptographically secure key, token, and password generation.

---

### `KPT\Str`

String inspection and search utilities. Provides multi-needle substring search, multi-pattern regex search, whole-word matching with punctuation-aware boundaries, empty/blank detection, and PHP 8.4 `array_any()` fallback compatibility for 8.2/8.3 environments.

---

### `KPT\Arr`

Array utilities. Provides case-insensitive multi-needle search, key subset matching with PHP 8.4 `array_find_key()` fallback compatibility, multi-dimensional sorting by subkey with numeric and string comparison, and recursive object-to-array conversion.

---

### `KPT\Num`

Number formatting utilities. Provides ordinal formatting (1st, 2nd, 3rd — with correct 11th/12th/13th handling) and human-readable byte formatting from bytes through petabytes.

---

### `KPT\DateTime`

Date and time utilities. Provides WordPress-compatible time constants (`MINUTE_IN_SECONDS` through `YEAR_IN_SECONDS`) and a human-readable time-ago formatter with singular/plural labels from seconds through months, falling back to a formatted date string beyond one year.

---

### `KPT\Http`

HTTP request inspection and network utilities. Provides safe header-aware redirects with JavaScript fallback, client IP detection with proxy header support, user agent and referer retrieval, and IPv4/IPv6 CIDR range matching.

---

### `KPT\Session`

Full-featured session management. Provides lifecycle control (start, close, destroy, regenerate), dot-notation data access (get, set, has, remove, clear, all), session ID management, and flash messaging for one-time values across redirects.

---

### `KPT\Curl`

HTTP client modelled after WordPress's HTTP API. All methods return a consistent response array consumable via `retrieveBody()`, `retrieveHeaders()`, `retrieveHeader()`, `retrieveResponseCode()`, `retrieveResponseMessage()`, `retrieveCookies()`, `isError()`, and `getError()`. Supports GET, POST, PUT, PATCH, DELETE, and HEAD, basic/digest/bearer authentication, SSL verification, cookie handling, and concurrent multi-request execution via `multiGet()`, `multiPost()`, and `multiRequest()` with an optional concurrency limit.

---

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Author

**Kevin Pirnie** - [iam@kevinpirnie.com](mailto:iam@kevinpirnie.com)

## Support

- [Issues](https://github.com/kpirnie/kp-php-utils/issues)
- [PayPal](https://www.paypal.biz/kevinpirnie)
- [Ko-fi](https://ko-fi.com/kevinpirnie)