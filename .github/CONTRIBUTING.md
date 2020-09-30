# Contributing

Contributions are **welcome** and will be fully **credited**.

We accept contributions via Pull Requests on [Github](https://github.com/matt-allan/laravel-code-style).

## Scope

If you are just changing the ruleset to better match Laravel's code style go ahead and open a pull request.  If you want to add a new feature please open an issue first.

## Code Generation

The `MattAllan\LaravelCodeStyle\Config::RULE_DEFINITIONS` array is generated. It should not be edited by hand. If you need to add a rule or change the config for a rule, you should first add it to `MattAllan\LaravelCodeStyle\Dev\GenerateRules`.

StyleCI does not always use the same rule names as PHPCS. To define a mapping you can add an entry to `GenerateRules::STYLECI_TO_PHPCS_MAP`.

Once you've updated the rules, you can re-generate the config:

```
composer gen-rules
composer fix-style
```

## Pull Requests

- **Use the code style:** Check the code style with ``$ composer check-style`` and fix it with ``$ composer fix-style``.

- **Add tests!** Your patch won't be accepted if it needs tests and doesn't have them.

- **Document any change in behaviour** - Make sure the `README.md` and any other relevant documentation are kept up-to-date.

- **Consider our release cycle** - We try to follow [SemVer v2.0.0](http://semver.org/). Randomly breaking public APIs is not an option.

- **Create feature branches** - Don't ask us to pull from your master branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please [squash them](http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.


## Running Tests

``` bash
$ composer test
```


**Happy coding**!
