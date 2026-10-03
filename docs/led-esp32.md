# Conventions LED / ESP32

Référence détaillée. Vue d'ensemble dans [CLAUDE.md](../CLAUDE.md).

Les effets LED ont toujours **3 couleurs** dans le payload, même si l'effet n'en utilise qu'une ou deux. Les couleurs inutilisées sont des strings vides `""`. C'est volontaire — l'ESP32 attend toujours un tableau de 3.

Structure d'un scénario en base / MQTT :
```json
{
  "line_0": {
    "steps": [{
      "duration": 5,
      "cursors": [0, 15, 30, 59],
      "segments": [{
        "first": 0, "last": 14,
        "effect": 44,
        "colors": ["#ff0000", "", ""],
        "speed": 1000,
        "reverse": false
      }]
    }]
  }
}
```
