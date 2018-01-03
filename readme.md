# Functional Options design pattern in PHP

This is an exploration of the Functional Options pattern which is popular in the 
Go world. Whilst there are some limitations in the approach that means it is not
identical, the goal is to demonstrate that whilst limited in its implementation
(especially in regards to type safety), there is still some benefits to this
pattern.

## Overview

Back in 2014, [Dave Cheney](https://dave.cheney.net/about) wrote an article 
about the emerging Functional Options pattern as used by Go developers 
[Functional options for friendly APIs](https://dave.cheney.net/2014/10/17/functional-options-for-friendly-apis).
Using this pattern we can make an API that has
- sensible defaults
- is highly configurable
- can grow over time
- self documenting
- safe for newcomers
- and never requires nil or an empty value to keep the compiler happy

Even though PHP lacks function types, which this pattern obstensively uses to 
ensure type safety, we can still gain the majority of the benefit of this pattern 
in PHP.

## The Pattern

Essentially, the pattern removes the need for optional parameters in constructors
of factories by defering to a varadic list of callable functions. Each function 
itself returns a function, which accepts the target object as an argument, and
using a closure, configures the target object.

By leaning on varadic functions to replace the optional arguments, this pattern
makes code easier to maintain because the function signature of the constructor
doesnt need to change as additional parameters are introduced.

In addition, the code becomes more readable as function names can be descriptive,
traditionally using the 'With' or 'From' prefix to describe the initialisation 
action they perform.

For example:

```php
$person = new Employee(
    WithFirstName('Ben'),
    WithSurname('Davies'),
    WithEmailAddress('ben.davies@example.com')
);
```

This pattern also makes it easy to initialise an object from an existing data 
structure, such as an array:

```php
$person = new Employee(
    FromArray(['first_name' => 'Ben', 'surname' => 'Davies'])
);
```

The separation of initialisation into functions means that third-parties can 
trivially add initialising functions without issue:

```php
function FromJSON(string $rawJSON):callable {
    return FromArray(json_decode($rawJSON, true));
}

$person = new Employee(
    FromJSON(file_get_contents('ben.davies.json'))
);
```

## Criticisms

The original approach in Go relies on a Function Type to ensure type safety of the
retuned function. The closest we can do is to rely on the return type hinting 
properties of PHP7 to ensure the returned function is callable. However this 
does not ensure that returned function accepts the target object as an argument.

Ideally, this problem could be solved with the introduction of Callable Prototypes
[PHP RFC](https://why-cant-we-have-nice-things.mwl.be/requests/callable-types).
However it appears that this particular RFC has been rejected.

It could be possibible to implement type checking of the first argument of the
returned callable in the constructor using refelction, like so:

```php
public function __construct(callable ...$options)
{
    if (count($options)>0) {
        foreach($options as $opt) {
            if (is_callable($opt)) {
                $rf = new \ReflectionFunction($opt);
                $rp = $rf->getParameters();
                $rc = $rp[0]->getClass();

                if ($this instanceof $rc->name) {
                    $opt($this);
                }
            }
        }
    }
}
```
