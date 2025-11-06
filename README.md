<p align="center"><a href="https://www.smartcitiesecosystem.gr/" target="_blank"><img src="public/images/logo.png" width="256" height="113" alt="Smart Cities Logo"></a></p>

## API Endpoints

### Routes
- `GET /api/routes` - List all routes
- `GET /api/routes/{id}` - Get specific route details by id
- `GET /api/routes/{id}/points` - Get points belonging to a route
- `GET /api/routes/{id}/tags` - Get tags associated with a route
- `GET /api/routes/{id}/geojson` —Get route points as GeoJSON FeatureCollection
- `GET /api/routes/nearby?lng={lng}&lat={lat}&radius={meters}` —Get routes within radius (meters) based on Latitude, Longitude

### Points
- `GET /api/points` - List all points
- `GET /api/points/{id}` - Get specific point details by id
- `GET /api/points/geojson` - Get all points as GeoJSON feature collection
- `GET /api/points/{id}/geojson` - Get specific point as GeoJSON feature
- `GET /api/points/nearby?lng={lng}&lat={lat}&radius={meters}` —Get points within radius (meters) based on Latitude, Longitude
- `GET /api/points/{id}/bibliographies` —GET bibliographies of a point

### Tags
- `GET /api/tags/` — List all tags
- `GET /api/tags/{id}` — Get specific tag details by id
- `GET /api/tags/{id}/routes` —Get routes that have this tag
- `GET /api/tags/nearby?lng={lng}&lat={lat}&radius={meters}` —Get tags within radius (meters) based on Latitude, Longitude

### Nearby parameters

All `nearby` endpoints accept:
- `lng` — longitude (decimal)
- `lat` — latitude (decimal)
- `radius` — search radius in **meters** which is an **optional**
- default value **1000**

**Examples**
- Points: `/api/points/nearby?lng=25.8691&lat=35.0034&radius=1000`
- Routes: `/api/routes/nearby?lng=25.8691&lat=35.0034&radius=1000`
- Tags: `/api/tags/nearby?lng=25.8691&lat=35.0034&radius=1000`

## Schemas
    
<details>
    <summary><strong>Routes</strong> (click to expand)</summary>

### Route (as returned by `GET /api/routes/{id}`)
```json
[
  {
    "id": "string-uuid",
    "url": "string-url",
    "name": "string",
    "description": "string (HTML)",
    "points": [
      {
        "id": "string-uuid",
        "url": "string-url",
        "name": "string",
        "description": "string (HTML)"
      },
      {}
    ],
    "tags": [
      {
        "id": "string-uuid",
        "name": "string",
        "url": "string-url"
      },
      {}
    ]
  }
]
```
### Routes List (returned by `GET /api/routes/`)
> Returns an array of `Route` objects; 

### Route → Points (as returned by GET /api/routes/{id}/points)
```json
[
    {
        "id": "string-uuid",
        "url": "string-url",
        "route_id": "string-uuid",
        "name": "string",
        "description": "string (HTML)",
        "map_number": 0,
        "lat": 0.0,
        "lng": 0.0
    },  
     {} 
]
```

### Route → GeoJSON of Points (as returned by GET /api/routes/{id}/geojson)
```json
{
    "type": "FeatureCollection",
    "features": [
        {
            "type": "Feature",
            "geometry": { "type": "Point", "coordinates": [0.0, 0.0] },
            "properties": {
                "id": "string-uuid",
                "url": "string-url",
                "route_id": "string-uuid",
                "name": "string",
                "description": "string (HTML)",
                "mapNumber": 0
            }
        },
        {}
    ]
}
```


### Route → Tags (as returned by GET /api/routes/{id}/tags)
```json
[
    { "id": "string-uuid", "url": "string-url", "name": "string" },
    {}
]
```
</details>

<details>
<summary><strong>Points</strong> (click to expand)</summary>

### Point (returned by `GET /api/points/{id}` and in lists)
```json
{
  "id": "string-uuid",
  "url": "string-url",
  "route_id": "string-uuid",
  "name": "string",
  "description": "string (HTML)",
  "map_number": null,
  "lat": 0.0,
  "lng": 0.0,
  "bibliographies": [
    { "id": "string-uuid", "text": "string (HTML)" },
      {}
  ]
}
```

### Points List (returned by `GET /api/points/`)
> Returns an array of `Point` objects; 

### Point → Bibliographies (returned by `GET /api/points/{id}/bibliographies`)
```json
[
  { "id": "string-uuid", "text": "string (HTML)" },
    {}
]
```

### Points GeoJSON (returned by `GET /api/points/geojson`)
```json
{
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "geometry": { "type": "Point", "coordinates": [0.0, 0.0] },
      "properties": {
        "id": "string-uuid",
        "url": "string-url",
        "route_id": "string-uuid",
        "name": "string",
        "description": "string (HTML)",
        "mapNumber": null
      }
    },
      {}
  ]
}
```

### Single Point GeoJSON (returned by `GET /api/points/{id}/geojson`)
```json
{
  "type": "Feature",
  "geometry": { "type": "Point", "coordinates": [0.0, 0.0] },
  "properties": {
    "id": "string-uuid",
    "url": "string-url",
    "route_id": "string-uuid",
    "name": "string",
    "description": "string (HTML)",
    "mapNumber": null
  }
}
```

### Points Nearby (returned by `GET /api/points/nearby?lng=&lat=&radius=`)
```json
[
  {
    "id_number": 0,
    "route_id_number": 0,
    "name": "string",
    "map_number": "string",
    "location": "string (WKB/EWKB hex)",
    "description": "string (HTML)",
    "id": "string-uuid",
    "route_id": "string-uuid",
    "distance": 0.0
  },{}
]
```

</details>

<details>
  <summary><strong>Tags</strong> (click to expand)</summary>

### Tag List (returned by `GET /api/tags/`)
```json
[
  {
    "id": "string-uuid",
    "name": "string",
    "url": "string-url",
    "routes": [
      {
        "id": "string-uuid",
        "url": "string-url",
        "name": "string",
        "description": "string (HTML)",
        "points": [{},{}]
      },
        {}
    ]
  }
]
```

### Tag (returned by `GET /api/tags/{id}`)
```json
 {
    "id": "string-uuid",
    "name": "string",
    "url": "string-url",
    "routes": [
      {
        "id": "string-uuid",
        "url": "string-url",
        "name": "string",
        "description": "string (HTML)",
        "points": [{},{}]
      },
        {}
    ]
  }

```


### Tag → Routes (returned by `GET /api/tags/{id}/routes`)
```json
[
    {
    "id": "string-uuid",
    "url": "string-url",
    "name": "string",
    "description": "string (HTML)",
    "points": [
        {
            "id": "string-uuid",
            "url": "string-url",
            "name": "string",
            "description": "string (HTML)"
        },
        {}
    ],
    "tags": [
        { "id": "string-uuid", "name": "string", "url": "string-url" }, 
        {}
    ]
  }
]
```

### Tags Nearby (returned by `GET /api/tags/nearby?lng=&lat=&radius=`)
```json
[
  { "id_number": 0, "name": "string", "id": "string-uuid" },
    {}
]
```

</details>
