<p align="center"><a href="https://symfony.com" target="_blank"><img src="https://symfony.com/logos/symfony_black_03.svg" width="400" alt="Symfony Logo"></a></p>



## API Endpoints

### Routes
- `GET /api/routes` - List all routes
- `GET /api/routes/{id}` - Get specific route details by id
- `GET /api/routes/{id}/points` - Get points belonging to a route
- `GET /api/routes/{id}/tags` - Get tags associated with a route
- `GET /api/routes/{id}/geojson` —Get route points as GeoJSON FeatureCollection
- `GET /api/routes/nearby?lng={lng}&lat={lat}&radius={meters}` —Get routes within radius (meters)

### Points
- `GET /api/points` - List all points
- `GET /api/points/{id}` - Get specific point details
- `GET /api/points/geojson` - Get all points as GeoJSON feature collection
- `GET /api/points/{id}/geojson` - Get specific point as GeoJSON feature
- `GET /api/points/nearby?lng={lng}&lat={lat}&radius={meters}` — points within radius (meters)
- `GET /api/points/{id}/bibliographies` —GET bibliographies of a point

### Tags
- `GET /api/tags/` — list tags
- `GET /api/tags/{id}` — tag by id
- `GET /api/tags/{id}/routes` — routes that have this tag
- `GET /api/tags/nearby?lng={lng}&lat={lat}&radius={meters}` — tags near location

## Nearby parameters

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
        { "id": "string-uuid", "name": "string", "url": "string-url" }, 
        {}
    ]
}]
```


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

