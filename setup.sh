#!/bin/bash

echo " Building Docker containers..."
docker-compose up --build -d

echo "✅ Camagru ready at http://localhost:8080"
