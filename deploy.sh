#!/bin/bash

# Variáveis
PROJECT_ID="leca-store-ai"
REGION="us-central1"
SERVICE_NAME="leca-service"
IMAGE_NAME="leca-image"
REPO="leca"
IMAGE_URL="$REGION-docker.pkg.dev/$PROJECT_ID/$REPO/$IMAGE_NAME"

# 1. Build da imagem
echo "🔨 Buildando imagem..."
docker build -t $IMAGE_URL .

# 2. Push pro Artifact Registry
echo "🚀 Enviando imagem para Artifact Registry..."
docker push $IMAGE_URL

# 3. Deploy no Cloud Run
echo "☁️ Realizando deploy no Cloud Run..."
gcloud run deploy $SERVICE_NAME \
  --image $IMAGE_URL \
  --platform managed \
  --region $REGION \
  --allow-unauthenticated

echo "✅ Deploy finalizado!"
