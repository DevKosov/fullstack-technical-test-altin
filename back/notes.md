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

To refactor the llm analysis so that i can have multiple environments i used a contract to create an interface for the llm client, then i created two implementations of this interface, one for the fake client and another for the openai client. Then i used the app service provider to bind the interface to the implementation based on the environment variable.

This way i can easily swap the implementation of the llm client based on config file i created for the environment. 

I also added .env.testing file to set the llm driver to fake for the testing environment.
I ran the `php artisan test` command and all tests were passing.

However i did not make a real openai client implementation as i do not have an api key, but i created the structure for it.

### Moderation layer
> Moderation layer: We need protection against prompt injection / malicious content. Propose & implement a clean integration point to call an external moderation service before the LLM.

I searched online on what i can do to prevent prompt injection and i found this [cheat sheet](https://cheatsheetseries.owasp.org/cheatsheets/LLM_Prompt_Injection_Prevention_Cheat_Sheet.html) from OWASP.

This allowed me to understand better what prompt injection is and how to prevent it.

I created a new contract for the moderation client and two implementations, one fake and another http client to call an external moderation service. I also created a config file for the moderation service to set the driver and the endpoint. just like the llm client, i used the app service provider to bind the interface to the implementation based on the environment variable.

I then integrated the moderation client in the llm analysis action, so that before calling the llm client, the prompt is sent to the moderation client for review. If the prompt is not safe, an error is returned and the llm client is not called.

I also added some basic rules in the fake moderation client to simulate prompt injection detection, such as checking for common prompt injection phrases, dangerous commands, and basic toxicity.

I added a test to test the moderation layer and ran the tests, all tests were passing.

### Error propagation
> Error propagation: At present, if llmService fails, the frontend never finds out. Ensure failures reach the client. Cover with a test if possible.

Added a try catch block in the llm analysis action to catch any exceptions thrown by the moderation or llm client. If an exception is caught, an error event is dispatched with the error message. I also added a test to test the error propagation and ran the tests, all tests were passing.