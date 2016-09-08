# Copycode

Copycode able you to copy part of your code project to another place, usefull to update local copy or multiple projects at one time or share file and automatize it delivery (only into local machine).

## How install

Install me via composer

```bash
composer global require javanile/copycode
```

## copycode.json struct

```json
{
    "single-task": 
    {           
        "name"        : "Single task",
        "description" : "run a single copy",
        "from"        : "..",
        "to"          : "copy1",
        "exclude"     : ["copycoder.json", "nbproject", "README.md"]
    }, 
    
    "grouped-task": 
    [
        {           
            "name"        : "First task in group",
            "description" : "copy project",
            "from"        : "..",
            "to"          : "copy2",
            "exclude"     : ["copycoder.json", "nbproject", "README.md"]
        },
        {           
            "name"        : "Second task in group",
            "description" : "copy project",
            "from"        : "..",
            "to"          : "copy3",
            "exclude"     : ["copycoder.json", "nbproject", "README.md"]
        }   
    ] 
}
```

