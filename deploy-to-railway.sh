#!/bin/bash

echo "🚀 PeeZ Dashboard - Railway Deployment Setup"
echo "============================================="
echo ""

# Schritt 1: Git Repository initialisieren
if [ ! -d .git ]; then
    echo "📦 Schritt 1: Git Repository initialisieren..."
    git init
    git add .
    git commit -m "Initial commit - PeeZ Dashboard ready for Railway deployment"
    echo "✅ Git Repository erstellt"
else
    echo "✅ Git Repository bereits vorhanden"
fi

# Schritt 2: GitHub Repository erstellen
echo ""
echo "📝 Schritt 2: GitHub Repository erstellen"
echo "   Gehe zu: https://github.com/new"
echo "   Repository Name: peez-dashboard"
echo "   Beschreibung: PeeZ Loyalty Platform - Laravel Backend API"
echo "   Visibility: Private (empfohlen)"
echo ""
read -p "Hast du das GitHub Repo erstellt? (y/n) " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo ""
    read -p "GitHub Repository URL (z.B. https://github.com/username/peez-dashboard.git): " GITHUB_URL

    if [ ! -z "$GITHUB_URL" ]; then
        echo "📤 Pushing zu GitHub..."
        git remote add origin $GITHUB_URL
        git branch -M main
        git push -u origin main
        echo "✅ Code auf GitHub gepusht!"
    fi
fi

# Schritt 3: Railway Setup
echo ""
echo "🚂 Schritt 3: Railway Deployment"
echo "================================"
echo ""
echo "1. Gehe zu: https://railway.app"
echo "2. Klicke 'Start a New Project'"
echo "3. Wähle 'Deploy from GitHub repo'"
echo "4. Wähle dein 'peez-dashboard' Repository"
echo ""
echo "5. WICHTIG: Füge MySQL Datenbank hinzu:"
echo "   - Klicke '+ New' → 'Database' → 'MySQL'"
echo ""
echo "6. Setze Environment Variables:"
echo "   (Kopiere aus RAILWAY_DEPLOYMENT.md)"
echo ""

# App Key generieren
echo "🔑 Dein APP_KEY für Railway:"
php artisan key:generate --show

echo ""
echo "✅ Deployment Setup abgeschlossen!"
echo ""
echo "📖 Nächste Schritte:"
echo "   1. Folge den Anweisungen in RAILWAY_DEPLOYMENT.md"
echo "   2. Warte bis Railway das Deployment abgeschlossen hat"
echo "   3. Führe Migrations aus: railway run php artisan migrate --seed"
echo "   4. Teste deine API: https://deine-app.up.railway.app/api/v1/neighborhoods"
echo ""
