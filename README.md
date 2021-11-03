# Rosebot
My personal chat bot
## Query
- <b> JSON Query</b>

```bash
{ 
  "query": {
     "sender": "sumith",
     "message": "hi"
  }
}
```
- <b>Query Command</b>

```bash
curl -s -X POST http://localhost:8080 \
-d '{"query":{"sender": "sumith","message": "hi"}}'
```

## Response
    apt install jq
```bash
~$ curl -s -X POST http://localhost:8080 -d '{"query":{"sender": "sumith","message": "call sumith"}}' | jq ##apt install jq
{
  "replies": [
    {
      "message": "*Rose* \r\n _Calling sumith...._"
    },
    {
      "message": "*Rose* \r\n_wait a minute...._"
    },
    {
      "message": "*Rose* \r\n_If there is no response within 1 minute sumith may be busy_"
    }
  ]
}
```

```bash
~$ curl -s -X POST http://localhost:8080 -d '{"query":{"sender": "sumith","message": "tell me a joke"}}' | jq
{
  "replies": [
    {
      "message": "*Rose*\r\n_A bus station is where a bus stops. A train station is where a train stops. On my desk, I have a work station. ._"
    }
  ]
}
```

- <b>message from new number</b>

```bash
~$ curl -s -X POST http://localhost:8080 -d '{"query":{"sender": "+91 12345 67890","message": "hi"}}' | jq
{
  "replies": [                                                            
    {
      "message": "*Rose*\r\n_I haven't seen your contact in sumith's contact list_"
    },
    {                                                                       
      "message": "*Rose*\r\n_who are you ?_"
    }
  ]
}
