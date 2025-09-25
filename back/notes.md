# Test notes

After setting up the project by installing its dependencies I started by the first task in hand, as described on the `read me` fixing the tests.

## Bug-fix
>Bug-fix: A recent code change broke the test-suite. First goal: make all tests green again.

After running php artisan test, i decided to first fix the llme2etest as it looked important.

### LlmAnalysis.php
The first error i encountered was:
```bash
App\Events\LlmAnalysisDone::__construct(): Argument #2 ($result) must be of type string, array given, called in /home/potat/lynkx/Diabolocom/fullstack-test/fullstack-technical-test/back/vendor/laravel/framework/src/Illuminate/Foundation/Events/Dispatchable.php on line 14
```
I went to the LlmAnalysisDone file and i looked at the constructor, i saw that the second argument was a string, but in the LlmAnalysis.php file, the second argument was an array. So i changed the type of the second argument in the constructor to array. Ran back the tests and they were all working.


### Design
> Design: LlmAnalysis currently returns hard-coded fake data. Refactor so we can swap a real LLM client in production while still using a test double in automated tests.

