<p align="center"><a href="https://symfony.com" target="_blank"><img src="https://symfony.com/logos/symfony_black_03.svg" width="400" alt="Symfony Logo"></a></p>



## API Endpoints

### Points
- `GET /api/points` - List all points
- `GET /api/points/{point}` - Get specific point details
- `GET /api/points/nearby` - Find points near a location (requires lat/lng/radius)
- `GET /api/points/geojson` - Get all points as GeoJSON feature collection
- `GET /api/points/{id}/geojson` - Get specific point as GeoJSON feature

### Routes
- `GET /api/routes` - List all routes
- `GET /api/routes/{route}` - Get specific route details
- `GET /api/routes/{id}/points` - Get points belonging to a route
- `GET /api/routes/{id}/tags` - Get tags associated with a route
- `GET /api/routes/nearby` - Find routes near a location (requires lat/lng/radius)

### Points Resource

#### Get all points
- **GET** `/api/points`
- Returns: List of all points in the system

#### Get a specific point
- **GET** `/api/points/{point}`
- Parameters: `point` (ID of the point)
- Returns: Details of the specified point

#### Find nearby points
- **GET** `/api/points/nearby`
- Parameters: requires latitude, longitude, and radius
- Returns: List of points within the specified area

#### Get all points as GeoJSON
- **GET** `/api/points/geojson`
- Returns: All points formatted as GeoJSON feature collection

#### Get specific point as GeoJSON
- **GET** `/api/points/{id}/geojson`
- Parameters: `id` (Point ID)
- Returns: Single point formatted as GeoJSON feature

### Routes Resource

#### Get all routes
- **GET** `/api/routes`
- Returns: List of all routes in the system

#### Get a specific route
- **GET** `/api/routes/{route}`
- Parameters: `route` (ID of the route)
- Returns: Details of the specified route

#### Get points for a route
- **GET** `/api/routes/{id}/points`
- Parameters: `id` (Route ID)
- Returns: List of points associated with the specified route

#### Get tags for a route
- **GET** `/api/routes/{id}/tags`
- Parameters: `id` (Route ID)
- Returns: List of tags associated with the specified route

#### Find nearby routes
- **GET** `/api/routes/nearby`
- Parameters: requires latitude, longitude, and radius.
- Returns: List of routes within the specified area
