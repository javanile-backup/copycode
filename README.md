# Copycode

```json
{
    "single-task": 
    {           
        "name": "Single task",
        "description": "run a single copy",
        "from": "..",
        "to": "copy1",
        "exclude": ["copycoder.json", "nbproject", "README.md"]
    }, 
    
    "grouped-task": 
    [
        {           
            "name": "First task in group",
            "description": "copy project",
            "from": "..",
            "to": "copy2",
            "exclude": ["copycoder.json", "nbproject", "README.md"]
        },
        {           
            "name": "Second task in group",
            "description": "copy project",
            "from": "..",
            "to": "copy3",
            "exclude": ["copycoder.json", "nbproject", "README.md"]
        }   
    ] 
}
```

