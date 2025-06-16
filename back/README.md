# LLM Analysis Technical Test (Backend Part) – (Laravel + Reverb WebSockets)

Welcome — and thanks for taking the time to show us how you build software!
This repository contains a Laravel 11 project wired for Reverb WebSockets and a Vue client. Our tests emulate calls to a Large-Language-Model (LLM) in an asynchronous workflow. All code and an initial test-suite are already in place.

Your mission is to improve and secure the backend by tackling the tasks below. Each task is designed to surface your problem-solving skills, code-design choices, and attention to quality.

## Setup

### Dependencies
- PHP (Setup has been done with v8.2.28)
- Node.js (Setup has been done with v.18.19)
- npm (Setup has been done with v10.2.3)
- Composer (Setup has been done with v2.7.2)
- Laravel 11
- SQLite (Setup has been done with v3.43.2)
- Redis (Setup has been done with v7.2.4)

## IDE
You can use any IDE you like, we're used to work with Phpstorm.

Fork the repo and then install it as you would install a classic laravel app.

Then run these commands to make server work, it will be useful for frontend integration:
```
php artisan serve
php artisan reverb:start
php artisan queue:work
```

## Project Architecture

```bash
app/
├── Actions/
│   └── LlmAnalysis.php               # orchestrates analysis request
├── Events/
│   └── LlmAnalysisDone.php           # broadcast when analysis completes
├── Models/
│   └── User.php                      # basic user model
tests/
├── Feature/                          # all tests are there
```

Reverb handles the websocket channel analysis so frontend clients receive the LlmAnalysisDone event when processing finishes.

## The exercise

1. **Bug-fix**: A recent code change broke the test-suite. First goal: make all tests green again.

2. **Design**: LlmAnalysis currently returns hard-coded fake data. Refactor so we can swap a real LLM client in production while still using a test double in automated tests.

3. **Moderation layer**: We need protection against prompt injection / malicious content. Propose & implement a clean integration point to call an external moderation service before the LLM.

4. **Error propagation**: At present, if llmService fails, the frontend never finds out. Ensure failures reach the client. Cover with a test if possible.

5. **Channel security**: The LlmAnalysisDone event is published on a trivially guessable private channel. Harden the broadcast auth so only the owner of a given analysis can subscribe.

6. **Payload validation**: Strengthen request validation (Spatie Laravel Data, native Validator, or another lib). Explain your choice and implement tighter rules.


## Running tests

```bash
php artisan test. # If it's red at beginning, that is normal, first task is to make them green again.
```

## Useful links

- [Laravel 11 Broadcasting](https://laravel.com/docs/11.x/broadcasting)
- [Laravel Actions](https://www.laravelactions.com/)

